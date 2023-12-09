import axios from 'axios';
import { useAuthStore } from '@/stores/authStore';

axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': window.csrf_token
};
axios.defaults.withCredentials = true;

export const client = axios.create({
    baseURL: '/api',
});

client.interceptors.request.use((config) => {
    const authStore = useAuthStore();

    if (authStore.loggedIn) {
        config.headers.Authorization = `${authStore.token.type} ${authStore.token.value}`;
    }

    return config;
});

export default {
    auth: {
        register: (form) => {
            return client.post("/register", form);
        },

        login: (form) => {
            return client.post("/login", form);
        },

        logout: () => {
            return client.post("/logout");
        },
    },
    task: {
        create: (task = null) => {
            return client.post('/task/create', task);
        },
        show: (id) => {
            return client.get(`/task/show/${id}`);
        },
        update: (task) => {
            return client.post('/task/update', task);
        },
        completed: (id, status) => {
            return client.post('/task/complete', { id: id, status: status });
        },
        archived: (id, status) => {
            return client.post('/task/archive', { id: id, status: status });
        },
        deleted: (id) => {
            return client.delete('/task/delete', { data: { id: id } });
        },
    },
    priority: {
        list: () => {
            return client.get('/priority/list');
        },
    },
    tag: {
        create: (name, taskId = null) => {
            return client.post('/tag/create', { name: name, task_id: taskId });
        },
        delete: (id) => {
            return client.delete('/tag/delete', { data: { id: id } });
        },
    },
    media: {
        upload: (file, taskId) => {
            var form = new FormData();
            form.append('file', file);
            form.append('task_id', taskId);

            return client.post('/media/upload', form, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        },
        delete: (id) => {
            return client.delete('/media/delete', { data: { id: id } });
        },
    },
}