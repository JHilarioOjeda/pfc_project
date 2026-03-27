@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-4 px-1">

        {{-- Info texto --}}
        <p class="text-xs text-gray-500 order-2 sm:order-1">
            Mostrando
            @if ($paginator->firstItem())
                <span class="font-semibold text-secondarycolor">{{ $paginator->firstItem() }}</span>
                –
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
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}"
                   class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-medium text-secondarycolor bg-white border border-gray-300 hover:bg-gray-100 focus:outline-none transition ease-in-out duration-150">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            @endif

            {{-- Números --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-gray-400 bg-white border border-gray-200 cursor-default select-none">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                  class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold text-white bg-primarycolor border border-primarycolor cursor-default select-none shadow-sm">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                               class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-secondarycolor bg-white border border-gray-300 hover:bg-gray-100 focus:outline-none transition ease-in-out duration-150">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Siguiente --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}"
                   class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-medium text-secondarycolor bg-white border border-gray-300 hover:bg-gray-100 focus:outline-none transition ease-in-out duration-150">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-r-md leading-5 dark:bg-gray-800 dark:border-gray-600" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
