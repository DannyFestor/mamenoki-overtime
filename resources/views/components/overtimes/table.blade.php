@props(['collection' => []])

<div class="flex flex-col w-full border-2 border-sky-900">
    <div class="text-center px-4 py-2">{{ $slot }}</div>

    <table class="table-fixed w-full">
        <thead>
        <tr>
            <th class="border border-slate-300 p-2">
                施設長<br>
                承認
            </th>
            <th class="border border-slate-300 p-2">取得日</th>
            <th class="border border-slate-300 p-2">時刻</th>
            <th class="border border-slate-300 p-2">時間</th>
            <th class="border border-slate-300 p-2">事由</th>
            <th class="border border-slate-300 p-2">申請者</th>
            <th class="border border-slate-300 p-2">作成者</th>
        </tr>
        </thead>
        <tbody>
        @foreach($collection as $item)
            <tr>
                <td class="border border-slate-300 p-2">
                    {{ $item->approver?->name }}<br>
                    <span class="text-sm">{{ $item->approved_at?->format('Y年m月d日 H:i:s') }}</span>
                </td>
                <td class="border border-slate-300 p-2">
                    {{ $item->date->format('m月d日') }}
                </td>
                <td class="border border-slate-300 p-2">
                    {{ substr($item->time_from, 0, 5) }}
                    〜
                    {{ substr($item->time_until, 0, 5) }}
                </td>
                <td class="border border-slate-300 p-2">
                    {{ $item->timeDifference }}
                </td>
                <td class="border border-slate-300 p-2">
                    {{ $item->reason->toString() }}<br>
                    <span class="text-sm">{{ $item->remarks }}</span>
                </td>
                <td class="border border-slate-300 p-2">
                    {{ $item->applicant?->name }}<br>
                    <span class="text-sm">{{ $item->applied_at?->format('Y年m月d日 H:i:s') }}</span>
                </td>
                <td class="border border-slate-300 p-2">
                    {{ $item->creator?->name }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
