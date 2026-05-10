<x-guest-layout>
    {{-- Icono de correo --}}
    <div class="flex justify-center mb-5">
        <div class="bg-indigo-100 rounded-full p-4">
            <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5H4.5a2.25 2.25 0 00-2.25 2.25m19.5 0l-9.75 7.5-9.75-7.5" />
            </svg>
        </div>
    </div>

    <h2 class="text-center text-lg font-semibold text-gray-800 mb-1">Verifica tu correo electrónico</h2>

    <p class="text-center text-sm text-gray-500 mb-4">
        Enviamos un enlace de verificación a<br>
        <span class="font-medium text-gray-700">{{ auth()->user()->email }}</span>
    </p>

    {{-- Pasos --}}
    <ol class="text-sm text-gray-600 space-y-1 mb-5 list-decimal list-inside">
        <li>Abre tu bandeja de entrada (revisa también Spam).</li>
        <li>Haz clic en el enlace del correo de verificación.</li>
        <li>Vuelve aquí e inicia sesión.</li>
    </ol>

    {{-- Mensaje de reenvío exitoso --}}
    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-3 rounded-md bg-green-50 border border-green-200 text-sm text-green-700">
            Correo reenviado. Revisa tu bandeja de entrada.
        </div>
    @endif

    {{-- Botón de reenvío con countdown --}}
    <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
        @csrf
        <button
            id="resend-btn"
            type="submit"
            class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition disabled:opacity-50 disabled:cursor-not-allowed"
        >
            Reenviar correo de verificación
        </button>
    </form>

    <div class="text-center">
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cerrar sesión
            </button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const COOLDOWN_SECONDS = 60;
            const STORAGE_KEY = 'verificationSentAt';
            const btn = document.getElementById('resend-btn');

            if (!btn) return;

            @if (session('status') == 'verification-link-sent')
                localStorage.setItem(STORAGE_KEY, Date.now().toString());
            @endif

            function startCountdown(remaining) {
                btn.disabled = true;

                const interval = setInterval(() => {
                    remaining--;
                    btn.textContent = `Reenviar en ${remaining}s`;

                    if (remaining <= 0) {
                        clearInterval(interval);
                        btn.disabled = false;
                        btn.textContent = 'Reenviar correo de verificación';
                        localStorage.removeItem(STORAGE_KEY);
                    }
                }, 1000);

                btn.textContent = `Reenviar en ${remaining}s`;
            }

            const sentAt = localStorage.getItem(STORAGE_KEY);
            if (sentAt) {
                const elapsed = Math.floor((Date.now() - parseInt(sentAt, 10)) / 1000);
                const remaining = COOLDOWN_SECONDS - elapsed;
                if (remaining > 0) {
                    startCountdown(remaining);
                } else {
                    localStorage.removeItem(STORAGE_KEY);
                }
            }
        });
    </script>
</x-guest-layout>
