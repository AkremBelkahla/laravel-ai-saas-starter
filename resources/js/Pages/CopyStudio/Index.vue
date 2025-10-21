<template>
    <AppLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Copy Studio</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Generate AI-powered text content</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Generator Form -->
                    <div class="lg:col-span-2">
                        <div class="card">
                            <form @submit.prevent="generate">
                                <div class="mb-4">
                                    <label class="label">Prompt</label>
                                    <textarea
                                        v-model="form.prompt"
                                        rows="6"
                                        class="input"
                                        placeholder="Describe what you want to generate..."
                                        required
                                    ></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="label">Max Tokens</label>
                                        <input
                                            v-model.number="form.options.max_tokens"
                                            type="number"
                                            class="input"
                                            min="50"
                                            max="4000"
                                        />
                                    </div>
                                    <div>
                                        <label class="label">Temperature</label>
                                        <input
                                            v-model.number="form.options.temperature"
                                            type="number"
                                            step="0.1"
                                            class="input"
                                            min="0"
                                            max="2"
                                        />
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="label">System Message (Optional)</label>
                                    <input
                                        v-model="form.options.system"
                                        type="text"
                                        class="input"
                                        placeholder="You are a helpful assistant..."
                                    />
                                </div>

                                <button
                                    type="submit"
                                    :disabled="loading"
                                    class="btn btn-primary w-full"
                                >
                                    <Loader2 v-if="loading" class="w-5 h-5 mr-2 animate-spin" />
                                    <Sparkles v-else class="w-5 h-5 mr-2" />
                                    {{ loading ? 'Generating...' : 'Generate Text' }}
                                </button>
                            </form>

                            <!-- Result -->
                            <div v-if="result" class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Result</h3>
                                    <button @click="copyResult" class="text-primary-600 hover:text-primary-700">
                                        <Copy class="w-5 h-5" />
                                    </button>
                                </div>
                                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ result }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Templates Sidebar -->
                    <div>
                        <div class="card">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Templates</h3>
                            <div class="space-y-2">
                                <button
                                    v-for="template in templates"
                                    :key="template.id"
                                    @click="useTemplate(template)"
                                    class="w-full text-left p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                >
                                    <p class="font-medium text-gray-900 dark:text-white">{{ template.name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ template.description }}</p>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Sparkles, Loader2, Copy } from 'lucide-vue-next';

const props = defineProps({
    templates: Array,
});

const toast = useToast();
const loading = ref(false);
const result = ref(null);

const form = reactive({
    prompt: '',
    options: {
        max_tokens: 2000,
        temperature: 0.7,
        system: '',
    },
});

const generate = async () => {
    loading.value = true;
    result.value = null;

    try {
        const response = await axios.post(route('copy-studio.generate'), form);
        
        if (response.data.success) {
            const jobId = response.data.job.id;
            pollJobStatus(jobId);
        }
    } catch (error) {
        toast.error(error.response?.data?.message || 'Failed to generate text');
        loading.value = false;
    }
};

const pollJobStatus = async (jobId) => {
    const interval = setInterval(async () => {
        try {
            const response = await axios.get(route('copy-studio.status', jobId));
            const job = response.data.job;

            if (job.status === 'completed') {
                clearInterval(interval);
                result.value = job.result;
                loading.value = false;
                toast.success('Text generated successfully!');
            } else if (job.status === 'failed') {
                clearInterval(interval);
                loading.value = false;
                toast.error(job.error || 'Generation failed');
            }
        } catch (error) {
            clearInterval(interval);
            loading.value = false;
            toast.error('Failed to check job status');
        }
    }, 2000);
};

const useTemplate = (template) => {
    form.prompt = template.prompt_template;
    toast.info(`Template "${template.name}" loaded`);
};

const copyResult = () => {
    navigator.clipboard.writeText(result.value);
    toast.success('Copied to clipboard!');
};
</script>
