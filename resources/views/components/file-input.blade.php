@props([
    'id' => null,
    'label' => null,
    'help' => null,
])

<div class="w-full">
    @if($label)
        <x-label :for="$id" :value="$label" />
    @endif

    <input
        {{ $attributes->merge([
            'type' => 'file',
            'id' => $id,
            'class' => 'w-full cursor-pointer border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 text-sm ' .
                        'file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 ' .
                        'file:text-sm file:font-semibold file:bg-primarycolor file:text-white ' .
                        'hover:file:bg-primarycolor/90 focus:ring-2 focus:ring-primarycolor focus:border-primarycolor'
        ]) }}
    >

    @if ($help)
        <p class="mt-1 text-xs text-gray-500">{{ $help }}</p>
    @endif

    {{ $slot }}
</div>
