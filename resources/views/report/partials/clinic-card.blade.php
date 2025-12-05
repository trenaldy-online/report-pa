@php
    $themeColor = $color ?? 'gray';
    $colors = [
        'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'btn' => 'bg-blue-600 hover:bg-blue-700', 'bar' => 'bg-blue-500'],
        'rose' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'btn' => 'bg-rose-600 hover:bg-rose-700', 'bar' => 'bg-rose-500'],
        'pink' => ['bg' => 'bg-pink-50', 'text' => 'text-pink-600', 'btn' => 'bg-pink-600 hover:bg-pink-700', 'bar' => 'bg-pink-500'],
        'purple' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'btn' => 'bg-purple-600 hover:bg-purple-700', 'bar' => 'bg-purple-500'],
        'cyan' => ['bg' => 'bg-cyan-50', 'text' => 'text-cyan-600', 'btn' => 'bg-cyan-600 hover:bg-cyan-700', 'bar' => 'bg-cyan-500'],
        'orange' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-600', 'btn' => 'bg-orange-600 hover:bg-orange-700', 'bar' => 'bg-orange-500'],
        'gray' => ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'btn' => 'bg-gray-600 hover:bg-gray-700', 'bar' => 'bg-gray-500'],
    ];
    $c = $colors[$themeColor] ?? $colors['gray'];
@endphp

