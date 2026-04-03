<div class="max-w-7xl mx-auto space-y-12 animate-in fade-in slide-in-from-bottom-8 duration-1000">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
        <div class="bg-white p-10 rounded-[4rem] border border-slate-100 shadow-lux group hover:-translate-y-2 transition-all duration-700">
            <div class="flex items-center gap-6 mb-8">
                <div class="w-16 h-16 rounded-[2rem] bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm group-hover:scale-110 transition-transform duration-700">
                    <span class="material-symbols-outlined font-bold text-3xl">payments</span>
                </div>
                <div>
                    <h3 class="text-[11px] font-bold uppercase tracking-[0.4em] text-slate-400 opacity-80">Allocation</h3>
                    <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest mt-1 italic">Total Orchestrated</p>
                </div>
            </div>
            <p class="text-5xl font-display font-extrabold text-slate-900 italic tracking-tight">${{ number_format($totalEstimated, 2) }}</p>
        </div>

        <div class="bg-white p-10 rounded-[4rem] border border-slate-100 shadow-lux group hover:-translate-y-2 transition-all duration-700 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-10 opacity-[0.03]">
                <span class="material-symbols-outlined text-9xl">account_balance_wallet</span>
            </div>
            <div class="flex items-center gap-6 mb-8 relative z-10">
                <div class="w-16 h-16 rounded-[2rem] bg-emerald-50 flex items-center justify-center text-emerald-600 shadow-sm group-hover:scale-110 transition-transform duration-700">
                    <span class="material-symbols-outlined font-bold text-3xl">account_balance_wallet</span>
                </div>
                <div>
                    <h3 class="text-[11px] font-bold uppercase tracking-[0.4em] text-slate-400 opacity-80">Burn Rate</h3>
                    <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mt-1 italic">Actual Realized</p>
                </div>
            </div>
            <p class="text-5xl font-display font-extrabold text-slate-900 italic tracking-tight relative z-10">${{ number_format($totalActual, 2) }}</p>
            @if($totalEstimated > 0)
                <div class="mt-8 w-full bg-slate-50 h-3 rounded-full overflow-hidden border border-slate-100 relative z-10 p-0.5">
                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000 shadow-[0_0_20px_rgba(16,185,129,0.3)]" style="width: {{ min(100, ($totalActual / $totalEstimated) * 100) }}%"></div>
                </div>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.3em] mt-4 text-right italic relative z-10">{{ round(($totalActual / $totalEstimated) * 100) }}% Depleted</p>
            @endif
        </div>

        <div class="bg-white p-10 rounded-[4rem] border border-slate-100 shadow-lux group hover:-translate-y-2 transition-all duration-700">
            <div class="flex items-center gap-6 mb-8">
                <div class="w-16 h-16 rounded-[2rem] bg-amber-50 flex items-center justify-center text-amber-600 shadow-sm group-hover:scale-110 transition-transform duration-700">
                    <span class="material-symbols-outlined font-bold text-3xl">priority_high</span>
                </div>
                <div>
                    <h3 class="text-[11px] font-bold uppercase tracking-[0.4em] text-slate-400 opacity-80">Liabilities</h3>
                    <p class="text-[10px] font-bold text-amber-600 uppercase tracking-widest mt-1 italic">Outstanding</p>
                </div>
            </div>
            <p class="text-5xl font-display font-extrabold text-slate-900 italic tracking-tight">${{ number_format($totalActual - $totalPaid, 2) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Category Breakdown -->
        <div class="space-y-10">
            <div class="bg-white p-12 rounded-[4rem] border border-slate-100 shadow-lux">
                <h3 class="text-3xl font-display font-extrabold text-slate-900 uppercase tracking-tight mb-12 italic">Visual Analysis</h3>
                <div class="space-y-10">
                    @forelse($categories as $catName => $stats)
                        <div class="space-y-4 group">
                            <div class="flex justify-between items-end">
                                <span class="text-[11px] font-bold uppercase tracking-[0.3em] text-slate-400 group-hover:text-indigo-600 transition-colors">{{ $catName }}</span>
                                <span class="text-sm font-bold text-slate-900 italic tracking-tight">${{ number_format($stats['actual'], 0) }} <span class="text-slate-300 mx-1">/</span> <span class="opacity-40 text-[11px] uppercaseTracking-widest text-slate-400 font-medium">${{ number_format($stats['estimated'], 0) }}</span></span>
                            </div>
                            <div class="w-full bg-slate-50 h-3 rounded-full overflow-hidden border border-slate-100 relative p-0.5">
                                <div class="h-full bg-gradient-to-r from-indigo-600 to-indigo-400 rounded-full transition-all duration-1000 group-hover:from-emerald-500 group-hover:to-emerald-400" style="width: {{ $stats['estimated'] > 0 ? min(100, ($stats['actual'] / $stats['estimated']) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 opacity-30">
                            <span class="material-symbols-outlined text-5xl mb-4">analytics</span>
                            <p class="text-[11px] font-bold uppercase tracking-[0.4em]">Zero metrics mapped.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            @if($this->hasPermission('edit_event'))
                <button wire:click="$set('isAddingItem', true)" class="w-full btn-lux p-10 rounded-[3rem] flex items-center justify-center gap-6 group">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center group-hover:rotate-90 transition-transform duration-500">
                        <span class="material-symbols-outlined text-2xl font-bold">add</span>
                    </div>
                    <span class="text-[13px] font-bold uppercase tracking-[0.4em]">Append Line Item</span>
                </button>
            @endif
        </div>

        <!-- Budget Items List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[4rem] border border-slate-100 shadow-lux overflow-hidden">
                <div class="p-12 border-b border-slate-50 flex justify-between items-center">
                    <div>
                        <h3 class="text-3xl font-display font-extrabold text-slate-900 uppercase tracking-tight italic leading-none mb-2">Fiscal Manuscript</h3>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.4em] opacity-80 italic">Validated orchestration expenditures.</p>
                    </div>
                    <span class="px-6 py-2.5 bg-indigo-50 text-indigo-600 text-[10px] font-bold uppercase tracking-widest rounded-full shadow-sm">{{ $budgets->count() }} Statements</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-12 py-8 text-[10px] font-bold uppercase tracking-[0.4em] text-slate-400">Orchestration Point</th>
                                <th class="px-12 py-8 text-[10px] font-bold uppercase tracking-[0.4em] text-slate-400">Division</th>
                                <th class="px-12 py-8 text-[10px] font-bold uppercase tracking-[0.4em] text-slate-400 text-right">Capital Value</th>
                                <th class="px-12 py-8 text-[10px] font-bold uppercase tracking-[0.4em] text-slate-400 text-center">Authentication</th>
                                @if($this->hasPermission('edit_event'))
                                    <th class="px-12 py-8 text-right"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($budgets as $item)
                                <tr class="hover:bg-slate-50/80 transition-all duration-700 group">
                                    <td class="px-12 py-10">
                                        <div class="font-bold text-slate-900 uppercase tracking-tight italic text-lg leading-none mb-2 group-hover:text-indigo-600 transition-colors">{{ $item->item_name }}</div>
                                        @if($item->description)
                                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] italic opacity-70">{{ \Illuminate\Support\Str::limit($item->description, 70) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-12 py-10">
                                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.3em] group-hover:text-slate-600 transition-colors">{{ $item->category }}</span>
                                    </td>
                                    <td class="px-12 py-10 text-right">
                                        <div class="text-xl font-display font-extrabold text-slate-900 italic tracking-tight leading-none mb-2 group-hover:-translate-x-1 transition-transform">${{ number_format($item->actual_amount ?? $item->estimated_amount, 2) }}</div>
                                        <div class="text-[9px] text-slate-400 font-bold flex items-center justify-end gap-3 uppercase tracking-widest">
                                            @if($item->actual_amount)
                                                <span class="text-emerald-500 italic">Confirmed Actual</span>
                                            @else
                                                <span class="italic">Estimated Projection</span>
                                            @endif
                                            @if($item->paid_amount > 0)
                                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                                <span class="text-indigo-600 italic shadow-indigo-100">${{ number_format($item->paid_amount, 2) }} realized</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-12 py-10 text-center">
                                        <span class="px-5 py-2 rounded-2xl text-[10px] font-bold uppercase tracking-[0.2em] border shadow-sm transition-all duration-700 group-hover:scale-105 inline-block
                                            {{ $item->status === 'paid' ? 'bg-emerald-50 text-emerald-600 border-emerald-100 shadow-emerald-50' : '' }}
                                            {{ $item->status === 'partially_paid' ? 'bg-amber-50 text-amber-600 border-amber-100 shadow-amber-50' : '' }}
                                            {{ $item->status === 'pending' ? 'bg-slate-50 text-slate-400 border-slate-100' : '' }}
                                            {{ $item->status === 'cancelled' ? 'bg-rose-50 text-rose-600 border-rose-100' : '' }}
                                        ">
                                            {{ str_replace('_', ' ', $item->status) }}
                                        </span>
                                    </td>
                                    @if($this->hasPermission('edit_event'))
                                        <td class="px-12 py-10 text-right">
                                            <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all duration-700 translate-x-6 group-hover:translate-x-0">
                                                <button wire:click="editBudgetItem({{ $item->id }})" class="w-12 h-12 rounded-xl bg-white border border-slate-100 hover:bg-indigo-600 hover:border-indigo-600 text-slate-400 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                                    <span class="material-symbols-outlined text-xl">edit_note</span>
                                                </button>
                                                <button wire:click="deleteBudgetItem({{ $item->id }})" wire:confirm="Are you certain you wish to purge this fiscal record?" class="w-12 h-12 rounded-xl bg-white border border-slate-100 hover:bg-rose-600 hover:border-rose-600 text-slate-400 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                                    <span class="material-symbols-outlined text-xl">delete</span>
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-12 py-32 text-center opacity-20 bg-slate-50/30">
                                        <div class="flex flex-col items-center">
                                            <div class="w-24 h-24 rounded-[3rem] bg-white shadow-lux flex items-center justify-center text-slate-200 mb-8">
                                                <span class="material-symbols-outlined text-6xl">receipt_long</span>
                                            </div>
                                            <p class="text-[13px] font-bold uppercase tracking-[0.4em] italic">The manuscript is void.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Item Modal -->
    @if($isAddingItem)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-8 overflow-y-auto">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xl transition-opacity animate-in fade-in duration-700" wire:click="resetForm"></div>

            <div class="relative w-full max-w-3xl bg-white rounded-[4rem] shadow-lux border border-slate-100 overflow-hidden animate-in zoom-in-95 duration-700">
                <div class="p-12 md:p-14 border-b border-slate-50 flex justify-between items-start">
                    <div class="space-y-2">
                        <h3 class="text-4xl font-display font-extrabold text-slate-900 tracking-tight uppercase italic leading-none mb-2">{{ $editingBudgetId ? 'Audit' : 'Initialize' }} Fiscal Entry</h3>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.3em] italic opacity-80">Strategic financial architecture interface.</p>
                    </div>
                    <button wire:click="resetForm" class="w-14 h-14 rounded-2xl bg-slate-50 hover:bg-indigo-50 flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-all hover:rotate-90">
                        <span class="material-symbols-outlined text-2xl font-bold">close</span>
                    </button>
                </div>

                <form wire:submit.prevent="saveBudgetItem" class="p-12 md:p-14 space-y-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-4">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] ml-2">Point Designation</label>
                            <input type="text" wire:model="item_name" class="w-full bg-slate-50 border-none ring-1 ring-slate-100 text-slate-900 text-base font-bold rounded-2xl focus:ring-2 focus:ring-indigo-600 p-6 transition-all shadow-inner italic outline-none" placeholder="e.g. Master Catering Orchestration">
                            @error('item_name') <span class="text-[10px] text-rose-500 font-bold uppercase tracking-widest ml-4 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] ml-2">Strategic Domain</label>
                            <select wire:model="category" class="w-full bg-slate-50 border-none ring-1 ring-slate-100 text-slate-900 text-base font-bold rounded-2xl focus:ring-2 focus:ring-indigo-600 p-6 transition-all shadow-inner italic appearance-none outline-none">
                                <option value="">Select Domain</option>
                                <option value="Venue">Venue Architecture</option>
                                <option value="Catering">Culinary Arts</option>
                                <option value="Entertainment">Performance</option>
                                <option value="Decor">Visual Aesthetic</option>
                                <option value="Logistics">Logistics Ops</option>
                                <option value="Marketing">External Presence</option>
                                <option value="Other">Miscellaneous</option>
                            </select>
                            @error('category') <span class="text-[10px] text-rose-500 font-bold uppercase tracking-widest ml-4 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] ml-2">Estimated Projection</label>
                            <div class="relative">
                                <span class="absolute left-6 top-1/2 -translate-y-1/2 text-indigo-600 font-bold italic text-lg">$</span>
                                <input type="number" step="0.01" wire:model="estimated_amount" class="w-full bg-slate-50 border-none ring-1 ring-slate-100 text-slate-900 text-base font-bold rounded-2xl focus:ring-2 focus:ring-indigo-600 pl-12 pr-6 py-6 transition-all shadow-inner italic outline-none" placeholder="0.00">
                            </div>
                            @error('estimated_amount') <span class="text-[10px] text-rose-500 font-bold uppercase tracking-widest ml-4 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] ml-2">Confirmed Realization</label>
                            <div class="relative">
                                <span class="absolute left-6 top-1/2 -translate-y-1/2 text-emerald-600 font-bold italic text-lg">$</span>
                                <input type="number" step="0.01" wire:model="actual_amount" class="w-full bg-slate-50 border-none ring-1 ring-slate-100 text-slate-900 text-base font-bold rounded-2xl focus:ring-2 focus:ring-emerald-500 pl-12 pr-6 py-6 transition-all shadow-inner italic outline-none" placeholder="0.00">
                            </div>
                            @error('actual_amount') <span class="text-[10px] text-rose-500 font-bold uppercase tracking-widest ml-4 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] ml-2">Liquidated Capital</label>
                            <div class="relative">
                                <span class="absolute left-6 top-1/2 -translate-y-1/2 text-amber-600 font-bold italic text-lg">$</span>
                                <input type="number" step="0.01" wire:model="paid_amount" class="w-full bg-slate-50 border-none ring-1 ring-slate-100 text-slate-900 text-base font-bold rounded-2xl focus:ring-2 focus:ring-amber-500 pl-12 pr-6 py-6 transition-all shadow-inner italic outline-none" placeholder="0.00">
                            </div>
                            @error('paid_amount') <span class="text-[10px] text-rose-500 font-bold uppercase tracking-widest ml-4 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] ml-2">Authentication Clearance</label>
                            <select wire:model="status" class="w-full bg-slate-50 border-none ring-1 ring-slate-100 text-slate-900 text-base font-bold rounded-2xl focus:ring-2 focus:ring-indigo-600 p-6 transition-all shadow-inner italic appearance-none outline-none">
                                <option value="pending">Awaiting Validation</option>
                                <option value="partially_paid">Partially Realized</option>
                                <option value="paid">Fully Authenticated</option>
                                <option value="cancelled">Retracted</option>
                            </select>
                            @error('status') <span class="text-[10px] text-rose-500 font-bold uppercase tracking-widest ml-4 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-end">
                        <div class="space-y-4">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] ml-2">Temporal Limit</label>
                            <input type="date" wire:model="due_date" class="w-full bg-slate-50 border-none ring-1 ring-slate-100 text-slate-900 text-base font-bold rounded-2xl focus:ring-2 focus:ring-indigo-600 p-6 transition-all shadow-inner outline-none">
                        </div>
                        <div class="pb-2">
                             <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] italic ml-4 leading-relaxed opacity-60">Validate all fiscal coordinates before final cryptographic commitment.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] ml-2">Narrative Context</label>
                        <textarea wire:model="notes" rows="4" class="w-full bg-slate-50 border-none ring-1 ring-slate-100 text-slate-900 text-base font-bold rounded-[2.5rem] focus:ring-2 focus:ring-indigo-600 p-8 transition-all shadow-inner italic outline-none" placeholder="Expound on the strategic expenditure rationale..."></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row-reverse gap-6 pt-12 border-t border-slate-50">
                        <button type="submit" class="btn-lux px-16 py-6 group shadow-indigo-100 bg-indigo-600 text-white rounded-[2rem] font-bold uppercase tracking-widest text-[11px]">
                            <div class="flex items-center justify-center gap-4">
                                <span>{{ $editingBudgetId ? 'Commit Analysis' : 'Verify Entry' }}</span>
                                <span class="material-symbols-outlined text-xl group-hover:scale-125 transition-all">encrypted</span>
                            </div>
                        </button>
                        <button type="button" wire:click="resetForm" class="px-12 py-6 bg-white border border-slate-100 text-slate-400 hover:text-rose-600 rounded-[2rem] transition-all font-bold text-[11px] uppercase tracking-widest hover:bg-rose-50">
                            Abandon
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
