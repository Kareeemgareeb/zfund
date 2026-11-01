<?php

namespace Tests\Feature;

use App\Models\AssistanceRequest;
use App\Models\BankAccount;
use App\Models\BankTransferSubmission;
use App\Models\Beneficiary;
use App\Models\CharitableProject;
use App\Models\FileRenewal;
use App\Models\Role;
use App\Models\User;
use App\Notifications\GeneralAnnouncementNotification;
use App\Services\FileRenewalService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InAppNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic roles
        Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'مدير النظام']);
        Role::firstOrCreate(['name' => 'beneficiary'], ['display_name' => 'مستفيد']);
        Role::firstOrCreate(['name' => 'donor'], ['display_name' => 'متبرع']);
        Role::firstOrCreate(['name' => 'case_officer'], ['display_name' => 'باحث اجتماعي']);
    }

    private function createBeneficiary(User $user, array $overrides = []): Beneficiary
    {
        return Beneficiary::create(array_merge([
            'user_id' => $user->id,
            'file_number' => 'SRT-BEN-' . rand(1000, 9999),
            'full_name' => 'اسم تجريبي ثلاثي',
            'gender' => 'MALE',
            'birth_date' => '1990-01-01',
            'marital_status' => 'MARRIED',
            'family_members_count' => 3,
            'city' => 'سرْت',
            'address' => 'حي رقم 1',
            'eligibility_status' => 'PENDING',
            'next_renewal_due' => Carbon::now()->addYear(),
        ], $overrides));
    }

    public function test_user_can_view_notifications_index()
    {
        $user = User::factory()->create();
        $user->notify(new GeneralAnnouncementNotification('تنبيه تجريبي', 'هذا نص التنبيه'));

        $response = $this->actingAs($user)->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('تنبيه تجريبي');
        $response->assertSee('هذا نص التنبيه');

        // Verify dashboard also renders unread notification alert widget
        $dashResponse = $this->actingAs($user)->get(route('dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('تنبيهات جديدة غير مقروءة');
        $dashResponse->assertSee('تنبيه تجريبي');
    }

    public function test_unread_and_read_notifications_filtering()
    {
        $user = User::factory()->create();
        $user->notify(new GeneralAnnouncementNotification('إشعار غير مقروء', 'رسالة 1'));
        $user->notify(new GeneralAnnouncementNotification('إشعار مقروء', 'رسالة 2'));

        // Mark second notification as read
        $readNotif = $user->notifications()->where('data->title', 'إشعار مقروء')->first();
        $readNotif->markAsRead();

        // Filter unread
        $responseUnread = $this->actingAs($user)->get(route('notifications.index', ['status' => 'unread']));
        $responseUnread->assertStatus(200);
        $unreadList = $responseUnread->viewData('notifications');
        $this->assertTrue($unreadList->contains('data.title', 'إشعار غير مقروء'));
        $this->assertFalse($unreadList->contains('data.title', 'إشعار مقروء'));

        // Filter read
        $responseRead = $this->actingAs($user)->get(route('notifications.index', ['status' => 'read']));
        $responseRead->assertStatus(200);
        $readList = $responseRead->viewData('notifications');
        $this->assertTrue($readList->contains('data.title', 'إشعار مقروء'));
        $this->assertFalse($readList->contains('data.title', 'إشعار غير مقروء'));
    }

    public function test_clicking_notification_marks_it_as_read_and_redirects()
    {
        $user = User::factory()->create();
        $user->notify(new GeneralAnnouncementNotification('تنبيه توجيهي', 'تفاصيل', route('dashboard')));

        $notification = $user->unreadNotifications()->first();
        $this->assertNotNull($notification);
        $this->assertNull($notification->read_at);

        $response = $this->actingAs($user)->get(route('notifications.go', $notification->id));

        $response->assertRedirect(route('dashboard'));
        $notification->refresh();
        $this->assertNotNull($notification->read_at);
    }

    public function test_user_can_mark_single_notification_as_read()
    {
        $user = User::factory()->create();
        $user->notify(new GeneralAnnouncementNotification('تنبيه', 'تفاصيل'));

        $notification = $user->unreadNotifications()->first();

        $response = $this->actingAs($user)->post(route('notifications.mark-read', $notification->id));

        $response->assertRedirect();
        $notification->refresh();
        $this->assertNotNull($notification->read_at);
    }

    public function test_user_can_mark_all_notifications_as_read()
    {
        $user = User::factory()->create();
        $user->notify(new GeneralAnnouncementNotification('تنبيه 1', 'تفاصيل 1'));
        $user->notify(new GeneralAnnouncementNotification('تنبيه 2', 'تفاصيل 2'));

        $this->assertEquals(2, $user->unreadNotifications()->count());

        $response = $this->actingAs($user)->post(route('notifications.mark-all-read'));

        $response->assertRedirect();
        $this->assertEquals(0, $user->unreadNotifications()->count());
    }

    public function test_user_can_delete_notification()
    {
        $user = User::factory()->create();
        $user->notify(new GeneralAnnouncementNotification('تنبيه للحذف', 'تفاصيل'));

        $notification = $user->notifications()->first();

        $response = $this->actingAs($user)->delete(route('notifications.destroy', $notification->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    }

    public function test_user_cannot_read_or_delete_other_users_notification()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $user1->notify(new GeneralAnnouncementNotification('تنبيه خاص', 'تفاصيل'));
        $notif = $user1->notifications()->first();

        $response = $this->actingAs($user2)->get(route('notifications.go', $notif->id));
        $response->assertStatus(404);

        $deleteResponse = $this->actingAs($user2)->delete(route('notifications.destroy', $notif->id));
        $deleteResponse->assertStatus(404);
    }

    public function test_beneficiary_review_triggers_notification()
    {
        $adminRole = Role::where('name', 'admin')->first();
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);

        $beneficiaryUser = User::factory()->create();
        $beneficiary = $this->createBeneficiary($beneficiaryUser, [
            'file_number' => 'SRT-BEN-0001',
            'full_name' => 'علي محمد الفلاني',
            'eligibility_status' => 'PENDING',
        ]);

        $this->actingAs($admin)->patch(route('admin.beneficiaries.review', $beneficiary), [
            'decision' => 'VERIFIED',
        ]);

        $beneficiary->refresh();
        $this->assertEquals('VERIFIED', $beneficiary->eligibility_status);

        $notification = $beneficiaryUser->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertStringContainsString('تم اعتماد ملفك الاجتماعي', $notification->data['title']);
    }

    public function test_file_renewal_triggers_notification()
    {
        $admin = User::factory()->create();
        $beneficiaryUser = User::factory()->create();
        $beneficiary = $this->createBeneficiary($beneficiaryUser, [
            'file_number' => 'SRT-BEN-0002',
            'full_name' => 'سالم محمد',
            'eligibility_status' => 'VERIFIED',
            'next_renewal_due' => Carbon::now()->subDay(),
        ]);

        $renewal = FileRenewal::create([
            'beneficiary_id' => $beneficiary->id,
            'renewal_year' => Carbon::now()->year,
            'submission_date' => Carbon::now(),
            'status' => 'PENDING',
        ]);

        $service = app(FileRenewalService::class);
        $service->reviewRenewal($renewal, 'APPROVED', $admin->id);

        $notification = $beneficiaryUser->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertStringContainsString('تم اعتماد تجديد ملفك السنوي', $notification->data['title']);
    }

    public function test_assistance_request_submission_and_status_update_trigger_notifications()
    {
        $adminRole = Role::where('name', 'admin')->first();
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);

        $benRole = Role::where('name', 'beneficiary')->first();
        $beneficiaryUser = User::factory()->create();
        $beneficiaryUser->roles()->attach($benRole->id);

        $beneficiary = $this->createBeneficiary($beneficiaryUser, [
            'file_number' => 'SRT-BEN-0003',
            'full_name' => 'فاطمة أحمد',
            'eligibility_status' => 'VERIFIED',
            'next_renewal_due' => Carbon::now()->addMonths(6),
        ]);

        // Submit assistance request
        $response = $this->actingAs($beneficiaryUser)->post(route('beneficiary.requests.store'), [
            'category' => 'FOOD_BASKET',
            'requested_amount' => 500,
            'description' => 'طلب مساعدة لشراء سلة غذائية رمضانية للأسرة المحتاجة',
        ]);

        $response->assertRedirect(route('beneficiary.requests.index'));

        // Check submitted notification
        $subNotif = $beneficiaryUser->notifications()->latest()->first();
        $this->assertNotNull($subNotif);
        $this->assertEquals('SUBMITTED', $subNotif->data['status']);

        // Admin updates status to UNDER_REVIEW
        $req = AssistanceRequest::where('beneficiary_id', $beneficiary->id)->first();
        $updateRes = $this->actingAs($admin)->patch(route('admin.requests.updateStatus', $req->id), [
            'status' => 'UNDER_REVIEW',
        ]);
        $updateRes->assertSessionHasNoErrors();
        $updateRes->assertRedirect();

        $req->refresh();
        $this->assertEquals('UNDER_REVIEW', $req->status);

        $notifications = $beneficiaryUser->notifications()->get();
        $reviewNotif = $notifications->firstWhere('data.status', 'UNDER_REVIEW');
        $this->assertNotNull($reviewNotif);
        $this->assertEquals('UNDER_REVIEW', $reviewNotif->data['status']);
    }

    public function test_bank_transfer_review_triggers_notification_for_registered_donor()
    {
        $adminRole = Role::where('name', 'admin')->first();
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);

        $donorRole = Role::where('name', 'donor')->first();
        $donorUser = User::factory()->create();
        $donorUser->roles()->attach($donorRole->id);

        $bankAccount = BankAccount::create([
            'bank_name' => 'مصرف التجارة والتنمية',
            'account_holder' => 'صندوق الزكاة سرت',
            'account_number' => '1234567890',
            'iban' => 'LY000000000000000000000000',
            'is_active' => true,
        ]);

        $project = CharitableProject::create([
            'title' => 'مشروع إفطار صائم',
            'category' => 'FOOD_BASKET',
            'target_budget' => 10000,
            'collected_amount' => 0,
            'status' => 'ACTIVE',
        ]);

        $transfer = BankTransferSubmission::create([
            'user_id' => $donorUser->id,
            'bank_account_id' => $bankAccount->id,
            'charitable_project_id' => $project->id,
            'donor_name' => 'متبرع فاعل خير',
            'donor_phone' => '0912345678',
            'amount' => 250,
            'transfer_reference' => 'REF-998877',
            'bank_name_snapshot' => $bankAccount->bank_name,
            'account_holder_snapshot' => $bankAccount->account_holder,
            'account_number_snapshot' => $bankAccount->account_number,
            'iban_snapshot' => $bankAccount->iban,
            'receipt_path' => 'receipts/test.png',
            'status' => 'PENDING',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.bank-transfers.decision', $transfer), [
            'decision' => 'APPROVED',
        ]);

        $response->assertRedirect();
        $transfer->refresh();
        $this->assertEquals('APPROVED', $transfer->status);

        $notif = $donorUser->notifications()->first();
        $this->assertNotNull($notif);
        $this->assertEquals('APPROVED', $notif->data['status']);
        $this->assertStringContainsString('تم تأكيد استلام الحوالة المصرفية', $notif->data['title']);
    }

    public function test_bank_transfer_review_for_guest_donor_does_not_throw_exception()
    {
        $adminRole = Role::where('name', 'admin')->first();
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);

        $bankAccount = BankAccount::create([
            'bank_name' => 'مصرف الجمهورية',
            'account_holder' => 'صندوق الزكاة سرت',
            'account_number' => '9988776655',
            'iban' => 'LY111111111111111111111111',
            'is_active' => true,
        ]);

        $project = CharitableProject::create([
            'title' => 'كفالة أيتام',
            'category' => 'ORPHAN_SPONSORSHIP',
            'target_budget' => 5000,
            'collected_amount' => 0,
            'status' => 'ACTIVE',
        ]);

        $guestTransfer = BankTransferSubmission::create([
            'user_id' => null, // Guest donor
            'bank_account_id' => $bankAccount->id,
            'charitable_project_id' => $project->id,
            'donor_name' => 'فاعل خير زائر',
            'donor_phone' => '0920000000',
            'amount' => 150,
            'transfer_reference' => 'GUEST-REF-1234',
            'bank_name_snapshot' => $bankAccount->bank_name,
            'account_holder_snapshot' => $bankAccount->account_holder,
            'account_number_snapshot' => $bankAccount->account_number,
            'iban_snapshot' => $bankAccount->iban,
            'receipt_path' => 'receipts/guest.png',
            'status' => 'PENDING',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.bank-transfers.decision', $guestTransfer), [
            'decision' => 'APPROVED',
        ]);

        $response->assertRedirect();
        $guestTransfer->refresh();
        $this->assertEquals('APPROVED', $guestTransfer->status);
    }

    public function test_admin_announcement_broadcasts_notifications_to_target_group()
    {
        $adminRole = Role::where('name', 'admin')->first();
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);

        $benRole = Role::where('name', 'beneficiary')->first();
        $benUser = User::factory()->create();
        $benUser->roles()->attach($benRole->id);

        $donorRole = Role::where('name', 'donor')->first();
        $donorUser = User::factory()->create();
        $donorUser->roles()->attach($donorRole->id);

        $response = $this->actingAs($admin)->post(route('admin.announcements.store'), [
            'title' => 'إعلان خاص للمستفيدين',
            'content' => 'نرجو من جميع المستفيدين تحديث أرقام هواتفهم في أقرب وقت.',
            'target_group' => 'beneficiary',
            'is_public' => 1,
        ]);

        $response->assertRedirect(route('admin.announcements.index'));

        // Beneficiary received notification
        $this->assertEquals(1, $benUser->notifications()->count());
        $this->assertEquals('إعلان خاص للمستفيدين', $benUser->notifications()->first()->data['title']);

        // Donor did NOT receive this notification
        $this->assertEquals(0, $donorUser->notifications()->count());
    }
}
