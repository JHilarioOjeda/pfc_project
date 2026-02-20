<button {{ $attributes->merge(['type' => 'submit', 'class' => 'mr-2 inline-flex items-center p-1 bg-secondarycolor border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-secondaryhcolor focus:bg-secondarycolor active:secondarycolor focus:outline-none focus:ring-2 focus:ring-secondarycolor  focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    <svg class="size-4 mr-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="-10 -200 970 960">
        <path fill="currentColor" d="M120 648v-720h437l-80 80h-277v560h560v-278l80 -80v438h-720zM480 288v0v0v0v0v0v0v0v0zM360 408v-170l425 -425l167 171l-422 424h-170zM841 -16v0l-56 -56v0zM440 328h56l232 -232l-28 -28l-29 -28l-231 231v57zM700 68l-29 -28v0l29 28l28 28v0z" class="cdn-arten"></path>
     </svg>
    {{ $slot }}
</button>
