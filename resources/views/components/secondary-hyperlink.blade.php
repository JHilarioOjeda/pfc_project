<a href="{{$href}}" {{ $attributes->merge(['class' => 'inline-flex items-center px-2 py-1 bg-secondarycolor border border-secondarycolor rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-secondaryhcolor focus:outline-none focus:ring-2 focus:ring-secondaryhcolor focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</a>
