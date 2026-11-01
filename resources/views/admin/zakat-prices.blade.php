<x-app-layout>
<x-slot name="header"><h2 style="font-weight:900;color:#102f52" dir="rtl">إدارة أسعار الذهب والفضة</h2></x-slot>
<section dir="rtl" style="max-width:920px;margin:36px auto 80px;padding:0 18px;font-family:Cairo,Tahoma,sans-serif">
 <div style="background:linear-gradient(110deg,#092e56,#0b4d8d);border-radius:22px;padding:32px;color:#fff;margin-bottom:22px">
  <div style="font-size:13px;opacity:.85">لوحة المدير / حاسبة الزكاة</div>
  <h1 style="font-size:29px;font-weight:900;margin:12px 0">تحديث الأسعار المعتمدة</h1>
  <p style="opacity:.9">أدخل سعر الجرام بالدينار الليبي. ستستخدم الحاسبة آخر أسعار محفوظة.</p>
 </div>
 <div style="background:white;border:1px solid #e0e8f0;border-radius:20px;padding:30px;box-shadow:0 12px 32px #1234560d">
 @if(session('status'))<div role="status" style="background:#ecfdf5;color:#065f46;padding:14px;border-radius:12px;margin-bottom:18px">{{session('status')}}</div>@endif
 @if($errors->any())<div role="alert" style="background:#fff1f2;color:#9f1239;padding:14px;border-radius:12px;margin-bottom:18px"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
 <form method="POST" action="{{ route('admin.zakat-prices.update') }}" style="display:grid;gap:22px">@csrf
 <div><label for="price_date" style="display:block;font-weight:800;margin-bottom:9px">تاريخ الأسعار</label><input id="price_date" type="date" name="price_date" lang="en" dir="ltr" required max="{{now()->toDateString()}}" value="{{old('price_date',now()->toDateString())}}" style="width:100%;padding:13px;border:1px solid #cbd5e1;border-radius:12px;font-variant-numeric:lining-nums"/></div>
 <div><label for="gold" style="display:block;font-weight:800;margin-bottom:9px">سعر جرام الذهب الخالص (عيار 24) — د.ل</label><input id="gold" type="text" lang="en" dir="ltr" inputmode="decimal" class="sz-latin-price" name="gold_price_per_gram" pattern="[0-9]+([.][0-9]+)?" required value="{{ old('gold_price_per_gram', $price?->gold_price_per_gram) }}" placeholder="0.0000" style="width:100%;padding:13px;border:1px solid #cbd5e1;border-radius:12px;font-family:Arial,sans-serif;font-variant-numeric:lining-nums"/></div>
 <div><label for="silver" style="display:block;font-weight:800;margin-bottom:9px">سعر جرام الفضة الخالصة — د.ل</label><input id="silver" type="text" lang="en" dir="ltr" inputmode="decimal" class="sz-latin-price" name="silver_price_per_gram" pattern="[0-9]+([.][0-9]+)?" required value="{{ old('silver_price_per_gram', $price?->silver_price_per_gram) }}" placeholder="0.0000" style="width:100%;padding:13px;border:1px solid #cbd5e1;border-radius:12px;font-family:Arial,sans-serif;font-variant-numeric:lining-nums"/></div>
 <button type="submit" style="display:block;width:100%;padding:16px;background:#0b4d8d!important;color:white!important;border:0;border-radius:12px;font-weight:900;font-size:16px;cursor:pointer">حفظ أسعار الذهب والفضة</button>
 </form>
 <p style="margin-top:20px;color:#64748b;font-size:13px">آخر تاريخ محفوظ: <span dir="ltr" style="font-family:Arial,sans-serif">{{ $price?->price_date?->format('Y-m-d') ?? '—' }}</span>. تأكد من السعر من مصدر موثوق قبل الحفظ.</p>
 </div>
<script>
(function () {
  const toLatin = (value) => String(value).replace(/[٠-٩]/g, c => String(c.charCodeAt(0) - 0x0660)).replace(/[۰-۹]/g, c => String(c.charCodeAt(0) - 0x06F0)).replace(/[٫،]/g, '.').replace(/٬/g, '');
  for (const field of document.querySelectorAll('input[name="gold_price_per_gram"],input[name="silver_price_per_gram"]')) {
    field.value = toLatin(field.value);
    field.addEventListener('input', () => { const start = field.selectionStart; const normalized = toLatin(field.value); if (field.value !== normalized) { field.value = normalized; try { field.setSelectionRange(start, start); } catch(e) {} } });
    field.form?.addEventListener('submit', () => { field.value = toLatin(field.value); });
  }
})();
</script>
</section>
</x-app-layout>
