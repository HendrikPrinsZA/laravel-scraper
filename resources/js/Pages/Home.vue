<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';

const url = 'https://medium.com/@soulaimaneyh/php-clean-code-tricks-everyone-should-know-afd406bd00bc';

const submitUrl = () => {
    console.log('Submit URL');
}

</script>

<template>
    <GuestLayout>
        <h1 class="text-2xl font-bold mb-6">Review Blog Post</h1>
        <form @submit.prevent="submitUrl()">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="url">
                    URL
                </label>
                <input v-model="url"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="url" type="text" placeholder="Enter a URL" />
            </div>
            <div class="flex items-center justify-between">
                <button
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="submit">
                    Review
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
