<template>
    <AppLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Welcome back, {{ $page.props.auth.user.name }}!</p>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <StatCard title="Total Jobs" :value="stats.total_jobs" icon="Zap" color="blue" />
                    <StatCard title="Completed" :value="stats.completed_jobs" icon="CheckCircle" color="green" />
                    <StatCard title="Text Jobs" :value="stats.text_jobs" icon="FileText" color="purple" />
                    <StatCard title="Image Jobs" :value="stats.image_jobs" icon="Image" color="pink" />
                </div>

                <!-- Credits Usage -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="card">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Text Credits</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Balance</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ usageStats.text.balance }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Used this month</span>
                                <span class="text-gray-900 dark:text-white">{{ usageStats.text.used }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Image Credits</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Balance</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ usageStats.image.balance }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Used this month</span>
                                <span class="text-gray-900 dark:text-white">{{ usageStats.image.used }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Jobs -->
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Recent Jobs</h3>
                    <div class="space-y-3">
                        <div
                            v-for="job in recentJobs"
                            :key="job.id"
                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                        >
                            <div class="flex items-center space-x-3">
                                <FileText v-if="job.type === 'text'" class="w-5 h-5 text-purple-500" />
                                <Image v-else class="w-5 h-5 text-pink-500" />
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ job.prompt.substring(0, 50) }}...
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ formatDate(job.created_at) }}
                                    </p>
                                </div>
                            </div>
                            <span
                                :class="[
                                    'px-2 py-1 text-xs rounded-full',
                                    job.status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                    job.status === 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
                                ]"
                            >
                                {{ job.status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import { FileText, Image } from 'lucide-vue-next';

defineProps({
    recentJobs: Array,
    stats: Object,
    usageStats: Object,
    chartData: Array,
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>
