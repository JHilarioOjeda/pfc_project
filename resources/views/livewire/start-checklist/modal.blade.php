<div class="top-20 @if(!$show) hidden @endif left-0 z-50 max-h-full overflow-y-auto">
    <div class="flex justify-center items-center bg-gray-800 antialiased top-0 opacity-70 left-0 z-30 w-full h-full fixed"></div>

    <div class="flex text-gray-500 text-md justify-center items-center antialiased top-0 left-0 z-40 w-full h-full fixed">
        <div class="flex flex-col w-11/12 lg:w-1/2 mx-auto rounded-lg overflow-y-auto bg-white px-6 py-4" style="max-height: 90%;">
            <div class="flex flex-row justify-between rounded-tl-lg rounded-tr-lg mb-2">
                <p class="text-2xl w-fit my-auto font-semibold text-primarycolor">Checklist puntos clave de arranque ({{ now()->format('d/m/Y') }})</p>
            </div>

            <p class="text-sm text-gray-600 mb-4">
                Debes completar este cuestionario para continuar.
            </p>

            <div class="space-y-4">
                @foreach($questions as $question)
                    @php
                        $key = $question['key'] ?? null;
                        $label = $question['label'] ?? $key;
                    @endphp

                    @if(!$key)
                        @continue
                    @endif

                    <div class="border border-gray-200 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-900">{{ $label }}</p>

                        <div class="mt-3 flex flex-row gap-6">
                            <label class="inline-flex items-center">
                                <input type="radio" class="text-primarycolor focus:ring-primarycolor" wire:model="answers.{{ $key }}" value="1">
                                <span class="ms-2 text-sm text-gray-700">Sí</span>
                            </label>

                            <label class="inline-flex items-center">
                                <input type="radio" class="text-primarycolor focus:ring-primarycolor" wire:model="answers.{{ $key }}" value="0">
                                <span class="ms-2 text-sm text-gray-700">No</span>
                            </label>
                        </div>

                        @error('answers.' . $key)
                            <div class="mt-2 text-xs text-red-600">{{ $message }}</div>
                        @enderror
                    </div>
                @endforeach
            </div>

            @error('save')
                <div class="mt-3 text-sm text-red-600">{{ $message }}</div>
            @enderror

            <div class="mt-6 flex justify-end">
                <x-button-primary wire:click="save" wire:loading.attr="disabled" class="">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 mr-2">
                    <path fill-rule="evenodd" d="M12 2.25a.75.75 0 0 1 .75.75v11.69l3.22-3.22a.75.75 0 1 1 1.06 1.06l-4.5 4.5a.75.75 0 0 1-1.06 0l-4.5-4.5a.75.75 0 1 1 1.06-1.06l3.22 3.22V3a.75.75 0 0 1 .75-.75Zm-9 13.5a.75.75 0 0 1 .75.75v2.25a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5V16.5a.75.75 0 0 1 1.5 0v2.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V16.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                    </svg>

                    Guardar
                </x-button-primary>
            </div>
        </div>
    </div>
</div>
