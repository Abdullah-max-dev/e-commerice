import { createRouter, createWebHistory } from 'vue-router'

import Home from './component/index/Index.vue'
import UserLogin from './component/index/auth/user/UserLogin.vue'
import UserRegister from './component/index/auth/user/UserRegister.vue'
import UserPannel from './component/user/UserPannel.vue'
import Cart from './component/index/Cart.vue'

import AdminPannel from './component/admin/AdminPannel.vue'
import VenderPannel from './component/vender/VenderPannel.vue'
import venderSignup from './component/index/auth/vender/VenderSignup.vue'
import AddProduct from './component/vender/AddProduct.vue'
import Category from './component/admin/Category.vue'
import ViewProduct from './component/vender/ViewProduct.vue'
import EditProduct from './component/vender/EditProduct.vue'
import ProductDiscount from './component/vender/ProductDiscount.vue'
import VenderSettings from './component/vender/Settings.vue'
import UserSettings from './component/user/Settings.vue'
import Verification from './component/admin/Verification.vue'
import ProductDetail from './component/index/ProductDetail.vue'

const routes = [
    { path: '/', component: Home },
    { path: '/product/:id', component: ProductDetail },
   { path: '/user-login', component: UserLogin },
  { path: '/vender-register', component: venderSignup },
  { path: '/cart', component: Cart, meta: { requiresAuth: true, role: 'user' } },
  { path: '/user-signup', component: UserRegister },

  { path: '/user-panel', component: UserPannel, meta: { requiresAuth: true, role: 'user' } },
  { path: '/user/settings', component: UserSettings, meta: { requiresAuth: true, role: 'user' } },

  { path: '/vender-panel', component: VenderPannel, meta: { requiresAuth: true, role: 'vender' } },
  { path: '/vender/settings', component: VenderSettings, meta: { requiresAuth: true, role: 'vender' } },
  { path: '/vender/add-product', component: AddProduct, meta: { requiresAuth: true, role: 'vender', requiresVerified: true } },
  { path: '/vender/view-product', component: ViewProduct, meta: { requiresAuth: true, role: 'vender' } },
  { path: '/vender/products/:id/edit', component: EditProduct, meta: { requiresAuth: true, role: 'vender' } },
  { path: '/vender/products/:id/discount', component: ProductDiscount, meta: { requiresAuth: true, role: 'vender', requiresVerified: true } },

  { path: '/admin-panel', component: AdminPannel, meta: { requiresAuth: true, role: 'admin' } },
  { path: '/admin/category', component: Category, meta: { requiresAuth: true, role: 'admin' } },
   { path: '/admin/verification', component: Verification, meta: { requiresAuth: true, role: 'admin' } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token')
    const role = localStorage.getItem('role')
    const verificationStatus = localStorage.getItem('verification_status') || 'unverified'
    if (to.meta.requiresAuth && !token) {
        return next('/user-login')
  }
    if (to.meta.role && to.meta.role !== role) {
    return next('/user-login')
  }

  if (to.meta.requiresVerified && verificationStatus !== 'verified') {
    alert('Your account must be verified before using this feature. Please submit verification from settings.')
    return next(role === 'vender' ? '/vender/settings' : '/user/settings')
  }

  next()
})



export default router
