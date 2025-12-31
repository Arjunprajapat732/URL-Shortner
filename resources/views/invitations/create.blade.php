<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if (auth()->user()->isSuperAdmin())
                Invite New Client
            @else
                Invite New Team Member
            @endif
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white rounded border">
                <div class="p-6">
                    <form method="POST" action="{{ route('invitations.store') }}">
                        @csrf

                        @if (auth()->user()->isSuperAdmin())
                            <div class="mb-4">
                                <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Company Name
                                </label>
                                <input type="text" 
                                       name="company_name" 
                                       id="company_name" 
                                       value="{{ old('company_name') }}"
                                       placeholder="Client Name..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('company_name') border-red-500 @enderror"
                                       required>
                                @error('company_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="company_email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Company Email
                                </label>
                                <input type="email" 
                                       name="company_email" 
                                       id="company_email" 
                                       value="{{ old('company_email') }}"
                                       placeholder="ex. sample@example.com"
                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('company_email') border-red-500 @enderror"
                                       required>
                                @error('company_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Name
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name') }}"
                                   placeholder="@if (auth()->user()->isSuperAdmin()) Client Name... @else User Name @endif"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email') }}"
                                   placeholder="ex. sample@example.com"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                                   required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        @if (auth()->user()->isAdmin())
                            <div class="mb-4">
                                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                                    Role
                                </label>
                                <select name="role" 
                                        id="role" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('role') border-red-500 @enderror"
                                        required>
                                    <option value="Member" {{ old('role') == 'Member' ? 'selected' : '' }}>Member</option>
                                    <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                @error('role')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                            <a href="{{ route('dashboard') }}" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm font-medium">
                                Cancel
                            </a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium shadow-sm">
                                Send Invitation
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