{{-- x-data untuk mengontrol Modal --}}
<div x-data="{ showModal: false }" class="bg-white rounded-[1.5rem] border border-gray-100 shadow-sm overflow-hidden flex flex-col h-full w-full break-inside-avoid">
    
    {{-- HEADER CARD --}}
    <div class="p-5 border-b border-gray-50 flex justify-between items-start {{ $c['bg'] }}">
        <div>
            <span class="text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-1 block">{{ $code }} Unit</span>
            <h3 class="font-bold text-lg text-gray-800">{{ $title }}</h3>
        </div>
        <div class="flex gap-4">
            <div class="text-right">
                <div class="text-3xl font-bold {{ $c['text'] }}">{{ $data['total_visit'] }}</div>
                <div class="text-[9px] text-gray-400 font-medium uppercase mt-1">Patients</div>
            </div>
            <div class="text-right border-l border-gray-200 pl-3">
                <div class="text-xl font-bold text-gray-600">{{ $data['monthly_total'] }}</div>
                <div class="text-[9px] text-gray-400 font-medium uppercase mt-1">Month Total</div>
            </div>
        </div>
    </div>

    <div class="p-5 flex flex-col flex-1">
        {{-- WRAPPER KONTEN ATAS --}}        
        {{-- SECTION: CONVERSION --}}
        @if(isset($data['converted']) || isset($data['not_converted']))
        <div class="mb-4 pb-4 border-b border-dashed border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</h4>
                @php $rate = $data['total_visit'] > 0 ? ($data['converted'] / $data['total_visit']) * 100 : 0; @endphp
                <span class="text-[10px] font-bold {{ $c['text'] }} bg-white px-2 py-0.5 rounded border border-gray-100">{{ number_format($rate, 1) }}% Rate</span>
            </div>
            <div class="bg-gray-50 rounded-lg p-1 flex text-center text-[10px] font-medium">
                <div class="flex-1 py-1"><div class="{{ $c['text'] }}">Conv.</div><div class="text-sm font-bold {{ $c['text'] }}">{{ $data['converted'] }}</div></div>
                <div class="flex-1 py-1 border-l border-gray-200"><div class="text-amber-600">Not Conv.</div><div class="text-sm font-bold text-amber-600">{{ $data['not_converted'] }}</div></div>
            </div>
            {{-- Reasons List --}}
            @if(count($data['reasons']) > 0)
            <div class="mt-3">
                {{-- LABEL JUDUL REASON --}}
                <div class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1 pl-1">
                    Reasons (Not Converted)
                </div>
                
                <div class="pl-2 border-l-2 border-amber-100 space-y-1">
                    @foreach($data['reasons'] as $reason)
                    <div class="flex justify-between items-center text-[10px] text-gray-600">
                        <span class="truncate w-3/4">{{ $reason->reason_text }}</span>
                        <span class="font-bold bg-amber-50 text-amber-600 px-1.5 rounded text-[9px]">{{ $reason->total }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- SECTION: TRAFFIC SOURCES --}}
        <div>
            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Sources</h4>
            <div class="space-y-2 pr-2 custom-scrollbar" style="max-height: 120px; overflow-y: auto;">
                @foreach($data['sources'] as $sourceName => $count)
                    @php $percent = $data['total_visit'] > 0 ? ($count / $data['total_visit']) * 100 : 0; @endphp
                    <div class="group">
                        <div class="flex justify-between items-end mb-0.5 text-xs">
                            <span class="text-gray-600 font-medium truncate w-3/4">{{ $sourceName }}</span>
                            <span class="font-bold text-gray-800">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1"><div class="{{ $c['bar'] }} h-1 rounded-full opacity-80" style="width: {{ $percent }}%"></div></div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- TOMBOL LIHAT DETAIL (MEMICU MODAL) --}}
        <div class="mt-auto pt-4 border-t border-gray-100 print:hidden">
            <button @click="showModal = true" class="w-full py-2 rounded-lg text-xs font-bold text-white {{ $c['btn'] }} transition shadow-sm flex justify-center items-center gap-2">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                Lihat Detail Pasien & Notes
            </button>
        </div>
        {{-- END WRAPPER KONTEN ATAS --}}
    </div>

    {{-- ========================================== --}}
    {{--                 MODAL POPUP                --}}
    {{-- ========================================== --}}
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            {{-- Backdrop --}}
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal Panel --}}
            <div x-show="showModal" x-transition.scale class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                
                {{-- Modal Header --}}
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span class="px-2 py-1 rounded text-xs text-white {{ $c['btn'] }}">{{ $code }}</span>
                            Detail Pasien & Notes
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Daftar pasien {{ $title }} periode ini.</p>
                    </div>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                {{-- Modal Body (List Pasien) --}}
                <div class="px-6 py-6 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    @foreach($data['details_list'] as $index => $patient)
                    
                    {{-- x-data untuk handle status Edit per item --}}
                    <div x-data="{ isEditing: false, noteVal: '{{ e($patient->note == '-' ? '' : $patient->note) }}' }" class="p-4 mb-3 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-white hover:shadow-sm transition duration-200">
                        
                        {{-- Header Pasien --}}
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm">
                                    {{ $index + 1 }}. {{ $patient->name }} 
                                    <span class="font-normal text-gray-500">({{ $patient->no_rm }})</span>
                                </h4>
                                <div class="text-xs text-gray-500 mt-0.5 flex gap-2">
                                    <span>{{ $patient->age }} Th</span>
                                    <span class="text-gray-300">|</span>
                                    <span>{{ $patient->diagnosis ?? '-' }}</span>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-white border border-gray-200 text-gray-600">
                                {{ $patient->source ?? 'Unknown' }}
                            </span>
                        </div>
                        
                        {{-- AREA NOTE (TAMPILAN BACA) --}}
                        <div x-show="!isEditing" class="mt-2 text-xs relative group/note">
                            <div class="bg-yellow-50 border-l-4 border-yellow-300 p-3 rounded-r-lg text-gray-700 italic cursor-pointer hover:bg-yellow-100 transition" @click="isEditing = true" title="Klik untuk edit">
                                <span class="font-bold text-yellow-600 not-italic mr-1">Note (Visit #{{ $patient->visit_rank }}):</span>
                                <span x-text="noteVal || '-'"></span>
                                
                                {{-- Icon Pencil (Muncul saat hover) --}}
                                <svg class="w-3 h-3 text-yellow-600 absolute right-2 top-3 opacity-0 group-hover/note:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </div>
                        </div>

                        {{-- AREA FORM EDIT (TAMPILAN EDIT) --}}
                        <div x-show="isEditing" style="display: none;" class="mt-2">
                            <form action="{{ route('report.update-note') }}" method="POST">
                                @csrf
                                <input type="hidden" name="no_rm" value="{{ $patient->no_rm }}">
                                <input type="hidden" name="note_col" value="{{ $patient->note_col }}">
                                
                                <div class="relative">
                                    <textarea name="note_content" x-model="noteVal" rows="2" class="w-full text-xs rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 bg-white" placeholder="Tulis catatan aktivitas..."></textarea>
                                </div>
                                
                                <div class="flex justify-end gap-2 mt-2">
                                    <button type="button" @click="isEditing = false" class="text-xs text-gray-500 hover:text-gray-700 px-3 py-1">Batal</button>
                                    <button type="submit" class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded shadow-sm">Simpan</button>
                                </div>
                            </form>
                        </div>

                    </div>
                    @endforeach
                </div>

                {{-- Modal Footer --}}
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex justify-end">
                    <button @click="showModal = false" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>