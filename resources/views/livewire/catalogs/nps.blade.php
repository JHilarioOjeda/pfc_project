<div class="containerpric">

    <x-loading functionsList="scmodalnps, createUpdateNp" />

    <p class="text-secondarycolor text-2xl font-bold">NP</p>
    <div class="bg-white rounded-lg shadow-lg my-3 p-3">

        <div class="pb-4 w-full flex">
            <x-search-input class="lg:w-1/3 w-3/4" wireModel="search" placeholder="Buscar..." />
            <x-button-primary class="my-auto ml-auto whitespace-nowrap" wire:click="scmodalnps(0)">
                <svg class="size-6 mr-2 font-semibold" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="-10 -200 970 960">
                    <path fill="currentColor" d="M440 328h-240v-80h240v-240h80v240h240v80h-240v240h-80v-240z"></path>
                 </svg>
                Crear NP
            </x-button-primary>
        </div>

        <div class="relative overflow-x-auto rounded-lg">
            <table class="table table-hover w-full text-left">
                <thead>
                    <tr class="bg-gray-200 text-sm font-semibold">
                        <th class="px-4 py-2">NP</th>
                        <th class="px-4 py-2 hidden lg:table-cell">Proceso</th>
                        <th class="px-4 py-2 hidden lg:table-cell">Micras</th>
                        <th class="px-4 py-2 hidden lg:table-cell">Pulgadas</th>
                        <th class="px-4 py-2 hidden lg:table-cell">Decímetros</th>
                        <th class="px-4 py-2 hidden lg:table-cell">Estatus</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($nps) != 0)
                        @foreach ($nps as $np)
                            <tr class="border-b border-gray-200 text-sm">
                                <td scope="row" class="px-4 py-2 font-medium whitespace-nowrap">
                                    <span class="font-bold">{{ $np->partnumber }}</span>

                                    <div class="block lg:hidden mt-2 text-gray-500 text-xs">
                                        <p class="text-secondarycolor"><span class="font-semibold">Proceso:</span> {{ $np->process ?? '-' }}</p>
                                        <p><span class="font-semibold">Detalles:</span> {{ $np->details ?? '-' }}</p>
                                        <p><span class="font-semibold">Micras:</span> {{ $np->microns ?? '-' }}</p>
                                        <p><span class="font-semibold">Pulgadas:</span> {{ $np->inches ?? '-' }}</p>
                                        <p><span class="font-semibold">Decímetros:</span> {{ $np->decimeters ?? '-' }}</p>
                                        <p class="pt-3">
                                            <span class="font-semibold">Estatus:</span>
                                            @if($np->active)
                                                <span class="text-green-600 p-1 rounded-lg bg-green-200 text-xs font-semibold">Activo</span>
                                            @else
                                                <span class="text-red-600 p-1 rounded-lg bg-red-200 text-xs font-semibold">Inactivo</span>
                                            @endif
                                        </p>
                                    </div>
                                </td>

                                <td class="px-4 py-2 hidden lg:table-cell">
                                    {{ $np->process ?? '-' }}
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    {{ $np->microns ?? '-' }}
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    {{ $np->inches ?? '-' }}
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    {{ $np->decimeters ?? '-' }}
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    @if($np->active)
                                        <span class="text-green-600 p-1 rounded-lg bg-green-200 text-xs font-semibold">Activo</span>
                                    @else
                                        <span class="text-red-600 p-1 rounded-lg bg-red-200 text-xs font-semibold">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center">
                                    @if($np->active)
                                        <x-buttonedit wire:click="scmodalnps({{ $np->id }})">Editar</x-buttonedit>
                                        <x-buttondesact onclick="changenpstatus('{{ $np->id }}', 'desactivate')" class="mr-2 mt-2">Desactivar</x-buttondesact>
                                    @else
                                        <x-buttonact onclick="changenpstatus('{{ $np->id }}', 'activate')" class="mr-2 mt-2">Activar</x-buttonact>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center py-4">No se encontraron NPs.</td>
                        </tr>
                    @endif
                </tbody>
           </table>
        </div>
    </div>

    <div class="top-20 @if(!$modalcenps) hidden @endif left-0 z-50 max-h-full overflow-y-auto">
        <div class="flex justify-center items-center bg-gray-800 antialiased top-0 opacity-70 left-0 z-30 w-full h-full fixed"></div>

        <div class="flex text-gray-500 text:md justify-center items-center antialiased top-0 left-0 z-40 w-full h-full fixed">
            <div class="flex flex-col w-11/12 lg:w-1/2 mx-auto rounded-lg overflow-y-auto bg-white px-6 py-3" style="max-height: 90%;">
                <div class="flex flex-row justify-between rounded-tl-lg rounded-tr-lg">
                    <p class="text-2xl w-fit my-auto font-semibold text-primarycolor">
                        @if($npselected == null) Crear NP @else Editar NP @endif
                    </p>
                    <button wire:click="scmodalnps(0)" class="closebttn">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="flex flex-col mt-3">
                    <div class="rounded-lg p-2">
                        <div class="md:flex w-full md:space-x-4">
                            <div class="w-full md:w-1/2">
                                <p class="text-secondarycolor">NP:</p>
                                <input wire:model="partnumber" type="text" class="inputcatalogues w-full">
                                <div>
                                    <span class="text-red-500 text-xs italic">
                                        @error('partnumber')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            </div>
                            <div class="w-full md:w-1/2">
                                <p class="text-secondarycolor">Proceso:</p>
                                <input wire:model="process" type="text" class="inputcatalogues w-full">
                                <div>
                                    <span class="text-red-500 text-xs italic">
                                        @error('process')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="w-full mt-3">
                            <p class="text-secondarycolor">Detalles:</p>
                            <textarea wire:model="details" class="inputcatalogues w-full" rows="3"></textarea>
                            <div>
                                <span class="text-red-500 text-xs italic">
                                    @error('details')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>

                        <div class="md:flex w-full md:space-x-4 mt-3">
                            <div class="w-full md:w-1/3">
                                <p class="text-secondarycolor">Micras:</p>
                                <input wire:model="microns" type="number" step="0.000001" class="inputcatalogues w-full">
                                <div>
                                    <span class="text-red-500 text-xs italic">
                                        @error('microns')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            </div>
                            <div class="w-full md:w-1/3">
                                <p class="text-secondarycolor">Pulgadas:</p>
                                <input wire:model="inches" type="number" step="0.000001" class="inputcatalogues w-full">
                                <div>
                                    <span class="text-red-500 text-xs italic">
                                        @error('inches')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            </div>
                            <div class="w-full md:w-1/3">
                                <p class="text-secondarycolor">Decímetros:</p>
                                <input wire:model="decimeters" type="number" step="0.000001" class="inputcatalogues w-full">
                                <div>
                                    <span class="text-red-500 text-xs italic">
                                        @error('decimeters')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <x-button-primary wire:click="createUpdateNp" class="w-fit ml-auto mt-6">
                        @if($npselected == null) Crear @else Actualizar @endif
                    </x-button-primary>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            function changenpstatus(idnp, action){
                if(action === 'desactivate'){
                    var message = 'desactivar';
                }else{
                    var message = 'activar';
                }

                Swal.fire({
                    title: '¿Seguro que deseas ' + message + ' este NP?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#F27D16',
                    cancelButtonColor: '#EF4444',
                    confirmButtonText: 'Si, ' + message,
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('changeNpStatus', idnp);
                    }
                })
            }
        </script>
    @endpush
</div>
