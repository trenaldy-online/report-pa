<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import Data Pasien') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Menampilkan Pesan Sukses --}}
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Menampilkan Pesan Error --}}
                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>- {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf 
                        {{-- @csrf adalah token keamanan wajib di Laravel --}}

                        <div class="mb-4">
                            <label for="type" class="block text-gray-700 text-sm font-bold mb-2">Pilih Jenis File:</label>
                            
                            <select name="type" id="type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                <option value="" disabled selected>-- Pilih Tipe Import --</option>
                                
                                <option value="KLINIK">📂 Data Kunjungan Klinik (Harian)</option>
                                <option value="DATABASE">📂 Database Pasien (Master)</option>
                                
                                <option disabled>────────── Laporan Validasi ──────────</option>
                                
                                <option value="RADIO_CONVERTED">✅ Laporan Radioterapi (Converted)</option>
                                <option value="KEMO_CONVERTED">✅ Laporan Kemoterapi (Converted)</option>
                            </select>
                            
                            <p class="text-xs text-gray-500 mt-1">
                                *Pilih "Laporan Validasi" untuk otomatis mengupdate status pasien menjadi Converted.
                            </p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Upload File Excel (.xlsx / .xls):</label>
                            <input type="file" name="file" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Upload & Proses
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>