<?php

namespace App\Models;

use App\JobSchedule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

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

    public function addTags(Array $attributes)
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
                    $this->tag($cleanTag);
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

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function scopeWithRelations(Builder $query)
    {
        return $query->with(['employer' , 'tags']);
    }

    public function casts()
    {
        return [
            'schedule' => JobSchedule::class
        ];
    }
}
