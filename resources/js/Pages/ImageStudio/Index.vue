<template>
    <AppLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Image Studio</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Generate AI-powered images</p>
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
                                        rows="4"
                                        class="input"
                                        placeholder="Describe the image you want to generate..."
                                        required
                                    ></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="label">Size</label>
                                        <select v-model="form.options.size" class="input">
                                            <option value="1024x1024">1024x1024 (Square)</option>
                                            <option value="1024x1792">1024x1792 (Portrait)</option>
                                            <option value="1792x1024">1792x1024 (Landscape)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="label">Number of Images</label>
                                        <select v-model.number="form.options.n" class="input">
                                            <option :value="1">1</option>
                                            <option :value="2">2</option>
                                            <option :value="3">3</option>
                                            <option :value="4">4</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="label">Quality</label>
                                        <select v-model="form.options.quality" class="input">
                                            <option value="standard">Standard</option>
                                            <option value="hd">HD</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="label">Style</label>
                                        <select v-model="form.options.style" class="input">
                                            <option value="vivid">Vivid</option>
                                            <option value="natural">Natural</option>
                                        </select>
                                    </div>
                                </div>

                                <button
                                    type="submit"
                                    :disabled="loading"
                                    class="btn btn-primary w-full"
                                >
                                    <Loader2 v-if="loading" class="w-5 h-5 mr-2 animate-spin" />
                                    <ImageIcon v-else class="w-5 h-5 mr-2" />
                                    {{ loading ? 'Generating...' : 'Generate Images' }}
                                </button>
                            </form>

                            <!-- Results -->
                            <div v-if="images.length > 0" class="mt-6">
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Generated Images</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div
                                        v-for="(image, index) in images"
                                        :key="index"
                                        class="relative group"
                                    >
                                        <img
                                            :src="image.url"
                                            :alt="`Generated image ${index + 1}`"
                                            class="w-full h-auto rounded-lg"
                                        />
                                        <a
                                            :href="image.url"
                                            download
                                            class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition rounded-lg"
                                        >
                                            <Download class="w-8 h-8 text-white" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Sidebar -->
                    <div>
                        <div class="card">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Tips</h3>
                            <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                <li>• Be specific and descriptive</li>
                                <li>• Mention style, mood, and colors</li>
                                <li>• Include composition details</li>
                                <li>• Specify lighting conditions</li>
                            </ul>
                        </div>

                        <div class="card mt-6">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Credit Cost</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Standard</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">100 credits</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">HD</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">200 credits</span>
                                </div>
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
import { useToast } from 'vue-toastification';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ImageIcon, Loader2, Download } from 'lucide-vue-next';

const toast = useToast();
const loading = ref(false);
const images = ref([]);

const form = reactive({
    prompt: '',
    options: {
        size: '1024x1024',
        n: 1,
        quality: 'standard',
        style: 'vivid',
    },
});

const generate = async () => {
    loading.value = true;
    images.value = [];

    try {
        const response = await axios.post(route('image-studio.generate'), form);
        
        if (response.data.success) {
            const jobId = response.data.job.id;
            pollJobStatus(jobId);
        }
    } catch (error) {
        toast.error(error.response?.data?.message || 'Failed to generate images');
        loading.value = false;
    }
};

const pollJobStatus = async (jobId) => {
    const interval = setInterval(async () => {
        try {
            const response = await axios.get(route('image-studio.status', jobId));
            const job = response.data.job;

            if (job.status === 'completed') {
                clearInterval(interval);
                images.value = job.result;
                loading.value = false;
                toast.success('Images generated successfully!');
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
    }, 3000);
};
</script>
