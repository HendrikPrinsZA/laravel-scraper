import { api } from "./api";

export default {
    post(payload) {
        return api.post('/scrapers/blog', payload);
    }
};
