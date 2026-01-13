<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <h2 class="text-2xl font-bold mb-6">Follow Up Pasien (WhatsApp)</h2>

                <form method="GET" action="{{ route('followup.index') }}" class="mb-8 p-4 bg-gray-50 rounded border flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-sm font-bold mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">Cari No RM (Opsional)</label>
                        <input type="text" name="no_rm" value="{{ $searchRm }}" placeholder="Contoh: 12345" class="border rounded px-3 py-2 w-48">
                    </div>
                    <div>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Tampilkan Data
                        </button>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-2 px-4 border-b text-left">No RM & Nama</th>
                                <th class="py-2 px-4 border-b text-left">Info Kunjungan</th>
                                <th class="py-2 px-4 border-b text-left">Diagnosis & Catatan</th>
                                <th class="py-2 px-4 border-b text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-b align-top">
                                        <div class="font-bold text-blue-600">{{ $row->no_rm }}</div>
                                        <div class="font-semibold">{{ $row->nama_pasien }}</div>
                                        <div class="text-xs text-gray-500">({{ $row->jenis_kelamin }})</div>
                                        <div class="text-sm mt-1">📞 {{ $row->telepon }}</div>
                                    </td>
                                    <td class="py-2 px-4 border-b align-top">
                                        <div class="text-sm">Tgl: {{ $row->tanggal_kunjungan }}</div>
                                    </td>
                                    <td class="py-2 px-4 border-b align-top">
                                        <div class="text-xs font-bold text-gray-600">Diagnosis Visit:</div>
                                        <div class="text-sm mb-2">{{ $row->diagnosis_visit ?? '-' }}</div>
                                        
                                        <div class="text-xs font-bold text-gray-600">Catatan Visit:</div>
                                        <div class="text-sm italic mb-2">"{{ $row->catatan ?? '-' }}"</div>

                                        <div class="text-xs font-bold text-gray-600">Master Notes:</div>
                                        <ul class="text-xs list-disc pl-4 text-gray-500">
                                            @if($row->activities_notes) <li>{{ Str::limit($row->activities_notes, 50) }}</li> @endif
                                            @if($row->activities_notes2) <li>{{ Str::limit($row->activities_notes2, 50) }}</li> @endif
                                        </ul>
                                    </td>
                                    <td class="py-2 px-4 border-b align-middle text-center">
                                        <button onclick="copyData(this)" 
                                            class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 transition"
                                            data-json="{{ json_encode($row) }}">
                                            Copy Data
                                        </button>
                                        <span class="text-green-600 text-xs font-bold hidden msg-success block mt-1">Copied!</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-gray-500">Data tidak ditemukan sesuai filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyData(btn) {
            // 1. Ambil data dari atribut tombol
            let data = JSON.parse(btn.getAttribute('data-json'));

            // 2. Susun Format Teks Mentah (Raw Data)
            let textToCopy = `DATA PASIEN (SUMBER DATABASE)
================================
No RM: ${data.no_rm}
Nama Pasien: ${data.nama_pasien} (${data.jenis_kelamin})
Telepon: ${data.telepon || '-'}
Tanggal Kunjungan: ${data.tanggal_kunjungan}

DIAGNOSIS & CATATAN:
Diagnosis (Visit): ${data.diagnosis_visit || '-'}
Diagnosis (Master): ${data.diagnosis_master || '-'}
Catatan Dokter (Visit): ${data.catatan || '-'}

RIWAYAT AKTIVITAS (MASTER DB):
1. ${data.activities_notes || '-'}
2. ${data.activities_notes2 || '-'}
3. ${data.activities_notes3 || '-'}
================================`;

            // 3. Salin ke Clipboard
            navigator.clipboard.writeText(textToCopy).then(() => {
                // Feedback Visual
                let msg = btn.nextElementSibling;
                msg.classList.remove('hidden');
                
                // Hilangkan pesan setelah 2 detik
                setTimeout(() => {
                    msg.classList.add('hidden');
                }, 2000);
            }).catch(err => {
                console.error('Gagal menyalin:', err);
                alert('Gagal menyalin data.');
            });
        }
    </script>
</x-app-layout>