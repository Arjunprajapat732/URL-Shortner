<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Generated Short URLs
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded border">
                <div class="p-4 border-b">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-lg">Short URLs</h3>
                        @if (!auth()->user()->isSuperAdmin())
                            <a href="{{ route('short-urls.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                                + Generate
                            </a>
                        @endif
                    </div>
                    <div class="flex items-center space-x-3">
                        <label class="text-sm text-gray-700">View and Download based on Date Interval:</label>
                        <form method="GET" action="{{ route('short-urls.index') }}" class="flex items-center space-x-2">
                            <select name="filter" id="filter" onchange="this.form.submit()" class="px-3 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="today" {{ ($filter ?? 'today') == 'today' ? 'selected' : '' }}>Today</option>
                                <option value="last_week" {{ ($filter ?? 'today') == 'last_week' ? 'selected' : '' }}>Last Week</option>
                                <option value="last_month" {{ ($filter ?? 'today') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                            </select>
                        </form>
                        <a href="{{ route('short-urls.download', ['filter' => $filter ?? 'today']) }}" class="bg-green-600 text-white px-4 py-1 rounded text-sm hover:bg-green-700">
                            Download
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Short URL</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Long URL</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Hits</th>
                                @if (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">User</th>
                                @endif
                                @if (auth()->user()->isSuperAdmin())
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Company</th>
                                @endif
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Created On</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shortUrls as $shortUrl)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm">
                                        <a href="{{ route('short-url.redirect', $shortUrl->short_code) }}" target="_blank" class="text-blue-600 hover:underline">
                                            {{ url('/s/' . $shortUrl->short_code) }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 truncate max-w-md">{{ $shortUrl->long_url }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $shortUrl->hits }}</td>
                                    @if (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $shortUrl->user->name }}</td>
                                    @endif
                                    @if (auth()->user()->isSuperAdmin())
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $shortUrl->company->name }}</td>
                                    @endif
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $shortUrl->created_at->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->isSuperAdmin() ? '6' : (auth()->user()->isAdmin() ? '5' : '4') }}" class="px-4 py-8 text-center text-gray-500">No short URLs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t">
                    {{ $shortUrls->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
