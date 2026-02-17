<template>
    <MainLayout >
    <div>
            <div id="productCarousel" class="carousel slide" data-bs-ride="carousel" v-if="carouselDeals.length">
                <div class="carousel-indicators">
                <button
                    v-for="(deal, index) in carouselDeals"
                    :key="`carousel-ind-${deal.p_id}`"
                    type="button"
                    data-bs-target="#productCarousel"
                    :data-bs-slide-to="index"
                    :class="{ active: index === 0 }"
                ></button>
                </div>

                <div class="carousel-inner">
                <div
                    v-for="(deal, index) in carouselDeals"
                    :key="`carousel-${deal.p_id}`"
                    class="carousel-item"
                    :class="{ active: index === 0 }"
                >
                    <img :src="deal.p_image" class="d-block w-100 carousel-img" :alt="deal.p_name">
                    <div class="carousel-caption">
                    <div v-if="deal.discount" class="badge bg-danger mb-2">
                        {{ getDiscountPercent(deal) }}% OFF
                    </div>
                    <h2 class="fw-bold mb-1">{{ deal.p_name }}</h2>
                    <p class="mb-1">{{ getDealTag(index) }}</p>
                    <p class="mb-0 fw-semibold">
                        Rs {{ deal.final_price }}
                        <span v-if="deal.discount" class="text-decoration-line-through text-light-emphasis ms-1">Rs {{ deal.p_price }}</span>
                    </p>
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
                        {{ getDiscountPercent(product) }}% OFF
                        </div>

                        <RouterLink :to="`/product/${product.p_id}`" @click="addRecentView(product)">
                        <img :src="product.p_image" class="card-img-top" :alt="product.p_name">
                        <div class="card-body d-flex flex-column text-dark">
                            <h4 class="card-title text-center">{{ product.p_name }}</h4>
                            <div class="text-center">
                            <span v-if="product.discount" class="text-danger fw-bold me-2">Rs {{ product.final_price }}</span>
                            <span v-if="product.discount" class="text-muted text-decoration-line-through">Rs {{ product.p_price }}</span>
                            <span v-else class="fw-bold">Rs {{ product.p_price }}</span>
                            </div>
                        </div>
                        </RouterLink>
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

                <div class="row row-cols-1 row-cols-md-4 g-2 mt-2">
                    <div
                    v-for="product in popularCategoryProducts"
                    :key="`popular-cat-product-${product.p_id}`"
                    class="col d-flex"
                    >
                    <div class="card w-100 h-100 card-hover position-relative">
                        <div
                        v-if="product.discount"
                        class="badge bg-danger position-absolute"
                        style="top:10px; right:10px; z-index:10;"
                        >
                        {{ getDiscountPercent(product) }}% OFF
                        </div>
                        <RouterLink :to="`/product/${product.p_id}`" @click="addRecentView(product)">
                        <img :src="product.p_image" class="card-img-top" :alt="product.p_name">
                        <div class="card-body text-center">
                            <h4 class="card-title text-dark">{{ product.p_name }}</h4 >
                            <h5 class="mb-0 fw-semibold">
                            Rs {{ product.final_price }}
                            <span v-if="product.discount" class="text-muted text-decoration-line-through ms-1">Rs {{ product.p_price }}</span>
                            </h5>
                        </div>
                        </RouterLink>
                    </div>
                    </div>
                    <span v-if="!popularCategoryProducts.length" class="text-muted">No products found in popular categories.</span>
                </div>
                </div>
            </section>

            <section class="my-5">
                <div class="container">
                <div class="d-flex">
                    <div class="p-2 flex-grow-1"><h1>Recent Views </h1></div>
                    <div class="p-2"><a href="#" class="text-decoration-none btn btn-success btn-sm">View all</a></div>
                </div>

                <div class="row row-cols-1 row-cols-md-4 g-2">
                    <div class="col d-flex" v-for="product in recentViews" :key="`recent-${product.p_id}`">
                    <div class="card w-100 h-100 card-hover position-relative">
                        <div
                        v-if="product.discount"
                        class="badge bg-danger position-absolute"
                        style="top:10px; right:10px; z-index:10;"
                        >
                        {{ getDiscountPercent(product) }}% OFF
                        </div>


                        <RouterLink :to="`/product/${product.p_id}`" @click="addRecentView(product)">
                        <img :src="product.p_image" class="card-img-top" :alt="product.p_name">              <div class="card-body d-flex flex-column text-dark">
                            <h5 class="card-title text-center">{{ product.p_name }}</h5>
                            <p class="small text-muted mb-1">Recently viewed</p>
                            <div class="text-center">
                            <span v-if="product.discount" class="text-danger fw-bold me-2">Rs {{ product.final_price }}</span>
                            <span v-if="product.discount" class="text-muted text-decoration-line-through">Rs {{ product.p_price }}</span>
                            <span v-else class="fw-bold">Rs {{ product.p_price }}</span>
                            </div>
                        </div>
                        </RouterLink>
                    </div>
                    </div>
                </div>
                <div v-if="!recentViews.length" class="text-muted">No recent views yet. Start exploring products.</div>
                </div>

            </section>

            <footer class="site-footer mt-5">
                <div class="container py-4">
                <h5 class="mb-2">About Our Website</h5>
                <p class="mb-0">
                    Welcome to our e-commerce website. Discover top deals, popular categories, and a personalized recent-view section
                    that helps you continue shopping from where you left off.
                </p>
                </div>
            </footer>
        </div>
    </MainLayout>
