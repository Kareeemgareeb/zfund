<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\GeneralAnnouncementNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class AnnouncementController extends Controller
{
    private function guard(): void
    {
        abort_unless(auth()->user()?->hasRole('admin'), 403);
    }

    public function index()
    {
        $this->guard();
        $announcements = Announcement::with('creator')->latest()->paginate(15);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        $this->guard();
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $this->guard();
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'content' => 'required|string|min:10|max:5000',
            'is_public' => 'boolean',
            'target_group' => 'required|in:all,beneficiary,donor,none',
        ], [
            'title.required' => 'يرجى كتابة عنوان الإعلان.',
            'title.max' => 'يجب ألا يتجاوز العنوان 150 حرفاً.',
            'content.required' => 'يرجى كتابة محتوى الإعلان.',
            'target_group.required' => 'يرجى تحديد الفئة المستهدفة بالإشعار الداخلي.',
        ]);

        $announcement = Announcement::create([
            'created_by' => auth()->id(),
            'title' => $data['title'],
            'content' => $data['content'],
            'is_public' => $request->boolean('is_public', true),
        ]);

        // Send In-App Notification according to target group
        if ($data['target_group'] !== 'none') {
            $query = User::query();
            if ($data['target_group'] === 'beneficiary') {
                $query->whereHas('roles', fn($q) => $q->where('name', 'beneficiary'));
            } elseif ($data['target_group'] === 'donor') {
                $query->whereHas('roles', fn($q) => $q->where('name', 'donor'));
            }

            // Chunk users to prevent memory overload
            $query->chunk(100, function ($users) use ($data) {
                Notification::send($users, new GeneralAnnouncementNotification(
                    $data['title'],
                    $data['content'],
                    route('dashboard')
                ));
            });
        }

        return redirect()->route('admin.announcements.index')->with('status', 'تم نشر الإعلان وبث الإشعارات الداخلية بنجاح.');
    }

    public function destroy(Announcement $announcement)
    {
        $this->guard();
        $announcement->delete();
        return back()->with('status', 'تم حذف الإعلان بنجاح.');
    }
}
