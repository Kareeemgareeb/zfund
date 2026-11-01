<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('تقديم طلب فتح ملف مستفيد جديد') }}
        </h2>
    </x-slot>

    <div class="sz-v2-form-page">
        <div class="sz-v2-form-container">
            <div class="sz-v2-surface">
                <div class="sz-v2-surface-body">
                    <div class="border-b border-slate-200 pb-5 mb-6">
                        <h3 class="sz-v2-section-title">استمارة الحصر والتسجيل الاجتماعي</h3>
                        <p class="text-sm text-slate-500 mt-1">الرجاء إدخال البيانات الشخصية والاجتماعية بدقة ليتمكن الباحث الاجتماعي من دراسة الحالة.</p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-bold">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('beneficiary.apply.store') }}" class="space-y-6 sz-v2-form">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Full Name -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-slate-700 mb-1">الاسم الرباعي الكامل لرب الأسرة *</label>
                                <input type="text" name="full_name" value="{{ old('full_name') }}" required class="w-full border-slate-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                            </div>

                            <!-- Gender -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">الجنس *</label>
                                <select name="gender" required class="w-full border-slate-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="MALE" {{ old('gender') === 'MALE' ? 'selected' : '' }}>ذكر</option>
                                    <option value="FEMALE" {{ old('gender') === 'FEMALE' ? 'selected' : '' }}>أنثى</option>
                                </select>
                            </div>

                            <!-- Birth Date -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">تاريخ الميلاد *</label>
                                <div class="relative">
                                    <input type="date" id="birth_date_picker" name="birth_date" value="{{ old('birth_date') }}" required max="{{ now()->subDay()->toDateString() }}" class="w-full border-slate-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 pr-10">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                        📅
                                    </div>
                                </div>
                            </div>

                            <!-- Marital Status -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">الحالة الاجتماعية *</label>
                                <select name="marital_status" required class="w-full border-slate-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="MARRIED" {{ old('marital_status') === 'MARRIED' ? 'selected' : '' }}>متزوج/ـة</option>
                                    <option value="SINGLE" {{ old('marital_status') === 'SINGLE' ? 'selected' : '' }}>أعزب/عزباء</option>
                                    <option value="WIDOWED" {{ old('marital_status') === 'WIDOWED' ? 'selected' : '' }}>أرمل/ـة</option>
                                    <option value="DIVORCED" {{ old('marital_status') === 'DIVORCED' ? 'selected' : '' }}>مطلق/ـة</option>
                                </select>
                            </div>

                            <!-- Family Members Count -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">عدد أفراد الأسرة المقيمين *</label>
                                <input type="number" name="family_members_count" min="1" value="{{ old('family_members_count', 1) }}" required class="w-full border-slate-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                            </div>

                            <!-- City -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">المدينة / الفرع البلدي *</label>
                                <input type="text" name="city" value="{{ old('city', 'سرت') }}" required class="w-full border-slate-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                            </div>

                            <!-- Address -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">عنوان السكن بالتفصيل (الحي / الشارع / علامة دالة) *</label>
                                <input type="text" name="address" value="{{ old('address') }}" required placeholder="مثال: حي الزعفران، قرب المسجد" class="w-full border-slate-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                            </div>

                            <!-- Bank Name -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">اسم المصرف (اختياري)</label>
                                <input type="text" name="bank_name" value="{{ old('bank_name') }}" placeholder="مثال: مصرف الجمهورية - فرع سرت" class="w-full border-slate-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                            </div>

                            <!-- Bank Account No -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">رقم الحساب المصرفي (إن وجد)</label>
                                <input type="text" name="bank_account_no" value="{{ old('bank_account_no') }}" placeholder="أدخل رقم الحساب لتحويل الإعانات" class="w-full border-slate-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>

                        <div class="rounded-2xl border border-blue-100 bg-blue-50/50 p-5">
                            <label for="case_description" class="block text-sm font-bold text-slate-800 mb-2">وصف الحالة الاجتماعية والاحتياج *</label>
                            <p class="text-sm text-slate-600 mb-3">اشرح ظروف الأسرة، ومصدر الدخل، وسبب الحاجة إلى المساعدة (20 حرفًا على الأقل).</p>
                            <textarea id="case_description" name="case_description" rows="5" maxlength="3000" minlength="20" required class="w-full border-slate-300 rounded-xl focus:ring-blue-500 focus:border-blue-500" placeholder="مثال: عدد المعالين، الوضع الصحي أو المعيشي، وأسباب طلب المساعدة">{{ old('case_description') }}</textarea>
                            @error('case_description') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                        </div>
                        <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold hover:bg-slate-50 transition">إلغاء</a>
                            <button type="submit" class="sz-v2-primary">
                                إرسال طلب فتح الملف
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
</x-app-layout>