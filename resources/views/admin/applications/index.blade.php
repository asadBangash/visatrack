@extends('admin.layout')
@section('title', 'Visa Applications')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Visa Applications</h1>
    <a class="btn btn-primary" href="{{ route('admin.applications.create') }}">Add Application</a>
</div>
<form class="row g-2 mb-3" method="get">
    <div class="col-md-5"><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Search confirmation or name"></div>
    <div class="col-md-4">
        <select class="form-select" name="status">
            <option value="">All statuses</option>
            @foreach ($statuses as $key => $label)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3"><button class="btn btn-outline-secondary w-100" type="submit">Filter</button></div>
</form>
<div class="table-responsive bg-white border rounded">
    <table class="table table-hover mb-0">
        <thead>
        <tr>
            <th>Confirmation</th><th>Applicant</th><th>Passport</th><th>Location</th><th>Status</th><th>Created</th><th class="text-end">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($applications as $application)
            <tr>
                <td>{{ $application->confirmation_number }}</td>
                <td>{{ $application->applicant_name }}</td>
                <td>{{ $application->maskedPassport() }}</td>
                <td>{{ $application->location->display_name }}</td>
                <td>{{ $application->statusLabel() }}</td>
                <td>{{ $application->created_at->format('Y-m-d') }}</td>
                <td class="text-end text-nowrap">
                    <a href="{{ route('admin.applications.show', $application) }}">View</a>
                    <form class="d-inline" method="post" action="{{ route('admin.applications.destroy', $application) }}" onsubmit="return confirm('Delete {{ $application->confirmation_number }}?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-link btn-sm text-danger p-0 ms-2" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $applications->links() }}</div>
@endsection