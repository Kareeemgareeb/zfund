<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="username" :value="__('اسم المستخدم')" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <div class="mt-4"><x-input-label for="national_id" value="الرقم الوطني" /><x-text-input id="national_id" class="block mt-1 w-full" name="national_id" inputmode="numeric" pattern="[0-9]{12}" minlength="12" maxlength="12" title="الرقم الوطني يجب أن يتكون من 12 رقماً" :value="old('national_id')" required /><x-input-error :messages="$errors->get('national_id')" class="mt-2" /></div>
        <div class="mt-4"><x-input-label for="phone_number" value="رقم الهاتف" /><x-text-input id="phone_number" class="block mt-1 w-full" name="phone_number" inputmode="numeric" pattern="[0-9]{10}" minlength="10" maxlength="10" title="رقم الهاتف يجب أن يتكون من 10 أرقام" :value="old('phone_number')" required /><x-input-error :messages="$errors->get('phone_number')" class="mt-2" /></div>
        <div class="mt-4"><x-input-label for="account_type" value="نوع الحساب" /><select id="account_type" name="account_type" required class="block mt-1 w-full rounded-md border-gray-300"><option value="beneficiary" @selected(old('account_type') === 'beneficiary')>مستفيد</option><option value="donor" @selected(old('account_type') === 'donor')>متبرع</option></select><x-input-error :messages="$errors->get('account_type')" class="mt-2" /></div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="'البريد الإلكتروني'" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="'كلمة المرور'" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password" data-password-field
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="'تأكيد كلمة المرور'" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password" data-password-field
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ 'لديك حساب بالفعل؟' }}
            </a>

            <x-primary-button class="ms-4">
                {{ 'إنشاء حساب' }}
            </x-primary-button>
        </div>
        <button type="button" id="toggle-register-password" class="mt-3 text-sm text-emerald-800 underline" aria-pressed="false">إظهار كلمات المرور</button>
    </form>
    <script>document.getElementById('toggle-register-password').addEventListener('click',function(){const fields=document.querySelectorAll('[data-password-field]');const visible=fields[0].type==='password';fields.forEach(f=>f.type=visible?'text':'password');this.textContent=visible?'إخفاء كلمات المرور':'إظهار كلمات المرور';this.setAttribute('aria-pressed',String(visible));});</script>
</x-guest-layout>
