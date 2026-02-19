<template>
  <VenderMain>
    <div class="container-fluid px-4 py-4">
      <h2 class="mb-4">Order Management</h2>

      <div v-if="loading" class="text-center py-5">Loading vendor orders...</div>
      <div v-else-if="error" class="alert alert-danger">{{ error }}</div>
      <div v-else-if="!orders.length" class="alert alert-info">No orders found for your products.</div>

      <div v-else class="row g-3">
        <div class="col-12" v-for="order in orders" :key="order.id">
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                <div>
                  <h5 class="mb-1">Order #{{ order.order_number }}</h5>
                  <small class="text-muted">Placed: {{ formatDate(order.placed_at) }}</small>
                </div>
                <span class="badge text-capitalize" :class="statusBadge(order.status)">{{ order.status }}</span>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-7">
                  <h6>Customer Info</h6>
                  <p class="mb-1"><strong>Name:</strong> {{ order.customer.name }}</p>
                  <p class="mb-1"><strong>Email:</strong> {{ order.customer.email }}</p>
                  <p class="mb-1"><strong>Phone:</strong> {{ order.customer.phone }}</p>
                  <p class="mb-0"><strong>Address:</strong> {{ order.customer.address }}</p>
                </div>
                <div class="col-md-5">
                  <h6>Amount</h6>
                  <p class="mb-2"><strong>Total:</strong> Rs {{ order.total_amount }}</p>
                  <div class="d-flex gap-2 flex-wrap">
                    <select class="form-select" v-model="order.statusDraft">
                      <option v-for="s in allowedStatuses" :key="s" :value="s">{{ s }}</option>
                    </select>
                    <button class="btn btn-primary" @click="updateStatus(order, order.statusDraft)">Update</button>
                    <button class="btn btn-outline-danger" @click="updateStatus(order, 'cancelled')">Cancel Order</button>
                  </div>
                </div>
              </div>

              <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                  <thead>
                    <tr>
                      <th>Product</th>
                      <th>Quantity</th>
                      <th>Unit Price</th>
                      <th>Line Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="product in order.products" :key="product.id">
                      <td>{{ product.name }}</td>
                      <td>{{ product.quantity }}</td>
                      <td>Rs {{ product.unit_price }}</td>
                      <td>Rs {{ product.line_total }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </VenderMain>
</template>

<script>
import axios from 'axios'
import { onMounted, ref } from 'vue'
import VenderMain from './layouts/VenderMain.vue'

export default {
  components: { VenderMain },
  setup() {
    const loading = ref(true)
    const error = ref('')
    const orders = ref([])
    const allowedStatuses = ['pending', 'processing', 'shipped', 'delivered']

    const authHeaders = () => ({
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
    })

    const fetchOrders = async () => {
      loading.value = true
      error.value = ''
      try {
        const { data } = await axios.get('/api/vender/orders', authHeaders())
        orders.value = (data.orders || []).map((order) => ({ ...order, statusDraft: order.status }))
      } catch (err) {
        error.value = err.response?.data?.message || 'Unable to load vendor orders.'
      } finally {
        loading.value = false
      }
    }

    const updateStatus = async (order, status) => {
      try {
        await axios.patch(`/api/vender/orders/${order.id}/status`, { status }, authHeaders())
        order.status = status
        order.statusDraft = status === 'cancelled' ? 'pending' : status
      } catch (err) {
        alert(err.response?.data?.message || 'Failed to update order status.')
      }
    }

    const statusBadge = (status) => ({
      'bg-secondary': status === 'pending',
      'bg-info': status === 'processing',
      'bg-primary': status === 'shipped',
      'bg-success': status === 'delivered',
      'bg-danger': status === 'cancelled',
    })

    const formatDate = (value) => {
      if (!value) return '-'
      return new Date(value).toLocaleString()
    }

    onMounted(fetchOrders)

    return { loading, error, orders, allowedStatuses, updateStatus, statusBadge, formatDate }
  },
}
</script>
