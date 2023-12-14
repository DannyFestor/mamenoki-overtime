<div class="w-full flex flex-col gap-8 justify-center items-center">
    <div class="border-8 border-slate-300 bg-slate-100 text-slate-800 text-center w-full max-w-3xl p-4">
        {{ $user->name }} ({{ $user->email }})
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <div class="h-64 w-64 rounded-full border-8 border-rose-300 bg-rose-100 text-rose-800 flex justify-center items-center text-center text-2xl">
                休暇届<br>
                給食なし申請<br>
                COMING SOON
            </div>
        </div>
        <div>
            <a href="{{ route('overtime.index') }}" class="h-64 w-64 rounded-full border-8 border-emerald-300 bg-emerald-100 text-emerald-800 flex justify-center items-center text-center text-2xl transition-all hover:scale-105">
                時間外勤務<br>
                休日出勤<br>
                申請
            </a>
        </div>
        <div>
            <div class="h-64 w-64 rounded-full border-8 border-amber-300 bg-amber-100 text-amber-800 flex justify-center items-center text-center text-2xl">
                給与明細<br>
                源泉徴収<br>
                COMING SOON
            </div>
        </div>
    </div>
</div>
