@php
    $version = config('visa.confirmation_version', '01.02.00');
    $photoSrc = $photoDataUri ?? null;
    $passportDisplay = $application->passport_number;
@endphp
@include('confirmations.partials.ds160-masthead')

<p class="ds160-kicker">Online Nonimmigrant Visa Application (DS-160)</p>

<div class="ds160-intro-row">
    <div class="ds160-intro-copy">
        <h1 class="ds160-title">Confirmation</h1>
        <p class="ds160-lead">This confirms the submission of the Nonimmigrant visa application for:</p>
    </div>
    <div class="ds160-top-barcode">
        <img src="data:image/png;base64,{{ $barcodeBase64 }}" alt="Confirmation barcode">
    </div>
</div>

<div class="ds160-panel">
    <div class="ds160-panel-grid">
        <div class="ds160-photo-wrap">
            @if ($photoSrc)
                <img class="ds160-photo" src="{{ $photoSrc }}" alt="Applicant photograph">
            @else
                <div class="ds160-photo ds160-photo--empty" aria-hidden="true"></div>
            @endif
        </div>
        <dl class="ds160-fields">
            <dt>Name Provided:</dt><dd>{{ strtoupper($application->applicant_name) }}</dd>
            <dt>Date Of Birth:</dt><dd>{{ $application->formattedDateOfBirth() }}</dd>
            <dt>Place of Birth:</dt><dd>{{ $application->place_of_birth }}</dd>
            <dt>Gender:</dt><dd>{{ $application->genderLabel() }}</dd>
            <dt>Nationality:</dt><dd>{{ $application->nationality }}</dd>
            <dt>Passport Number:</dt><dd>{{ $passportDisplay }}</dd>
            <dt>Purpose of Travel:</dt><dd>{{ strtoupper($application->purpose_of_travel) }}</dd>
            <dt>Completed On:</dt><dd>{{ $application->formattedCompletedOn() }}</dd>
            <dt class="ds160-confirm-label">Confirmation No:</dt>
            <dd class="ds160-confirm-no">{{ $application->confirmation_number }}</dd>
        </dl>
        <dl class="ds160-fields ds160-fields--location">
            <dt>Location Selected:</dt><dd>{{ $application->location->confirmationLocationLabel() }}</dd>
            <dt>Address:</dt><dd>{{ $application->confirmationAddressLine() }}</dd>
        </dl>
    </div>
    <div class="ds160-panel-footer">
        <div class="ds160-panel-footer-tag">APPLICATION CONFIRMATION PAGE</div>
        <div class="ds160-panel-version">Version {{ $version }}</div>
    </div>
</div>

<p class="ds160-bring">
    <strong>YOU MUST BRING</strong> the confirmation page and the following document(s) with you at all steps during the application process:
    <strong>{{ $application->requiredDocumentsText() }}</strong>
</p>
<p class="ds160-bring-note">You may also provide any additional documents you feel will support your case.</p>

<div class="ds160-bottom-barcode">
    <img src="data:image/png;base64,{{ $barcodeBase64 }}" alt="Confirmation barcode">
</div>