<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Mail\Apply;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobs = Job::latest()->with(['employer' , 'tags'])->get()->groupBy('featured');

        return view('jobs.index' , [
            'featuredJobs' => $jobs[1],
            'jobs' => $jobs[0],
            'tags' => Tag::all()
        ]);
    }

    public function myJobs()
    {
        $jobs = Auth::user()->employer->jobs()->get();

        return view('jobs.all-posted', ['jobs' => $jobs]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('jobs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'title' => ['required'],
            'salary' => ['required'],
            'location' => ['required'],
            'schedule' => ['required', Rule::in(['Part Time', 'Full Time'])],
            'details' => ['required'],
            'requirements' => ['required'],
            'tags' => ['nullable'],
        ]);

        $attributes['featured'] = $request->has('featured');

        $job = Auth::user()->employer->jobs()->create(Arr::except($attributes , 'tags'));

        if (empty($attributes['tags']) == false)
        {
            $uniquesTags = [];
            foreach (explode(',' , $attributes['tags']) as $tag)
            {
                $cleanTag = trim($tag);

                if ($cleanTag == '') continue;

                if (isset($uniquesTags[$cleanTag]) == false)
                {
                    $uniquesTags[$cleanTag] = true;
                    $job->tag($cleanTag);
                }
            }
        }

        return redirect('/');
    }

    /**
     * Display the specified resource.
     */
    public function show(Job $job)
    {
        return view('jobs.show' , ['job' => $job]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Job $job)
    {
        return view('jobs.edit' , ['job' => $job]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request , Job $job)
    {
        $attributes = $request->validate([
            'title' => ['required'],
            'salary' => ['required'],
            'location' => ['required'],
            'schedule' => ['required', Rule::in(['Part Time', 'Full Time'])],
            'url' => ['required', 'active_url'],
            'tags' => ['nullable'],
        ]);

        $attributes['featured'] = $request->has('featured');

        $job->update(Arr::except($attributes , ['tags']));
        $job->tags()->detach();

        if (empty($attributes['tags']) == false)
        {
            $uniquesTags = [];
            foreach (explode(',' , $attributes['tags']) as $tag)
            {
                $cleanTag = trim($tag);

                if ($cleanTag == '') continue;

                if (isset($uniquesTags[$cleanTag]) == false)
                {
                    $uniquesTags[$cleanTag] = true;
                    $job->tag($cleanTag);
                }
            }
        }

        return redirect('/jobs/all-posted');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Job $job)
    {
        $job->delete();

        return redirect('/jobs/all-posted');
    }

    public function applyToJob(Request $request , Job $job)
    {
        $request->validate([
            'cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $path = $request->file('cv')->store('temporary_cvs' , 'local');

        Mail::to($job->employer->user)->queue(new Apply($job , $path));

        return back()->with('success' , 'Your application and CV have been submitted successfully!');
    }
}
