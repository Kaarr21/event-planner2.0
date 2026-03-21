<div class="max-w-7xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-[#1e293b]/50 dark:backdrop-blur-xl p-8 rounded-[2.5rem] border border-gray-100 dark:border-white/5 shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                    <span class="material-symbols-outlined">payments</span>
                </div>
                <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Budgeted</h3>
            </div>
            <p class="text-3xl font-black text-gray-900 dark:text-white">${{ number_format($totalEstimated, 2) }}</p>
        </div>

        <div class="bg-white dark:bg-[#1e293b]/50 dark:backdrop-blur-xl p-8 rounded-[2.5rem] border border-gray-100 dark:border-white/5 shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <span class="material-symbols-outlined">account_balance_wallet</span>
                </div>
                <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400">Actual Spent</h3>
            </div>
            <p class="text-3xl font-black text-gray-900 dark:text-white">${{ number_format($totalActual, 2) }}</p>
            @if($totalEstimated > 0)
                <div class="mt-4 w-full bg-gray-100 dark:bg-white/5 h-1.5 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 transition-all duration-1000" style="width: {{ min(100, ($totalActual / $totalEstimated) * 100) }}%"></div>
                </div>
            @endif
        </div>

        <div class="bg-white dark:bg-[#1e293b]/50 dark:backdrop-blur-xl p-8 rounded-[2.5rem] border border-gray-100 dark:border-white/5 shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500">
                    <span class="material-symbols-outlined">priority_high</span>
                </div>
                <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400">Remaining to Pay</h3>
            </div>
            <p class="text-3xl font-black text-gray-900 dark:text-white">${{ number_format($totalActual - $totalPaid, 2) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Category Breakdown -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-[#1e293b]/50 dark:backdrop-blur-xl p-8 rounded-[2.5rem] border border-gray-100 dark:border-white/5 shadow-sm">
                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter mb-8 text-center sm:text-left">Category Breakdown</h3>
                <div class="space-y-6">
                    @forelse($categories as $catName => $stats)
                        <div class="space-y-2">
                            <div class="flex justify-between items-end">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ $catName }}</span>
                                <span class="text-xs font-bold text-gray-900 dark:text-white">${{ number_format($stats['actual'], 0) }} / ${{ number_format($stats['estimated'], 0) }}</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-white/5 h-2 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-500" style="width: {{ $stats['estimated'] > 0 ? min(100, ($stats['actual'] / $stats['estimated']) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 text-center py-4">No categories yet.</p>
                    @endforelse
                </div>
            </div>

            @if($this->hasPermission('edit_event'))
                <button wire:click="$set('isAddingItem', true)" class="w-full p-6 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[2rem] shadow-xl shadow-indigo-500/20 transition-all hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-4 group">
                    <span class="material-symbols-outlined text-2xl group-hover:rotate-90 transition-transform">add</span>
                    <span class="text-xs font-black uppercase tracking-widest">Add Budget Item</span>
                </button>
            @endif
        </div>

        <!-- Budget Items List -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-[#1e293b]/50 dark:backdrop-blur-xl rounded-[2.5rem] border border-gray-100 dark:border-white/5 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-gray-100 dark:border-white/5 flex justify-between items-center">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">Budget Items</h3>
                    <span class="text-[10px] font-black bg-indigo-500/10 text-indigo-500 px-3 py-1 rounded-full uppercase tracking-widest">{{ $budgets->count() }} Items</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-white/5">
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Item</th>
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Category</th>
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Amount</th>
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Status</th>
                                @if($this->hasPermission('edit_event'))
                                    <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            @forelse($budgets as $item)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors group">
                                    <td class="px-8 py-6">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $item->item_name }}</div>
                                        @if($item->description)
                                            <div class="text-[10px] text-gray-500 mt-1 uppercase tracking-wider">{{ \Illuminate\Support\Str::limit($item->description, 40) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $item->category }}</span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-xs font-bold text-gray-900 dark:text-white">${{ number_format($item->actual_amount ?? $item->estimated_amount, 2) }}</div>
                                        <div class="text-[10px] text-gray-400 flex items-center gap-1">
                                            @if($item->actual_amount)
                                                <span class="text-emerald-500">Actual</span>
                                            @else
                                                <span>Est.</span>
                                            @endif
                                            @if($item->paid_amount > 0)
                                                <span class="mx-1">•</span>
                                                <span class="text-indigo-400">${{ number_format($item->paid_amount, 2) }} paid</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest border
                                            {{ $item->status === 'paid' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : '' }}
                                            {{ $item->status === 'partially_paid' ? 'bg-amber-100 text-amber-700 border-amber-200' : '' }}
                                            {{ $item->status === 'pending' ? 'bg-gray-100 text-gray-700 border-gray-200' : '' }}
                                            {{ $item->status === 'cancelled' ? 'bg-rose-100 text-rose-700 border-rose-200' : '' }}
                                        ">
                                            {{ str_replace('_', ' ', $item->status) }}
                                        </span>
                                    </td>
                                    @if($this->hasPermission('edit_event'))
                                        <td class="px-8 py-6 text-right">
                                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button wire:click="editBudgetItem({{ $item->id }})" class="p-2 text-gray-400 hover:text-indigo-500 transition-colors">
                                                    <span class="material-symbols-outlined text-lg">edit</span>
                                                </button>
                                                <button wire:click="deleteBudgetItem({{ $item->id }})" wire:confirm="Are you sure you want to delete this budget item?" class="p-2 text-gray-400 hover:text-rose-500 transition-colors">
                                                    <span class="material-symbols-outlined text-lg">delete</span>
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="material-symbols-outlined text-5xl text-gray-200 dark:text-white/10 mb-4">payments</span>
                                            <p class="text-gray-500 font-medium">No budget items yet. Start tracking your expenses!</p>
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
        <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="resetForm"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block align-middle bg-white dark:bg-[#1e293b] rounded-[2.5rem] text-left shadow-[0_40px_80px_-20px_rgba(0,0,0,0.2)] transform transition-all sm:my-8 sm:max-w-2xl sm:w-full border border-gray-100 dark:border-white/5 overflow-hidden">
                    <div class="p-8 border-b border-gray-100 dark:border-white/5 flex justify-between items-center">
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight uppercase">{{ $editingBudgetId ? 'Edit' : 'Add' }} Budget Item</h3>
                        <button wire:click="resetForm" class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-white/5 flex items-center justify-center text-gray-400 hover:text-gray-900 dark:hover:text-white transition-all">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveBudgetItem" class="p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Item Name</label>
                                <input type="text" wire:model="item_name" class="w-full bg-gray-50 dark:bg-white/5 border-0 ring-1 ring-gray-200 dark:ring-white/10 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-indigo-500 p-4 transition-all" placeholder="e.g. Venue Catering">
                                @error('item_name') <span class="text-[10px] text-rose-500 font-bold uppercase ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Category</label>
                                <select wire:model="category" class="w-full bg-gray-50 dark:bg-white/5 border-0 ring-1 ring-gray-200 dark:ring-white/10 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-indigo-500 p-4 transition-all appearance-none">
                                    <option value="">Select Category</option>
                                    <option value="Venue">Venue</option>
                                    <option value="Catering">Catering</option>
                                    <option value="Entertainment">Entertainment</option>
                                    <option value="Decor">Decor</option>
                                    <option value="Logistics">Logistics</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Other">Other</option>
                                </select>
                                @error('category') <span class="text-[10px] text-rose-500 font-bold uppercase ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Estimated Amount</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                                    <input type="number" step="0.01" wire:model="estimated_amount" class="w-full bg-gray-50 dark:bg-white/5 border-0 ring-1 ring-gray-200 dark:ring-white/10 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-indigo-500 pl-8 pr-4 py-4 transition-all" placeholder="0.00">
                                </div>
                                @error('estimated_amount') <span class="text-[10px] text-rose-500 font-bold uppercase ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Actual Amount</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                                    <input type="number" step="0.01" wire:model="actual_amount" class="w-full bg-gray-50 dark:bg-white/5 border-0 ring-1 ring-gray-200 dark:ring-white/10 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-indigo-500 pl-8 pr-4 py-4 transition-all" placeholder="0.00">
                                </div>
                                @error('actual_amount') <span class="text-[10px] text-rose-500 font-bold uppercase ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Paid Amount</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                                    <input type="number" step="0.01" wire:model="paid_amount" class="w-full bg-gray-50 dark:bg-white/5 border-0 ring-1 ring-gray-200 dark:ring-white/10 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-indigo-500 pl-8 pr-4 py-4 transition-all" placeholder="0.00">
                                </div>
                                @error('paid_amount') <span class="text-[10px] text-rose-500 font-bold uppercase ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Status</label>
                                <select wire:model="status" class="w-full bg-gray-50 dark:bg-white/5 border-0 ring-1 ring-gray-200 dark:ring-white/10 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-indigo-500 p-4 transition-all appearance-none">
                                    <option value="pending">Pending</option>
                                    <option value="partially_paid">Partially Paid</option>
                                    <option value="paid">Paid</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                @error('status') <span class="text-[10px] text-rose-500 font-bold uppercase ml-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Due Date</label>
                            <input type="date" wire:model="due_date" class="w-full bg-gray-50 dark:bg-white/5 border-0 ring-1 ring-gray-200 dark:ring-white/10 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-indigo-500 p-4 transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Notes / Description</label>
                            <textarea wire:model="notes" rows="3" class="w-full bg-gray-50 dark:bg-white/5 border-0 ring-1 ring-gray-200 dark:ring-white/10 text-gray-900 dark:text-white text-sm rounded-2xl focus:ring-2 focus:ring-indigo-500 p-4 transition-all" placeholder="Add any details or payment links..."></textarea>
                        </div>

                        <div class="flex flex-col sm:flex-row-reverse gap-3 pt-6 border-t border-gray-100 dark:border-white/5">
                            <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-indigo-500/20 transition-all active:scale-95">
                                {{ $editingBudgetId ? 'Update' : 'Save' }} Item
                            </button>
                            <button type="button" wire:click="resetForm" class="w-full sm:w-auto px-10 py-4 bg-gray-50 dark:bg-white/5 text-gray-500 dark:text-gray-400 text-xs font-black uppercase tracking-widest rounded-2xl transition-all">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
