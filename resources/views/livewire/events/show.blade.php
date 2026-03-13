<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $event->title }}
        </h2>
        <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
            &larr; Back to Dashboard
        </a>
    </div>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column: Event Details & Tasks -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Event Details Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Event Details</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            {{ $event->description ?: 'No description provided.' }}
                        </p>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="flex items-center text-gray-500 dark:text-gray-500">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ \Carbon\Carbon::parse($event->date)->format('F j, Y @ H:i') }}
                            </div>
                            <div class="flex items-center text-gray-500 dark:text-gray-500">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $event->location ?: 'Online / TBD' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tasks Management Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tasks Checklist</h3>
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
                        </div>

                        <!-- Add Task Form -->
                        <form wire:submit.prevent="addTask" class="flex gap-2 mb-8">
                            <input type="text" wire:model="newTaskTitle" placeholder="What needs to be done?" class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500">
                            <input type="date" wire:model="newTaskDueDate" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Add
                            </button>
                        </form>

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
                                        <div class="space-y-3">
                                            <div class="flex gap-2">
                                                <input type="text" wire:model="editTaskTitle" class="flex-1 bg-white border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2 dark:bg-gray-800 dark:border-gray-700 dark:text-white" placeholder="Task title">
                                                <input type="date" wire:model="editTaskDueDate" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-indigo-500 focus:border-indigo-500 block p-2 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                                            </div>
                                            <textarea wire:model="editTaskDescription" rows="2" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-indigo-500 focus:border-indigo-500 block p-2 dark:bg-gray-800 dark:border-gray-700 dark:text-white" placeholder="Add details or notes..."></textarea>
                                            <div class="flex justify-end gap-2 pt-1">
                                                <button wire:click="cancelEditTask" class="px-3 py-1.5 text-xs font-semibold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Cancel</button>
                                                <button wire:click="saveTask" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Save</button>
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
                                                    @if ($task->due_date)
                                                        <p class="text-xs text-indigo-500 dark:text-indigo-400 mt-1 font-medium {{ $task->completed ? 'opacity-50' : '' }}">
                                                            <svg class="inline w-3.5 h-3.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                            {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
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
            </div>

            <!-- Right Column: RSVPs & Invite (Sidebar) -->
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
                <livewire:events.invite-form :event="$event" />
            </div>

        </div>
    </div>
</div>
