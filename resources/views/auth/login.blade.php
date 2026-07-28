<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SITech - Salus Institute of Technology</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative flex min-h-screen items-center justify-center overflow-hidden bg-green-950 px-4 py-6 sm:px-8">
    <div class="absolute inset-0 -z-20 bg-cover bg-center" style="background-image: url('{{ asset('images/campus-background.jpg') }}');"></div>
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-green-950/85 via-green-900/50 to-emerald-950/70"></div>
    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-green-950/80 via-transparent to-green-900/20"></div>

    <div class="grid w-full max-w-5xl overflow-hidden rounded-3xl bg-white shadow-2xl shadow-green-950/40 lg:grid-cols-[0.95fr_1.05fr]">
        <section class="relative flex min-h-[255px] flex-col items-center justify-center overflow-hidden bg-gradient-to-br from-[#14532d] via-[#1a5c1a] to-[#0f766e] px-8 py-10 text-center text-white sm:min-h-[300px] lg:min-h-[620px] lg:items-start lg:justify-between lg:px-12 lg:py-12 lg:text-left">
            <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-emerald-300/15 blur-2xl"></div>
            <div class="absolute -bottom-20 -left-16 h-56 w-56 rounded-full bg-amber-300/10 blur-2xl"></div>

            <div class="relative z-10 mt-0">
                <div class="mx-auto h-40 w-40 overflow-hidden rounded-full shadow-2xl shadow-green-950/30 sm:h-48 sm:w-48 lg:mx-0 lg:h-56 lg:w-56">
                    <img src="{{ asset('images/salus-logo.png') }}" alt="Salus Institute of Technology seal" class="h-full w-full rounded-full object-cover">
                </div>
                <h1 class="mt-6 text-2xl font-bold tracking-tight sm:text-3xl">Salus Institute of Technology</h1>
                <p class="mt-2 max-w-sm text-sm leading-6 text-green-100">One connected space for students, teachers, registrars, and school administrators.</p>
            </div>

            <p class="relative z-10 mt-8 hidden text-xs text-green-100/80 lg:block">AY 2025–2026 · SITech</p>
        </section>

        <section class="bg-white px-6 py-8 sm:px-12 sm:py-12 lg:px-14 lg:py-16">
            <div class="mx-auto max-w-md">
                <div class="mb-8">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-green-700">Welcome back</p>
                    <h2 class="mt-2 text-3xl font-bold tracking-tight text-green-950">Sign in to your portal</h2>
                    <p class="mt-2 text-sm text-gray-500">Use your school account to continue.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-green-900/70">Email address or username</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-green-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            </span>
                            <input id="email" type="text" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                class="w-full rounded-xl border-green-100 py-3 pl-11 pr-4 text-sm shadow-sm transition focus:border-green-600 focus:ring-2 focus:ring-green-500/20"
                                placeholder="you@salus.edu or student number">
                        </div>
                    </div>

                    <div>
                        <div class="mb-1.5 flex items-center justify-between gap-3">
                            <label for="password" class="block text-xs font-semibold uppercase tracking-wide text-green-900/70">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-green-700 hover:text-green-900">Forgot password?</a>
                            @endif
                        </div>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-green-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </span>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="w-full rounded-xl border-green-100 py-3 pl-11 pr-12 text-sm shadow-sm transition focus:border-green-600 focus:ring-2 focus:ring-green-500/20"
                                placeholder="Enter your password">
                            <button type="button" id="toggle-password" aria-label="Show password" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-gray-400 transition hover:text-green-700">
                                <svg id="eye-closed" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.06 12.35a1 1 0 0 1 0-.7C3.64 7.68 7.51 5 12 5c4.49 0 8.36 2.68 9.94 6.65a1 1 0 0 1 0 .7C20.36 16.32 16.49 19 12 19c-4.49 0-8.36-2.68-9.94-6.65Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                <svg id="eye-open" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3l18 18"></path><path d="M10.58 10.58a2 2 0 0 0 2.83 2.83"></path><path d="M9.88 4.24A10.94 10.94 0 0 1 12 4c4.49 0 8.36 2.68 9.94 6.65a1 1 0 0 1 0 .7 10.97 10.97 0 0 1-4.12 5.05"></path><path d="M6.61 6.61A10.98 10.98 0 0 0 2.06 11.65a1 1 0 0 0 0 .7C3.64 16.32 7.51 19 12 19c1.61 0 3.13-.35 4.5-.98"></path></svg>
                            </button>
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-green-700 focus:ring-green-600">
                        Remember me
                    </label>

                    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-[#1a5c1a] to-[#0f766e] py-3 text-sm font-bold text-white shadow-lg shadow-green-900/20 transition hover:-translate-y-0.5 hover:shadow-xl">Log in</button>
                </form>

                <p class="mt-8 border-t border-gray-100 pt-5 text-center text-xs text-gray-400">Secure SITech portal · AY 2025–2026</p>
            </div>
        </section>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('toggle-password');
        const eyeClosed = document.getElementById('eye-closed');
        const eyeOpen = document.getElementById('eye-open');

        togglePassword.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            eyeClosed.classList.toggle('hidden', !isPassword);
            eyeOpen.classList.toggle('hidden', isPassword);
            togglePassword.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
        });
    </script>
</body>
</html>
