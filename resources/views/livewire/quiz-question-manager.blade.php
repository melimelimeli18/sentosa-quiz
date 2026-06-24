<div style="display:flex;flex-direction:column;gap:0">

    {{-- ══════════════════════════════════════════════
         QUESTION CARDS
    ══════════════════════════════════════════════ --}}
    <div id="questions-list" style="display:flex;flex-direction:column;gap:12px">
        @forelse($questions as $index => $question)
            <div wire:key="question-{{ $question->id }}"
                 data-id="{{ $question->id }}"
                 style="display:flex;align-items:stretch;background:#fff;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.06);overflow:hidden;transition:box-shadow .15s"
                 onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,.1)'"
                 onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,.06)'">

                {{-- Drag handle: narrow strip, vertically centered --}}
                <div class="drag-handle"
                     style="display:flex;align-items:center;justify-content:center;width:36px;flex-shrink:0;background:#f9fafb;border-right:1px solid #f1f5f9;color:#d1d5db;cursor:grab;transition:all .15s"
                     onmouseover="this.style.background='#eff6ff';this.style.color='#93c5fd'"
                     onmouseout="this.style.background='#f9fafb';this.style.color='#d1d5db'">
                    <svg style="width:14px;height:14px;display:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="8.5"  cy="6"  r="1.5" fill="currentColor"/>
                        <circle cx="15.5" cy="6"  r="1.5" fill="currentColor"/>
                        <circle cx="8.5"  cy="12" r="1.5" fill="currentColor"/>
                        <circle cx="15.5" cy="12" r="1.5" fill="currentColor"/>
                        <circle cx="8.5"  cy="18" r="1.5" fill="currentColor"/>
                        <circle cx="15.5" cy="18" r="1.5" fill="currentColor"/>
                    </svg>
                </div>

                {{-- Main content --}}
                <div style="flex:1;min-width:0;padding:14px 16px">

                    {{-- Row 1: index badge + question text + action buttons --}}
                    <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:10px">

                        {{-- Question number badge --}}
                        <span style="flex-shrink:0;margin-top:1px;display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:6px;background:#f3f4f6;border:1px solid #e5e7eb;font-size:10px;font-weight:700;color:#6b7280">
                            {{ $index + 1 }}
                        </span>

                        {{-- Question text + chapter tag --}}
                        <div style="flex:1;min-width:0">
                            <p style="font-size:14px;font-weight:600;color:#111827;line-height:1.5;margin:0;text-transform:capitalize">
                                {{ $question->body }}
                            </p>
                            @if($question->chapter)
                                <div style="display:inline-flex;align-items:center;gap:4px;margin-top:5px;padding:2px 8px 2px 6px;border-radius:999px;background:#eef2ff;border:1px solid #c7d2fe">
                                    <svg style="width:10px;height:10px;color:#6366f1;flex-shrink:0;display:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                                    </svg>
                                    <span style="font-size:10px;font-weight:700;color:#4f46e5;text-transform:uppercase;letter-spacing:.04em">
                                        {{ $question->chapter->order }}. {{ $question->chapter->name }}
                                    </span>
                                </div>
                            @else
                                <div style="display:inline-flex;align-items:center;gap:4px;margin-top:5px;padding:2px 8px 2px 6px;border-radius:999px;background:#fafafa;border:1px solid #e5e7eb">
                                    <span style="font-size:10px;font-weight:600;color:#9ca3af;letter-spacing:.03em">No chapter</span>
                                </div>
                            @endif
                        </div>

                        {{-- Action buttons: edit + delete --}}
                        <div style="display:flex;align-items:center;gap:4px;flex-shrink:0">
                            <button wire:click="openEditModal({{ $question->id }})"
                                    type="button"
                                    title="Edit question"
                                    style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;color:#9ca3af;cursor:pointer;transition:all .15s"
                                    onmouseover="this.style.borderColor='#bfdbfe';this.style.color='#2563eb';this.style.background='#eff6ff'"
                                    onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#9ca3af';this.style.background='#fff'">
                                <svg style="width:13px;height:13px;display:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                                </svg>
                            </button>

                            <button wire:click="deleteQuestion({{ $question->id }})"
                                    wire:confirm="Remove this question from the quiz?"
                                    type="button"
                                    title="Remove question"
                                    style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;color:#9ca3af;cursor:pointer;transition:all .15s"
                                    onmouseover="this.style.borderColor='#fca5a5';this.style.color='#dc2626';this.style.background='#fef2f2'"
                                    onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#9ca3af';this.style.background='#fff'">
                                <svg style="width:13px;height:13px;display:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Row 2: MCQ options — visually separated from the question --}}
                    <div style="display:flex;flex-wrap:wrap;gap:6px;padding-top:10px;border-top:1px solid #f3f4f6">
                        @foreach($question->options->sortBy('label') as $option)
                            <div wire:key="opt-{{ $option->id }}"
                                 wire:click="setCorrectOption({{ $question->id }}, {{ $option->id }})"
                                 title="Click to mark as correct"
                                 style="display:inline-flex;align-items:center;gap:6px;padding:4px 12px 4px 6px;border-radius:999px;cursor:pointer;user-select:none;font-size:12px;font-weight:500;transition:all .15s;
                                        {{ $option->is_correct
                                            ? 'background:#f0fdf4;border:1.5px solid #86efac;color:#166534'
                                            : 'background:#f9fafb;border:1.5px solid #e5e7eb;color:#374151' }}">

                                {{-- Letter bubble --}}
                                <span style="display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;border-radius:50%;font-size:10px;font-weight:700;flex-shrink:0;
                                             {{ $option->is_correct
                                                ? 'background:#16a34a;color:#fff'
                                                : 'background:#e5e7eb;color:#6b7280' }}">
                                    {{ $option->label }}
                                </span>

                                <span style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                    {{ $option->body }}
                                </span>

                                @if($option->is_correct)
                                    <svg style="width:13px;height:13px;color:#16a34a;flex-shrink:0;display:block" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        @empty
            <div style="padding:56px 24px;text-align:center;border:2px dashed #e5e7eb;border-radius:16px;background:#fafafa">
                <div style="display:inline-flex;align-items:center;justify-content:center;width:52px;height:52px;border-radius:50%;background:#f3f4f6;margin-bottom:12px">
                    <svg style="width:26px;height:26px;color:#9ca3af;display:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                    </svg>
                </div>
                <p style="font-size:15px;font-weight:600;color:#374151;margin:0 0 4px">No questions yet</p>
                <p style="font-size:13px;color:#9ca3af;margin:0">Import from Excel or add questions using the buttons above.</p>
            </div>
        @endforelse
    </div>

    {{-- ══════════════════════════════════════════════
         EDIT MODAL
    ══════════════════════════════════════════════ --}}
    @if($isEditModalOpen)
        {{-- Backdrop --}}
        <div style="position:fixed;inset:0;z-index:50;background:rgba(0,0,0,0.45);backdrop-filter:blur(3px)"
             wire:click="closeEdit"></div>

        {{-- Dialog --}}
        <div style="position:fixed;inset:0;z-index:51;display:flex;align-items:center;justify-content:center;padding:24px"
             wire:click.stop>
            <div style="background:#fff;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,0.2);width:100%;max-width:560px;overflow:hidden"
                 wire:click.stop>

                {{-- Header --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f3f4f6;background:#f9fafb">
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:#dbeafe">
                            <svg style="width:15px;height:15px;color:#2563eb;display:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <span style="font-size:15px;font-weight:700;color:#111827">Edit Question</span>
                    </div>
                    <button wire:click="closeEdit" type="button"
                            style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;border:none;background:transparent;color:#9ca3af;cursor:pointer"
                            onmouseover="this.style.background='#f3f4f6';this.style.color='#374151'"
                            onmouseout="this.style.background='transparent';this.style.color='#9ca3af'">
                        <svg style="width:14px;height:14px;display:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div style="padding:24px 24px 20px">
                    <div style="margin-bottom:16px">
                        <label style="display:block;font-size:11px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#6b7280;margin-bottom:6px">Materi (Chapter)</label>
                        <select wire:model="editChapterId"
                                style="display:block;width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:8px 12px;font-size:14px;color:#111827;background:#f9fafb;outline:none;transition:border .15s"
                                onfocus="this.style.borderColor='#60a5fa';this.style.background='#fff'"
                                onblur="this.style.borderColor='#e5e7eb';this.style.background='#f9fafb'">
                            <option value="">-- Tanpa Materi --</option>
                            @foreach($subjectChapters as $chapter)
                                <option value="{{ $chapter['id'] }}">{{ $chapter['order'] }}. {{ $chapter['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom:20px">
                        <label style="display:block;font-size:11px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#6b7280;margin-bottom:6px">Question</label>
                        <textarea wire:model="editBody"
                                  rows="3"
                                  style="display:block;width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:10px 12px;font-size:14px;color:#111827;background:#f9fafb;resize:vertical;outline:none;transition:border .15s"
                                  onfocus="this.style.borderColor='#60a5fa';this.style.background='#fff'"
                                  onblur="this.style.borderColor='#e5e7eb';this.style.background='#f9fafb'"
                                  placeholder="Enter the question text…"></textarea>
                        @error('editBody') <p style="font-size:12px;color:#dc2626;margin-top:4px;font-weight:500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#6b7280;margin-bottom:8px">
                            Answer Options — <span style="color:#16a34a">select correct answer</span>
                        </label>
                        <div style="display:flex;flex-direction:column;gap:8px">
                            @foreach(['A','B','C','D'] as $label)
                                @if(isset($editOptions[$label]))
                                    <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;border:1.5px solid {{ $editCorrectLabel === $label ? '#86efac' : '#e5e7eb' }};background:{{ $editCorrectLabel === $label ? '#f0fdf4' : '#f9fafb' }};cursor:pointer;transition:all .15s">
                                        <input type="radio"
                                               wire:model="editCorrectLabel"
                                               value="{{ $label }}"
                                               style="width:15px;height:15px;accent-color:#16a34a;cursor:pointer;flex-shrink:0">
                                        <span style="display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:8px;font-size:12px;font-weight:700;flex-shrink:0;
                                                     {{ $editCorrectLabel === $label ? 'background:#16a34a;color:#fff' : 'background:#e5e7eb;color:#6b7280' }}">
                                            {{ $label }}
                                        </span>
                                        <input type="text"
                                               wire:model="editOptions.{{ $label }}.body"
                                               style="flex:1;border:none;background:transparent;font-size:13px;color:#111827;outline:none;font-weight:500"
                                               placeholder="Option {{ $label }}…">
                                    </label>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;padding:14px 24px;border-top:1px solid #f3f4f6;background:#f9fafb">
                    <button wire:click="closeEdit" type="button"
                            style="padding:8px 18px;border-radius:8px;border:1px solid #e5e7eb;background:#fff;font-size:13px;font-weight:600;color:#374151;cursor:pointer"
                            onmouseover="this.style.background='#f9fafb'"
                            onmouseout="this.style.background='#fff'">
                        Cancel
                    </button>
                    <button wire:click="saveEdit" wire:loading.attr="disabled" type="button"
                            style="display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:8px 20px;border-radius:8px;border:none;background:#2563eb;font-size:13px;font-weight:600;color:#fff;cursor:pointer"
                            onmouseover="this.style.background='#1d4ed8'"
                            onmouseout="this.style.background='#2563eb'">
                        <!-- ponytail: loading indicator circle -->
                        <svg wire:loading wire:target="saveEdit" class="animate-spin" style="width:14px;height:14px;color:#fff;display:inline-block" fill="none" viewBox="0 0 24 24">
                            <circle style="opacity:0.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path style="opacity:0.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Save Changes</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════
         SORTABLEJS
    ══════════════════════════════════════════════ --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        (function () {
            let sortableInstance = null;

            function initSortable() {
                const el = document.getElementById('questions-list');
                if (!el) return;
                if (sortableInstance) { sortableInstance.destroy(); sortableInstance = null; }
                sortableInstance = Sortable.create(el, {
                    handle: '.drag-handle',
                    animation: 200,
                    onEnd: function (evt) {
                        if (evt.oldIndex !== evt.newIndex) {
                            @this.reorderQuestions(evt.oldIndex, evt.newIndex);
                        }
                    }
                });
            }

            document.addEventListener('livewire:initialized', initSortable);
            document.addEventListener('livewire:updated', initSortable);
        })();
    </script>

</div>
