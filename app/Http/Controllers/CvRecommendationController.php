<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCvWithAi;
use App\Models\CvAnalysis;
use App\Models\Job;
use Illuminate\Http\Request;

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

        $cvPath = $request->file('cv')->store('temp_cvs' , 'local');

        $analysis = CvAnalysis::create([
            'status' => 'pending'
        ]);

        ProcessCvWithAi::dispatch($cvPath , $analysis->id);

        return redirect()->route('cv.recommended' , ['analysis' => $analysis->id]);
    }

    public function showResults(CvAnalysis $analysis)
    {
        if ($analysis->status === 'pending')
        {
            return view('cv.waiting' , ['analysis' => $analysis]);
        }

        $recommendedJobs = Job::whereIn('id' , $analysis->recommended_job_ids)->get();

        return view('cv.recommended' , ['recommendedJobs' => $recommendedJobs]);
    }
}