@extends('dashboard-layouts.app')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- Alert Container (for custom alerts) -->
        <div class="alert-container"></div>

        <!-- Page Title -->
        @include('dashboard-layouts.partials.page-title', [
            'title' => 'View Team',
            'breadcrumbHome' => 'Dashboard',
            'breadcrumbHomeUrl' => route('dashboard'),
            'breadcrumbItems' => [
                ['name' => 'Team Leaders', 'url' => route('admin.teamLeaders')],
                ['name' => 'View Team', 'url' => '#'],
            ],
        ])
        @include('components.alerts')

        <div class="card">
            <div class="card-header">
                <h4>Team Leader: {{ $teamLeader->first_name }} {{ $teamLeader->last_name }}</h4>
                <!-- Button to trigger offcanvas panel -->
                <button class="btn btn-info float-end" type="button" data-bs-toggle="offcanvas" data-bs-target="#employeeAssignCanvas" aria-controls="employeeAssignCanvas">
                    Assign Team Members
                </button>
            </div>
            <div class="card-body" id="teamMembersContainer">
                <div class="card-body" id="teamMembersContainer">
                    @include('team.partials.team_members', ['teamMembers' => $teamMembers])
                </div>
            </div>
        </div>

    </div> <!-- container-fluid -->
</div>

<!-- Offcanvas: Assign Team Members -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="employeeAssignCanvas" aria-labelledby="employeeAssignCanvasLabel">
    <div class="offcanvas-header">
        <h5 id="employeeAssignCanvasLabel">Assign Team Members</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <!-- Search input to filter employees -->
        <div class="mb-3">
            <input type="text" id="employeeSearchInput" class="form-control" placeholder="Search by name, email or ID">
        </div>
        <!-- Container for employee cards (returned via AJAX) -->
        <div id="employeeSearchResults">
            <!-- Initial content can be empty -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Custom alert function using Bootstrap alert elements.
function showAlert(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.role = "alert";
    alertDiv.innerHTML = `${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;
    document.querySelector('.alert-container').appendChild(alertDiv);
    setTimeout(() => {
        alertDiv.classList.remove('show');
        alertDiv.remove();
    }, 3000);
}

document.addEventListener('DOMContentLoaded', function () {
    // Offcanvas search functionality.
    const searchInput = document.getElementById('employeeSearchInput');
    const resultsContainer = document.getElementById('employeeSearchResults');
    let timeout = null;

    searchInput.addEventListener('input', function () {
        clearTimeout(timeout);
        timeout = setTimeout(function () {
            const query = searchInput.value;
            if(query.length > 0) {
                fetch("{{ route('team.employees.search') }}?q=" + encodeURIComponent(query) + "&team_leader_id={{ $teamLeader->id }}")
                    .then(response => response.text())
                    .then(html => {
                        resultsContainer.innerHTML = html;
                        attachAssignEventHandlers();
                    })
                    .catch(error => {
                        console.error('Error fetching employees:', error);
                        resultsContainer.innerHTML = '<p class="text-danger">An error occurred.</p>';
                    });
            } else {
                resultsContainer.innerHTML = '';
            }
        }, 300);
    });

    // Function to attach event handlers to assign buttons.
    function attachAssignEventHandlers() {
        document.querySelectorAll('.assign-btn').forEach(button => {
            button.addEventListener('click', function () {
                const employeeId = this.getAttribute('data-id');
                fetch("{{ route('admin.teamLeaders.assignTeam', $teamLeader->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ employee_id: employeeId })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        showAlert('Team member assigned successfully.', 'success');
                        this.closest('.col-md-6').remove();
                        refreshTeamMembers();
                    } else {
                        showAlert(data.error || 'An error occurred.', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error assigning team member:', error);
                    showAlert('An error occurred.', 'danger');
                });
            });
        });
    }

    // Function to attach event handlers to remove buttons using SweetAlert2 for confirmation.
    function attachRemoveEventHandlers() {
        document.querySelectorAll('.remove-team-btn').forEach(button => {
            button.addEventListener('click', function () {
                const assignmentId = this.getAttribute('data-assignment-id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you really want to remove this team member?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, remove it!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'btn btn-danger me-2',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch("{{ route('team.teamMember.remove', '') }}/" + assignmentId, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if(data.success) {
                                showAlert('Team member removed successfully.', 'success');
                                refreshTeamMembers();
                            } else {
                                showAlert(data.error || 'An error occurred.', 'danger');
                            }
                        })
                        .catch(error => {
                            console.error('Error removing team member:', error);
                            showAlert('An error occurred.', 'danger');
                        });
                    }
                });
            });
        });
    }

    // Function to refresh team members list via AJAX.
    function refreshTeamMembers() {
        fetch("{{ route('team.members.get', $teamLeader->id) }}")
            .then(response => response.text())
            .then(html => {
                document.getElementById('teamMembersContainer').innerHTML = html;
                attachRemoveEventHandlers();
            })
            .catch(error => console.error('Error refreshing team members:', error));
    }

    // Initial attach for remove buttons.
    attachRemoveEventHandlers();
});
</script>
@endpush