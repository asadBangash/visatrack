@extends('admin.layout')
@section('title', 'Add Visa Application')
@section('content')
<h1 class="h3 mb-3">Add Visa Application (DS-160)</h1>
<p class="text-muted">Capture applicant details as they appear on the DS-160 confirmation page. A U.S.-style confirmation number and Code 128 barcodes are generated automatically.</p>
<form method="post" action="{{ route('admin.applications.store') }}" enctype="multipart/form-data" class="card card-body shadow-sm" style="max-width:860px">
    @csrf
    <div class="mb-3">
        <label class="form-label" for="photograph">Applicant photograph</label>
        <input class="form-control" type="file" name="photograph" id="photograph" accept="image/jpeg,image/png" required>
    </div>
    @include('admin.applications._ds160-fields', ['application' => null])
    <details class="mb-3 mt-2">
        <summary class="small text-muted">Optional: set surname verification (first 5 letters)</summary>
        <input class="form-control mt-2" name="surname_verification_override" maxlength="5" value="{{ old('surname_verification_override') }}" placeholder="Leave blank to use configurable strategy">
    </details>
    <button class="btn btn-primary" type="submit">Save application</button>
</form>
@endsection