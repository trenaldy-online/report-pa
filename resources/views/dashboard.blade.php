<x-app-layout>
    {{-- 1. GLOBAL STYLE & ANIMATION --}}
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        @keyframes loadWidth { from { width: 0; } }
        .progress-animate { animation: loadWidth 1.5s ease-out forwards; }
    </style>

    {{-- WRAPPER UTAMA DENGAN STATE POPUP (x-data) --}}
    <div class="min-h-screen bg-gray-50 text-gray-900 font-sans pb-12" x-data="{ showHelp: false }">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
            
            {{-- 2. HEADER, TOMBOL PANDUAN & FILTER --}}
            <div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-10">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h2 class="text-xs font-bold text-blue-600 tracking-widest uppercase">
                            Executive Overview
                        </h2>
                        {{-- TOMBOL PANDUAN GLOBAL --}}
                        <button @click="showHelp = true" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded-full text-[10px] font-bold transition flex items-center gap-1 cursor-pointer">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Cara Membaca Data
                        </button>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-light text-gray-800">
                        Dashboard <span class="font-bold text-black">Utama</span>
                    </h1>
                </div>

                {{-- Filter Tanggal --}}
                <div class="bg-white p-1.5 rounded-2xl border border-gray-200 flex flex-col sm:flex-row items-center gap-2 shadow-sm">
                    <form action="{{ route('dashboard.filter') }}" method="POST" class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                        @csrf 
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
                
                {{-- Total Database --}}
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

                {{-- New Patient --}}
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

                {{-- Status Kemo --}}
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
                            <div class="text-[9px] text-purple-400/70 mt-0.5">({{ $totalKemo > 0 ? number_format(($convertedChemo / $totalKemo) * 100, 1) : 0 }}%)</div>
                        </div>
                        <div class="flex-1 p-3 bg-amber-50 rounded-br-[1.7rem] flex flex-col justify-center">
                            <div class="text-xl font-bold text-amber-600">{{ number_format($notConvertedChemo) }}</div>
                            <div class="text-[9px] font-bold text-amber-500 uppercase mt-1">Not Converted</div>
                            <div class="text-[9px] text-amber-500/70 mt-0.5">({{ $totalKemo > 0 ? number_format(($notConvertedChemo / $totalKemo) * 100, 1) : 0 }}%)</div>
                        </div>
                    </div>
                </div>

                {{-- Status RT --}}
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
                            <div class="text-[9px] text-rose-400/70 mt-0.5">({{ $totalRt > 0 ? number_format(($convertedRt / $totalRt) * 100, 1) : 0 }}%)</div>
                        </div>
                        <div class="flex-1 p-3 bg-amber-50 rounded-br-[1.7rem] flex flex-col justify-center">
                            <div class="text-xl font-bold text-amber-600">{{ number_format($notConvertedRt) }}</div>
                            <div class="text-[9px] font-bold text-amber-500 uppercase mt-1">Not Converted</div>
                            <div class="text-[9px] text-amber-500/70 mt-0.5">({{ $totalRt > 0 ? number_format(($notConvertedRt / $totalRt) * 100, 1) : 0 }}%)</div>
                        </div>
                    </div>
                </div>
                
            </div>
            {{-- 3.5. GRAFIK TREN KUNJUNGAN (STACKED BAR) --}}
            <div class="mb-8 bg-white rounded-[2rem] border border-gray-100 p-6 shadow-sm relative overflow-hidden">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-800">📊 Statistik Kunjungan & Konversi</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Perbandingan pasien Converted vs Non-Converted. 
                        <span class="font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded ml-1">
                            Mode: {{ $chartPeriodLabel }}
                        </span>
                    </p>
                </div>
                
                {{-- Container Grafik --}}
                <div id="visitChart" class="w-full h-[350px]"></div>
            </div>

            {{-- 4. SECTION: KUNJUNGAN & SUMBER (2 Columns) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                
                {{-- Kunjungan Klinik --}}
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
                                        <td class="px-6 py-4 text-center"><span class="text-sm font-bold text-gray-900">{{ $stat->total }}</span></td>
                                        <td class="px-6 py-4 text-right">
                                            <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
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
                                                @if($stat->details->isEmpty()) <span class="text-xs text-gray-400 italic">Tidak ada data sumber detail.</span> @endif
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                @endforeach
                            </tbody>
                        </table>
                        @if($statsKlinik->isEmpty()) <div class="p-8 text-center text-gray-400 text-sm italic">Belum ada data kunjungan.</div> @endif
                    </div>
                </div>

                {{-- Sumber (All) --}}
                <div class="bg-white rounded-[2rem] border border-gray-100 flex flex-col h-full overflow-hidden shadow-sm">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-gray-800 text-lg">📢 Sumber (All)</h3>
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full border border-blue-100">Total: {{ $totalSource }}</span>
                    </div>
                    <div class="p-6 max-h-[500px] overflow-y-auto">
                        @if($marketingStats->isEmpty())
                            <div class="flex flex-col items-center justify-center h-48 text-gray-400"><p class="text-sm italic">Belum ada data sumber info.</p></div>
                        @else
                            @php $top = $marketingStats->first(); @endphp
                            <div class="mb-6 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-5 text-white relative overflow-hidden shadow-lg shadow-blue-200">
                                <div class="absolute right-0 top-0 opacity-20 transform translate-x-4 -translate-y-4">
                                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                </div>
                                <div class="relative z-10">
                                    <div class="flex items-center gap-2 mb-1"><span class="bg-white/20 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Top Source</span></div>
                                    <div class="text-xl font-bold truncate pr-8">{{ $top->sumber_informasi }}</div>
                                    <div class="flex items-end gap-2 mt-3">
                                        <span class="text-4xl font-extrabold">{{ $top->total }}</span>
                                        <span class="text-blue-100 text-xs mb-1.5 font-medium">Pasien ({{ number_format($totalSource > 0 ? ($top->total / $totalSource) * 100 : 0, 1) }}%)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                @foreach($marketingStats->slice(1) as $stat)
                                    @php $persen = $totalSource > 0 ? ($stat->total / $totalSource) * 100 : 0; @endphp
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
            </div>

            {{-- 7. REVISED WIDGET: EFEKTIVITAS MARKETING (ACCORDION STYLE) --}}
            <div class="mb-8 bg-white rounded-[2rem] border border-gray-100 p-6 shadow-sm">
                
                {{-- HEADER & LEGEND --}}
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <span>🎯 Efektivitas Source (Conversion Rate)</span>
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Klik pada baris nama source untuk melihat <strong>daftar lengkap pasien</strong>.
                        </p>
                    </div>
                    
                    {{-- LEGENDA STATUS --}}
                    <div class="flex flex-wrap gap-3 bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                            <span class="text-[10px] font-bold text-gray-600">Converted</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                            <span class="text-[10px] font-bold text-gray-600">Not Converted</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-gray-100 border border-gray-300"></span>
                            <span class="text-[10px] font-bold text-gray-600">Pending</span>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-xs text-gray-400 border-b border-gray-100">
                                <th class="py-3 font-semibold uppercase tracking-wider pl-4 w-1/3">Nama Source</th>
                                <th class="py-3 font-semibold text-center w-24">Total Lead</th>
                                <th class="py-3 font-semibold w-1/3 pl-8">Detail Converted</th>
                                <th class="py-3 font-semibold w-1/4 pr-4 text-right">Rate</th>
                                <th class="w-8"></th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-50">
                            @forelse($conversionAnalysis as $stat)
                            <tbody x-data="{ open: false }">
                                {{-- BARIS UTAMA (CLICKABLE) --}}
                                <tr class="group hover:bg-gray-50 transition cursor-pointer" @click="open = !open">
                                    <td class="py-4 pl-4 font-medium text-gray-700 group-hover:text-black transition-colors pt-5 align-top">{{ $stat->name }}</td>
                                    <td class="py-4 text-center text-gray-600 font-bold pt-5 align-top">{{ $stat->total_lead }}</td>
                                    <td class="py-3 pl-8 align-top">
                                        <div class="flex gap-4">
                                            <div class="bg-purple-50 rounded-lg p-2 flex-1 border border-purple-100">
                                                <div class="text-[9px] font-bold text-purple-600 uppercase mb-1">MO</div>
                                                <span class="text-green-600 font-bold text-xs">{{ $stat->mo_conv }}</span>
                                            </div>
                                            <div class="bg-rose-50 rounded-lg p-2 flex-1 border border-rose-100">
                                                <div class="text-[9px] font-bold text-rose-600 uppercase mb-1">RO</div>
                                                <span class="text-green-600 font-bold text-xs">{{ $stat->ro_conv }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 pr-4 pt-5 align-top">
                                        <div class="flex flex-col items-end gap-1">
                                            <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden flex border border-gray-200" title="Putih = Pending / Belum Ada Status">
                                                <div class="h-full bg-emerald-500" style="width: {{ $stat->rate_converted }}%" title="Converted: {{ $stat->rate_converted }}%"></div>
                                                <div class="h-full bg-amber-400" style="width: {{ $stat->rate_not_converted }}%" title="Not Converted: {{ $stat->rate_not_converted }}%"></div>
                                            </div>
                                            <div class="flex justify-end gap-2 text-[10px] font-medium mt-1">
                                                <span class="text-emerald-600">{{ $stat->rate_converted }}% Converted</span>
                                                <span class="text-gray-300">|</span>
                                                <span class="text-amber-600">{{ $stat->rate_not_converted }}% Not</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 pr-2 text-right align-top pt-5">
                                        <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </td>
                                </tr>

                                {{-- BARIS DETAIL (DROPDOWN) --}}
                                <tr x-show="open" x-collapse style="display: none;">
                                    <td colspan="5" class="p-0">
                                        <div class="bg-gray-50/80 inner-shadow p-4 pl-8 border-t border-gray-100">
                                            <div class="flex justify-between items-center mb-3">
                                                <div class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Daftar Lengkap Pasien ({{ count($stat->patients_list) }})</div>
                                            </div>
                                            <div class="max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                                                <table class="w-full text-xs">
                                                    <thead class="sticky top-0 bg-gray-100/95 backdrop-blur-sm z-10 shadow-sm">
                                                        <tr class="text-gray-500 text-left border-b border-gray-200">
                                                            <th class="py-2 pl-2 font-bold w-28">Tgl Visit</th>
                                                            <th class="py-2 font-bold">Nama Pasien / RM</th>
                                                            <th class="py-2 font-bold text-center w-20">Visit Fisik</th>
                                                            <th class="py-2 font-bold w-24">Status MO</th>
                                                            <th class="py-2 font-bold w-24">Status RO</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-200/50">
                                                        @foreach($stat->patients_list as $p)
                                                        <tr class="hover:bg-white transition">
                                                            <td class="py-2 pl-2 text-gray-500 font-mono">{{ \Carbon\Carbon::parse($p['tanggal'])->translatedFormat('d F Y') }}</td>
                                                            <td class="py-2">
                                                                <div class="font-bold text-gray-800 text-sm">{{ $p['nama'] }}</div>
                                                                <div class="text-[10px] text-blue-500 font-mono bg-blue-50 inline-block px-1 rounded mt-0.5">{{ $p['no_rm'] }}</div>
                                                            </td>
                                                            <td class="py-2 text-center">
                                                                <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold text-white {{ $p['visit'] == 'MO' ? 'bg-purple-400' : ($p['visit'] == 'RO' ? 'bg-rose-400' : 'bg-gray-400') }}">{{ $p['visit'] }}</span>
                                                            </td>
                                                            <td class="py-2">
                                                                @if($p['status_mo'] == 'Converted') <span class="text-emerald-600 font-bold bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100">Converted</span>
                                                                @elseif($p['status_mo'] == 'Not Converted') <span class="text-amber-600 font-medium">Not Conv.</span>
                                                                @else <span class="text-gray-300">-</span> @endif
                                                            </td>
                                                            <td class="py-2">
                                                                @if($p['status_ro'] == 'Converted') <span class="text-emerald-600 font-bold bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100">Converted</span>
                                                                @elseif($p['status_ro'] == 'Not Converted') <span class="text-amber-600 font-medium">Not Conv.</span>
                                                                @else <span class="text-gray-300">-</span> @endif
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            @empty
                            <tr><td colspan="5" class="py-8 text-center text-gray-400 italic">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 8. DPJP PERFORMANCE (DENGAN CONVERTED DATA) --}}
            <div>
                <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
                    <span>👨‍⚕️ Performa DPJP per Klinik</span>
                    <span class="text-xs font-normal text-gray-500 bg-gray-200 px-2 py-1 rounded">Distribusi & Konversi</span>
                </h3>

                @if($dpjpStats->isEmpty())
                    <div class="bg-white p-8 rounded-2xl border border-gray-100 text-center text-gray-400 italic shadow-sm">
                        Belum ada data DPJP pada periode ini.
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($dpjpStats as $stat)
                        <div class="bg-white rounded-[2rem] border border-gray-100 p-5 shadow-sm hover:shadow-md transition duration-300 flex flex-col h-full">
                            
                            {{-- Header Klinik --}}
                            <div class="flex justify-between items-center mb-4 border-b border-gray-50 pb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                                        {{ substr($stat->klinik, 0, 2) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800">{{ $stat->klinik }}</h4>
                                        <div class="text-[10px] text-gray-400">Total Kunjungan: {{ $stat->total_visit }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- List Dokter --}}
                            <div class="space-y-4 flex-1">
                                @foreach($stat->doctors as $doc)
                                <div class="group">
                                    {{-- Baris 1: Nama & Total Visit --}}
                                    <div class="flex justify-between items-start text-sm mb-1">
                                        <div class="flex items-center gap-2 max-w-[70%]">
                                            <div class="w-6 h-6 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-[9px] font-bold shrink-0">DR</div>
                                            <span class="font-medium text-gray-700 group-hover:text-blue-600 transition truncate" title="{{ $doc->name }}">{{ $doc->name }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-bold text-gray-900 block">{{ $doc->total }}</span>
                                            <span class="text-[9px] text-gray-400">Pasien</span>
                                        </div>
                                    </div>

                                    {{-- Baris 2: Statistik Konversi (Hanya muncul jika Klinik MO/RO) --}}
                                    @if(in_array($stat->klinik, ['MO', 'RO']))
                                        <div class="flex justify-between items-center text-[10px] mb-1">
                                            <span class="text-gray-400">Success Rate:</span>
                                            <div class="flex gap-2">
                                                <span class="font-bold text-emerald-600">{{ $doc->converted }} Conv.</span>
                                                <span class="text-gray-300">|</span>
                                                <span class="font-bold text-gray-600">{{ $doc->conv_rate }}%</span>
                                            </div>
                                        </div>
                                        
                                        {{-- Progress Bar (Rate Konversi) --}}
                                        <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden flex">
                                            <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $doc->conv_rate }}%"></div>
                                        </div>
                                    @else
                                        {{-- Untuk klinik non-kemo/radio, tampilkan share visit saja --}}
                                        <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden mt-2">
                                            <div class="bg-blue-200 group-hover:bg-blue-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $doc->percent }}%"></div>
                                        </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>

                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        {{-- MODAL PANDUAN GLOBAL --}}
        <div x-show="showHelp" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showHelp = false"></div>
            
            {{-- Modal Content --}}
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col transform transition-all">
                
                {{-- Header --}}
                <div class="bg-gray-50 px-8 py-5 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <span class="bg-blue-600 text-white p-1.5 rounded-lg shadow-blue-200 shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </span>
                            Panduan Membaca Data
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">Penjelasan logika perhitungan dan definisi status.</p>
                    </div>
                    <button @click="showHelp = false" class="text-gray-400 hover:text-gray-600 transition p-2 hover:bg-gray-100 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                {{-- Body (Scrollable) --}}
                <div class="p-8 overflow-y-auto space-y-8 bg-white flex-1">
                    
                    {{-- 1. KONSEP DASAR (PENTING) --}}
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg shadow-sm">
                        <h4 class="text-sm font-bold text-yellow-800 uppercase mb-2">⚠️ Konsep Utama: Logika Ketat (Strict Logic)</h4>
                        <p class="text-sm text-yellow-800/80 leading-relaxed">
                            Dashboard ini menggunakan filter klinik yang ketat untuk menentukan keberhasilan (Converted):
                        </p>
                        <ul class="list-disc pl-5 mt-2 text-sm text-yellow-800/80 space-y-1">
                            <li>Pasien hanya dianggap <strong>Converted (Hijau)</strong> jika dia datang ke <strong>Klinik MO</strong> (untuk Kemo) atau <strong>Klinik RO</strong> (untuk Radio).</li>
                            <li>Jika Pasien Kemo datang ke Poli Bedah/Ginek, hari itu statusnya dianggap <strong>Non-Converted (Kuning)</strong>, meskipun di database dia pasien sukses.</li>
                        </ul>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        {{-- 2. ARTI WARNA --}}
                        <div>
                            <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-3 border-l-4 border-blue-500 pl-3">1. Definisi Warna & Status</h4>
                            <div class="space-y-3">
                                <div class="flex gap-3 bg-emerald-50 p-3 rounded-lg border border-emerald-100">
                                    <div class="w-4 h-4 rounded-full bg-emerald-500 mt-0.5 shrink-0"></div>
                                    <div>
                                        <strong class="text-emerald-700 text-xs uppercase block">Converted (Sukses)</strong>
                                        <p class="text-[11px] text-gray-600">Pasien fisik datang ke klinik MO/RO <strong>DAN</strong> sudah dijadwalkan tindakan (ada tanggal di database).</p>
                                    </div>
                                </div>
                                <div class="flex gap-3 bg-amber-50 p-3 rounded-lg border border-amber-100">
                                    <div class="w-4 h-4 rounded-full bg-amber-400 mt-0.5 shrink-0"></div>
                                    <div>
                                        <strong class="text-amber-700 text-xs uppercase block">Non Converted / Pending</strong>
                                        <p class="text-[11px] text-gray-600">Pasien fisik datang, tapi statusnya menolak, batal, atau berkunjung ke poli lain (Bedah/Ginek) sehingga status Kemo/RT tidak dihitung.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 3. PERBEDAAN GRAFIK VS WIDGET --}}
                        <div>
                            <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-3 border-l-4 border-purple-500 pl-3">2. Grafik vs Widget Atas</h4>
                            <div class="text-sm text-gray-600 space-y-3">
                                <p class="bg-gray-50 p-2 rounded border border-gray-200">
                                    <strong>❓ Mengapa angka di Grafik lebih banyak dari Widget Atas?</strong>
                                </p>
                                <ul class="list-disc pl-5 space-y-2 text-xs">
                                    <li>
                                        <strong>Widget Atas (Orang):</strong> Menghitung <span class="text-purple-600 font-bold">Pasien Unik</span>. Jika Bapak Budi datang 5x sebulan, tetap dihitung 1.
                                    </li>
                                    <li>
                                        <strong>Grafik Batang (Volume):</strong> Menghitung <span class="text-purple-600 font-bold">Total Kunjungan/Tiket</span>. Jika Bapak Budi datang 5x, akan muncul 5 batang hijau di grafik.
                                    </li>
                                </ul>
                                <p class="text-[10px] italic text-gray-400 mt-2">*Grafik digunakan untuk melihat beban kerja harian operasional.</p>
                            </div>
                        </div>

                    </div>

                    <hr class="border-gray-100">

                    {{-- 4. SUMBER DATA --}}
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-3 border-l-4 border-cyan-500 pl-3">3. Penjelasan Sumber Data</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs text-gray-600">
                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                <strong class="block text-gray-800 mb-1">Total Lead</strong>
                                Diambil dari tabel <em>Clinic Visit</em>. Menghitung jumlah orang unik yang datang ke RS pada periode ini.
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                <strong class="block text-gray-800 mb-1">Status Kemo (MO)</strong>
                                Diambil dari tabel <em>Patient DB</em> kolom <code>new_chemo</code>. Harus berisi Angka/Tanggal untuk dianggap Converted.
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                <strong class="block text-gray-800 mb-1">Status Radio (RO)</strong>
                                Diambil dari tabel <em>Patient DB</em> kolom <code>new_rt</code>. Harus berisi Angka/Tanggal untuk dianggap Converted.
                            </div>
                        </div>
                    </div>

                    {{-- 5. DETAIL GRAFIK MINGGUAN --}}
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-3 border-l-4 border-indigo-500 pl-3">4. Grafik Harian vs Mingguan</h4>
                        <p class="text-xs text-gray-600 bg-indigo-50 p-3 rounded-lg border border-indigo-100">
                            Sistem otomatis mengubah tampilan grafik:
                            <br>• Jika filter tanggal <strong>≤ 3 Bulan</strong>: Grafik menampilkan detail <strong>Harian</strong>.
                            <br>• Jika filter tanggal <strong>> 3 Bulan</strong>: Grafik menampilkan ringkasan <strong>Mingguan (Weekly)</strong> agar mudah dibaca.
                        </p>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="bg-gray-50 px-8 py-4 border-t border-gray-100 text-right">
                    <button @click="showHelp = false" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-8 rounded-xl text-sm transition shadow-lg shadow-blue-200">
                        Saya Mengerti
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- SCRIPT CHART --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var options = {
                series: [
                    {
                        name: 'Non Converted / Pending',
                        data: @json($chartNonConverted)
                    },
                    {
                        name: 'Converted',
                        data: @json($chartConverted)
                    }
                ],
                chart: {
                    type: 'bar',
                    height: 350,
                    stacked: true, // <--- KUNCI: STACKED BAR
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                colors: ['#fbbf24', '#10b981'], // Kuning (Atas), Hijau (Bawah)
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '50%',
                        borderRadius: 4,
                        dataLabels: {
                            total: {
                                enabled: true, // Tampilkan Total di Atas
                                style: {
                                    fontSize: '11px',
                                    fontWeight: 900,
                                    color: '#374151'
                                },
                                offsetY: -10, // Geser ke atas sedikit
                                formatter: function (val) {
                                    return val > 0 ? val : ''; // Hanya tampil jika > 0
                                }
                            }
                        }
                    },
                },
                dataLabels: {
                    enabled: false // Matikan label di dalam batang agar bersih
                },
                stroke: {
                    width: 1,
                    colors: ['#fff'] // Garis putih pemisah antar stack
                },
                xaxis: {
                    categories: @json($chartDates),
                    labels: {
                        rotate: -45, // Putar label tanggal agar tidak menumpuk
                        rotateAlways: false, // Putar hanya jika sempit
                        hideOverlappingLabels: true, // Sembunyikan jika terlalu rapat
                        style: {
                            fontSize: '10px',
                            colors: '#64748b'
                        }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        style: { colors: '#94a3b8', fontSize: '10px' },
                        formatter: function (val) { return val.toFixed(0); }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    offsetY: -20
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4,
                    padding: { top: 0, right: 0, bottom: 0, left: 10 }
                },
                fill: { opacity: 1 }
            };

            var chart = new ApexCharts(document.querySelector("#visitChart"), options);
            chart.render();
        });
    </script>
</x-app-layout>