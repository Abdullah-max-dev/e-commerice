<template>
    <UserMain>
        <div class="container-fluid px-4 py-4">

            <h2 class="mb-4">Order History</h2>

            <div v-if="loading" class="text-center py-5">Loading your orders...</div>
            <div v-else-if="error" class="alert alert-danger">{{ error }}</div>
            <div v-else-if="!orders.length" class="alert alert-info">No orders found yet.</div>

            <div v-else class="row g-3 ">
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
                                <div class="col-md-6">
                                <h6>Customer Information</h6>
                                    <p class="mb-1"><strong>Name:</strong> {{ order.customer.name }}</p>
                                    <p class="mb-1"><strong>Email:</strong> {{ order.customer.email }}</p>
                                    <p class="mb-1"><strong>Phone:</strong> {{ order.customer.phone }}</p>
                                    <p class="mb-0"><strong>Address:</strong> {{ order.customer.address }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Payment & Totals</h6>
                                    <p class="mb-1"><strong>Method:</strong> {{ order.payment_method }}</p>
                                    <p class="mb-1"><strong>Subtotal:</strong> Rs {{ order.subtotal }}</p>
                                    <p class="mb-1"><strong>Discount:</strong> Rs {{ order.discount }}</p>
                                    <p class="mb-0"><strong>Total:</strong> Rs {{ order.total }}</p>
                                </div>
                            </div>

                            <div class="mt-3">
                              <button
                                class="btn btn-outline-danger btn-sm"
                                :disabled="order.already_reported || reportingOrderId === order.id"
                                @click="toggleReportForm(order.id)"
                              >
                                {{ order.already_reported ? 'Already Reported' : (reportingOrderId === order.id ? 'Close Report' : 'Report Order') }}
                              </button>
                            </div>

                            <div v-if="reportingOrderId === order.id && !order.already_reported" class="mt-3">
                              <textarea
                                v-model="reportMessage"
                                class="form-control mb-2"
                                rows="3"
                                placeholder="Describe your issue with this order"
                              />
                              <button class="btn btn-danger btn-sm" :disabled="submittingReport" @click="submitReport(order)">
                                {{ submittingReport ? 'Submitting...' : 'Submit Report' }}
                              </button>
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
  </UserMain>
</template>

<script>
import axios from 'axios'
import { onMounted, onUnmounted, ref } from 'vue'
import UserMain from './layouts/UserMain.vue'

export default {
  components: { UserMain },
  setup() {
    const loading = ref(true)
    const error = ref('')
    const orders = ref([])
    const reportingOrderId = ref(null)
    const reportMessage = ref('')
    const submittingReport = ref(false)

    const fetchOrders = async ({ silent = false } = {}) => {
      if (!silent) {
        loading.value = true
      }

      if (!silent || !orders.value.length) {
        error.value = ''
      }
      try {
        const token = localStorage.getItem('token')
        const { data } = await axios.get('/api/orders', {
          headers: { Authorization: `Bearer ${token}` },
        })
        orders.value = data.orders || []
      } catch (err) {
        error.value = err.response?.data?.message || 'Unable to load orders.'
      } finally {
        if (!silent) {
          loading.value = false
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

     const toggleReportForm = (orderId) => {
      if (reportingOrderId.value === orderId) {
        reportingOrderId.value = null
        reportMessage.value = ''
        return
      }

      reportingOrderId.value = orderId
      reportMessage.value = ''
    }

    const submitReport = async (order) => {
      if (!reportMessage.value.trim()) {
        alert('Please enter a report message.')
        return
      }

      submittingReport.value = true

      try {
        const token = localStorage.getItem('token')
        await axios.post(`/api/orders/${order.id}/report`, { message: reportMessage.value.trim() }, {
          headers: { Authorization: `Bearer ${token}` },
        })
        order.already_reported = true
        reportingOrderId.value = null
        reportMessage.value = ''
      } catch (err) {
        alert(err.response?.data?.message || 'Unable to submit order report.')
      } finally {
        submittingReport.value = false
      }
    }

    let intervalId = null

    onMounted(() => {
      fetchOrders()
       intervalId = setInterval(() => {
        fetchOrders({ silent: true })
      }, REFRESH_INTERVAL_MS)
    })

    onUnmounted(() => {
      if (intervalId) clearInterval(intervalId)
    })

    return { loading, error, orders, statusBadge, formatDate, reportingOrderId, reportMessage, submittingReport, toggleReportForm, submitReport }  },
}
</script>



