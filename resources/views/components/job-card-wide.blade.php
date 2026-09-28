@props(['job'])

<x-panel class="flex gap-x-6">
    <div>
        <x-empolyer-logo :employer="$job->employer" :size="'w-[90px] h-[90px]'"></x-empolyer-logo>
    </div>

    <div class="flex-1 flex flex-col">
        <a href="#" class="self-start text-sm text-gray-400">{{ $job->employer->name }}</a>

        <h3 class="group-hover:text-blue-600 text-xl font-bold transition-colors duration-300">
            <a href="/jobs/{{ $job->id }}" target="_blank">{{ $job->title }}</a>
        </h3>
        <p class="text-sm text-gray-400 mt-auto">{{ $job->salary }}</p>
    </div>

    <div class="flex justify-between items-center mt-auto">
        <div>
            @foreach ($job->tags as $tag)
                <x-tag :tag="$tag" />
            @endforeach
        </div>
    </div>
</x-panel>