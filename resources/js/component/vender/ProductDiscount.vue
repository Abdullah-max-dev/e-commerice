<template>
  <VenderMain>
    <div class="container mt-4">
      <div class="card p-4">

        <h5 class="mb-3">Add Discount</h5>

        <!-- SUCCESS MESSAGE -->
        <div v-if="success" class="alert alert-success">
          {{ success }}
        </div>

        <!-- ERROR MESSAGE -->
        <div v-if="error" class="alert alert-danger">
          {{ error }}
        </div>

        <form @submit.prevent="saveDiscount">

          <div class="mb-3">
            <label class="form-label">Discount Type</label>
            <select v-model="form.type" class="form-select">
              <option value="percentage">Percentage (%)</option>
              <option value="fixed">Fixed Amount</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Value</label>
            <input
              type="number"
              v-model="form.value"
              class="form-control"
              min="1"
              required
            >
          </div>

          <div class="mb-3">
            <label class="form-label">Start Date</label>
            <input
              type="date"
              v-model="form.starts_at"
              class="form-control"
            >
          </div>

          <div class="mb-3">
            <label class="form-label">End Date</label>
            <input
              type="date"
              v-model="form.ends_at"
              class="form-control"
            >
          </div>

          <button class="btn btn-primary w-100">
            Save Discount
          </button>

        </form>
      </div>
    </div>
  </VenderMain>
</template>

<script>
import { reactive, computed, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { useStore } from 'vuex'
import VenderMain from './layouts/VenderMain.vue'

export default {
  components: {
    VenderMain
  },

  setup() {
    const route = useRoute()
    const store = useStore()

    const form = reactive({
      type: 'percentage',
      value: '',
      starts_at: '',
      ends_at: '',
    })

    const success = computed(() =>
      store.getters['product/success']
    )

    const error = computed(() =>
      store.getters['product/error']
    )

    const saveDiscount = async () => {
      await store.dispatch(
        'product/addDiscount',
        {
          id: route.params.id,
          form
        }
      )
    }

    // Clear messages when leaving page
    onUnmounted(() => {
      store.commit('product/CLEAR_STATUS')
    })

    return {
      form,
      saveDiscount,
      success,
      error
    }
  }
}
</script>
