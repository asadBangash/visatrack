<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#111;margin:14px}
        .ds160-banner{height:70px;background:#1a3f6b;color:#fff;padding:12px 14px}
        .ds160-banner strong{font-size:18px}
        .ds160-banner span{display:block;font-size:10px;margin-top:4px}
        .ds160-kicker{margin:14px 0 4px;font-size:13px;color:#163265}
        .ds160-title{margin:0 0 8px;font-size:24px;color:#1f2f5c}
        .ds160-lead{margin:0 0 10px}
        .ds160-top{text-align:right;margin-bottom:4px}
        .ds160-top img{height:44px}
        .ds160-panel{background:#e4e4e4;border:1px solid #b8b8b8;padding:8px 10px 0}
        table.ds160-grid{width:100%;border-collapse:collapse}
        table.ds160-grid td{vertical-align:top;padding:4px 6px;font-size:10px}
        .ds160-photo{width:120px;height:150px;object-fit:cover;border:1px solid #999}
        .ds160-label{font-weight:bold}
        .ds160-confirm{font-size:14px;font-weight:bold;padding-top:6px}
        .ds160-version{text-align:right;font-size:10px;border-top:8px solid #b11212;padding:4px 0 5px}
        .ds160-bring{margin:12px 0 4px;line-height:1.4}
        .ds160-bottom img{height:42px}
    </style>
</head>
<body>
@php
    $photoPath = $application->photograph_path
        ? \Illuminate\Support\Facades\Storage::disk('local')->path($application->photograph_path)
        : null;
    $photoDataUri = ($photoPath && is_file($photoPath))
        ? 'data:image/jpeg;base64,'.base64_encode(file_get_contents($photoPath))
        : '';
@endphp
<div class="ds160-banner">
    <strong>U.S. DEPARTMENT <em>of</em> STATE</strong>
    <span>CONSULAR ELECTRONIC APPLICATION CENTER</span>
</div>
<p class="ds160-kicker">Online Nonimmigrant Visa Application (DS-160)</p>
<h1 class="ds160-title">Confirmation</h1>
<p class="ds160-lead">This confirms the submission of the Nonimmigrant visa application for:</p>
<div class="ds160-top">
    <img src="data:image/png;base64,{{ $barcodeBase64 }}" alt="">
</div>
<div class="ds160-panel">
    <table class="ds160-grid">
        <tr>
            <td width="128">
                @if ($photoDataUri)
                    <img class="ds160-photo" src="{{ $photoDataUri }}" alt="">
                @endif
            </td>
            <td width="250">
                <span class="ds160-label">Name Provided:</span> {{ strtoupper($application->applicant_name) }}<br>
                <span class="ds160-label">Date Of Birth:</span> {{ $application->formattedDateOfBirth() }}<br>
                <span class="ds160-label">Place of Birth:</span> {{ $application->place_of_birth }}<br>
                <span class="ds160-label">Gender:</span> {{ $application->genderLabel() }}<br>
                <span class="ds160-label">Nationality:</span> {{ $application->nationality }}<br>
                <span class="ds160-label">Passport Number:</span> {{ $application->passport_number }}<br>
                <span class="ds160-label">Purpose of Travel:</span> {{ strtoupper($application->purpose_of_travel) }}<br>
                <span class="ds160-label">Completed On:</span> {{ $application->formattedCompletedOn() }}<br>
                <span class="ds160-label">Confirmation No:</span>
                <div class="ds160-confirm" style="color:#c40000">{{ $application->confirmation_number }}</div>
            </td>
            <td>
                <span class="ds160-label">Location Selected:</span> {{ $application->location->confirmationLocationLabel() }}<br>
                <span class="ds160-label">Address:</span> {{ $application->confirmationAddressLine() }}
            </td>
        </tr>
    </table>
    <div class="ds160-version">Version {{ config('visa.confirmation_version', '01.02.00') }}</div>
</div>
<p class="ds160-bring">
    <strong>YOU MUST BRING</strong> the confirmation page and the following document(s) with you at all steps during the application process:
    <strong>{{ $application->requiredDocumentsText() }}</strong>
</p>
<p>You may also provide any additional documents you feel will support your case.</p>
<div class="ds160-bottom">
    <img src="data:image/png;base64,{{ $barcodeBase64 }}" alt="">
</div>
</body>
</html>