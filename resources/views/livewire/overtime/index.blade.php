<div class="w-full max-w-7xl mx-auto flex flex-col gap-4">
    <div class="w-full flex flex-col sm:flex-row justify-between border-2 border-sky-900">
        <div class="p-2 sm:p-4 text-center sm:text-left">{{ $name }}</div>

        <div class="flex gap-2 justify-center items-center">
            <x-svgs.chevron-left wire:click="decreaseYear" class="cursor-pointer"/>
            {{ $this->japaneseYear() }}年度 時間外勤務命令簿
            <x-svgs.chevron-right wire:click="increaseYear" class="cursor-pointer"/>
        </div>

        <div class="flex gap-2 justify-center items-center">
            <x-svgs.chevron-left wire:click="decreaseMonth" class="cursor-pointer"/>
            {{ $month }}月分
            <x-svgs.chevron-right wire:click="increaseMonth" class="cursor-pointer"/>
        </div>
    </div>

    <a wire:navigate href="{{ route('overtime.create') }}"
       class="w-full border-2 border-sky-900 px-4 py-2 text-xl flex justify-center items-center gap-2 hover:bg-sky-100">
        新規作成
        <x-svgs.plus-circle/>
    </a>

    <div class="w-full flex flex-col sm:flex-row gap-4">
        <div class="hidden sm:flex flex-col border border-2 border-sky-900 w-full sm:w-[150px]">
            <a wire:navigate
               href="{{ route('overtime.index', ['year' => now()->year, 'month' => now()->month]) }}"
               class="px-4 py-2 hover:bg-sky-100 text-center"
            >
                {{ now()->year }}年{{ str_pad(now()->month, 2, '0', STR_PAD_LEFT) }}月
            </a>
            @foreach($overtimeConfirmationsPerYear as $confirmationYear => $yearlyOvertimeConfirmations)
                <div x-data="{ expanded: false }" wire:key="overtime-confirmations-{{ $confirmationYear }}">
                    <div @click="expanded = !expanded"
                         class="font-bold text-center px-4 py-2 bg-slate-200 hover:bg-slate-300 cursor-pointer">{{ $confirmationYear }}
                        年度
                    </div>
                    <div x-show="expanded" x-collapse class="flex flex-col">
                        @foreach($yearlyOvertimeConfirmations as $yearlyOvertimeConfirmation)
                            <a wire:navigate
                               wire:key="overtime-confirmation-{{ $yearlyOvertimeConfirmation['year'] }}-{{ $yearlyOvertimeConfirmation['month'] }}"
                               href="{{ route('overtime.index', ['year' => $yearlyOvertimeConfirmation['year'], 'month' => $yearlyOvertimeConfirmation['month']]) }}"
                               class="px-4 py-2 hover:bg-sky-200 text-right"
                            >
                                {{ $yearlyOvertimeConfirmation['year'] }}
                                年{{ str_pad($yearlyOvertimeConfirmation['month'], 2, '0', STR_PAD_LEFT) }}月
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex-1 flex flex-col gap-4">

            <x-overtimes.table :collection="$overtimes['saved'] ?? []">
                未申請 一覧
            </x-overtimes.table>

            <x-overtimes.table :collection="$overtimes['applied'] ?? []">
                申請済 一覧
            </x-overtimes.table>

            <x-overtimes.table :collection="$overtimes['approved'] ?? []">
                承認済 一覧
            </x-overtimes.table>
        </div>
    </div>

    <form @submit.prevent="submit"
          x-data="{
            isOpen: false,

            init() {
                console.log(this.$refs.form);
            },

            submit() {
                $wire.submit();
                this.isOpen = false;
            },
          }"
          id="form"
          class="w-full flex flex-col sm:flex-row justify-between border-2 border-sky-900">

        <div class="order-2 sm:order-1 p-4 w-full sm:w-1/2 flex flex-col justify-center items-center">
            <div wire:loading>
                <svg class="animate-spin -ml-1 mr-3 h-16 w-16 text-slate-900" xmlns="http://www.w3.org/2000/svg"
                     fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <div wire:loading.remove>
                @if(!$form->confirmed_at || $form->confirmed_at === '')
                    <button @click="isOpen = true"
                            type="button"
                            class="p-4 border-4 border-emerald-500 bg-emerald-50 rounded-xl font-bold text-3xl text-emerald-900">
                        最終確認
                    </button>
                @else
                    <div class="border-4 border-emerald-500 flex flex-col">
                        <div class="bg-emerald-50 text-emerald-900 p-4 text-center">
                            <span class="font-bold">最終確認(申請者 本人）</span><br>
                            当月分の申請は以上になります。
                        </div>

                        <div class="p-4 text-center">
                            {{ $user->name }}<br>
                            {{ $form->confirmed_at }}
                        </div>
                    </div>
                @endif

                @if($uuid !== '' && $form->confirmed_at)
                    <a href="{{ route('overtime.show', ['overtime_confirmation' => $uuid]) }}"
                       class="border-2 border-sky-900 mt-4 flex transition-all hover:scale-105 px-4 py-2 flex justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                        </svg>

                        PDF Download
                    </a>
                @endif

                @json($uuid)
            </div>
        </div>

        <div class="order-1 sm:order-2 flex flex-col gap-2 p-4 w-full sm:w-1/2">
            <div class="flex flex-col">
                <label for="form_remarks">
                    備考欄（当月分）
                </label>
                <textarea @if(!$form->confirmed_at || $form->confirmed_at === '') disabled @endif id="form_remarks"
                          rows="3" class="form-textarea" wire:model="form.remarks"></textarea>
            </div>
            <div class="flex flex-col">
                <label for="form_transfer_remarks">
                    引継ぎ欄<span class="text-xs">（ここに記入したことは来月の「備考欄」に反映されます）</span>
                </label>
                <textarea @if(!$form->confirmed_at || $form->confirmed_at === '') disabled @endif rows="3"
                          class="form-textarea" wire:model="form.transfer_remarks"></textarea>
            </div>
        </div>

        <div x-show="isOpen" x-transition class="fixed inset-0 flex justify-center items-center">
            <div @click="isOpen = false" class="absolute inset-0 bg-black opacity-50"></div>
            <div class="relative bg-white p-4 flex flex-col gap-4">
                <h2 class="font-bold text-3xl">
                    {{ $year }}年{{ $month }}月分の最終確認を行いますか？
                </h2>

                <div>
                    「確認」を押した後に、{{ $year }}年{{ $month }}月に新規作成することはできません。
                </div>

                <div class="flex justify-evenly">
                    <button @click="isOpen = false" class="underline" type="button">
                        キャンセル
                    </button>

                    <button
                        class="rounded-xl border-2 border-blue-800 px-4 py-2 min-w-32 bg-blue-700 text-white font-bold text-lg"
                        type="submit">
                        確認
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
