<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="applicant_name">Name provided (as on application)</label>
        <input class="form-control" name="applicant_name" id="applicant_name" value="{{ old('applicant_name', optional($application)->applicant_name) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="passport_number">Passport number</label>
        <input class="form-control" name="passport_number" id="passport_number" value="{{ old('passport_number', optional($application)->passport_number) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="date_of_birth">Date of birth</label>
        <input class="form-control" type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', isset($application) && $application->date_of_birth ? $application->date_of_birth->format('Y-m-d') : '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="gender">Gender</label>
        <select class="form-select" name="gender" id="gender" required>
            @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                <option value="{{ $value }}" @selected(old('gender', optional($application)->gender) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="nationality">Nationality</label>
        <input class="form-control" name="nationality" id="nationality" value="{{ old('nationality', optional($application)->nationality) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="place_of_birth">Place of birth</label>
        <input class="form-control" name="place_of_birth" id="place_of_birth" value="{{ old('place_of_birth', optional($application)->place_of_birth) }}" placeholder="City, Country" required>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="purpose_of_travel">Purpose of travel</label>
        <input class="form-control" name="purpose_of_travel" id="purpose_of_travel" value="{{ old('purpose_of_travel', optional($application)->purpose_of_travel) }}" placeholder="DRIVER OCCUPATION (A2)" required>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="completed_on">Completed on (confirmation date)</label>
        <input class="form-control" type="date" name="completed_on" id="completed_on" value="{{ old('completed_on', isset($application) && $application->completed_on ? $application->completed_on->format('Y-m-d') : now()->format('Y-m-d')) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="location_id">Consular location</label>
        <select class="form-select" name="location_id" id="location_id" required>
            <option value="">Select location</option>
            @foreach ($locations as $location)
                <option value="{{ $location->id }}" @selected(old('location_id', optional($application)->location_id) == $location->id)>{{ $location->confirmationLocationLabel() }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label" for="confirmation_address">Address (on confirmation page)</label>
        <textarea class="form-control" name="confirmation_address" id="confirmation_address" rows="2" placeholder="Leave blank to use the consulate street address for the selected location">{{ old('confirmation_address', optional($application)->confirmation_address) }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label" for="required_documents">Documents to bring (shown on confirmation page)</label>
        <textarea class="form-control" name="required_documents" id="required_documents" rows="2" placeholder="{{ config('visa.default_required_documents') }}">{{ old('required_documents', optional($application)->required_documents) }}</textarea>
    </div>
</div>