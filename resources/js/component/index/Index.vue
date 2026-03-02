<template>
    <MainLayout>
    <div>
        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel" v-if="carouselDeals.length">
            <div class="carousel-indicators">
                <button v-for="(deal, index) in carouselDeals" :key="`carousel-ind-${deal.p_id}`" type="button" data-bs-target="#productCarousel" :data-bs-slide-to="index" :class="{ active: index === 0 }"></button>
            </div>
            <div class="carousel-inner">
                <div v-for="(deal, index) in carouselDeals" :key="`carousel-${deal.p_id}`" class="carousel-item" :class="{ active: index === 0 }">
                    <img :src="deal.p_image" class="d-block w-100 carousel-img" :alt="deal.p_name">
                    <div class="carousel-caption">
                        <div v-if="deal.discount" class="badge bg-danger mb-2">{{ getDiscountPercent(deal) }}% OFF</div>
                        <h2 class="fw-bold mb-1">{{ deal.p_name }}</h2>
                        <p class="mb-1">{{ getDealTag(index) }}</p>

                    </div>
                </div>
            </div>
        </div>
        <section v-if="selectedCategoryId" class="my-5">
            <div class="container">
                <h1>Selected Category Products</h1>
                <div class="row row-cols-1 row-cols-md-4 g-2 mt-2">
                    <div v-for="product in categoryProducts" :key="`cat-${product.p_id}`" class="col d-flex">
                        <ProductCard :product="product" @open="addRecentView" />
                    </div>
                </div>
                <div v-if="!categoryProducts.length" class="text-muted mt-2">No products found for the selected category.</div>
            </div>
        </section>
        <template v-else>
            <section class="my-5">
                <div class="container">
                    <div class="d-flex align-items-center">
                        <div class="p-2 flex-grow-1"><h1>Top Deals</h1></div>
                    </div>
                    <div class="row row-cols-1 row-cols-md-4 g-2">
                        <div class="col d-flex" v-for="product in visibleTopDeals" :key="`top-${product.p_id}`">
                            <ProductCard :product="product" @open="addRecentView" />
                        </div>
                        <div v-if="!topDeals.length" class="text-muted">No top deals available right now.</div>
                    </div>
                    <div v-if="topDeals.length > 4" class="mt-3 d-flex gap-2">
                        <button class="btn btn-outline-success btn-sm" @click="showMoreDeals" :disabled="topDealsVisibleCount >= topDeals.length">More</button>
                        <button class="btn btn-outline-secondary btn-sm" @click="resetDeals" :disabled="topDealsVisibleCount === 4">Less</button>
                    </div>
                </div>
            </section>

            <section class="my-5">
                <div class="container">
                    <h1>Popular Categories</h1>
                    <div class="row row-cols-1 row-cols-md-4 g-2 mt-2">
                        <div v-for="product in popularCategoryProducts" :key="`popular-${product.p_id}`" class="col d-flex">
                            <ProductCard :product="product" @open="addRecentView" />
                        </div>
                    </div>
                </div>
            </section>

        <section class="my-5">
            <div class="container">
                <h1>Recently Viewed</h1>
                <div class="row row-cols-1 row-cols-md-4 g-2 mt-2">
                    <div v-for="product in recentViews" :key="`recent-${product.p_id}`" class="col d-flex">
                        <ProductCard :product="product" @open="addRecentView" />
                    </div>
                </div>
                <div v-if="!recentViews.length" class="text-muted">No recent views yet. Start exploring products.</div>
            </div>
        </section>
      </template>
    </div>
  </MainLayout>

</template>

<script>
import MainLayout from './layouts/MainLayout.vue'
import axios from 'axios'
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute } from 'vue-router';


const ProductCard = {
  props: ['product'],
  emits: ['open'],
  template: `
    <div class="card w-100 h-100 card-hover position-relative">
      <div v-if="product.discount" class="badge bg-danger position-absolute" style="top:10px; right:10px; z-index:10;">{{ Math.round(((product.p_price - product.final_price) / product.p_price) * 100) }}% OFF</div>
      <RouterLink :to="'/product/' + product.p_id" @click="$emit('open', product)">
        <img :src="product.p_image" class="card-img-top" :alt="product.p_name">
        <div class="card-body d-flex flex-column text-dark text-center">
          <h5 class="card-title">{{ product.p_name }}</h5>
          <div>
            <span v-if="product.discount" class="text-danger fw-bold me-2">Rs {{ product.final_price }}</span>
            <span v-if="product.discount" class="text-muted text-decoration-line-through">Rs {{ product.p_price }}</span>
            <span v-else class="fw-bold">Rs {{ product.p_price }}</span>
          </div>
        </div>
      </RouterLink>
    </div>
  `,

}
    export default {

    components:{
        MainLayout, ProductCard
    },

    setup() {
        const route = useRoute()

        const topDeals = ref([])
        const popularCategoryProducts = ref([])
        const categoryProducts = ref([])
        const recentViews = ref([])
        const topDealsVisibleCount = ref(4)

        const dealTags = ['Top Deals', 'New Arrival', 'Limited Offer']

        const selectedCategoryId = computed(() => route.query.category || null)

        const visibleTopDeals = computed(() =>
            topDeals.value.slice(0, topDealsVisibleCount.value)
        )

        const carouselDeals = computed(() =>
            topDeals.value.slice(0, 3)
        )

        const normalizeProducts = (products) =>
            (products || []).map(product => ({
            ...product,
            p_image: product.p_image || '/default-product.png',
            discount: product.discount || false,
            final_price: product.final_price ?? product.p_price
            }))

        const getTopDeals = async () => {
            const { data } = await axios.get('/api/top-deals', {
            params: { limit: 40 }
            })
            topDeals.value = normalizeProducts(data.products)
        }

        const getPopularCategoryProducts = async () => {
            const { data } = await axios.get('/api/popular-products')
            popularCategoryProducts.value = normalizeProducts(data.products)
        }

        const getProductsByCategory = async (categoryId) => {
            if (!categoryId) {
            categoryProducts.value = []
            return
            }

            const { data } = await axios.get(`/api/categories/${categoryId}/products`)
            categoryProducts.value = normalizeProducts(data.products)
        }

        const showMoreDeals = () => {
            topDealsVisibleCount.value = Math.min(
            topDealsVisibleCount.value + 4,
            topDeals.value.length
            )
        }

        const resetDeals = () => {
            topDealsVisibleCount.value = 4
        }

        watch(
            selectedCategoryId,
            (value) => {
            getProductsByCategory(value)
            },
            { immediate: true }
        )

        onMounted(async () => {
            await Promise.all([getTopDeals(), getPopularCategoryProducts()])
        })

        return {
            topDeals,
            popularCategoryProducts,
            categoryProducts,
            recentViews,
            carouselDeals,
            selectedCategoryId,
            visibleTopDeals,
            topDealsVisibleCount,
            showMoreDeals,
            resetDeals,
            getDiscountPercent,   // ✅ ADD THIS
            getDealTag,           // ✅ ADD THIS
            addRecentView
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
