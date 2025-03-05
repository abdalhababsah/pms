@extends('dashboard-layouts.app')

@section('content')

    <style>
        /* Dashboard Cards */
        .dashboard-card {
            transition: transform 0.2s;
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        /* Icon Styles */
        .icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        /* Custom Tab Styles */
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 1rem 1.5rem;
        }

        .nav-tabs .nav-link.active {
            color: #0d6efd;
            border-bottom: 2px solid #0d6efd;
        }

        /* Timeline Styles */
        .timeline-wrapper {
            padding: 2rem;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .timeline-item {
            position: relative;
            padding: 20px;
            margin-left: 40px;
            border-left: 2px solid #e9ecef;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -7px;
            top: 25px;
            width: 12px;
            height: 12px;
            background: #0d6efd;
            border-radius: 50%;
        }

        /* Progress Bar */
        .progress-custom {
            height: 25px;
            border-radius: 15px;
            background-color: #e9ecef;
        }

        .progress-bar-custom {
            border-radius: 15px;
            transition: width 0.6s ease;
        }
    </style>
    <div class="page-content">
        <div class="container-fluid py-4">
            <!-- Page Title -->
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h3 mb-2 text-gray-800">Dashboard</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Dashboard Cards -->
            <div class="row g-4 mb-4">
                <!-- Working Hours Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="dashboard-card card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-3">Working Hours</h6>
                                    <h4 class="mb-2 fw-bold">
                                        {{ number_format($workHoursData['total_hours'], 1) }}h Today
                                    </h4>
                                    <p class="mb-0 text-muted">
                                        {{-- Example: Weekly hours calculated as daily total * 7 --}}
                                        {{ number_format($workHoursData['total_hours'] * 7, 1) }}h This Week
                                    </p>
                                </div>
                                <div class="icon-wrapper bg-primary bg-opacity-10">
                                    <i data-feather="clock" class="text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Average Feedback Rating Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="dashboard-card card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-3">Average Feedback Rating</h6>
                                    <h4 class="mb-2 fw-bold">
                                        {{ number_format($feedbackStats['average_rating'], 1) }}
                                    </h4>
                                    <p class="mb-0 text-muted">Rating (out of 5)</p>
                                </div>
                                <div class="icon-wrapper bg-info bg-opacity-10">
                                    <i data-feather="star" class="text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fair Feedback Count Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="dashboard-card card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-3">Fair Feedback Count</h6>
                                    <h4 class="mb-2 fw-bold">
                                        {{ $feedbackStats['fair_feedback_count'] }}
                                    </h4>
                                    <p class="mb-0 text-muted">Feedback without unfair flag</p>
                                </div>
                                <div class="icon-wrapper bg-success bg-opacity-10">
                                    <i data-feather="thumbs-up" class="text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Unfair Feedback Count Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="dashboard-card card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-3">Unfair Feedback Count</h6>
                                    <h4 class="mb-2 fw-bold">
                                        {{ $feedbackStats['unfair_feedback_count'] }}
                                    </h4>
                                    <p class="mb-0 text-muted">Marked as unfair</p>
                                </div>
                                <div class="icon-wrapper bg-danger bg-opacity-10">
                                    <i data-feather="thumbs-down" class="text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Work Log Forms + Shift Progress -->
            <div class="row g-4">
                <!-- Shift Progress Card -->
                <div class="col-xl-4">
                    @livewire('shift-progress', ['my_shift' => $my_shift])
                </div>
                <!-- Start/End Work Log Card -->
                <div class="col-xl-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            @if ($isWithinShift)
                                @php
                                    $ongoingLog = $timeline->first(function ($log) {
                                        return is_null($log->end_time) || empty($log->task_description);
                                    });
                                @endphp

                                @if ($ongoingLog)
                                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                        <i data-feather="alert-circle" class="me-2"></i>
                                        You have an ongoing work log. Please complete it before starting a new one.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                <!-- Nav Tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#start"
                                            type="button">
                                            Start Work Log
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#end" type="button">
                                            End Work Log
                                        </button>
                                    </li>
                                </ul>

                                <!-- Tab Content -->
                                <div class="tab-content mt-4">
                                    <!-- Start Work Log Form -->
                                    <div class="tab-pane fade show active" id="start">
                                        <form method="POST" action="{{ route('employee.worklog.start') }}"
                                            class="needs-validation" novalidate>
                                            @csrf
                                            <input type="hidden" name="shift_id" value="{{ $my_shift->id }}">

                                            <div class="mb-3">
                                                <label class="form-label">Project</label>
                                                <select class="form-select" name="project_id" required>
                                                    <option value="">Select Project</option>
                                                    @foreach ($projects as $project)
                                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Please select a project.</div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Project Status</label>
                                                <select class="form-select" name="project_status_id" required>
                                                    <option value="">Select Status</option>
                                                    @foreach ($statuses as $status)
                                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Please select a status.</div>
                                            </div>

                                            <input type="hidden" name="on_outlier" value="0">
                                            <div class="mb-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="on_outlier"
                                                        name="on_outlier" value="1">
                                                    <label class="form-check-label" for="on_outlier">On Outlier</label>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-primary px-4"
                                                {{ $ongoingLog ? 'disabled' : '' }}>
                                                <i data-feather="play-circle" class="me-2"></i>Start Work Log
                                            </button>
                                        </form>
                                    </div>

                                    <!-- End Work Log Form -->
                                    <div class="tab-pane fade" id="end">
                                        <form method="POST" action="{{ route('employee.worklog.end') }}"
                                            class="needs-validation" novalidate>
                                            @csrf
                                            <input type="hidden" name="work_log_id"
                                                value="{{ $activeWorkLog ? $activeWorkLog->id : '' }}">

                                            <div class="mb-3">
                                                <label class="form-label">Task Count</label>
                                                <input type="number" class="form-control" name="task_count" required>
                                                <div class="invalid-feedback">Please enter the task count.</div>
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label">Task Description</label>
                                                <textarea class="form-control" name="task_description" rows="3" required></textarea>
                                                <div class="invalid-feedback">Please provide a task description.</div>
                                            </div>

                                            <button type="submit" class="btn btn-success px-4">
                                                <i data-feather="check-circle" class="me-2"></i>End Work Log
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i data-feather="clock" class="text-muted mb-3"
                                        style="width: 48px; height: 48px;"></i>
                                    <p class="text-muted mb-0">You can start your work log only during your shift hours.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>


            </div>

            <!-- Timeline Section -->
            <div class="timeline-wrapper mt-4">
                <h5 class="mb-4">
                    <i data-feather="calendar" class="me-2"></i>
                    Today's Work Log Timeline
                </h5>

                @php
                    $groupedTimeline = $timeline->groupBy(function ($log) {
                        return \Carbon\Carbon::parse($log->start_time)->format('Y-m-d');
                    });
                @endphp
                @foreach ($groupedTimeline as $date => $logs)
                    <div class="timeline-date mb-3">
                        <h6 class="text-muted">{{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</h6>
                    </div>

                    @foreach ($logs as $log)
                        <div class="timeline-item">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <h6 class="text-muted">On Outlier: {{ $log->on_outlier ? 'Yes' : 'No' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm mb-3">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="icon-wrapper bg-primary bg-opacity-10 me-3">
                                                    <i data-feather="play" class="text-primary"></i>
                                                </div>
                                                <h6 class="mb-0">Start Work Log</h6>
                                            </div>
                                            <p class="mb-2">
                                                <i data-feather="clock" class="me-2"></i>
                                                {{ \Carbon\Carbon::parse($log->start_time)->format('h:i A') }}
                                            </p>
                                            <p class="mb-2">
                                                <i data-feather="briefcase" class="me-2"></i>
                                                {{ $log->project->name ?? 'N/A' }}
                                            </p>
                                            <p class="mb-0">
                                                <i data-feather="info" class="me-2"></i>
                                                {{ $log->projectStatus->name ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm mb-3">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="icon-wrapper bg-success bg-opacity-10 me-3">
                                                    <i data-feather="check" class="text-success"></i>
                                                </div>
                                                <h6 class="mb-0">End Work Log</h6>
                                            </div>
                                            @if ($log->end_time)
                                                <p class="mb-2">
                                                    <i data-feather="clock" class="me-2"></i>
                                                    {{ \Carbon\Carbon::parse($log->end_time)->format('h:i A') }}
                                                </p>
                                                <p class="mb-2">
                                                    <i data-feather="check-square" class="me-2"></i>
                                                    Tasks: {{ $log->task_count }}
                                                </p>
                                                <p class="mb-2">
                                                    <i data-feather="file-text" class="me-2"></i>
                                                    {{ $log->task_description }}
                                                </p>
                                            @else
                                                <div class="text-warning">
                                                    <i data-feather="clock" class="me-2"></i>
                                                    On Progress
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Form validation
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()

        // Counter animation
        document.addEventListener("DOMContentLoaded", function() {
            const counterElements = document.querySelectorAll('.counter-value');

            counterElements.forEach(element => {
                const target = parseInt(element.getAttribute('data-target'));
                let count = 0;
                const duration = 2000; // 2 seconds
                const increment = target / (duration / 16); // 60fps

                const updateCount = () => {
                    if (count < target) {
                        count += increment;
                        element.textContent = Math.round(count);
                        requestAnimationFrame(updateCount);
                    } else {
                        element.textContent = target;
                    }
                };

                updateCount();
            });
        });
    </script>
@endsection
