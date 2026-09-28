@props(['job'])

<x-panel class="flex flex-col text-center">
    <div class="flex justify-between items-center text-sm">
        <div>
            {{ $job->employer->name }}
        </div>
    </div>

    <div class="py-8">
        <h3 class="group-hover:text-blue-600 text-xl font-bold transition-colors duration-300">
            <a href="/jobs/{{ $job->id }}" target="_blank">{{ $job->title }}</a>
        </h3>
        <p class="mt-4 text-sm">{{ $job->salary }}</p>
    </div>

    <div class="flex justify-between items-center mt-auto">
        <div>
            @foreach ($job->tags as $tag)
                <x-tag size="small" :tag="$tag" />
            @endforeach
        </div>

        <div>
            <x-empolyer-logo :employer="$job->employer" :size="'w-[42px] h-[42px]'"></x-empolyer-logo>
        </div>
    </div>
</x-panel>