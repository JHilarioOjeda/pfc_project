@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Paginacion" class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-1 px-1">

            {{-- Info texto --}}
            <p class="text-xs text-gray-500 order-2 sm:order-1">
                Mostrando
                @if ($paginator->firstItem())
                    <span class="font-semibold text-secondarycolor">{{ $paginator->firstItem() }}</span>
                    &ndash;
                    <span class="font-semibold text-secondarycolor">{{ $paginator->lastItem() }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
                de <span class="font-semibold text-secondarycolor">{{ $paginator->total() }}</span> resultados
            </p>

            {{-- Controles --}}
            <div class="flex items-center gap-1 order-1 sm:order-2">

                {{-- Anterior --}}
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-medium text-gray-300 bg-gray-100 border border-gray-200 cursor-default select-none">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @else
                    <button type="button"
                            wire:click="previousPage('{{ $paginator->getPageName() }}')"
                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                            wire:loading.attr="disabled"
                            dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after"
                            aria-label="{{ __('pagination.previous') }}"
                            class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-medium text-secondarycolor bg-white border border-gray-300 hover:bg-gray-100 focus:outline-none transition ease-in-out duration-150">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                @endif

                {{-- Numeros --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-gray-400 bg-white border border-gray-200 cursor-default select-none">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page"
                                          class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold text-secondarycolor bg-gray-200 border border-gray-200 cursor-default select-none shadow-sm">
                                        {{ $page }}
                                    </span>
                                @else
                                    <button type="button"
                                            wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                            aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                                            class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-secondarycolor bg-white border border-gray-300 hover:bg-gray-100 focus:outline-none transition ease-in-out duration-150">
                                        {{ $page }}
                                    </button>
                                @endif
                            </span>
                        @endforeach
                    @endif
                @endforeach

                {{-- Siguiente --}}
                @if ($paginator->hasMorePages())
                    <button type="button"
                            wire:click="nextPage('{{ $paginator->getPageName() }}')"
                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                            wire:loading.attr="disabled"
                            dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after"
                            aria-label="{{ __('pagination.next') }}"
                            class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-medium text-secondarycolor bg-white border border-gray-300 hover:bg-gray-100 focus:outline-none transition ease-in-out duration-150">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                @else
                    <span class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-medium text-gray-300 bg-gray-100 border border-gray-200 cursor-default select-none">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @endif

            </div>
        </nav>
    @endif
</div>