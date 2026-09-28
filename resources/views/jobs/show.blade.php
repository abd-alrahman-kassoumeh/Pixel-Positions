<x-layout>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div class="space-y-6 max-w-5xl mx-auto px-4 py-8">
        <x-panel>
            <div class="text-center">
                <x-page-heading class="group-hover:text-blue-600 transition-colors duration-300">{{ $job->title }}</x-page-heading>
                <div class="text-lg font-bold text-red-600">{{ $job->employer->name }}</div>
            </div>

            <div>
                <form action="/jobs/{{ $job->id }}/apply" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                    @csrf
                    <label class="cursor-pointer bg-blue-600 text-sm font-bold py-2 px-4 rounded transition duration-200 shadow-md flex items-center gap-2">
                        <span>📎 Upload CV & Apply</span>
                        <input type="file" name="cv" class="hidden" onchange="this.form.submit()">
                    </label>
                </form>

                @error('cv')
                    <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>
                @enderror

                @if (session('success'))
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: "{{ session('success') }}",
                            confirmButtonColor: '#2563eb'
                        });
                    </script>
                @else
                    <script>
                        Swal.fire({
                            icon: 'wrong',
                            title: 'Wrong!',
                            text: "Try again",
                            confirmButtonColor: '#DC143C'
                        });
                    </script>
                @endif
            </div>
        </x-panel>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <div class="lg:col-span-2 space-y-6">
                <x-panel>
                    <h3 class="group-hover:text-blue-600 text-slate-300 text-lg font-bold border-b border-slate-100 pb-3 mb-4">Job Details</h3>
                    <div class="leading-relaxed whitespace-pre-line">{{ $job->details }}</div>
                </x-panel>

                <x-panel>
                    <h3 class="group-hover:text-blue-600 text-slate-300 text-lg font-bold border-b border-slate-100 pb-3 mb-4">Requirements</h3>
                    <div class="leading-relaxed whitespace-pre-line">{{ $job->requirements }}</div>
                </x-panel>
            </div>

            <div class="space-y-6 lg:sticky lg:top-6">
                <x-panel>
                    <h3 class="group-hover:text-blue-600 text-md font-semibold text-slate-300 tracking-wider uppercase border-b border-slate-800 pb-2">Overview</h3>

                    <div class="mt-1 mb-1">
                        <span class="text-xs text-slate-300 block uppercase font-medium tracking-tight">Offered Salary</span>
                        <span class="text-xl font-bold text-emerald-500 mt-0.5 block">{{ $job->salary }}</span>
                    </div>

                    <div class="flex items-start gap-3 mt-1 mb-1">
                        <div class="mt-0.5 text-slate-400">📍</div>
                        <div>
                            <span class="text-xs text-slate-300 block uppercase font-medium tracking-tight">Location</span>
                            <span class="text-sm font-medium text-slate-200">{{ $job->location }}</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 mt-1 mb-1">
                        <div class="mt-0.5 text-slate-400">⏰</div>
                        <div>
                            <span class="text-xs text-slate-300 block uppercase font-medium tracking-tight">Job Schedule</span>
                            <span class="text-sm font-medium text-slate-200">{{ $job->schedule }}</span>
                        </div>
                    </div>
                </x-panel>
            </div>

        </div>
    </div>
</x-layout>