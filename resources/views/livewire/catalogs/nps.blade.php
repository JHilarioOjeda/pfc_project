<div class="containerpric">

    <x-loading functionsList="scmodalnps, createUpdateNp, scmodalImportNps, analyzeImportFile, processImport" />

    <p class="text-secondarycolor text-2xl font-bold">NP</p>
    <div class="bg-white rounded-lg shadow-lg my-3 p-3">

        <div class="pb-4 w-full flex">
            <x-search-input class="lg:w-1/3 w-3/4" wireModel="search" placeholder="Buscar..." />
            <x-button-primary class="my-auto ml-auto whitespace-nowrap mr-2 !bg-[#217346] hover:!bg-[#1a5c38] focus:!ring-[#217346]" wire:click="scmodalImportNps">
                <svg class="size-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2a1 1 0 0 1 1 1v9.586l2.293-2.293a1 1 0 1 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 1 1 1.414-1.414L11 12.586V3a1 1 0 0 1 1-1Z"/>
                    <path d="M4 15a1 1 0 0 1 1 1v3h14v-3a1 1 0 1 1 2 0v4a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1Z"/>
                </svg>
                Carga Masiva
            </x-button-primary>
            <x-button-primary class="my-auto whitespace-nowrap" wire:click="scmodalnps(0)">
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
                        <th class="px-4 py-2 hidden lg:table-cell">Último Precio</th>
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
                                        <p><span class="font-semibold">Micras:</span> {{ round($np->microns) ?? '-' }}</p>
                                        <p><span class="font-semibold">Pulgadas:</span> {{ round($np->inches) ?? '-' }}</p>
                                        <p><span class="font-semibold">Decímetros:</span> {{ round($np->decimeters) ?? '-' }}</p>
                                        <p><span class="font-semibold">Último Precio:</span> {{ $np->latestPrice ? '$' . number_format($np->latestPrice->price, 2) : '-' }}</p>
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
                                    {{ round($np->microns, 2) ?? '-' }}
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    {{ round($np->inches, 2) ?? '-' }}
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    {{ round($np->decimeters, 2) ?? '-' }}
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    @if($np->latestPrice)
                                        <span class="text-green-700 font-semibold">${{ number_format($np->latestPrice->price, 2) }}</span>
                                        <br><span class="text-xs text-gray-500">{{ $np->latestPrice->price_date->format('d/m/Y') }}</span>
                                    @else
                                        -
                                    @endif
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
                            <td colspan="8" class="text-center py-4">No se encontraron NPs.</td>
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
                                <input wire:model="inches" wire:change="calculateDecimeters" type="number" step="0.000001" class="inputcatalogues w-full">
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

                        @if($npselected != null)
                        <div class="mt-6 border-t pt-4">
                            <p class="text-secondarycolor font-semibold text-lg mb-3">Precios</p>
                            
                            <div class="flex w-full space-x-4 items-end">
                                <div class="w-1/3">
                                    <p class="text-secondarycolor text-sm">Precio:</p>
                                    <input wire:model="newPrice" type="number" step="0.01" min="0" class="inputcatalogues w-full" placeholder="0.00">
                                    <span class="text-red-500 text-xs italic">@error('newPrice') {{ $message }} @enderror</span>
                                </div>
                                <div class="w-1/3 mt-auto">
                                    <x-secondary-button wire:click="addPrice" class="w-fit !text-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 mr-1">
                                            <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                                        </svg>
                                        Agregar Precio
                                    </x-secondary-button>
                                </div>
                            </div>

                            @if(count($npPrices) > 0)
                            <div class="mt-4 max-h-48 overflow-y-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-3 py-2 text-left">Precio</th>
                                            <th class="px-3 py-2 text-left">Fecha</th>
                                            <th class="px-3 py-2 text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($npPrices as $price)
                                        <tr class="border-b border-gray-200">
                                            <td class="px-3 py-2 font-semibold text-primarycolor">${{ number_format($price['price'], 2) }}</td>
                                            <td class="px-3 py-2">{{ \Carbon\Carbon::parse($price['price_date'])->format('d/m/Y') }}</td>
                                            <td class="px-3 py-2 text-center">
                                                <x-buttondelete onclick="deletePrice({{ $price['id'] }})">
                                                    Eliminar
                                                </x-buttondelete>

                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-gray-500 text-sm mt-3">No hay precios registrados.</p>
                            @endif
                        </div>
                        @endif
                    </div>

                    <x-button-primary wire:click="createUpdateNp" class="w-fit ml-auto mt-6">
                        @if($npselected == null) Crear @else Actualizar @endif
                    </x-button-primary>
                </div>
            </div>
        </div>
    </div>

    <div class="top-20 @if(!$modalImportNps) hidden @endif left-0 z-50 max-h-full overflow-y-auto">
        <div class="flex justify-center items-center bg-gray-800 antialiased top-0 opacity-70 left-0 z-30 w-full h-full fixed"></div>

        <div class="flex text-gray-500 text:md justify-center items-center antialiased top-0 left-0 z-40 w-full h-full fixed">
            <div class="flex flex-col w-11/12 lg:w-2/3 mx-auto rounded-lg overflow-y-auto bg-white px-6 py-3" style="max-height: 90%;">
                <div class="flex flex-row justify-between rounded-tl-lg rounded-tr-lg">
                    <p class="text-2xl w-fit my-auto font-semibold text-primarycolor">Importar NPs</p>
                    <button wire:click="scmodalImportNps" class="closebttn">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mt-4 space-y-4">
                    <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                        <p class="text-[11px] text-gray-600">Paso opcional: si necesitas guía de columnas, descarga la plantilla.</p>
                        <a href="{{ asset('nps_estructura.csv') }}" download="nps_estructura.csv" class="inline-flex items-center mt-1 text-[11px] text-gray-700 hover:text-gray-900 underline underline-offset-2">
                            <svg class="size-3 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 3a1 1 0 0 1 1 1v9.586l2.293-2.293a1 1 0 1 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 1 1 1.414-1.414L11 13.586V4a1 1 0 0 1 1-1Z"/>
                                <path d="M4 15a1 1 0 0 1 1 1v3h14v-3a1 1 0 1 1 2 0v4a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1Z"/>
                            </svg>
                            Descargar archivo de ejemplo
                        </a>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white p-4">
                        <p class="text-sm font-semibold text-gray-800">1) Selecciona el archivo</p>
                        <label class="mt-2 block rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-4 hover:border-[#217346] hover:bg-emerald-50 transition-colors cursor-pointer">
                            <div class="flex items-center gap-3">
                                <div class="rounded-md bg-[#217346] p-2 text-white">
                                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 16a1 1 0 0 1-1-1V6.414L8.707 8.707a1 1 0 0 1-1.414-1.414l4-4a1 1 0 0 1 1.414 0l4 4a1 1 0 1 1-1.414 1.414L13 6.414V15a1 1 0 0 1-1 1Z"/>
                                        <path d="M4 14a1 1 0 0 1 1 1v3h14v-3a1 1 0 1 1 2 0v4a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1Z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Haz clic para seleccionar el archivo</p>
                                    <p class="text-xs text-gray-500">Formatos permitidos: CSV, TXT, XLSX, XLS</p>
                                </div>
                            </div>
                            <input type="file" wire:model="importFile" accept=".csv,.txt,.xlsx,.xls" class="hidden" />
                        </label>

                        @if($importFile)
                        <p class="text-xs text-emerald-700 mt-2">
                            Archivo seleccionado: <span class="font-semibold">{{ $importFile->getClientOriginalName() }}</span>
                        </p>
                        @endif

                        <span class="text-red-500 text-xs italic">@error('importFile') {{ $message }} @enderror</span>

                        <div wire:loading wire:target="importFile" class="text-amber-700 text-sm mt-2 font-semibold">
                            Subiendo archivo...
                        </div>

                        <p class="text-sm font-semibold text-gray-800 mt-4">2) Analiza y luego importa</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <x-secondary-button wire:click="analyzeImportFile" wire:loading.attr="disabled" wire:target="analyzeImportFile,processImport">
                                Analizar Archivo
                            </x-secondary-button>
                            <x-button-primary wire:click="processImport" wire:loading.attr="disabled" wire:target="analyzeImportFile,processImport" :disabled="!$importIsAnalyzed">
                                Importar Registros
                            </x-button-primary>
                        </div>

                        @if(!$importIsAnalyzed)
                        <p class="text-xs text-gray-500 mt-2">Primero analiza el archivo para habilitar la importación.</p>
                        @endif

                        <div wire:loading wire:target="analyzeImportFile" class="text-blue-700 text-sm mt-3 font-semibold">
                            Analizando archivo...
                        </div>

                        <div wire:loading wire:target="processImport" class="text-green-700 text-sm mt-3 font-semibold">
                            Guardando registros, por favor espera...
                        </div>
                    </div>

                    @if($importNewCount > 0 || $importUpdateCount > 0)
                    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="rounded-lg bg-green-50 border border-green-200 p-3">
                            <p class="text-sm text-green-700">Nuevos registros</p>
                            <p class="text-2xl font-bold text-green-800">{{ $importNewCount }}</p>
                        </div>
                        <div class="rounded-lg bg-amber-50 border border-amber-200 p-3">
                            <p class="text-sm text-amber-700">Registros a actualizar</p>
                            <p class="text-2xl font-bold text-amber-800">{{ $importUpdateCount }}</p>
                        </div>
                    </div>
                    @endif

                    @if(count($importPreview) > 0)
                    <div class="mt-4 max-h-72 overflow-y-auto border border-gray-200 rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 sticky top-0">
                                <tr>
                                    <th class="px-3 py-2 text-left">Numero de Parte</th>
                                    <th class="px-3 py-2 text-left">Acabado</th>
                                    <th class="px-3 py-2 text-left">IN²</th>
                                    <th class="px-3 py-2 text-left">Micraje</th>
                                    <th class="px-3 py-2 text-left">Precio</th>
                                    <th class="px-3 py-2 text-left">Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($importPreview as $row)
                                <tr class="border-b border-gray-200">
                                    <td class="px-3 py-2 font-semibold">{{ $row['partnumber'] }}</td>
                                    <td class="px-3 py-2">{{ $row['process'] ?: '-' }}</td>
                                    <td class="px-3 py-2">{{ $row['inches'] ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $row['microns'] ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $row['price'] ?? '-' }}</td>
                                    <td class="px-3 py-2">
                                        @if(!empty($row['id']))
                                            <span class="text-amber-700 font-semibold">Actualizar</span>
                                        @else
                                            <span class="text-green-700 font-semibold">Crear</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Mostrando los primeros 10 registros del archivo.</p>
                    @endif
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

            function deletePrice(priceId){
                Swal.fire({
                    title: '¿Seguro que deseas eliminar este precio?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#F27D16',
                    cancelButtonColor: '#EF4444',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('deletePrice', priceId);
                    }
                })
            }
        </script>
    @endpush
</div>
