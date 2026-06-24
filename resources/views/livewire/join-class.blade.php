<div>
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-md mx-4 p-6 overflow-hidden transition-all">

                {{-- Header --}}
                <div class="flex justify-between items-start mb-5">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.webp') }}" alt="Logo" class="h-8 w-8 object-contain" />
                        <h3 class="text-lg font-bold text-gray-900" id="modal-title">Bergabung ke Kelas</h3>
                    </div>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="h-px bg-gray-100 mb-5"></div>

                <div class="mb-5">
                    <p class="text-sm text-gray-500">
                        Masukkan kode kelas 6 digit yang diberikan oleh guru kamu untuk bergabung ke ruang kelas.
                    </p>
                </div>

                <form wire:submit.prevent="joinClass" class="flex flex-col gap-4">
                    <div>
                        <label for="join_code" class="block text-sm font-semibold text-gray-700 mb-1">Kode Kelas</label>
                        <input
                            wire:model="join_code"
                            type="text"
                            id="join_code"
                            class="w-full uppercase bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-900 focus:ring-2 focus:ring-[#C50303] focus:border-[#C50303] tracking-widest text-center font-bold text-lg outline-none transition"
                            placeholder="A3F9KZ"
                            maxlength="6"
                            autocomplete="off"
                            autofocus
                        >
                        @error('join_code')
                            <span class="text-[#C50303] text-xs mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="h-px bg-gray-100"></div>

                    <div class="flex gap-3">
                        <button type="button" wire:click="closeModal"
                            class="w-full bg-white text-gray-600 border border-gray-200 hover:bg-gray-50 font-semibold py-2.5 px-4 rounded-xl transition-colors">
                            Lewati
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                            class="w-full bg-[#C50303] hover:bg-[#a50202] text-white font-semibold py-2.5 px-4 rounded-xl transition-colors inline-flex items-center justify-center gap-2">
                            <!-- ponytail: circle animation for loading state -->
                            <svg wire:loading wire:target="joinClass" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Bergabung</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
