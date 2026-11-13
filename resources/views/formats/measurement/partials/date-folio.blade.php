{{-- Tabla FECHA / FOLIO. Parámetros: $date (Carbon|null), $folio --}}
<div class="flex justify-end mt-2">
    <table class="text-[10px] border border-black border-collapse">
        <tr>
            <td class="border border-black px-2 py-1 font-semibold">FECHA</td>
            <td class="border border-black px-2 py-1 text-center min-w-[5rem]">{{ optional($date)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="border border-black px-2 py-1 font-semibold">FOLIO</td>
            <td class="border border-black px-2 py-1 text-center">{{ $folio ?? '' }}</td>
        </tr>
    </table>
</div>
