<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Generate Short URL
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white rounded border">
                <div class="p-6">
                    <form method="POST" action="{{ route('short-urls.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="long_url" class="block text-sm font-medium text-gray-700 mb-2">
                                Long URL
                            </label>
                            <input type="url" 
                                   name="long_url" 
                                   id="long_url" 
                                   value="{{ old('long_url') }}"
                                   placeholder="e.g. https://sembark.com/travel-software/features/best-itinerary-builder"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('long_url') border-red-500 @enderror"
                                   required>
                            @error('long_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('short-urls.index') }}" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Generate
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
