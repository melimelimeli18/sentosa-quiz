<div>
    <template x-teleport="body">
        <div x-data="{ get open() { return $wire.open } }">

            {{-- Backdrop --}}
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9998;background:rgba(0,0,0,0.5);backdrop-filter:blur(3px)"
                @click="$wire.set('open', false)"
            ></div>

            {{-- Dialog --}}
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;display:flex;align-items:center;justify-content:center;padding:24px;box-sizing:border-box"
                @click.self="$wire.set('open', false)"
            >
                <div style="background:#fff;border-radius:16px;box-shadow:0 25px 60px rgba(0,0,0,0.25);width:100%;max-width:600px;overflow:hidden;display:flex;flex-direction:column;max-height:88vh">

                    {{-- Header --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #f1f5f9;background:#f8fafc;flex-shrink:0">
                        <div style="display:flex;align-items:center;gap:12px">
                            <div style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:10px;background:#eef2ff">
                                <svg style="width:18px;height:18px;color:#6366f1;display:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v6m3-3H9m4.06-7.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                </svg>
                            </div>
                            <div>
                                <p style="font-size:15px;font-weight:700;color:#111827;margin:0">Add Questions to Chapter</p>
                                <p style="font-size:12px;color:#6b7280;margin:2px 0 0">
                                    @if($step === 1) Step 1 of 2 — Choose a chapter @else Step 2 of 2 — Select questions @endif
                                </p>
                            </div>
                        </div>
                        <button
                            wire:click="$set('open', false)"
                            style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;border:none;background:transparent;color:#9ca3af;cursor:pointer;transition:all .15s"
                            onmouseover="this.style.background='#f3f4f6';this.style.color='#374151'"
                            onmouseout="this.style.background='transparent';this.style.color='#9ca3af'"
                        >
                            <svg style="width:16px;height:16px;display:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Progress bar --}}
                    <div style="height:3px;background:#e5e7eb;flex-shrink:0">
                        <div style="height:3px;background:#6366f1;transition:width 0.3s ease;width:{{ $step === 1 ? '50%' : '100%' }}"></div>
                    </div>

                    {{-- Body --}}
                    <div style="padding:24px;overflow-y:auto;flex:1">

                        @if($step === 1)
                            @if($this->chapters->isEmpty())
                                <div style="text-align:center;padding:48px 24px;background:#f9fafb;border-radius:12px;border:2px dashed #e5e7eb">
                                    <div style="display:inline-flex;align-items:center;justify-content:center;width:48px;height:48px;border-radius:50%;background:#f3f4f6;margin-bottom:12px">
                                        <svg style="width:24px;height:24px;color:#9ca3af;display:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v6m3-3H9m4.06-7.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                        </svg>
                                    </div>
                                    <p style="font-size:14px;font-weight:600;color:#374151;margin:0 0 4px">No chapters found</p>
                                    <p style="font-size:13px;color:#9ca3af;margin:0">Add chapters from the Subject management page first.</p>
                                </div>
                            @else
                                <div style="display:flex;flex-direction:column;gap:8px">
                                    @foreach($this->chapters as $chapter)
                                        <button
                                            wire:click="selectChapter({{ $chapter->id }})"
                                            style="display:flex;align-items:center;gap:14px;padding:14px 16px;border:1.5px solid #e5e7eb;border-radius:12px;background:#fff;cursor:pointer;text-align:left;width:100%;transition:all .15s"
                                            onmouseover="this.style.borderColor='#818cf8';this.style.background='#f5f3ff'"
                                            onmouseout="this.style.borderColor='#e5e7eb';this.style.background='#fff'"
                                        >
                                            <div style="flex-shrink:0;width:36px;height:36px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#6b7280">
                                                {{ $chapter->order }}
                                            </div>
                                            <div style="flex:1;min-width:0">
                                                <p style="font-size:14px;font-weight:600;color:#111827;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $chapter->name }}</p>
                                            </div>
                                            <svg style="width:16px;height:16px;color:#d1d5db;flex-shrink:0;display:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    @endforeach
                                </div>
                            @endif

                        @elseif($step === 2)
                            @php $untagged = $this->untaggedQuestions; @endphp

                            @if($untagged->isEmpty())
                                <div style="text-align:center;padding:48px 24px;background:#f0fdf4;border-radius:12px;border:1px solid #bbf7d0">
                                    <div style="display:inline-flex;align-items:center;justify-content:center;width:48px;height:48px;border-radius:50%;background:#dcfce7;margin-bottom:12px">
                                        <svg style="width:24px;height:24px;color:#16a34a;display:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <p style="font-size:14px;font-weight:600;color:#166534;margin:0 0 4px">All questions already have a chapter!</p>
                                    <p style="font-size:13px;color:#4ade80;margin:0">No untagged questions remain in this quiz.</p>
                                </div>
                            @else
                                <p style="font-size:11px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#9ca3af;margin:0 0 12px">{{ $untagged->count() }} untagged question(s)</p>
                                <div x-data="{ checkedCount: 0 }" style="display:flex;flex-direction:column;gap:8px">
                                    @foreach($untagged as $question)
                                        <label style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;border:1.5px solid #e5e7eb;border-radius:12px;cursor:pointer;transition:all .15s"
                                               onmouseover="this.style.background='#f9fafb'"
                                               onmouseout="this.style.background='#fff'">
                                            <input
                                                type="checkbox"
                                                wire:model.live="selectedQuestionIds"
                                                value="{{ $question->id }}"
                                                @change="checkedCount = $el.closest('[x-data]').querySelectorAll('input:checked').length"
                                                style="margin-top:2px;width:16px;height:16px;accent-color:#6366f1;cursor:pointer;flex-shrink:0"
                                            >
                                            <span style="font-size:13px;font-weight:500;color:#111827;line-height:1.6;text-transform:capitalize">{{ $question->body }}</span>
                                        </label>
                                    @endforeach

                                    {{-- Save button inside x-data scope so it can read checkedCount --}}
                                    <div style="display:flex;align-items:center;justify-content:space-between;border-top:1px solid #f1f5f9;margin-top:16px;padding-top:16px">
                                        <button
                                            wire:click="back"
                                            style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:8px;border:1px solid #e5e7eb;background:#fff;font-size:13px;font-weight:600;color:#374151;cursor:pointer"
                                            onmouseover="this.style.background='#f9fafb'"
                                            onmouseout="this.style.background='#fff'"
                                        >
                                            <svg style="width:14px;height:14px;display:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                                            </svg>
                                            Back
                                        </button>
                                        <button
                                            wire:click="save"
                                            wire:loading.attr="disabled"
                                            :disabled="checkedCount === 0"
                                            :style="checkedCount === 0
                                                ? 'display:inline-flex;align-items:center;gap:6px;padding:9px 24px;border-radius:8px;border:none;background:#a5b4fc;font-size:13px;font-weight:600;color:#fff;cursor:not-allowed'
                                                : 'display:inline-flex;align-items:center;gap:6px;padding:9px 24px;border-radius:8px;border:none;background:#6366f1;font-size:13px;font-weight:600;color:#fff;cursor:pointer'"
                                            x-on:mouseover="if(checkedCount > 0) $el.style.background='#4f46e5'"
                                            x-on:mouseout="if(checkedCount > 0) $el.style.background='#6366f1'"
                                        >
                                            <!-- ponytail: circle animation for loading state -->
                                            <svg wire:loading wire:target="save" class="animate-spin" style="width:14px;height:14px;color:#fff;display:inline-block" fill="none" viewBox="0 0 24 24">
                                                <circle style="opacity:0.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path style="opacity:0.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span x-text="checkedCount > 0 ? 'Save (' + checkedCount + ') Questions' : 'Select questions first'"></span>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Footer (only shown on step 1 or as Cancel on step 2 outside of x-data scope) --}}
                    @if($step === 1)
                    <div style="display:flex;align-items:center;justify-content:flex-end;padding:16px 24px;border-top:1px solid #f1f5f9;background:#f8fafc;flex-shrink:0">
                        <button
                            wire:click="$set('open', false)"
                            style="padding:9px 18px;border-radius:8px;border:1px solid #e5e7eb;background:#fff;font-size:13px;font-weight:600;color:#374151;cursor:pointer"
                            onmouseover="this.style.background='#f9fafb'"
                            onmouseout="this.style.background='#fff'"
                        >
                            Cancel
                        </button>
                    </div>
                    @endif

                </div>
            </div>

        </div>
    </template>
</div>
