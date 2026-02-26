<div class="containerpric">
    <x-loading functionsList="saveTarima, addNumberPart" />

    <div class="w-full flex space-x-4">
        <x-secondary-hyperlink href="{{ route('storage') }}" target="" class="my-auto whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 mr-2">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 0 1 0-1.06l7.5-7.5a.75.75 0 1 1 1.06 1.06L9.31 12l6.97 6.97a.75.75 0 1 1-1.06 1.06l-7.5-7.5Z" clip-rule="evenodd" />
            </svg>
            Volver
        </x-secondary-hyperlink>
        <p class="text-secondarycolor text-2xl font-bold">@if($tarima_selected) Información de @else Nueva @endif entrada</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg my-3 p-3">
            <div class="w-full md:flex md:space-x-3 mb-4">
                <div class="w-full md:w-1/3 mb-2">
                <p class="text-secondarycolor">Número de tarima:</p>
                <input wire:model="serial_number" type="text" class="inputcatalogues w-full">
                <span class="text-red-500 text-xs italic">
                    @error('serial_number')
                        {{$message}}
                    @enderror
                </span>
            </div>
            <div class="w-full md:w-1/3">
                <p class="text-secondarycolor">Cliente:</p>
                <select wire:model="id_customer" id="customer_select" class="inputcatalogues w-full">
                    <option value="">Selecciona un cliente</option>
                    @foreach ($customers as $customer)
                        <option value="{{$customer->id}}" @selected($id_customer == $customer->id)>{{$customer->company_name}}</option>
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
                <div class="w-full md:w-1/3">
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
                <div class="w-full md:w-1/3">
                    <p class="text-secondarycolor">Cantidad:</p>
                    <input wire:model="quantity_np" type="text" class="inputcatalogues w-full">
                    <span class="text-red-500 text-xs italic">
                        @error('quantity_np')
                            {{$message}}
                        @enderror
                    </span>
                </div>
            </div>
            <div class="w-full flex space-x-3 mt-3">
                <div class="w-full md:w-1/3">
                    <p class="text-secondarycolor">OC:</p>
                    <input wire:model="oc_np" type="text" class="inputcatalogues w-full">
                    <span class="text-red-500 text-xs italic">
                        @error('oc_np')
                            {{$message}}
                        @enderror
                    </span>
                </div>
                <div class="w-full md:w-1/3">
                    <p class="text-secondarycolor">OF:</p>
                    <input wire:model="of_np" type="text" class="inputcatalogues w-full">
                    <span class="text-red-500 text-xs italic">
                        @error('of_np')
                            {{$message}}
                        @enderror
                    </span>
                </div>
                <x-secondary-button class="w-fit mt-auto ml-auto" wire:click="addNumberPart">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 mr-2">
                        <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                    </svg>
                    Agregar NP
                </x-secondary-button>
            </div>
        </div>

        <div class="w-full mt-4">
            <p class="font-semibold text-primarycolor">NPS registrados</p>
            <div class="w-full rounded-lg border-2 border-dashed border-gray-200 p-2">
                @if(count($numberPartsList) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs md:text-sm">
                            <thead class="bg-gray-100 text-gray-600">
                                <tr>
                                    <th class="px-2 py-1 text-left">NP</th>
                                    <th class="px-2 py-1 text-left">Cantidad</th>
                                    <th class="px-2 py-1 text-left">OC</th>
                                    <th class="px-2 py-1 text-left">OF</th>
                                    <th class="px-2 py-1 text-left">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($numberPartsList as $index => $item)
                                    <tr class="border-b last:border-b-0">
                                        <td class="px-1 py-2">{{ $item['partnumber'] }}</td>
                                        <td class="px-1 py-2">{{ $item['quantity'] }}</td>
                                        <td class="px-1 py-2">{{ $item['oc'] }}</td>
                                        <td class="px-1 py-2">{{ $item['of'] }}</td>
                                        <td class="px-1 py-2">
                                            @if($tarima_selected)
                                                @if(!$item['status_cont'])
                                                    <x-buttonact class="!px-2 !py-1 text-xs mr-2" onclick="confirmcount({{ $index }})">
                                                        confirmar conteo
                                                    </x-buttonact>
                                                @else
                                                    <span class="bg-green-200 py-1 px-2 text-xs rounded-lg italic font-semibold mr-2 uppercase"><span class="text-green-600">conteo confirmado</span></span>
                                                @endif
                                            @endif
                                            <x-buttondelete class="!px-2 !py-1 text-xs" onclick="confirmremovenp({{ $index }})">
                                                Eliminar
                                            </x-buttondelete>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-400 text-sm">Aún no has agregado ningún NP.</p>
                @endif
            </div>
            <span class="text-red-500 text-xs italic">
                @error('numberPartsList')
                    Debe agregar al menos un NP a la lista.
                @enderror
            </span>
        </div>

        <div class="w-full mt-4 flex justify-end">
            @if($tarima_selected == null)
                <x-button-primary class="ml-auto" wire:click="saveTarima">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 mr-2">
                        <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                    </svg>
                    Crear entrada
                </x-button-primary>
            @else
                <x-secondary-button class="ml-auto" wire:click="saveTarima">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 mr-1">
                    <path fill-rule="evenodd" d="M12 2.25a.75.75 0 0 1 .75.75v11.69l3.22-3.22a.75.75 0 1 1 1.06 1.06l-4.5 4.5a.75.75 0 0 1-1.06 0l-4.5-4.5a.75.75 0 1 1 1.06-1.06l3.22 3.22V3a.75.75 0 0 1 .75-.75Zm-9 13.5a.75.75 0 0 1 .75.75v2.25a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5V16.5a.75.75 0 0 1 1.5 0v2.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V16.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                    </svg>

                    Guardar cambios
                </x-secondary-button>
                @if($this->allConfirmed)
                    <x-button-primary class="ml-2" onclick="confirmEntry()">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 mr-1">
                            <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" />
                        </svg>
                        Confirmar entrada
                    </x-button-primary>
                @endif
            @endif
            
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

    function confirmcount(index) {
        Swal.fire({
            title: '¿Seguro que deseas confirmar el conteo de este NP?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#F27D16',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Si, confirmar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('confirmCount', index);
            }
        });
    }

    function confirmremovenp(index) {
        Swal.fire({
            title: '¿Seguro que deseas eliminar este NP de la lista?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#F27D16',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Si, eliminar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('removeNumberPart', index);
            }
        });
    }

    function confirmEntry() {
        Swal.fire({
            title: '¿Deseas confirmar la entrada?',
            text: 'Una vez confirmada, se registrará en el sistema y SE ENVIARÁ A PROCESOS.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#F27D16',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Si, confirmar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('confirmEntry');
            }
        });
    }
</script>
@endpush
