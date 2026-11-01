<x-app-layout>
<x-slot name="header"><h2>طلباتي — متابعة طلبات الإعانة</h2></x-slot>
<style>
.myreq{max-width:1180px;margin:30px auto;padding:0 20px;direction:rtl;color:#15344d;font-family:Cairo,Tahoma,sans-serif}.myreq h1{font-size:29px;font-weight:900;color:#123d63}.myreq .lead{color:#64748b;margin:7px 0 23px}.myreq .cards{display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:12px;margin:22px 0}.myreq .card,.myreq .panel{background:#fff;border:1px solid #e1e9f1;border-radius:16px;box-shadow:0 6px 22px #123d630a}.myreq .card{padding:17px 12px;min-width:0;min-height:145px;display:flex;flex-direction:column;align-items:stretch}.myreq .card small{display:block;color:#66788a;font-size:12px;font-weight:700;line-height:1.7;min-height:42px}.myreq .card strong{display:block;font-size:clamp(16px,1.5vw,23px);font-weight:900;color:#123d63;margin-top:auto;padding-top:10px;direction:ltr;text-align:right;font-variant-numeric:tabular-nums}.myreq .card .unit{font-size:12px;font-weight:700;flex-shrink:0}.myreq .panel{padding:22px;margin:18px 0;overflow-x:auto}.myreq table{width:100%;border-collapse:collapse;min-width:820px}.myreq th,.myreq td{text-align:right;padding:14px 10px;border-bottom:1px solid #edf1f5;font-size:14px}.myreq th{color:#4a6176;background:#f4f8fc}.myreq .badge{display:inline-block;border-radius:999px;padding:5px 11px;background:#e9f1f9;color:#13538b;font-weight:800;font-size:12px}.myreq .badge.ok{background:#e4f6eb;color:#18743c}.myreq .badge.no{background:#fff0ee;color:#ad352a}.myreq .button{display:inline-block;background:#10518f;color:white!important;border-radius:10px;padding:11px 18px;text-decoration:none;font-weight:800}.myreq .notice{padding:15px;background:#eaf3fb;border-radius:10px;margin:15px 0}.myreq .amount-line{display:inline-flex;align-items:baseline;gap:5px;direction:ltr;white-space:nowrap}.myreq .amount-line .amount-number{font-variant-numeric:tabular-nums}.myreq .card strong.amount-total{display:flex;align-items:baseline;justify-content:flex-end;gap:5px;direction:ltr;white-space:nowrap}@media(max-width:1100px){.myreq .cards{grid-template-columns:repeat(4,minmax(0,1fr))}}@media(max-width:650px){.myreq .cards{grid-template-columns:repeat(2,minmax(0,1fr))}.myreq .card{min-height:132px}}@media(max-width:360px){.myreq .cards{grid-template-columns:1fr}}.myreq .money{direction:ltr;unicode-bidi:isolate;display:inline-block;white-space:nowrap;font-variant-numeric:tabular-nums}
</style>
<main class="myreq">
<h1>طلباتي</h1><p class="lead">جميع طلبات الإعانة الخاصة بك، وحالاتها والمبالغ المرتبطة بها.</p>
@if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif
@if(session('success'))<div class="notice" role="status">{{ session('success') }}</div>@endif
@if(!$beneficiary)
<div class="panel"><p>ليس لديك ملف مستفيد حتى الآن.</p><a class="button" href="{{ route('beneficiary.apply') }}">فتح ملف مستفيد</a></div>
@else
<div class="notice">رقم ملف المستفيد: <strong>{{ $beneficiary->file_number }}</strong> — حالة الملف: <strong>{{ ['PENDING'=>'قيد المراجعة','VERIFIED'=>'معتمد','INELIGIBLE'=>'غير معتمد'][$beneficiary->eligibility_status] ?? $beneficiary->eligibility_status }}</strong></div>
<div class="cards">
 <div class="card"><small>إجمالي الطلبات</small><strong>{{ $statistics['count'] }}</strong></div>
 <div class="card"><small>قيد الدراسة</small><strong>{{ $statistics['pending'] }}</strong></div>
 <div class="card"><small>الموافق عليها (بما فيها المصروفة)</small><strong>{{ $statistics['approved'] }}</strong></div>
 <div class="card"><small>المرفوضة</small><strong>{{ $statistics['rejected'] }}</strong></div>
 <div class="card"><small>إجمالي المبالغ المطلوبة</small><strong class="amount-total"><span class="unit">د.ل</span><span class="amount-number">{{ number_format($statistics['requested'],2) }}</span></strong></div>
 <div class="card"><small>إجمالي المبالغ المعتمدة</small><strong class="amount-total"><span class="unit">د.ل</span><span class="amount-number">{{ number_format($statistics['approved_amount'],2) }}</span></strong></div>
 <div class="card"><small>إجمالي المبالغ المصروفة فعليًا</small><strong class="amount-total"><span class="unit">د.ل</span><span class="amount-number">{{ number_format($statistics['disbursed'],2) }}</span></strong></div>
</div>
@if($beneficiary->eligibility_status === 'VERIFIED' && !$beneficiary->next_renewal_due?->isPast())
<a class="button" href="{{ route('beneficiary.requests.create') }}">+ تقديم طلب إعانة جديد</a>
@else<div class="notice">يمكنك متابعة طلباتك السابقة، لكن تقديم طلب جديد يتطلب ملفًا معتمدًا وساري الصلاحية.</div>@endif
<div class="panel"><h2 style="font-size:20px;font-weight:900;margin-bottom:17px">سجل طلبات الإعانة</h2>
<table><thead><tr><th>رقم الطلب</th><th>نوع الإعانة</th><th>المبلغ المطلوب</th><th>المبلغ المعتمد</th><th>المبلغ المصروف</th><th>الحالة</th><th>تاريخ التقديم</th></tr></thead><tbody>
@forelse($requests as $item)
<tr><td><strong>{{ $item->request_code }}</strong></td><td>{{ ['ORPHAN_SPONSORSHIP'=>'كفالة الأيتام','MARRIAGE_AID'=>'مساعدة الزواج','FOOD_BASKET'=>'سلة غذائية','MEDICAL_AID'=>'مساعدة طبية','EMERGENCY_DEBT'=>'ديون طارئة','GENERAL_RELIEF'=>'مساعدة عامة'][$item->category] ?? $item->category }}</td>
<td><span class="amount-line"><span>د.ل</span><span class="amount-number">{{ number_format($item->requested_amount,2) }}</span></span></td>
<td><span class="amount-line">@if(in_array($item->status,['APPROVED','DISBURSED']))<span>د.ل</span><span class="amount-number">{{ number_format($item->approved_amount ?? 0,2) }}</span>@else — @endif</span></td>
<td><span class="amount-line"><span>د.ل</span><span class="amount-number">{{ number_format($item->disbursements_sum_amount ?? 0,2) }}</span></span></td>
<td><span class="badge {{ in_array($item->status,['APPROVED','DISBURSED']) ? 'ok' : ($item->status === 'REJECTED' ? 'no' : '') }}">{{ ['SUBMITTED'=>'تم التقديم','UNDER_REVIEW'=>'قيد المراجعة','APPROVED'=>'تمت الموافقة','REJECTED'=>'مرفوض','DISBURSED'=>'تم الصرف'][$item->status] ?? $item->status }}</span></td><td>{{ $item->created_at?->format('Y-m-d') }}</td></tr>
@empty<tr><td colspan="7" style="text-align:center;padding:35px">لا توجد طلبات إعانة مسجلة حتى الآن.</td></tr>@endforelse
</tbody></table></div><div style="margin:18px 0">{{ $requests->links() }}</div>
@endif
</main></x-app-layout>
