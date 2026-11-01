<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                مركز الإشعارات والتنبيهات
            </h2>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition-colors border border-slate-300 flex items-center gap-2">
                        <span>✓</span>
                        <span>تعيين الكل كمقروء</span>
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-8" dir="rtl">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('status'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-800 font-bold text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Tabs / Filter -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-3 flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-2">
                    <a href="{{ route('notifications.index', ['status' => 'all']) }}" 
                       class="px-4 py-2 rounded-xl text-sm font-bold transition-all {{ $status === 'all' ? 'bg-[#10518f] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                        جميع الإشعارات ({{ Auth::user()->notifications()->count() }})
                    </a>
                    <a href="{{ route('notifications.index', ['status' => 'unread']) }}" 
                       class="px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ $status === 'unread' ? 'bg-[#10518f] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                        <span>غير المقروءة</span>
                        @if($unreadCount > 0)
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $status === 'unread' ? 'bg-white text-[#10518f]' : 'bg-red-500 text-white' }} font-bold">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('notifications.index', ['status' => 'read']) }}" 
                       class="px-4 py-2 rounded-xl text-sm font-bold transition-all {{ $status === 'read' ? 'bg-[#10518f] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                        المقروءة
                    </a>
                </div>
            </div>

            <!-- Notifications Feed -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden divide-y divide-slate-100">
                @forelse($notifications as $notification)
                    @php
                        $isUnread = is_null($notification->read_at);
                        $data = $notification->data;
                        $icon = $data['icon'] ?? 'bell';
                        $actionUrl = route('notifications.go', $notification->id);
                    @endphp
                    <div class="p-5 hover:bg-slate-50 transition-colors flex items-start gap-4 {{ $isUnread ? 'bg-blue-50/40 border-r-4 border-r-[#10518f]' : '' }}">
                        <!-- Icon Indicator -->
                        <div class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center text-lg {{ $isUnread ? 'bg-blue-100 text-[#10518f]' : 'bg-slate-100 text-slate-500' }}">
                            @if($icon === 'check-circle')
                                <span>✓</span>
                            @elseif($icon === 'x-circle' || $icon === 'alert-circle')
                                <span>✕</span>
                            @elseif($icon === 'clock')
                                <span>⏱</span>
                            @elseif($icon === 'alert-triangle')
                                <span>⚠</span>
                            @else
                                <span>🔔</span>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 flex-wrap mb-1">
                                <a href="{{ $actionUrl }}" class="font-bold text-slate-900 hover:text-[#10518f] text-base">
                                    {{ $data['title'] ?? 'إشعار جديد' }}
                                </a>
                                <div class="flex items-center gap-2">
                                    @if($isUnread)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-[#10518f]">
                                            جديد
                                        </span>
                                    @endif
                                    <span class="text-xs text-slate-400">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            <p class="text-sm text-slate-600 leading-relaxed mb-3">
                                {{ $data['message'] ?? '' }}
                            </p>

                            <!-- Actions -->
                            <div class="flex items-center gap-3 pt-1">
                                <a href="{{ $actionUrl }}" class="text-xs font-bold text-[#10518f] hover:underline flex items-center gap-1">
                                    <span>عرض التفاصيل</span>
                                    <span>←</span>
                                </a>

                                @if($isUnread)
                                    <form method="POST" action="{{ route('notifications.mark-read', $notification->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs text-slate-500 hover:text-slate-800 font-medium">
                                            تعيين كمقروء
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('هل أنت متأكد من حذف هذا الإشعار؟')" class="text-xs text-rose-500 hover:text-rose-700 font-medium">
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-16 px-4 text-center">
                        <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                            🔕
                        </div>
                        <h4 class="text-lg font-bold text-slate-800 mb-1">لا توجد إشعارات حالياً</h4>
                        <p class="text-sm text-slate-500 max-w-sm mx-auto">
                            عندما يصدر قرار بشأن طلباتك أو يتم تحديث ملفك، ستظهر جميع التنبيهات هنا فوراً.
                        </p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="pt-4">
                    {{ $notifications->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
