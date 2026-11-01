<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                نشر إعلان وتنبيه داخلي جديد
            </h2>
            <a href="{{ route('admin.announcements.index') }}" class="text-sm font-bold text-[#10518f] hover:underline">
                ← العودة لقائمة الإعلانات
            </a>
        </div>
    </x-slot>

    <div class="py-8" dir="rtl">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
                
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.announcements.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="title" class="block text-sm font-bold text-slate-700 mb-2">عنوان الإعلان / التنبيه *</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required maxlength="150"
                               class="w-full rounded-xl border-slate-300 shadow-sm focus:border-[#10518f] focus:ring focus:ring-[#10518f]/20 text-sm p-3"
                               placeholder="مثال: بدء استقبال طلبات المساعدات للموسم الجديد">
                    </div>

                    <div>
                        <label for="target_group" class="block text-sm font-bold text-slate-700 mb-2">الفئة المستهدفة بالإشعار الفوري *</label>
                        <select name="target_group" id="target_group" required
                                class="w-full rounded-xl border-slate-300 shadow-sm focus:border-[#10518f] focus:ring focus:ring-[#10518f]/20 text-sm p-3">
                            <option value="all" {{ old('target_group') === 'all' ? 'selected' : '' }}>جميع مستخدمي المنصة (مستفيدين ومتبرعين)</option>
                            <option value="beneficiary" {{ old('target_group') === 'beneficiary' ? 'selected' : '' }}>المستفيدين فقط</option>
                            <option value="donor" {{ old('target_group') === 'donor' ? 'selected' : '' }}>المتبرعين فقط</option>
                            <option value="none" {{ old('target_group') === 'none' ? 'selected' : '' }}>لا ترسل إشعاراً (حفظ كإعلان فقط)</option>
                        </select>
                        <p class="text-xs text-slate-500 mt-1">سيتم إرسال إشعار داخلي فوري لجميع المستخدمين المسجلين ضمن الفئة المختارة.</p>
                    </div>

                    <div>
                        <label for="content" class="block text-sm font-bold text-slate-700 mb-2">نص الإعلان والتفاصيل *</label>
                        <textarea name="content" id="content" rows="5" required
                                  class="w-full rounded-xl border-slate-300 shadow-sm focus:border-[#10518f] focus:ring focus:ring-[#10518f]/20 text-sm p-3"
                                  placeholder="اكتب تفاصيل الإعلان هنا...">{{ old('content') }}</textarea>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_public" id="is_public" value="1" {{ old('is_public', 1) ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-[#10518f] border-slate-300 focus:ring-[#10518f]">
                        <label for="is_public" class="text-sm font-semibold text-slate-700">
                            عرض الإعلان للزوار في الصفحة الرئيسية (عام)
                        </label>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.announcements.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-sm hover:bg-slate-50">
                            إلغاء
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#10518f] hover:bg-[#0c3e6c] text-white font-bold text-sm transition-all shadow-sm">
                            نشر الإعلان وبث التنبيه
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
