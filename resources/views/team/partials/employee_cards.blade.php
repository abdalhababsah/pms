<div class="row">
    @forelse($employees as $employee)
        <div class="col-md-6 col-lg-6 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">{{ $employee->first_name }} {{ $employee->last_name }}</h5>
                    <p class="card-text"><strong>ID:</strong> {{ $employee->id }}</p>
                    <p class="card-text"><strong>Email:</strong> {{ $employee->email }}</p>
                    <!-- "Assign" button -->
                    <button type="button" class="btn btn-success assign-btn" data-id="{{ $employee->id }}">
                        Assign
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <p class="text-muted">No employees found.</p>
        </div>
    @endforelse
</div>