<template>
  <MainLayout>
    <div class="container product-page mt-5">

      <!-- ===== Product Section ===== -->
      <div class="row g-5 align-items-start">

        <!-- Product Image -->
        <div class="col-md-6">
            <img :src="selectedImage || product?.p_image || '/default-product.png'" class="img-fluid mb-3 main-img" />
            <div class="thumb-wrapper" v-if="product?.gallery_images?.length">
            <img
              v-for="(img, index) in product.gallery_images || []"
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

          <h2 class="product-title" v-if="product?.p_name">{{ product.p_name }}</h2>

          <!-- Vendor Info -->
          <div class="mb-2 text-warning fw-semibold">
            {{ averageRating.toFixed(1) }} ★
            <small class="text-muted ms-1">({{ ratingsCount }} ratings)</small>
          </div>
          <div v-if="vender" class="vendor-box mt-2 mb-3">
            <img v-if="vender?.shop_logo" :src="`/storage/shop_logos/${vender.shop_logo}`" class="vendor-logo" alt="Vendor Logo" />

            <div>
              <small class="text-muted">Sold by</small>
              <div class="fw-semibold">{{venderName }}</div>
            </div>
          </div>

          <!-- Price -->
          <div class="price-section mb-2">
            <span class="current-price">Rs {{ product.final_price ?? product.p_price }}</span>
            <span v-if="product.discount" class="old-price">Rs {{ product.p_price }}</span>
          </div>

          <!-- Discount -->
          <div v-if="product.discount" class="discount-badge mb-3">
             {{ product.discount.type === 'percentage' ? product.discount.value + '% OFF' : 'Rs ' + product.discount.value + ' OFF' }}
          </div>

          <!-- Stock -->
          <div class="mb-3">
            <span class="stock-badge" :class="product.p_stock > 0 ? 'in-stock' : 'out-stock'">
              {{ product.p_stock > 0 ? 'In Stock' : 'Out of Stock' }}
            </span>
          </div>

          <!-- Description -->
          <p class="description">{{ product.p_description || 'No description available.' }}</p>

          <!-- Quantity + Cart -->
          <div class="cart-section mt-4">

            <div class="qty-control">
              <button @click="decreaseQty">-</button>
              <input type="number" v-model.number="quantity" min="1" />
              <button @click="increaseQty">+</button>
            </div>

            <button class="btn-add-cart" :disabled="product.p_stock <= 0" @click="addToCart">🛒 Add to Cart</button>
          </div>

          </div>

          <div v-if="cartMessage" class="cart-message mt-3">
            {{ cartMessage }}
          </div>

        </div>
      </div>

      <!-- ===== Comment Section ===== -->

        <div class="comment-section mt-5">
            <h4 class="mb-3">Comments & Ratings</h4>

            <div class="card p-3 mb-3">
                <h6>{{ replyingTo ? 'Reply to comment' : 'Write a comment' }}</h6>

                <div v-if="!replyingTo" class="mb-2">
                    <label class="form-label">Star rating *</label>
                    <div class="star-picker">
                        <button v-for="star in 5" :key="star" type="button" class="star-btn" :class="{ active: star <= newRating }" @click="newRating = star">★</button>
                    </div>
                </div>
                <textarea v-model="newComment" class="form-control mb-2" rows="3" placeholder="Write your comment"></textarea>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary" @click="submitComment">Submit</button>
                    <button v-if="replyingTo" class="btn btn-outline-secondary" @click="cancelReply">Cancel</button>
                    <small v-if="commentMessage" class="d-block mt-2 text-muted">{{ commentMessage }}</small>
                    <small v-if="!isUserLoggedIn" class="d-block mt-2 text-danger">Login as a user to post comments/replies.</small>
                    </div>

                    <div class="comment-list" v-if="comments.length">
                        <ProductCommentItem
                            v-for="comment in comments"
                            :key="comment.id"
                            :comment="comment"
                            :can-reply="isUserLoggedIn"
                            @reply="startReply"
                        />
                    </div>
                    <p v-else class="text-muted">No comments yet.</p>
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
  </MainLayout>
</template>

<script>

