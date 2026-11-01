<x-app-layout><style>.sz-sortbar{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin:15px 0 20px;direction:rtl}.sz-sortbar select{border:1px solid #b9c9dc;border-radius:9px;padding:9px 12px;color:#153c63;background:#fff;min-width:180px}.sz-sortbar button{background:#10518f;color:#fff;border:1px solid #10518f;padding:9px 17px;border-radius:9px;cursor:pointer}.sz-sortbar button:hover,.sz-sortbar button:focus{background:#073b69;color:#fff}.sz-renew-filter{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:9px 16px;border-radius:9px;border:1px solid #c5d4e2;background:#fff;color:#123b63!important;text-decoration:none!important;font-weight:700;transition:background .15s,color .15s}.sz-renew-filter:hover,.sz-renew-filter:focus-visible{background:#e5f0fc!important;color:#073b69!important;outline:2px solid #10518f;outline-offset:2px}.sz-renew-filter.is-active,.sz-renew-filter.is-active:hover,.sz-renew-filter.is-active:focus-visible{background:#10518f!important;color:#fff!important;border-color:#10518f!important}</style>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('إدارة ومراجعة طلبات الإعانة') }}
        </h2>
    </x-slot>

    <section class="sz-v2-intro"><div class="sz-v2-intro-inner"><div><span class="sz-v2-eyebrow">الإدارة / طلبات الإعانة</span><h1>مراجعة طلبات الإعانة</h1><p>عرض الطلبات وتحديث حالاتها ضمن واجهة واضحة وسهلة المتابعة.</p></div><div class="sz-v2-intro-icon" aria-hidden="true">▤</div></div></section>
    <div class="sz-v2-dashboard" dir="rtl">
        <div class="sz-v2-container">
            <div class="sz-v2-surface sz-v2-surface-body">
                
                @if($errors->any())<div class="alert alert-danger mb-4" style="color:#b42318;padding:14px;background:#fff0ef;border-radius:8px">{{ $errors->first() }}</div>@endif
                @if(session('success'))
                    <div class="alert alert-success mb-4 text-green-600 bg-green-100 p-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="GET" action="{{ route('admin.requests.index') }}" class="sz-sortbar"><label for="assistance-sort">ترتيب حسب:</label><select id="assistance-sort" name="sort"><option value="date_desc" {{ request('sort', 'date_desc') === 'date_desc' ? 'selected' : '' }}>الأحدث أولًا</option><option value="date_asc" {{ request('sort') === 'date_asc' ? 'selected' : '' }}>الأقدم أولًا</option><option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>الاسم: أ إلى ي</option><option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>الاسم: ي إلى أ</option></select><button type="submit">تطبيق الفرز</button></form>
                <div class="sz-v2-table-scroll">
                    <table class="sz-v2-table">
                        <thead>
                            <tr class="bg-gray-100 border-b">
                                <th class="p-3">رقم الطلب</th>
                                <th class="p-3">اسم المستفيد</th>
                                <th class="p-3">نوع الإعانة</th>
                                <th class="p-3">المبلغ المطلوب</th>
                                <th class="p-3">الحالة</th>
                                <th class="p-3">تاريخ التقديم</th>
                                <th class="p-3">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $req)
                                <tr class="border-b">
                                    <td class="p-3"><a style="color:#10518f;text-decoration:underline" href="{{ route('admin.requests.show', $req->id) }}">{{ $req->request_code }}</a></td>
                                    <td class="p-3">{{ $req->beneficiary?->full_name ?? 'غير متوفر' }}</td>
                                    <td class="p-3">{{ ['ORPHAN_SPONSORSHIP'=>'كفالة الأيتام','MARRIAGE_AID'=>'مساعدة الزواج','FOOD_BASKET'=>'سلة غذائية','MEDICAL_AID'=>'مساعدة طبية','EMERGENCY_DEBT'=>'ديون طارئة','GENERAL_RELIEF'=>'مساعدة عامة'][$req->category] ?? $req->category }}</td>
                                    <td class="p-3">{{ $req->requested_amount }} د.ل</td>
                                    <td class="p-3">
                                        <span class="px-2 py-1 rounded text-sm 
                                            @if($req->status == 'APPROVED') bg-green-200 text-green-850 
                                            @elseif($req->status == 'REJECTED') bg-red-200 text-red-800 
                                            @else bg-yellow-200 text-yellow-800 @endif">
                                            {{ ['SUBMITTED'=>'تم التقديم','UNDER_REVIEW'=>'قيد المراجعة','APPROVED'=>'تمت الموافقة','REJECTED'=>'مرفوض','DISBURSED'=>'تم الصرف'][$req->status] ?? $req->status }}
                                        </span>
                                    </td>
                                    <td class="p-3">{{ $req->created_at?->format('Y-m-d') }}</td>
                                    <td class="p-3">
                                        @if(!in_array($req->status, ['REJECTED','DISBURSED']))
                                        <form action="{{ route('admin.requests.updateStatus', $req->id) }}" method="POST" class="inline-flex gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="form-select text-sm border rounded p-1">
                                                
                                                <option value="APPROVED" {{ $req->status == 'APPROVED' ? 'selected' : '' }}>موافقة</option>
                                                <option value="REJECTED" {{ $req->status == 'REJECTED' ? 'selected' : '' }}>رفض</option>
                                                <option value="UNDER_REVIEW" {{ $req->status == 'UNDER_REVIEW' ? 'selected' : '' }}>قيد المراجعة</option>
                                            </select>
                                            <button type="submit" class="sz-v2-small-action">تحديث</button>
                                        </form>
                                        @else <span>مغلق</span> @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center p-4 text-gray-500">لا توجد طلبات إعانة مسجلة حالياً.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $requests->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>