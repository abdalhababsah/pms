<div class="row">
    @if ($teamMembers->isEmpty())
        <div class="col-12">
            <p class="text-muted">No team members assigned.</p>
        </div>
    @else
        @foreach ($teamMembers as $assignment)
            @php
                $employee = $assignment->employee;
                $avgRating = $employee->monthly_average_feedback;
                $feedbackCount = $employee->monthly_feedback_count;

                if (!$avgRating || $avgRating < 1) {
                    $borderClass = 'border-white';
                } elseif ($avgRating < 2.5) {
                    $borderClass = 'border-danger';
                } elseif ($avgRating < 3) {
                    $borderClass = 'border-warning';
                } elseif ($avgRating < 4) {
                    $borderClass = 'border-success';
                } else {
                    $borderClass = 'border-primary';
                }

                $requiresAttention = $avgRating >= 1 && $avgRating <= 2 && $feedbackCount > 15;
            @endphp

            <div class="col-md-4">
                <div class="card mb-3 shadow-sm {{ $borderClass }}" style="border-width: 2px;">
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $employee->first_name }} {{ $employee->last_name }}</h5>
                        <p class="card-text"><strong>ID:</strong> {{ $employee->id }}</p>
                        <p class="card-text"><strong>Email:</strong> {{ $employee->email }}</p>
                        <p class="card-text">
                            <strong>Avg. Rating:</strong>
                            @if ($avgRating)
                                {{ number_format($avgRating, 1) }}
                            @else
                                N/A
                            @endif
                        </p>
                        <p class="card-text"><strong>Feedback Count:</strong> {{ $feedbackCount }}</p>
                        @if ($requiresAttention)
                            <span class="badge bg-danger">Requires Attention</span>
                        @endif

                        <div class="btn-group mt-2" role="group">
                            @if (auth()->user()->role->id == 1)
                                <a href="{{ route('employee.performance.admin', $employee->id) }}"
                                    class="btn btn-outline-primary btn-sm">View Performance</a>
                                <button type="button" class="btn btn-outline-danger btn-sm remove-team-btn"
                                    data-assignment-id="{{ $assignment->id }}">
                                    Remove from Team
                                </button>
                            @elseif(auth()->user()->role->id == 2)
                                <a href="{{ route('employee.performance.teamleader', $employee->id) }}"
                                    class="btn btn-outline-primary btn-sm">View Performance</a>
                                <button type="button" class="btn btn-outline-danger btn-sm remove-team-btn"
                                    data-assignment-id="{{ $assignment->id }}">
                                    Remove from Team
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
