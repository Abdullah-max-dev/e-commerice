<template>
  <MainLayout>
    <div class="container product-page mt-5">

      <!-- ===== Product Section ===== -->
      <div class="row g-5 align-items-start">

        <!-- Product Image -->
        <div class="col-md-6">

          <!-- Main Image -->
          <img
            :src="selectedImage || product.p_image"
            class="main-img"
          />

          <!-- Thumbnails -->
          <div class="thumb-wrapper">
            <img
              v-for="(img, index) in product.gallery_images"
              :key="index"
              :src="img"
              class="thumb-img"
              :class="{ active: selectedImage === img }"
              @click="selectedImage = img"
            />
          </div>

        </div>

        <!-- Product Info -->
        <div class="col-md-6">

          <h2 class="product-title">{{ product.p_name }}</h2>

          <!-- Vendor Info -->
          <div v-if="product.vender" class="vendor-box mt-2 mb-3">
            <img
            v-if="product.vender?.shop_logo"
            :src="`/storage/shop_logos/${product.vender.shop_logo}`"
            class="vendor-logo"
            alt="Vendor Logo"
            />

            <div>
              <small class="text-muted">Sold by</small>
              <div class="fw-semibold">{{ product.vender.verification_data.business_name }}</div>
            </div>
          </div>

          <!-- Price -->
          <div class="price-section mb-2">
            <span class="current-price">
              Rs {{ product.final_price ?? product.p_price }}
            </span>

            <span v-if="product.discount" class="old-price">
              Rs {{ product.p_price }}
            </span>
          </div>

          <!-- Discount -->
          <div v-if="product.discount" class="discount-badge mb-3">
            {{
              product.discount.type === 'percentage'
                ? product.discount.value + '% OFF'
                : 'Rs ' + product.discount.value + ' OFF'
            }}
          </div>

          <!-- Stock -->
          <div class="mb-3">
            <span
              class="stock-badge"
              :class="product.p_stock > 0 ? 'in-stock' : 'out-stock'"
            >
              {{ product.p_stock > 0 ? 'In Stock' : 'Out of Stock' }}
            </span>
          </div>

          <!-- Description -->
          <p class="description">
            {{ product.p_description || 'No description available.' }}
          </p>

          <!-- Quantity + Cart -->
          <div class="cart-section mt-4">

            <div class="qty-control">
              <button @click="decreaseQty">-</button>
              <input type="number" v-model.number="quantity" min="1" />
              <button @click="increaseQty">+</button>
            </div>

            <button
              class="btn-add-cart"
              :disabled="product.p_stock <= 0"
              @click="addToCart"
            >
              🛒 Add to Cart
            </button>

          </div>

          <div v-if="cartMessage" class="cart-message mt-3">
            {{ cartMessage }}
          </div>

        </div>
      </div>

      <!-- ===== Related Products ===== -->
      <div class="related-section mt-5">
        <h4 class="mb-4">Related Products</h4>

        <div class="row row-cols-1 row-cols-md-4 g-4">
          <div class="col" v-for="rel in relatedProducts" :key="rel.p_id">
            <div class="related-card">
              <img
                :src="rel.p_image || '/default-product.png'"
                class="related-img"
                :alt="rel.p_name"
              />
              <div class="p-3 text-center">
                <h6>{{ rel.p_name }}</h6>
                <p class="fw-bold mb-2">
                  Rs {{ rel.final_price ?? rel.p_price }}
                </p>
                <RouterLink
                  class="btn-view"
                  :to="`/product/${rel.p_id}`"
                >
                  View
                </RouterLink>
              </div>
            </div>
          </div>
        </div>

        <p v-if="!relatedProducts.length" class="text-muted text-center mt-3">
          No related products found.
        </p>
      </div>

    </div>
  </MainLayout>
</template>

<script>

import axios from 'axios'
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter  } from 'vue-router'
import MainLayout from './layouts/MainLayout.vue'

