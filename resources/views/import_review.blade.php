<x-app-layout>
    <div class="p-6 bg-white rounded-lg shadow">
        <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
            <span>🔍</span> 
            Review Import: <span class="text-blue-600">{{ $type == 'KLINIK' ? 'Kunjungan Klinik' : 'Database Pasien' }}</span>
        </h2>
        
        {{-- LEGEND STATUS --}}
        <div class="flex flex-wrap gap-4 mb-4 text-sm bg-gray-50 p-3 rounded border">
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full font-bold">BARU</span>
                <span class="text-gray-600">: Data belum ada di sistem.</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full font-bold">UPDATE</span>
                <span class="text-gray-600">: Data sudah ada tapi isinya berubah.</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full font-bold">SAMA</span>
                <span class="text-gray-600">: Data persis sama (Akan dilewati/timpa aman).</span>
            </div>
        </div>

        <div class="overflow-x-auto mb-6 shadow-sm border rounded">
            <table class="w-full border-collapse border-gray-200 text-sm">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="p-3 text-center w-24">Status</th>
                        <th class="p-3 text-center w-16">Aksi</th>
                        <th class="p-3 text-left">No RM</th>
                        
                        @if($type == 'KLINIK')
                            <th class="p-3 text-left">Tanggal</th>
                            <th class="p-3 text-left">Pasien</th>
                            <th class="p-3 text-left">Klinik</th>
                            <th class="p-3 text-left">Catatan (Baru)</th>
                        @else
                            {{-- HEADER UNTUK DATABASE PASIEN --}}
                            <th class="p-3 text-left">Nama Pasien</th>
                            <th class="p-3 text-left">Diagnosis</th>
                            <th class="p-3 text-left">Umur</th>
                            <th class="p-3 text-left">Notes</th>
                        @endif

                        <th class="p-3 text-left bg-gray-700 w-1/4">Perbandingan (Data Lama)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($previewData as $data)
                    <tr class="{{ $data->class_row }} border-b hover:brightness-95 transition">
                        
                        {{-- 1. STATUS --}}
                        <td class="p-3 text-center font-bold text-xs">
                            @if($data->status_import == 'NEW')
                                <span class="bg-green-200 text-green-800 px-2 py-1 rounded">BARU</span>
                            @elseif($data->status_import == 'UPDATE')
                                <span class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded">UPDATE</span>
                            @else
                                <span class="bg-gray-200 text-gray-600 px-2 py-1 rounded">SAMA</span>
                            @endif
                        </td>

                        {{-- 2. AKSI HAPUS --}}
                        <td class="p-3 text-center">
                            <form action="{{ route('import.destroyTemp', $data->id) }}" method="POST" onsubmit="return confirm('Yakin hapus baris ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 hover:scale-110 transition" title="Hapus baris ini">
                                    🗑️
                                </button>
                            </form>
                        </td>

                        {{-- 3. NO RM --}}
                        <td class="p-3 font-mono font-semibold">{{ $data->no_rm }}</td>

                        {{-- 4. KOLOM DINAMIS --}}
                        @if($type == 'KLINIK')
                            <td class="p-3">{{ $data->tanggal_kunjungan ? $data->tanggal_kunjungan->format('d/m/Y') : '-' }}</td>
                            <td class="p-3">{{ $data->nama_pasien }}</td>
                            <td class="p-3">{{ $data->klinik }}</td>
                            <td class="p-3">{{ Str::limit($data->catatan, 50) }}</td>
                            
                            {{-- Perbandingan Klinik --}}
                            <td class="p-3 text-gray-600 text-xs italic border-l">
                                @if($data->status_import == 'UPDATE' && isset($data->old_data))
                                    <div><span class="font-bold text-red-500">Catatan Lama:</span> {{ Str::limit($data->old_data->catatan, 50) }}</div>
                                    <div><span class="font-bold text-red-500">Stadium Lama:</span> {{ $data->old_data->stadium }}</div>
                                @elseif($data->status_import == 'NEW')
                                    <span class="text-green-600">Data Baru</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                        @else
                            {{-- ISI UNTUK DATABASE PASIEN --}}
                            <td class="p-3">{{ $data->nama_pasien }}</td>
                            <td class="p-3">{{ Str::limit($data->diagnosis, 30) }}</td>
                            <td class="p-3">{{ $data->age }}</td>
                            <td class="p-3">{{ Str::limit($data->activities_notes, 30) }}</td>
                            
                            {{-- Perbandingan Pasien --}}
                            <td class="p-3 text-gray-600 text-xs italic border-l">
                                @if($data->status_import == 'UPDATE' && isset($data->old_data))
                                    <div><span class="font-bold text-red-500">Nama Lama:</span> {{ $data->old_data->nama_pasien }}</div>
                                    <div><span class="font-bold text-red-500">Diag Lama:</span> {{ Str::limit($data->old_data->diagnosis, 30) }}</div>
                                @elseif($data->status_import == 'NEW')
                                    <span class="text-green-600">Pasien Baru</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        @endif

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- FOOTER ACTION --}}
        <div class="flex justify-between items-center bg-gray-50 p-4 rounded border-t">
            <div class="text-gray-600 text-sm">
                Pastikan data di atas sudah benar. Data berstatus <b>UPDATE</b> akan menimpa data lama.
            </div>
            <div class="flex gap-3">
                <a href="{{ route('import.form') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition">
                    ❌ Batal / Upload Ulang
                </a>
                
                <form action="{{ route('import.commit') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-blue-700 text-white font-bold rounded hover:bg-blue-800 shadow-lg flex items-center gap-2 transition transform hover:-translate-y-0.5">
                        <span>💾</span> Simpan Semua ke Database
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>