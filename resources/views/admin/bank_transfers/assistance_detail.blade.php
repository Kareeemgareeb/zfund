<style id="sz-global-money-fix">.sz-currency-value{display:inline-flex!important;direction:ltr!important;unicode-bidi:isolate;align-items:baseline;gap:5px;white-space:nowrap;font-family:Arial,Tahoma,sans-serif;font-variant-numeric:tabular-nums}.sz-currency-value .sz-currency-symbol{font-family:Cairo,Tahoma,sans-serif;font-size:.78em;font-weight:700;direction:rtl}.sz-currency-value .sz-currency-number{direction:ltr;unicode-bidi:isolate;font-weight:inherit}</style>
<x-app-layout><x-slot name="header"><h2 class="font-semibold text-xl">تفاصيل طلب الإعانة</h2></x-slot>
<div dir="rtl" class="max-w-4xl mx-auto px-4 py-8" style="font-family:Cairo,Tahoma,sans-serif">
 <div style="background:#fff;border:1px solid #dce7f1;border-radius:18px;padding:30px;box-shadow:0 8px 30px #123a6010">
  <a href="{{ route('admin.bank-transfers.index') }}" style="color:#10518f;text-decoration:underline">← العودة لمراجعة الحوالات</a>
  <h1 style="font-size:26px;font-weight:800;color:#123e68;margin:20px 0">طلب إعانة رقم #{{ $assistance->id }}</h1>
  <p style="color:#66788b">تفاصيل إدارية خاصة بالمراجعة المالية. لا تظهر هذه الصفحة للزوار.</p>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-top:22px">
   <div style="background:#f1f6fb;border-radius:12px;padding:18px"><strong>حالة الطلب</strong><p>{{ $assistance->status }}</p></div>
   <div style="background:#f1f6fb;border-radius:12px;padding:18px"><strong>تاريخ تقديم الطلب</strong><p>{{ $assistance->created_at?->format('Y-m-d H:i') }}</p></div>
   <div style="background:#f1f6fb;border-radius:12px;padding:18px"><strong>المبلغ المحصل من التبرعات المعتمدة</strong><p style="font-weight:800;direction:ltr;text-align:right"><span class="sz-currency-value"><span class="sz-currency-symbol">د.ل</span><span class="sz-currency-number">{{ number_format($collected,2) }}</span></span></p></div>
  </div>
  <div style="margin-top:24px"><a href="{{ route('admin.requests.index') }}" style="display:inline-block;background:#10518f;color:white;border-radius:10px;padding:12px 20px;text-decoration:none">فتح إدارة طلبات الإعانة</a></div>
 </div>
</div></x-app-layout>