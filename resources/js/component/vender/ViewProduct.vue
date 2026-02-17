<template>
  <VenderMain>
    <div id="layoutSidenav_content">
      <main class="container-fluid px-4">

        <div class="card shadow-sm border-0 my-5">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">Products</h5>
            </div>

            <div class="card-body p-0">
                <table class="table table-hover table-bordered align-middle mb-0">

                <thead class="table-light">
                    <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Discount</th>
                    <th class="w-50">Description</th>
                    <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody v-if="products.length">
                    <tr v-for="p in products" :key="p.p_id">

                    <!-- PRODUCT IMAGE + NAME -->
                    <td>
                        <div class="d-flex align-items-center gap-3">

                        <!-- ✅ ONLY MAIN IMAGE (NO STATIC IMAGE) -->
                        <img
                            v-if="p.main_image && p.main_image.image"
                            :src="`/uploads/products/${p.main_image.image}`"
                            class="product-thumb"
                            alt="Product Image"

                        />

                        <span class="fw-medium">{{ p.p_name }}</span>
                        </div>
                    </td>

                    <!-- PRICE -->
                    <td class="fw-semibold text-success">
                        Rs {{ p.p_price }}
                    </td>

                    <!-- CATEGORY -->
                    <td>
                        {{ p.category?.c_name || '-' }}
                    </td>

                    <!-- STOCK -->
                    <td>
                        <span
                        class="badge"
                        :class="p.p_stock > 0 ? 'bg-success' : 'bg-danger'"
                        >
                        {{ p.p_stock > 0 ? 'In Stock' : 'Out of Stock' }}
                        </span>
                    </td>
                    <!-- Discount -->
                     <td>
                        <router-link
                            class="btn btn-outline-primary btn-sm"
                            :to="`/vender/products/${p.p_id}/discount`"
                        >
                            discount
                        </router-link>
                     </td>

                    <!-- DESCRIPTION -->
                    <td class="text-truncate" style="max-width: 300px;">
                        {{ p.p_description }}
                    </td>

                    <!-- ACTIONS -->
                    <td class="text-center">
                        <div class="btn-group" role="group">

                        <router-link
                            class="btn btn-outline-primary btn-sm"
                            :to="`/vender/products/${p.p_id}/edit`"
                        >
                            <i class="fa-regular fa-pen-to-square"></i>
                        </router-link>

                        <button
                            class="btn btn-outline-danger btn-sm"
                            @click="deleteItem(p.p_id)"
                        >
                            <i class="fa-solid fa-trash"></i>
                        </button>

                        </div>
                    </td>

                    </tr>
                </tbody>

                <!-- EMPTY STATE -->
                <tbody v-else>
                    <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        No products found
                    </td>
                    </tr>
                </tbody>

                </table>
            </div>
        </div>

      </main>
    </div>
  </VenderMain>
</template>




<script>
import { useStore } from 'vuex'
import { computed, onMounted } from 'vue'
import VenderMain from './layouts/VenderMain.vue'

export default {
  components: { VenderMain },

  setup() {
    const store = useStore()

    const products = computed(() =>
      store.getters['product/products']
    )

    onMounted(() => {
      store.dispatch('product/fetchVenderProducts')
    })

    const deleteItem = (id) => {
      if (confirm('Are you sure you want to delete this product?')) {
        store.dispatch('product/deleteProduct', id)
      }
    }

    return {
      products,
      deleteItem
    }
  }
}
</script>
<style>
    /* ===============================
   PRODUCT TABLE
================================ */

.table th h5 {
  font-size: 0.9rem;
  font-weight: 600;
  margin: 0;
}

.table td {
  vertical-align: middle;
  font-size: 0.85rem;
}

/* ===============================
   PRODUCT IMAGE (MAIN IMAGE ONLY)
================================ */

.product-thumb {
  width: 70px !important;
  height: 70px !important;
  object-fit: cover;        /* shows proper crop */
  border-radius: 10px;
  background: #f8f9fa;
  border: 1px solid #e0e0e0;
  padding: 4px;
}

/* Hover preview (table-safe) */
.product-thumb:hover {
  box-shadow: 0 6px 18px rgba(0,0,0,0.25);
  transform: scale(1.1);
  transition: 0.2s ease;
}

/* ===============================
   DESCRIPTION TRUNCATION
================================ */

.text-truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* ===============================
   ACTION BUTTONS
================================ */

.btn-sm {
  padding: 4px 10px;
  font-size: 0.75rem;
}


</style>
