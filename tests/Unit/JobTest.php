<?php

use App\Models\Employer;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\Apply;

it('belongs to an employer' , function () {
    $employer = Employer::factory()->create();
    $job = Job::factory()->create([
        'employer_id' => $employer->id
    ]);

    expect($job->employer->is($employer))->toBeTrue();
});

it('can have tags' , function () {
    $job = Job::factory()->create();

    $job->tag('Frontend');

    expect($job->tags)->toHaveCount(1);
});

it('prevents unauthorized users from updating another users job' , function () {
    $owner = User::factory()->create();
    $unauthorizedUser = User::factory()->create();

    $employer = Employer::factory()->create(['user_id' => $owner->id]);

    $job = Job::factory()->create([
        'employer_id'=> $employer->id,
        'title' => 'Test Title'
    ]);

    $this->actingAs($unauthorizedUser)
        ->put("/jobs/{$job->id}" , [
            'title' => 'Edited Test Title'
        ]);

    expect($job->fresh()->title)->toBe('Test Title');
});

it('processes job application using fakes', function() {
    Storage::fake('local');

    $cv = UploadedFile::fake()->create('testCv.pdf' , 100);

    $accountId = config('services.cloudflare.account_id');
    $url = "https://api.cloudflare.com/client/v4/accounts/{$accountId}/ai/v1/chat/completions";
    Http::fake([
        $url => Http::response(['status' => 'success'], 200)
    ]);

    Mail::fake();

    $applicant = User::factory()->create();

    $job = Job::factory()->create();

    $response = $this->actingAs($applicant)
        ->post("/jobs/{$job->id}/apply", [
            'cv' => $cv,
        ]);

    $response->assertSessionHasNoErrors();

    Storage::disk('local')->assertExists('temporary_cvs/' . $cv->hashName());

    Mail::assertQueued(Apply::class , function ($mail) use ($job , $applicant) {
        return $mail->hasTo($job->employer->user->email) && $mail->applicant->id === $applicant->id;
    });
});