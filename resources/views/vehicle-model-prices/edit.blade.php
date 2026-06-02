<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Vehicle Model Price</h2></x-slot>

    <div class="py-4">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"><ul class="list-disc space-y-1 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            <form method="POST" action="{{ route('vehicle-model-prices.update', $vehicleModelPrice->id) }}" class="rounded-lg bg-white p-6 shadow-sm">
                @csrf
                @method('PUT')
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2"><label for="price-model" class="block text-sm font-medium text-gray-700">Model</label><input id="price-model" type="text" name="model" value="{{ old('model', $vehicleModelPrice->model) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></div>
                    <div><label for="price-day-rent" class="block text-sm font-medium text-gray-700">Day Rent</label><input id="price-day-rent" type="number" step="0.01" min="0" name="day_rent" value="{{ old('day_rent', $vehicleModelPrice->day_rent) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></div>
                    <div><label for="price-rate-per-km" class="block text-sm font-medium text-gray-700">Rate Per Km</label><input id="price-rate-per-km" type="number" step="0.01" min="0" name="rate_per_km" value="{{ old('rate_per_km', $vehicleModelPrice->rate_per_km) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></div>
                    <div><label for="price-hourly-charge" class="block text-sm font-medium text-gray-700">Hourly Charge</label><input id="price-hourly-charge" type="number" step="0.01" min="0" name="hourly_charge" value="{{ old('hourly_charge', $vehicleModelPrice->hourly_charge) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></div>
                    <div><label for="price-local-charge" class="block text-sm font-medium text-gray-700">Local Charge</label><input id="price-local-charge" type="number" step="0.01" min="0" name="local_charge" value="{{ old('local_charge', $vehicleModelPrice->local_charge) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></div>
                </div>
                <div class="mt-6 flex items-center justify-end gap-3"><a href="{{ route('vehicle-model-prices.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Cancel</a><button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Update price</button></div>
            </form>
        </div>
    </div>
</x-app-layout>
