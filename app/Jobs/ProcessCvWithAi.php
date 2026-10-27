<?php

namespace App\Jobs;

use App\Models\CvAnalysis;
use App\Models\Job;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Collection;


class ProcessCvWithAi implements ShouldQueue
{
    use Queueable;

    public string $cvPath;
    public int $analysisId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $cvPath , int $analysisId)
    {
        $this->cvPath = $cvPath;
        $this->analysisId = $analysisId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fullPath = Storage::disk('local')->path($this->cvPath);

        $parser = new Parser();
        $pdf = $parser->parseFile($fullPath);
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

        $suitableJobIDs = $this->evaluateJobsWithAI($cvText , $bestJobs);

        CvAnalysis::where('id' , $this->analysisId)->update([
            'status' => 'completed',
            'recommended_job_ids' => $suitableJobIDs
        ]);

        Storage::disk('local')->delete($this->cvPath);
    }

        public function getKeywordsFromCV(string $cvText)
    {
        $prompt = "Extract up to 15 key technical skills, job titles, or core competencies from this CV text:\n\"{$cvText}\"\n\n"
                . "CRITICAL: Return ONLY a valid JSON array of strings and nothing else. "
                . "Example format: [\"PHP\", \"Laravel\", \"Vue.js\", \"Developer\"]";

        return $this->AiProcess($prompt);
    }

    public function evaluateJobsWithAI(string $cvText , Collection $jobs)
    {
        $prompt = "You are an HR evaluation system.\n\n"
                . "Candidate CV:\n\"{$cvText}\"\n\n"
                . "Available Jobs:\n" . json_encode($jobs->values()->toArray()) . "\n\n"
                . "Task: Iterate through every job provided above. Decide if it is a suitable match for this candidate's CV.\n"
                . "Return ONLY a valid JSON array containing the integer IDs of all jobs that are a good match. Example output: [2, 5, 12].";

        return $this->AiProcess($prompt);
    }

    public function AiProcess(string $prompt)
    {
        $accountId = config('services.cloudflare.account_id');
        $url = "https://api.cloudflare.com/client/v4/accounts/{$accountId}/ai/v1/chat/completions";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.cloudflare.key'),
            'Content-Type'  => 'application/json',
        ])
        ->timeout(30)
        ->retry(3 , 1000)
        ->post($url , [
            'model' => config('services.cloudflare.model' , '@cf/meta/llama-3.1-8b-instruct'),
            'messages' => [
                ['role' => 'user' , 'content' => $prompt]
            ],
            'temperature' => 0.1,
        ]);

        if ($response->failed())
        {
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