import axios from 'axios'
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import ProductCommentItem from './ProductCommentItem.vue'
import MainLayout from './layouts/MainLayout.vue'

    export default {
        components: { MainLayout, ProductCommentItem},

        setup() {
            const router = useRouter()
            const route = useRoute()
            const defaultProduct = { gallery_images: [], p_stock: 0 }
            const product = ref({ ...defaultProduct })
            const quantity = ref(1)
            const cartMessage = ref('')
            const relatedProducts = ref([])
            const selectedImage = ref(null)

            const comments = ref([])
            const averageRating = ref(0)
            const ratingsCount = ref(0)
            const newComment = ref('')
            const newRating = ref(0)
            const replyingTo = ref(null)
            const commentMessage = ref('')

            const vender = computed(() => product.value?.vender ?? null)
            const venderName = computed(() => vender.value?.verification_data?.business_name || 'Verified Vender')
            const isUserLoggedIn = computed(() => !!localStorage.getItem('token') && localStorage.getItem('role') === 'user')

            const fetchComments = async id => {
            const { data } = await axios.get(`/api/products/${id}/comments`)
            comments.value = data.comments || []
            averageRating.value = Number(data.average_rating || 0)
            ratingsCount.value = Number(data.ratings_count || 0)
            }

            const fetchProduct = async id => {
                const res = await axios.get(`/api/products/${id}`)
                product.value = res.data?.product ?? { ...defaultProduct }
                averageRating.value = Number(res.data?.average_rating || 0)
                ratingsCount.value = Number(res.data?.ratings_count || 0)
                quantity.value = 1
                selectedImage.value = null

                const relRes = await axios.get(`/api/products/${id}/related`)
                relatedProducts.value = relRes.data.products || []
                await fetchComments(id)
            }

            const increaseQty = () => {
            if (quantity.value < (product.value?.p_stock ?? 0)) {
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
                    { p_id: product.value?.p_id, quantity: quantity.value },
                    { headers: { Authorization: `Bearer ${token}` } }
                    )

                    cartMessage.value = data.message || `${quantity.value} x ${product.value?.p_name} added to cart!`
                } catch (error) {
                    cartMessage.value = error.response?.data?.message || 'Unable to add this product to cart.'
                }

            }
            const startReply = comment => {
                replyingTo.value = comment
                commentMessage.value = `Replying to ${comment.user?.name || 'user'}`
            }
            const cancelReply = () => {
                replyingTo.value = null
                newComment.value = ''
                commentMessage.value = ''
            }
            const submitComment = async () => {
                if (!isUserLoggedIn.value) {
                    router.push('/user-login')
                    return
                }

                if (!newComment.value.trim()) {
                    commentMessage.value = 'Comment text is required.'
                    return
                }

                if (!replyingTo.value && !newRating.value) {
                    commentMessage.value = 'Please choose a rating from 1 to 5 stars.'
                    return
                }

                try {
                    const token = localStorage.getItem('token')
                    const payload = { comment: newComment.value }

                    if (replyingTo.value) payload.parent_id = replyingTo.value.id
                    else payload.rating = newRating.value

                    const { data } = await axios.post(`/api/products/${product.value?.p_id}/comments`, payload, {
                    headers: { Authorization: `Bearer ${token}` },
                    })

                    commentMessage.value = data.message || 'Comment submitted successfully.'
                    newComment.value = ''
                    newRating.value = 0
                    replyingTo.value = null
                    await fetchComments(product.value?.p_id)
                } catch (error) {
                    commentMessage.value = error.response?.data?.message || 'Unable to submit comment.'
                }
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
                selectedImage,
                vender,
                venderName,
                comments,
                averageRating,
                ratingsCount,
                newComment,
                newRating,
                replyingTo,
                commentMessage,
                startReply,
                cancelReply,
                submitComment,
                isUserLoggedIn,
            }
        }
    }

</script>

<style scoped>
.main-img { width: 100%; height: 420px; object-fit: contain; }
.thumb-wrapper { display: flex; gap: 12px; overflow-x: auto; }
.thumb-img { width: 80px; height: 80px; object-fit: cover; cursor: pointer; border: 2px solid transparent; }
.thumb-img.active { border-color: #111827; }
.product-title { font-weight: 700; font-size: 1.8rem; }
.vendor-box { display: flex; align-items: center; gap: 10px; }
.vendor-logo { width: 50px; height: 50px; object-fit: cover; border-radius: 999px; }
.current-price { font-size: 1.3rem; font-weight: 700; margin-right: 8px; }
.old-price { text-decoration: line-through; color: #6b7280; }
.stock-badge.in-stock { color: #166534; }
.stock-badge.out-stock { color: #991b1b; }
.qty-control { display: inline-flex; gap: 8px; margin-right: 10px; }
.qty-control input { width: 70px; }
.star-picker { display: flex; gap: 6px; }
.star-btn { border: none; background: transparent; font-size: 24px; color: #9ca3af; }
.star-btn.active { color: #f59e0b; }
.comment-list { display: grid; gap: 12px; }
.related-card { border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
.related-img { width: 100%; height: 180px; object-fit: cover; }
</style>
