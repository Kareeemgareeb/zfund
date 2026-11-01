<x-guest-layout>
    <div class="mb-5 text-sm text-slate-600 leading-8">
        <h2 class="text-xl font-extrabold text-slate-900 mb-2">استعادة كلمة المرور</h2>
        أدخل بريدك الإلكتروني المسجل في المنصة، وسنرسل لك رابطًا لإعادة تعيين كلمة المرور.
    </div>
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div>
            <x-input-label for="email" value="البريد الإلكتروني" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div class="flex items-center justify-end mt-5">
            <x-primary-button>إرسال رابط إعادة تعيين كلمة المرور</x-primary-button>
        </div>
    </form>
</x-guest-layout>
