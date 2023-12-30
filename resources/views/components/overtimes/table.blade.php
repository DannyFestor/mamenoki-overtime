@props(['collection' => []])

<div x-data="{ expanded: true }" class="flex flex-col w-full border-2 border-sky-900">
    <div class="text-center px-4 py-2 hover:bg-slate-200" @click="expanded = !expanded">{{ $slot }}</div>

    <div class="w-full flex flex-col" x-show="expanded" x-collapse>
        <div class="hidden sm:flex">
            <div class="flex-1 flex justify-center items-center text-center font-bold border border-slate-300 p-2">
                施設長<br>
                承認
            </div>
            <div class="flex-1 flex justify-center items-center text-center font-bold border border-slate-300 p-2">
                取得日
            </div>
            <div class="flex-1 flex justify-center items-center text-center font-bold border border-slate-300 p-2">
                時刻
            </div>
            <div class="flex-1 flex justify-center items-center text-center font-bold border border-slate-300 p-2">
                時間
            </div>
            <div class="flex-1 flex justify-center items-center text-center font-bold border border-slate-300 p-2">
                事由
            </div>
            <div class="flex-1 flex justify-center items-center text-center font-bold border border-slate-300 p-2">
                申請者
            </div>
            <div class="flex-1 flex justify-center items-center text-center font-bold border border-slate-300 p-2">
                作成者
            </div>
        </div>

        <div class="hidden sm:flex sm:flex-col w-full">
            @foreach($collection as $item)
                <a href="{{ route('overtime.create', ['date' => $item->date->format('Y-m-d')]) }}"
                   class="flex hover:bg-slate-200">
                    <div class="flex-1 flex flex-col border border-slate-300 p-2">
                        {{ $item->approver?->name }}<br>
                        <span class="text-sm">{{ $item->approved_at?->format('Y年m月d日 H:i:s') }}</span>
                    </div>
                    <div class="flex-1 flex flex-col border border-slate-300 p-2">
                        {{ $item->date->format('Y年m月d日') }}
                    </div>
                    <div class="flex-1 flex flex-col border border-slate-300 p-2">
                        {{ substr($item->time_from, 0, 5) }}
                        〜
                        {{ substr($item->time_until, 0, 5) }}
                    </div>
                    <div class="flex-1 flex flex-col border border-slate-300 p-2">
                        {{ $item->timeDifference }}
                    </div>
                    <div class="flex-1 flex flex-col border border-slate-300 p-2">
                        {{ $item->reason->toString() }}<br>
                        <span class="text-sm">{{ $item->remarks }}</span>
                    </div>
                    <div class="flex-1 flex flex-col border border-slate-300 p-2">
                        {{ $item->applicant?->name }}<br>
                        <span class="text-sm">{{ $item->applied_at?->format('Y年m月d日 H:i:s') }}</span>
                    </div>
                    <div class="flex-1 flex flex-col border border-slate-300 p-2">
                        {{ $item->creator?->name }}
                    </div>
                </a>
            @endforeach
        </div>

        <div class="flex flex-col w-full sm:hidden divide-y divide-slate-200">
            @foreach($collection as $item)
                <a href="{{ route('overtime.create', ['date' => $item->date->format('Y-m-d')]) }}"
                   class="flex flex-col">
                    <div class="p-2 flex justify-between">
                        <div>取得日付 {{ $item->date->format('Y年m月d日') }}</div>
                        <div>{{ substr($item->time_from, 0, 5) }}〜{{ substr($item->time_until, 0, 5) }}</div>
                    </div>
                    @if($item->approver)
                        <div class="p-2">
                            承認済み {{ $item->approver->name }} @ <span
                                class="text-sm">{{ $item->approved_at?->format('Y年m月d日 H:i:s') }}</span>
                        </div>
                    @elseif($item->applicant)
                        <div class="p-2">
                            申請済み {{ $item->applicant->name }} @ <span
                                class="text-sm">{{ $item->applied_at?->format('Y年m月d日 H:i:s') }}</span>
                        </div>
                    @else
                        <div class="p-2">未申請</div>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>
