<?php

namespace App\Notifications;

use App\Models\AssistanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssistanceRequestStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public AssistanceRequest $request,
        public string $status,
        public ?string $note = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $statusLabels = [
            'SUBMITTED' => 'تم استلام الطلب بنجاح',
            'UNDER_REVIEW' => 'قيد المراجعة والدراسة',
            'APPROVED' => 'تمت الموافقة على الطلب',
            'REJECTED' => 'تم رفض الطلب',
            'DISBURSED' => 'تم صرف الإعانة',
        ];

        $statusText = $statusLabels[$this->status] ?? $this->status;
        $title = "تحديث طلب الإعانة ({$this->request->request_code})";
        
        $message = match ($this->status) {
            'SUBMITTED' => "تم تسجيل طلبك رقم ({$this->request->request_code}) بنجاح وهو بانتظار بدء المراجعة من الباحث الاجتماعي.",
            'UNDER_REVIEW' => "بدأت إدارة الصندوق في دراسة ومراجعة طلبك رقم ({$this->request->request_code}).",
            'APPROVED' => "تهانينا! تمت الموافقة على طلب الإعانة رقم ({$this->request->request_code})" . ($this->request->approved_amount ? " بمبلغ معتمد قدره " . number_format($this->request->approved_amount, 2) . " د.ل." : "."),
            'REJECTED' => "نأسف، تم رفض طلب الإعانة رقم ({$this->request->request_code})." . ($this->note ? " السبب: {$this->note}" : ''),
            'DISBURSED' => "تم تحويل وصرف مبالغ الإعانة لطلبك رقم ({$this->request->request_code}).",
            default => "تم تحديث حالة طلب الإعانة رقم ({$this->request->request_code}) إلى: {$statusText}."
        };

        $icon = match ($this->status) {
            'APPROVED', 'DISBURSED' => 'check-circle',
            'REJECTED' => 'x-circle',
            'UNDER_REVIEW' => 'clock',
            default => 'info',
        };

        return [
            'title' => $title,
            'message' => $message,
            'action_url' => route('beneficiary.requests.index'),
            'type' => 'assistance_request',
            'status' => $this->status,
            'icon' => $icon,
            'created_at' => now()->toIso8601String(),
        ];
    }
}
