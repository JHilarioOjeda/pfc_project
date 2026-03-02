<x-app-layout>

    <livewire:processes.process :idprocess="$id"/>
    
    @stack('js') 
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
</x-app-layout>