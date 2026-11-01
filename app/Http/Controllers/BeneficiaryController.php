<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BeneficiaryController extends Controller
{
    /**
     * Show the beneficiary registration/application form.
     */
    public function create()
    {
        $user = Auth::user();
        abort_unless($user->hasRole('beneficiary'), 403);

        // If user already has a beneficiary profile, redirect to dashboard
        if ($user->beneficiary) {
            return redirect()->route('dashboard');
        }

        return view('beneficiary.apply');
    }

    /**
     * Store new beneficiary application.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->hasRole('beneficiary'), 403);

        if ($user->beneficiary) {
            return redirect()->route('dashboard');
        }

        // Accept both native HTML date (Y-m-d) and older Arabic date picker (d/m/Y).
        $birthDate = $request->input('birth_date');
        if (is_string($birthDate) && preg_match('~^\d{2}/\d{2}/\d{4}$~', $birthDate)) {
            try {
                $date = \Carbon\Carbon::createFromFormat('!d/m/Y', $birthDate);
                if ($date && $date->format('d/m/Y') === $birthDate) {
                    $request->merge(['birth_date' => $date->format('Y-m-d')]);
                }
            } catch (\Exception $e) { /* Laravel validation will report the invalid date. */ }
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'gender' => ['required', 'in:MALE,FEMALE'],
            'birth_date' => ['required', 'date_format:Y-m-d', 'before:today'],
            'marital_status' => ['required', 'in:SINGLE,MARRIED,WIDOWED,DIVORCED'],
            'family_members_count' => ['required', 'integer', 'min:1'],
            'city' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_no' => ['nullable', 'string', 'max:34'],
            'case_description' => ['required', 'string', 'min:20', 'max:3000'],
        ], [
            'birth_date.date_format' => 'يرجى إدخال تاريخ الميلاد بصيغة صحيحة.',
            'birth_date.before' => 'يجب أن يكون تاريخ الميلاد سابقًا لليوم.',
        ]);

        // Auto-generate clean file number: ZAK-YEAR-USERID
        $fileNumber = 'SRT-' . date('Y') . '-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);

        Beneficiary::create([
            'user_id' => $user->id,
            'file_number' => $fileNumber,
            'full_name' => $validated['full_name'],
            'gender' => $validated['gender'],
            'birth_date' => $validated['birth_date'],
            'marital_status' => $validated['marital_status'],
            'family_members_count' => $validated['family_members_count'],
            'city' => $validated['city'],
            'address' => $validated['address'],
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_account_no' => $validated['bank_account_no'] ?? null,
            'case_description' => $validated['case_description'],
            'eligibility_status' => 'PENDING',
            'next_renewal_due' => Carbon::now()->addYear()->format('Y-m-d'),
        ]);

        return redirect()->route('dashboard')->with('status', 'تم تقديم طلب فتح الملف بنجاح! ملفك الآن قيد المراجعة والتدقيق.');
    }
}