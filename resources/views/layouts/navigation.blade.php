<nav x-data="{ open: false }" class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between py-3">

            <div class="flex items-center gap-8">
                <!-- Brand -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 shrink-0">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75" />
                    </svg>
                    <span class="font-semibold text-slate-800 dark:text-slate-100 tracking-tight">Caja Chica</span>
                </a>

                <!-- Desktop links -->
                <div class="hidden sm:flex items-center gap-1">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-nav-link>
                    <x-nav-link :href="route('ingresos')" :active="request()->routeIs('ingresos')">Ingresos</x-nav-link>
                    <x-nav-link :href="route('gastos')" :active="request()->routeIs('gastos')">Gastos</x-nav-link>
                    <x-nav-link :href="route('categorias')" :active="request()->routeIs('categorias')">Categorías</x-nav-link>
                    <x-nav-link :href="route('aportantes')" :active="request()->routeIs('aportantes')">Aportantes</x-nav-link>
                    <x-nav-link :href="route('reportes')" :active="request()->routeIs('reportes')">Reportes</x-nav-link>
                    @if(Auth::user()->isAdmin())
                        <x-nav-link :href="route('auditoria')" :active="request()->routeIs('auditoria')">Auditoría</x-nav-link>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2">
                <!-- Botón modo oscuro -->
                <button
                    x-data
                    @click="$store.theme.toggle()"
                    :title="$store.theme.dark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
                    class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition"
                >
                    <!-- Luna (visible en modo claro) -->
                    <svg x-show="!$store.theme.dark" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                    <!-- Sol (visible en modo oscuro) -->
                    <svg x-show="$store.theme.dark" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                </button>

                <!-- User menu (desktop) -->
                <div class="hidden sm:flex items-center">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-slate-100 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">Perfil</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    Cerrar sesión
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Hamburger (mobile) -->
                <div class="flex items-center sm:hidden">
                    <button @click="open = !open" class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        <svg class="w-5 h-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden border-t border-slate-100 dark:border-slate-700">
        <div class="py-2 space-y-0.5 px-3">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('ingresos')" :active="request()->routeIs('ingresos')">Ingresos</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('gastos')" :active="request()->routeIs('gastos')">Gastos</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('categorias')" :active="request()->routeIs('categorias')">Categorías</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('aportantes')" :active="request()->routeIs('aportantes')">Aportantes</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('reportes')" :active="request()->routeIs('reportes')">Reportes</x-responsive-nav-link>
            @if(Auth::user()->isAdmin())
                <x-responsive-nav-link :href="route('auditoria')" :active="request()->routeIs('auditoria')">Auditoría</x-responsive-nav-link>
            @endif
        </div>

        <div class="border-t border-slate-100 dark:border-slate-700 px-3 py-3">
            <div class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ Auth::user()->name }}</div>
            <div class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ Auth::user()->email }}</div>
            <div class="mt-2 space-y-0.5">
                <x-responsive-nav-link :href="route('profile.edit')">Perfil</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Cerrar sesión
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
