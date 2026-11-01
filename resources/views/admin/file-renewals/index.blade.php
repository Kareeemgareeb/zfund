<x-app-layout><style>.sz-sortbar{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin:15px 0 20px;direction:rtl}.sz-sortbar select{border:1px solid #b9c9dc;border-radius:9px;padding:9px 12px;color:#153c63;background:#fff;min-width:180px}.sz-sortbar button{background:#10518f;color:#fff;border:1px solid #10518f;padding:9px 17px;border-radius:9px;cursor:pointer}.sz-sortbar button:hover,.sz-sortbar button:focus{background:#073b69;color:#fff}.sz-renew-filter{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:9px 16px;border-radius:9px;border:1px solid #c5d4e2;background:#fff;color:#123b63!important;text-decoration:none!important;font-weight:700;transition:background .15s,color .15s}.sz-renew-filter:hover,.sz-renew-filter:focus-visible{background:#e5f0fc!important;color:#073b69!important;outline:2px solid #10518f;outline-offset:2px}.sz-renew-filter.is-active,.sz-renew-filter.is-active:hover,.sz-renew-filter.is-active:focus-visible{background:#10518f!important;color:#fff!important;border-color:#10518f!important}
.sz-renew-approve,
.sz-renew-approve:hover,
.sz-renew-approve:focus,
.sz-renew-approve:active {
    background-color: #1e40af !important;
    color: #ffffff !important;
    border: 1px solid #1e40af !important;
    font-weight: 700;
    cursor: pointer;
}

.sz-renew-approve:hover,
.sz-renew-approve:focus {
    background-color: #1e3a8a !important;
    border-color: #1e3a8a !important;
}
/* زر رفض طلب التجديد */
.sz-renew-reject {
    background-color: #dc2626 !important;
    color: #ffffff !important;
    border: 1px solid #dc2626 !important;
    font-weight: 700;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.sz-renew-reject:hover,
.sz-renew-reject:focus,
.sz-renew-reject:active {
    background-color: #991b1b !important;
    color: #ffffff !important;
    border-color: #991b1b !important;
}

</style>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">مراجعة التجديد السنوي لملفات المستفيدين</h2></x-slot>
    <div class="max-w-7xl mx-auto py-8 px-4" dir="rtl">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h1 class="text-xl font-bold text-slate-900 mb-2">طلبات تجديد الملفات</h1>
            <p class="text-slate-600 mb-5">يمكن للإدارة مراجعة الطلبات واعتماد التجديد أو رفضه. لا تؤدي هذه الإجراءات إلى تعديل بنية قاعدة البيانات.</p>
            @if(session('status'))<div class="bg-green-50 text-green-800 border border-green-200 p-3 rounded-lg mb-4">{{ session('status') }}</div>@endif
            @if($errors->any())<div class="bg-red-50 text-red-800 border border-red-200 p-3 rounded-lg mb-4">{{ $errors->first() }}</div>@endif
            <div class="flex flex-wrap gap-2 mb-6">
                @foreach(['PENDING'=>'قيد المراجعة','APPROVED'=>'معتمد','REJECTED'=>'مرفوض','ALL'=>'جميع الطلبات'] as $key=>$label)
                    <a href="{{ route('admin.file-renewals.index', ['status'=>$key, 'sort'=>request('sort','date_desc')]) }}" class="sz-renew-filter {{ $status === $key ? 'is-active' : '' }}">{{ $label }}</a>
                @endforeach
            </div>
            <form method="GET" action="{{ route('admin.file-renewals.index') }}" class="sz-sortbar"><input type="hidden" name="status" value="{{ $status }}"><label for="renewal-sort">ترتيب حسب:</label><select id="renewal-sort" name="sort"><option value="date_desc" {{ request('sort', 'date_desc') === 'date_desc' ? 'selected' : '' }}>الأحدث أولًا</option><option value="date_asc" {{ request('sort') === 'date_asc' ? 'selected' : '' }}>الأقدم أولًا</option><option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>الاسم: أ إلى ي</option><option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>الاسم: ي إلى أ</option></select><button type="submit">تطبيق الفرز</button></form>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right border-collapse">
                    <thead><tr class="bg-slate-100 text-slate-700"><th class="p-3">المستفيد</th><th class="p-3">رقم الملف</th><th class="p-3">السنة</th><th class="p-3">تاريخ الطلب</th><th class="p-3">الملاحظات</th><th class="p-3">الحالة</th><th class="p-3">القرار</th></tr></thead>
                    <tbody>
                    @forelse($renewals as $renewal)
                        <tr class="border-b border-slate-100 align-top">
                            <td class="p-3">{{ $renewal->beneficiary?->full_name ?? 'ملف غير متاح' }}</td>
                            <td class="p-3">{{ $renewal->beneficiary?->file_number ?? '—' }}</td>
                            <td class="p-3">{{ $renewal->renewal_year }}</td>
                            <td class="p-3">{{ $renewal->submission_date?->format('Y-m-d') }}</td>
                            <td class="p-3 max-w-xs break-words">{{ $renewal->beneficiary_notes ?: 'لا توجد' }}</td>
                            <td class="p-3 font-semibold">{{ ['PENDING'=>'قيد المراجعة','APPROVED'=>'معتمد','REJECTED'=>'مرفوض'][$renewal->status] ?? $renewal->status }}</td>
                            <td class="p-3">
                                @if($renewal->status === 'PENDING')
                                    <div class="flex gap-2 flex-wrap">
                                        <form method="POST" action="{{ route('admin.file-renewals.decision', $renewal) }}" onsubmit="return confirm('هل تريد اعتماد طلب التجديد؟')">@csrf<input type="hidden" name="status" value="APPROVED"><button
    type="submit"
    class="sz-renew-approve px-3 py-2 rounded-lg"
>
    اعتماد
</button></form>
                                        <form method="POST" action="{{ route('admin.file-renewals.decision', $renewal) }}" onsubmit="return confirm('هل تريد رفض طلب التجديد؟')">@csrf<input type="hidden" name="status" value="REJECTED"><button type="submit" class="sz-renew-reject px-3 py-2 rounded-lg">
    رفض
</button></form>
                                    </div>
                                @else
                                    <span class="text-slate-500">تمت المراجعة</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-8 text-center text-slate-500">لا توجد طلبات تجديد في هذه القائمة.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-5">{{ $renewals->links() }}</div>
        </div>
    </div>
</x-app-layout>
