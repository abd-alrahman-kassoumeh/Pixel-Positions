<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Smalot\PdfParser\Parser;

class CvRecommendationController extends Controller
{
    public function create()
    {
        return view('cv.upload');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cv' => 'required|mimes:pdf|max:2048',
        ]);

        $parser = new Parser();
        $pdf = $parser->parseFile($request->file('cv')->path());
        $cvText = $pdf->getText();

        $cvText = mb_convert_encoding($cvText , 'UTF-8' , 'UTF-8');

        $keywords = $this->getKeywordsFromCV($cvText);

        $bestJobs = collect();
        foreach ($keywords as $word)
        {
            $jobs = Job::where('title' , 'LIKE' , '%' . $word . '%')
                ->select('id' , 'title')
                ->limit(20)
                ->get();

            $bestJobs = $bestJobs->merge($jobs);
        }

        $bestJobs = $bestJobs->unique('id');

        if ($bestJobs->isEmpty())
        {
            return view('cv.recommended' , ['recommendedJobs' => collect()]);
        }

        $suitableJobIDs = $this->evaluateJobsWithAI($cvText , $bestJobs);

        $recommendedJobs = Job::whereIn('id' , $suitableJobIDs)->get();

        return view('cv.recommended', ['recommendedJobs' => $recommendedJobs]);
    }

    public function getKeywordsFromCV(string $cvText)
    {
        $prompt = "Extract up to 10 key technical skills, job titles, or core competencies from this CV text:\n\"{$cvText}\"\n\n"
                . "CRITICAL: Return ONLY a valid JSON array of strings and nothing else. "
                . "Example format: [\"PHP\", \"Laravel\", \"Vue.js\", \"Developer\"]";

        $accountId = config('services.cloudflare.account_id');
        $url = "https://api.cloudflare.com/client/v4/accounts/{$accountId}/ai/v1/chat/completions";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.cloudflare.key'),
            'Content-Type'  => 'application/json',
        ])->post($url , [
            'model' => config('services.cloudflare.model' , '@cf/meta/llama-3.1-8b-instruct'),
            'messages' => [
                ['role' => 'user' , 'content' => $prompt]
            ],
            'temperature' => 0.1,
        ]);

        if ($response->failed()) {
            logger()->error('Cloudflare AI Keyword Error' , [
                'status' => $response->status(),
                'body'   => $response->body()
            ]);
            return [];
        }

        $rawContent = $response->json('choices.0.message.content' , '');

        if (preg_match('/\[.*\]/s' , $rawContent , $matches))
        {
            $jsonString = $matches[0];
        }
        else
        {
            $jsonString = trim(str_replace(['```json' , '```'] , '' , $rawContent));
        }

        $decodedJsonString = json_decode($jsonString , true);
        if ($decodedJsonString !== null) return $decodedJsonString;

        return [];
    }

    public function evaluateJobsWithAI(string $cvText , Collection $jobs)
    {
        $prompt = "You are an HR evaluation system.\n\n"
                . "Candidate CV:\n\"{$cvText}\"\n\n"
                . "Available Jobs:\n" . json_encode($jobs->values()->toArray()) . "\n\n"
                . "Task: Iterate through every job provided above. Decide if it is a suitable match for this candidate's CV.\n"
                . "Return ONLY a valid JSON array containing the integer IDs of all jobs that are a good match. Example output: [2, 5, 12].";

        $accountId = config('services.cloudflare.account_id');
        $url = "https://api.cloudflare.com/client/v4/accounts/{$accountId}/ai/v1/chat/completions";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.cloudflare.key'),
            'Content-Type'  => 'application/json',
        ])->post($url , [
            'model' => config('services.cloudflare.model' , '@cf/meta/llama-3.1-8b-instruct'),
            'messages' => [
                ['role' => 'user' , 'content' => $prompt]
            ],
            'temperature' => 0.1,
        ]);

        if ($response->failed()) {
            logger()->error('Cloudflare AI Job Match Error' , [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return [];
        }

        $rawContent = $response->json('choices.0.message.content' , '');

        if (preg_match('/\[.*\]/s' , $rawContent , $matches))
        {
            $jsonString = $matches[0];
        }
        else
        {
            $jsonString = trim(str_replace(['```json' , '```'] , '' , $rawContent));
        }

        $decodedJsonString = json_decode($jsonString , true);
        if ($decodedJsonString !== null) return $decodedJsonString;

        return [];
    }
}