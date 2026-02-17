<template>
  <div class="container mt-4">
    <div class="row">
      <!-- Product Image -->
      <div class="col-md-5">
        <img
          :src="product.image || '/placeholder.png'"
          class="img-fluid rounded shadow-sm"
          alt="Product Image"
        />
      </div>

      <!-- Product Info -->
      <div class="col-md-7">
        <h3>{{ product.name }}</h3>
        <p class="text-muted">{{ product.category_name }}</p>
        <h4 class="text-primary fw-bold">$ {{ product.price }}</h4>
        <p v-if="product.discount">Discount: {{ product.discount }}%</p>
        <p>
          <span
            class="badge"
            :class="product.is_active ? 'bg-success' : 'bg-secondary'"
          >
            {{ product.is_active ? 'In Stock' : 'Out of Stock' }}
          </span>
        </p>
        <p class="mt-3">{{ product.description || 'No description available.' }}</p>

        <!-- Quantity & Add to Cart -->
        <div class="d-flex align-items-center mt-4 gap-2">
          <label class="me-2 mb-0">Quantity:</label>
          <input
            type="number"
            v-model.number="quantity"
            min="1"
            class="form-control w-auto"
          />
          <button
            class="btn btn-primary"
            :disabled="!product.is_active"
            @click="addToCart"
          >
            Add to Cart
          </button>
        </div>

        <p v-if="cartMessage" class="text-success mt-2">{{ cartMessage }}</p>
      </div>
    </div>

    <!-- Related Products -->
    <div class="mt-5">
      <h5>Related Products</h5>
      <div class="row row-cols-1 row-cols-md-4 g-3 mt-2">
        <div
          class="col"
          v-for="rel in relatedProducts"
          :key="rel.id"
        >
          <div class="card h-100 shadow-sm">
            <img
              :src="rel.image || '/placeholder.png'"
              class="card-img-top"
              alt="Related Product"
            />
            <div class="card-body">
              <h6 class="card-title">{{ rel.name }}</h6>
              <p class="card-text text-primary fw-bold">$ {{ rel.price }}</p>
            </div>
            <div class="card-footer text-center">
              <button class="btn btn-sm btn-outline-primary w-100" @click="viewProduct(rel.id)">
                View
              </button>
            </div>
          </div>
        </div>
        <p v-if="!relatedProducts.length" class="text-muted text-center">
          No related products found.
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";

export default {
  setup() {
    const route = useRoute();
    const product = ref({});
    const quantity = ref(1);
    const cartMessage = ref("");
    const relatedProducts = ref([]);

    const fetchProduct = async () => {
      const res = await axios.get(`/api/products/${route.params.id}`);
      product.value = res.data.data;

      // Fetch related products (same category)
      const relRes = await axios.get(
        `/api/products?category_id=${product.value.category_id}&exclude=${product.value.id}`
      );
      relatedProducts.value = relRes.data.data;
    };

    const addToCart = () => {
      // For now, just show message. Later integrate with Vuex or backend cart API
      cartMessage.value = `${quantity.value} x ${product.value.name} added to cart!`;
    };

    const viewProduct = (id) => {
      window.location.href = `/product/${id}`;
    };

    onMounted(() => {
      fetchProduct();
    });

    return { product, quantity, addToCart, cartMessage, relatedProducts, viewProduct };
  },
};
</script>

<style scoped>
.card img {
  height: 150px;
  object-fit: cover;
}
</style>
