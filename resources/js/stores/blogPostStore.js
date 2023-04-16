import blogPostService from '@/services/blogPostService';
import { getError } from '@/utils/responseHelpers';
import { defineStore } from 'pinia'

export const useBlogPostStore = defineStore('blog-post', {
    state: () => ({
        loading: false,
        data: null
    }),

    getters: {
        doubleCount: (state) => state.count * 2,
    },

    actions: {
        async submit(url) {
            this.loading = true;

            const payload = {
                url: url
            };

            try {
                const response = await blogPostService.post(payload);

                // To-do: Change to queue handling
                // if (response.data.queueId) {
                //     router.push({ name: 'review', params: { id: response.data.queueId } });
                // }

                this.data = response.data.data;
            } catch (error) {
                this.error = getError(error);
            }

            this.loading = false;
        },

        increment() {
            this.count++
        },
    },

    getters: {
        feedback: (state) => state.data?.feedback,
        suggestions: (state) => state.data?.suggestions,
    },
})
