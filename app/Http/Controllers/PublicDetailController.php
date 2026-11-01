<?php
namespace App\Http\Controllers;
use App\Models\AssistanceRequest;
use App\Models\CharitableProject;
use Illuminate\View\View;

class PublicDetailController extends Controller
{
    public function project(CharitableProject $project): View
    {
        abort_unless(in_array($project->status, ['ACTIVE', 'FULLY_FUNDED'], true), 404);
        $target = max(0, (float) $project->target_budget);
        $collected = max(0, (float) $project->collected_amount);
        $remaining = max(0, $target - $collected);
        $percentage = $target > 0 ? min(100, ($collected / $target) * 100) : 0;
        $canDonate = $project->status === 'ACTIVE' && $remaining > 0 && !(auth()->check() && auth()->user()->hasRole('admin'));
        return view('public_details.project', compact('project', 'target', 'collected', 'remaining', 'percentage', 'canDonate'));
    }

    public function assistance(AssistanceRequest $assistance): View
    {
        abort_unless($assistance->status === 'APPROVED', 404);
        $target = max(0, (float) ($assistance->approved_amount ?? $assistance->requested_amount));
        $collected = max(0, (float) $assistance->transactions()->where('status', 'SUCCESSFUL')->sum('amount'));
        $remaining = max(0, $target - $collected);
        $percentage = $target > 0 ? min(100, ($collected / $target) * 100) : 0;
        $categories = ['ORPHAN_SPONSORSHIP'=>'كفالة الأيتام','MARRIAGE_AID'=>'مساعدة الزواج','FOOD_BASKET'=>'السلة الغذائية','MEDICAL_AID'=>'مساعدة علاجية','EMERGENCY_DEBT'=>'سداد دين','GENERAL_RELIEF'=>'إعانة عامة'];
        $categoryLabel = $categories[$assistance->category] ?? 'طلب إعانة';
        $canDonate = $remaining > 0 && !(auth()->check() && auth()->user()->hasRole('admin'));
        // Deliberately do not expose description, beneficiary relation, attachments, phone, address or identifying data.
        return view('public_details.assistance', compact('assistance', 'target', 'collected', 'remaining', 'percentage', 'categoryLabel', 'canDonate'));
    }
}
