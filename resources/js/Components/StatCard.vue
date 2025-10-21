<template>
    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ title }}</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ value }}</p>
            </div>
            <div :class="[
                'p-3 rounded-full',
                color === 'blue' ? 'bg-blue-100 dark:bg-blue-900' :
                color === 'green' ? 'bg-green-100 dark:bg-green-900' :
                color === 'purple' ? 'bg-purple-100 dark:bg-purple-900' :
                'bg-pink-100 dark:bg-pink-900'
            ]">
                <component :is="iconComponent" :class="[
                    'w-6 h-6',
                    color === 'blue' ? 'text-blue-600 dark:text-blue-300' :
                    color === 'green' ? 'text-green-600 dark:text-green-300' :
                    color === 'purple' ? 'text-purple-600 dark:text-purple-300' :
                    'text-pink-600 dark:text-pink-300'
                ]" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Zap, CheckCircle, FileText, Image } from 'lucide-vue-next';

const props = defineProps({
    title: String,
    value: [String, Number],
    icon: String,
    color: String,
});

const iconComponent = computed(() => {
    const icons = { Zap, CheckCircle, FileText, Image };
    return icons[props.icon] || Zap;
});
</script>
