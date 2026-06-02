<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Add Vehicle') }}</h2></x-slot>

    <div class="py-4">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"><ul class="list-disc space-y-1 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            <form method="POST" action="{{ route('vehicles.store') }}" class="rounded-lg bg-white p-6 shadow-sm">
                @csrf
                <div class="grid gap-5 sm:grid-cols-2">
                    <div><label for="vehicle-owner" class="block text-sm font-medium text-gray-700">Owner</label><select id="vehicle-owner" name="owner_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><option value="">No owner</option>@foreach ($owners as $owner)<option value="{{ $owner->id }}" @selected(old('owner_id') == $owner->id)>{{ $owner->name }}</option>@endforeach</select></div>
                    <div><label for="vehicle-model-price" class="block text-sm font-medium text-gray-700">Model</label><select id="vehicle-model-price" name="vehicle_model_price_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><option value="">Select model</option>@foreach ($vehicleModelPrices as $vehicleModelPrice)<option value="{{ $vehicleModelPrice->id }}" @selected(old('vehicle_model_price_id') == $vehicleModelPrice->id)>{{ $vehicleModelPrice->model }}</option>@endforeach</select></div>
                    <div class="sm:col-span-2"><label for="vehicle-registration-number" class="block text-sm font-medium text-gray-700">Registration number</label><input id="vehicle-registration-number" type="text" name="registration_number" value="{{ old('registration_number') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></div>
                    <div><label for="vehicle-insurance-validity" class="block text-sm font-medium text-gray-700">Insurance validity</label><input id="vehicle-insurance-validity" type="date" name="insurance_validity" value="{{ old('insurance_validity') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></div>
                    <div><label for="vehicle-permit-validity" class="block text-sm font-medium text-gray-700">Permit validity</label><input id="vehicle-permit-validity" type="date" name="permit_validity" value="{{ old('permit_validity') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></div>
                </div>
                <div class="mt-6 flex items-center justify-end gap-3"><a href="{{ route('vehicles.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Cancel</a><button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Save vehicle</button></div>
            </form>
        </div>
    </div>
</x-app-layout>
