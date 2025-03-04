@extends('dashboard-layouts.app')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        @include('dashboard-layouts.partials.page-title', [
            'title' => 'Performance Statistics',
            'breadcrumbHome' => 'Dashboard',
            'breadcrumbHomeUrl' => route('dashboard'),
        ])
        @include('components.alerts')

        <div class="row">
            <!-- Profile Card -->
            <div class="col-xl-4 col-lg-5">
                <div class="card">
                    <div class="card-body text-center">
                        <img src="{{ asset('assets/images/UserAvatar.png') }}" alt="Avatar" class="img-fluid rounded-circle mb-3" style="width: 100px;">
                        <h4>{{ $employee->first_name }} {{ $employee->last_name }}</h4>
                        <p class="text-muted">{{ $employee->email }}</p>
                        <hr>
                        <h5>Overall Average Rating</h5>
                        <h3>{{ $overallAvg ? number_format($overallAvg, 1) : 'N/A' }}</h3>
                        <p>Total Feedback: {{ $feedbackCount }}</p>
                    </div>
                </div>
            </div>

            <!-- Charts Column -->
            <div class="col-xl-8 col-lg-7">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Rating Distribution</h5>
                            </div>
                            <div class="card-body">
                                <div id="ratingDistributionChart" style="height: 300px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Overall Feedback Summary</h5>
                            </div>
                            <div class="card-body">
                                <div id="feedbackSummaryChart" style="height: 300px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row: Project Feedback & Work Log Timeline -->
        <div class="row mt-4">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Feedback by Project</h5>
                    </div>
                    <div class="card-body">
                        <div id="projectFeedbackChart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Daily Work Log Timeline</h5>
                        <input type="date" id="timelineFilterDate" class="form-control form-control-sm"
                               value="{{ $workDay ? \Carbon\Carbon::parse($workDay->date)->toDateString() : \Carbon\Carbon::yesterday()->toDateString() }}">
                    </div>
                    <div class="card-body">
                        <!-- Timeline container: content will be loaded via Ajax -->
                        <div id="timelineContainer"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Third Row: Project Feedback Details -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Project Feedback Details</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Project Name</th>
                                    <th>Average Rating</th>
                                    <th>Feedback Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($projectFeedback as $feedback)
                                    <tr>
                                        <td>{{ $feedback->project->name ?? 'N/A' }}</td>
                                        <td>{{ number_format($feedback->avg_rating, 1) }}</td>
                                        <td>{{ $feedback->count }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No feedback available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/libs/echarts/echarts.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Rating Distribution Chart
    var ratingChart = echarts.init(document.getElementById('ratingDistributionChart'));
    var ratingData = {!! json_encode($ratingDistribution) !!};
    var ratingSeries = Object.keys(ratingData).map(key => ({ value: ratingData[key], name: key + ' Star' }));
    ratingChart.setOption({
        tooltip: { trigger: 'item' },
        series: [{ type: 'pie', radius: '70%', data: ratingSeries }]
    });

    // Overall Feedback Summary Chart
    var feedbackChart = echarts.init(document.getElementById('feedbackSummaryChart'));
    feedbackChart.setOption({
        series: [{ type: 'doughnut', data: [{ value: {{ $feedbackCount }}, name: 'Total Feedback' }] }]
    });

    // Feedback by Project Chart
    var projectChart = echarts.init(document.getElementById('projectFeedbackChart'));
    projectChart.setOption({
        xAxis: { type: 'category', data: {!! json_encode($projectFeedback->pluck('project.name')) !!} },
        yAxis: { type: 'value' },
        series: [{ type: 'bar', data: {!! json_encode($projectFeedback->pluck('avg_rating')) !!} }]
    });

    // Function to load the work log timeline via Ajax
    function loadTimeline(filterDate) {
        fetch("{{ route('employee.worklog.timeline', $employee->id) }}?filter_date=" + filterDate)
            .then(response => response.json())
            .then(data => {
                let container = document.getElementById('timelineContainer');
                let html = '';

                if (data.workDay) {
                    html += `<div class="mb-3">
                                <strong>Date:</strong> ${new Date(data.workDay.date).toLocaleDateString()}<br>
                                <strong>Location:</strong> ${data.workDay.location}
                             </div>`;
                    if (data.workLogs && data.workLogs.length > 0) {
                        html += '<ul class="list-group">';
                        data.workLogs.forEach(function(log) {
                            html += `<li class="list-group-item">
                                        <div><strong>Task:</strong> ${log.task_description}</div>
                                        <div class="small text-muted">
                                            Start: ${formatTime(log.start_time)}
                                            ${log.end_time ? ' | End: ' + formatTime(log.end_time) : ''}
                                        </div>
                                     </li>`;
                        });
                        html += '</ul>';
                    } else {
                        html += '<p class="text-muted">No work log entries for this day.</p>';
                    }
                } else {
                    html = '<p class="text-muted">No work day record found for this date.</p>';
                }
                container.innerHTML = html;
            })
            .catch(error => console.error('Error fetching timeline:', error));
    }

    // Utility function to format a time string into hh:mm AM/PM
    function formatTime(dateTimeStr) {
        let date = new Date(dateTimeStr);
        let hours = date.getHours();
        let minutes = date.getMinutes();
        let ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        return hours + ':' + minutes + ' ' + ampm;
    }

    // Initialize the timeline on page load using the current filter date
    let timelineFilterInput = document.getElementById('timelineFilterDate');
    loadTimeline(timelineFilterInput.value);

    // Reload the timeline when the date filter changes
    timelineFilterInput.addEventListener('change', function () {
        loadTimeline(this.value);
    });
});
</script>
@endpush