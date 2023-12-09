<template>
    <router-view />
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from '@/stores/authStore';

onMounted(function () {
    const authStore = useAuthStore();
    authStore.autoLogoutWhenTokenExpires();
});
</script>