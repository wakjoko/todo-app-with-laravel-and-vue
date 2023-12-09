import { createApp } from 'vue';
import App from './App.vue';
import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';
import router from './router';

const store = createPinia().use(piniaPluginPersistedstate);

createApp(App)
    .use(store)
    .use(router)
    .mount('#app');