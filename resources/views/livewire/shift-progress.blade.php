<div wire:poll.1000ms>
    @php
        $progressData = $this->progress;
    @endphp

    <div class="progress progress-custom mb-3">
        <div class="progress-bar progress-bar-custom bg-success" 
             role="progressbar" 
             style="width: {{ $progressData['progress'] }}%"
             aria-valuenow="{{ $progressData['progress'] }}" 
             aria-valuemin="0" 
             aria-valuemax="100">
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted">Progress</span>
        <span class="fw-bold">{{ number_format($progressData['progress'], 1) }}%</span>
    </div>

    <hr class="my-3">

    <div class="text-center">
        @if($progressData['now']->lt($progressData['shiftStart']))
            <span class="badge bg-warning">Shift not started</span>
        @elseif($progressData['now']->between($progressData['shiftStart'], $progressData['shiftEnd']))
            <span class="badge bg-success">
                Elapsed: {{ gmdate("H:i:s", $progressData['elapsed']) }} / {{ gmdate("H:i:s", $progressData['shiftDuration']) }}
            </span>
        @else
            <span class="badge bg-secondary">Shift complete</span>
        @endif
    </div>
</div>