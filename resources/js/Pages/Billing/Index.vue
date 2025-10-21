<template>
    <AppLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Billing & Plans</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Manage your subscription and credits</p>
                </div>

                <!-- Current Plan -->
                <div class="card mb-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Current Plan</h3>
                            <p class="text-2xl font-bold text-primary-600 mt-1">{{ currentPlan.label }}</p>
                        </div>
                        <button
                            v-if="subscription"
                            @click="manageBilling"
                            class="btn btn-secondary"
                        >
                            Manage Billing
                        </button>
                    </div>
                </div>

                <!-- Plans Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div
                        v-for="plan in plans"
                        :key="plan.name"
                        :class="[
                            'card relative',
                            currentPlan.value === plan.name ? 'ring-2 ring-primary-500' : ''
                        ]"
                    >
                        <div v-if="currentPlan.value === plan.name" class="absolute top-4 right-4">
                            <span class="px-2 py-1 text-xs font-semibold bg-primary-100 text-primary-800 rounded-full">
                                Current
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ plan.label }}</h3>
                        <div class="mb-4">
                            <span class="text-3xl font-bold text-gray-900 dark:text-white">
                                ${{ (plan.price / 100).toFixed(0) }}
                            </span>
                            <span class="text-gray-600 dark:text-gray-400">/month</span>
                        </div>

                        <ul class="space-y-2 mb-6">
                            <li class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <Check class="w-4 h-4 text-green-500 mr-2" />
                                {{ plan.features.text_credits.toLocaleString() }} text credits
                            </li>
                            <li class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <Check class="w-4 h-4 text-green-500 mr-2" />
                                {{ plan.features.image_credits }} image credits
                            </li>
                            <li v-if="plan.features.api_access" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <Check class="w-4 h-4 text-green-500 mr-2" />
                                API Access
                            </li>
                            <li v-if="plan.features.custom_templates" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <Check class="w-4 h-4 text-green-500 mr-2" />
                                Custom Templates
                            </li>
                        </ul>

                        <button
                            v-if="currentPlan.value !== plan.name && plan.name !== 'free'"
                            @click="checkout(plan.name)"
                            class="btn btn-primary w-full"
                        >
                            Upgrade to {{ plan.label }}
                        </button>
                    </div>
                </div>

                <!-- Usage Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="card">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Text Credits</h3>
                        <div class="space-y-3">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">Used</span>
                                    <span class="text-gray-900 dark:text-white">{{ usageStats.text.used }} / {{ usageStats.text.balance + usageStats.text.used }}</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div
                                        class="bg-primary-600 h-2 rounded-full"
                                        :style="{ width: `${(usageStats.text.used / (usageStats.text.balance + usageStats.text.used)) * 100}%` }"
                                    ></div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ usageStats.text.balance }} credits remaining
                            </p>
                        </div>
                    </div>

                    <div class="card">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Image Credits</h3>
                        <div class="space-y-3">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">Used</span>
                                    <span class="text-gray-900 dark:text-white">{{ usageStats.image.used }} / {{ usageStats.image.balance + usageStats.image.used }}</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div
                                        class="bg-pink-600 h-2 rounded-full"
                                        :style="{ width: `${(usageStats.image.used / (usageStats.image.balance + usageStats.image.used)) * 100}%` }"
                                    ></div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ usageStats.image.balance }} credits remaining
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Check } from 'lucide-vue-next';

defineProps({
    plans: Array,
    currentPlan: Object,
    subscription: Object,
    usageStats: Object,
});

const checkout = (plan) => {
    router.post(route('billing.checkout'), { plan });
};

const manageBilling = () => {
    window.location.href = route('billing.portal');
};
</script>
