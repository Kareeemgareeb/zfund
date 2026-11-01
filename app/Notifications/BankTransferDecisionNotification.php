<?php

namespace App\Notifications;

use App\Models\BankTransferSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BankTransferDecisionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public BankTransferSubmission $transfer,
        public string $decision,
        public ?string $reviewNote = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $isApproved = $this->decision === 'APPROVED';
        $formattedAmount = number_format($this->transfer->amount, 2);
        
        $title = $isApproved ? 'تم تأكيد استلام الحوالة المصرفية' : 'تحديث بشأن الحوالة المصرفية';
        
        $message = $isApproved
            ? "شكرًا لك! تم التحقق من الحوالة المصرفية بالمرجع ({$this->transfer->transfer_reference}) بقيمة ({$formattedAmount} د.ل.) وإضافتها بنجاح إلى التبرعات."
            : "نود إعلامك بأنه تعذر قبول الحوالة المصرفية بالمرجع ({$this->transfer->transfer_reference})." . ($this->reviewNote ? " السبب: {$this->reviewNote}" : '');

        return [
            'title' => $title,
            'message' => $message,
            'action_url' => route('donations.history'),
            'type' => 'bank_transfer',
            'status' => $this->decision,
            'icon' => $isApproved ? 'check-circle' : 'alert-circle',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
