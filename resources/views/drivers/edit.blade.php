<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Driver
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('drivers.update', $driver->id) }}" class="rounded-lg bg-white p-6 shadow-sm">
                @csrf
                @method('PUT')

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="driver-name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input
                            id="driver-name"
                            type="text"
                            name="name"
                            value="{{ old('name', $driver->name) }}"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <div>
                        <label for="driver-email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input
                            id="driver-email"
                            type="email"
                            name="email"
                            value="{{ old('email', $driver->email) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <div>
                        <label for="driver-phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input
                            id="driver-phone"
                            type="text"
                            name="phone"
                            value="{{ old('phone', $driver->phone) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <div>
                        <label for="driver-gst-number" class="block text-sm font-medium text-gray-700">GST number</label>
                        <input
                            id="driver-gst-number"
                            type="text"
                            name="gst_number"
                            value="{{ old('gst_number', $driver->gst_number) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <div class="sm:col-span-2">
                        <label for="driver-address" class="block text-sm font-medium text-gray-700">Address</label>
                        <textarea
                            id="driver-address"
                            name="address"
                            rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >{{ old('address', $driver->address) }}</textarea>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <a
                        href="{{ route('drivers.index') }}"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Update driver
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
