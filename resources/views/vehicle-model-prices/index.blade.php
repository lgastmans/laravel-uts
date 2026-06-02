<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Vehicle Model Prices') }}
        </h2>
    </x-slot>

    <div class="py-4" x-data x-on:open-vehicle-model-price-create-modal.window="$dispatch('open-modal', 'add-vehicle-model-price-modal')">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
            @endif

            <livewire:vehicle-model-price-table />
        </div>
    </div>

    <x-modal name="add-vehicle-model-price-modal" :show="$errors->any()" max-width="2xl" focusable>
        <form method="POST" action="{{ route('vehicle-model-prices.store') }}" class="p-6">
            @csrf

            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Add vehicle model price</h3>
                    <p class="mt-1 text-sm text-gray-600">Create model pricing without leaving this page.</p>
                </div>
                <button type="button" x-on:click="$dispatch('close-modal', 'add-vehicle-model-price-modal')" class="rounded-md p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600" aria-label="Close add vehicle model price window">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
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
                    <label for="price-model" class="block text-sm font-medium text-gray-700">Model</label>
                    <input id="price-model" name="model" type="text" value="{{ old('model') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="price-day-rent" class="block text-sm font-medium text-gray-700">Day Rent</label>
                    <input id="price-day-rent" name="day_rent" type="number" step="0.01" min="0" value="{{ old('day_rent', 0) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="price-rate-per-km" class="block text-sm font-medium text-gray-700">Rate Per Km</label>
                    <input id="price-rate-per-km" name="rate_per_km" type="number" step="0.01" min="0" value="{{ old('rate_per_km', 0) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="price-hourly-charge" class="block text-sm font-medium text-gray-700">Hourly Charge</label>
                    <input id="price-hourly-charge" name="hourly_charge" type="number" step="0.01" min="0" value="{{ old('hourly_charge', 0) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="price-local-charge" class="block text-sm font-medium text-gray-700">Local Charge</label>
                    <input id="price-local-charge" name="local_charge" type="number" step="0.01" min="0" value="{{ old('local_charge', 0) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'add-vehicle-model-price-modal')" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Cancel</button>
                <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Save price</button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
