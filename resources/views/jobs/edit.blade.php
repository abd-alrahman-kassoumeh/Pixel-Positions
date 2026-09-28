<x-layout>
    <x-page-heading>Edit Job</x-page-heading>

    <x-forms.form method="POST" action="/jobs/{{ $job->id }}">
        @method('PATCH')
        <x-forms.input label="Title" name="title" placeholder="CEO" :value="old('title' , $job->title)"></x-forms.input>
        <x-forms.input label="Salary" name="salary" placeholder="$90,000"></x-forms.input>
        <x-forms.input label="Location" name="location" placeholder="Remote"></x-forms.input>

        <x-forms.select label="Schedule" name="schedule">
            <option>Part Time</option>
            <option>Full Time</option>
        </x-forms.select>

        <x-forms.input label="URL (Job Details)" name="url" placeholder="https://acme.com/jobs/ceo-wanted" />
        <x-forms.checkbox label="Feature (Costs Extra)" name="featured" />

        <x-forms.divider />

        <x-forms.input label="Tags (comma seperated)" name="tags" placeholder="Frontent, Backend, video, education" :value="old('tags', $job->tags->pluck('name')->implode(', '))"></x-forms.input>

        <x-forms.button>Publish</x-forms.button>
        <button class="bg-red-800 rounded py-2 px-6 font-bold" form="delete-form">Delete The Job</button>
    </x-forms.form>

    <form method="POST" action="/jobs/{{ $job->id }}" id="delete-form">
        @csrf
        @method('DELETE')
    </form>
</x-layout>