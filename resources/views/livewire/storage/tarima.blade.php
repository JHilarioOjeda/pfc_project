<div class="containerpric">
    <x-loading functionsList="" />

    <div class="w-full flex space-x-4">
        <x-secondary-hyperlink href="{{ route('storage') }}" target="" class="my-auto whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 mr-2">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 0 1 0-1.06l7.5-7.5a.75.75 0 1 1 1.06 1.06L9.31 12l6.97 6.97a.75.75 0 1 1-1.06 1.06l-7.5-7.5Z" clip-rule="evenodd" />
            </svg>
            Volver
        </x-secondary-hyperlink>
        <p class="text-secondarycolor text-2xl font-bold">Nueva entrada</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg my-3 p-3">
        <div class="w-full md:flex md:space-x-6 mb-4">
            <div class="w-full md:w-1/2">
                <p class="text-secondarycolor">Número de tarima:</p>
                <input wire:model ="serial_number" type="text" class="inputcatalogues w-full">
                <span class="text-red-500 text-xs italic">
                    @error('serial_number')
                        {{$message}}
                    @enderror
                </span>
            </div>
            <div class="w-full md:w-1/2">
                <p class="text-secondarycolor">Cliente:</p>
                <select wire:model="id_customer" id="customer_select" class="inputcatalogues w-full">
                    <option value="">Selecciona un cliente</option>
                    @foreach ($customers as $customer)
                        <option value="{{$customer->id}}">{{$customer->company_name}}</option>
                    @endforeach
                </select>
                <span class="text-red-500 text-xs italic">
                    @error('id_customer')
                        {{$message}}
                    @enderror
                </span>
            </div>
        </div>

        <div class="w-full">
            <p class="font-semibold text-primarycolor">NP  a registrar</p>
            <div class="w-full flex space-x-3">
                <div class="w-full md:w-1/4">
                    <p class="text-secondarycolor">Número de parte:</p>
                    <select wire:model="id_number_part" id="number_part_select" class="inputcatalogues w-full">
                        <option value="">Seleccionar...</option>
                        @foreach ($number_parts as $number_part)
                            <option value="{{$number_part->id}}">{{$number_part->partnumber}}</option>
                        @endforeach
                    </select>
                    <span class="text-red-500 text-xs italic">
                        @error('id_number_part')
                            {{$message}}
                        @enderror
                    </span>
                </div>

            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    function initCustomerSelect() {
        if (typeof SlimSelect === 'undefined') return;

        if (window.customerSlim) {
            window.customerSlim.destroy();
        }

        const el = document.getElementById('customer_select');
        if (!el) return;

        window.customerSlim = new SlimSelect({
            select: el,
            settings: {
                placeholderText: 'Selecciona un cliente',
                searchPlaceholder: 'Buscar',
                searchText: 'No se encontraron resultados',
            },
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initCustomerSelect();
        initNumberPartSelect();
    });

    if (window.Livewire && Livewire.hook) {
        Livewire.hook('message.processed', () => {
            initCustomerSelect();
            initNumberPartSelect();
        });
    }

    function initNumberPartSelect() {
        if (typeof SlimSelect === 'undefined') return;

        if (window.numberPartSlim) {
            window.numberPartSlim.destroy();
        }

        const el = document.getElementById('number_part_select');
        if (!el) return;

        window.numberPartSlim = new SlimSelect({
            select: el,
            settings: {
                placeholderText: 'Seleccionar número de parte',
                searchPlaceholder: 'Buscar',
                searchText: 'No se encontraron resultados',
            },
        });
    }
</script>
@endpush
