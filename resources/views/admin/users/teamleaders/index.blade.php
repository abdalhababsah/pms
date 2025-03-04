@extends('dashboard-layouts.app')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title -->
            @include('dashboard-layouts.partials.page-title', [
                'title' => 'Team Leaders',
                'breadcrumbHome' => 'Dashboard',
                'breadcrumbHomeUrl' => route('dashboard'),
                'breadcrumbItems' => [['name' => 'Team Leaders', 'url' => route('admin.teamLeaders')]],
            ])
            @include('components.alerts')
            <!-- Button to open Create Team Leader Modal -->
            <div class="text-end mb-3">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createTeamLeaderModal">
                    Create Team Leader
                </button>
            </div>
            <!-- Team Leaders Cards -->
            <div class="row">
                @foreach ($teamLeaders as $teamLeader)
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Id: {{ $teamLeader->id }}</h5>
                                <h6 class="card-text">{{ $teamLeader->first_name }} {{ $teamLeader->last_name }}</h5>
                                <p class="card-text">{{ $teamLeader->email }}</p>
                                <div class="d-flex justify-content-end gap-2">
                                    <!-- View Team Button -->
                                    <a href="{{ route('admin.teamLeaders.viewTeam', $teamLeader->id) }}"
                                        class="btn btn-info btn-sm">
                                        View Team
                                    </a>
                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-primary btn-sm edit-btn"
                                        data-id="{{ $teamLeader->id }}" data-first_name="{{ $teamLeader->first_name }}"
                                        data-last_name="{{ $teamLeader->last_name }}" data-email="{{ $teamLeader->email }}"
                                        data-role="{{ $teamLeader->role_id }}" data-bs-toggle="modal"
                                        data-bs-target="#editTeamLeaderModal">
                                        Edit
                                    </button>
                                    <!-- Delete Form -->
                                    <form action="{{ route('admin.users.destroy', $teamLeader->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this team leader?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if ($teamLeaders->isEmpty())
                    <div class="col-12">
                        <p class="text-center">No team leaders found.</p>
                    </div>
                @endif
            </div>


        </div>
    </div>

    <!-- Create Team Leader Modal -->
    <div class="modal fade" id="createTeamLeaderModal" tabindex="-1" aria-labelledby="createTeamLeaderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createTeamLeaderModalLabel">Create Team Leader</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- First Name -->
                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <!-- Last Name -->
                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <!-- Hidden Role Input: Always set to Team Leader (role_id = 2) -->
                        <input type="hidden" name="role_id" value="2">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Create Team Leader</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- End Create Team Leader Modal -->

    <!-- Edit Team Leader Modal -->
    <div class="modal fade" id="editTeamLeaderModal" tabindex="-1" aria-labelledby="editTeamLeaderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="editTeamLeaderForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Team Leader</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Hidden ID -->
                        <input type="hidden" id="edit-teamleader-id" name="id">
                        <!-- First Name -->
                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" id="edit-teamleader-first-name" name="first_name"
                                required>
                        </div>
                        <!-- Last Name -->
                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="edit-teamleader-last-name" name="last_name"
                                required>
                        </div>
                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit-teamleader-email" name="email"
                                required>
                        </div>
                        <!-- Checkbox to enable role update -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="enableTeamLeaderRoleUpdate">
                            <label class="form-check-label" for="enableTeamLeaderRoleUpdate">Update Role</label>
                        </div>
                        <!-- Role Select, initially disabled -->
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" id="edit-teamleader-role" name="role_id" disabled>
                                <option value="1">Admin</option>
                                <option value="2">Team Leader</option>
                                <option value="3">Employee</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Team Leader</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- End Edit Team Leader Modal -->
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // When an edit button is clicked, populate the edit modal with team leader data.
            $('.edit-btn').click(function() {
                var id = $(this).data('id');
                var firstName = $(this).data('first_name');
                var lastName = $(this).data('last_name');
                var email = $(this).data('email');
                var roleId = $(this).data('role'); // Should be 2 for team leaders by default

                $('#edit-teamleader-id').val(id);
                $('#edit-teamleader-first-name').val(firstName);
                $('#edit-teamleader-last-name').val(lastName);
                $('#edit-teamleader-email').val(email);
                // Set the role and disable the select initially.
                $('#edit-teamleader-role').val(roleId).prop('disabled', true);
                $('#enableTeamLeaderRoleUpdate').prop('checked', false);

                // Update the form action.
                $('#editTeamLeaderForm').attr('action', '/admin/users/' + id);
            });

            // Toggle role select enable/disable on checkbox change.
            $('#enableTeamLeaderRoleUpdate').change(function() {
                $('#edit-teamleader-role').prop('disabled', !$(this).is(':checked'));
            });
        });
    </script>
@endpush
