<template>
    <MainLayout />
        <div class="container-fluid bg-light p-5">
            <h1 class="text-center text-secondary"><i class="fa-solid fa-cart-arrow-down"></i> Cart List</h1>
        </div>

        <section class="py-4">
            <div class="container">
            <div v-if="loading" class="text-center py-5">Loading cart...</div>

            <div v-else-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>

            <div v-else-if="!cartItems.length" class="text-center py-5">
                <h4 class="text-muted">Your cart is empty.</h4>
            </div>

            <div v-else class="row">
                <div class="col-lg-12 table-responsive">
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th scope="col"><h4>Product</h4></th>
                        <th scope="col"><h4>Price</h4></th>
                        <th scope="col"><h4>Quantity</h4></th>
                        <th scope="col"><h4>Subtotal</h4></th>
                        <th scope="col"><h4>Remove</h4></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in cartItems" :key="item.id">
                        <th>
                        <div class="d-flex gap-3 align-items-center">
                            <img :src="item.product.p_image" style="width:70px" class="rounded-3" :alt="item.product.p_name" />
                            <h5 class="mb-0">{{ item.product.p_name }}</h5>
                        </div>
                        </th>
                        <td>
                        <div>Rs {{ item.product.final_price }}</div>
                        <small v-if="item.product.final_price < item.product.p_price" class="text-muted text-decoration-line-through">
                            Rs {{ item.product.p_price }}
                        </small>
                        </td>
                        <td>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-secondary btn-sm" @click="changeQuantity(item, -1)" :disabled="item.quantity <= 1 || isItemLoading(item.id)">
                            <i class="fa-solid fa-minus"></i>
                            </button>
                            <span class="fw-semibold">{{ item.quantity }}</span>
                            <button
                            class="btn btn-secondary btn-sm"
                            @click="changeQuantity(item, 1)"
                            :disabled="item.quantity >= item.product.p_stock || isItemLoading(item.id)"
                            >
                            <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                        </td>
                        <td>Rs {{ item.line_total }}</td>
                        <td>
                        <button type="button" class="btn-close" aria-label="Remove" @click="removeItem(item.id)" :disabled="isItemLoading(item.id)"></button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                </div>

                <div class="col-lg-5 ms-auto my-4">
                <div class="d-flex justify-content-between"><h5>Subtotal</h5><h5>Rs {{ summary.subtotal }}</h5></div>
                <div class="d-flex justify-content-between"><h5>Discount</h5><h5>Rs {{ summary.discount }}</h5></div>
                <div class="d-flex justify-content-between"><h5>Delivery Charges</h5><h5>Free</h5></div>
                <hr />
                <div class="d-flex justify-content-between"><h4>Total</h4><h4>Rs {{ summary.total }}</h4></div>
                <button class="btn theme-login btn-lg mt-3 text-light rounded-pill w-100" @click="proceedToPaycheck">
                    Proceed to Paycheck
                </button>
                </div>
            </div>
            </div>
        </section>
</template>

<script>
import axios from 'axios'
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import MainLayout from './layouts/MainLayout.vue'

export default {
  components: { MainLayout },
  setup() {
    const router = useRouter()
    const loading = ref(true)
    const pendingItemIds = ref([])
    const errorMessage = ref('')
    const cartItems = ref([])
    const summary = ref({ subtotal: 0, discount: 0, total: 0, items_count: 0 })

    const authHeader = () => {
      const token = localStorage.getItem('token')
      return token ? { headers: { Authorization: `Bearer ${token}` } } : {}
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
        errorMessage.value = error.response?.data?.message || 'Unable to load cart.'
      } finally {
        loading.value = false
      }
    }

    const isItemLoading = (id) => pendingItemIds.value.includes(id)

    const changeQuantity = async (item, step) => {
      const nextQuantity = item.quantity + step
      if (nextQuantity < 1 || nextQuantity > item.product.p_stock) return

      pendingItemIds.value.push(item.id)
      try {
        const { data } = await axios.patch(`/api/cart/${item.id}`, { quantity: nextQuantity }, authHeader())
        applyCartResponse(data)
      } catch (error) {
        errorMessage.value = error.response?.data?.message || 'Unable to update quantity.'
      } finally {
        pendingItemIds.value = pendingItemIds.value.filter((id) => id !== item.id)
      }
    }

    const removeItem = async (id) => {
      pendingItemIds.value.push(id)
      try {
        const { data } = await axios.delete(`/api/cart/${id}`, authHeader())
        applyCartResponse(data)
      } catch (error) {
        errorMessage.value = error.response?.data?.message || 'Unable to remove item.'
      } finally {
        pendingItemIds.value = pendingItemIds.value.filter((itemId) => itemId !== id)
      }
    }

    const proceedToPaycheck = () => {
      router.push('/paycheck')
    }

    onMounted(fetchCart)

    return {
      loading,
      errorMessage,
      cartItems,
      summary,
      changeQuantity,
      removeItem,
      proceedToPaycheck,
      isItemLoading,
    }
  },
}
</script>
