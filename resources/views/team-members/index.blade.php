<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Team Members
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white rounded border">
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="font-semibold text-lg">Team Members</h3>
                    <a href="{{ route('invitations.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                        + Invite
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Name</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Email</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Role</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Total URLs</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Total Hits</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teamMembers as $member)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-center">{{ $member->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $member->email }}</td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <span class="px-2 py-1 rounded text-xs {{ $member->role === 'Admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $member->role }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center">{{ $member->short_urls_count }}</td>
                                    <td class="px-4 py-3 text-sm text-center">{{ $member->total_hits ?? 0 }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">No team members found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t">
                    {{ $teamMembers->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