export default {
  components: { MainLayout },

  setup() {
    const router = useRouter()
    const route = useRoute()
    const product = ref({})
    const quantity = ref(1)
    const cartMessage = ref('')
    const relatedProducts = ref([])
    const selectedImage = ref(null)

    const fetchProduct = async id => {
      const res = await axios.get(`/api/products/${id}`)
      product.value = res.data.product
      quantity.value = 1
      selectedImage.value = null

      const relRes = await axios.get(`/api/products/${id}/related`)
      relatedProducts.value = relRes.data.products || []
    }

    const increaseQty = () => {
      if (quantity.value < product.value.p_stock) {
        quantity.value++
      }
    }

    const decreaseQty = () => {
      if (quantity.value > 1) {
        quantity.value--
      }
    }

    const addToCart = async () => {
      const token = localStorage.getItem('token')
      const role = localStorage.getItem('role')

      if (!token || role !== 'user') {
        cartMessage.value = 'Please login as a user to add items to cart.'
        router.push('/user-login')
        return
      }

      try {
        const { data } = await axios.post(
          '/api/cart',
          {
            p_id: product.value.p_id,
            quantity: quantity.value,
          },
          {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          }
        )

        cartMessage.value = data.message || `${quantity.value} x ${product.value.p_name} added to cart!`
      } catch (error) {
        cartMessage.value = error.response?.data?.message || 'Unable to add this product to cart.'
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

    return {
      product,
      quantity,
      increaseQty,
      decreaseQty,
      addToCart,
      cartMessage,
      relatedProducts,
      selectedImage
    }
  },
}
</script>

<style scoped>



.main-img {
  width: 100%;
  height: 420px;
  object-fit: contain;
  background: #f9fafb;
  padding: 25px;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
  transition: transform 0.4s ease;
}

.main-img:hover {
  transform: scale(1.08);
}

.thumb-wrapper {
  display: flex;
  gap: 12px;
  margin-top: 15px;
  overflow-x: auto;
  padding-bottom: 5px;
}

.thumb-wrapper::-webkit-scrollbar {
  height: 6px;
}

.thumb-wrapper::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 10px;
}

.thumb-img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 14px;
  cursor: pointer;
  background: #ffffff;
  padding: 5px;
  border: 2px solid transparent;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.thumb-img:hover {
  transform: translateY(-4px);
  border-color: #2563eb;
}

.thumb-img.active {
  border-color: #111827;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
}



.product-title {
  font-weight: 700;
  font-size: 1.8rem;
}

.vendor-box {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #f3f4f6;
  padding: 8px 12px;
  border-radius: 12px;
  width: fit-content;
}

.vendor-logo {
  width: 45px;
  height: 45px;
  border-radius: 50%;
  object-fit: cover;
}

.price-section {
  font-size: 1.6rem;
}

.current-price {
  color: #2563eb;
  font-weight: 700;
}

.old-price {
  text-decoration: line-through;
  color: #9ca3af;
  margin-left: 10px;
  font-size: 1rem;
}

.discount-badge {
  display: inline-block;
  background: #dc2626;
  color: white;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.85rem;
}

.stock-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
}

.in-stock {
  background: #16a34a;
  color: white;
}

.out-stock {
  background: #6b7280;
  color: white;
}

.description {
  line-height: 1.6;
  color: #374151;
}

.cart-section {
  display: flex;
  gap: 15px;
  align-items: center;
}

.qty-control {
  display: flex;
  border: 1px solid #d1d5db;
  border-radius: 25px;
  overflow: hidden;
}

.qty-control button {
  background: #f3f4f6;
  border: none;
  padding: 6px 12px;
  cursor: pointer;
}

.qty-control input {
  width: 50px;
  text-align: center;
  border: none;
}

.btn-add-cart {
  background: #111827;
  color: white;
  border: none;
  padding: 10px 22px;
  border-radius: 25px;
  transition: 0.3s;
}

.btn-add-cart:hover {
  background: #2563eb;
  transform: translateY(-2px);
}

.cart-message {
  color: #16a34a;
  font-weight: 500;
}



.related-card {
  background: white;
  border-radius: 18px;
  box-shadow: 0 8px 22px rgba(0,0,0,0.08);
  transition: 0.3s;
  overflow: hidden;
}

.related-card:hover {
  transform: translateY(-6px);
}

.related-img {
  height: 180px;
  width: 100%;
  object-fit: contain;
  background: #f9fafb;
  padding: 15px;
}

.btn-view {
  background: #2563eb;
  color: white;
  padding: 6px 18px;
  border-radius: 20px;
  text-decoration: none;
  font-size: 0.85rem;
}

.btn-view:hover {
  background: #111827;
}

@media (max-width: 768px) {
  .cart-section {
    flex-direction: column;
    align-items: flex-start;
  }

  .main-img {
    height: 300px;
  }
}

</style>
