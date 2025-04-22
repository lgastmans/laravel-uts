<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="p-4">
        {{--        
        <div id="bulkAlertBox"
            class="relative flex p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800"
            role="alert"
            style="display: none;">
            
            <svg class="shrink-0 inline w-4 h-4 me-3 mt-[2px]" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 20 20">
                <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>

            <div>
                <span id="bulkAlertTitle" class="font-medium">Messages:</span>
                <ul id="bulkAlertList" class="mt-1.5 list-disc list-inside"></ul>
            </div>

            <!-- Close button -->
            <button onclick="document.getElementById('bulkAlertBox').style.display='none'"
                class="absolute top-2 right-2 text-red-800 hover:text-red-900 dark:text-red-400 dark:hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        --}}

        <div id="bulkAlertBox"
            class="relative flex p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50"
            role="alert"
            style="display: none;">
            
            <svg class="shrink-0 inline w-4 h-4 me-3 mt-[2px]" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 20 20">
                <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>

            <div>
                <span id="bulkAlertTitle" class="font-medium">Messages:</span>
                <ul id="bulkAlertList" class="mt-1.5 list-disc list-inside"></ul>
            </div>

            <!-- Close button -->
            <button onclick="document.getElementById('bulkAlertBox').style.display='none'"
                class="absolute top-2 right-2 text-red-800 hover:text-red-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <livewire:bills-table />

    </div>

</x-app-layout>

<script>
    
    window.addEventListener('DOMContentLoaded', () => {
        const alertBox = document.getElementById('bulkAlertBox');
        if (alertBox) alertBox.style.display = 'none';
    });

    window.addEventListener('showBulkMessages', event => {
        console.log(event);
        const { type, title, messages } = event.detail[0];

        const alertBox = document.getElementById('bulkAlertBox');
        const titleSpan = document.getElementById('bulkAlertTitle');
        const list = document.getElementById('bulkAlertList');

        if (alertBox && list) {
            list.innerHTML = '';
            messages.forEach(msg => {
                const li = document.createElement('li');
                li.textContent = msg;
                list.appendChild(li);
            });

            titleSpan.textContent = title ?? 'Messages:';

            // Show the alert
            alertBox.style.display = 'flex';

            // Auto-hide after 6 seconds
            /*
            setTimeout(() => {
                alertBox.style.display = 'none';
            }, 6000);
            */
        }
    });

</script>