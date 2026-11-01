{{-- Unified institutional two-level header; links respect actual Laravel roles and routes. --}}
<link rel="stylesheet" href="{{ asset('css/sirte-unified-header.css') }}">
<header class="szuh" role="banner" dir="rtl">
 <div class="szuh-top"><div class="szuh-wrap szuh-top-inner">
  <a class="szuh-identity" href="{{ url('/') }}" aria-label="الصفحة الرئيسية لصندوق الزكاة فرع سرت">
   <span class="szuh-logo-frame"><img src="{{ asset('images/sirte-zakat-logo.png') }}" width="60" height="60" alt="شعار صندوق الزكاة – فرع سرت"></span>
   <span class="szuh-identity-copy"><strong>صندوق الزكاة – فرع سرت</strong><small>البوابة الإلكترونية للخدمات</small></span>
  </a>
  <div class="szuh-tools">
   @auth
    @php
     $szuhUnreadCount = auth()->user()->unreadNotifications()->count();
     $szuhRecentNotifs = auth()->user()->notifications()->take(5)->get();
    @endphp
    <div class="szuh-notif-wrapper">
     <button type="button" class="szuh-notif-btn" id="szuhNotifBtn" aria-label="الإشعارات" title="الإشعارات" onclick="var d=document.getElementById('szuhNotifDropdown');d.classList.toggle('szuh-show');">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
       <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
       <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
      </svg>
      @if($szuhUnreadCount > 0)
       <span class="szuh-notif-badge">{{ $szuhUnreadCount > 99 ? '99+' : $szuhUnreadCount }}</span>
      @endif
     </button>
     <div class="szuh-notif-dropdown" id="szuhNotifDropdown">
      <div class="szuh-notif-header">
       <strong>التنبيهات والإشعارات</strong>
       @if($szuhUnreadCount > 0)
        <span class="szuh-notif-pill">{{ $szuhUnreadCount }} غير مقروء</span>
       @endif
      </div>
      <div class="szuh-notif-body">
       @forelse($szuhRecentNotifs as $sn)
        @php $isUnread = is_null($sn->read_at); @endphp
        <a href="{{ route('notifications.go', $sn->id) }}" class="szuh-notif-item {{ $isUnread ? 'is-unread' : '' }}">
         <div class="szuh-notif-item-title">{{ $sn->data['title'] ?? 'إشعار جديد' }}</div>
         <div class="szuh-notif-item-snippet">{{ Str::limit($sn->data['message'] ?? '', 65) }}</div>
         <div class="szuh-notif-item-time">{{ $sn->created_at->diffForHumans() }}</div>
        </a>
       @empty
        <div class="szuh-notif-empty">لا توجد تنبيهات جديدة</div>
       @endforelse
      </div>
      <div class="szuh-notif-footer">
       <a href="{{ route('notifications.index') }}">عرض جميع الإشعارات ←</a>
      </div>
     </div>
    </div>
    <span class="szuh-username" title="الحساب الحالي">{{ auth()->user()->username ?? auth()->user()->name ?? 'حسابي' }}</span>
    <a class="szuh-account" href="{{ route('profile.edit') }}" @if(request()->routeIs('profile.*')) aria-current="page" @endif>حسابي</a>
    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="szuh-logout">تسجيل الخروج</button></form>
   @else
    <a class="szuh-account" href="{{ route('login') }}">تسجيل الدخول</a>
    <a class="szuh-register" href="{{ route('register') }}">إنشاء حساب</a>
   @endauth
  </div>
 </div></div>
 <div class="szuh-nav-row"><div class="szuh-wrap szuh-nav-inner">
  <button class="szuh-menu-toggle" type="button" aria-controls="szuh-navigation" aria-expanded="false" onclick="var n=document.getElementById('szuh-navigation');var x=this.getAttribute('aria-expanded')==='true';this.setAttribute('aria-expanded',String(!x));n.classList.toggle('szuh-open',!x)">☰ <span>القائمة الرئيسية</span></button>
  <nav class="szuh-nav" id="szuh-navigation" aria-label="القائمة الرئيسية">
   <a href="{{ url('/') }}" @if(request()->is('/')) aria-current="page" @endif>الرئيسية</a>
   <a href="{{ route('projects.index') }}" @if(request()->routeIs('projects.*')) aria-current="page" @endif>المشاريع الخيرية</a>
   <a href="{{ route('assistance.public.index') }}" @if(request()->routeIs('assistance.public.*')) aria-current="page" @endif>طلبات الإعانة</a>
   <a href="{{ route('calculator') }}" @if(request()->routeIs('calculator')) aria-current="page" @endif>حاسبة الزكاة</a>
   @auth
    <a href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>لوحة التحكم</a>
    @if(auth()->user()->hasRole('admin'))
     <a href="{{ route('admin.projects.index') }}" @if(request()->routeIs('admin.projects.*')) aria-current="page" @endif>إدارة المشاريع</a>
     <a href="{{ route('admin.requests.index') }}" @if(request()->routeIs('admin.requests.*')) aria-current="page" @endif>إدارة الإعانات</a>
     <a href="{{ route('admin.bank-transfers.index') }}" @if(request()->routeIs('admin.bank-transfers.*')) aria-current="page" @endif>مراجعة الحوالات</a>
     <a href="{{ route('admin.bank-accounts.index') }}" @if(request()->routeIs('admin.bank-accounts.*')) aria-current="page" @endif>الحسابات المصرفية</a>
     <a href="{{ route('admin.beneficiaries.index') }}" @if(request()->routeIs('admin.beneficiaries.*')) aria-current="page" @endif>المستفيدون</a>
     <a href="{{ route('admin.announcements.index') }}" @if(request()->routeIs('admin.announcements.*')) aria-current="page" @endif>الإعلانات والتنبيهات</a>
     <a href="{{ route('admin.zakat-prices.edit') }}" @if(request()->routeIs('admin.zakat-prices.*')) aria-current="page" @endif>أسعار المعادن</a>
    @elseif(auth()->user()->hasRole('beneficiary'))
     <a href="{{ route('beneficiary.requests.index') }}" @if(request()->routeIs('beneficiary.*')) aria-current="page" @endif>طلباتي</a>
    @elseif(auth()->user()->hasRole('donor'))
     <a href="{{ route('donations.history') }}" @if(request()->routeIs('donations.history')) aria-current="page" @endif>تبرعاتي</a>
    @endif
   @endauth
  </nav>
 </div></div>
</header>
<script>
document.addEventListener('click', function(e) {
  if (!e.target.closest('.szuh-notif-wrapper')) {
    var dd = document.getElementById('szuhNotifDropdown');
    if (dd) dd.classList.remove('szuh-show');
  }
});
</script>
