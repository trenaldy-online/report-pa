<x-app-layout>
    {{-- 1. GLOBAL STYLE (Hapus pemaksaan warna hitam) --}}
    <style>
        /* Scrollbar halus */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>

    <div class="min-h-screen bg-gray-50 text-gray-900 font-sans pb-12">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
            
            {{-- 2. HEADER & FILTER BAR --}}
            <div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-10">
                <div>
                    <h2 class="text-xs font-bold text-blue-600 tracking-widest uppercase mb-1">
                        Executive Overview
                    </h2>
                    <h1 class="text-3xl md:text-4xl font-light text-gray-800">
                        Dashboard <span class="font-bold text-black">Utama</span>
                    </h1>
                </div>

                {{-- Filter Tanggal (White Style) --}}
                <div class="bg-white p-1.5 rounded-2xl border border-gray-200 flex flex-col sm:flex-row items-center gap-2 shadow-sm">
                    <form action="{{ route('dashboard.filter') }}" method="POST" class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                        @csrf {{-- WAJIB ADA --}}
                        <div class="relative group w-full sm:w-auto">
                            <input type="date" name="start_date" value="{{ $startDate }}" 
                                class="bg-gray-50 border-0 text-gray-600 text-xs rounded-xl focus:ring-2 focus:ring-blue-500 block w-full pl-3 p-2.5 transition-all font-medium">
                        </div>
                        <span class="text-gray-400 hidden sm:inline">-</span>
                        <div class="relative group w-full sm:w-auto">
                            <input type="date" name="end_date" value="{{ $endDate }}" 
                                class="bg-gray-50 border-0 text-gray-600 text-xs rounded-xl focus:ring-2 focus:ring-blue-500 block w-full pl-3 p-2.5 transition-all font-medium">
                        </div>
                        <button type="submit" class="w-full sm:w-auto bg-black hover:bg-gray-800 text-white px-6 py-2.5 rounded-xl text-xs font-bold transition-all shadow-lg shadow-gray-200">
                            Filter Data
                        </button>
                    </form>
                </div>

            </div>

            {{-- 3. KEY METRICS (KARTU ATAS) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                
                <div class="bg-white rounded-[2rem] border border-gray-100 p-6 relative overflow-hidden shadow-sm hover:shadow-md transition duration-300">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-cyan-50 rounded-full blur-2xl -mr-6 -mt-6"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="p-2 bg-cyan-50 rounded-lg text-cyan-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Database</span>
                        </div>
                        <div class="text-4xl font-bold text-gray-800 tracking-tight">{{ number_format($totalPasienDB) }}</div>
                        <div class="mt-2 text-[10px] text-gray-400 font-medium">Sejak awal sistem</div>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] border border-gray-100 p-6 relative overflow-hidden shadow-sm hover:shadow-md transition duration-300">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-green-50 rounded-full blur-2xl -mr-6 -mt-6"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="p-2 bg-green-50 rounded-lg text-green-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                            </div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">New Patient</span>
                        </div>
                        <div class="text-4xl font-bold text-green-600 tracking-tight">{{ number_format($pasienBaru) }}</div>
                        <div class="mt-2 text-[10px] text-gray-400 font-medium">Periode terpilih</div>
                    </div>
                </div>

<div class="bg-white rounded-[2rem] border border-gray-100 p-1 flex flex-col relative overflow-hidden shadow-sm hover:shadow-md transition duration-300">
                    <div class="p-5 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status Kemo</span>
                        </div>
                    </div>
                    
                    @php $totalKemo = $convertedChemo + $notConvertedChemo; @endphp

                    <div class="flex flex-1 mt-2 text-center divide-x divide-gray-100">
                        <div class="flex-1 p-3 bg-purple-50 rounded-bl-[1.7rem] flex flex-col justify-center">
                            <div class="text-xl font-bold text-purple-600">{{ number_format($convertedChemo) }}</div>
                            <div class="text-[9px] font-bold text-purple-400 uppercase mt-1">Converted</div>
                            <div class="text-[9px] text-purple-400/70 mt-0.5">
                                ({{ $totalKemo > 0 ? number_format(($convertedChemo / $totalKemo) * 100, 1) : 0 }}%)
                            </div>
                        </div>
                        <div class="flex-1 p-3 bg-amber-50 rounded-br-[1.7rem] flex flex-col justify-center">
                            <div class="text-xl font-bold text-amber-600">{{ number_format($notConvertedChemo) }}</div>
                            <div class="text-[9px] font-bold text-amber-500 uppercase mt-1">Not Converted</div>
                            <div class="text-[9px] text-amber-500/70 mt-0.5">
                                ({{ $totalKemo > 0 ? number_format(($notConvertedChemo / $totalKemo) * 100, 1) : 0 }}%)
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] border border-gray-100 p-1 flex flex-col relative overflow-hidden shadow-sm hover:shadow-md transition duration-300">
                    <div class="p-5 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status RT</span>
                        </div>
                    </div>

                    @php $totalRt = $convertedRt + $notConvertedRt; @endphp
                    
                    <div class="flex flex-1 mt-2 text-center divide-x divide-gray-100">
                        <div class="flex-1 p-3 bg-rose-50 rounded-bl-[1.7rem] flex flex-col justify-center">
                            <div class="text-xl font-bold text-rose-600">{{ number_format($convertedRt) }}</div>
                            <div class="text-[9px] font-bold text-rose-400 uppercase mt-1">Converted</div>
                            <div class="text-[9px] text-rose-400/70 mt-0.5">
                                ({{ $totalRt > 0 ? number_format(($convertedRt / $totalRt) * 100, 1) : 0 }}%)
                            </div>
                        </div>
                        <div class="flex-1 p-3 bg-amber-50 rounded-br-[1.7rem] flex flex-col justify-center">
                            <div class="text-xl font-bold text-amber-600">{{ number_format($notConvertedRt) }}</div>
                            <div class="text-[9px] font-bold text-amber-500 uppercase mt-1">Not Converted</div>
                            <div class="text-[9px] text-amber-500/70 mt-0.5">
                                ({{ $totalRt > 0 ? number_format(($notConvertedRt / $totalRt) * 100, 1) : 0 }}%)
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- 4. BOTTOM SECTION (2 Columns) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <div class="bg-white rounded-[2rem] border border-gray-100 flex flex-col h-full overflow-hidden shadow-sm">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-gray-800 text-lg">📊 Kunjungan Klinik</h3>
                        <span class="text-[10px] text-gray-500 bg-white px-2 py-1 rounded border border-gray-200">Click to expand</span>
                    </div>
                    
                    <div class="p-0 overflow-x-auto">
                        <table class="min-w-full text-left">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold">
                                <tr>
                                    <th class="px-6 py-4">Klinik</th>
                                    <th class="px-6 py-4 text-center">Total</th>
                                    <th class="px-6 py-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($statsKlinik as $stat)
                                <tbody x-data="{ open: false }">
                                    <tr class="hover:bg-blue-50/50 cursor-pointer transition-colors group" @click="open = !open">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-xs group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors">
                                                    {{ substr($stat->klinik ?? 'OT', 0, 2) }}
                                                </div>
                                                <span class="text-sm font-bold text-gray-700 group-hover:text-black">{{ $stat->klinik ?? 'Lainnya' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="text-sm font-bold text-gray-900">{{ $stat->total }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 transform transition-transform duration-200" 
                                                 :class="{'rotate-180': open}" 
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </td>
                                    </tr>
                                    
                                    <tr x-show="open" x-collapse style="display: none;">
                                        <td colspan="3" class="px-6 pb-6 pt-2 bg-gray-50/50 inner-shadow">
                                            <div class="space-y-2 pl-11">
                                                <div class="text-[10px] uppercase font-bold text-gray-400 mb-2">Detail Sumber Pasien</div>
                                                @foreach($stat->details as $sumber => $jumlah)
                                                    <div class="flex justify-between items-center text-sm group/item">
                                                        <span class="text-gray-500 group-hover/item:text-gray-800 transition-colors">{{ $sumber }}</span>
                                                        <span class="text-xs font-bold text-gray-600 bg-white px-2 py-0.5 rounded border border-gray-200 shadow-sm">{{ $jumlah }}</span>
                                                    </div>
                                                @endforeach
                                                @if($stat->details->isEmpty())
                                                    <span class="text-xs text-gray-400 italic">Tidak ada data sumber detail.</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                @endforeach
                            </tbody>
                        </table>
                        
                        @if($statsKlinik->isEmpty())
                            <div class="p-8 text-center text-gray-400 text-sm italic">Belum ada data kunjungan.</div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] border border-gray-100 flex flex-col h-full overflow-hidden shadow-sm">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-gray-800 text-lg">📢 Sumber (All)</h3>
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full border border-blue-100">
                            Total: {{ $totalSource }}
                        </span>
                    </div>

                    <div class="p-6 max-h-[500px] overflow-y-auto">
                        @if($marketingStats->isEmpty())
                            <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                                <p class="text-sm italic">Belum ada data sumber info.</p>
                            </div>
                        @else
                            {{-- JUARA 1 (Highlight Box Biru) --}}
                            @php $top = $marketingStats->first(); @endphp
                            <div class="mb-6 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-5 text-white relative overflow-hidden shadow-lg shadow-blue-200">
                                <div class="absolute right-0 top-0 opacity-20 transform translate-x-4 -translate-y-4">
                                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                </div>
                                <div class="relative z-10">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="bg-white/20 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Top Source</span>
                                    </div>
                                    <div class="text-xl font-bold truncate pr-8">{{ $top->sumber_informasi }}</div>
                                    <div class="flex items-end gap-2 mt-3">
                                        <span class="text-4xl font-extrabold">{{ $top->total }}</span>
                                        <span class="text-blue-100 text-xs mb-1.5 font-medium">Pasien ({{ number_format($totalSource > 0 ? ($top->total / $totalSource) * 100 : 0, 1) }}%)</span>
                                    </div>
                                </div>
                            </div>

                            {{-- SISANYA (List Clean) --}}
                            <div class="space-y-4">
                                @foreach($marketingStats->slice(1) as $stat)
                                    @php
                                        $persen = $totalSource > 0 ? ($stat->total / $totalSource) * 100 : 0;
                                    @endphp
                                    <div class="group">
                                        <div class="flex justify-between text-sm mb-1.5">
                                            <span class="font-medium text-gray-500 group-hover:text-gray-900 transition-colors">{{ $stat->sumber_informasi }}</span>
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-gray-800">{{ $stat->total }}</span>
                                                <span class="text-[10px] text-gray-400">({{ number_format($persen, 1) }}%)</span>
                                            </div>
                                        </div>
                                        <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-gray-300 group-hover:bg-blue-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $persen }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 5. SECTION DPJP PER KLINIK (GRID LAYOUT) --}}
            <div class="mt-8">
                <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
                    <span>👨‍⚕️ Performa DPJP per Klinik</span>
                    <span class="text-xs font-normal text-gray-500 bg-gray-200 px-2 py-1 rounded">Distribusi Pasien</span>
                </h3>

                @if($dpjpStats->isEmpty())
                    <div class="bg-white p-8 rounded-2xl border border-gray-100 text-center text-gray-400 italic shadow-sm">
                        Belum ada data DPJP pada periode ini.
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($dpjpStats as $stat)
                        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition duration-300 flex flex-col">
                            
                            {{-- Header Klinik --}}
                            <div class="flex justify-between items-center mb-4 border-b border-gray-50 pb-3">
                                <div class="flex items-center gap-3">
                                    {{-- Icon inisial Klinik --}}
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                                        {{ substr($stat->klinik, 0, 2) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800">{{ $stat->klinik }}</h4>
                                        <div class="text-[10px] text-gray-400">Total Pasien: {{ $stat->total_visit }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- List Dokter --}}
                            <div class="space-y-3 flex-1">
                                @foreach($stat->doctors as $doc)
                                <div class="group">
                                    <div class="flex justify-between items-center text-sm mb-1">
                                        <div class="flex items-center gap-2">
                                            {{-- Avatar Dokter Kecil --}}
                                            <div class="w-6 h-6 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-[9px] font-bold">
                                                DR
                                            </div>
                                            <span class="font-medium text-gray-600 group-hover:text-blue-600 transition">{{ $doc->name }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-bold text-gray-800">{{ $doc->total }}</span>
                                            <span class="text-[10px] text-gray-400 ml-1">({{ number_format($doc->percent, 1) }}%)</span>
                                        </div>
                                    </div>
                                    {{-- Progress Bar --}}
                                    <div class="w-full bg-gray-50 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-blue-200 group-hover:bg-blue-500 h-1.5 rounded-full transition-all duration-500" 
                                             style="width: {{ $doc->percent }}%"></div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
            
            </div>

        </div>
    </div>
</x-app-layout>