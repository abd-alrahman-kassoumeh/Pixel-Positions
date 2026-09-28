@props(['label' , 'name' , 'textarea' => false])

@php
    $defaults =
    [
        'type' => 'text',
        'id' => $name,
        'name' => $name,
        'class' => 'rounded-xl bg-white/10 border border-white/10 px-5 py-4 w-full',
        'value' => old($name)
    ];

    if ($textarea == false)
    {
        $defaults['value'] = old($name);
    }
@endphp

<x-forms.field :label="$label" :name="$name">
    @if ($textarea == true)
        <textarea {{ $attributes->merge($defaults) }} rows="5" cols="10">{{ old($name) }}</textarea>
    @else
        <input {{ $attributes->merge($defaults) }}>
    @endif
</x-forms.field>