<div class="containerpric">
    <div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="text-secondarycolor text-2xl font-bold">Mi perfil</p>
            <p class="text-sm text-gray-500">Actualiza tus datos de acceso.</p>
        </div>
        <div class="hidden sm:flex items-center bg-white px-3 py-2 rounded-lg shadow-sm space-x-3">
            <div class="h-10 w-10 rounded-full bg-primarycolor text-white flex items-center justify-center font-semibold px-2 py-1">
                {{ strtoupper(mb_substr($name, 0, 1)) }}
            </div>
            <div class="text-xs text-gray-600">
                <p class="font-semibold">{{ $name }}</p>
                <p>{{ $email }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg my-3 p-6 border border-gray-100">
        <p class="italic text-sm font-semibold text-secondarycolor mb-4 flex items-center gap-2">
            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-primarycolor/10 text-primarycolor text-lg font-bold">1</span>
            Información de cuenta
        </p>

        <div class="space-y-4 md:max-w-xl md:mx-auto">
            <div class="w-full">
                <p class="text-secondarycolor">Nombre completo:</p>
                <input wire:model="name" type="text" class="inputcatalogues w-full">
                <span class="text-red-500 text-xs italic">
                    @error('name') {{ $message }} @enderror
                </span>
            </div>

            <div class="w-full">
                <p class="text-secondarycolor">Correo electrónico:</p>
                <input wire:model="email" type="email" class="inputcatalogues w-full">
                <span class="text-red-500 text-xs italic">
                    @error('email') {{ $message }} @enderror
                </span>
            </div>

            <div class="w-full flex space-x-4">
                <div class="w-full md:w-1/2 mb-4 md:mb-0">
                    <p class="text-secondarycolor">Puesto:</p>
                    <input type="text" class="inputcatalogues w-full bg-gray-100 cursor-not-allowed" value="{{ $title_job }}" disabled>
                    <!-- <span class="text-[11px] text-gray-500">Este dato solo puede ser modificado por un administrador.</span> -->
                </div>

                <div class="w-full md:w-1/2">
                    <p class="text-secondarycolor">Tipo de usuario:</p>
                    <input type="text" class="inputcatalogues w-full bg-gray-100 cursor-not-allowed" value="@switch($user_type)
                            @case(1)Administrador
                                @break
                            @case(2)Almacenista
                                @break
                            @case(3)Líder de producción
                                @break
                            @default
                                Desconocido
                        @endswitch" disabled>
                    <!-- <span class="text-[11px] text-gray-500">Este dato solo puede ser modificado por un administrador.</span> -->
                </div>
            </div>
        </div>

        <p class="italic text-sm font-semibold text-secondarycolor mt-6 mb-2 flex items-center gap-2 md:max-w-xl md:mx-auto">
            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-primarycolor/10 text-primarycolor text-lg font-bold">2</span>
            Cambiar contraseña
        </p>
        <div class="space-y-4 md:max-w-xl md:mx-auto">
            <div class="w-full">
                <p class="text-secondarycolor">Nueva contraseña:</p>
                <div x-data="{ show: false }">
                    <div class="relative">
                        <input wire:model="password" :type="show ? 'text' : 'password'" autocomplete="new-password" class="inputcatalogues w-full pr-10">
                        <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-secondarycolor" @click="show = !show">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5" x-show="!show">
                                <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5" x-show="show">
                                <path d="M3.53 2.47a.75.75 0 0 0-1.06 1.06l18 18a.75.75 0 1 0 1.06-1.06l-18-18ZM22.676 12.553a11.249 11.249 0 0 1-2.631 4.31l-3.099-3.099a5.25 5.25 0 0 0-6.71-6.71L7.759 4.577a11.217 11.217 0 0 1 4.242-.827c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113Z" />
                                <path d="M15.75 12c0 .18-.013.357-.037.53l-4.244-4.243A3.75 3.75 0 0 1 15.75 12ZM12.53 15.713l-4.243-4.244a3.75 3.75 0 0 0 4.244 4.243Z" />
                                <path d="M6.75 12c0-.619.107-1.213.304-1.764l-3.1-3.1a11.25 11.25 0 0 0-2.63 4.31c-.12.362-.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 0 1 6.75 12Z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <span class="text-red-500 text-xs italic">
                    @error('password') {{ $message }} @enderror
                </span>
            </div>

            <div class="w-full">
                <p class="text-secondarycolor">Confirmar contraseña:</p>
                <div x-data="{ show: false }">
                    <div class="relative">
                        <input wire:model="password_confirmation" :type="show ? 'text' : 'password'" autocomplete="new-password" class="inputcatalogues w-full pr-10">
                        <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-secondarycolor" @click="show = !show">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5" x-show="!show">
                                <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5" x-show="show">
                                <path d="M3.53 2.47a.75.75 0 0 0-1.06 1.06l18 18a.75.75 0 1 0 1.06-1.06l-18-18ZM22.676 12.553a11.249 11.249 0 0 1-2.631 4.31l-3.099-3.099a5.25 5.25 0 0 0-6.71-6.71L7.759 4.577a11.217 11.217 0 0 1 4.242-.827c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113Z" />
                                <path d="M15.75 12c0 .18-.013.357-.037.53l-4.244-4.243A3.75 3.75 0 0 1 15.75 12ZM12.53 15.713l-4.243-4.244a3.75 3.75 0 0 0 4.244 4.243Z" />
                                <path d="M6.75 12c0-.619.107-1.213.304-1.764l-3.1-3.1a11.25 11.25 0 0 0-2.63 4.31c-.12.362-.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 0 1 6.75 12Z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <span class="text-red-500 text-xs italic">
                    @error('password_confirmation') {{ $message }} @enderror
                </span>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <x-button-primary wire:click="save" wire:loading.attr="disabled">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 mr-2">
                    <path fill-rule="evenodd" d="M12 2.25a.75.75 0 0 1 .75.75v11.69l3.22-3.22a.75.75 0 1 1 1.06 1.06l-4.5 4.5a.75.75 0 0 1-1.06 0l-4.5-4.5a.75.75 0 1 1 1.06-1.06l3.22 3.22V3a.75.75 0 0 1 .75-.75Zm-9 13.5a.75.75 0 0 1 .75.75v2.25a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5V16.5a.75.75 0 0 1 1.5 0v2.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V16.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                </svg>

                Guardar cambios
            </x-button-primary>
        </div>
    </div>
    </div>
</div>
