@props(['error' => false])

@if ($error == true)
    <p class="text-sm text-red-500 mt-1">{{ $error }}</p>
@endif