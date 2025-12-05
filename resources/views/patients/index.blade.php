<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Database Pasien') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- HEADER TOOLS: JUDUL & SEARCH BAR --}}
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                
                {{-- Kiri: Info & Tombol Upload --}}
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <a href="{{ route('import.form') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition text-xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Upload / Import Data
                    </a>
                    <span class="text-xs text-gray-500 hidden md:inline">Total: {{ $patients->total() }} Pasien</span>
                </div>

                {{-- Kanan: Form Search --}}
                <div class="w-full md:w-1/3">
                    <form action="{{ route('patients.index') }}" method="GET" class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari Nama atau No RM..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition shadow-sm">
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        @if(request('search'))
                            <a href="{{ route('patients.index') }}" class="absolute right-3 top-2.5 text-gray-400 hover:text-red-500 text-xs font-bold">X</a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- TABEL DATA --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal text-left">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 uppercase text-[10px] font-bold tracking-wider border-b border-gray-200">
                                <th class="px-5 py-3 w-24">No RM</th>
                                <th class="px-5 py-3">Nama Pasien</th>
                                <th class="px-5 py-3">Diagnosis</th>
                                <th class="px-5 py-3 w-20">Usia</th>
                                <th class="px-5 py-3">Sources / Referrals</th> {{-- Kolom Gabungan --}}
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($patients as $patient)
                            <tr class="hover:bg-blue-50/30 transition duration-150 group">
                                
                                <td class="px-5 py-3 font-bold text-gray-900 font-mono">
                                    {{ $patient->no_rm }}
                                </td>
                                
                                <td class="px-5 py-3">
                                    <div class="font-bold text-gray-800">{{ $patient->name_of_patient }}</div>
                                    {{-- Tampilkan Overseas jika ada --}}
                                    @if(!empty($patient->overseas_hospital) && $patient->overseas_hospital != '-')
                                        <div class="text-[10px] text-amber-600 mt-0.5 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ $patient->overseas_hospital }}
                                        </div>
                                    @endif
                                </td>
                                
                                <td class="px-5 py-3 text-gray-500 italic text-xs">
                                    {{ $patient->diagnosis ?? '-' }}
                                </td>
                                
                                <td class="px-5 py-3 text-gray-600">
                                    {{ $patient->age ?? '-' }} Th
                                </td>

                                {{-- KOLOM SOURCES (URUT KRONOLOGIS) --}}
                                <td class="px-5 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        @php
                                            // 1. Definisi Mapping Klinik -> Kolom Database & Warna
                                            $sourceMap = [
                                                'MO' => ['col' => 'source_information_mo', 'color' => 'bg-blue-100 text-blue-700 border-blue-200'],
                                                'RO' => ['col' => 'source_information_ro', 'color' => 'bg-rose-100 text-rose-700 border-rose-200'],
                                                'BO' => ['col' => 'source_information_bo', 'color' => 'bg-pink-100 text-pink-700 border-pink-200'],
                                                'GO' => ['col' => 'source_information_go', 'color' => 'bg-purple-100 text-purple-700 border-purple-200'],
                                                'PO' => ['col' => 'source_information_po', 'color' => 'bg-cyan-100 text-cyan-700 border-cyan-200'],
                                                'AO' => ['col' => 'source_information_ao', 'color' => 'bg-orange-100 text-orange-700 border-orange-200'],
                                            ];

                                            // 2. Ambil urutan klinik yang dikunjungi (Unique, agar tidak double badge jika visit berkali-kali ke klinik sama)
                                            $visitedClinics = $patient->visits->pluck('klinik')->unique()->filter();
                                            
                                            // Array untuk melacak klinik mana yang sudah ditampilkan
                                            $shownClinics = [];
                                        @endphp

                                        {{-- A. TAMPILKAN BERDASARKAN URUTAN KUNJUNGAN (PRIORITAS) --}}
                                        @foreach($visitedClinics as $klinikCode)
                                            @php 
                                                $kCode = strtoupper($klinikCode); // Pastikan uppercase (MO, RO)
                                                $config = $sourceMap[$kCode] ?? null;
                                            @endphp

                                            @if($config)
                                                @php 
                                                    $colName = $config['col'];
                                                    $sourceVal = $patient->$colName;
                                                @endphp

                                                {{-- Cek apakah ada data source untuk klinik ini --}}
                                                @if(!empty($sourceVal) && $sourceVal !== '-' && $sourceVal !== '0')
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border whitespace-nowrap flex items-center gap-1 {{ $config['color'] }}">
                                                        {{-- Indikator Urutan (1, 2, 3) --}}
                                                        <span class="w-3 h-3 rounded-full bg-white/50 flex items-center justify-center text-[8px]">{{ count($shownClinics) + 1 }}</span>
                                                        {{ $kCode }}: {{ $sourceVal }}
                                                    </span>
                                                    @php $shownClinics[] = $kCode; @endphp
                                                @endif
                                            @endif
                                        @endforeach

                                        {{-- B. TAMPILKAN SISA SOURCE (JIKA ADA DI DB TAPI BELUM PERNAH VISIT / DATA VISIT BELUM UPLOAD) --}}
                                        @foreach($sourceMap as $kCode => $config)
                                            @if(!in_array($kCode, $shownClinics))
                                                @php 
                                                    $colName = $config['col'];
                                                    $sourceVal = $patient->$colName;
                                                @endphp
                                                
                                                @if(!empty($sourceVal) && $sourceVal !== '-' && $sourceVal !== '0')
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border whitespace-nowrap opacity-60 {{ $config['color'] }}" title="Belum ada data kunjungan">
                                                        {{ $kCode }}: {{ $sourceVal }}*
                                                    </span>
                                                @endif
                                            @endif
                                        @endforeach

                                        {{-- Jika Kosong --}}
                                        @if(empty($shownClinics) && empty($sourceVal))
                                            <span class="text-gray-300 text-xs">-</span>
                                        @endif
                                    </div>
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        <p class="text-sm italic">Data pasien tidak ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="p-4 border-t border-gray-200 bg-gray-50">
                    {{ $patients->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>