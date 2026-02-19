<template>
    <VenderMain>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4 py-3 vendor-dashboard">
                    <div class="greeting-card d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                        <div>
                            <h2 class="mb-1">Welcome back, {{ profile.name || 'Vendor' }}</h2>
                            <p class="text-muted mb-0">Manage your business, orders, and inventory from one place.</p>
                        </div>
                        <span class="badge rounded-pill" :class="verificationBadgeClass">
                            {{ profile.verification_status || 'unverified' }}
                        </span>
                    </div>
                    <div v-if="showVerificationPrompt" :class="statusClass">{{ statusMessage }}</div>

                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-4 col-xl-2" v-for="card in statCards" :key="card.title">
                            <div class="stat-card">
                                <p class="stat-label mb-1">{{ card.title }}</p>
                                <h4 class="mb-0">{{ card.value }}</h4>
                            </div>
                        </div>
                    </div>

                        <div class="row g-3 mb-4">
                        <div class="col-lg-8">
                            <div class="panel-card h-100">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Sales Trend</h5>
                                </div>
                                <div class="chart-scroll">
                                    <div class="bar-chart">
                                        <div v-for="month in stats.monthly_revenue" :key="month.month" class="bar-col">
                                            <div class="bar" :style="{ height: barHeight(month.total) }"></div>
                                            <span>{{ month.month }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="panel-card h-100">
                                <h5 class="mb-3">Order Status</h5>
                                <div class="pie-legend" v-for="(item, index) in stats.order_status" :key="item.status">
                                    <span class="dot" :style="{ backgroundColor: pieColors[index % pieColors.length] }"></span>
                                    <span class="text-capitalize">{{ item.status }}</span>
                                    <strong class="ms-auto">{{ item.total }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-lg-8">
                            <div class="panel-card">
                                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Recent Orders</h5>
                                    <input v-model="ordersSearch" @input="fetchRecentOrders(1)" type="search" class="form-control order-search" placeholder="Search order/customer/status" />
                                </div>

                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Customer Name</th>
                                                <th>Date</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="order in recentOrders.data" :key="`${order.order_id}-${order.date}`">
                                                <td>{{ order.order_id }}</td>
                                                <td>{{ order.customer_name }}</td>
                                                <td>{{ order.date }}</td>
                                                <td>${{ Number(order.total || 0).toFixed(2) }}</td>
                                                <td><span class="status-pill text-capitalize">{{ order.status }}</span></td>
                                            </tr>
                                            <tr v-if="!recentOrders.data.length">
                                                <td colspan="5" class="text-center text-muted">No recent orders found.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-outline-secondary btn-sm" :disabled="recentOrders.current_page <= 1" @click="fetchRecentOrders(recentOrders.current_page - 1)">Prev</button>
                                    <button class="btn btn-outline-secondary btn-sm" :disabled="recentOrders.current_page >= recentOrders.last_page" @click="fetchRecentOrders(recentOrders.current_page + 1)">Next</button>
                                </div>


                            </div>
                        </div>

                        <<div class="col-lg-4">
                            <div class="panel-card mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Notifications</h5>
                                </div>
                                <div class="notification-item" v-for="alert in notifications" :key="alert.id">
                                    <small class="text-uppercase text-muted">{{ alert.type }}</small>
                                    <div class="fw-semibold">{{ alert.title }}</div>
                                    <small>{{ alert.message }}</small>
                                </div>
                                <p v-if="!notifications.length" class="text-muted mb-0">No notifications.</p>
                            </div>

                            <div class="panel-card">
                                <h5 class="mb-3">Recent Ratings</h5>
                                <div class="notification-item" v-for="review in stats.recent_reviews" :key="review.product_id">
                                    <div class="fw-semibold">{{ review.product_name }}</div>
                                    <small>⭐ {{ review.rating }} / 5</small>
                                </div>


                            </div>
                        </div>
                    </div>
                   <div class="panel-card">
                        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Product Management</h5>
                            <div class="d-flex gap-2">
                                <input v-model="productsSearch" @input="fetchProducts(1)" type="search" class="form-control order-search" placeholder="Search products" />
                                <router-link class="btn btn-primary" to="/vender/add-product">Add New Product</router-link>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Quick Edit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in products.data" :key="item.id">
                                        <td>{{ item.name }}</td>
                                        <td>${{ Number(item.price || 0).toFixed(2) }}</td>
                                        <td>{{ item.stock }}</td>
                                        <td><span class="status-pill text-capitalize">{{ item.stock_status.replace('_', ' ') }}</span></td>
                                        <td><router-link class="btn btn-sm btn-outline-primary" :to="item.quick_edit_url">Edit</router-link></td>
                                    </tr>
                                    <tr v-if="!products.data.length">
                                        <td colspan="5" class="text-center text-muted">No products found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </VenderMain>
</template>

<script>

import { computed, onMounted, reactive, ref } from 'vue'
import { useStore } from 'vuex'
import VenderMain from './layouts/VenderMain.vue'

export default {
    components: { VenderMain },
    setup() {
        const store = useStore()
        const token = localStorage.getItem('token')

        const profile = reactive({})
        const stats = reactive({
            total_orders: 0,
            total_sales: 0,
            pending_orders: 0,
            active_products: 0,
            monthly_revenue: [],
            order_status: [],
            recent_reviews: []
        })

        const recentOrders = reactive({ data: [], current_page: 1, last_page: 1 })
        const products = reactive({ data: [], current_page: 1, last_page: 1 })
        const notifications = ref([])
        const ordersSearch = ref('')
        const productsSearch = ref('')
        const pieColors = ['#4f46e5', '#22c55e', '#f97316', '#06b6d4', '#eab308']

        const verificationStatus = computed(() => store.state.verification_status || profile.verification_status)
        const statusMessage = computed(() => {
            const map = {
                unverified: 'Please complete your information to get verified.',
                pending: 'Your verification request is pending admin review.',
                verified: 'Your account is verified.',
                rejected: 'Verification was rejected. Please update settings and resubmit.'
            }
            return map[verificationStatus.value] || map.unverified
        })

        const statusClass = computed(() => {
            const map = { unverified: 'alert-warning', pending: 'alert-info', verified: 'alert-success', rejected: 'alert-danger' }
            return `alert ${map[verificationStatus.value] || 'alert-warning'}`
        })

        const showVerificationPrompt = computed(() => verificationStatus.value !== 'verified')

        const verificationBadgeClass = computed(() => {
            const map = {
                verified: 'text-bg-success',
                pending: 'text-bg-warning',
                rejected: 'text-bg-danger',
                unverified: 'text-bg-secondary'
             }
            return map[profile.verification_status] || 'text-bg-secondary'
        })

        const statCards = computed(() => [
            { title: 'Total Orders', value: stats.total_orders },
            { title: 'Total Sales', value: `$${Number(stats.total_sales || 0).toFixed(2)}` },
            { title: 'Pending Orders', value: stats.pending_orders },
            { title: 'Active Products', value: stats.active_products },
            { title: 'Monthly Revenue', value: `$${Number((stats.monthly_revenue.at(-1) || {}).total || 0).toFixed(2)}` }
        ])

        const config = () => ({ headers: { Authorization: `Bearer ${token}` } })

        const syncVerificationStatus = async () => {
            const res = await axios.get('/api/me', config())
            store.dispatch('setVerificationStatus', res.data.data.verification_status || 'unverified')
        }

        const fetchProfile = async () => {
            try {
                const res = await axios.get('/api/vender/dashboard/profile', config())
                Object.assign(profile, res.data.data || {})
            } catch (_) {}
        }

        const fetchStats = async () => {
            try {
                const res = await axios.get('/api/vender/dashboard/stats', config())
                Object.assign(stats, res.data.data || {})
            } catch (_) {}
        }

        const fetchRecentOrders = async (page = 1) => {
            try {
                const res = await axios.get('/api/vender/dashboard/recent-orders', {
                    ...config(),
                    params: { page, search: ordersSearch.value, per_page: 8 }
                })
                Object.assign(recentOrders, res.data.data || { data: [] })
            } catch (_) {}
        }

        const fetchProducts = async (page = 1) => {
            try {
                const res = await axios.get('/api/vender/dashboard/products', {
                    ...config(),
                    params: { page, search: productsSearch.value, per_page: 8 }
                })
                Object.assign(products, res.data.data || { data: [] })
            } catch (_) {}
        }
        const fetchNotifications = async () => {
            try {
                const res = await axios.get('/api/vender/dashboard/notifications', config())
                notifications.value = res.data.data || []
            } catch (_) {
                notifications.value = []
            }
        }
        const barHeight = (value) => {
            const totals = stats.monthly_revenue.map((m) => Number(m.total || 0))
            const max = Math.max(...totals, 1)
            return `${Math.max((Number(value || 0) / max) * 180, 8)}px`
        }
         onMounted(async () => {
            await syncVerificationStatus()
            await Promise.all([fetchProfile(), fetchStats(), fetchRecentOrders(), fetchProducts(), fetchNotifications()])
        })

        return {
            profile,
            stats,
            recentOrders,
            products,
            notifications,
            ordersSearch,
            productsSearch,
            pieColors,
            statusMessage,
            statusClass,
            showVerificationPrompt,
            verificationBadgeClass,
            statCards,
            fetchRecentOrders,
            fetchProducts,
            barHeight
        }

    }
}
</script>

<style scoped>
.vendor-dashboard .greeting-card,
.vendor-dashboard .stat-card,
.vendor-dashboard .panel-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(17, 24, 39, 0.06);
    padding: 16px;
    transition: transform .2s ease, box-shadow .2s ease;
}
.vendor-dashboard .greeting-card:hover,
.vendor-dashboard .stat-card:hover,
.vendor-dashboard .panel-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(17, 24, 39, 0.1);
}
.stat-label { color: #6b7280; font-size: .85rem; }
.order-search { max-width: 280px; }
.status-pill {
    background: #eef2ff;
    color: #3730a3;
    border-radius: 999px;
    font-size: 12px;
    padding: 5px 10px;
    display: inline-block;
}
.notification-item {
    border-bottom: 1px dashed #e5e7eb;
    padding: 8px 0;
}
.notification-item:last-child { border-bottom: 0; }
.bar-chart {
    min-height: 210px;
    display: flex;
    align-items: end;
    gap: 12px;
}
.bar-col {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    min-width: 42px;
}
.bar {
    width: 24px;
    border-radius: 8px 8px 0 0;
    background: linear-gradient(180deg, #60a5fa, #3b82f6);
}
.pie-legend {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
}
.dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}
.chart-scroll { overflow-x: auto; }
@media (max-width: 767px) {
    .order-search { max-width: 100%; }
}
</style>
