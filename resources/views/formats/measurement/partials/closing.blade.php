{{-- Nota, caducidad, texto adicional opcional y firma.
     Parámetros: $notes (string|null), $extraText (string|null) --}}
<div class="w-full mt-3 text-[10px] border border-black">
    <div class="px-2 py-1 border-b border-black">
        <p class="font-semibold">NOTA</p>
        <p>{{ $notes ?: \App\Support\MeasurementReportFormat::DEFAULT_NOTE }}</p>
    </div>
    <div class="px-2 py-1 @if(!empty($extraText)) border-b border-black @endif">
        <p>Caducidad: 30 días a partir de la fecha de su tratamiento.</p>
    </div>
    @if(!empty($extraText))
        <div class="px-2 py-1">
            <p class="font-semibold">{{ $extraText }}</p>
        </div>
    @endif
</div>

<div class="w-full mt-8 flex flex-col items-center justify-end flex-1">
    <img src="{{ asset('imgs/signs/firma_transparente_negro.png') }}" alt="Firma" class="h-16 object-contain">
    <div class="w-1/2 border-t border-black mt-1 pt-1 text-center text-[11px]">
        <p class="font-semibold">Ing. Erika Gpe. Martínez Castro</p>
        <p>Nombre y firma del responsable de calidad</p>
    </div>
</div>
