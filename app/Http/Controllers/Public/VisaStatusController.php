<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicVisaStatusRequest;
use App\Models\Location;
use App\Services\CaptchaService;
use App\Services\PublicVisaLookupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisaStatusController extends Controller
{
    public function __construct(
        private readonly CaptchaService $captchaService,
        private readonly PublicVisaLookupService $lookupService,
    ) {}

    public function show(Request $request)
    {
        $locations = Location::query()
            ->where('is_active', true)
            ->orderBy('display_name')
            ->get();

        $captcha = $this->captchaService->current();

        return view('public.visa-status', [
            'locations' => $locations,
            'captchaCode' => $captcha,
            'defaultType' => $request->query('type', 'NIV'),
        ]);
    }

    public function refreshCaptcha(): JsonResponse
    {
        return response()->json([
            'code' => $this->captchaService->rotate(),
        ]);
    }

    public function check(PublicVisaStatusRequest $request): JsonResponse
    {
        if (! $this->captchaService->validate($request->input('captcha'))) {
            return response()->json([
                'message' => 'The information you entered does not match our records. Please verify your details and try again.',
            ], 422);
        }

        $application = $this->lookupService->lookup($request->validated());

        if (! $application) {
            return response()->json([
                'message' => 'The information you entered does not match our records. Please verify your details and try again.',
            ], 422);
        }

        return response()->json([
            'popup' => $this->lookupService->popupPayload($application),
        ]);
    }
}