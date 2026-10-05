@props([
    'name' => 'delete-confirm-modal',
    'title' => 'Delete Record',
    'message' => 'Are you sure you want to delete this record?',
    'recordName' => null,
    'recordDetails' => null,
    'confirmText' => 'Delete',
    'cancelText' => 'Cancel',
])

<div
    x-data="{
        show: false,
        recordId: null,
        recordName: '',
        recordDetails: '',
        formAction: '',

        open(id, name, details, action) {
            this.recordId = id;
            this.recordName = name;
            this.recordDetails = details;
            this.formAction = action;
            this.show = true;
        },

        close() {
            this.show = false;
            this.recordId = null;
            this.recordName = '';
            this.recordDetails = '';
            this.formAction = '';
        }
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
        } else {
            document.body.classList.remove('overflow-y-hidden');
        }
    })"
    x-on:open-delete-modal.window="
        if ($event.detail.modalName === '{{ $name }}') {
            open(
                $event.detail.id,
                $event.detail.name,
                $event.detail.details,
                $event.detail.action
            )
        }
    "
    x-on:close-delete-modal.window="$event.detail.modalName === '{{ $name }}' ? close() : null"
    x-on:keydown.escape.window="close()"
    x-show="show"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    style="display: none;"
>
    <!-- Backdrop -->
    <div
        x-show="show"
        class="fixed inset-0 transform transition-all"
        x-on:click="close()"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <!-- Modal -->
    <div
        x-show="show"
        class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-md sm:mx-auto"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77-1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-3 w-full">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $title }}
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            {{ $message }}
                        </p>
                        <p class="mt-2 text-sm font-medium text-gray-900" x-text="recordName"></p>
                        <p class="mt-1 text-xs text-gray-500" x-text="recordDetails"></p>
                        <p class="mt-3 text-xs text-red-600 font-medium">
                            This action cannot be undone.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <form
                x-ref="deleteForm"
                method="POST"
                :action="formAction"
                class="contents"
            >
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                >
                    {{ $confirmText }}
                </button>
            </form>
            <button
                type="button"
                x-on:click="close()"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
                {{ $cancelText }}
            </button>
        </div>
    </div>
</div>
