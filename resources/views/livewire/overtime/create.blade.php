<div class="w-full max-w-7xl mx-auto flex flex-col gap-4">
    <div class="w-full flex justify-between border-2 border-sky-900 p-4">
        <div>{{ $name }}</div>

        <div class="flex-1 text-center">
            時間外勤務命新規作成
        </div>

        <a href="{{ route('overtime.index') }}">一覧へ戻る</a>
    </div>

    <form wire:submit="submit" class="w-full max-w-lg mx-auto flex flex-col divide-y-2 divide-slate-200 border-2 border-sky-900">
        {{-- Date --}}
        <div class="flex p-4 gap-4">
            <div class="sm:mt-5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 sm:w-12 sm:h-12">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                </svg>
            </div>

            <div wire:ignore
                x-data="{
                value: $wire.entangle('date').live,
                init() {
                    let picker = flatpickr(this.$refs.pickr, {
                        altInput: true,
                        altFormat: 'Y年m月d日',
                        dateFormat: 'Y-m-d',
                        defaultDate: this.value,
                        onChange: (date, dateString) => {
                            this.value = dateString;
                        }
                    });

                    this.$watch('value', () => picker.setDate(this.value))
                }
            }" class="flex-1 flex flex-col">
                <label for="date">時間外勤務をした日を選んでください</label>
                <input x-ref="pickr" name="date" type="text" class="form-input w-full">
                @error('form.date')
                {{ $message }}
                @enderror
            </div>
        </div>

        @if($isConfirmed)
            <div class="text-red-500 font-bold p-4 text-center">
                選択された年月は既に最終確認を行いましたので、新規作成することはできません。
            </div>
        @endif

        {{-- Time --}}
        <div class="flex p-4 gap-4">
            <div class="sm:mt-5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 sm:w-12 sm:h-12">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>

            <div wire:ignore
                 x-data="{
                timeFrom: $wire.entangle('form.timeFrom').live,
                timeUntil: $wire.entangle('form.timeUntil').live,
                isApproved: $wire.entangle('isApproved').live,
                isConfirmed: $wire.entangle('isConfirmed').live,

                init() {
                    let pickerFrom = flatpickr(this.$refs.time_from, {
                        altInput: true,
                        altFormat: 'H:i時',
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: 'H:i',
                        time_24hr: true,
                        defaultDate: this.timeFrom,
                        onChange: (date, dateString) => {
                            this.timeFrom = dateString;
                        }
                    });
                    let pickerUntil = flatpickr(this.$refs.time_until, {
                        altInput: true,
                        altFormat: 'H:i時',
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: 'H:i',
                        time_24hr: true,
                        defaultDate: this.timeUntil,
                        onChange: (date, dateString) => {
                            this.timeUntil = dateString;
                        }
                    });

                    if (this.isApproved || this.isConfirmed) {
                        pickerFrom._input.setAttribute('disabled', 'disabled');
                        pickerUntil._input.setAttribute('disabled', 'disabled');
                    } else {
                        pickerFrom._input.removeAttribute('disabled');
                        pickerUntil._input.removeAttribute('disabled');
                    }

                    this.$watch('timeFrom', () => pickerFrom.setDate(this.timeFrom))
                    this.$watch('timeUntil', () => pickerUntil.setDate(this.timeUntil))
                    this.$watch('isApproved', (v) => {
                        if (v) {
                            pickerFrom._input.setAttribute('disabled', 'disabled');
                            pickerUntil._input.setAttribute('disabled', 'disabled');
                        } else {
                            pickerFrom._input.removeAttribute('disabled');
                            pickerUntil._input.removeAttribute('disabled');
                        }
                    });
                    this.$watch('isConfirmed', (v) => {
                        if (v) {
                            pickerFrom._input.setAttribute('disabled', 'disabled');
                            pickerUntil._input.setAttribute('disabled', 'disabled');
                        } else {
                            pickerFrom._input.removeAttribute('disabled');
                            pickerUntil._input.removeAttribute('disabled');
                        }
                    });
                }
            }" class="flex-1 flex flex-col">
                <label for="time_from">取得日を選んでください</label>
                <div class="flex flex-col sm:flex-row w-full">
                    <input x-ref="time_from" name="time_from" type="text" class="form-input w-full sm:w-[45%]">
                    <div class="flex items-center justify-center w-full sm:w-[10%]">〜</div>
                    <input x-ref="time_until" name="time_until" type="text" class="form-input w-full sm:w-[45%]">
                </div>
                @error('form.timeFrom')
                {{ $message }}
                @enderror
                @error('form.timeUntil')
                {{ $message }}
                @enderror
            </div>
        </div>

        {{-- Reason --}}
        <div class="flex p-4 gap-4">
            <div class="sm:mt-5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 sm:w-12 sm:h-12">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                </svg>
            </div>

            <div class="flex-1 flex flex-col">
                <label for="reason">事由を選んでください</label>
                <select @if($isApproved || $isConfirmed) disabled @endif wire:model="form.reason" name="reason" class="form-select w-full">
                    <option value="0">理由を選んでください</option>
                    @foreach(\App\Enums\OvertimeReason::toArray() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('form.reason')
                {{ $message }}
                @enderror
            </div>
        </div>

        {{-- Remarks --}}
        <div class="flex p-4 gap-4">
            <div class="sm:mt-5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 sm:w-12 sm:h-12">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
            </div>

            <div class="flex-1 flex flex-col">
                <label for="remarks">備考</label>
                <textarea @if($isApproved || $isConfirmed) disabled @endif wire:model="form.remarks" name="remarks" id="remarks" cols="30" rows="3" class="form-textarea w-full"></textarea>
                @error('form.remarks')
                {{ $message }}
                @enderror
            </div>
        </div>

        <div>
            @if($isApproved)
            <div class="text-center text-red-500">
                選択された日付には既に承認済みの残業があります。
            </div>
            @endif
            <div class="flex justify-evenly p-4">
                <button type="submit"
                        @class([
                            'w-20 h-20 rounded-full border-4 border-emerald-300 bg-emerald-100 text-emerald-800 flex flex-col justify-center items-center transition-all hover:scale-105 hover:shadow',
                            'opacity-50' => ($isApproved || $isConfirmed)
                        ])
                        @if($isApproved || $isConfirmed)
                            disabled
                        @endif
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                    </svg>
                    <div class="font-bold">申請</div>
                </button>
                <button wire:click="saveDraft" type="button"
                        @class([
                            'w-20 h-20 rounded-full border-4 border-amber-300 bg-amber-100 text-amber-800 flex flex-col justify-center items-center transition-all hover:scale-105 hover:shadow',
                            'opacity-50' => ($isApproved || $isConfirmed)
                        ])
                        @if($isApproved || $isConfirmed)
                            disabled
                        @endif
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 0 0 4.5 4.5H18a3.75 3.75 0 0 0 1.332-7.257 3 3 0 0 0-3.758-3.848 5.25 5.25 0 0 0-10.233 2.33A4.502 4.502 0 0 0 2.25 15Z" />
                    </svg>

                    <div class="font-bold">一時保存</div>
                </button>
            </div>
        </div>

        <div class="flex">
            <a href="{{ route('overtime.index') }}" class="w-full text-center hover:underline hover:bg-slate-200 p-4">一覧へ戻る</a>
        </div>
    </form>
</div>
