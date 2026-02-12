<template>
  <MainLayout />

  <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="0" class="active"></button>
      <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="1"></button>
      <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="2"></button>
    </div>

    <div class="carousel-inner">
      <div class="carousel-item active">
        <img
          src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=1920&q=80"
          class="d-block w-100 carousel-img"
          alt="Shoes"
        >
        <div class="carousel-caption d-none d-md-block">
          <h2 class="fw-bold">Step Up Your Style</h2>
          <p>Comfort & design that moves with you</p>
        </div>
      </div>

      <div class="carousel-item">
        <img
          src="https://images.unsplash.com/photo-1491553895911-0055eca6402d?w=1920&q=80"
          class="d-block w-100 carousel-img"
          alt="Fashion Products"
        >
        <div class="carousel-caption d-none d-md-block">
          <h2 class="fw-bold">New Season Arrivals</h2>
          <p>Check out all the new trends</p>
        </div>
      </div>

      <div class="carousel-item">
        <img
          src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=1920&q=80"
          class="d-block w-100 carousel-img"
          alt="Electronics"
        >
        <div class="carousel-caption d-none d-md-block">
          <h2 class="fw-bold">Latest Arrival</h2>
          <p>Smart gadgets for smart living</p>
        </div>
      </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>

  <section class="my-5">
    <div class="container">
      <div class="d-flex">
        <div class="p-2 flex-grow-1"><h1>Top Deals</h1></div>
        <div class="p-2"><a href="#" class="text-decoration-none btn btn-success btn-sm">View all</a></div>
      </div>

      <div class="row row-cols-1 row-cols-md-4 g-2">
        <div class="col d-flex" v-for="product in topDeals" :key="`top-${product.p_id}`">
          <div class="card w-100 h-100 card-hover position-relative">
            <div
              v-if="product.discount"
              class="badge bg-danger position-absolute"
              style="top:10px; right:10px; z-index:10;"
            >
              {{ Math.round(((product.p_price - product.final_price) / product.p_price) * 100) }}% OFF
            </div>

            <a href="#">
              <img :src="$product.p_image" class="card-img-top" :alt="product.p_name">
              <div class="card-body d-flex flex-column text-dark">
                <h5 class="card-title text-center">{{ product.p_name }}</h5>
                <div class="text-center">
                  <span v-if="product.discount" class="text-danger fw-bold me-2">Rs {{ product.final_price }}</span>
                  <span v-if="product.discount" class="text-muted text-decoration-line-through">Rs {{ product.p_price }}</span>
                  <span v-else class="fw-bold">Rs {{ product.p_price }}</span>
                </div>
              </div>
            </a>
          </div>
        </div>

        <div v-if="!topDeals.length" class="text-muted">No top deals available right now.</div>
      </div>
    </div>
  </section>

  <section class="my-5">
    <div class="container">
      <div class="d-flex">
        <div class="p-2 flex-grow-1"><h1>Popular Categories</h1></div>
      </div>

      <div class="d-flex flex-wrap gap-2 mt-3">
        <img :src="$product.p_image" alt="">
        <span
          v-for="category in popularCategories"
          :key="`cat-${category.c_id}`"
          class="badge rounded-pill bg-primary px-3 py-2"
        >
          {{ category.c_name }}
        </span>
        <span v-if="!popularCategories.length" class="text-muted">No popular categories found.</span>
      </div>
    </div>
  </section>

  <section class="my-5">
    <div class="container">
      <div class="d-flex">
        <div class="p-2 flex-grow-1"><h1>Popular Products</h1></div>
        <div class="p-2"><a href="#" class="text-decoration-none btn btn-success btn-sm">View all</a></div>
      </div>

      <div class="row row-cols-1 row-cols-md-4 g-2">
        <div class="col d-flex" v-for="product in popularProducts" :key="product.p_id">
          <div class="card w-100 h-100 card-hover position-relative">
            <div
              v-if="product.discount"
              class="badge bg-danger position-absolute"
              style="top:10px; right:10px; z-index:10;"
            >
              {{ Math.round(((product.p_price - product.final_price) / product.p_price) * 100) }}% OFF
            </div>

            <a href="#">
              <img :src="`/uploads/products/${product.p_image}`" class="card-img-top" :alt="product.p_name">
              <div class="card-body d-flex flex-column text-dark">
                <h5 class="card-title text-center">{{ product.p_name }}</h5>

                <div class="text-center">
                  <span v-if="product.discount" class="text-danger fw-bold me-2">Rs {{ product.final_price }}</span>
                  <span v-if="product.discount" class="text-muted text-decoration-line-through">Rs {{ product.p_price }}</span>
                  <span v-else class="fw-bold">Rs {{ product.p_price }}</span>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import MainLayout from './layouts/MainLayout.vue'
import axios from 'axios'
import { ref, onMounted } from 'vue'

export default {
  components: {
    MainLayout
  },

  setup() {
    const popularProducts = ref([])
    const topDeals = ref([])
    const popularCategories = ref([])

    const normalizeProducts = products => {
      return (products || []).map(product => ({
        ...product,
        p_image: product.p_image || 'default-product.png',
        discount: product.discount || false,
        final_price: product.final_price ?? product.p_price
      }))
    }

    const getPopularProducts = async () => {
      try {
        const res = await axios.get('/api/popular-products')
        popularProducts.value = normalizeProducts(res.data.products)
      } catch (e) {
        console.error('Error loading popular products', e)
      }
    }

    const getTopDeals = async () => {
      try {
        const res = await axios.get('/api/top-deals')
        topDeals.value = normalizeProducts(res.data.products)
      } catch (e) {
        console.error('Error loading top deals', e)
      }
    }

    const getPopularCategories = async () => {
      try {
        const res = await axios.get('/api/categories-list')
        popularCategories.value = (res.data || []).filter(category => category.is_popular)
      } catch (e) {
        console.error('Error loading categories', e)
      }
    }

    onMounted(() => {
      getPopularProducts()
      getTopDeals()
      getPopularCategories()
    })

    return {
      popularProducts,
      topDeals,
      popularCategories
    }
  }
}
</script>

<style>
h1 {
  font-weight: 700;
  font-size: 1.4rem;
  margin-bottom: 0;
}

section {
  margin-top: 30px;
}

.carousel-img {
  height: 55vh;
  width: 100%;
  object-fit: cover;
}

.carousel-caption {
  background: rgba(0, 0, 0, 0.5);
  padding: 12px 16px;
  border-radius: 8px;
}

.carousel-caption h2 {
  font-size: 1.6rem;
  margin-bottom: 4px;
}

.carousel-caption p {
  font-size: 0.9rem;
  margin-bottom: 0;
}

@media (max-width: 768px) {
  .carousel-img {
    height: 38vh;
  }
}

.card {
  border-radius: 12px;
  box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
  overflow: hidden;
  transition: all 0.2s ease;
  background: #fff;
}

.card img {
  width: 100%;
  height: 220px;
  object-fit: cover;
  display: block;
}

.card-body {
  padding: 10px 12px;
  text-align: center;
}

.card-body h5 {
  font-size: 0.95rem;
  margin-bottom: 4px;
  font-weight: 600;
  color: #212529;
}

.card-hover:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 22px rgba(0, 0, 0, 0.12);
}

.btn-sm {
  padding: 4px 12px;
  font-size: 0.75rem;
  border-radius: 20px;
}

.row {
  --bs-gutter-x: 0.5rem;
  --bs-gutter-y: 0.5rem;
}

@media (max-width: 576px) {
  .card img {
    height: 180px;
  }

  h1 {
    font-size: 1.2rem;
  }
}
</style>
