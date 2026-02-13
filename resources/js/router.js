import { createRouter, createWebHistory } from 'vue-router'

import Home from './component/index/Index.vue'
import UserLogin from './component/index/auth/user/UserLogin.vue'
import UserRegister from './component/index/auth/user/UserRegister.vue'
import UserPannel from './component/user/UserPannel.vue'
import Cart from './component/index/Cart.vue'

import AdminPannel from './component/admin/AdminPannel.vue'
import VenderPannel from './component/vender/VenderPannel.vue'
import Store from './store';
import venderSignup from './component/index/auth/vender/VenderSignup.vue'
import AddProduct from './component/vender/AddProduct.vue'
import Category from './component/admin/Category.vue'
import ViewProduct from './component/vender/ViewProduct.vue'
import EditProduct from './component/vender/EditProduct.vue'
import ProductDiscount from './component/vender/ProductDiscount.vue'

const routes = [
    // login
  { path: '/', component: Home },

    {
        path: '/user-login', component: UserLogin,

    },
    //vender register
    {
        path: '/vender-register', component: venderSignup,

    },
    // cart
    {
        path: '/cart', component: Cart,

    },
    // user register
    {
        path: '/user-signup',
        component: UserRegister,

    },
    // user panel
    {
        path: '/user-panel', component: UserPannel,
       meta: { requiresAuth: true, role: 'user' }
    },
    // vender
       //vender panel
        {
            path: '/vender-panel', component: VenderPannel,
        meta: {requiresAuth: true, role: 'vender' }
        },
        // vender/ Add product
        {
            path: '/vender/add-product', component: AddProduct,
        meta: {requiresAuth: true, role: 'vender' }
        },
        // vender / view product
        {
            path: '/vender/view-product', component: ViewProduct,
            meta: {requiresAuth: true, role: 'vender' }
        },
        // vender / edit product
        {
            path: '/vender/products/:id/edit', component: EditProduct,
            meta: {requiresAuth: true, role: 'vender' }
        },
        // discount
        {
            path: '/vendor/products/:id/discount', component: ProductDiscount,
        meta: {requiresAuth: true, role: 'vender' }
        },
    //admin
        // admin pannel
        {
            path: '/admin-panel', component: AdminPannel,
        meta: { requiresAuth: true, role: 'admin' }
        },
        //category
        {
        path: '/admin/category', component: Category,
        meta: { requiresAuth: true, role: 'admin' }
        },
        // add bussiness type
        {
        path: '//admin/bussiness-type', component: AddBussiness,
        meta: { requiresAuth: true, role: 'admin' }
        },


]

const router =  createRouter({
  history: createWebHistory(),
  routes,
});



router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token')
    const role  = localStorage.getItem('role')

    // 🔐 auth check
    if (to.meta.requiresAuth && !token) {
        return next('/user-login')
    }

    // 🧑 role check
    if (to.meta.role && to.meta.role !== role) {
        return next('/user-login')
    }

    next()
})



export default router;
