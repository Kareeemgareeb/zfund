<?php

namespace App\Notifications;

use App\Models\FileRenewal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FileRenewalNotification extends Notification
{
    use Queueable;

    public function __construct(
        public FileRenewal $renewal,
        public string $decision
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $isApproved = $this->decision === 'APPROVED';
        $title = $isApproved ? 'تم اعتماد تجديد ملفك السنوي' : 'رفض طلب تجديد الملف';
        
        $message = $isApproved
            ? "تم اعتماد طلب تجديد ملفك السنوي لعام ({$this->renewal->renewal_year}) بنجاح، وتم تمديد الصلاحية لعام كامل."
            : "نأسف، تم رفض طلب تجديد ملفك السنوي لعام ({$this->renewal->renewal_year}). يرجى مراجعة إدارة الصندوق.";

        return [
            'title' => $title,
            'message' => $message,
            'action_url' => route('dashboard'),
            'type' => 'file_renewal',
            'status' => $this->decision,
            'icon' => $isApproved ? 'check-circle' : 'alert-triangle',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
