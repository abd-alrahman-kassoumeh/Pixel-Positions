<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Job extends Model
{
    /** @use HasFactory<\Database\Factories\JobFactory> */
    use HasFactory;

    protected $fillable = [
        'employer_id',
        'title',
        'salary',
        'location',
        'schedule',
        'details',
        'requirements',
        'featured'
    ];

    public function tag(string $name)
    {
        $tag = Tag::firstOrCreate(['name' => Str::lower(Str::squish($name))]);

        $this->tags()->attach($tag);
    }

    public function addTags(Array $attributes , Job $job)
    {
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
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }
}
