<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $user->hasRole('admin') ? 'لوحة إدارة الصندوق' : ($user->hasRole('donor') ? 'بوابة المتبرع' : 'بوابة المستفيد') }}
        </h2>
    </x-slot>

    @if(!$user->hasRole('admin') && !$user->hasRole('donor'))
    <section class="sz-v2-intro"><div class="sz-v2-intro-inner"><div><span class="sz-v2-eyebrow">الخدمات الإلكترونية / بوابة المستفيد</span><h1>مرحبًا بك في بوابتك الإلكترونية</h1><p>تابع ملفك الاجتماعي وطلبات التجديد من مكان واحد، بخطوات واضحة وآمنة.</p></div><div class="sz-v2-intro-icon" aria-hidden="true">✦</div></div></section>
    @endif
    <div class="sz-v2-dashboard">
        <div class="sz-v2-container">
            <div class="sz-v2-surface">
                <div class="sz-v2-surface-body" @if(!$user->hasRole('admin') && !$user->hasRole('donor')) x-data="beneficiaryPortal()" @endif>

                                        @if(session('status'))
                        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-800 font-bold text-sm">
                            {{ session('status') }}
                        </div>
                    @endif

                    @php
                        $dashboardUnreadNotifications = $user->unreadNotifications()->take(3)->get();
                    @endphp
                    @if($dashboardUnreadNotifications->isNotEmpty())
                        <div class="mb-6 rounded-2xl bg-blue-50/70 border border-blue-200 p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-sm text-[#10518f] flex items-center gap-2">
                                    <span>🔔</span>
                                    <span>تنبيهات جديدة غير مقروءة ({{ $dashboardUnreadNotifications->count() }})</span>
                                </h4>
                                <a href="{{ route('notifications.index') }}" class="text-xs font-bold text-[#10518f] hover:underline">
                                    عرض كل الإشعارات ←
                                </a>
                            </div>
                            <div class="space-y-2">
                                @foreach($dashboardUnreadNotifications as $dun)
                                    <div class="bg-white rounded-xl p-3 border border-blue-100 flex items-center justify-between gap-3 flex-wrap">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-[#10518f]"></span>
                                            <strong class="text-xs text-slate-800">{{ $dun->data['title'] ?? 'إشعار جديد' }}</strong>
                                            <span class="text-xs text-slate-600">— {{ \Illuminate\Support\Str::limit($dun->data['message'] ?? '', 80) }}</span>
                                        </div>
                                        <a href="{{ route('notifications.go', $dun->id) }}" class="text-xs font-bold text-[#10518f] hover:underline whitespace-nowrap">
                                            عرض والتأكيد ←
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($user->hasRole('admin'))
  <div class="sz-p3-role"><span class="sz-p3-tag">لوحة الإدارة</span><h2>إدارة الخدمات والطلبات</h2><p>الوصول السريع إلى مهام صندوق الزكاة مع المحافظة على صلاحيات المستخدم.</p>
  <div class="sz-p3-grid"><a class="sz-p3-tile" href="{{ route('admin.beneficiaries.index') }}"><span class="sz-p3-symbol">▤</span><strong>ملفات المستفيدين</strong><small>مراجعة الملفات الجديدة واعتمادها</small><b>فتح الملفات ←</b></a><a class="sz-p3-tile" href="{{ route('admin.file-renewals.index') }}"><span class="sz-p3-symbol">↻</span><strong>التجديد السنوي</strong><small>مراجعة طلبات تجديد ملفات المستفيدين</small><b>فتح التجديدات ←</b></a><a class="sz-p3-tile" href="{{ route('admin.requests.index') }}"><span class="sz-p3-symbol">▤</span><strong>طلبات الإعانة</strong><small>مراجعة الطلبات وتحديث حالاتها</small><b>فتح الصفحة ←</b></a><a class="sz-p3-tile" href="{{ route('admin.announcements.index') }}"><span class="sz-p3-symbol">📢</span><strong>الإعلانات والتنبيهات</strong><small>نشر التنبيهات وبث الإشعارات للمستخدمين</small><b>إدارة التنبيهات ←</b></a><a class="sz-p3-tile" href="{{ route('admin.zakat-prices.edit') }}"><span class="sz-p3-symbol">◈</span><strong>أسعار الذهب والفضة</strong><small>تحديث أسعار الجرام المستخدمة في الحاسبة</small><b>إدارة الأسعار ←</b></a><a class="sz-p3-tile" href="{{ route('profile.edit') }}"><span class="sz-p3-symbol">♙</span><strong>الملف الشخصي</strong><small>مراجعة معلومات الحساب وكلمة المرور</small><b>إدارة الحساب ←</b></a></div></div>
@elseif($user->hasRole('donor'))
  <div class="sz-p3-role"><span class="sz-p3-tag">بوابة المتبرع</span><h2>أهلًا بك في بوابة المتبرعين</h2><p>يمكنك حساب الزكاة ومتابعة الخدمات المتاحة. الدفع الإلكتروني غير مفعل حتى الآن.</p>
  <div class="sz-p3-grid"><a class="sz-p3-tile" href="{{ route('calculator') }}"><span class="sz-p3-symbol">◈</span><strong>احسب زكاتك</strong><small>حاسبة الأموال والمعادن والأنعام والمحاصيل</small><b>الانتقال للحاسبة ←</b></a><a class="sz-p3-tile" href="{{ route('profile.edit') }}"><span class="sz-p3-symbol">♙</span><strong>بيانات الحساب</strong><small>تحديث البيانات وكلمة المرور</small><b>عرض الملف ←</b></a><div class="sz-p3-tile sz-p3-disabled"><span class="sz-p3-symbol">↗</span><strong>الدفع الإلكتروني</strong><small>سيتم تفعيل الخدمة بعد برمجة الدفع واعتمادها</small><b>قريبًا</b></div></div></div>
@else
                    @if(!$user->beneficiary)
                        <div class="bg-amber-50 border border-amber-200 p-8 rounded-2xl text-center max-w-2xl mx-auto my-6">
                            <div class="w-14 h-14 bg-amber-100 text-amber-700 rounded-2xl flex items-center justify-center mx-auto text-2xl mb-4">
                                📋
                            </div>
                            <h3 class="text-xl font-black text-slate-900 mb-2">حسابك غير مرتبط بملف مستفيد حتى الآن</h3>
                            <p class="text-slate-600 text-sm mb-6 leading-relaxed">
                                لكي تتمكن من الاستفادة من المساعدات وصرف الإعانات الدورية، يرجى تعبئة استمارة التسجيل الاجتماعي لفتح ملف لدى صندوق الزكاة بسرت.
                            </p>
                            <a href="{{ route('beneficiary.apply') }}" class="sz-v2-primary">
                                <span>تعبئة استمارة فتح ملف مستفيد جديد</span>
                                <span>←</span>
                            </a>
                        </div>
                    @else
                        <h3 class="sz-v2-section-title">بيانات الملف الأساسية</h3>
                        
                        <div class="sz-v2-metrics">
                            <div class="sz-v2-metric">
                                <div class="text-sm text-gray-500 mb-1">رقم الملف</div>
                                <div class="text-lg font-bold text-gray-800">{{ $user->beneficiary->file_number }}</div>
                            </div>
                            <div class="sz-v2-metric">
                                <div class="text-sm text-gray-500 mb-1">رب الأسرة</div>
                                <div class="text-lg font-bold text-gray-800">{{ $user->beneficiary->full_name }}</div>
                            </div>
                            <div class="sz-v2-metric">
                                <div class="text-sm text-gray-500 mb-1">حالة الملف</div>
                                <div>
                                    @if($user->beneficiary->eligibility_status === 'VERIFIED')
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">معتمد</span>
                                    @elseif($user->beneficiary->eligibility_status === 'PENDING')
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">قيد المراجعة</span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">غير مستحق</span>
                                    @endif
                                </div>
                            </div>
                            <div class="sz-v2-metric">
                                <div class="text-sm text-gray-500 mb-1">تاريخ التجديد القادم</div>
                                @php
                                    $dueDate = $user->beneficiary->next_renewal_due;
                                    $isExpired = !$dueDate || $dueDate->startOfDay()->lte(today());
                                    $latestRenewal = $user->beneficiary->fileRenewals->sortByDesc('id')->first();
                                    $pendingRenewal = $user->beneficiary->fileRenewals->firstWhere('status', 'PENDING');
                                @endphp
                                <div class="text-lg font-bold {{ $isExpired ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $dueDate ? $dueDate->format('Y-m-d') : 'غير محدد' }}
                                </div>
                            </div>
                        </div>

                        <div class="my-6" dir="rtl" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
                            @if($user->beneficiary->eligibility_status === 'VERIFIED' && !$isExpired)
                                <a href="{{ route('beneficiary.requests.create') }}" class="sz-v2-primary" style="background:#10518f;color:white;padding:13px 20px;border-radius:10px;text-decoration:none;">تقديم طلب إعانة جديد</a>
                            @elseif($user->beneficiary->eligibility_status === 'PENDING')
                                <p style="color:#8b621a;font-weight:700;">ملفك قيد المراجعة. سيتاح تقديم طلب إعانة بعد اعتماده.</p>
                            @elseif($user->beneficiary->eligibility_status === 'INELIGIBLE')
                                <p style="color:#a32b2b;font-weight:700;">الملف غير معتمد. راجع الإدارة لمعرفة السبب.</p>
                            @else
                                <p style="color:#a32b2b;font-weight:700;">انتهت صلاحية الملف. يرجى تجديده قبل تقديم طلب إعانة.</p>
                            @endif
                            <a href="{{ route('beneficiary.requests.index') }}" style="display:inline-block;border:1px solid #10518f;color:#10518f;padding:12px 20px;border-radius:10px;text-decoration:none;">متابعة طلباتي</a>
                        </div>
                        <!-- File Renewal Section -->
                        <h3 class="sz-v2-section-title">تجديد الملف السنوي</h3>
                        
                        <div class="sz-v2-renew">
                            @if($latestRenewal)
                                <div class="mb-4 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm">
                                    <strong>آخر طلب تجديد:</strong>
                                    @switch($latestRenewal->status)
                                        @case('PENDING') <span class="text-amber-700 font-bold">قيد المراجعة</span> @break
                                        @case('APPROVED') <span class="text-green-700 font-bold">تم الاعتماد</span> @break
                                        @case('REJECTED') <span class="text-red-700 font-bold">مرفوض - يمكنك إعادة التقديم عند استحقاق التجديد</span> @break
                                    @endswitch
                                    <div class="text-slate-500 mt-1">تاريخ الطلب: {{ $latestRenewal->submission_date?->format('Y-m-d') }}</div>
                                </div>
                            @endif
                            @if($pendingRenewal)
                                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-800 font-bold">طلب تجديد ملفك قيد المراجعة لدى الإدارة. لا يمكن إرسال طلب آخر حاليًا.</div>
                            @elseif($isExpired)
                                <div class="mb-4 text-red-600 font-bold">
                                    ⚠️ لقد انتهت صلاحية ملفك. يجب تقديم طلب تجديد سنوي لاستمرار صرف المساعدات.
                                </div>
                                
                                <div x-show="!successMessage">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات إضافية للإدارة (مثال: مولود جديد، تغيير السكن)</label>
                                    <textarea x-model="notes" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 mb-4" rows="3"></textarea>
                                    
                                    <button @click="submitRenewal" :disabled="loading" class="sz-v2-primary">
                                        <span x-show="!loading">تقديم طلب التجديد</span>
                                        <span x-show="loading">جاري الإرسال...</span>
                                    </button>
                                </div>

                                <div x-show="errorMessage" x-text="errorMessage" class="mt-3 text-red-600 font-bold"></div>
                                <div x-show="successMessage" x-text="successMessage" class="mt-3 text-green-700 font-bold bg-green-100 p-3 rounded"></div>
                            @else
                                <div class="text-green-700 font-bold">
                                    @if($user->beneficiary->eligibility_status === 'VERIFIED')
                                        ✅ ملفك معتمد وساري المفعول ولا يحتاج إلى تجديد حاليًا.
                                    @else
                                        تاريخ التجديد لم يحِن بعد، لكن ملفك لم يُعتمد بعد.
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                    @endif {{-- نهاية تمييز واجهات الإدارة والمتبرع والمستفيد --}}

                </div>
            </div>
        </div>
    </div>

    @if(!$user->hasRole('admin') && !$user->hasRole('donor'))
    <!-- Alpine.js Logic -->
    <script>
        function beneficiaryPortal() {
            return {
                notes: '',
                loading: false,
                errorMessage: null,
                successMessage: null,

                async submitRenewal() {
                    this.loading = true;
                    this.errorMessage = null;
                    this.successMessage = null;

                    try {
                        const response = await axios.post('/beneficiary/renew-file', {
                            beneficiary_notes: this.notes
                        });
                        this.successMessage = response.data.message;
                    } catch (err) {
                        this.errorMessage = err.response?.data?.error || 'حدث خطأ أثناء إرسال الطلب.';
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
    @endif
</x-app-layout>