</template>

<script>
import MainLayout from './layouts/MainLayout.vue'
import axios from 'axios'
import { ref, onMounted, computed } from 'vue'

export default {
  components: {
    MainLayout
  },

  setup() {
    const topDeals = ref([])
    const popularCategoryProducts = ref([])
    const recentViews = ref([])
    const dealTags = ['Top Deals', 'New Arrival', 'Limited Offer']

    const normalizeProducts = products => {
      return (products || []).map(product => ({
        ...product,
         p_image: product.p_image || '/default-product.png',
        discount: product.discount || false,
        final_price: product.final_price ?? product.p_price
      }))
    }

    const carouselDeals = computed(() => topDeals.value.slice(0, 3))

    const getDiscountPercent = product => {
      if (!product.discount || !product.p_price) return 0
      return Math.round(((product.p_price - product.final_price) / product.p_price) * 100)
    }

    const getDealTag = index => dealTags[index % dealTags.length]

    const saveRecentViews = () => {
      localStorage.setItem('recentlyViewedProducts', JSON.stringify(recentViews.value))
    }

    const loadRecentViews = () => {
      const stored = localStorage.getItem('recentlyViewedProducts')
      recentViews.value = stored ? normalizeProducts(JSON.parse(stored)) : []
    }

    const addRecentView = product => {
      const current = recentViews.value.filter(item => item.p_id !== product.p_id)
      recentViews.value = [product, ...current].slice(0, 8)
      saveRecentViews()
    }

    const getCategoryImage = categoryId => {
      const fromTopDeals = topDeals.value.find(item => item.c_id === categoryId)
      const fromRecent = recentViews.value.find(item => item.c_id === categoryId)
      return (fromTopDeals || fromRecent)?.p_image || '/default-product.png'
    }

    const getTopDeals = async () => {
      try {
        const res = await axios.get('/api/top-deals')
        topDeals.value = normalizeProducts(res.data.products)
      } catch (e) {
        console.error('Error loading top deals', e)
      }
    }

    const getPopularCategoryProducts = async () => {
      try {
        const res = await axios.get('/api/popular-products')
        popularCategoryProducts.value = normalizeProducts(res.data.products)      } catch (e) {
        console.error('Error loading popular category products', e)
      }
    }

    onMounted(() => {
      getTopDeals()
      getPopularCategoryProducts()
      loadRecentViews()
    })

    return {
      topDeals,
      carouselDeals,
      getPopularCategoryProducts,
      recentViews,
      addRecentView,
      getDiscountPercent,
      getDealTag,
      getCategoryImage,
      popularCategoryProducts
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
  object-fit: contain;
  background: #f8f9fa;
}

.carousel-caption {
  background: rgba(0, 0, 0, 0.58);
  padding: 12px 16px;
  border-radius: 8px;
   max-width: 460px;
  text-align: left;
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
  object-fit: contain;
  background: #f8f9fa;
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
.site-footer {
  background: #111827;
  color: #f3f4f6;
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
