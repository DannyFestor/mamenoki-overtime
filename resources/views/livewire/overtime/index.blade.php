<div class="w-full max-w-7xl mx-auto flex flex-col gap-4">
    <div class="w-full flex justify-between border-2 border-sky-900 p-4">
        <div>{{ $name }}</div>
        <div class="flex gap-2">
            <x-svgs.chevron-left wire:click="decreaseYear" class="cursor-pointer" />
            {{ $year }}年度 時間外勤務命令簿
            <x-svgs.chevron-right wire:click="increaseYear" class="cursor-pointer" />
        </div>
        <div class="flex gap-2">
            <x-svgs.chevron-left wire:click="decreaseMonth" class="cursor-pointer" />
            {{ $month }}月分
            <x-svgs.chevron-right wire:click="increaseMonth" class="cursor-pointer" />
        </div>
    </div>
</div>
