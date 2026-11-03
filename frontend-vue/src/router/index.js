import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import AdminDashboard from '../views/AdminDashboard.vue'
import MyAspirations from '../views/MyAspirations.vue'
import about from '../views/about.vue'
import Explore from '../views/explore.vue'

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
    meta: { hideNavbar: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { hideNavbar: true }
  },
  {
    path: '/admin',
    name: 'AdminDashboard',
    component: AdminDashboard,
    meta: { requiresAdmin: true }
  },
  {
  path: '/riwayat',
  name: 'MyAspirations',
  component: MyAspirations
},
{
  path: '/about',
  name: 'About',
  component: about
},
{
  path: '/explore',
  name: 'Explore',
  component: Explore
}
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation Guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  const role = localStorage.getItem('role')

  if (to.meta.requiresAdmin) {
    // 1. Cek apakah pengguna sudah login
    if (!token) {
      alert('Silakan login terlebih dahulu.')
      return next('/login')
    }

    // 2. Cek apakah rolenya adalah admin atau guru
    if (role !== 'admin' && role !== 'guru') {
      alert('Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.')
      return next('/') // Alihkan kembali ke beranda
    }
  }

  next()
})

export default router