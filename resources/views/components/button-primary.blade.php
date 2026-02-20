<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-2 py-1 bg-primarycolor border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-primaryhovercolor focus:bg-primaryhovercolor active:bg-primaryhovercolor focus:outline-none focus:ring-2 focus:ring-primarycolor focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
