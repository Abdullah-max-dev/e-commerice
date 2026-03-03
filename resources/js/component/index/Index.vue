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
                    <div v-for="product in visibleCategoryProducts" :key="`cat-${product.p_id}`" class="col d-flex">
                        <ProductCard :product="product" @open="addRecentView" />
                    </div>
                </div>
                <div v-if="categoryProducts.length > 4" class="mt-3 d-flex gap-2">
                    <button class="btn btn-outline-success btn-sm" @click="showMoreCategoryProducts" :disabled="categoryVisibleCount >= categoryProducts.length">Show More</button>
                    <button class="btn btn-outline-secondary btn-sm" @click="resetCategoryProducts" :disabled="categoryVisibleCount === 4">Show Less</button>
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
                         <button class="btn btn-outline-success btn-sm" @click="showMoreDeals" :disabled="topDealsVisibleCount >= topDeals.length">Show More</button>
                        <button class="btn btn-outline-secondary btn-sm" @click="resetDeals" :disabled="topDealsVisibleCount === 4">Show Less</button>
                    </div>
                </div>
            </section>

            <section class="my-5">
                <div class="container">
                    <h1>Popular Categories</h1>
                    <div class="row row-cols-1 row-cols-md-4 g-2 mt-2">
                        <div v-for="product in visiblePopularCategoryProducts" :key="`popular-${product.p_id}`" class="col d-flex">                            <ProductCard :product="product" @open="addRecentView" />
                        </div>
                    </div>
                    <div v-if="popularCategoryProducts.length > 4" class="mt-3 d-flex gap-2">
                        <button class="btn btn-outline-success btn-sm" @click="showMorePopularProducts" :disabled="popularVisibleCount >= popularCategoryProducts.length">Show More</button>
                        <button class="btn btn-outline-secondary btn-sm" @click="resetPopularProducts" :disabled="popularVisibleCount === 4">Show Less</button>
                    </div>
                </div>
            </section>

        <section class="my-5">
            <div class="container">
                <h1>Recently Viewed</h1>
                <div class="row row-cols-1 row-cols-md-4 g-2 mt-2">
                    <div v-for="product in visibleRecentViews" :key="`recent-${product.p_id}`" class="col d-flex">                        <ProductCard :product="product" @open="addRecentView" />
                    </div>
                </div>
                <div v-if="recentViews.length > 4" class="mt-3 d-flex gap-2">
                    <button class="btn btn-outline-success btn-sm" @click="showMoreRecentViews" :disabled="recentVisibleCount >= recentViews.length">Show More</button>
                    <button class="btn btn-outline-secondary btn-sm" @click="resetRecentViews" :disabled="recentVisibleCount === 4">Show Less</button>
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
import ProductCard from './ProductCard.vue'
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
         const popularVisibleCount = ref(4)
        const categoryVisibleCount = ref(4)
        const recentVisibleCount = ref(4)

        const dealTags = ['Top Deals', 'New Arrival', 'Limited Offer']

        const selectedCategoryId = computed(() => route.query.category || null)

        const visibleTopDeals = computed(() =>
            topDeals.value.slice(0, topDealsVisibleCount.value)
        )

        const carouselDeals = computed(() =>
            topDeals.value.slice(0, 3)
        )

        const visiblePopularCategoryProducts = computed(() =>
            popularCategoryProducts.value.slice(0, popularVisibleCount.value)
        )

        const visibleCategoryProducts = computed(() =>
            categoryProducts.value.slice(0, categoryVisibleCount.value)
        )

        const visibleRecentViews = computed(() =>
            recentViews.value.slice(0, recentVisibleCount.value)
        )

        const normalizeProducts = (products) =>
            (products || []).map(product => ({
            ...product,
            p_image: product.p_image || '/default-product.png',
            discount: product.discount || false,
            final_price: product.final_price ?? product.p_price
            }))
        const getDiscountPercent = (product) => {
            if (!product?.discount || !product?.p_price) {
                return 0
            }

            return Math.round(((product.p_price - product.final_price) / product.p_price) * 100)
        }

        const getDealTag = (index) => dealTags[index % dealTags.length]

        const addRecentView = (product) => {
            if (!product?.p_id) {
                return
            }

            recentViews.value = [
                product,
                ...recentViews.value.filter(item => item.p_id !== product.p_id)
            ].slice(0, 8)

            recentVisibleCount.value = Math.max(4, Math.min(recentVisibleCount.value, recentViews.value.length))
        }

        const getTopDeals = async () => {
            const { data } = await axios.get('/api/top-deals', {
            params: { limit: 40 }
            })
            topDeals.value = normalizeProducts(data.products)
        }

        const getPopularCategoryProducts = async () => {
            const { data } = await axios.get('/api/popular-products')
            popularCategoryProducts.value = normalizeProducts(data.products)
            popularVisibleCount.value = 4
        }

        const getProductsByCategory = async (categoryId) => {
            if (!categoryId) {
            categoryProducts.value = []
            return
            }

            const { data } = await axios.get(`/api/categories/${categoryId}/products`)
            categoryProducts.value = normalizeProducts(data.products)
            categoryVisibleCount.value = 4
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

        const showMorePopularProducts = () => {
            popularVisibleCount.value = Math.min(
                popularVisibleCount.value + 4,
                popularCategoryProducts.value.length
            )
        }

        const resetPopularProducts = () => {
            popularVisibleCount.value = 4
        }

        const showMoreCategoryProducts = () => {
            categoryVisibleCount.value = Math.min(
                categoryVisibleCount.value + 4,
                categoryProducts.value.length
            )
        }

        const resetCategoryProducts = () => {
            categoryVisibleCount.value = 4
        }

        const showMoreRecentViews = () => {
            recentVisibleCount.value = Math.min(
                recentVisibleCount.value + 4,
                recentViews.value.length
            )
        }

        const resetRecentViews = () => {
            recentVisibleCount.value = 4
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
            visiblePopularCategoryProducts,
            visibleCategoryProducts,
            visibleRecentViews,
            topDealsVisibleCount,
            popularVisibleCount,
            categoryVisibleCount,
            recentVisibleCount,
            showMoreDeals,
            resetDeals,
            showMorePopularProducts,
            resetPopularProducts,
            showMoreCategoryProducts,
            resetCategoryProducts,
            showMoreRecentViews,
            resetRecentViews,
            getDiscountPercent,   
            getDealTag,
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
