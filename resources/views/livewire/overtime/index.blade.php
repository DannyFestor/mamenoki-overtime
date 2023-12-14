<div class="w-full max-w-7xl mx-auto flex flex-col gap-4">
    <div class="w-full flex justify-between border-2 border-sky-900 p-4">
        <div>{{ $name }}</div>
        <div class="flex gap-2">
            <x-svgs.chevron-left wire:click="decreaseYear" class="cursor-pointer" />
            {{ $this->japaneseYear() }}年度 時間外勤務命令簿
            <x-svgs.chevron-right wire:click="increaseYear" class="cursor-pointer" />
        </div>
        <div class="flex gap-2">
            <x-svgs.chevron-left wire:click="decreaseMonth" class="cursor-pointer" />
            {{ $month }}月分
            <x-svgs.chevron-right wire:click="increaseMonth" class="cursor-pointer" />
        </div>
    </div>
    <div class="w-full flex">
        <div class="flex flex-col border border-2 border-sky-900">
            @if(!$this->hasCurrentYear())
                <a wire:navigate
                   href="{{ route('overtime.index', ['year' => now()->year, 'month' => now()->month]) }}"
                   class="px-4 py-2 hover:bg-sky-200"
                >
                    {{ now()->year }}年{{ str_pad(now()->month, 2, '0', STR_PAD_LEFT) }}月
                </a>
            @endif
            @foreach($overtimeConfirmations as $overtimeConfirmation)
                <a wire:navigate
                   href="{{ route('overtime.index', ['year' => $overtimeConfirmation['year'], 'month' => $overtimeConfirmation['month']]) }}"
                   class="px-4 py-2 hover:bg-sky-200"
                >
                    {{ $overtimeConfirmation['year'] }}年{{ str_pad($overtimeConfirmation['month'], 2, '0', STR_PAD_LEFT) }}月
                </a>
            @endforeach
        </div>
    </div>
</div>
