<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplyToJobRequest;
use App\Models\Job;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Mail\Apply;
use App\Models\Application;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobs = Job::latest()->with(['employer' , 'tags'])->get();

        [$featuredJobs , $regularJobs] = $jobs->partition('featured');

        return view('jobs.index' , [
            'jobs' => $regularJobs,
            'featuredJobs' => $featuredJobs,
            'tags' => Tag::all()
        ]);
    }

    public function myJobs()
    {
        $jobs = Auth::user()->employer->jobs()->with('tags')->get();

        $employer = Auth::user()->employer;

        return view('jobs.all-posted' , [
            'jobs' => $jobs,
            'employer' => $employer
        ]);
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
    public function store(StoreJobRequest $request)
    {
        $attributes = $request->validated();

        $attributes['featured'] = $request->has('featured');

        DB::transaction(function () use ($attributes) {
            $job = Auth::user()->employer->jobs()->create(Arr::except($attributes , 'tags'));

            $job->addTags($attributes);
        });

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
    public function update(UpdateJobRequest $request , Job $job)
    {
        $attributes = $request->validated();

        $attributes['featured'] = $request->has('featured');

        DB::transaction(function () use ($attributes , $job) {
            $job->update(Arr::except($attributes , ['tags']));
            $job->tags()->detach();

            $job->addTags($attributes);
        });

        Application::where('job_id' , $job->id)->delete();

        return redirect('/all-posted');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Job $job)
    {
        $job->delete();

        return redirect('/all-posted');
    }

    public function applyToJob(ApplyToJobRequest $request , Job $job)
    {
        $request->validated();

        $path = $request->file('cv')->store('temporary_cvs' , 'local');

        Mail::to($job->employer->user)->queue(new Apply($job , $path));

        return back()->with('success' , 'Your application and CV have been submitted successfully!');
    }
}
