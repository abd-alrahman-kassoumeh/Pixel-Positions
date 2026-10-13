<x-layout>
    <x-page-heading>Edit Job</x-page-heading>

    <x-forms.form method="POST" action="/jobs/{{ $job->id }}">
        @method('PATCH')
        <x-forms.input label="Title" name="title" placeholder="CEO"></x-forms.input>
        <x-forms.input label="Salary" name="salary" placeholder="$90,000"></x-forms.input>
        <x-forms.input label="Location" name="location" placeholder="Remote"></x-forms.input>

        <x-forms.select label="Schedule" name="schedule">
            <option>Part Time</option>
            <option>Full Time</option>
        </x-forms.select>

        <x-forms.input label="Job Details" name="details" placeholder="About The Job..." :textarea="true" />
        <x-forms.input label="Job Requirements" name="requirements" placeholder="Enter each requirement on a new line..." :textarea="true" />
        <x-forms.checkbox label="Feature (Costs Extra)" name="featured" />

        <x-forms.divider />

        <x-forms.input label="Tags (comma seperated)" name="tags" placeholder="Frontent, Backend, video, education" />

        <x-forms.button>Edit</x-forms.button>
        <button class="bg-red-800 rounded py-2 px-6 font-bold" form="delete-form">Delete The Job</button>
    </x-forms.form>

    <form method="POST" action="/jobs/{{ $job->id }}" id="delete-form">
        @csrf
        @method('DELETE')
    </form>
</x-layout>