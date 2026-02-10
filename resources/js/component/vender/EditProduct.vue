<template>
  <VenderMain>
    <div id="layoutSidenav_content">
      <main class="container-fluid px-4">
        <div class="card p-3 mt-3">
          <h5>Edit Product</h5>

          <div v-if="success" class="alert alert-success">{{ success }}</div>
          <div v-if="error" class="alert alert-danger">{{ error }}</div>

          <form @submit.prevent="saveProduct" class="product-form">


              <!-- LEFT -->
              <div class="col-xl-8">
                <div class="row">

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Product Name</label>
                    <input class="form-control p-3" v-model="form.p_name" />
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" class="form-control p-3" v-model="form.p_price" />
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Category</label>
                    <select class="form-select p-3" v-model.number="form.c_id">
                      <option disabled value="">Select Category</option>
                      <option v-for="cat in categories" :key="cat.c_id" :value="cat.c_id">
                        {{ cat.c_name }}
                      </option>
                    </select>
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Stock</label>
                    <input type="number" class="form-control p-3" v-model="form.p_stock" />
                  </div>

                  <div class="col-12 mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control p-3" v-model="form.p_description"></textarea>
                  </div>

                </div>
              </div>

              <!-- RIGHT -->
              <div class="col-xl-4">

                <!-- EXISTING IMAGES -->
                <div class="d-flex flex-wrap gap-2 ">
                    <div v-for="img in existingImages" :key="img.id" class="position-relative">
                        <img
                        :src="`/uploads/products/${img.image}`"
                        class="preview-img"
                        >

                        <button
                        @click="removeExistingImage(img.id)"
                        class="btn btn-danger btn-sm remove-btn"
                        >
                        ✕
                        </button>
                    </div>
                </div>


                <!-- NEW IMAGES -->
                <div class="d-flex flex-wrap gap-2 mb-3 ">
                  <div
                    v-for="(img, index) in newPreviews"
                    :key="index"
                    class="preview-img"
                  >
                    <img :src="img" class="preview-img" />
                    <button
                      type="button"
                      class="remove-btn"
                      @click="removeNewImage(index)"
                    >
                      ✕
                    </button>
                  </div>
                </div>

                <input
                  type="file"
                  class="form-control"
                  multiple
                  @change="handleImages"
                />
              </div>

              <div class="col-12">
                <button class="btn btn-primary w-100 mt-3">
                  Update Product
                </button>
              </div>

          </form>
        </div>
      </main>
    </div>
  </VenderMain>
</template>


<script>
import { reactive, ref, onMounted, computed } from 'vue'
import { useStore } from 'vuex'
import { useRoute, useRouter } from 'vue-router'
import VenderMain from './layouts/VenderMain.vue'

export default {
  components: { VenderMain },

  setup() {
    const store = useStore()
    const route = useRoute()
    const router = useRouter()

    const success = computed(() => store.getters['product/success'])
    const error = computed(() => store.getters['product/error'])
    const categories = computed(() => store.getters['category/categories'])

    const form = reactive({
      c_id: '',
      p_name: '',
      p_price: '',
      p_stock: '',
      p_description: '',
      images: [],
      remove_images: []
    })

    const existingImages = ref([])
    const newPreviews = ref([])

    onMounted(async () => {
      await store.dispatch('product/fetchProduct', route.params.id)
      await store.dispatch('category/fetchCategories')

      const p = store.getters['product/currentProduct']
      if (!p) return

      form.c_id = p.c_id
      form.p_name = p.p_name
      form.p_price = p.p_price
      form.p_stock = p.p_stock
      form.p_description = p.p_description

      existingImages.value = p.images || []
    })

    const handleImages = (e) => {
      const files = Array.from(e.target.files)

      files.forEach(file => {
        form.images.push(file)
        newPreviews.value.push(URL.createObjectURL(file))
      })

      e.target.value = null
    }

    const removeExistingImage = (id, index) => {
      form.remove_images.push(id)
      existingImages.value.splice(index, 1)
    }

    const removeNewImage = (index) => {
      form.images.splice(index, 1)
      newPreviews.value.splice(index, 1)
    }

    const saveProduct = async () => {
      const ok = await store.dispatch('product/updateProduct', {
        id: route.params.id,
        form
      })

      if (ok) {
        router.push('/vender/view-product')
      }
    }

    return {
      form,
      success,
      error,
      categories,
      existingImages,
      newPreviews,
      handleImages,
      removeExistingImage,
      removeNewImage,
      saveProduct
    }
  }
}
</script>

<style>
    .preview-img {
  width: 80px !important;
  height: 80px !important;
  object-fit: cover !important;
  border-radius: 8px !important;
  border: 1px solid #ddd;
}
</style>
