<template>
  <MainLayout />

  <div class="container-fluid bg-light p-5">
    <h1 class="text-center text-secondary"><i class="fa-solid fa-credit-card"></i> Paycheck</h1>
  </div>

  <section class="py-4">
    <div class="container">
      <div v-if="loading" class="text-center py-5">Loading checkout details...</div>
      <div v-else-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>

      <div v-else-if="!cartItems.length" class="text-center py-5">
        <h4 class="text-muted mb-3">Your cart is empty.</h4>
        <button class="btn btn-outline-secondary" @click="goToCart">Go to Cart</button>
      </div>

      <form v-else class="row g-4" @submit.prevent="placeOrder">
        <div class="col-lg-7">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h4 class="mb-3">Customer Information</h4>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Full Name</label>
                  <input v-model.trim="form.name" type="text" class="form-control" placeholder="Enter your full name" />
                  <small class="text-danger" v-if="validationErrors.name">{{ validationErrors.name }}</small>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Email</label>
                  <input v-model.trim="form.email" type="email" class="form-control" placeholder="Enter your email" />
                  <small class="text-danger" v-if="validationErrors.email">{{ validationErrors.email }}</small>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Phone</label>
                  <input v-model.trim="form.phone" type="text" class="form-control" placeholder="03XXXXXXXXX" />
                  <small class="text-danger" v-if="validationErrors.phone">{{ validationErrors.phone }}</small>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Payment Method</label>
                  <select v-model="form.payment_method" class="form-select">
                    <option disabled value="">Choose payment method</option>
                    <option value="cash_on_delivery">Cash on Delivery</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="card">Credit / Debit Card</option>
                  </select>
                  <small class="text-danger" v-if="validationErrors.payment_method">{{ validationErrors.payment_method }}</small>
                </div>

                <div class="col-12">
                  <label class="form-label">Address</label>
                  <textarea
                    v-model.trim="form.address"
                    rows="3"
                    class="form-control"
                    placeholder="House no, street, city"
                  ></textarea>
                  <small class="text-danger" v-if="validationErrors.address">{{ validationErrors.address }}</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h4 class="mb-3">Order Summary</h4>

              <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item px-0" v-for="item in cartItems" :key="item.id">
                  <div class="d-flex justify-content-between">
                    <div>
                      <h6 class="mb-0">{{ item.product.p_name }}</h6>
                      <small class="text-muted">Qty: {{ item.quantity }} x Rs {{ item.product.final_price }}</small>
                    </div>
                    <span class="fw-semibold">Rs {{ item.line_total }}</span>
                  </div>
                </li>
              </ul>

              <div class="d-flex justify-content-between"><span>Subtotal</span><strong>Rs {{ summary.subtotal }}</strong></div>
              <div class="d-flex justify-content-between"><span>Discount</span><strong>Rs {{ summary.discount }}</strong></div>
              <div class="d-flex justify-content-between mb-2"><span>Delivery</span><strong>Free</strong></div>
              <hr />
              <div class="d-flex justify-content-between mb-3"><h5>Total</h5><h5>Rs {{ summary.total }}</h5></div>

              <div v-if="submitError" class="alert alert-danger py-2">{{ submitError }}</div>
              <div v-if="successMessage" class="alert alert-success py-2">{{ successMessage }}</div>

              <button type="submit" class="btn theme-login text-light w-100 rounded-pill" :disabled="processing">
                {{ processing ? 'Placing order...' : 'Place Order' }}
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </section>
</template>

<script>
import axios from 'axios'
import { onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import MainLayout from './layouts/MainLayout.vue'

export default {
  components: { MainLayout },
  setup() {
    const router = useRouter()
    const loading = ref(true)
    const processing = ref(false)
    const errorMessage = ref('')
    const submitError = ref('')
    const successMessage = ref('')
    const cartItems = ref([])
    const summary = ref({ subtotal: 0, discount: 0, total: 0, items_count: 0 })

    const form = reactive({
      name: '',
      email: '',
      phone: '',
      address: '',
      payment_method: '',
    })

    const validationErrors = reactive({
      name: '',
      email: '',
      phone: '',
      address: '',
      payment_method: '',
    })

    const authHeader = () => {
      const token = localStorage.getItem('token')
      return token ? { headers: { Authorization: `Bearer ${token}` } } : {}
    }

    const resetValidation = () => {
      Object.keys(validationErrors).forEach((key) => {
        validationErrors[key] = ''
      })
    }

    const applyCartResponse = (data) => {
      cartItems.value = data.cart_items || []
      summary.value = data.summary || { subtotal: 0, discount: 0, total: 0, items_count: 0 }
    }

    const fetchCart = async () => {
      loading.value = true
      errorMessage.value = ''

      try {
        const { data } = await axios.get('/api/cart', authHeader())
        applyCartResponse(data)
      } catch (error) {
        errorMessage.value = error.response?.data?.message || 'Unable to load paycheck details.'
      } finally {
        loading.value = false
      }
    }

    const validateClientSide = () => {
      resetValidation()
      let valid = true

      if (!form.name) {
        validationErrors.name = 'Name is required.'
        valid = false
      }

      if (!form.email) {
        validationErrors.email = 'Email is required.'
        valid = false
      }

      if (!form.phone) {
        validationErrors.phone = 'Phone is required.'
        valid = false
      }

      if (!form.address) {
        validationErrors.address = 'Address is required.'
        valid = false
      }

      if (!form.payment_method) {
        validationErrors.payment_method = 'Payment method is required.'
        valid = false
      }

      return valid
    }

    const placeOrder = async () => {
      submitError.value = ''
      successMessage.value = ''

      if (!validateClientSide()) return

      processing.value = true
      try {
        const { data } = await axios.post('/api/orders', form, authHeader())
        successMessage.value = data.message || 'Order placed successfully.'
        applyCartResponse(data)
        setTimeout(() => router.push('/'), 1200)
      } catch (error) {
        if (error.response?.status === 422 && error.response?.data?.errors) {
          resetValidation()
          const errors = error.response.data.errors
          Object.keys(errors).forEach((field) => {
            validationErrors[field] = errors[field][0]
          })
          submitError.value = error.response.data.message || 'Please fix the highlighted fields.'
        } else {
          submitError.value = error.response?.data?.message || 'Unable to place order.'
        }
      } finally {
        processing.value = false
      }
    }

    const goToCart = () => router.push('/cart')

    onMounted(fetchCart)

    return {
      loading,
      processing,
      errorMessage,
      submitError,
      successMessage,
      cartItems,
      summary,
      form,
      validationErrors,
      placeOrder,
      goToCart,
    }
  },
}
</script>
