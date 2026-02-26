<header class="no-print sticky top-0 z-20 bg-white w-full text-gray-600 body-font shadow-sm flex flex-wrap p-5 flex-row items-center justify-between">
    <a class="flex title-font font-medium items-center text-gray-900 mb-4 lg:mb-0 border-r border-borderseparate pr-5" href="/">
        <img class="h-12" src="/imgs/logos/principallogo.jpg" alt="logo">
    </a>
    <!-- Botón del menú móvil -->
    <button id="header-nav" class="lg:hidden">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
        </svg>
    </button>
    <!-- Menú de navegación -->
    <nav id="header-menu" class="hidden lg:flex flex-col lg:flex-row w-full lg:w-auto items-center lg:space-x-6">
        <div class="flex flex-col lg:flex-row lg:space-x-3 space-y-3 lg:space-y-0">
            <!-- Opciones del menú principal -->
                <div class="relative">
                    <x-dropdown align="right" class="w-fit">
                        <x-slot name="trigger">
                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-white hover:bg-primarycolor  focus:outline-none focus:bg-primarycolor focus:text-white active:bg-primarycolor active:text-white transition ease-in-out duration-150">
                                Catálogos
                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link href="/admin/users">
                                Usuarios
                            </x-dropdown-link>
                            <x-dropdown-link href="/admin/customers">
                                Clientes
                            </x-dropdown-link>
                            <x-dropdown-link href="/admin/np">
                                NP
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div>

                <a href="/storage" class="inline-flex items-center px-3 py-2 border border-transparent leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-white hover:bg-primarycolor focus:outline-none focus:bg-primarycolor focus:text-white active:bg-primarycolor active:text-white transition ease-in-out duration-150">
                    Almacén
                </a>
                <a href="/processes" class="inline-flex items-center px-3 py-2 border border-transparent leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-white hover:bg-primarycolor focus:outline-none focus:bg-primarycolor focus:text-white active:bg-primarycolor active:text-white transition ease-in-out duration-150">
                    Procesos
                </a>
                <!-- <a href="/reports" class="inline-flex items-center px-3 py-2 border border-transparent leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-white hover:bg-primarycolor focus:outline-none focus:bg-primarycolor focus:text-white active:bg-primarycolor active:text-white transition ease-in-out duration-150">
                    Reportes
                </a> -->
        </div>

        <!-- Opciones de perfil -->
        <div class="flex flex-col lg:flex-row lg:space-x-3 space-y-3 lg:space-y-0 mt-4 lg:mt-0">
            <a href="/myprofile" class="flex items-center px-3 py-2 border border-transparent leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-white hover:bg-primarycolor focus:outline-none focus:bg-primarycolor focus:text-white active:bg-primarycolor active:text-white transition ease-in-out duration-150">
                Mi perfil
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 my-auto ml-2">
                    <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                </svg>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="flex">
                @csrf
                <button type="submit" class="flex items-center px-3 py-2 border border-transparent leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-white hover:bg-primarycolor focus:outline-none focus:bg-primarycolor focus:text-white active:bg-primarycolor active:text-white transition ease-in-out duration-150">
                    Salir
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 my-auto ml-2">
                        <path fill-rule="evenodd" d="M7.5 3.75A1.5 1.5 0 0 0 6 5.25v13.5a1.5 1.5 0 0 0 1.5 1.5h6a1.5 1.5 0 0 0 1.5-1.5V15a.75.75 0 0 1 1.5 0v3.75a3 3 0 0 1-3 3h-6a3 3 0 0 1-3-3V5.25a3 3 0 0 1 3-3h6a3 3 0 0 1 3 3V9A.75.75 0 0 1 15 9V5.25a1.5 1.5 0 0 0-1.5-1.5h-6Zm10.72 4.72a.75.75 0 0 1 1.06 0l3 3a.75.75 0 0 1 0 1.06l-3 3a.75.75 0 1 1-1.06-1.06l1.72-1.72H9a.75.75 0 0 1 0-1.5h10.94l-1.72-1.72a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg> 
                </button>
            </form>
        </div>
    </nav>
</header>

<script>
    document.getElementById('header-nav').addEventListener('click', function () {
        const menu = document.getElementById('header-menu');
        menu.classList.toggle('hidden');
    });
</script>