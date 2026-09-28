<?php

use App\Http\Controllers\CvRecommendationController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TagController;
use App\Models\Job;
use Illuminate\Support\Facades\Route;

Route::get('/' , [JobController::class , 'index']);

Route::get('/jobs/create' , [JobController::class , 'create']);
Route::get('/jobs/{job}' , [JobController::class , 'show']);
Route::post('/jobs' , [JobController::class , 'store'])->middleware('auth');
Route::get('/all-posted' , [JobController::class , 'myJobs'])->middleware('auth');
Route::get('/jobs/{job}/edit' , [JobController::class , 'edit'])
    ->middleware('auth')
    ->can('edit' , 'job');
Route::patch('/jobs/{job}' , [JobController::class , 'update']);
Route::delete('/jobs/{job}' , [JobController::class , 'destroy']);
Route::post('/jobs/{job}/apply' , [JobController::class , 'applyToJob'])->middleware('auth');

Route::get('/search' , [SearchController::class , 'index']);

Route::get('/tags/{tag:name}' , [TagController::class , 'index']);

Route::middleware('guest')->group(function () {
    Route::get('/register' , [RegisteredUserController::class , 'create']);
    Route::post('/register' , [RegisteredUserController::class , 'store']);

    Route::get('/login' , [SessionController::class , 'create']);
    Route::post('/login' , [SessionController::class , 'store']);
});

Route::get('/logout' , [SessionController::class , 'destroy']);
Route::delete('/logout' , [SessionController::class , 'destroy'])->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/upload' , [CvRecommendationController::class , 'create']);
    Route::post('/upload' , [CvRecommendationController::class , 'store']);
});