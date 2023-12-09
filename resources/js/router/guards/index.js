import { useAuthStore } from '@/stores/authStore';

export function guestGuard(to, from, next) {
	if (!useAuthStore().loggedIn) {
		return next();
	}
	else {
		return next('/tasks');
	}
}

export function loggedInGuard(to, from, next) {
	if (useAuthStore().loggedIn) {
		return next();
	}
	else {
		return next('/login');
	}
}