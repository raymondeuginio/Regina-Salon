<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Kami telah mengirimkan kode OTP ke email :email. Masukkan kode tersebut untuk memverifikasi akun Anda.', ['email' => $email]) }}
    </div>

    @if ($status === 'otp-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('Kode OTP berhasil dikirim.') }}
        </div>
    @elseif ($status === 'otp-resent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('Kode OTP baru telah dikirim ke email Anda.') }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.otp.verify') }}">
        @csrf

        <div>
            <x-input-label for="otp" :value="__('Kode OTP')" />
            <x-text-input id="otp" class="block mt-1 w-full" type="text" name="otp" inputmode="numeric" autocomplete="one-time-code" required autofocus />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <div class="mt-4 flex items-center justify-between">
            <x-primary-button>
                {{ __('Verifikasi Email') }}
            </x-primary-button>

            <button form="resend-otp" type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Kirim Ulang Kode') }}
            </button>
        </div>
    </form>

    <form id="resend-otp" method="POST" action="{{ route('verification.otp.resend') }}" class="hidden">
        @csrf
    </form>
</x-guest-layout>
