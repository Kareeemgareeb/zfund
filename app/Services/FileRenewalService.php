<?php

namespace App\Services;

use App\Models\FileRenewal;
use App\Models\Beneficiary;
use Carbon\Carbon;

class FileRenewalService
{
    /**
     * Beneficiary submits a new annual renewal request.
     */
    public function submitRenewal(Beneficiary $beneficiary, ?string $notes)
    {
        $currentYear = Carbon::now()->year;
        if ($beneficiary->next_renewal_due && Carbon::parse($beneficiary->next_renewal_due)->startOfDay()->isFuture()) {
            throw new \DomainException('لم يحن موعد تجديد ملفك بعد.');
        }
        if ($beneficiary->fileRenewals()->where('status', 'PENDING')->exists()) {
            throw new \DomainException('لديك طلب تجديد قيد المراجعة بالفعل.');
        }

        // Prevent submitting multiple pending requests for the same year
        $existing = $beneficiary->fileRenewals()
            ->where('renewal_year', $currentYear)
            ->whereIn('status', ['PENDING', 'APPROVED'])
            ->first();

        if ($existing) {
            throw new \Exception("يوجد طلب تجديد قيد المراجعة أو معتمد لهذه السنة.");
        }

        return $beneficiary->fileRenewals()->create([
            'renewal_year' => $currentYear,
            'submission_date' => Carbon::now(),
            'status' => 'PENDING',
            'beneficiary_notes' => $notes
        ]);
    }

    /**
     * Admin or Case Officer reviews the request.
     */
    public function reviewRenewal(FileRenewal $renewal, string $status, int $adminId)
    {
        if ($renewal->status !== 'PENDING') {
            throw new \DomainException('تمت مراجعة طلب التجديد مسبقًا.');
        }
        $renewal->update([
            'status' => $status,
            'reviewed_by' => $adminId
        ]);

        // If approved, strictly update the Beneficiary's due date to exactly 1 year from now
        if ($status === 'APPROVED') {
            $renewal->beneficiary->update([
                'next_renewal_due' => Carbon::now()->addYear()->format('Y-m-d')
            ]);
        }

        if ($renewal->beneficiary?->user) {
            $renewal->beneficiary->user->notify(new \App\Notifications\FileRenewalNotification(
                $renewal,
                $status
            ));
        }

        return $renewal;
    }
}
