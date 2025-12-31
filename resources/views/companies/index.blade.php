<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Companies
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white rounded border">
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="font-semibold text-lg">Companies</h3>
                    <a href="{{ route('invitations.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                        + Invite New Client
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b">
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Company Name</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Email</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Users</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Total URLs</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($companies as $company)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-center">{{ $company->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $company->email }}</td>
                                    <td class="px-4 py-3 text-sm text-center">{{ $company->users_count ?? 0 }}</td>
                                    <td class="px-4 py-3 text-sm text-center">{{ $company->short_urls_count ?? 0 }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">No companies found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t">
                    {{ $companies->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

