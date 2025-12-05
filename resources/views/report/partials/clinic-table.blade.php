<div class="bg-zinc-900 rounded-[2rem] border border-zinc-800 p-6 h-full flex flex-col relative overflow-hidden group hover:border-zinc-700 transition duration-500">
    
    {{-- Ambient Light Effect (Hiasan) --}}
    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 rounded-full blur-3xl -mr-10 -mt-10 pointer-events-none"></div>

    {{-- HEADER CARD --}}
    <div class="flex justify-between items-start mb-8 relative z-10">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_10px_#10b981]"></span>
                <span class="text-[10px] font-bold tracking-widest text-zinc-500 uppercase">{{ $code ?? 'CL' }} UNIT</span>
            </div>
            <h3 class="font-medium text-lg text-zinc-100 tracking-wide">{{ $title }}</h3>
        </div>
        <div class="text-right">
            <div class="text-4xl font-light text-white tracking-tighter">{{ $data['total_new'] }}</div>
            <div class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider mt-1">New Patients</div>
        </div>
    </div>

    {{-- CONVERSION SECTION (Matrix Box Style) --}}
    @if(!isset($simple))
    <div class="mb-8 p-1 rounded-2xl bg-gradient-to-b from-zinc-800 to-zinc-900 border border-zinc-700/50">
        <div class="bg-zinc-950/80 rounded-xl p-4 backdrop-blur-sm">
            <div class="flex justify-between items-center mb-4">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Conversion Flow</span>
                <span class="text-xs font-bold text-black bg-emerald-400 px-2 py-0.5 rounded shadow-[0_0_10px_rgba(52,211,153,0.3)]">
                    {{ number_format($conversion, 1) }}%
                </span>
            </div>
            
            {{-- Visualization Bar --}}
            <div class="flex items-center gap-1 h-12 w-full">
                {{-- Converted Bar --}}
                @if($new_conv > 0)
                <div style="flex: {{ $new_conv }}" class="h-full bg-emerald-500/20 border border-emerald-500/50 rounded-lg flex flex-col justify-center items-center relative group/bar">
                    <span class="text-[10px] text-emerald-400 font-bold uppercase mb-0.5">Start</span>
                    <span class="text-lg font-bold text-white leading-none">{{ $new_conv }}</span>
                </div>
                @endif

                {{-- Separator arrow --}}
                <div class="text-zinc-700 px-1">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </div>

                {{-- Not Converted Bar --}}
                <div style="flex: {{ max(1, $data['total_new'] - $new_conv) }}" class="h-full bg-zinc-900 border border-zinc-800 rounded-lg flex flex-col justify-center items-center">
                    <span class="text-[10px] text-zinc-600 font-bold uppercase mb-0.5">Pending</span>
                    <span class="text-lg font-bold text-zinc-400 leading-none">{{ $data['total_new'] - $new_conv }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- SOURCE LIST (Minimalist) --}}
    <div class="flex-grow">
        <h4 class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest mb-4 border-b border-zinc-800 pb-2">Traffic Sources</h4>
        <div class="space-y-4">
            @foreach($data['sources'] as $sourceName => $count)
                @if($count > 0 || (isset($main) && $main)) 
                <div class="group">
                    <div class="flex justify-between items-end mb-1">
                        <span class="text-xs text-zinc-400 group-hover:text-emerald-300 transition-colors">{{ $sourceName }}</span>
                        <span class="text-sm font-bold text-white">{{ $count }}</span>
                    </div>
                    {{-- Thin Line Progress --}}
                    <div class="w-full h-1 bg-zinc-800 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500 shadow-[0_0_8px_#10b981]" style="width: {{ $data['total_new'] > 0 ? ($count / $data['total_new']) * 100 : 0 }}%"></div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- FOOTER (Overseas) --}}
    @if($data['overseas'] > 0)
    <div class="mt-6 pt-4 border-t border-zinc-800/50">
        <div class="flex items-center justify-between text-zinc-400">
            <div class="flex items-center gap-2">
                <div class="p-1.5 bg-zinc-800 rounded-md text-emerald-400">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-xs font-medium">Overseas History</span>
            </div>
            <span class="text-xs font-bold text-white">{{ $data['overseas'] }}</span>
        </div>
    </div>
    @endif

</div>