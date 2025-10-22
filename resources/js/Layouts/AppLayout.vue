<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Navigation -->
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <Link :href="route('home')" class="text-xl font-bold text-primary-600">
                                AI SaaS
                            </Link>
                        </div>

                        <!-- Navigation Links -->
                        <div v-if="$page.props.auth.user" class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                Dashboard
                            </NavLink>
                            <NavLink :href="route('copy-studio.index')" :active="route().current('copy-studio.*')">
                                Copy Studio
                            </NavLink>
                            <NavLink :href="route('image-studio.index')" :active="route().current('image-studio.*')">
                                Image Studio
                            </NavLink>
                            <NavLink :href="route('billing.index')" :active="route().current('billing.*')">
                                Billing
                            </NavLink>
                        </div>
                    </div>

                    <!-- Right Side -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <!-- Credits Display -->
                        <div v-if="credits && $page.props.auth.user" class="mr-4 text-sm">
                            <span class="text-gray-600 dark:text-gray-400">
                                Text: {{ credits.text.balance }} | Image: {{ credits.image.balance }}
                            </span>
                        </div>

                        <!-- Dark Mode Toggle -->
                        <button @click="toggleDarkMode" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 mr-2">
                            <Moon v-if="!isDark" class="w-5 h-5" />
                            <Sun v-else class="w-5 h-5" />
                        </button>

                        <!-- Auth Links (when not logged in) -->
                        <div v-if="!$page.props.auth.user" class="flex items-center space-x-4">
                            <Link :href="route('login')" class="text-sm text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">
                                Se connecter
                            </Link>
                            <Link :href="route('register')" class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md">
                                S'inscrire
                            </Link>
                        </div>

                        <!-- User Dropdown -->
                        <Dropdown v-if="$page.props.auth.user" align="right" width="48">
                            <template #trigger>
                                <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                    <div>{{ $page.props.auth.user.name }}</div>
                                    <ChevronDown class="ml-2 w-4 h-4" />
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Moon, Sun, ChevronDown } from 'lucide-vue-next';
import NavLink from '@/Components/NavLink.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const page = usePage();
const credits = computed(() => page.props.credits);

const isDark = ref(false);

onMounted(() => {
    isDark.value = document.documentElement.classList.contains('dark');
});

const toggleDarkMode = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};
</script>
