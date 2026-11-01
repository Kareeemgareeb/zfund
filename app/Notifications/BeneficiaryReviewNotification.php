<?php

namespace App\Notifications;

use App\Models\Beneficiary;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BeneficiaryReviewNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Beneficiary $beneficiary,
        public string $decision,
        public ?string $reviewNote = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $isVerified = $this->decision === 'VERIFIED';
        $title = $isVerified ? 'تم اعتماد ملفك الاجتماعي' : 'تحديث بشأن ملفك الاجتماعي';
        
        $message = $isVerified
            ? "تهانينا، تم اعتماد ملفك الاجتماعي بنجاح برقم ({$this->beneficiary->file_number}). يمكنك الآن تقديم طلبات الإعانة ومتابعة التجديد."
            : "نود إعلامك بأنه تم فحص ملفك وتقرر عدم اعتماده في الوقت الحالي." . ($this->reviewNote ? " السبب: {$this->reviewNote}" : '');

        return [
            'title' => $title,
            'message' => $message,
            'action_url' => route('dashboard'),
            'type' => 'beneficiary_review',
            'status' => $this->decision,
            'icon' => $isVerified ? 'check-circle' : 'x-circle',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
