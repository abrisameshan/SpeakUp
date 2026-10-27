import { createRouter, createWebHistory } from 'vue-router'

import Welcome from '../components/Welcome.vue'
import Dashboard from '../components/Dashboard.vue'

const routes = [
    {
        path: '/',
        component: Welcome,
    },
    {
        path: '/dashboard',
        component: Dashboard,
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router