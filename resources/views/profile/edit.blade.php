<x-app-layout>
 <x-slot name="header"><h2 class="text-xl font-bold text-slate-900">الملف الشخصي وإعدادات الحساب</h2></x-slot>
 <div class="sz-profile-shell" dir="rtl">
  <div class="sz-profile-heading"><span>إعدادات الحساب</span><h1>بياناتك الشخصية</h1><p>حدّث معلومات الاتصال وكلمة المرور من مكان واحد، بكل سهولة وأمان.</p></div>
  <div class="sz-profile-grid">
   <main class="sz-profile-main">
    <div class="sz-profile-card">@include('profile.partials.update-profile-information-form')</div>
    <div class="sz-profile-card">@include('profile.partials.update-password-form')</div>
    <div class="sz-profile-card sz-profile-danger">@include('profile.partials.delete-user-form')</div>
   </main>
   <aside class="sz-profile-aside"><div class="sz-profile-avatar">{{ mb_substr($user->username, 0, 1) }}</div><h2>{{ $user->username }}</h2><p>{{ $user->email ?: 'لم تتم إضافة بريد إلكتروني' }}</p><div class="sz-profile-rule"></div><strong>معلومات الحساب</strong><dl><dt>الرقم الوطني</dt><dd>{{ $user->national_id ?: 'غير متوفر' }}</dd><dt>رقم الهاتف</dt><dd>{{ $user->phone_number ?: 'غير متوفر' }}</dd><dt>حالة الحساب</dt><dd>{{ $user->status === 'ACTIVE' ? 'نشط' : 'غير نشط' }}</dd></dl><a href="{{ route('dashboard') }}" class="sz-profile-back">العودة إلى لوحة التحكم ←</a></aside>
  </div>
 </div>
 <style>
 .sz-profile-shell{max-width:1150px;margin:0 auto;padding:38px 20px 80px;font-family:Cairo,Tahoma,sans-serif;color:#143252}.sz-profile-heading{margin-bottom:28px}.sz-profile-heading span{color:#1a5795;font-size:13px;font-weight:800}.sz-profile-heading h1{font-size:32px;font-weight:900;margin:8px 0}.sz-profile-heading p{color:#64748b}.sz-profile-grid{display:grid;grid-template-columns:minmax(0,1fr) 300px;gap:24px;align-items:start}.sz-profile-main{display:grid;gap:20px}.sz-profile-card,.sz-profile-aside{background:#fff;border:1px solid #e2e8f0;border-radius:20px;padding:28px;box-shadow:0 12px 34px rgba(16,48,80,.055)}.sz-profile-card h2{font-weight:800!important;color:#102f52!important}.sz-profile-card input{border-radius:10px}.sz-profile-card input:focus{border-color:#125a9e!important;--tw-ring-color:#125a9e!important}.sz-profile-aside{text-align:center;position:sticky;top:25px}.sz-profile-avatar{width:68px;height:68px;border-radius:18px;background:#0b4d8d;color:white;font-size:30px;font-weight:900;display:grid;place-items:center;margin:0 auto 14px}.sz-profile-aside h2{font-size:20px;font-weight:900}.sz-profile-aside p{font-size:12px;color:#64748b;overflow-wrap:anywhere;margin-top:6px}.sz-profile-rule{height:1px;background:#e2e8f0;margin:22px 0}.sz-profile-aside strong{display:block;text-align:right;margin-bottom:15px}.sz-profile-aside dl{display:grid;gap:12px;text-align:right}.sz-profile-aside dt{font-size:12px;color:#64748b}.sz-profile-aside dd{font-size:14px;font-weight:700;overflow-wrap:anywhere}.sz-profile-back{display:block;background:#edf4fb;color:#0b4d8d;font-weight:800;padding:13px;margin-top:24px;border-radius:12px}.sz-profile-danger{border-color:#fee2e2}@media(max-width:800px){.sz-profile-grid{grid-template-columns:1fr}.sz-profile-aside{position:static;grid-row:1}.sz-profile-heading h1{font-size:26px}}
 </style>
</x-app-layout>
