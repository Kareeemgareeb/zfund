<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CharitableProject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CharitableProjectController extends Controller
{
    private const CATEGORIES = ['ORPHAN_SPONSORSHIP', 'MARRIAGE_AID', 'MOSQUE_CONSTRUCTION', 'FOOD_BASKET_DISTRIBUTION', 'MEDICAL_SUPPORT', 'EDUCATION_SUPPORT', 'WATER_PROJECTS', 'HOUSING_REPAIR', 'WINTER_RELIEF', 'EMERGENCY_RELIEF', 'RAMADAN_MEALS', 'SCHOOL_SUPPLIES', 'DISABILITY_SUPPORT', 'SMALL_BUSINESSES', 'COMMUNITY_SERVICES', 'OTHER'];
    private const STATUSES = ['ACTIVE', 'FULLY_FUNDED', 'COMPLETED', 'PAUSED'];

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasRole('admin'), 403);
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();
        $filters = $request->validate([
            'q' => ['nullable','string','max:150'],
            'category' => ['nullable',Rule::in(self::CATEGORIES)],
            'status' => ['nullable',Rule::in(self::STATUSES)],
            'funding' => ['nullable',Rule::in(['needs_funding','fully_funded'])],
        ]);
        $query = CharitableProject::query();
        if (!empty($filters['q'])) {
            $term = str_replace(['%', '_'], ['\\%', '\\_'], $filters['q']);
            $query->where(fn($q) => $q->where('title','like','%'.$term.'%')->orWhere('description','like','%'.$term.'%'));
        }
        if (!empty($filters['category'])) $query->where('category',$filters['category']);
        if (!empty($filters['status'])) $query->where('status',$filters['status']);
        if (($filters['funding']??null)==='needs_funding') $query->whereColumn('collected_amount','<','target_budget');
        if (($filters['funding']??null)==='fully_funded') $query->whereColumn('collected_amount','>=','target_budget');
        $projects = $query->latest('id')->paginate(15)->withQueryString();
        $categories = self::CATEGORIES;
        $statuses = self::STATUSES;
        return view('admin.projects.index', compact('projects','categories','statuses'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        return view('admin.projects.form', ['project' => new CharitableProject()]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();
        $data = $this->validateProject($request);
        $data['collected_amount'] = 0;
        $data['disbursed_amount'] = 0;
        CharitableProject::create($data);
        return redirect()->route('admin.projects.index')->with('status', 'تمت إضافة المشروع بنجاح.');
    }

    public function edit(CharitableProject $project)
    {
        $this->authorizeAdmin();
        return view('admin.projects.form', compact('project'));
    }

    public function update(Request $request, CharitableProject $project)
    {
        $this->authorizeAdmin();
        $project->update($this->validateProject($request));
        return redirect()->route('admin.projects.index')->with('status', 'تم تحديث المشروع بنجاح.');
    }

    private function validateProject(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:10000'],
            'category' => ['required', Rule::in(self::CATEGORIES)],
            'target_budget' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'status' => ['required', Rule::in(self::STATUSES)],
        ]);
    }
}
