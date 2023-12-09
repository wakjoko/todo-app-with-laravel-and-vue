<script setup>
import { computed } from 'vue';

const props = defineProps(['icon', 'color', 'text']);

const icon = computed(() => {
    return `bi bi-${props.icon}`;
})

const color = computed(() => {
    return props.color ? `text-${props.color}` : '';
})
</script>

<template>
    <div class="d-flex" v-if="props.icon">
        <i :class="[icon, color]"></i>
        <span class="ms-1" v-if="props.text">{{ props.text }}</span>
    </div>
</template>