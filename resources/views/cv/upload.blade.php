<x-layout>
    <div class="max-w-xl mx-auto py-8">
        <x-forms.form method="POST" action="/upload" enctype="multipart/form-data">
            <x-forms.input label="Upload Your CV (PDF)" name="cv" type="file" accept=".pdf" />
            
            <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded font-bold">
                Upload & Search Jobs
            </button>
        </x-forms.form>
    </div>
</x-layout>