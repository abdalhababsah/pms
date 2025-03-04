@extends('dashboard-layouts.app')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title -->
            @include('dashboard-layouts.partials.page-title', [
                'title' => 'Employees',
                'breadcrumbHome' => 'Dashboard',
                'breadcrumbHomeUrl' => route('dashboard'),
                'breadcrumbItems' => [
                    ['name' => 'Users', 'url' => route('admin.employees')],
                ]
            ])
            @include('components.alerts')

            <!-- Filters Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Filter Employees</h4>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('admin.employees') }}">
                                <div class="row g-3 align-items-center">
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <div class="mb-3 mb-lg-0">
                                            <input type="text" 
                                                   name="name" 
                                                   class="form-control" 
                                                   placeholder="Search by name" 
                                                   value="{{ request('name') }}">
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <div class="mb-3 mb-lg-0">
                                            <input type="number" 
                                                   name="id" 
                                                   class="form-control" 
                                                   placeholder="Search by ID" 
                                                   value="{{ request('id') }}">
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <div class="mb-3 mb-lg-0">
                                            <input type="text" 
                                                   name="email" 
                                                   class="form-control" 
                                                   placeholder="Search by Email" 
                                                   value="{{ request('email') }}">
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <div class="d-flex justify-content-lg-end gap-2">
                                            <button type="submit" class="btn btn-primary">Apply</button>
                                            <a href="{{ route('admin.employees') }}" 
                                               class="btn btn-outline-secondary">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Filters Card -->

            <!-- Employees Table -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Employees</h4>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
                                Create Employee
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Full Name</th>
                                            <th>Email</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($employees as $employee)
                                            <tr>
                                                <th scope="row">{{ $employee->id }}</th>
                                                <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                                <td>{{ $employee->email }}</td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-primary btn-sm edit-btn"
                                                        data-id="{{ $employee->id }}" 
                                                        data-first_name="{{ $employee->first_name }}"
                                                        data-last_name="{{ $employee->last_name }}"
                                                        data-email="{{ $employee->email }}"
                                                        data-role="{{ $employee->role_id }}" 
                                                        data-bs-toggle="modal" data-bs-target="#editEmployeeModal">
                                                        Edit
                                                    </button>

                                                    <form action="{{ route('admin.users.destroy', $employee->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm delete-btn">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if ($employees->isEmpty())
                                            <tr>
                                                <td colspan="4" class="text-center">No employees found.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <div class="mt-3">
                                    {{ $employees->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- container-fluid -->
    </div>
    <!-- End Page-content -->

    <!-- Create Employee Modal -->
    <div class="modal fade" id="createEmployeeModal" tabindex="-1" aria-labelledby="createEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createEmployeeModalLabel">Create Employee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <!-- Hidden input for role, always set to 3 (Employee) -->
                        <input type="hidden" name="role_id" value="3">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Create Employee</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- End Create Employee Modal -->

    <!-- Edit Employee Modal -->
    <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editEmployeeForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Employee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit-employee-id" name="id">
                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" id="edit-employee-first-name" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="edit-employee-last-name" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit-employee-email" name="email" required>
                        </div>
                        <!-- Checkbox to enable role update -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="enableRoleUpdate">
                            <label class="form-check-label" for="enableRoleUpdate">Update Role</label>
                        </div>
                        <!-- Role select, initially disabled -->
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" id="edit-employee-role" name="role_id" disabled>
                                <option value="1">Admin</option>
                                <option value="2">Team Leader</option>
                                <option value="3">Employee</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Employee</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- End Edit Employee Modal -->
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // When an edit button is clicked, fill the modal form with employee data.
            $('.edit-btn').click(function() {
                var employeeId = $(this).data('id');
                var firstName = $(this).data('first_name');
                var lastName = $(this).data('last_name');
                var email = $(this).data('email');
                var roleId = $(this).data('role');

                $('#edit-employee-id').val(employeeId);
                $('#edit-employee-first-name').val(firstName);
                $('#edit-employee-last-name').val(lastName);
                $('#edit-employee-email').val(email);

                // Set role select value and disable it initially.
                $('#edit-employee-role').val(roleId).prop('disabled', true);
                $('#enableRoleUpdate').prop('checked', false);

                // Update the form action dynamically.
                $('#editEmployeeForm').attr('action', '/admin/users/' + employeeId);
            });

            // Enable or disable the role select when checkbox is toggled.
            $('#enableRoleUpdate').change(function() {
                if ($(this).is(':checked')) {
                    $('#edit-employee-role').prop('disabled', false);
                } else {
                    $('#edit-employee-role').prop('disabled', true);
                }
            });
        });
    </script>
@endpush