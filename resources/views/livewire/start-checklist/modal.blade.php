<div>
    <x-dialog-modal wire:model.live="show" :closeable="false" maxWidth="2xl">
        <x-slot name="title">
            Checklist de inicio ({{ now()->format('d/m/Y') }})
        </x-slot>

        <x-slot name="content">
            <p class="text-sm text-gray-600">
                Debes completar este cuestionario para continuar.
            </p>

            <div class="mt-4 space-y-4">
                @foreach($questions as $question)
                    @php
                        $key = $question['key'] ?? null;
                        $type = $question['type'] ?? 'boolean';
                        $label = $question['label'] ?? $key;
                    @endphp

                    @if(!$key)
                        @continue
                    @endif

                    <div class="border border-gray-200 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-900">{{ $label }}</p>

                        @if($type === 'boolean')
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
                        @elseif($type === 'text')
                            <div class="mt-3">
                                <x-input type="text" class="mt-1 block w-full" wire:model="answers.{{ $key }}" />
                            </div>
                        @elseif($type === 'number')
                            <div class="mt-3">
                                <x-input type="number" class="mt-1 block w-full" wire:model="answers.{{ $key }}" />
                            </div>
                        @endif

                        <x-input-error for="answers.{{ $key }}" class="mt-2" />
                    </div>
                @endforeach
            </div>

            @error('save')
                <div class="mt-3 text-sm text-red-600">{{ $message }}</div>
            @enderror
        </x-slot>

        <x-slot name="footer">
            <x-primary-button wire:click="save" wire:loading.attr="disabled">
                Guardar
            </x-primary-button>
        </x-slot>
    </x-dialog-modal>
</div>
