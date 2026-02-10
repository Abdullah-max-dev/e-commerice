<template>
    <VenderMain>
        <div id="layoutSidenav_content">
            <main class="container-fluid px-4">
                <div class="card p-3 mt-3">
                    <h5>Add Product</h5>

                    <!-- SUCCESS -->
                    <div v-if="success" class="alert alert-success">
                        {{ success }}
                    </div>

                    <!-- ERROR -->
                    <div v-if="error" class="alert alert-danger">
                        {{ error }}
                    </div>

                    <form @submit.prevent="saveProduct">


                            <div class="col-xl-8">
                                <div class="row">

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Product Name</label>
                                        <input type="text" class="form-control p-3" v-model="form.p_name" />
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Price</label>
                                        <input type="number" class="form-control p-3" v-model="form.p_price" />
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Category</label>
                                        <select class="form-select p-3" v-model="form.c_id">
                                        <option disabled value="">Select Category</option>
                                        <option
                                            v-for="cat in categories"
                                            :key="cat.c_id"
                                            :value="cat.c_id"
                                        >
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


                            <div class="col-xl-12">
                                <div class="row g-2">

                                    <div
                                    v-for="(img, index) in imagePreviews"
                                    :key="index"
                                    class="col-4"
                                    >
                                    <div class="preview-box position-relative">
                                        <img :src="img" class="preview-img" />

                                        <button
                                        type="button"
                                        class="btn btn-danger btn-sm remove-btn"
                                        @click="removeImage(index)"
                                        >
                                        ✕
                                        </button>
                                    </div>
                                </div>

                            </div>

                            <input
                                type="file"
                                class="form-control mt-3"
                                multiple
                                accept="image/*"
                                @change="handleImages"
                            />




                            <div class="col-12">
                                <button class="btn btn-primary w-100 mt-3">
                                Save Product
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </main>
        </div>
    </VenderMain>
</template>
<script>
import { reactive, ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import VenderMain from './layouts/VenderMain.vue'

export default {
  components: { VenderMain },

  setup() {
    const store = useStore()

    /* ---------------- FORM ---------------- */
    const form = reactive({
      c_id: '',
      p_name: '',
      p_price: '',
      p_stock: '',
      p_description: '',
      images: [] // MULTIPLE IMAGES
    })

    /* ---------------- PREVIEWS ---------------- */
    const imagePreviews = ref([])

    const handleImages = (e) => {
      const files = Array.from(e.target.files)

      files.forEach(file => {
        form.images.push(file)
        imagePreviews.value.push(URL.createObjectURL(file))
      })

      e.target.value = null
    }

    const removeImage = (index) => {
      form.images.splice(index, 1)
      imagePreviews.value.splice(index, 1)
    }

    /* ---------------- STORE STATE ---------------- */
    const categories = computed(() =>
      store.getters['category/categories']
    )

    const error = computed(() =>
      store.getters['product/error']
    )

    const success = computed(() =>
      store.getters['product/success']
    )

    /* ---------------- FETCH DATA ---------------- */
    onMounted(() => {
      store.dispatch('category/fetchCategories')
    })

    /* ---------------- SUBMIT ---------------- */
    const saveProduct = async () => {
      if (
        !form.c_id ||
        !form.p_name ||
        !form.p_price ||
        !form.p_stock ||
        !form.p_description ||
        !form.images.length
      ) {
        alert('All fields are required')
        return
      }

      const ok = await store.dispatch('product/addProduct', form)

      if (ok) {
        // reset form
        Object.keys(form).forEach(key => {
          if (Array.isArray(form[key])) form[key] = []
          else form[key] = ''
        })
        imagePreviews.value = []
      }
    }

    /* ---------------- EXPORT ---------------- */
    return {
      form,
      categories,
      error,
      success,
      handleImages,
      removeImage,
      imagePreviews,
      saveProduct
    }
  }
}
</script>


<style scoped>
.preview-img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #ddd;
}

</style>
