<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo"></x-slot>

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}" x-data="{ loading: false, email: '', password: '' }" @submit="loading = true">
            @csrf

            {{-- Hero de marca --}}
            <div class="relative -mx-6 -mt-4 mb-8 overflow-hidden bg-white px-6 py-8 text-center">

                <div class="relative mx-auto w-fit shadow-lg ring-4 ring-[#eeeeee] rounded">
                    <x-application-logo class="mx-auto h-auto w-56 max-w-full object-contain rounded" />
                </div>
            </div>

            <div class="space-y-5">
                <div>
                    <x-label for="email" value="{{ __('Email') }}" />
                    <div class="relative mt-1">
                        <svg x-show="! email" x-cloak class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                        <x-input id="email" class="block mt-0 w-full rounded-lg bg-gray-50 ps-9 focus:bg-white" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" @input="email = $event.target.value" />
                    </div>
                    <x-input-error for="email" class="mt-2" />
                </div>

                <div x-data="{ show: false }">
                    <x-label for="password" value="{{ __('Password') }}" />
                    <div class="relative mt-1">
                        <svg x-show="! password" x-cloak class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                        <x-input id="password" class="block w-full rounded-lg bg-gray-50 ps-9 pe-10 focus:bg-white" type="password" x-bind:type="show ? 'text' : 'password'" name="password" required autocomplete="current-password" @input="password = $event.target.value"/>
                        <button type="button" class="absolute inset-y-0 end-0 flex items-center px-3 text-sm text-gray-500 hover:text-gray-700 focus:outline-none" @click="show = ! show">
                            <svg x-show="! show" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
                            </svg>

                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                <path d="M3.53 2.47a.75.75 0 0 0-1.06 1.06l18 18a.75.75 0 1 0 1.06-1.06l-18-18ZM22.676 12.553a11.249 11.249 0 0 1-2.631 4.31l-3.099-3.099a5.25 5.25 0 0 0-6.71-6.71L7.759 4.577a11.217 11.217 0 0 1 4.242-.827c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113Z" />
                                <path d="M15.75 12c0 .18-.013.357-.037.53l-4.244-4.243A3.75 3.75 0 0 1 15.75 12ZM12.53 15.713l-4.243-4.244a3.75 3.75 0 0 0 4.244 4.243Z" />
                                <path d="M6.75 12c0-.619.107-1.213.304-1.764l-3.1-3.1a11.25 11.25 0 0 0-2.63 4.31c-.12.362-.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 0 1 6.75 12Z" />
                            </svg>
                        </button>
                    </div>
                    <x-input-error for="password" class="mt-2" />
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label for="remember_me" class="flex items-center">
                        <x-checkbox id="remember_me" name="remember" />
                        <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div>
                    <x-button-primary class="w-full justify-center rounded-lg !py-3 !px-0 shadow-lg shadow-primarycolor/30" x-bind:disabled="loading">
                        <svg x-show="loading" x-cloak class="animate-spin -ms-1 me-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-show="! loading">{{ __('Iniciar sesión') }}</span>
                        <span x-show="loading" x-cloak>{{ __('Ingresando...') }}</span>
                    </x-button-primary>
                </div>
            </div>

            <div class="border-t mt-6 pt-4 text-center">
                <p class="text-xs text-gray-400">Versión 1.2.0</p>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
