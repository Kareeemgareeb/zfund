<x-app-layout>
<x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">إدارة المشاريع الخيرية</h2></x-slot>
<style>
.sz-admin-projects{font-family:Cairo,Tahoma,sans-serif;direction:rtl;color:#163b5d}
.sz-admin-projects *{box-sizing:border-box}
.sz-admin-projects .panel{background:#fff;border:1px solid #e0e8f0;border-radius:18px;box-shadow:0 8px 30px #123a6010;padding:24px}
.sz-admin-projects .heading{display:flex;justify-content:space-between;align-items:center;gap:15px;flex-wrap:wrap;margin-bottom:22px}
.sz-admin-projects h1{font-size:25px;font-weight:800;color:#123e68;margin:0}
.sz-admin-projects .hint{color:#64748b;font-size:13px;margin-top:5px}
.sz-admin-projects .primary{display:inline-flex;align-items:center;justify-content:center;background:#10518f;color:#fff!important;padding:12px 20px;border-radius:11px;font-weight:800;text-decoration:none}
.sz-admin-projects .filters{display:grid;grid-template-columns:minmax(180px,2fr) repeat(3,minmax(135px,1fr)) auto auto;gap:10px;margin-bottom:22px;align-items:end}
.sz-admin-projects .field label{display:block;font-size:12px;font-weight:800;color:#395874;margin-bottom:6px}
.sz-admin-projects input,.sz-admin-projects select{width:100%;border:1px solid #cddce9;border-radius:10px;padding:10px 12px;background:#fff;min-height:45px}
.sz-admin-projects button{background:#10518f;color:white;border:0;border-radius:10px;padding:11px 18px;min-height:45px;font-weight:800;cursor:pointer}
.sz-admin-projects .reset{border:1px solid #cbd5e1;border-radius:10px;padding:10px 14px;min-height:45px;display:inline-flex;align-items:center;text-decoration:none;color:#1a4d78}
.sz-admin-projects .table-wrap{overflow-x:auto}
.sz-admin-projects table{width:100%;border-collapse:collapse;text-align:right;font-size:13px}
.sz-admin-projects th{background:#f0f5fa;color:#244766;font-weight:800;padding:15px 12px;white-space:nowrap}
.sz-admin-projects td{padding:15px 12px;border-bottom:1px solid #e8edf2;vertical-align:middle}
.sz-admin-projects tbody tr:hover{background:#f7faff}
.sz-admin-projects .project-title{font-weight:800;color:#153e64}
.sz-admin-projects .sub{color:#65798c;font-size:12px;max-width:240px}
.sz-admin-projects .progress{height:8px;background:#e6edf4;border-radius:20px;overflow:hidden;min-width:90px;margin-bottom:6px}
.sz-admin-projects .progress span{display:block;height:100%;background:#10518f;border-radius:20px}
.sz-admin-projects .status{display:inline-flex;border-radius:20px;padding:5px 12px;font-size:12px;font-weight:800;background:#eaf4ff;color:#10518f;white-space:nowrap}
.sz-admin-projects .status.PAUSED{background:#fff2dd;color:#8a580a}.sz-admin-projects .status.COMPLETED,.sz-admin-projects .status.FULLY_FUNDED{background:#e5f5ec;color:#14633d}
.sz-admin-projects .edit{display:inline-block;border:1px solid #bcd0e4;border-radius:9px;padding:8px 14px;color:#10518f;text-decoration:none;font-weight:800}
.sz-admin-projects a:focus-visible,.sz-admin-projects button:focus-visible{outline:3px solid #10518f;outline-offset:2px}
@media(max-width:950px){.sz-admin-projects .filters{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:550px){.sz-admin-projects .filters{grid-template-columns:1fr}.sz-admin-projects .panel{padding:15px}}
.sz-admin-projects .sz-money{display:inline-flex;direction:ltr;gap:5px;white-space:nowrap;align-items:baseline;font-family:Arial,Tahoma,sans-serif;font-variant-numeric:tabular-nums;font-weight:700}.sz-admin-projects .sz-money-currency{font-family:Cairo,Tahoma,sans-serif;font-size:11px}
</style>
<div class="sz-admin-projects max-w-7xl mx-auto py-8 px-4">
 <div class="panel">
  <div class="heading"><div><h1>إدارة المشاريع الخيرية</h1><p class="hint">استعراض المشاريع ومتابعة التمويل وإدارة الحالات والتصنيفات</p></div><a class="primary" href="{{ route('admin.projects.create') }}">+ إضافة مشروع جديد</a></div>
  @if(session('status'))<div class="bg-green-50 text-green-800 p-3 rounded mb-4">{{ session('status') }}</div>@endif
  <form method="GET" action="{{ route('admin.projects.index') }}" class="filters">
   <div class="field"><label for="pq">البحث</label><input id="pq" name="q" value="{{ request('q') }}" placeholder="اسم المشروع أو الوصف"></div>
   <div class="field"><label for="pc">التصنيف</label><select id="pc" name="category"><option value="">كل التصنيفات</option>@foreach($categories as $category)<option value="{{ $category }}" @selected(request('category')===$category)>{{ (new \App\Models\CharitableProject(['category'=>$category]))->category_label }}</option>@endforeach</select></div>
   <div class="field"><label for="ps">حالة المشروع</label><select id="ps" name="status"><option value="">كل الحالات</option>@foreach($statuses as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ ['ACTIVE'=>'نشط','FULLY_FUNDED'=>'مكتمل التمويل','COMPLETED'=>'مكتمل','PAUSED'=>'متوقف'][$status] ?? $status }}</option>@endforeach</select></div>
   <div class="field"><label for="pf">التمويل</label><select id="pf" name="funding"><option value="">جميع مستويات التمويل</option><option value="needs_funding" @selected(request('funding')==='needs_funding')>لم يكتمل التمويل</option><option value="fully_funded" @selected(request('funding')==='fully_funded')>اكتمل التمويل</option></select></div>
   <button type="submit">تطبيق</button><a class="reset" href="{{ route('admin.projects.index') }}">مسح</a>
  </form>
  @if($errors->any())<div class="text-red-700 mb-4">{{ $errors->first() }}</div>@endif
  <div class="table-wrap"><table><thead><tr><th>المشروع</th><th>التصنيف</th><th>المبلغ المستهدف</th><th>المبلغ المحصل</th><th>المتبقي</th><th>التمويل</th><th>الحالة</th><th>الإجراء</th></tr></thead><tbody>
  @forelse($projects as $project)
   @php($percentage = $project->target_budget > 0 ? max(0,min(100,($project->collected_amount/$project->target_budget)*100)) : 0)
   <tr><td><div class="project-title">{{ $project->title }}</div><div class="sub">{{ \Illuminate\Support\Str::limit($project->description ?: 'لا يوجد وصف',75) }}</div></td>
   <td>{{ $project->category_label }}</td><td><span class="sz-money"><span class="sz-money-currency">د.ل</span><span class="sz-money-number">{{ number_format($project->target_budget,2) }}</span></span></td><td><span class="sz-money"><span class="sz-money-currency">د.ل</span><span class="sz-money-number">{{ number_format($project->collected_amount,2) }}</span></span></td>
   <td><span class="sz-money"><span class="sz-money-currency">د.ل</span><span class="sz-money-number">{{ number_format(max(0,$project->target_budget-$project->collected_amount),2) }}</span></span></td>
   <td><div class="progress"><span style="width:{{ $percentage }}%"></span></div><small>{{ number_format($percentage,1) }}%</small></td>
   <td><span class="status {{ $project->status }}">{{ ['ACTIVE'=>'نشط','FULLY_FUNDED'=>'مكتمل التمويل','COMPLETED'=>'مكتمل','PAUSED'=>'متوقف'][$project->status] ?? $project->status }}</span></td>
   <td><a class="edit" href="{{ route('admin.projects.edit',$project) }}">تعديل</a></td></tr>
  @empty<tr><td colspan="8" style="text-align:center;padding:35px">لا توجد مشاريع تطابق معايير البحث.</td></tr>@endforelse
  </tbody></table></div><div class="mt-5">{{ $projects->links() }}</div>
 </div>
</div>
</x-app-layout>