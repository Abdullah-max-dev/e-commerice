<template>
    <AdminMainLayout>
    <div class="container mt-4">
        <h4 class="mb-3">Bussiness Type Management</h4>
        <div class="card mb-4">
        <div class="card-body">
          <form @submit.prevent="submitType">
            <div class="row g-2">
              <div class="col-md-8">
                <input
                  v-model="form.bussiness_type"
                  type="text"
                  class="form-control"
                  placeholder="Bussiness Type"
                />
              </div>

              <div class="col-md-4">
                <button class="btn btn-primary w-100">
                  {{ editId ? 'Update' : 'Add' }} Type
                </button>
              </div>
            </div>

            <p v-if="error" class="text-danger mt-2">{{ error }}</p>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-body table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Bussiness Type</th>
                <th width="180">Action</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="item in bussinessTypes" :key="item.id">
                <td>{{ item.id }}</td>
                <td>{{ item.bussiness_type }}</td>
                <td>
                  <button
                    class="btn btn-sm btn-warning me-1"
                    @click="editType(item)"
                  >
                    Edit
                  </button>

                  <button
                    class="btn btn-sm btn-danger"
                    @click="deleteType(item.id)"
                  >
                    Delete
                  </button>
                </td>
              </tr>

              <tr v-if="!bussinessTypes.length">
                <td colspan="3" class="text-center">No Bussiness Types</td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </AdminMainLayout>
</template>

<script>
import { computed, onMounted, reactive, ref } from 'vue'
import { useStore } from 'vuex'
import AdminMainLayout from './layouts/AdminMainLayout.vue'

export default {
  components: {
    AdminMainLayout
  },
  setup() {
    const store = useStore()

    const form = reactive({
      bussiness_type: ''
    })

    const editId = ref(null)
    const error = ref(null)

    const bussinessTypes = computed(() => store.getters['bussiness/types'] || [])

    onMounted(() => {
      store.dispatch('bussiness/fetchTypes')
    })

    const submitType = async () => {
      error.value = null

      if (!form.bussiness_type) {
        error.value = 'Bussiness type is required'
        return
      }

      try {
        if (editId.value) {
          await store.dispatch('bussiness/updateType', {
            id: editId.value,
            data: { bussiness_type: form.bussiness_type }
          })
        } else {
          await store.dispatch('bussiness/addType', {
            bussiness_type: form.bussiness_type
          })
        }

        form.bussiness_type = ''
        editId.value = null
      } catch (e) {
        error.value = e?.response?.data?.message || 'Unable to save bussiness type'
      }
    }

    const editType = (item) => {
      editId.value = item.id
      form.bussiness_type = item.bussiness_type
    }

    const deleteType = (id) => {
      if (confirm('Delete this bussiness type?')) {
        store.dispatch('bussiness/deleteType', id)
      }
    }

    return {
      form,
      editId,
      error,
      bussinessTypes,
      submitType,
      editType,
      deleteType
    }
  }
}
</script>
