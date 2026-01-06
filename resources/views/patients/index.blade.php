<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Database Pasien') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- HEADER TOOLS --}}
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <a href="{{ route('import.form') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded shadow-sm transition text-xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Import Data
                    </a>
                    <span class="text-xs text-gray-500 hidden md:inline font-medium">Total: {{ $patients->total() }} Pasien</span>
                </div>

                <div class="w-full md:w-1/3">
                    <form action="{{ route('patients.index') }}" method="GET" class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari Nama / No RM..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm transition shadow-sm">
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        @if(request('search'))
                            <a href="{{ route('patients.index') }}" class="absolute right-3 top-2.5 text-gray-400 hover:text-red-500 text-xs font-bold">✕</a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- TABEL DATA --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal text-left">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 uppercase text-[11px] font-bold tracking-wider border-b border-gray-300">
                                <th class="px-4 py-3 w-28 border-r border-gray-200">No RM</th>
                                <th class="px-4 py-3 w-24 text-center border-r border-gray-200">Status</th>
                                <th class="px-4 py-3 border-r border-gray-200">Nama Pasien</th>
                                <th class="px-4 py-3 border-r border-gray-200">Diagnosis</th>
                                <th class="px-4 py-3 w-20 text-center border-r border-gray-200">Usia</th>
                                <th class="px-4 py-3">Sources</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm bg-white">
                            @forelse($patients as $patient)
                            <tr class="hover:bg-blue-50 transition duration-150">
                                
                                <td class="px-4 py-3 font-mono font-bold text-gray-700 border-r border-gray-100">
                                    {{ $patient->no_rm }}
                                </td>

                                {{-- KOLOM STATUS (Desain Kotak & Rapi) --}}
                                <td class="px-4 py-3 text-center border-r border-gray-100">
                                    @if($patient->is_converted)
                                        <span class="px-2 py-1 inline-block text-[10px] font-bold text-white bg-green-600 rounded shadow-sm tracking-wide">
                                            CONVERTED
                                        </span>
                                    @else
                                        <span class="px-2 py-1 inline-block text-[10px] font-bold text-gray-500 bg-gray-100 border border-gray-200 rounded tracking-wide">
                                            NON-ACTIVE
                                        </span>
                                    @endif
                                </td>
                                
                                <td class="px-4 py-3 border-r border-gray-100">
                                    <div class="font-semibold text-gray-800">{{ $patient->name_of_patient }}</div>
                                    @if(!empty($patient->overseas_hospital) && $patient->overseas_hospital != '-')
                                        <div class="text-[10px] text-amber-700 mt-1 flex items-center gap-1 font-medium bg-amber-50 px-1 py-0.5 rounded w-fit">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ $patient->overseas_hospital }}
                                        </div>
                                    @endif
                                </td>
                                
                                <td class="px-4 py-3 text-gray-600 text-xs border-r border-gray-100 italic">
                                    {{ Str::limit($patient->diagnosis ?? '-', 40) }}
                                </td>
                                
                                <td class="px-4 py-3 text-center text-gray-700 border-r border-gray-100 font-medium">
                                    {{ $patient->age ?? '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1.5">
                                        @php
                                            $sourceMap = [
                                                'MO' => ['col' => 'source_information_mo', 'color' => 'bg-blue-50 text-blue-700 border-blue-200'],
                                                'RO' => ['col' => 'source_information_ro', 'color' => 'bg-red-50 text-red-700 border-red-200'],
                                                'BO' => ['col' => 'source_information_bo', 'color' => 'bg-pink-50 text-pink-700 border-pink-200'],
                                                'GO' => ['col' => 'source_information_go', 'color' => 'bg-purple-50 text-purple-700 border-purple-200'],
                                                'PO' => ['col' => 'source_information_po', 'color' => 'bg-cyan-50 text-cyan-700 border-cyan-200'],
                                                'AO' => ['col' => 'source_information_ao', 'color' => 'bg-orange-50 text-orange-700 border-orange-200'],
                                            ];
                                            $visitedClinics = $patient->visits->pluck('klinik')->unique()->filter();
                                            $shownClinics = [];
                                        @endphp

                                        @foreach($visitedClinics as $klinikCode)
                                            @php 
                                                $kCode = strtoupper($klinikCode);
                                                $config = $sourceMap[$kCode] ?? null;
                                            @endphp
                                            @if($config)
                                                @php 
                                                    $colName = $config['col'];
                                                    $sourceVal = $patient->$colName;
                                                @endphp
                                                @if(!empty($sourceVal) && $sourceVal !== '-' && $sourceVal !== '0')
                                                    <span class="px-2 py-0.5 rounded border text-[10px] font-semibold flex items-center gap-1.5 {{ $config['color'] }}">
                                                        <span class="w-3.5 h-3.5 rounded bg-white flex items-center justify-center text-[9px] text-gray-600 border border-gray-200 shadow-sm">{{ count($shownClinics) + 1 }}</span>
                                                        {{ $kCode }}
                                                    </span>
                                                    @php $shownClinics[] = $kCode; @endphp
                                                @endif
                                            @endif
                                        @endforeach

                                        @foreach($sourceMap as $kCode => $config)
                                            @if(!in_array($kCode, $shownClinics))
                                                @php 
                                                    $colName = $config['col'];
                                                    $sourceVal = $patient->$colName;
                                                @endphp
                                                @if(!empty($sourceVal) && $sourceVal !== '-' && $sourceVal !== '0')
                                                    <span class="px-2 py-0.5 rounded border text-[10px] font-semibold opacity-60 {{ $config['color'] }}">
                                                        {{ $kCode }}*
                                                    </span>
                                                @endif
                                            @endif
                                        @endforeach

                                        @if(empty($shownClinics) && empty($sourceVal))
                                            <span class="text-gray-300 text-xs">-</span>
                                        @endif
                                    </div>
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center bg-gray-50 border-t border-gray-200">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-12 h-12 mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <p class="text-sm font-medium">Belum ada data pasien.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                    {{ $patients->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>