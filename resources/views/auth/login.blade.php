<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="'البريد الإلكتروني'" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="'كلمة المرور'" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password" id="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <button type="button" id="toggle-login-password" class="mt-2 text-sm text-emerald-800 underline" aria-pressed="false">إظهار كلمة المرور</button>
        <script>document.getElementById('toggle-login-password').addEventListener('click',function(){const field=document.getElementById('password');const show=field.type==='password';field.type=show?'text':'password';this.textContent=show?'إخفاء كلمة المرور':'إظهار كلمة المرور';this.setAttribute('aria-pressed',String(show));});</script>
        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ 'تذكرني' }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ 'هل نسيت كلمة المرور؟' }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ 'تسجيل الدخول' }}
            </x-primary-button>
        </div>
        @if (Route::has('register'))
            <div class="mt-6 pt-5 border-t border-gray-200 text-center" dir="rtl">
                <p class="text-sm text-gray-600 mb-3">ليس لديك حساب في المنصة؟</p>
                <a href="{{ route('register') }}" style="display:inline-block;padding:11px 22px;border-radius:9px;background:#10518f;color:#fff;text-decoration:none;font-weight:700" onmouseover="this.style.backgroundColor='#073b69'" onmouseout="this.style.backgroundColor='#10518f'">فتح حساب جديد</a>
            </div>
        @endif
    </form>
</x-guest-layout>
