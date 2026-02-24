<template>
  <VenderMain>
    <div class="container py-4">
      <h3 class="mb-3">Reported Products</h3>
      <div class="table-responsive bg-white p-3 rounded shadow-sm">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>#</th><th>Product</th><th>User</th><th>Reason</th><th>Message</th><th>Status</th><th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="report in reports" :key="report.id">
              <td>{{ report.id }}</td>
              <td>{{ report.product?.p_name || '-' }}</td>
              <td>{{ report.user?.name || '-' }}</td>
              <td>{{ report.reason }}</td>
              <td>{{ report.message || '-' }}</td>
              <td><span class="badge bg-secondary text-capitalize">{{ report.status }}</span></td>
              <td><button class="btn btn-sm btn-primary" @click="openModal(report)">Justify</button></td>
            </tr>
            <tr v-if="!reports.length"><td colspan="7" class="text-center text-muted">No reports yet.</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="selected" class="modal-backdrop-custom">
      <div class="modal-card">
        <h5>Vendor Justification (Report #{{ selected.id }})</h5>
        <textarea v-model="vendorJustification" class="form-control my-3" rows="5" placeholder="Explain your response"></textarea>
        <p v-if="message" class="small text-muted">{{ message }}</p>
        <div class="d-flex justify-content-end gap-2">
          <button class="btn btn-light" @click="closeModal">Cancel</button>
          <button class="btn btn-success" @click="submitJustification">Submit</button>
        </div>
      </div>
    </div>
  </VenderMain>
</template>

<script>
import axios from 'axios'
import { onMounted, ref } from 'vue'
import VenderMain from './layouts/VenderMain.vue'

export default {
  components: { VenderMain },
  setup() {
    const reports = ref([])
    const selected = ref(null)
    const vendorJustification = ref('')
    const message = ref('')

    const authHeaders = () => ({ Authorization: `Bearer ${localStorage.getItem('token')}` })

    const fetchReports = async () => {
      const { data } = await axios.get('/api/vendor/reports', { headers: authHeaders() })
      reports.value = data.data || []
    }

    const openModal = report => {
      selected.value = report
      vendorJustification.value = report.vendor_justification || ''
      message.value = ''
    }

    const closeModal = () => {
      selected.value = null
      vendorJustification.value = ''
    }

    const submitJustification = async () => {
      if (!vendorJustification.value.trim()) {
        message.value = 'Justification is required.'
        return
      }

      try {
        const { data } = await axios.post(`/api/vendor/reports/${selected.value.id}/justify`, {
          vendor_justification: vendorJustification.value,
        }, { headers: authHeaders() })

        message.value = data.message || 'Saved.'
        await fetchReports()
      } catch (error) {
        message.value = error.response?.data?.message || 'Unable to submit justification.'
      }
    }

    onMounted(fetchReports)

    return { reports, selected, vendorJustification, message, openModal, closeModal, submitJustification }
  },
}
</script>

<style scoped>
.modal-backdrop-custom { position: fixed; inset: 0; background: rgba(0,0,0,.5); display: grid; place-items: center; }
.modal-card { background: #fff; width: min(620px, 92vw); border-radius: 10px; padding: 20px; }
</style>
