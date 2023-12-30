<div class="w-full max-w-4xl mx-auto">
    <div class="flex">
        <div class="flex-1">{{ $this->japaneseYear() }}年度 時間外勤務命令簿</div>
        <div class="font-xl">{{ $overtime_confirmation->month }}月分</div>
    </div>

    <div class="flex flex-col mt-4">
        <div class="flex">
            <div class="flex-1 border border-black text-center">{{ $user->name }}</div>
            <div class="flex-1 border border-black text-center">採用年月日</div>
            <div
                class="flex-1 border border-black text-center">{{ $user->userWorkInformation?->employed_at->format('Y-m-d') }}</div>
        </div>

        <div class="flex">
            <div class="flex-1 flex">
                <div class="border border-black text-center flex-1 text-sm">1日の<br>勤務（実務）時間</div>
                <div
                    class="border border-black text-center flex-1">{{ $user->userWorkInformation?->working_hours }}</div>
            </div>
            <div class="flex-1 flex">
                <div class="border border-black text-center flex-1 text-sm">手当計算時<br>勤務時間※１</div>
                <div
                    class="border border-black text-center flex-1">{{ $user->userWorkInformation?->used_working_hours }}</div>
            </div>
            <div class="flex-1 flex">
                <div class="border border-black text-center flex-1 text-sm">給与形態</div>
                <div
                    class="border border-black text-center flex-1">{{ $user->userWorkInformation?->working_system->toString() }}</div>
            </div>
        </div>
    </div>

    <table class="table-fixed w-full mt-4">
        <thead>
        <tr>
            <th class="border border-black">
                施設長<br>
                承認
            </th>
            <th class="border border-black">
                取得日
            </th>
            <th class="border border-black">
                時刻
            </th>
            <th class="border border-black">
                時間
            </th>
            <th class="border border-black">
                事由
            </th>
            <th class="border border-black">
                申請者
            </th>
            <th class="border border-black">
                作成者
            </th>
        </tr>
        </thead>

        <tbody>
        @foreach($overtime_confirmation->overtimes as $overtime)
            <tr>
                <td class="border border-black p-2">
                    {{ $overtime->approver?->name }}<br>
                    <span class="text-sm">{{ $overtime->approved_at?->format('Y年m月d日 H:i:s') }}</span>
                </td>
                <td class="border border-black p-2">
                    {{ $overtime->date->format('Y年m月d日') }}
                </td>
                <td class="border border-black p-2">
                    {{ substr($overtime->time_from, 0, 5) }}
                    〜
                    {{ substr($overtime->time_until, 0, 5) }}
                </td>
                <td class="border border-black p-2">
                    {{ $overtime->timeDifference }}
                </td>
                <td class="border border-black p-2">
                    {{ $overtime->reason->toString() }}<br>
                    <span class="text-sm">{{ Str::limit($overtime->remarks, 30) }}</span>
                </td>
                <td class="border border-black p-2">
                    {{ $overtime->applicant?->name }}<br>
                    <span class="text-sm">{{ $overtime->applied_at?->format('Y年m月d日 H:i:s') }}</span>
                </td>
                <td class="border border-black p-2">
                    {{ $overtime->creator?->name }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="flex mt-4">
        <div class="flex items-center justify-center border border-black p-0.5">
            合計
            <span class="text-sm">※2</span>
        </div>
        <div>
            <div class="border border-black p-0.5">時間外手当①　1日の勤務時間 合計8時間未満　（時給×1.0）</div>
            <div class="border border-black p-0.5">時間外手当②　1日の勤務時間　合計8時間以上（時給×1.25）</div>
            <div class="border border-black p-0.5">時間外手当③　深夜手当　22時～朝5時　（時給×1.5）</div>
            <div class="border border-black p-0.5">時間外手当④　お泊り後 朝5時～　（時給×1.25）</div>
            <div class="border border-black p-0.5">時間外手当⑤　休日出勤　月給職員（時給×1.25）※３</div>
            <div class="border border-black p-0.5">時間外手当⑤　休日出勤　時給職員（時給×1.0）</div>
        </div>
        <div>
            <div class="border border-black p-0.5">TODO</div>
            <div class="border border-black p-0.5">TODO</div>
            <div class="border border-black p-0.5">TODO</div>
            <div class="border border-black p-0.5">TODO</div>
            <div class="border border-black p-0.5">TODO</div>
            <div class="border border-black p-0.5">TODO</div>
        </div>
    </div>

    <div class="mt-4">
        <div class="flex">
            <div class="w-12">※１：</div>
            <div>1日の合計勤務時間が8時間以上になってからの時間外勤務は手当を割増計算する。</div>
        </div>
        <div class="flex">
            <div class="w-12"></div>
            <div>1日の合計勤務が8時間未満の場合、合計勤務時間が8時間に達するまでは割増なしで手当を計算。</div>
        </div>
        <div class="flex">
            <div class="w-12"></div>
            <div>6.5時間勤務の場合、7時間勤務扱いにし、最初の残業1時間は割増なし、それ以降は割増あり。</div>
        </div>
        <div class="flex">
            <div class="w-12">※２：</div>
            <div>給与計算では、合計時間を基に30分切り上げで計算されます。　例）2時間30分⇒3時間　　2時間29分⇒2</div>
        </div>
        <div class="flex">
            <div class="w-12">※３：</div>
            <div>週40時間以上勤務している職員の休日出勤は割増（≒月給職員）、 週40時間未満は割増なし（≒時給職員）</div>
        </div>
    </div>

    <div class="flex mt-4">
        <div class="flex items-center">
            <div class="border-4 border-emerald-500 flex flex-col">
                <div class="bg-emerald-50 text-emerald-900 p-4 text-center">
                    <span class="font-bold">最終確認(申請者 本人）</span><br>
                    当月分の申請は以上になります。
                </div>

                <div class="p-4 text-center">
                    {{ $user->name }}<br>
                    {{ $overtime_confirmation->confirmed_at }}
                </div>
            </div>
        </div>

        <div class="flex flex-1 flex-col gap-2 p-4 w-full sm:w-1/2">
            <div class="flex flex-col">
                <label for="form_remarks">
                    備考欄（当月分）
                </label>
                <textarea disabled id="form_remarks"
                          rows="3" class="form-textarea">{{ $overtime_confirmation->remarks }}</textarea>
            </div>
            <div class="flex flex-col">
                <label for="form_transfer_remarks">
                    引継ぎ欄<span class="text-xs">（ここに記入したことは来月の「備考欄」に反映されます）</span>
                </label>
                <textarea disabled rows="3"
                          class="form-textarea">{{ $overtime_confirmation->transfer_remarks }}</textarea>
            </div>
        </div>
    </div>
</div>
