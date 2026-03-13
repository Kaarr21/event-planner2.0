<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Notifications') }}
        </h2>
        @if($unreadCount > 0)
            <button wire:click="markAllAsRead" class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                Mark all as read
            </button>
        @endif
    </div>
</x-slot>

<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="space-y-6">
                    @forelse ($notifications as $notification)
                        <div class="flex items-start gap-4 p-4 rounded-lg {{ $notification->read ? 'bg-white dark:bg-gray-800 opacity-75' : 'bg-indigo-50 dark:bg-indigo-900/20 border-l-4 border-indigo-500 shadow-sm' }}">
                            <div class="flex-shrink-0 mt-1">
                                @if($notification->type === 'invite')
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @else
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $notification->title }}</h4>
                                    <span class="text-xs text-gray-500">{{ $notification->created_at?->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $notification->message }}
                                </p>
                                
                                <div class="mt-4 flex gap-4">
                                    @if($notification->type === 'invite' && !$notification->read)
                                        <button wire:click="acceptInvite({{ $notification->id }})" class="inline-flex items-center px-3 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Accept
                                        </button>
                                        <button wire:click="declineInvite({{ $notification->id }})" class="inline-flex items-center px-3 py-1 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                            Decline
                                        </button>
                                    @endif

                                    @if(!$notification->read)
                                        <button wire:click="markAsRead({{ $notification->id }})" class="text-xs font-semibold text-gray-500 hover:text-gray-900 uppercase tracking-wider">
                                            Mark as read
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">All clear!</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You don't have any new notifications.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
