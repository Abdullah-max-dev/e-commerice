<template>
  <UserMain>
     <div class="container-fluid py-4 dashboard-wrap">
      <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-4">
        <div>
          <p class="text-muted mb-1">Welcome back</p>
          <h2 class="fw-bold mb-1">Hi, {{ dashboard.user.name || 'User' }} 👋</h2>
          <span class="badge" :class="verificationBadgeClass">
            {{ verificationLabel }}
          </span>
        </div>
      </div>

      <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>
      <div v-if="successMessage" class="alert alert-success">{{ successMessage }}</div>

      <div class="row g-3 mb-4">
        <div v-for="item in statCards" :key="item.label" class="col-6 col-xl-3">
          <div class="card modern-card h-100">
            <div class="card-body">
              <small class="text-muted">{{ item.label }}</small>
              <h4 class="mt-2 mb-0 fw-bold">{{ item.value }}</h4>
            </div>
          </div>
        </div>
        </div>

        <div class="row g-3">
        <div class="col-12 col-xl-4">
          <div class="card modern-card h-100">
            <div class="card-body">
              <div class="d-flex align-items-center gap-3 mb-3">
                <img :src="dashboard.user.avatar_url" class="avatar" alt="avatar" />
                <div>
                  <h5 class="mb-1">{{ dashboard.user.name }}</h5>
                  <p class="text-muted mb-0">{{ dashboard.user.email }}</p>
                </div>
              </div>

              <label class="form-label fw-semibold">Billing Address</label>
              <textarea
                v-model="billingAddress"
                rows="4"
                class="form-control mb-3"
                placeholder="Add your billing address"
              ></textarea>
              <button class="btn btn-dark w-100" :disabled="savingAddress" @click="saveBillingAddress">
                {{ savingAddress ? 'Saving...' : 'Save Address' }}
              </button>
            </div>
          </div>
        </div>

      <div class="col-12 col-xl-8">
          <div class="card modern-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Recent Orders</h5>
                <small class="text-muted">Page {{ pagination.current_page }} of {{ pagination.last_page }}</small>
              </div>

              <div class="table-responsive">
                <table class="table align-middle">
                  <thead>
                    <tr>
                      <th>Order ID</th>
                      <th>Date</th>
                      <th>Total</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="order in orders" :key="order.id">
                      <td class="fw-semibold">{{ order.order_number }}</td>
                      <td>{{ order.date }}</td>
                      <td>${{ Number(order.total).toFixed(2) }}</td>
                      <td><span class="badge text-capitalize" :class="statusBadgeClass(order.status)">{{ order.status }}</span></td>
                    </tr>
                    <tr v-if="orders.length === 0">
                      <td colspan="4" class="text-center text-muted py-4">No orders found.</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div class="d-flex justify-content-end gap-2 mt-3">
                <button class="btn btn-outline-secondary btn-sm" :disabled="pagination.current_page <= 1 || loadingOrders" @click="fetchOrders(pagination.current_page - 1)">Prev</button>
                <button class="btn btn-outline-secondary btn-sm" :disabled="pagination.current_page >= pagination.last_page || loadingOrders" @click="fetchOrders(pagination.current_page + 1)">Next</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </UserMain>
</template>

<script>
import axios from 'axios'
import UserMain from './layouts/UserMain.vue'

export default {
  components: { UserMain },


 data() {
    return {
      dashboard: {
        user: {},
        stats: {},
      },
      orders: [],
      pagination: {
        current_page: 1,
        last_page: 1,
      },
      billingAddress: '',
      loadingOrders: false,
      savingAddress: false,
      errorMessage: '',
      successMessage: '',
    }
  },
  computed: {
    verificationLabel() {
      return this.dashboard.user.verification_status === 'verified' ? 'Verified Account' : 'Verification Pending'
    },
    verificationBadgeClass() {
      return this.dashboard.user.verification_status === 'verified' ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis'
    },
    statCards() {
      return [
        { label: 'Total Orders', value: this.dashboard.stats.total_orders ?? 0 },
        { label: 'Total Spent', value: `$${Number(this.dashboard.stats.total_spent || 0).toFixed(2)}` },
        { label: 'Account Verification', value: this.dashboard.stats.account_verified ? 'Verified' : 'Pending' },
        { label: 'Rewards Points', value: this.dashboard.stats.rewards_points ?? 0 },
      ]
    },
  },
  mounted() {
    this.fetchDashboard()
    this.fetchOrders(1)
  },
  methods: {
    authHeader() {
      return { Authorization: `Bearer ${localStorage.getItem('token')}` }
    },
    async fetchDashboard() {
      try {
        const { data } = await axios.get('/api/dashboard/summary', { headers: this.authHeader() })
        this.dashboard = data.data
        this.billingAddress = data.data.user.billing_address || ''
      } catch (error) {
        this.errorMessage = error.response?.data?.message || 'Unable to load dashboard summary.'
      }
    },
    async fetchOrders(page) {
      this.loadingOrders = true
      try {
        const { data } = await axios.get(`/api/dashboard/orders?page=${page}&per_page=6`, { headers: this.authHeader() })
        this.orders = data.data.data
        this.pagination.current_page = data.data.current_page
        this.pagination.last_page = data.data.last_page
      } catch (error) {
        this.errorMessage = error.response?.data?.message || 'Unable to load recent orders.'
      } finally {
        this.loadingOrders = false
      }
    },
    async saveBillingAddress() {
      this.errorMessage = ''
      this.successMessage = ''
      this.savingAddress = true
      try {
        const { data } = await axios.patch(
          '/api/dashboard/billing-address',
          { billing_address: this.billingAddress },
          { headers: this.authHeader() },
        )
        this.successMessage = data.message
      } catch (error) {
        this.errorMessage = error.response?.data?.message || 'Failed to update billing address.'
      } finally {
        this.savingAddress = false
      }
    },
    statusBadgeClass(status) {
      const map = {
        pending: 'bg-warning-subtle text-warning-emphasis',
        processing: 'bg-primary-subtle text-primary-emphasis',
        shipped: 'bg-info-subtle text-info-emphasis',
        delivered: 'bg-success-subtle text-success-emphasis',
        cancelled: 'bg-danger-subtle text-danger-emphasis',
      }
      return map[status] || 'bg-secondary-subtle text-secondary-emphasis'
    },
  },
}

</script>
