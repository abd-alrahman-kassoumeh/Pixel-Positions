@props(['size' => 'w-[42px] h-[42px]' , 'employer'])

<img src="{{ asset($employer->logo) }}" class="{{ $size }} rounded-xl">