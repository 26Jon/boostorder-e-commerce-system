import { createApp } from 'vue'
import { createWebHistory } from 'vue-router'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap-icons/font/bootstrap-icons.css'
import 'bootstrap'

import Login from './components/Login.vue'
import Catalogue from './components/Catalogue.vue'
import Cart from './components/Cart.vue'
import Order from './components/Order.vue'
import { createRouter } from 'vue-router'

const routes = [
    {'path': '/login', 'component': Login},
    {'path': '/catalogue', 'component': Catalogue, meta: { requiresAuth: true }},
    {'path': '/cart', 'component': Cart, meta: { requiresAuth: true }},
    {'path': '/orders', 'component': Order, meta: { requiresAuth: true }},
    {'path': '/', redirect: 'catalogue'}
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to, from, next) => {
    const user = localStorage.getItem('user')

    if(to.meta.requiresAuth && !user) {
        next('/login')
    }

    else if (user && to.path === '/login') {
        next('/catalogue')
    }

    else {
        next();
    }
})

const app = createApp({
    template:
        `<router-view></router-view>`,        
})

app.use(router);

app.mount('#app');