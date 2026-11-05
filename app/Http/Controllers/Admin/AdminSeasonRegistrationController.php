<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\ModelRegistrationPayment;
use App\Models\Season;
use App\Models\SeasonModelRegistration;
use App\Models\SeasonRegistrationPrice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminSeasonRegistrationController extends Controller
{
    /**
     * Display a listing of season model registrations with filters and statistics.
     */
    public function index(Request $request): View
    {
        $query = SeasonModelRegistration::with('season');

        // 1. Search by name, phone, email, registration_number
        if ($request->filled('search')) {
            $term = trim((string) $request->input('search'));
            $query->where(function ($q) use ($term) {
                $q->where('full_name', 'like', "%{$term}%")
                    ->orWhere('whatsapp_number', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('registration_number', 'like', "%{$term}%");
            });
        }

        // 2. Filter by Season
        if ($request->filled('season_id')) {
            $query->where('season_id', $request->input('season_id'));
        }

        // 3. Filter by Gender
        if ($request->filled('gender')) {
            $query->where('gender', strtolower($request->input('gender')));
        }

        // 4. Filter by Category
        if ($request->filled('category')) {
            $query->where('category', strtolower($request->input('category')));
        }

        // 5. Filter by Payment Status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', strtolower($request->input('payment_status')));
        }

        // 6. Filter by Registration Status
        if ($request->filled('registration_status')) {
            $query->where('registration_status', strtolower($request->input('registration_status')));
        }

        // Base query for statistics (filtered by season if selected)
        $statsQuery = SeasonModelRegistration::query();
        if ($request->filled('season_id')) {
            $statsQuery->where('season_id', $request->input('season_id'));
        }

        $totalRegistrations = (clone $statsQuery)->count();
        $approvedRegistrations = (clone $statsQuery)->where('registration_status', 'approved')->count();
        $pendingRegistrations = (clone $statsQuery)->where('registration_status', 'pending')->count();
        $paidRegistrations = (clone $statsQuery)->where('payment_status', 'paid')->count();
        $unpaidRegistrations = (clone $statsQuery)->where('payment_status', 'unpaid')->count();
        $totalCollected = (float) (clone $statsQuery)->sum('amount_paid');
        $totalFeeSum = (float) (clone $statsQuery)->sum('registration_fee');
        $totalOutstanding = max(0.00, $totalFeeSum - $totalCollected);

        $stats = [
            'total' => $totalRegistrations,
            'approved' => $approvedRegistrations,
            'pending' => $pendingRegistrations,
            'paid' => $paidRegistrations,
            'unpaid' => $unpaidRegistrations,
            'collected' => $totalCollected,
            'outstanding' => $totalOutstanding,
        ];

        $registrations = $query->latest()->paginate(15)->withQueryString();
        $seasons = Season::orderByDesc('number')->get();

        return view('admin.season-registrations.index', compact('registrations', 'stats', 'seasons'));
    }

    /**
     * Export registrations as CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = SeasonModelRegistration::with('season');

        if ($request->filled('search')) {
            $term = trim((string) $request->input('search'));
            $query->where(function ($q) use ($term) {
                $q->where('full_name', 'like', "%{$term}%")
                    ->orWhere('whatsapp_number', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('registration_number', 'like', "%{$term}%");
            });
        }
        if ($request->filled('season_id')) {
            $query->where('season_id', $request->input('season_id'));
        }
        if ($request->filled('gender')) {
            $query->where('gender', strtolower($request->input('gender')));
        }
        if ($request->filled('category')) {
            $query->where('category', strtolower($request->input('category')));
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', strtolower($request->input('payment_status')));
        }
        if ($request->filled('registration_status')) {
            $query->where('registration_status', strtolower($request->input('registration_status')));
        }

        $records = $query->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="IDFR_Model_Registrations_'.date('Ymd_His').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            fputcsv($handle, [
                'Registration ID',
                'Full Name',
                'WhatsApp Number',
                'Email',
                'Age',
                'Gender',
                'Category',
                'Citizenship',
                'Season',
                'Registration Fee (INR)',
                'Amount Paid (INR)',
                'Remaining (INR)',
                'Payment Status',
                'Registration Status',
                'Parent/Guardian Name',
                'Parent/Guardian Contact',
                'Parent Relationship',
                'Additional Notes',
                'Registered Date',
            ]);

            foreach ($records as $r) {
                fputcsv($handle, [
                    $r->registration_number,
                    $r->full_name,
                    $r->whatsapp_number,
                    $r->email ?? '',
                    $r->age,
                    ucfirst($r->gender),
                    ucfirst($r->category),
                    $r->citizenship,
                    $r->season ? $r->season->title : "Season {$r->season_id}",
                    number_format((float) $r->registration_fee, 2, '.', ''),
                    number_format((float) $r->amount_paid, 2, '.', ''),
                    number_format((float) $r->remaining_balance, 2, '.', ''),
                    ucfirst($r->payment_status),
                    ucfirst($r->registration_status),
                    $r->parent_name ?? '',
                    $r->parent_contact ?? '',
                    $r->parent_relationship ?? '',
                    $r->additional_info ?? '',
                    $r->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Show registration details.
     */
    public function show(SeasonModelRegistration $registration): View
    {
        $registration->load(['season', 'payments.recordedBy']);

        return view('admin.season-registrations.show', compact('registration'));
    }

    /**
     * Edit registration form.
     */
    public function edit(SeasonModelRegistration $registration): View
    {
        $registration->load('season');
        $seasons = Season::orderByDesc('number')->get();

        return view('admin.season-registrations.edit', compact('registration', 'seasons'));
    }

    /**
     * Update registration details.
     */
    public function update(Request $request, SeasonModelRegistration $registration): RedirectResponse
    {
        $validated = $request->validate([
            'season_id' => ['required', 'exists:seasons,id'],
            'full_name' => ['required', 'string', 'min:2', 'max:150'],
            'whatsapp_number' => ['required', 'string', 'min:8', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'age' => ['required', 'integer', 'min:3', 'max:99'],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'category' => ['required', 'string', 'in:adult,kids'],
            'citizenship' => ['required', 'string', 'min:2', 'max:100'],
            'parent_name' => ['nullable', 'string', 'max:150'],
            'parent_contact' => ['nullable', 'string', 'max:50'],
            'parent_relationship' => ['nullable', 'string', 'max:50'],
            'registration_fee' => ['required', 'numeric', 'min:0'],
            'registration_status' => ['required', 'string', 'in:pending,approved,rejected,cancelled'],
            'payment_status' => ['required', 'string', 'in:unpaid,pending,paid,failed,refunded'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
            'additional_info' => ['nullable', 'string', 'max:2000'],
        ]);

        $registration->update($validated);

        AdminActivity::log(
            'season_registration_updated',
            Auth::user(),
            Auth::user()->email ?? 'admin',
            "Updated model registration #{$registration->registration_number} ({$registration->full_name})",
            $request
        );

        return redirect()->route('admin.season-registrations.show', $registration)
            ->with('success', "Registration #{$registration->registration_number} updated successfully.");
    }

    /**
     * Delete registration.
     */
    public function destroy(Request $request, SeasonModelRegistration $registration): RedirectResponse
    {
        $number = $registration->registration_number;
        $name = $registration->full_name;

        $registration->delete();

        AdminActivity::log(
            'season_registration_deleted',
            Auth::user(),
            Auth::user()->email ?? 'admin',
            "Deleted model registration #{$number} ({$name})",
            $request
        );

        return redirect()->route('admin.season-registrations.index')
            ->with('success', "Registration #{$number} ({$name}) was permanently deleted.");
    }

    /**
     * Record a manual payment against a registration.
     */
    public function storePayment(Request $request, SeasonModelRegistration $registration): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:1000000'],
            'payment_method' => ['required', 'string', 'in:cash,bank_transfer,upi,card,cheque,other'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
            'payment_date' => ['required', 'date'],
            'payment_status' => ['required', 'string', 'in:paid,pending,failed,refunded'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $seasonNum = $registration->season ? $registration->season->number : 8;
        $payRef = 'PAY-S'.$seasonNum.'-'.mt_rand(10000, 99999);

        $payment = ModelRegistrationPayment::create([
            'season_model_registration_id' => $registration->id,
            'payment_reference' => $payRef,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'transaction_reference' => $validated['transaction_reference'] ?? null,
            'payment_date' => $validated['payment_date'],
            'payment_status' => $validated['payment_status'],
            'notes' => $validated['notes'] ?? null,
            'recorded_by_user_id' => Auth::id(),
        ]);

        // Sync registration amount_paid and payment_status
        $registration->syncPaymentStatus();

        AdminActivity::log(
            'registration_payment_recorded',
            Auth::user(),
            Auth::user()->email ?? 'admin',
            "Recorded manual payment of ₹{$payment->amount} (Ref: {$payment->payment_reference}) for #{$registration->registration_number}",
            $request
        );

        return redirect()->route('admin.season-registrations.show', $registration)
            ->with('success', "Payment {$payRef} of ₹".number_format((float) $payment->amount, 0).' recorded successfully.');
    }

    /**
     * Delete a payment record.
     */
    public function destroyPayment(Request $request, ModelRegistrationPayment $payment): RedirectResponse
    {
        $registration = $payment->registration;
        $payRef = $payment->payment_reference;
        $amount = $payment->amount;

        $payment->delete();

        if ($registration) {
            $registration->syncPaymentStatus();
        }

        AdminActivity::log(
            'registration_payment_deleted',
            Auth::user(),
            Auth::user()->email ?? 'admin',
            "Deleted payment record {$payRef} (₹{$amount})",
            $request
        );

        return back()->with('success', "Payment {$payRef} was removed and balance recalculated.");
    }

    /**
     * Season Registration Prices Management index.
     */
    public function pricesIndex(Request $request): View
    {
        $seasons = Season::with('registrationPrice')->orderByDesc('number')->get();

        return view('admin.season-prices.index', compact('seasons'));
    }

    /**
     * Store or update price for a season.
     */
    public function pricesStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'season_id' => ['required', 'exists:seasons,id'],
            'price' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'currency' => ['required', 'string', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $price = SeasonRegistrationPrice::updateOrCreate(
            ['season_id' => $validated['season_id']],
            [
                'price' => $validated['price'],
                'currency' => $validated['currency'] ?? 'INR',
                'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : true,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        $season = Season::find($validated['season_id']);
        $seasonTitle = $season ? $season->title : "Season #{$validated['season_id']}";

        AdminActivity::log(
            'season_price_configured',
            Auth::user(),
            Auth::user()->email ?? 'admin',
            'Configured registration fee ₹'.number_format((float) $price->price, 0)." for {$seasonTitle}",
            $request
        );

        return redirect()->route('admin.season-prices.index')
            ->with('success', "Registration fee for {$seasonTitle} set to ₹".number_format((float) $price->price, 0).'.');
    }

    /**
     * Update an individual price.
     */
    public function pricesUpdate(Request $request, SeasonRegistrationPrice $price): RedirectResponse
    {
        $validated = $request->validate([
            'price' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'currency' => ['required', 'string', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : true;

        $price->update($validated);

        $seasonTitle = $price->season ? $price->season->title : "Season #{$price->season_id}";

        AdminActivity::log(
            'season_price_updated',
            Auth::user(),
            Auth::user()->email ?? 'admin',
            'Updated registration fee ₹'.number_format((float) $price->price, 0)." for {$seasonTitle}",
            $request
        );

        return redirect()->route('admin.season-prices.index')
            ->with('success', "Registration fee for {$seasonTitle} updated to ₹".number_format((float) $price->price, 0).'.');
    }

    /**
     * Delete or reset a price.
     */
    public function pricesDestroy(Request $request, SeasonRegistrationPrice $price): RedirectResponse
    {
        $seasonTitle = $price->season ? $price->season->title : "Season #{$price->season_id}";
        $price->delete();

        AdminActivity::log(
            'season_price_deleted',
            Auth::user(),
            Auth::user()->email ?? 'admin',
            "Reset registration fee for {$seasonTitle} to default",
            $request
        );

        return redirect()->route('admin.season-prices.index')
            ->with('success', "Registration price configuration for {$seasonTitle} was removed (reverts to system default ₹9,999).");
    }

    /**
     * Dedicated Payment Management index.
     */
    public function paymentsIndex(Request $request): View
    {
        $query = ModelRegistrationPayment::with(['registration.season', 'recordedBy']);

        if ($request->filled('search')) {
            $term = trim((string) $request->input('search'));
            $query->where(function ($q) use ($term) {
                $q->where('payment_reference', 'like', "%{$term}%")
                    ->orWhere('transaction_reference', 'like', "%{$term}%")
                    ->orWhereHas('registration', function ($rq) use ($term) {
                        $rq->where('full_name', 'like', "%{$term}%")
                            ->orWhere('whatsapp_number', 'like', "%{$term}%")
                            ->orWhere('registration_number', 'like', "%{$term}%");
                    });
            });
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        if ($request->filled('status')) {
            $query->where('payment_status', $request->input('status'));
        }

        if ($request->filled('season_id')) {
            $seasonId = $request->input('season_id');
            $query->whereHas('registration', function ($q) use ($seasonId) {
                $q->where('season_id', $seasonId);
            });
        }

        $payments = $query->latest('payment_date')->paginate(15)->withQueryString();
        $seasons = Season::orderByDesc('number')->get();

        $totalCollected = (float) ModelRegistrationPayment::where('payment_status', 'paid')->sum('amount');
        $totalPaymentsCount = ModelRegistrationPayment::count();

        return view('admin.season-payments.index', compact('payments', 'seasons', 'totalCollected', 'totalPaymentsCount'));
    }
}
