<div class="containerpric">

    <x-loading functionsList="scmodalusers, createUpdateUser" />

    <p class="text-secondarycolor text-2xl font-bold">Usuarios</p>
    <div class="bg-white rounded-lg shadow-lg my-3 p-3">

        <div class="pb-4 w-full flex">
            <x-search-input class="lg:w-1/3 w-3/4" wireModel="search" placeholder="Buscar..." />
            <x-button-primary class="my-auto ml-auto whitespace-nowrap" wire:click="scmodalusers(0)">
                <svg class="size-6 mr-2 font-semibold" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="-10 -200 970 960">
                    <path fill="currentColor" d="M440 328h-240v-80h240v-240h80v240h240v80h-240v240h-80v-240z"></path>
                 </svg>
                Crear usuario
            </x-button-primary>
        </div>

        <div class="relative overflow-x-auto rounded-lg">
            <table class="table table-hover w-full text-left">
                <thead>
                    <tr class="bg-gray-200 text-sm font-semibold">
                        <th class="px-4 py-2">Nombre</th>
                        <th class="px-4 py-2 hidden lg:table-cell">Correo electrónico</th>
                        <th class="px-4 py-2 hidden lg:table-cell">Puesto</th>
                        <th class="px-4 py-2 hidden lg:table-cell">Tipo de usuario</th>
                        <th class="px-4 py-2 hidden lg:table-cell">Estatus</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($users) != 0)
                        @foreach ($users as $user)
                            <tr class="border-b border-gray-200 text-sm">
                                <!-- Nombre -->
                                <td scope="row" class="px-4 py-2 font-medium whitespace-nowrap">
                                    <span class="font-bold">{{$user->name}}</span>
                                    <!-- Datos extra en móvil -->
                                    <div class="block lg:hidden mt-2 text-gray-500 text-xs">
                                        <p class="text-secondarycolor"><span class="font-semibold">Correo electrónico:</span> {{$user->email}}</p>
                                        <p><span class="font-semibold">Puesto:</span> {{$user->title_job}}</p>
                                        <p>
                                            <span class="font-semibold">Tipo de usuario:</span> 
                                            @switch($user->user_type)
                                                @case(1)
                                                    Administrador
                                                    @break
                                                @case(2)
                                                    Almacenista
                                                    @break
                                                @case(3)
                                                    Líder de producción
                                                    @break
                                                @case(4)
                                                    Personal de Secado
                                                    @break
                                                @case(5)
                                                    Calidad
                                                    @break
                                                @case(6)
                                                    Coordinador de producción
                                                    @break
                                                @default
                                                    Desconocido
                                            @endswitch
                                        </p>
                                        <p class="pt-3">
                                            <span class="font-semibold">Estatus:</span> 
                                            @if($user->active)
                                                <span class="text-green-600 p-1 rounded-lg bg-green-200 text-xs font-semibold">Activo</span>
                                            @else
                                                <span class="text-red-600 p-1 rounded-lg bg-red-200 text-xs font-semibold">Inactivo</span>
                                            @endif
                                        </p>
                                    </div>
                                </td>
                                
                                <!-- Columnas visibles solo en pantallas grandes o más grandes -->
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    {{$user->email}}
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    {{$user->title_job}}
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    @switch($user->user_type)
                                        @case(1)
                                            Administrador
                                            @break
                                        @case(2)
                                            Almacenista
                                            @break
                                        @case(3)
                                            Líder de producción
                                            @break
                                        @case(4)
                                            Personal de Secado
                                            @break
                                        @case(5)
                                            Calidad
                                            @break
                                        @case(6)
                                            Coordinador de producción
                                            @break
                                        @default
                                            Desconocido
                                    @endswitch
                                </td>
                                <td class="px-4 py-2 hidden lg:table-cell">
                                    @if($user->active)
                                        <span class="text-green-600 p-2 rounded-lg text-xs font-semibold bg-green-200">Activo</span>
                                    @else
                                        <span class="text-red-600 p-2 rounded-lg text-xs font-semibold bg-red-200">Inactivo</span>
                                    @endif
                                </td>
                                 <!-- Botones -->
                                @if(Auth::user()->user_type == '1')
                                    <td class="px-4 py-2 text-center">
                                        @if($user->active)
                                            <x-buttonedit wire:click="scmodalusers({{$user->id}})">Editar</x-buttonedit>
                                            <x-buttondesact onclick="desactusers('{{$user->id}}')" class="mr-2 mt-2">Desactivar</x-buttondesact>
                                        @else
                                            <x-buttonact onclick="actusers('{{$user->id}}')" class="mr-2 mt-2">Activar</x-buttonact>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-primarycolor text-xl text-center">No hay registros.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div>
        </div>
    </div>


    <div class="top-20 @if(!$modalceusers) hidden @endif left-0 z-50 max-h-full overflow-y-auto">
        <div class="flex justify-center items-center  bg-gray-800 antialiased top-0 opacity-70 left-0  z-30 w-full h-full fixed "></div>
        
        <div class="flex text-gray-500 text:md justify-center items-center antialiased top-0  left-0  z-40 w-full h-full fixed">
            <div class=" flex flex-col w-11/12 lg:w-1/2 mx-auto rounded-lg overflow-y-auto bg-white px-6 py-3" style="max-height: 90%;">
                <div class="flex flex-row justify-between rounded-tl-lg rounded-tr-lg">
                        <p class="text-2xl w-fit my-auto font-semibold text-primarycolor">@if($userselected == null) Crear usuario @else Editar usuario @endif</p>
                    <button wire:click="scmodalusers(0)" class="closebttn">
                        <svg  class="w-6 h-6 text-white"  fill="none"
                        stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="flex flex-col mt-3">
                    <p class="italic text-sm font-semibold text-secondarycolor">Información de cuenta</p>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-2">
                        <div class="md:flex w-full md:space-x-4">
                            <div class="w-full md:w-1/2">
                                <p class="text-secondarycolor">Nombre completo:</p>
                                <input wire:model ="name" type="text" class="inputcatalogues w-full">
                                <div>
                                    <span class="text-red-500 text-xs italic">
                                        @error('name')
                                            {{$message}}
                                        @enderror
                                    </span>
                                </div>
                            </div>
                            <div class="w-full md:w-1/2">
                                <p class="text-secondarycolor">Correo electrónico:</p>
                                <input wire:model ="email" type="text" class="inputcatalogues w-full">
                                <div>
                                    <span class="text-red-500 text-xs italic">
                                        @error('email')
                                            {{$message}}
                                        @enderror
                                    </span>
                                </div>
                            </div>
                            
                        </div>
                        <div class="flex mt-3 md:space-x-4">
                            <div class="w-full md:w-1/2">
                                    <p class="text-secondarycolor">Nueva contraseña:</p>
                                    <div class="" x-data="{ show: true }">
                                        <div class="relative">
                                            <input wire:model ="password" :type="show ? 'password' : 'text'" autocomplete="new-password" class="w-full border border-gray-300 focus:border-primarycolor focus:ring-1 outline-none focus:ring-primarycolor rounded-md shadow-sm appearance-none p-1">
                                            <div class="absolute inset-y-0 right-0 flex items-center text-sm leading-5 pr-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 text-secondarycolor" @click="show = !show" :class="{'hidden': !show, 'block':show }">
                                                    <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                                    <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
                                                </svg>

                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 text-secondarycolor" @click="show = !show" :class="{'block': !show, 'hidden':show }">
                                                    <path d="M3.53 2.47a.75.75 0 0 0-1.06 1.06l18 18a.75.75 0 1 0 1.06-1.06l-18-18ZM22.676 12.553a11.249 11.249 0 0 1-2.631 4.31l-3.099-3.099a5.25 5.25 0 0 0-6.71-6.71L7.759 4.577a11.217 11.217 0 0 1 4.242-.827c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113Z" />
                                                    <path d="M15.75 12c0 .18-.013.357-.037.53l-4.244-4.243A3.75 3.75 0 0 1 15.75 12ZM12.53 15.713l-4.243-4.244a3.75 3.75 0 0 0 4.244 4.243Z" />
                                                    <path d="M6.75 12c0-.619.107-1.213.304-1.764l-3.1-3.1a11.25 11.25 0 0 0-2.63 4.31c-.12.362-.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 0 1 6.75 12Z" />
                                                </svg>

                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-red-500 text-xs italic">
                                            @error('password')
                                                {{$message}}
                                            @enderror
                                        </span>
                                    </div>
                            </div>
                        </div>
                    </div>
                    

                    <p class="italic text-sm font-semibold text-secondarycolor mt-5">Información de usuario</p>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-2">
                        <div class="w-full md:flex my-3 md:space-x-4">
                            <div class="w-full md:w-1/2"
                                x-data="{
                                    userType: $wire.entangle('user_type'),
                                    get desc() {
                                        const v = this.userType;
                                        return (v && String(v) !== 'null') ? (this.info[String(v)] ?? null) : null;
                                    },
                                    info: {
                                        '1': {
                                            label: 'Administrador',
                                            color: 'text-purple-700 bg-purple-50 border-purple-200',
                                            dot: 'bg-purple-500',
                                            items: [
                                                'Gestión completa de usuarios, clientes y números de parte',
                                                'Control total del almacén y movimiento de tarimas',
                                                'Supervisión de todos los procesos de producción',
                                                'Acceso a estadísticas y dashboard del sistema',
                                                'Reportes generales, de medición por tarima y de procesos'
                                            ]
                                        },
                                        '2': {
                                            label: 'Almacenista',
                                            color: 'text-blue-700 bg-blue-50 border-blue-200',
                                            dot: 'bg-blue-500',
                                            items: [
                                                'Registrar, consultar y actualizar tarimas en almacén',
                                                'Ver el estado e historial de movimientos de inventario'
                                            ]
                                        },
                                        '6': {
                                            label: 'Coordinador de producción',
                                            color: 'text-orange-700 bg-orange-50 border-orange-200',
                                            dot: 'bg-orange-500',
                                            items: [
                                                'Supervisar los procesos de todos los líderes de línea',
                                                'Consultar reportes de procesos de cualquier líder',
                                                'Monitorear el estado general de producción en tiempo real'
                                            ]
                                        },
                                        '3': {
                                            label: 'Líder de producción',
                                            color: 'text-green-700 bg-green-50 border-green-200',
                                            dot: 'bg-green-500',
                                            items: [
                                                'Gestionar los procesos de su propia línea de producción',
                                                'Registrar cargas, piezas procesadas y tiempos muertos',
                                                'Llenar el checklist de arranque de turno',
                                                'Consultar reportes de procesos de su línea'
                                            ]
                                        },
                                        '4': {
                                            label: 'Personal de Secado',
                                            color: 'text-yellow-700 bg-yellow-50 border-yellow-200',
                                            dot: 'bg-yellow-500',
                                            items: [
                                                'Registrar piezas procesadas en la etapa de secado',
                                                'Acceso a los procesos asignados al área de secado'
                                            ]
                                        },
                                        '5': {
                                            label: 'Calidad',
                                            color: 'text-red-700 bg-red-50 border-red-200',
                                            dot: 'bg-red-500',
                                            items: [
                                                'Revisar y validar procesos desde la perspectiva de calidad',
                                                'Generar y consultar reportes de medición por tarima'
                                            ]
                                        }
                                    }
                                }"
                            >
                                <p class="text-secondarycolor">Tipo de usuario:</p>
                                <select wire:model="user_type" class="inputcatalogues w-full" id="usertype">
                                    <option value="null">Seleccionar...</option>
                                    <option value="1">Administrador</option>
                                    <option value="2">Almacenista</option>
                                    <option value="6">Coordinador de producción</option>
                                    <option value="3">Líder de producción</option>
                                    <option value="4">Personal de Secado</option>
                                    <option value="5">Calidad</option>
                                </select>
                                <div>
                                    <span class="text-red-500 text-xs italic">
                                        @error('user_type')
                                            {{$message}}
                                        @enderror
                                    </span>
                                </div>

                                <div
                                    x-show="desc !== null"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="mt-2 border rounded-lg p-2.5"
                                    :class="desc?.color ?? ''"
                                >
                                    <div class="flex items-center gap-1.5 mb-1.5">
                                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" :class="desc?.dot ?? ''"></span>
                                        <p class="text-xs font-bold" x-text="desc?.label ?? ''"></p>
                                    </div>
                                    <ul class="space-y-1">
                                        <template x-for="(item, idx) in desc?.items ?? []" :key="idx">
                                            <li class="flex items-start gap-1.5 text-xs opacity-80">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-3.5 flex-shrink-0 mt-0.5">
                                                    <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" />
                                                </svg>
                                                <span x-text="item"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>

                            <div class="w-full md:w-1/2">
                                <p class="text-secondarycolor">Posición de trabajo:</p>
                                <input wire:model ="title_job" type="text" class="inputcatalogues w-full">
                                <div>
                                    <span class="text-red-500 text-xs italic">
                                        @error('title_job')
                                            {{$message}}
                                        @enderror
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    

                    

                    
                    {{-- <div class="w-1/2 relative mt-5">
                        <p class="text-secondarycolor">Firma:</p>
                        <p class="text-primarycolor text-xs">Coloca una foto de tu firma que ira en los certificados. Esta firma solo la puedes modificar</p>
                        <div class="relative h-32 w-32 shadow-xl mx-auto  border-gray-100 rounded-lg overflow-hidden border-2 mb-4">
                            <label for="imguseredit">
                                @if($sign_photo_path)
                                    @if($sign_photo_path == $old_sign_photo_path )
                                        <img  src="{{$sign_photo_path}}" class="h-32 w-32 rounded-full object-cover">
                                    @else
                                        <img src="{{$sign_photo_path->temporaryUrl()}}" class="h-32 w-32 rounded-full object-cover">
                                    @endif
                                @else
                                    <img class="h-32 w-32 rounded-full object-cover" src="{{$img}}" alt="firma" />
                                @endif
                        
                                <input type="file" wire:change="changeflag" wire:model="sign_photo_path" name="imguseredit" id="imguseredit" accept="image/*" style="display: none;">
                            </label>
                        </div>
                    </div>
                     --}}
                   
                    
                    <x-button-primary wire:click="createUpdateUser" class="w-fit ml-auto mt-6"> @if($userselected == null) Crear @else Actualizar @endif </x-button-primary>
                    
                </div>
            </div>
        </div>
    </div>
    
    @push('js')
        <script>

            function desactusers(iduser){
                Swal.fire({
                    title: '¿Seguro que deseas desactivar este usuario?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#F27D16',
                    cancelButtonColor: '#EF4444',
                    confirmButtonText: 'Si, desactivar',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                       @this.call('desactivateuser', iduser);
                    }
                })
            }

            function actusers(iduser){
                Swal.fire({
                    title: '¿Seguro que deseas activar este usuario?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#F27D16',
                    cancelButtonColor: '#EF4444',
                    confirmButtonText: 'Si, activar',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('activateuser', iduser);
                    }
                })
            }
            
        </script>
    @endpush
