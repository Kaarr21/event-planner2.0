<x-slot name="header">
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $event->title }}
            </h2>
            
            @if($userRole !== 'invited')
                <!-- Role Badge -->
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm
                    {{ $userRole === 'owner' ? 'bg-amber-100 text-amber-700 border border-amber-200' : '' }}
                    {{ $userRole === 'organizer' ? 'bg-indigo-100 text-indigo-700 border border-indigo-200' : '' }}
                    {{ $userRole === 'guest' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : '' }}
                ">
                    {{ $userRole }}
                </span>
            @endif
        </div>
        <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
            &larr; Back to Dashboard
        </a>
    </div>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if($userRole === 'invited')
    <div class="mb-10 p-8 bg-gradient-to-br from-indigo-600/10 via-white/5 to-emerald-600/10 backdrop-blur-3xl border border-white/20 rounded-[3rem] shadow-2xl animate-in fade-in zoom-in duration-700 relative overflow-hidden">
        <!-- Decorative background glow -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-indigo-500/20 rounded-full blur-[80px]"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-emerald-500/20 rounded-full blur-[80px]"></div>

        <div class="relative flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <div class="w-20 h-20 rounded-[2rem] bg-indigo-600 flex items-center justify-center border border-white/20 shadow-2xl shadow-indigo-500/40 transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                    <span class="material-symbols-outlined text-4xl text-white">mail</span>
                </div>
                <div class="text-center md:text-left">
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none">You're Invited!</h3>
                    <p class="text-gray-500 dark:text-gray-400 mt-3 font-medium text-lg">
                        <span class="text-indigo-500 font-black">{{ $inviter?->name ?? 'Someone' }}</span> 
                        <span class="opacity-50 font-normal">({{ $inviter?->email ?? 'unknown' }})</span>
                        has invited you to join this event team.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-4 w-full md:w-auto">
                <button wire:click="updateRSVP('attending')" class="flex-1 md:flex-none px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-2xl shadow-indigo-500/40 transition-all hover:scale-105 active:scale-95 group">
                    Accept Invitation
                    <span class="material-symbols-outlined text-sm ml-2 group-hover:translate-x-1 transition-transform">celebration</span>
                </button>
                <button wire:click="updateRSVP('declined')" class="px-8 py-4 bg-white/5 hover:bg-white/10 text-gray-600 dark:text-white text-xs font-black uppercase tracking-widest rounded-2xl border border-white/10 transition-all active:scale-95">
                    Decline
                </button>
            </div>
        </div>
    </div>
@endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 md:gap-12">
            
            <!-- Left Column: Event Details & Tasks -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Event Details Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8">
                        <div class="flex items-center gap-4 text-gray-500 dark:text-gray-400 mb-6">
                            <span class="material-symbols-outlined text-indigo-500">info</span>
                            <h3 class="text-xs font-black uppercase tracking-widest">Event Description</h3>
                        </div>
                        <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed italic">
                            {{ $event->description ?: 'No description provided for this event.' }}
                        </p>
                        
                        @if($userRole !== 'invited')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-10 pt-10 border-t border-gray-100 dark:border-white/5">
                                <div class="flex items-center gap-4 group">
                                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined">calendar_month</span>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">When</p>
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($event->date)->format('F d, Y') }}</p>
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($event->date)->format('h:i A') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 group">
                                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined">location_on</span>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Where</p>
                                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate max-w-[200px]">{{ $event->location ?: 'Online / TBD' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if($userRole !== 'invited')
                    <!-- Tasks Management Card -->
                    <div class="bg-white dark:bg-[#1e293b]/50 dark:backdrop-blur-xl overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100 dark:border-white/5">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tasks Checklist</h3>
                                @if($this->hasPermission('manage_tasks'))
                                <div class="flex items-center gap-4">
                                    <button type="button" wire:click="suggestAITasks" class="text-xs text-indigo-600 hover:text-indigo-900 font-semibold flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z" clip-rule="evenodd" />
                                        </svg>
                                        Suggest with AI
                                    </button>
                                    @if (session()->has('task_message'))
                                        <span class="text-sm text-green-600">{{ session('task_message') }}</span>
                                    @endif
                                </div>
                                @endif
                            </div>

                            @if($this->hasPermission('manage_tasks'))
                            <!-- Add Task Form -->
                            <form wire:submit.prevent="addTask" class="flex flex-col gap-3 mb-8 bg-gray-50 dark:bg-gray-700/30 p-4 rounded-xl border border-gray-100 dark:border-gray-600">
                                <div class="flex gap-2">
                                    <input type="text" wire:model="newTaskTitle" placeholder="What needs to be done?" class="flex-1 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                    <input type="date" wire:model="newTaskDueDate" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-xs">
                                </div>
                                <div class="flex gap-2 items-center">
                                    <div class="flex-1 flex items-center gap-2">
                                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assign to:</span>
                                        <select wire:model="newTaskAssignedTo" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="">Unassigned</option>
                                            @foreach($eligibleAssignees as $assignee)
                                                <option value="{{ $assignee->id }}">{{ $assignee->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition ease-in-out duration-150">
                                        Add Task
                                    </button>
                                </div>
                            </form>
                            @endif

                            <!-- AI Suggestions Section -->
                            @if (!empty($aiSuggestions))
                                <div class="mb-8 p-4 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-lg">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-sm font-bold text-indigo-900 dark:text-indigo-300 flex items-center">
                                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z" clip-rule="evenodd" />
                                            </svg>
                                            AI Suggested Tasks
                                        </h4>
                                        <button type="button" wire:click="suggestAITasks" class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 flex items-center bg-white dark:bg-gray-800 px-2.5 py-1 rounded-md border border-indigo-200 dark:border-indigo-700 shadow-sm transition-colors">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            Refresh AI Suggestions
                                        </button>
                                    </div>
                                    <div class="space-y-3 mb-4">
                                        @foreach($aiSuggestions as $index => $suggestion)
                                            <div class="flex items-start gap-3 bg-white dark:bg-gray-800 p-3 rounded-md border border-indigo-50 dark:border-gray-700 shadow-sm">
                                                <div class="pt-1">
                                                    <input type="checkbox" wire:model.live="aiSuggestions.{{ $index }}.selected" class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-700 dark:border-gray-600">
                                                </div>
                                                <div class="flex-1">
                                                    <input type="text" wire:model="aiSuggestions.{{ $index }}.title" class="w-full bg-transparent border-0 border-b border-transparent hover:border-indigo-200 focus:border-indigo-500 focus:ring-0 text-sm text-gray-900 dark:text-white p-0 py-1 transition-colors" {{ !$suggestion['selected'] ? 'disabled' : '' }}>
                                                </div>
                                                <button wire:click="removeSuggestion({{ $index }})" class="text-gray-400 hover:text-red-500 transition-colors p-1" title="Remove suggestion">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="flex justify-end pt-2 border-t border-indigo-100 dark:border-indigo-800/50">
                                        <button wire:click="saveSuggestions" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Save Selected
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <!-- Tasks List -->
                            <ul class="space-y-4">
                                @forelse ($tasks as $task)
                                    <li class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg group">
                                        @if($editingTaskId === $task->id)
                                            <div class="space-y-3 bg-white dark:bg-gray-800 p-4 rounded-lg border border-indigo-200 dark:border-indigo-800 shadow-sm transition-all">
                                                <div class="flex gap-2">
                                                    <input type="text" wire:model="editTaskTitle" class="flex-1 bg-white border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2 dark:bg-gray-800 dark:border-gray-700 dark:text-white" placeholder="Task title">
                                                    <input type="date" wire:model="editTaskDueDate" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-indigo-500 focus:border-indigo-500 block p-2 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                                                </div>
                                                <textarea wire:model="editTaskDescription" rows="2" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-indigo-500 focus:border-indigo-500 block p-2 dark:bg-gray-800 dark:border-gray-700 dark:text-white" placeholder="Add details or notes..."></textarea>
                                                
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assign to:</span>
                                                    <select wire:model="editTaskAssignedTo" class="bg-white border border-gray-300 text-gray-900 text-xs rounded-md focus:ring-indigo-500 focus:border-indigo-500 block p-1 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                                                        <option value="">Unassigned</option>
                                                        @foreach($eligibleAssignees as $assignee)
                                                            <option value="{{ $assignee->id }}">{{ $assignee->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="flex justify-end gap-2 pt-1">
                                                    <button wire:click="cancelEditTask" class="px-3 py-1.5 text-xs font-semibold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Cancel</button>
                                                    <button wire:click="saveTask" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-indigo-700 transition ease-in-out duration-150 shadow-sm shadow-indigo-200 dark:shadow-none">Save Changes</button>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-start gap-4">
                                                    <div class="pt-0.5">
                                                        <input type="checkbox" wire:click="toggleTask({{ $task->id }})" {{ $task->completed ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium {{ $task->completed ? 'line-through text-gray-400 dark:text-gray-500' : 'text-gray-900 dark:text-white' }}">
                                                            {{ $task->title }}
                                                        </p>
                                                        @if ($task->description)
                                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 {{ $task->completed ? 'opacity-50' : '' }}">{{ $task->description }}</p>
                                                        @endif
                                                        
                                                        <div class="flex flex-wrap items-center gap-3 mt-2">
                                                            @if ($task->due_date)
                                                                <p class="text-[10px] text-indigo-500 dark:text-indigo-400 font-bold uppercase flex items-center gap-1 {{ $task->completed ? 'opacity-50' : '' }}">
                                                                    <span class="material-symbols-outlined text-sm">calendar_today</span>
                                                                    {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                                                                </p>
                                                            @endif

                                                            @if($task->assignee)
                                                                <div class="flex items-center gap-1.5">
                                                                    <div class="h-5 w-5 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold text-[10px]">
                                                                        {{ substr($task->assignee->name, 0, 1) }}
                                                                    </div>
                                                                    <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-tight">{{ $task->assignee->name }}</span>
                                                                    
                                                                    <!-- Status Badge -->
                                                                    <span class="text-[8px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider
                                                                        {{ $task->assignment_status === 'pending' ? 'bg-amber-50 text-amber-600 border border-amber-100' : '' }}
                                                                        {{ $task->assignment_status === 'accepted' ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : '' }}
                                                                        {{ $task->assignment_status === 'declined' ? 'bg-rose-50 text-rose-600 border border-rose-100' : '' }}
                                                                        {{ $task->assignment_status === 'completed' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : '' }}
                                                                    ">
                                                                        {{ $task->assignment_status }}
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <!-- Completion Comment -->
                                                        @if($task->completion_comment)
                                                            <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-800/40 rounded-xl border border-gray-100 dark:border-gray-700 italic text-[11px] text-gray-500 dark:text-gray-400 flex items-start gap-2">
                                                                <span class="material-symbols-outlined text-gray-300 text-sm">chat_bubble</span>
                                                                "{{ $task->completion_comment }}"
                                                            </div>
                                                        @endif

                                                        <!-- Interaction for Assigned User -->
                                                        @if($task->assigned_to === auth()->id() && !$task->completed)
                                                            <div class="mt-4 flex flex-wrap items-center gap-3">
                                                                @if($task->assignment_status === 'pending')
                                                                    <button wire:click="acceptTask({{ $task->id }})" class="bg-[#257bf4] hover:bg-[#257bf4]/90 text-white text-[10px] font-black px-4 py-1.5 rounded-lg uppercase tracking-widest transition-all shadow-md shadow-blue-500/10 active:scale-95">Accept Task</button>
                                                                    <button wire:click="declineTask({{ $task->id }})" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-600 dark:text-gray-300 text-[10px] font-black px-4 py-1.5 rounded-lg uppercase tracking-widest transition-all active:scale-95">Decline</button>
                                                                @elseif($task->assignment_status === 'accepted')
                                                                    @if($completingTaskId === $task->id)
                                                                        <div class="w-full space-y-3 mt-2 bg-white dark:bg-gray-800/50 p-3 rounded-xl border border-indigo-50 dark:border-indigo-900/30">
                                                                            <textarea wire:model="completionComment" placeholder="Add a comment on completion (optional)..." class="w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-xs rounded-lg p-2.5 focus:ring-indigo-500"></textarea>
                                                                            <div class="flex gap-3">
                                                                                <button wire:click="completeTask" class="bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-black px-4 py-2 rounded-lg uppercase tracking-widest shadow-md shadow-emerald-500/10 active:scale-95">Submit Completion</button>
                                                                                <button wire:click="$set('completingTaskId', null)" class="text-xs text-gray-500 underline decoration-dotted underline-offset-4">Cancel</button>
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <button wire:click="startCompletion({{ $task->id }})" class="text-[#257bf4] dark:text-blue-400 hover:text-[#257bf4]/80 text-[10px] font-black px-2 py-1 rounded-md uppercase tracking-widest flex items-center gap-1.5 transition-all group/btn">
                                                                            <span class="material-symbols-outlined text-[16px] group-hover/btn:scale-110 transition-transform">check_circle</span>
                                                                            Mark as Complete
                                                                        </button>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2 {{ !$this->hasPermission('manage_tasks') ? 'hidden' : 'opacity-0 group-hover:opacity-100' }} transition-opacity">
                                                    <button wire:click="startEditTask({{ $task->id }})" class="text-gray-400 hover:text-indigo-500 p-1">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                    </button>
                                                    <button wire:click="deleteTask({{ $task->id }})" class="text-gray-400 hover:text-red-500 p-1">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </li>
                                @empty
                                    <div class="text-center py-8">
                                        <p class="text-gray-500 text-sm">No tasks added yet. Plan your event by adding some tasks!</p>
                                    </div>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <!-- Media Library Section -->
                    <div class="mt-8">
                        <livewire:events.media-library :event="$event" :user-permissions="$userPermissions" :user-role="$userRole" />
                    </div>
                @endif
            </div>

            <!-- Right Column: RSVPs & Invite (Sidebar) -->
            @if($userRole !== 'invited')
                <div class="space-y-8">
                    
                    <!-- RSVP Status Card -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Your Attendance</h3>
                            
                            <div class="space-y-4">
                                <div class="grid grid-cols-3 gap-2">
                                    <button wire:click="updateRSVP('attending')" class="flex flex-col items-center p-3 rounded-lg border {{ $userRSVP?->status === 'attending' ? 'bg-green-50 border-green-200 text-green-700 shadow-sm' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                                        <span class="text-xs font-bold uppercase">Attending</span>
                                    </button>
                                    <button wire:click="updateRSVP('maybe')" class="flex flex-col items-center p-3 rounded-lg border {{ $userRSVP?->status === 'maybe' ? 'bg-amber-50 border-amber-200 text-amber-700 shadow-sm' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                                        <span class="text-xs font-bold uppercase">Maybe</span>
                                    </button>
                                    <button wire:click="updateRSVP('declined')" class="flex flex-col items-center p-3 rounded-lg border {{ $userRSVP?->status === 'declined' ? 'bg-red-50 border-red-200 text-red-700 shadow-sm' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                                        <span class="text-xs font-bold uppercase">Declined</span>
                                    </button>
                                </div>
                                
                                @if (session()->has('rsvp_message'))
                                    <p class="text-xs text-center text-green-600">{{ session('rsvp_message') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- RSVPs List Card -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Guest List</h3>
                            <div class="space-y-4">
                                @forelse ($rsvps as $rsvp)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                                {{ substr($rsvp->user->name, 0, 1) }}
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $rsvp->user->name }}</span>
                                        </div>
                                        <span class="text-xs px-2 py-0.5 rounded-full 
                                            {{ $rsvp->status === 'attending' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $rsvp->status === 'maybe' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ $rsvp->status === 'declined' ? 'bg-red-100 text-red-800' : '' }}
                                        ">
                                            {{ ucfirst($rsvp->status) }}
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 text-center py-4">No guests have RSVPed yet.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Invite Form -->
                    @if($this->hasPermission('manage_invites'))
                        <livewire:events.invite-form :event="$event" />
                    @endif

                    <!-- Event Management (Owner/Organizers) -->
                    @if($this->hasPermission('manage_invites') || $this->hasPermission('owner'))
                        <div class="pt-8 mt-8 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Management</h4>
                            <div class="space-y-8">
                                @if($this->hasPermission('manage_invites'))
                                    <livewire:events.invited-list :event="$event" />
                                @endif

                                @if($this->hasPermission('owner'))
                                    <livewire:events.manage-organizers :event="$event" />
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
</div>
