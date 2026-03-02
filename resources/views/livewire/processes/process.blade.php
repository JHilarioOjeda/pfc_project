<div class="containerpric">
    <x-loading functionsList="saveTarima, addNumberPart" />

    <div class="w-full flex space-x-4">
        <x-secondary-hyperlink href="{{ route('storage') }}" target="" class="my-auto whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 mr-2">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 0 1 0-1.06l7.5-7.5a.75.75 0 1 1 1.06 1.06L9.31 12l6.97 6.97a.75.75 0 1 1-1.06 1.06l-7.5-7.5Z" clip-rule="evenodd" />
            </svg>
            Volver
        </x-secondary-hyperlink>
        <p class="text-secondarycolor text-2xl font-bold">@if($process_selected) Información de @else Comenzar @endif proceso</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg my-3 p-3">

        
    </div>
</div>

@push('js')
<script>
    
</script>
@endpush
