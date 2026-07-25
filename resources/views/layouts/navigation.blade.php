<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('daily-menus.index')" :active="request()->routeIs('daily-menus.*')">
                        {{ __('Daily Menu') }}
                    </x-nav-link>
                    <x-nav-link :href="route('dish-categories.index')" :active="request()->routeIs('dish-categories.*')">
                        {{ __('Categories') }}
                    </x-nav-link>
                    <x-nav-link :href="route('dishes.index')" :active="request()->routeIs('dishes.*')">
                        {{ __('Dishes') }}
                    </x-nav-link>
                    <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*') && !request()->routeIs('orders.kanban')">
                        {{ __('Orders') }}
                    </x-nav-link>
                    <x-nav-link :href="route('orders.kanban')" :active="request()->routeIs('orders.kanban')">
                        {{ __('Kanban') }}
                    </x-nav-link>
                    <x-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">
                        {{ __('Customers') }}
                    </x-nav-link>
                    <x-nav-link :href="route('deliveries.index')" :active="request()->routeIs('deliveries.*')">
                        {{ __('Deliveries') }}
                    </x-nav-link>
                    <x-nav-link :href="route('payments.index')" :active="request()->routeIs('payments.*')">
                        {{ __('Payments') }}
                    </x-nav-link>
                    <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                        {{ __('Reports') }}
                    </x-nav-link>
                    <x-nav-link :href="route('settings.index')" :active="request()->routeIs('settings.*')">
                        {{ __('Settings') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('restaurant.edit')">
                            {{ __('Restaurant') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <i class="fa-solid fa-bars text-lg" :class="{'hidden': open, 'inline-flex': ! open }"></i>
                    <i class="fa-solid fa-xmark text-lg" :class="{'hidden': ! open, 'inline-flex': open }"></i>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('daily-menus.index')" :active="request()->routeIs('daily-menus.*')">
                {{ __('Daily Menu') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dish-categories.index')" :active="request()->routeIs('dish-categories.*')">
                {{ __('Categories') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dishes.index')" :active="request()->routeIs('dishes.*')">
                {{ __('Dishes') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*') && !request()->routeIs('orders.kanban')">
                {{ __('Orders') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('orders.kanban')" :active="request()->routeIs('orders.kanban')">
                {{ __('Kanban') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">
                {{ __('Customers') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('deliveries.index')" :active="request()->routeIs('deliveries.*')">
                {{ __('Deliveries') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('payments.index')" :active="request()->routeIs('payments.*')">
                {{ __('Payments') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                {{ __('Reports') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('settings.index')" :active="request()->routeIs('settings.*')">
                {{ __('Settings') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('restaurant.edit')">
                    {{ __('Restaurant') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
