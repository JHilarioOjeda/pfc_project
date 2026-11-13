{{-- Encabezado de los formatos de reporte de medición.
     Parámetros: $format (1|2|3), $pageNumber, $totalPages
     Se arma como cuadrícula de 2 filas iguales para que la línea horizontal
     del centro (empresa / título) y la de la derecha (código / revisado)
     queden a la misma altura. --}}
@php
    $headerData = \App\Support\MeasurementReportFormat::HEADERS[$format];
@endphp
<div class="grid border border-black leading-tight" style="grid-template-columns: 2fr 6fr 4fr; grid-template-rows: 1fr 1fr;">
    <div class="flex items-center p-1 border-r border-black" style="grid-row: span 2 / span 2;">
        <img src="/imgs/logos/principallogo.jpg" class="w-full" alt="Logo">
    </div>

    <div class="flex items-center justify-center text-center px-1 border-r border-b border-black">
        <p class="font-bold text-sm tracking-wide">{{ $headerData['company'] }}</p>
    </div>

    <div class="px-1 py-0.5 text-[9px] border-b border-black">
        <p><span class="font-semibold">Código:</span> FO-CA-CA-14</p>
        <p><span class="font-semibold">Revisión:</span> 01</p>
        <p><span class="font-semibold">Fecha:</span> {{ $headerData['date'] }}</p>
        @if(($totalPages ?? 1) > 1)
            <p><span class="font-semibold">Página:</span> {{ $pageNumber }} de {{ $totalPages }}</p>
        @endif
    </div>

    <div class="flex items-center justify-center text-center px-1 border-r border-black">
        <p class="font-semibold text-sm tracking-wide">REPORTE DE MEDICIONES</p>
    </div>

    <div class="grid text-[9px]" style="grid-template-columns: 1fr 1fr;">
        <div class="px-1 py-0.5 border-r border-black">
            <p class="font-semibold">Revisado:</p>
            <p>Carlos Medina</p>
        </div>
        <div class="px-1 py-0.5">
            <p class="font-semibold">Elaborado:</p>
            <p>Erika Martínez</p>
        </div>
    </div>
</div>
