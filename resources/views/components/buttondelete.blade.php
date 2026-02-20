<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center p-1 bg-dangercolor border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dangerhcolor focus:bg-dangerhcolor active:dangerhcolor focus:outline-none focus:ring-2 focus:ring-dangerhcolor  focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    <svg class="size-4 mr-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="-10 -200 970 960">
        <path fill="currentColor" d="M200 648v-600h-40v-80h200v-40h240v40h200v80h-40v600h-560zM280 568h400v-520h-400v520zM360 488h80v-360h-80v360zM520 488h80v-360h-80v360zM280 48v0v520v0v-520z" class="cdn-arten"></path>
     </svg>
    {{ $slot }}
</button>
