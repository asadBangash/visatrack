@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<h1 class="h3 mb-4">Dashboard</h1>
<div class="row g-3 mb-4">
    @foreach ([['Total', $stats['total'], 'primary'], ['Application Received', $stats['received'], 'secondary'], ['Approved', $stats['approved'], 'success'], ['Issued', $stats['issued'], 'info']] as [$label, $value, $color])
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">{{ $label }}</div>
                    <div class="fs-3 fw-semibold text-{{ $color }}">{{ $value }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>
<div class="row g-4">
    <div class="col-lg-6">
        <h2 class="h5">Recent Applications</h2>
        <div class="table-responsive bg-white border rounded">
            <table class="table table-sm mb-0">
                <thead><tr><th>Confirmation</th><th>Name</th><th>Status</th></tr></thead>
                <tbody>
                @forelse ($recentApplications as $app)
                    <tr>
                        <td><a href="{{ route('admin.applications.show', $app) }}">{{ $app->confirmation_number }}</a></td>
                        <td>{{ $app->applicant_name }}</td>
                        <td>{{ $app->statusLabel() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-muted">No applications yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-6">
        <h2 class="h5">Recent Status Changes</h2>
        <div class="table-responsive bg-white border rounded">
            <table class="table table-sm mb-0">
                <thead><tr><th>Application</th><th>Change</th><th>When</th></tr></thead>
                <tbody>
                @forelse ($recentStatusChanges as $h)
                    <tr>
                        <td>{{ $h->application?->confirmation_number }}</td>
                        <td>{{ $h->previous_status ?? '—' }} → {{ $h->new_status }}</td>
                        <td>{{ $h->created_at?->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-muted">No changes yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection