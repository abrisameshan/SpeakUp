import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/home.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue' // 1. Import komponen Register

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: {hideNavbar: true}
  },
  {
    path: '/register', // 2. Tambahkan rute register
    name: 'Register',
    component: Register,
    meta: {hideNavbar: true}
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router