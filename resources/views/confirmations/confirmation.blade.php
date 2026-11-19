<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DS-160 Confirmation — {{ $application->confirmation_number }}</title>
    <style>
        *{box-sizing:border-box}
        body{margin:0;padding:0;font:12px/1.35 Arial,Helvetica,sans-serif;color:#111;background:#fff}
        .ds160-page{max-width:830px;margin:0 auto;padding:0 10px 28px}
        .ds160-masthead{height:88px;display:flex;align-items:center;gap:12px;padding:0 14px;color:#fff;
            background:
                linear-gradient(90deg,rgba(20,63,100,.95) 0%,rgba(24,55,83,.88) 38%,rgba(155,35,28,.92) 62%,rgba(110,22,18,.96) 100%),
                linear-gradient(180deg,#123a5c 0%,#0a2840 100%)}
        .ds160-masthead-flag-img{width:89px;height:49px;flex:none;display:block;object-fit:contain;border:1px solid rgba(255,255,255,.35)}
        .ds160-masthead-seal-img{width:67px;height:67px;flex:none;display:block;object-fit:contain}
        .ds160-dept{font:700 21px Georgia,'Times New Roman',serif;letter-spacing:.3px;line-height:1.15}
        .ds160-dept em{font-style:italic;font-weight:400;font-size:17px}
        .ds160-ceac{font-size:11px;margin-top:3px;letter-spacing:.15px}
        .ds160-kicker{margin:14px 0 0;font-size:14px;color:#193d8a;font-weight:600}
        .ds160-intro-row{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:8px}
        .ds160-title{margin:4px 0 8px;font:400 28px Georgia,'Times New Roman',serif;color:#1f2f5c}
        .ds160-lead{margin:0;font-size:12px}
        .ds160-top-barcode{flex:0 0 210px;text-align:right;padding-top:6px}
        .ds160-top-barcode img{height:56px;max-width:210px;width:100%;object-fit:contain}
        .ds160-panel{background:#e3e3e3;border:1px solid #b5b5b5;padding:10px 12px 0}
        .ds160-panel-grid{display:grid;grid-template-columns:142px 1.1fr 1fr;gap:12px;align-items:start}
        .ds160-photo{width:132px;height:168px;object-fit:cover;border:1px solid #999;display:block;background:#ddd}
        .ds160-photo--empty{min-height:168px}
        .ds160-fields{margin:0;display:grid;grid-template-columns:max-content 1fr;column-gap:8px;row-gap:3px;font-size:11px;line-height:1.35}
        .ds160-fields dt{font-weight:700;margin:0}
        .ds160-fields dd{margin:0;font-weight:400}
        .ds160-confirm-label{align-self:end}
        .ds160-confirm-no{grid-column:2;font-size:17px;font-weight:700;letter-spacing:.6px;color:#c40000}
        .ds160-panel-footer{display:flex;justify-content:space-between;align-items:center;margin:12px -12px 0;padding:0;
            border-top:11px solid #b11212;background:#e3e3e3}
        .ds160-panel-footer-tag{background:#b11212;color:#fff;font-size:10px;font-weight:700;padding:5px 8px;min-height:24px;display:flex;align-items:center}
        .ds160-panel-version{font-size:11px;padding:4px 10px 6px;color:#222}
        .ds160-bring{margin:14px 0 6px;font-size:11px;line-height:1.45;max-width:700px}
        .ds160-bring-note{margin:0 0 14px;font-size:11px;color:#333;max-width:700px}
        .ds160-bottom-barcode img{height:52px;max-width:100%}
        .no-print{text-align:center;margin-top:18px}
        @media print{.no-print{display:none}body{padding:0}.ds160-page{max-width:none;padding:0}}
        @media(max-width:700px){
            .ds160-intro-row{flex-direction:column}
            .ds160-top-barcode{text-align:left;flex-basis:auto}
            .ds160-panel-grid{grid-template-columns:1fr}
        }
    </style>
</head>
<body>
<div class="ds160-page">
    @include('confirmations.partials.ds160-sheet', ['photoDataUri' => $photoDataUri ?? null])
</div>
<div class="no-print">
    <button type="button" onclick="window.print()">Print</button>
</div>
</body>
</html>