@props([
    'unlockAt' => null,
    'class' => '',
])

@if($unlockAt)
    @php
        $dt = $unlockAt instanceof \Carbon\CarbonInterface ? $unlockAt : \Illuminate\Support\Carbon::parse($unlockAt);
        $secs = max(0, $dt->diffInSeconds(now()));
        $days = intdiv($secs, 86400);
        $hours = intdiv($secs % 86400, 3600);
        $mins = intdiv($secs % 3600, 60);
        $ssecs = $secs % 60;
        $initial = ($days > 0 ? $days.'d ' : '').$hours.'h '.str_pad($mins, 2, '0', STR_PAD_LEFT).'m '.str_pad($ssecs, 2, '0', STR_PAD_LEFT).'s';
        $label = $dt->format('M j, Y g:i A');
    @endphp
    <span title="Opens {{ $label }}" class="inline-flex items-center gap-1 tabular-nums font-mono whitespace-nowrap {{ $class }}">
        <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span data-countdown="{{ $dt->toIso8601String() }}">{{ $initial }}</span>
    </span>
@endif