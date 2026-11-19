@extends('admin.layout')
@section('title', $application->confirmation_number)
@section('content')
@if (session('status_check_hint'))
    @php($hint = session('status_check_hint'))
    <div class="alert alert-info small">
        Track this case on the CEAC landing page: open
        <a href="{{ $hint['url'] }}&amp;cn={{ urlencode($hint['confirmation_number']) }}&amp;location_id={{ $application->location_id }}" target="_blank" rel="noopener">Check My Visa Application Status</a>
        and enter confirmation <strong>{{ $hint['confirmation_number'] }}</strong>, location <strong>{{ $hint['location'] }}</strong>, passport, and surname letters <strong>{{ $hint['surname'] }}</strong> (family name, first 5 letters).
    </div>
@endif
<div class="d-flex flex-wrap gap-2 mb-3">
    <h1 class="h3 mb-0">{{ $application->confirmation_number }}</h1>
    <span class="badge text-bg-secondary align-self-center">{{ $application->statusLabel() }}</span>
</div>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <img class="img-fluid border" src="{{ route('admin.applications.photograph', $application) }}" alt="Applicant photo">
                    </div>
                    <div class="col-md-8">
                        <dl class="row mb-0 small">
                            <dt class="col-sm-4">Name</dt><dd class="col-sm-8">{{ $application->applicant_name }}</dd>
                            <dt class="col-sm-4">Date of birth</dt><dd class="col-sm-8">{{ $application->formattedDateOfBirth() ?: '—' }}</dd>
                            <dt class="col-sm-4">Place of birth</dt><dd class="col-sm-8">{{ $application->place_of_birth ?: '—' }}</dd>
                            <dt class="col-sm-4">Gender</dt><dd class="col-sm-8">{{ $application->gender ? $application->genderLabel() : '—' }}</dd>
                            <dt class="col-sm-4">Nationality</dt><dd class="col-sm-8">{{ $application->nationality ?: '—' }}</dd>
                            <dt class="col-sm-4">Passport</dt><dd class="col-sm-8">{{ $application->maskedPassport() }}</dd>
                            <dt class="col-sm-4">Purpose of travel</dt><dd class="col-sm-8">{{ $application->purpose_of_travel ?: '—' }}</dd>
                            <dt class="col-sm-4">Completed on</dt><dd class="col-sm-8">{{ $application->formattedCompletedOn() }}</dd>
                            <dt class="col-sm-4">Location</dt><dd class="col-sm-8">{{ $application->location->confirmationLocationLabel() }}</dd>
                            <dt class="col-sm-4">Visa type</dt><dd class="col-sm-8">{{ $application->visa_type }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm mt-3">
            <div class="card-header">Status history</div>
            <ul class="list-group list-group-flush">
                @foreach ($application->statusHistories as $history)
                    <li class="list-group-item small">
                        {{ $history->created_at->format('Y-m-d H:i') }} — {{ $history->previous_status ?? 'new' }} → {{ $history->new_status }}
                        ({{ $history->changer->name }})
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body d-grid gap-2">
                <a class="btn btn-outline-primary" href="{{ route('admin.applications.confirmation', $application) }}" target="_blank">View Confirmation</a>
                <a class="btn btn-outline-primary" href="{{ route('admin.applications.confirmation', $application) }}" target="_blank" onclick="window.open(this.href); return false;">Print Confirmation</a>
                <a class="btn btn-outline-primary" href="{{ route('admin.applications.pdf', $application) }}">Download PDF</a>
                <a class="btn btn-outline-secondary" href="{{ route('admin.applications.edit', $application) }}">Edit Applicant</a>
            </div>
        </div>
        <form class="card shadow-sm mt-3" method="post" action="{{ route('admin.applications.status', $application) }}">
            @csrf
            @method('PATCH')
            <div class="card-header">Change Status</div>
            <div class="card-body">
                <select class="form-select" name="status">
                    @foreach ($statuses as $key => $label)
                        <option value="{{ $key }}" @selected($application->status === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="card-footer"><button class="btn btn-sm btn-primary w-100" type="submit">Save status</button></div>
        </form>
        <div class="text-center mt-3">
            <img src="data:image/png;base64,{{ $barcodeBase64 }}" alt="Barcode" class="img-fluid" style="max-height:60px">
        </div>
    </div>
</div>
@endsection