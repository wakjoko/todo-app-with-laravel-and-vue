import { createRouter, createWebHistory } from 'vue-router';
import { guestGuard, loggedInGuard } from '@/router/guards';

const router = createRouter({
    history: createWebHistory(),
    routes: [{
            path: '/',
            component: () =>
                import ('@/views/layouts/Guest.vue'),
            redirect: '/login',
            beforeEnter: [guestGuard],
            children: [{
                path: 'login',
                name: 'login',
                component: () =>
                    import ('@/views/pages/Login.vue'),
            }, {
                path: 'register',
                name: 'register',
                component: () =>
                    import ('@/views/pages/Register.vue'),
            }],
        },
        {
            path: '/tasks',
            component: () =>
                import ('@/views/layouts/LoggedIn.vue'),
            redirect: '/tasks/todo',
            beforeEnter: [loggedInGuard],
            children: [{
                    path: 'todo',
                    name: 'todo',
                    component: () =>
                        import ('@/views/pages/Todo.vue'),
                },
                {
                    path: 'completed',
                    name: 'completed',
                    component: () =>
                        import ('@/views/pages/Completed.vue'),
                },
                {
                    path: 'archived',
                    name: 'archived',
                    component: () =>
                        import ('@/views/pages/Archived.vue'),
                },
            ],
        },
        {
            path: '/:pathMatch(.*)*',
            name: 'notFound',
            component: () =>
                import ('@/views/pages/NotFound.vue'),
        },
    ],
});

export default router