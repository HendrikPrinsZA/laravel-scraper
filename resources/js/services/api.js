import { getResponse } from '@/utils/responseHelpers';
import axios from 'axios';

const apiUrl = import.meta.env.VITE_APP_API_URL;

export const api = axios.create({
    baseURL: `${apiUrl}/api`,
});

/*
 * Add a response interceptor
 */
api.interceptors.response.use(
    (response) => {
        return getResponse(response);
    },
    (error) => {
        if (error.response && [401, 419].includes(error.response.status)) {
            console.error('User not authorized, login failed with API');
        }
        return Promise.reject(error);
    }
);
