<x-app-layout>

    <x-slot name="header">
        <h2>Edit Customer</h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 text-green-700 bg-green-100 p-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 text-red-700 bg-red-100 p-3 rounded">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('customers.update', $customer->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block font-medium text-sm mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <div class="mb-4">
                    <label class="block font-medium text-sm mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <div class="mb-4">
                    <label class="block font-medium text-sm mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
                    <a href="{{ route('customers.index') }}" class="ml-3 text-sm text-gray-600 underline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>