<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-100">
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('patch')

                    <div>
                        <label class="block text-sm">Name</label>
                        <input name="name" type="text" value="{{ old('name', $user->name) }}"
                            class="mt-1 block w-full bg-gray-900 border-gray-700 rounded" />
                        @error('name') <div class="text-red-400 text-sm">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm">Email</label>
                        <input name="email" type="email" value="{{ old('email', $user->email) }}"
                            class="mt-1 block w-full bg-gray-900 border-gray-700 rounded" />
                        @error('email') <div class="text-red-400 text-sm">{{ $message }}</div> @enderror
                    </div>

                    <div class="flex justify-end">
                        <button class="bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded">Save</button>
                    </div>
                </form>

                <hr class="my-6 border-gray-700" />

                <form method="POST" action="{{ route('profile.destroy') }}"
                    onsubmit="return confirm('Delete account? This cannot be undone.');">
                    @csrf
                    @method('delete')

                    <div class="mb-2">
                        <label class="block text-sm">Confirm Password</label>
                        <input name="password" type="password"
                            class="mt-1 block w-full bg-gray-900 border-gray-700 rounded" required />
                        @error('userDeletion.password') <div class="text-red-400 text-sm">{{ $message }}</div> @enderror
                    </div>

                    <button class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded">Delete Account</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>