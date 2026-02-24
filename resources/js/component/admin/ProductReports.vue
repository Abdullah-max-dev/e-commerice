<template>
  <AdminMainLayout>
    <div class="container py-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Product Reports</h3>
        <select class="form-select w-auto" v-model="status" @change="fetchReports">
          <option value="">All</option>
          <option value="pending">Pending</option>
          <option value="resolved">Resolved</option>
          <option value="rejected">Rejected</option>
        </select>
      </div>

      <div class="table-responsive bg-white p-3 rounded shadow-sm">
        <table class="table align-middle">
          <thead>
            <tr><th>ID</th><th>Product</th><th>User</th><th>Vendor</th><th>Reason</th><th>Vendor Justification</th><th>Status</th><th>Admin Note</th><th>Action</th></tr>
          </thead>
          <tbody>
            <tr v-for="r in reports" :key="r.id">
              <td>{{ r.id }}</td><td>{{ r.product?.p_name }}</td><td>{{ r.user?.name }}</td><td>{{ r.vendor?.name }}</td>
              <td>{{ r.reason }}</td><td>{{ r.vendor_justification || '-' }}</td><td>{{ r.status }}</td><td>{{ r.admin_note || '-' }}</td>
              <td><button class="btn btn-sm btn-primary" @click="openEditor(r)">Update</button></td>
            </tr>
            <tr v-if="!reports.length"><td colspan="9" class="text-center text-muted">No reports found.</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="selected" class="modal-backdrop-custom">
      <div class="modal-card">
        <h5>Update Report #{{ selected.id }}</h5>
        <select class="form-select mt-3" v-model="form.status"><option value="resolved">Resolved</option><option value="rejected">Rejected</option></select>
        <textarea class="form-control my-3" rows="4" v-model="form.admin_note" placeholder="Admin note"></textarea>
        <div class="form-check mb-3">
          <input id="deactivate" class="form-check-input" type="checkbox" v-model="form.deactivate_product" />
          <label class="form-check-label" for="deactivate">Deactivate product if resolved</label>
        </div>
        <p v-if="message" class="small text-muted">{{ message }}</p>
        <div class="d-flex justify-content-end gap-2"><button class="btn btn-light" @click="selected=null">Cancel</button><button class="btn btn-success" @click="save">Save</button></div>
      </div>
    </div>
  </AdminMainLayout>
</template>

<script>
import axios from 'axios'
import { onMounted, reactive, ref } from 'vue'
import AdminMainLayout from './layouts/AdminMainLayout.vue'

export default {
  components: { AdminMainLayout },
  setup() {
    const reports = ref([])
    const status = ref('')
    const selected = ref(null)
    const message = ref('')
    const form = reactive({ status: 'resolved', admin_note: '', deactivate_product: false })
    const headers = () => ({ Authorization: `Bearer ${localStorage.getItem('token')}` })

    const fetchReports = async () => {
      const { data } = await axios.get('/api/admin/reports', { params: { status: status.value || undefined }, headers: headers() })
      reports.value = data.data || []
    }

    const openEditor = r => {
      selected.value = r
      form.status = r.status === 'rejected' ? 'rejected' : 'resolved'
      form.admin_note = r.admin_note || ''
      form.deactivate_product = false
      message.value = ''
    }

    const save = async () => {
      try {
        const { data } = await axios.put(`/api/admin/reports/${selected.value.id}`, form, { headers: headers() })
        message.value = data.message || 'Updated.'
        await fetchReports()
      } catch (error) {
        message.value = error.response?.data?.message || 'Failed to update report.'
      }
    }

    onMounted(fetchReports)
    return { reports, status, selected, form, message, fetchReports, openEditor, save }
  },
}
</script>

<style scoped>
.modal-backdrop-custom { position: fixed; inset: 0; background: rgba(0,0,0,.5); display: grid; place-items: center; }
.modal-card { background: #fff; width: min(620px, 92vw); border-radius: 10px; padding: 20px; }
</style>
