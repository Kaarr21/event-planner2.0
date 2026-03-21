<div class="max-w-7xl mx-auto space-y-10 animate-in fade-in slide-in-from-bottom-6 duration-700">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="glass-card dark:glass-card-dark p-10 rounded-[3rem] border-none shadow-3xl group hover:-translate-y-1 transition-all duration-500">
            <div class="flex items-center gap-5 mb-6">
                <div class="w-14 h-14 rounded-2xl bg-brand-orange/10 flex items-center justify-center text-brand-orange shadow-lg shadow-brand-orange/5 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined font-black text-2xl">payments</span>
                </div>
                <div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 opacity-60">Allocation</h3>
                    <p class="text-xs font-black text-brand-orange uppercase tracking-widest mt-1 italic">Total Budgeted</p>
                </div>
            </div>
            <p class="text-4xl font-black text-gray-900 dark:text-white italic tracking-tighter">${{ number_format($totalEstimated, 2) }}</p>
        </div>

        <div class="glass-card dark:glass-card-dark p-10 rounded-[3rem] border-none shadow-3xl group hover:-translate-y-1 transition-all duration-500 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-5">
                <span class="material-symbols-outlined text-7xl">account_balance_wallet</span>
            </div>
            <div class="flex items-center gap-5 mb-6">
                <div class="w-14 h-14 rounded-2xl bg-brand-teal/10 flex items-center justify-center text-brand-teal shadow-lg shadow-brand-teal/5 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined font-black text-2xl">account_balance_wallet</span>
                </div>
                <div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 opacity-60">Burn Rate</h3>
                    <p class="text-xs font-black text-brand-teal uppercase tracking-widest mt-1 italic">Actual Spent</p>
                </div>
            </div>
            <p class="text-4xl font-black text-gray-900 dark:text-white italic tracking-tighter">${{ number_format($totalActual, 2) }}</p>
            @if($totalEstimated > 0)
                <div class="mt-6 w-full bg-white/10 h-2 rounded-full overflow-hidden border border-white/5">
                    <div class="h-full bg-brand-teal transition-all duration-1000 shadow-[0_0_15px_rgba(30,195,160,0.5)]" style="width: {{ min(100, ($totalActual / $totalEstimated) * 100) }}%"></div>
                </div>
                <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest mt-3 text-right italic">{{ round(($totalActual / $totalEstimated) * 100) }}% Used</p>
            @endif
        </div>

        <div class="glass-card dark:glass-card-dark p-10 rounded-[3rem] border-none shadow-3xl group hover:-translate-y-1 transition-all duration-500">
            <div class="flex items-center gap-5 mb-6">
                <div class="w-14 h-14 rounded-2xl bg-brand-yellow/10 flex items-center justify-center text-brand-yellow shadow-lg shadow-brand-yellow/5 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined font-black text-2xl">priority_high</span>
                </div>
                <div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 opacity-60">Liabilities</h3>
                    <p class="text-xs font-black text-brand-yellow uppercase tracking-widest mt-1 italic">Remaining</p>
                </div>
            </div>
            <p class="text-4xl font-black text-gray-900 dark:text-white italic tracking-tighter">${{ number_format($totalActual - $totalPaid, 2) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Category Breakdown -->
        <div class="space-y-8">
            <div class="glass-card dark:glass-card-dark p-10 rounded-[3rem] border-none shadow-3xl">
                <h3 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter mb-10 italic">Analysis</h3>
                <div class="space-y-8">
                    @forelse($categories as $catName => $stats)
                        <div class="space-y-3 group">
                            <div class="flex justify-between items-end">
                                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 group-hover:text-brand-orange transition-colors">{{ $catName }}</span>
                                <span class="text-xs font-black text-gray-900 dark:text-white italic tracking-tight">${{ number_format($stats['actual'], 0) }} / <span class="opacity-40">${{ number_format($stats['estimated'], 0) }}</span></span>
                            </div>
                            <div class="w-full bg-white/5 h-2.5 rounded-full overflow-hidden border border-white/5 relative">
                                <div class="h-full bg-gradient-to-r from-brand-orange to-brand-yellow group-hover:from-brand-teal group-hover:to-brand-teal transition-all duration-700" style="width: {{ $stats['estimated'] > 0 ? min(100, ($stats['actual'] / $stats['estimated']) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 opacity-40">
                            <span class="material-symbols-outlined text-4xl mb-2">query_stats</span>
                            <p class="text-[10px] font-black uppercase tracking-widest">No data mapped.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            @if($this->hasPermission('edit_event'))
                <button wire:click="$set('isAddingItem', true)" class="w-full btn-brand pamoja-gradient p-8 rounded-[2.5rem] flex items-center justify-center gap-4 group">
                    <span class="material-symbols-outlined text-2xl font-black group-hover:rotate-90 transition-transform">add_circle</span>
                    <span class="text-xs font-black uppercase tracking-[0.2em]">Add Budget Item</span>
                </button>
            @endif
        </div>

        <!-- Budget Items List -->
        <div class="lg:col-span-2">
            <div class="glass-card dark:glass-card-dark rounded-[3.5rem] border-none shadow-3xl overflow-hidden">
                <div class="p-10 border-b border-white/5 flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter italic leading-none mb-1">Fiscal Record</h3>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest opacity-60">Verified expenditure data.</p>
                    </div>
                    <span class="text-[10px] font-black bg-brand-teal/10 text-brand-teal border border-brand-teal/20 px-5 py-2 rounded-full uppercase tracking-widest shadow-sm">{{ $budgets->count() }} Line Items</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white/5">
                                <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.3em] text-gray-400">Position</th>
                                <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.3em] text-gray-400">Department</th>
                                <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.3em] text-gray-400">Capital</th>
                                <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 text-center">Clearance</th>
                                @if($this->hasPermission('edit_event'))
                                    <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 text-right">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($budgets as $item)
                                <tr class="hover:bg-brand-orange/5 transition-all duration-500 group">
                                    <td class="px-10 py-8">
                                        <div class="font-black text-gray-900 dark:text-white uppercase tracking-tighter italic text-base leading-none mb-1 group-hover:text-brand-orange transition-colors">{{ $item->item_name }}</div>
                                        @if($item->description)
                                            <div class="text-[9px] text-gray-500 font-bold uppercase tracking-widest italic opacity-60">{{ \Illuminate\Support\Str::limit($item->description, 60) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-10 py-8">
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest group-hover:text-gray-300 transition-colors">{{ $item->category }}</span>
                                    </td>
                                    <td class="px-10 py-8">
                                        <div class="text-base font-black text-gray-900 dark:text-white italic tracking-tighter leading-none mb-1 group-hover:translate-x-1 transition-transform">${{ number_format($item->actual_amount ?? $item->estimated_amount, 2) }}</div>
                                        <div class="text-[8px] text-gray-500 font-black flex items-center gap-2 uppercase tracking-widest">
                                            @if($item->actual_amount)
                                                <span class="text-brand-teal italic">Confirmed Actual</span>
                                            @else
                                                <span class="italic">Estimated Projection</span>
                                            @endif
                                            @if($item->paid_amount > 0)
                                                <span class="h-1 w-1 rounded-full bg-gray-600"></span>
                                                <span class="text-brand-orange italic">${{ number_format($item->paid_amount, 2) }} realized</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-10 py-8 text-center">
                                        <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-[0.2em] border shadow-sm transition-all duration-500 group-hover:scale-105
                                            {{ $item->status === 'paid' ? 'bg-brand-teal/10 text-brand-teal border-brand-teal' : '' }}
                                            {{ $item->status === 'partially_paid' ? 'bg-brand-yellow/10 text-brand-yellow border-brand-yellow' : '' }}
                                            {{ $item->status === 'pending' ? 'bg-white/5 text-gray-500 border-white/10' : '' }}
                                            {{ $item->status === 'cancelled' ? 'bg-brand-red/10 text-brand-red border-brand-red' : '' }}
                                        ">
                                            {{ str_replace('_', ' ', $item->status) }}
                                        </span>
                                    </td>
                                    @if($this->hasPermission('edit_event'))
                                        <td class="px-10 py-8 text-right">
                                            <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-all duration-500 translate-x-4 group-hover:translate-x-0">
                                                <button wire:click="editBudgetItem({{ $item->id }})" class="w-10 h-10 rounded-xl bg-white/5 hover:bg-brand-orange text-gray-500 hover:text-white transition-all flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-lg">edit</span>
                                                </button>
                                                <button wire:click="deleteBudgetItem({{ $item->id }})" wire:confirm="Are you sure you want to delete this budget item?" class="w-10 h-10 rounded-xl bg-white/5 hover:bg-brand-red text-gray-500 hover:text-white transition-all flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-lg">delete</span>
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-10 py-32 text-center opacity-40">
                                        <div class="flex flex-col items-center">
                                            <span class="material-symbols-outlined text-7xl text-brand-orange mb-6">receipt_long</span>
                                            <p class="text-[10px] font-black uppercase tracking-[0.3em] italic">The fiscal record is empty.</p>
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
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-6 backdrop-blur-xl animate-in fade-in duration-500">
            <div class="absolute inset-0 bg-black/40 transition-opacity" wire:click="resetForm"></div>

            <div class="relative w-full max-w-3xl glass-card dark:glass-card-dark rounded-[3.5rem] border-none shadow-4xl transform transition-all overflow-hidden animate-in zoom-in-95 duration-500">
                <div class="p-10 border-b border-white/5 flex justify-between items-start">
                    <div>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter uppercase italic leading-none mb-2">{{ $editingBudgetId ? 'Edit' : 'Deploy' }} Budget Entry</h3>
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest italic opacity-60">Strategic financial management interface.</p>
                    </div>
                    <button wire:click="resetForm" class="w-12 h-12 rounded-2xl bg-white/5 hover:bg-white/10 flex items-center justify-center text-gray-400 hover:text-gray-900 dark:hover:text-white transition-all">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form wire:submit.prevent="saveBudgetItem" class="p-10 space-y-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] ml-2">Item Designation</label>
                            <input type="text" wire:model="item_name" class="w-full bg-white dark:bg-gray-800/50 border-0 ring-1 ring-white/10 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-brand-orange p-5 transition-all font-bold italic" placeholder="e.g. Master Catering">
                            @error('item_name') <span class="text-[10px] text-brand-red font-black uppercase tracking-widest ml-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] ml-2">Strategic Category</label>
                            <select wire:model="category" class="w-full bg-white dark:bg-gray-800/50 border-0 ring-1 ring-white/10 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-brand-orange p-5 transition-all appearance-none font-bold italic">
                                <option value="">Select Sphere</option>
                                <option value="Venue">Venue</option>
                                <option value="Catering">Catering</option>
                                <option value="Entertainment">Entertainment</option>
                                <option value="Decor">Decor</option>
                                <option value="Logistics">Logistics</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Other">Other</option>
                            </select>
                            @error('category') <span class="text-[10px] text-brand-red font-black uppercase tracking-widest ml-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] ml-2">Projection (Est.)</label>
                            <div class="relative">
                                <span class="absolute left-5 top-1/2 -translate-y-1/2 text-brand-orange font-black italic">$</span>
                                <input type="number" step="0.01" wire:model="estimated_amount" class="w-full bg-white dark:bg-gray-800/50 border-0 ring-1 ring-white/10 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-brand-orange pl-10 pr-5 py-5 transition-all font-black italic" placeholder="0.00">
                            </div>
                            @error('estimated_amount') <span class="text-[10px] text-brand-red font-black uppercase tracking-widest ml-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] ml-2">Actual (Confirmed)</label>
                            <div class="relative">
                                <span class="absolute left-5 top-1/2 -translate-y-1/2 text-brand-teal font-black italic">$</span>
                                <input type="number" step="0.01" wire:model="actual_amount" class="w-full bg-white dark:bg-gray-800/50 border-0 ring-1 ring-white/10 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-brand-teal pl-10 pr-5 py-5 transition-all font-black italic" placeholder="0.00">
                            </div>
                            @error('actual_amount') <span class="text-[10px] text-brand-red font-black uppercase tracking-widest ml-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] ml-2">Paid Capital</label>
                            <div class="relative">
                                <span class="absolute left-5 top-1/2 -translate-y-1/2 text-brand-yellow font-black italic">$</span>
                                <input type="number" step="0.01" wire:model="paid_amount" class="w-full bg-white dark:bg-gray-800/50 border-0 ring-1 ring-white/10 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-brand-yellow pl-10 pr-5 py-5 transition-all font-black italic" placeholder="0.00">
                            </div>
                            @error('paid_amount') <span class="text-[10px] text-brand-red font-black uppercase tracking-widest ml-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] ml-2">Status clearance</label>
                            <select wire:model="status" class="w-full bg-white dark:bg-gray-800/50 border-0 ring-1 ring-white/10 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-brand-orange p-5 transition-all appearance-none font-bold italic">
                                <option value="pending">Pending</option>
                                <option value="partially_paid">Partially Paid</option>
                                <option value="paid">Paid</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            @error('status') <span class="text-[10px] text-brand-red font-black uppercase tracking-widest ml-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-end">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] ml-2">Deadline</label>
                            <input type="date" wire:model="due_date" class="w-full bg-white dark:bg-gray-800/50 border-0 ring-1 ring-white/10 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-brand-orange p-5 transition-all font-bold">
                        </div>
                        <div class="pb-1">
                             <p class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] italic ml-2">Ensure all fiscal points are validated before commitment.</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] ml-2">Strategy Notes</label>
                        <textarea wire:model="notes" rows="3" class="w-full bg-white dark:bg-gray-800/50 border-0 ring-1 ring-white/10 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-brand-orange p-5 transition-all font-medium italic" placeholder="Detail the expenditure rationale..."></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row-reverse gap-4 pt-10 border-t border-white/5">
                        <button type="submit" class="btn-brand pamoja-gradient px-12 py-5 group">
                            {{ $editingBudgetId ? 'Commit Changes' : 'Deploy Entry' }}
                            <span class="material-symbols-outlined text-lg ml-2 group-hover:translate-x-1 transition-transform">verified</span>
                        </button>
                        <button type="button" wire:click="resetForm" class="btn-secondary border-none bg-white/5 hover:bg-white/10 text-gray-400 px-10 py-5 rounded-2xl transition-all font-black text-[10px] uppercase tracking-widest">
                            Abort
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
