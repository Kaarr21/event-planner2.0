<div class="bg-transparent overflow-hidden">
    <div class="p-0">
        @if (session()->has('invite_warning'))
            <div class="mb-8 p-6 bg-brand-yellow/10 border border-brand-yellow/20 rounded-2xl animate-in slide-in-from-top-4">
                <p class="text-[10px] font-black text-brand-yellow uppercase tracking-widest mb-4 italic">{{ session('invite_warning') }}</p>
                <div class="flex gap-3">
                    <button type="button" wire:click="resendInvite" class="px-6 py-2.5 bg-brand-yellow text-gray-900 rounded-xl text-[10px] font-black uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-brand-yellow/20">Resend Anyway</button>
                    <button type="button" wire:click="$reset" class="px-6 py-2.5 bg-white/5 border border-white/10 text-gray-400 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-white/10 transition-all">Cancel</button>
                </div>
            </div>
        @endif

        <form wire:submit.prevent="invite" class="space-y-8">
            <div class="space-y-3">
                <label for="email" class="text-[10px] font-black text-white/50 uppercase tracking-[.3em] ml-2">Email Identity</label>
                <input wire:model="email" id="email" type="email" class="w-full bg-white/10 border-0 ring-1 ring-white/10 text-white text-sm rounded-2xl focus:ring-2 focus:ring-white p-5 transition-all font-bold italic placeholder-white/20 shadow-inner" placeholder="talent@onyx.com" required />
                @error('email') <span class="text-[10px] text-white font-black uppercase tracking-widest ml-2 bg-brand-red/80 px-2 py-0.5 rounded">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-3">
                <label for="invite_message" class="text-[10px] font-black text-white/50 uppercase tracking-[.3em] ml-2">Personal Directive (Optional)</label>
                <textarea wire:model="message" id="invite_message" class="w-full bg-white/10 border-0 ring-1 ring-white/10 text-white text-sm rounded-2xl focus:ring-2 focus:ring-white p-5 transition-all font-medium italic placeholder-white/20 shadow-inner" rows="3" placeholder="Join the inner circle for a premium experience..."></textarea>
                @error('message') <span class="text-[10px] text-white font-black uppercase tracking-widest ml-2 bg-brand-red/80 px-2 py-0.5 rounded">{{ $message }}</span> @enderror
            </div>

            @if (session()->has('invite_message'))
                <div class="bg-white/10 border border-white/20 text-white p-4 rounded-xl text-[9px] font-black uppercase tracking-[.2em] animate-in fade-in">
                    {{ session('invite_message') }}
                </div>
            @endif

            <div class="flex justify-end pt-4">
                <button type="submit" class="w-full bg-white text-brand-orange px-10 py-5 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] shadow-2xl hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-3 group">
                    Dispatch Invitation
                    <span class="material-symbols-outlined text-lg group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform">send</span>
                </button>
            </div>
        </form>
    </div>
</div>
