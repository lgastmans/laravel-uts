<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Customers') }}
        </h2>
    </x-slot>

    <div
        class="py-4"
        x-data
        x-on:open-customer-create-modal.window="$dispatch('open-modal', 'add-customer-modal')"
    >
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <livewire:customer-table />
        </div>
    </div>

    <x-modal name="add-customer-modal" :show="$errors->any()" max-width="2xl" focusable>
        <form method="POST" action="{{ route('customers.store') }}" class="p-6">
            @csrf

            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Add customer</h3>
                    <p class="mt-1 text-sm text-gray-600">Create a customer without leaving this page.</p>
                </div>

                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', 'add-customer-modal')"
                    class="rounded-md p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                    aria-label="Close add customer window"
                >
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            @if ($errors->any())
                <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="customer-name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input
                        id="customer-name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label for="customer-email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input
                        id="customer-email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label for="customer-phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input
                        id="customer-phone"
                        name="phone"
                        type="text"
                        value="{{ old('phone') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label for="customer-gst-number" class="block text-sm font-medium text-gray-700">GST number</label>
                    <input
                        id="customer-gst-number"
                        name="gst_number"
                        type="text"
                        value="{{ old('gst_number') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>

                <div class="sm:col-span-2">
                    <label for="customer-address" class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea
                        id="customer-address"
                        name="address"
                        rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >{{ old('address') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', 'add-customer-modal')"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Save customer
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
