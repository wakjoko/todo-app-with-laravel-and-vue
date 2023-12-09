import { defineStore } from "pinia";
import api from '@/api';
import router from '@/router';
import { DateTime } from 'luxon';

export const useAuthStore = defineStore("authStore", {
    persist: true,
    state: () => {
        return {
            user: {},
            token: {},
        }
    },
    getters: {
        loggedIn: (state) => Object.keys(state.token).length > 0,
    },
    actions: {
        async register(form) {
            try {
                this.update(
                    (await api.auth.register(form)).data
                );
            }
            catch (error) {
                return Promise.reject(error);
            }

            this.autoLogoutWhenTokenExpires();
            this.login();
        },

        async login(form = null) {
            if (!this.loggedIn) {
                try {
                    this.update(
                        (await api.auth.login(form)).data
                    );
                }
                catch (error) {
                    return Promise.reject(error);
                }
            }

            this.autoLogoutWhenTokenExpires();
            router.push('/tasks');
        },

        async logout(notifyBackend = false) {
            if (notifyBackend) {
                try {
                    await api.auth.logout();
                }
                catch (error) {
                    // return Promise.reject(error);
                }
            }

            this.$reset();
            router.push('/login');
        },

        update(data) {
            this.user = data.user;
            this.token = data.token;
        },

        autoLogoutWhenTokenExpires() {
            if (!this.loggedIn) {
                return;
            }

            const expiresAt = DateTime.fromISO(this.token.expires_at);
            const duration = expiresAt.diffNow();

            setTimeout(() => this.logout(), duration.milliseconds);
        },
    },
});
