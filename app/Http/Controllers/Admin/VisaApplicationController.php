<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVisaApplicationRequest;
use App\Http\Requests\Admin\UpdateStatusRequest;
use App\Http\Requests\Admin\UpdateVisaApplicationRequest;
use App\Models\Location;
use App\Models\VisaApplication;
use App\Services\AuditLogService;
use App\Services\BarcodeService;
use App\Services\ConfirmationPdfService;
use App\Services\VisaApplicationService;
use App\Services\VisaStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VisaApplicationController extends Controller
{
    public function __construct(
        private readonly VisaApplicationService $applicationService,
        private readonly VisaStatusService $statusService,
        private readonly BarcodeService $barcodeService,
        private readonly ConfirmationPdfService $pdfService,
        private readonly AuditLogService $auditLogService,
    ) {}

    public function index(Request $request)
    {
        $query = VisaApplication::query()->with('location');

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('confirmation_number', 'like', "%{$search}%")
                    ->orWhere('applicant_name', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $applications = $query->latest()->paginate(15)->withQueryString();

        return view('admin.applications.index', [
            'applications' => $applications,
            'statuses' => config('visa.statuses'),
        ]);
    }

    public function create()
    {
        $locations = Location::query()->where('is_active', true)->orderBy('display_name')->get();

        return view('admin.applications.create', compact('locations'));
    }

    public function store(StoreVisaApplicationRequest $request)
    {
        $application = $this->applicationService->create(
            $request->safe()->except('photograph'),
            $request->file('photograph'),
            $request->user(),
        );

        $surnameHint = $application->surname_verification_override
            ?: app(\App\Services\SurnameVerificationService::class)->tokenFromFullName($application->applicant_name);

        return redirect()
            ->route('admin.applications.show', $application)
            ->with('success', 'Application saved. Confirmation number: '.$application->confirmation_number)
            ->with('status_check_hint', [
                'confirmation_number' => $application->confirmation_number,
                'location' => $application->location->display_name,
                'surname' => $surnameHint,
                'url' => route('visa-status', ['type' => 'NIV']),
            ]);
    }

    public function show(VisaApplication $application)
    {
        $application->load(['location', 'creator', 'statusHistories.changer']);
        $barcodeBase64 = $this->barcodeService->pngBase64($application->confirmation_number);

        return view('admin.applications.show', [
            'application' => $application,
            'barcodeBase64' => $barcodeBase64,
            'statuses' => config('visa.statuses'),
        ]);
    }

    public function edit(VisaApplication $application)
    {
        $locations = Location::query()->where('is_active', true)->orderBy('display_name')->get();

        return view('admin.applications.edit', compact('application', 'locations'));
    }

    public function update(UpdateVisaApplicationRequest $request, VisaApplication $application)
    {
        $this->applicationService->updateApplicant(
            $application,
            $request->safe()->except('photograph'),
            $request->file('photograph'),
            $request->user(),
        );

        return redirect()
            ->route('admin.applications.show', $application)
            ->with('success', 'Application updated.');
    }

    public function updateStatus(UpdateStatusRequest $request, VisaApplication $application)
    {
        $this->statusService->changeStatus($application, $request->validated('status'), $request->user());

        return redirect()
            ->route('admin.applications.show', $application)
            ->with('success', 'Status updated.');
    }

    public function destroy(Request $request, VisaApplication $application)
    {
        $this->applicationService->delete($application, $request->user());

        return redirect()
            ->route('admin.applications.index')
            ->with('success', 'Application deleted.');
    }

    public function confirmation(VisaApplication $application)
    {
        $application->load('location');
        $barcodeBase64 = $this->barcodeService->pngBase64($application->confirmation_number);
        $photoDataUri = $this->photoDataUri($application);
        $this->auditLogService->log(request()->user(), 'visa.confirmation_viewed', VisaApplication::class, $application->id);

        return view('confirmations.confirmation', compact('application', 'barcodeBase64', 'photoDataUri'));
    }

    public function pdf(VisaApplication $application)
    {
        $this->auditLogService->log(request()->user(), 'visa.confirmation_pdf', VisaApplication::class, $application->id);

        return $this->pdfService->download($application)->download('visa-confirmation-'.$application->confirmation_number.'.pdf');
    }

    public function photograph(VisaApplication $application)
    {
        $path = $this->photographPath($application);
        if (! $path || ! is_file($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    private function photographPath(VisaApplication $application): ?string
    {
        if (! $application->photograph_path) {
            return null;
        }

        $path = Storage::disk('local')->path($application->photograph_path);

        return is_file($path) ? $path : null;
    }

    private function photoDataUri(VisaApplication $application): ?string
    {
        $path = $this->photographPath($application);
        if (! $path) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/jpeg';

        return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($path));
    }
}