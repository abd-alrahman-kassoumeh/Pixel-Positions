<x-layout>
    <section class="text-center py-6">
        <x-page-heading>Your Posted Jobs</x-page-heading>

        <x-forms.form action="/search" class="mt-6">
            <x-forms.input :label="false" name="q" placeholder="Web Developer..." />
        </x-forms.form>
    </section>

    <div class="space-y-6">
        @foreach($jobs as $job)
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
                <div class="flex flex-col justify-between items-end">
                    <a href="/jobs/{{ $job->id }}/edit" 
                        class="px-2 py-1 text-xs font-semibold text-blue-400 bg-blue-950/40 hover:bg-blue-900/60 rounded-md border border-blue-800/50 transition-colors">
                        Edit The Job
                    </a>

                    <div class="flex gap-x-1">
                        @foreach ($job->tags as $tag)
                            <x-tag :tag="$tag" />
                        @endforeach
                    </div>
                </div>
            </x-panel>
        @endforeach
    </div>
</x-layout>