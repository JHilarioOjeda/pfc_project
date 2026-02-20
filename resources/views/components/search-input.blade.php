<div class="relative w-full">
    <span class="absolute inset-y-0 left-1 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="-10 -200 970 960" class="size-7 text-secondarycolor">
            <path fill="currentColor" d="M784 648l-252 -252q-30 24 -69 38t-83 14q-109 0 -184.5 -75.5t-75.5 -184.5t75.5 -184.5t184.5 -75.5t184.5 75.5t75.5 184.5q0 44 -14 83t-38 69l252 252zM380 368q75 0 127.5 -52.5t52.5 -127.5t-52.5 -127.5t-127.5 -52.5t-127.5 52.5t-52.5 127.5t52.5 127.5
            t127.5 52.5z" class=""></path>
        </svg>
    </span>
    <input {!! $attributes->merge(['class' => 'block bg-primarylight border border-gray-300 rounded-lg pl-10 py-2 focus:border-primarycolor focus:ring-primarycolor focus:ring-1 ']) !!}" 
           wire:model.live="{{ $wireModel }}" 
           placeholder="{{ $placeholder }}" 
           type="text" 
           name="search">
</div>