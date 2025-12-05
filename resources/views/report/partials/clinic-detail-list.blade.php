@php
    // Mapping warna badge header berdasarkan kode klinik
    $badgeColors = [
        'MO' => 'bg-blue-600',
        'RO' => 'bg-rose-600',
        'BO' => 'bg-pink-600',
        'GO' => 'bg-purple-600',
        'PO' => 'bg-cyan-600',
        'AO' => 'bg-orange-600',
    ];
    $badgeColor = $badgeColors[$code] ?? 'bg-gray-600';
@endphp

{{-- Wrapper Utama (Hindari terpotong saat print) --}}
<div class="mb-10">
    
    {{-- Header Klinik --}}
    <div class="flex items-center gap-3 mb-6 border-b border-gray-200 pb-3">
        <span class="px-2.5 py-1 rounded-md text-sm font-bold text-white {{ $badgeColor }}">
            {{ $code }}
        </span>
        <h3 class="font-bold text-xl text-gray-900">Detail Pasien & Notes</h3>
        <span class="text-sm text-gray-500 font-normal ml-auto">Daftar pasien {{ $title }} periode ini.</span>
    </div>

    {{-- List Card Pasien --}}
    @if(count($data['details_list']) > 0)
        <div class="space-y-4">
            @foreach($data['details_list'] as $index => $patient)
                {{-- Hanya tampilkan jika ada Note atau status spesifik --}}
                @if(!empty($patient->note) && $patient->note !== '-')
                
                <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-[0_2px_8px_rgba(0,0,0,0.04)] break-inside-avoid">
                    
                    {{-- Baris Atas: Nama & Source --}}
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 mb-2">
                        <div>
                            <h4 class="text-base font-bold text-gray-900">
                                {{ $index + 1 }}. {{ $patient->name }} 
                                <span class="text-gray-400 font-normal text-sm">({{ $patient->age }} Th)</span>
                            </h4>
                            <div class="text-xs text-gray-500 mt-1">
                                RM: {{ $patient->no_rm }} <span class="mx-1 text-gray-300">|</span> Dx: {{ $patient->diagnosis ?? '-' }}
                            </div>
                        </div>
                        
                        {{-- Badge Source (Pill Shape) --}}
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-medium border border-gray-200 text-gray-600 bg-gray-50">
                                Source: {{ $patient->source ?? 'Unknown' }}
                            </span>
                        </div>
                    </div>

                    {{-- Box Kuning (Note) --}}
                    <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-xl">
                        <div class="flex gap-2">
                            {{-- Teks Note --}}
                            <div class="text-xs text-gray-800 leading-relaxed">
                                <span class="font-bold text-yellow-700 block mb-1">
                                    Note (Kunjungan ke-{{ $patient->visit_rank }}):
                                </span>
                                <span class="italic">"{{ $patient->note }}"</span>
                            </div>
                        </div>
                    </div>

                </div>
                @endif
            @endforeach
        </div>
        
        {{-- Empty State --}}
        @if(collect($data['details_list'])->where('note', '!=', '-')->isEmpty())
            <div class="text-center py-8 bg-gray-50 rounded-xl border border-dashed border-gray-200 text-gray-400 text-sm italic">
                Tidak ada catatan aktivitas spesifik untuk ditampilkan.
            </div>
        @endif

    @else
        <div class="text-center py-8 bg-gray-50 rounded-xl border border-dashed border-gray-200 text-gray-400 text-sm italic">
            Tidak ada data pasien pada periode ini.
        </div>
    @endif
</div>