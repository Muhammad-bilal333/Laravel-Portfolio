@extends('Qualityenhancementcell.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('qec.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('qec.failed-plos') }}">
                            <i class="fas fa-chart-line"></i> CRR
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Course Review Report</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('qec.failed-plos') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <!-- Course Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Course Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Course Code:</strong> {{ $crr->course_code }}</p>
                                    <p><strong>Course Name:</strong> {{ $crr->course_name }}</p>
                                    <p><strong>Lecturer:</strong> {{ $crr->lecturer->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Total Students:</strong> {{ $crr->total_students }}</p>
                                    <p><strong>Last Updated:</strong> {{ $crr->last_updated->format('M d, Y H:i') }}</p>
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-{{ $crr->status === 'reviewed' ? 'success' : ($crr->status === 'needs attention' ? 'warning' : 'info') }}">
                                            {{ ucfirst($crr->status) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Failed PLOs -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Failed PLOs</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>PLO</th>
                                            <th>Description</th>
                                            <th>Failure Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($crr->failed_plos as $plo)
                                        <tr>
                                            <td>{{ $plo['code'] }}</td>
                                            <td>{{ $plo['description'] }}</td>
                                            <td>{{ $plo['failure_rate'] }}%</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Lecturer's Comments -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Lecturer's Comments</h5>
                        </div>
                        <div class="card-body">
                            <p>{{ $crr->lecturer_comments ?? 'No comments provided.' }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Actions -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('qec.download-crr', $crr->id) }}" class="btn btn-primary">
                                    <i class="fas fa-download"></i> Download CRR
                                </a>
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                                    <i class="fas fa-edit"></i> Update Status
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- QEC Comments -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">QEC Comments</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('qec.update-crr-status', $crr->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="qec_comments" class="form-label">Add Comments</label>
                                    <textarea class="form-control" id="qec_comments" name="qec_comments" rows="4">{{ $crr->qec_comments }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label">Update Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="pending review" {{ $crr->status === 'pending review' ? 'selected' : '' }}>Pending Review</option>
                                        <option value="reviewed" {{ $crr->status === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                        <option value="needs attention" {{ $crr->status === 'needs attention' ? 'selected' : '' }}>Needs Attention</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update CRR Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="updateStatusForm">
                    <div class="mb-3">
                        <label for="modal_status" class="form-label">Status</label>
                        <select class="form-select" id="modal_status" name="status">
                            <option value="pending review" {{ $crr->status === 'pending review' ? 'selected' : '' }}>Pending Review</option>
                            <option value="reviewed" {{ $crr->status === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                            <option value="needs attention" {{ $crr->status === 'needs attention' ? 'selected' : '' }}>Needs Attention</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveStatus">Save changes</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('saveStatus').addEventListener('click', function() {
    const status = document.getElementById('modal_status').value;
    
    fetch('{{ route('qec.update-crr-status', $crr->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update status');
    });
});
</script>
@endpush
@endsection 