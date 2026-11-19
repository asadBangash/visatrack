@extends('admin.layout')
@section('title', 'Edit Application')
@section('content')
<h1 class="h3 mb-3">Edit Applicant — {{ $application->confirmation_number }}</h1>
<form method="post" action="{{ route('admin.applications.update', $application) }}" enctype="multipart/form-data" class="card card-body shadow-sm" style="max-width:860px">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label" for="photograph">Replace photograph (optional)</label>
        <input class="form-control" type="file" name="photograph" id="photograph" accept="image/jpeg,image/png">
    </div>
    @include('admin.applications._ds160-fields')
    <div class="mb-3 mt-2">
        <label class="form-label" for="surname_verification_override">Surname verification (5 letters)</label>
        <input class="form-control" name="surname_verification_override" maxlength="5" value="{{ old('surname_verification_override', $application->surname_verification_override) }}">
    </div>
    <button class="btn btn-primary" type="submit">Update</button>
    <a class="btn btn-link" href="{{ route('admin.applications.show', $application) }}">Cancel</a>
</form>
@endsection