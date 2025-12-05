<x-app-layout>
    {{-- STYLE MODERN CLEAN & PRINT CONFIG --}}
    <style>
        /* 1. Scrollbar Halus (Untuk Tampilan Web) */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f8fafc; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* 2. KONFIGURASI KHUSUS PRINT / PDF */
        @media print {
            /* Atur Kertas A4 Margin Tipis */
            @page {
                size: A4;
                margin: 10mm;
            }

            /* Paksa browser mencetak warna background (PENTING!) */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            /* Hilangkan elemen yang tidak perlu dicetak */
            nav, header, .no-print, button, input, form {
                display: none !important;
            }

            /* Reset Background jadi Putih Bersih */
            body, .min-h-screen, .bg-gray-50 {
                background-color: white !important;
                height: auto !important;
            }

            /* Hilangkan Shadow agar teks lebih tajam */
            .shadow-sm, .shadow-md, .shadow-lg {
                box-shadow: none !important;
                border: 1px solid #e2e8f0 !important; /* Ganti shadow dengan border tipis */
            }

            /* Atur Scrollbar agar tercetak penuh (Expand semua konten) */
            .overflow-y-auto, .overflow-x-auto {
                overflow: visible !important;
                height: auto !important;
                max-height: none !important;
            }

            /* Pastikan Grid tidak berantakan */
            .grid {
                display: grid !important;
                gap: 1.5rem !important;
            }

            /* Header Laporan Khusus Print */
            .print-header {
                display: block !important;
                margin-bottom: 20px;
                padding-bottom: 10px;
                border-bottom: 2px solid #000;
            }
            
            /* Footer/Halaman */
            .page-break {
                page-break-after: always;
            }

            /* Agar grid 3 kolom tetap jalan di print landscape/lebar, atau paksa block */
            .print-grid { display: grid !important; grid-template-columns: repeat(3, 1fr) !important; gap: 1rem !important; }

        }

        /* Sembunyikan header print di tampilan web */
        .print-header { display: none; }


    </style>

    <div class="min-h-screen bg-gray-50 text-gray-900 font-sans pb-20">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
            
            {{-- HEADER KHUSUS PRINT (Hanya muncul saat di-print) --}}
            <div class="print-header text-center">
                <h1 class="text-2xl font-bold text-black uppercase tracking-wider">Laporan Bi-Weekly AHCC</h1>
                <p class="text-sm text-gray-600 mt-1">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
            </div>

            {{-- HEADER WEB & FILTER BAR (Akan hilang saat di-print karena class 'no-print') --}}
            <div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-10 no-print">
                <div>
                    <h2 class="text-xs font-bold text-blue-600 tracking-widest uppercase mb-1">
                        AHCC Performance Report
                    </h2>
                    <h1 class="text-3xl md:text-4xl font-light text-gray-800">
                        BiWeekly <span class="font-bold text-black">Summary</span>
                    </h1>
                </div>

                <div class="bg-white p-1.5 rounded-2xl border border-gray-200 flex flex-col sm:flex-row items-center gap-2 shadow-sm">
                    
                    {{-- UPDATE: Action ke report.filter, Method POST --}}
                    <form action="{{ route('report.filter') }}" method="POST" class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                        @csrf {{-- WAJIB: Token keamanan untuk form POST --}}
                        
                        <div class="relative group w-full sm:w-auto">
                            <input type="date" name="start_date" value="{{ $startDate }}" 
                                class="bg-gray-50 border-0 text-gray-600 text-xs rounded-xl focus:ring-2 focus:ring-blue-500 block w-full pl-3 p-2.5 font-medium">
                        </div>
                        <span class="text-gray-400 hidden sm:inline">-</span>
                        <div class="relative group w-full sm:w-auto">
                            <input type="date" name="end_date" value="{{ $endDate }}" 
                                class="bg-gray-50 border-0 text-gray-600 text-xs rounded-xl focus:ring-2 focus:ring-blue-500 block w-full pl-3 p-2.5 font-medium">
                        </div>
                        <button type="submit" class="w-full sm:w-auto bg-black hover:bg-gray-800 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-lg shadow-gray-200 transition-all">
                            Filter
                        </button>
                    </form>
                    
                    <button onclick="printReport()" class="bg-blue-50 text-blue-600 px-4 py-2.5 rounded-xl text-xs font-bold hover:bg-blue-100 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Save PDF
                    </button>
                    
                </div>
            </div>
            {{-- END HEADER & FILTER BAR --}}

            {{-- CONTENT LAPORAN --}}
            {{-- 1. GRID KARTU KLINIK (3 Kolom x 2 Baris) --}}
            <div class="page-break grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mb-10 print-grid">
                @include('report.partials.clinic-card', ['title' => 'Medical Oncology', 'code' => 'MO', 'data' => $moData, 'conversion' => $moConversionRate, 'color' => 'blue'])
                @include('report.partials.clinic-card', ['title' => 'Radiation Oncology', 'code' => 'RO', 'data' => $roData, 'conversion' => $roConversionRate, 'color' => 'rose'])
                @include('report.partials.clinic-card', ['title' => 'Breast Oncology', 'code' => 'BO', 'data' => $boData, 'simple' => true, 'color' => 'pink'])
                @include('report.partials.clinic-card', ['title' => 'Gyne Oncology', 'code' => 'GO', 'data' => $goData, 'simple' => true, 'color' => 'purple'])
                @include('report.partials.clinic-card', ['title' => 'Pulmo Oncology', 'code' => 'PO', 'data' => $poData, 'simple' => true, 'color' => 'cyan'])
                @include('report.partials.clinic-card', ['title' => 'Pediatric Oncology', 'code' => 'AO', 'data' => $aoData, 'simple' => true, 'color' => 'orange'])
            </div>

            {{-- 3. HALAMAN DETAIL (ACTIVITIES / NOTES) --}}
            <div class="hidden print:block bg-white p-8 rounded-2xl border border-gray-100 shadow-sm print:shadow-none print:border-0">
                <h2 class="text-xl font-bold text-gray-900 mb-2">📝 Detail Aktivitas Pasien & Notes</h2>

                {{-- Loop semua klinik menggunakan Partial Detail --}}
                <div class="grid grid-cols-1 gap-8">
                    @include('report.partials.clinic-detail-list', ['title' => 'Medical Oncology', 'code' => 'MO', 'data' => $moData])
                    @include('report.partials.clinic-detail-list', ['title' => 'Radiation Oncology', 'code' => 'RO', 'data' => $roData])
                    @include('report.partials.clinic-detail-list', ['title' => 'Breast Oncology', 'code' => 'BO', 'data' => $boData])
                    @include('report.partials.clinic-detail-list', ['title' => 'Gyne Oncology', 'code' => 'GO', 'data' => $goData])
                    @include('report.partials.clinic-detail-list', ['title' => 'Pulmo Oncology', 'code' => 'PO', 'data' => $poData])
                    @include('report.partials.clinic-detail-list', ['title' => 'Pediatric Oncology', 'code' => 'AO', 'data' => $aoData])
                </div>
            </div>
            {{-- END CONTENT LAPORAN --}}

        </div>
    </div>

    {{-- SCRIPT KHUSUS PRINT FILENAME --}}
    <script>
        function printReport() {
            // 1. Simpan Judul Asli Halaman
            const originalTitle = document.title;

            // 2. Ambil Tanggal dari PHP Blade
            // Kita format jadi d-m-Y agar aman untuk nama file (jangan pakai garis miring /)
            const start = "{{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }}";
            const end = "{{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}";

            // 3. Set Judul Baru (Ini yang akan jadi Nama File PDF)
            document.title = `Laporan BiWeekly (${start} sd ${end})`;

            // 4. Jalankan Print
            window.print();

            // 5. Kembalikan Judul Asli setelah selesai (opsional, biar rapi)
            setTimeout(() => {
                document.title = originalTitle;
            }, 1000);
        }
    </script>

</x-app-layout>