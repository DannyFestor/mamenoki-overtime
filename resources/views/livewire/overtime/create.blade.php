<div class="w-full max-w-7xl mx-auto flex flex-col gap-4">
    <div class="w-full flex justify-between border-2 border-sky-900 p-4">
        <div>{{ $name }}</div>

        <div class="flex-1 text-center">
            時間外勤務命新規作成
        </div>

        <a href="{{ route('overtime.index') }}">一覧へ戻る</a>
    </div>

    <form wire:submit="submit" class="w-full max-w-lg mx-auto flex flex-col divide-y-2 divide-slate-200 gap-4 border-2 border-sky-900">
        <div wire:ignore
            x-data="{
            value: $wire.entangle('form.date').live,
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
        }" class="flex flex-col p-4">
            <label for="date">時間外勤務をした日を選んでください</label>
            <input x-ref="pickr" name="date" type="text" class="form-input">
            @error('form.date')
            {{ $message }}
            @enderror
        </div>

        <div wire:ignore
             x-data="{
            timeFrom: $wire.entangle('form.timeFrom').live,
            timeUntil: $wire.entangle('form.timeUntil').live,
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

                this.$watch('timeFrom', () => pickerFrom.setDate(this.timeFrom))
                this.$watch('timeUntil', () => pickerUntil.setDate(this.timeUntil))
            }
        }" class="flex flex-col p-4">
            <label for="time_from">取得日を選んでください</label>
            <div class="flex w-full">
                <input x-ref="time_from" name="time_from" type="text" class="form-input w-[45%]">
                <div class="flex items-center justify-center w-[10%]">〜</div>
                <input x-ref="time_until" name="time_until" type="text" class="form-input w-[45%]">
            </div>
            @error('form.timeFrom')
            {{ $message }}
            @enderror
            @error('form.timeUntil')
            {{ $message }}
            @enderror
            TODO: better date picker
        </div>

        <div class="flex flex-col p-4">
            <label for="reason">事由を選んでください</label>
            <select wire:model="form.reason" name="reason" class="form-select">
                <option value="0">理由を選んでください</option>
                @foreach(\App\Enums\OvertimeReason::toArray() as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            @error('form.reason')
            {{ $message }}
            @enderror
        </div>

        <div class="flex flex-col p-4">
            <label for="remarks">備考</label>
            <textarea wire:model="form.remarks" name="remarks" id="remarks" cols="30" rows="3"></textarea>
            @error('form.remarks')
            {{ $message }}
            @enderror
        </div>

        <div>
            @if($locked)
            <div class="text-center text-red-500">
                選択された日付には既に承認済みの残業があります。
            </div>
            @endif
            <div class="flex justify-center gap-4 p-4">
                <button type="submit"
                        @class([
                            'w-20 h-20 rounded-full border-4 border-emerald-300 bg-emerald-100 text-emerald-800 flex justify-center items-center',
                            'opacity-50' => $locked
                        ])
                        class="w-20 h-20 rounded-full border-4 border-emerald-300 bg-emerald-100 text-emerald-800 flex justify-center items-center"
                        @if($locked)
                            disabled
                        @endif
                >申請</button>
                <button wire:click="saveDraft" type="button"
                        @class([
                            'w-20 h-20 rounded-full border-4 border-amber-300 bg-amber-100 text-amber-800 flex justify-center items-center',
                            'opacity-50' => $locked
                        ])
                        @if($locked)
                            disabled
                        @endif
                >一時保存</button>
            </div>
        </div>
    </form>
</div>
