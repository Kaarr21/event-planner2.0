<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $organization->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 flex flex-col items-center">
                    @if ($organization->logo_path)
                        <img src="{{ Storage::url($organization->logo_path) }}" alt="{{ $organization->name }} Logo" class="h-32 w-32 object-cover rounded-full mb-4">
                    @endif
                    <h1 class="text-3xl font-bold">{{ $organization->name }}</h1>
                    <p class="text-gray-500 mb-2">{{ $organization->type }}</p>

                    @if ($organization->website_url)
                        <a href="{{ $organization->website_url }}" target="_blank" class="text-indigo-600 hover:underline mb-4">{{ $organization->website_url }}</a>
                    @endif

                    @if ($organization->bio)
                        <div class="mt-4 max-w-2xl text-center">
                            <p>{{ $organization->bio }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
