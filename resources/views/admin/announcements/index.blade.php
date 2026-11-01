<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                إدارة الإعلانات والتنبيهات العامة
            </h2>
            <a href="{{ route('admin.announcements.create') }}" class="px-4 py-2 bg-[#10518f] hover:bg-[#0c3e6c] text-white text-sm font-bold rounded-xl transition-all shadow-sm flex items-center gap-2">
                <span>+</span>
                <span>نشر إعلان / تنبيه جديد</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8" dir="rtl">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('status'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-800 font-bold text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <table class="min-w-full divide-y divide-slate-200 text-right">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">العنوان</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">الناشر</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">تاريخ النشر</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($announcements as $announcement)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 text-sm">{{ $announcement->title }}</div>
                                    <div class="text-xs text-slate-500 mt-1 line-clamp-1">{{ Str::limit($announcement->content, 90) }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $announcement->creator->username ?? 'مدير النظام' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($announcement->is_public)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            عام (منشور)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                            داخلي
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500">
                                    {{ $announcement->created_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('هل تريد حذف هذا الإعلان؟')" class="text-xs font-bold text-rose-600 hover:text-rose-800">
                                            حذف
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400">
                                    لا توجد إعلانات منشورة حالياً.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($announcements->hasPages())
                <div class="pt-4">
                    {{ $announcements->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
