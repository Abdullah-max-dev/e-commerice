<template>
  <MainLayout>
    <div class="container mt-4">
      <div class="row g-4">
        <div class="col-md-5">
          <img
            :src="product.p_image || '/default-product.png'"
            class="img-fluid rounded shadow-sm w-100"
            :alt="product.p_name || 'Product Image'"
          />
        </div>
         <div class="col-md-7">
          <h3>{{ product.p_name }}</h3>
          <p class="text-muted mb-1">{{ product.category?.c_name || 'Uncategorized' }}</p>
          <h4 class="text-primary fw-bold mb-2">Rs {{ product.final_price ?? product.p_price }}</h4>
          <p v-if="product.discount" class="text-danger mb-2">
            Discount: {{ product.discount.type === 'percentage' ? `${product.discount.value}%` : `Rs ${product.discount.value}` }}
          </p>
          <p>
            <span class="badge" :class="product.p_stock > 0 ? 'bg-success' : 'bg-secondary'">
              {{ product.p_stock > 0 ? 'In Stock' : 'Out of Stock' }}
            </span>
          </p>
          <p class="mt-3">{{ product.p_description || 'No description available.' }}</p>

          <div class="d-flex align-items-center mt-4 gap-2">
            <label class="me-2 mb-0">Quantity:</label>
            <input type="number" v-model.number="quantity" min="1" class="form-control w-auto" />
            <button class="btn btn-primary" :disabled="product.p_stock <= 0" @click="addToCart">
              Add to Cart
            </button>
          </div>

          <p v-if="cartMessage" class="text-success mt-2">{{ cartMessage }}</p>
        </div>
      </div>

    <div class="mt-5">
        <h5>Related Products</h5>
        <div class="row row-cols-1 row-cols-md-4 g-3 mt-2">
          <div class="col" v-for="rel in relatedProducts" :key="rel.p_id">
            <div class="card h-100 shadow-sm">
              <img :src="rel.p_image || '/default-product.png'" class="card-img-top" :alt="rel.p_name" />
              <div class="card-body">
                <h6 class="card-title">{{ rel.p_name }}</h6>
                <p class="card-text text-primary fw-bold mb-0">Rs {{ rel.final_price ?? rel.p_price }}</p>
              </div>
              <div class="card-footer text-center">
                <RouterLink class="btn btn-sm btn-outline-primary w-100" :to="`/product/${rel.p_id}`">
                  View
                </RouterLink>
              </div>
            </div>
          </div>
          <p v-if="!relatedProducts.length" class="text-muted text-center">No related products found.</p>
        </div>

      </div>
    </div>
</MainLayout>
</template>

<script>
import axios from 'axios'
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import MainLayout from './layouts/MainLayout.vue'

export default {
    components: { MainLayout },
  setup() {
    const route = useRoute()
    const product = ref({})
    const quantity = ref(1)
    const cartMessage = ref('')
    const relatedProducts = ref([])
     const fetchProduct = async id => {
      const res = await axios.get(`/api/products/${id}`)
      product.value = res.data.product
      quantity.value = 1

      const relRes = await axios.get(`/api/products/${id}/related`)
      relatedProducts.value = relRes.data.products || []
    }

    const addToCart = () => {
       cartMessage.value = `${quantity.value} x ${product.value.p_name} added to cart!`
    }

    onMounted(() => {
      fetchProduct(route.params.id)
    })

    watch(
      () => route.params.id,
      id => {
        if (id) fetchProduct(id)
      }
    )

    return { product, quantity, addToCart, cartMessage, relatedProducts }
  }
}
</script>

<style scoped>
.card img {
  height: 150px;
  object-fit: cover;
}
</style>
