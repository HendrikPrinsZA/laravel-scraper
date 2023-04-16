<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { useBlogPostStore } from '@/stores/blogPostStore.js';

const blogPostStore = useBlogPostStore();

const url = 'https://medium.com/@soulaimaneyh/php-clean-code-tricks-everyone-should-know-afd406bd00bc';
</script>

<template>
    <GuestLayout>
        <div v-if="blogPostStore.data">
            <h1 class="text-xl font-bold mb-6">Feedback</h1>
            <p>{{ blogPostStore.feedback }}</p>

            <h1 class="text-xl font-bold mb-6">Title suggestions</h1>
            <p>{{ blogPostStore.suggestions }}</p>

            <pre><code>{{ blogPostStore.data }}</code></pre>
        </div>
        <form v-else @submit.prevent="blogPostStore.submit(url)">
            <h1 class="text-2xl font-bold mb-6">
                Review Blog Post
            </h1>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="url">
                    URL
                </label>
                <input v-model="url"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="url" type="text" placeholder="Enter a URL" />
            </div>
            <div class="flex items-center justify-between">
                <button :disabled="blogPostStore.loading" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline disabled:opacity-25" type="submit">
                    Review
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
