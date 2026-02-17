<template>
  <AdminMainLayout>
    <div id="layoutSidenav_content">
      <main class="container-fluid px-4">
        <h1 class="mt-4">Admin Dashboard</h1>
        <p class="text-muted">Users & Vendors Management</p>

        <div class="row g-4 mt-3">
          <div class="col-xl-6 col-md-6">
            <div class="card bg-primary text-white shadow">
              <div class="card-body text-center">
                <h6>Total Users</h6>
                <h2>{{ users.length }}</h2>
              </div>
            </div>
          </div>
          <div class="col-xl-6 col-md-6">
            <div class="card bg-success text-white shadow">
              <div class="card-body text-center">
                <h6>Total Vendors</h6>
                <h2>{{ vendors.length }}</h2>
              </div>
            </div>
          </div>
        </div>

        <div class="card shadow mt-5">
          <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0">Verifications</h5>
            <div class="d-flex align-items-center gap-3">
              <div class="btn-group" role="group" aria-label="verification type">
                <button class="btn btn-sm" :class="activeType === 'users' ? 'btn-primary' : 'btn-outline-primary'" @click="activeType = 'users'">Users</button>
                <button class="btn btn-sm" :class="activeType === 'vendors' ? 'btn-primary' : 'btn-outline-primary'" @click="activeType = 'vendors'">Vendors</button>
              </div>
              <div class="form-check m-0">
                <input id="pendingOnly" v-model="pendingOnly" class="form-check-input" type="checkbox">
                <label for="pendingOnly" class="form-check-label">Pending only</label>
              </div>
            </div>
          </div>

          <div class="card-body table-responsive">
            <table class="table table-striped align-middle">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Status</th>
                  <th>Submitted</th>
                  <th>Reviewed</th>
                  <th>Details</th>
                  <th>Note</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in filteredRecords" :key="`${activeType}-${item.id}`">
                  <td>{{ item.id }}</td>
                  <td>{{ item.name }}</td>
                  <td>{{ item.email }}</td>
                  <td><span class="badge text-uppercase" :class="statusBadgeClass(item.verification_status)">{{ item.verification_status }}</span></td>
                  <td>{{ formatDate(item.verification_submitted_at) }}</td>
                  <td>{{ formatDate(item.verification_reviewed_at) }}</td>
                  <td><small>{{ formatDetails(item.verification_data) }}</small></td>
                  <td><small>{{ item.verification_note || '-' }}</small></td>
                  <td>
                    <button class="btn btn-sm btn-success me-1" :disabled="item.verification_status === 'verified'" @click="updateStatus(activeType, item.id, 'verified')">Approve</button>
                    <button class="btn btn-sm btn-danger" @click="updateStatus(activeType, item.id, 'rejected')">Reject</button>
                  </td>
                </tr>
                <tr v-if="!filteredRecords.length">
                  <td colspan="9" class="text-center text-muted">No verification records found.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </AdminMainLayout>
</template>

<script>
import axios from 'axios'
import { ref, computed, onMounted } from 'vue'
import AdminMainLayout from './layouts/AdminMainLayout.vue'

export default {
  components: { AdminMainLayout },
  setup() {
    const users = ref([])
    const vendors = ref([])
    const activeType = ref('users')
    const pendingOnly = ref(true)

    const authHeader = () => ({
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
    })

    const fetchData = async () => {
      const [usersRes, vendorRes] = await Promise.all([
        axios.get('/api/admin/users', authHeader()),
        axios.get('/api/admin/vendors', authHeader())
      ])
      users.value = usersRes.data.data
      vendors.value = vendorRes.data.data
    }

    const updateStatus = async (type, id, status) => {
      const payload = { verification_status: status }
      if (status === 'rejected') {
        payload.verification_note = window.prompt('Add rejection note (optional):') || null
      }
      await axios.patch(`/api/admin/${type}/${id}/verification`, payload, authHeader())
      await fetchData()
    }

    const statusBadgeClass = status => {
      const map = { unverified: 'bg-warning', pending: 'bg-info', verified: 'bg-success', rejected: 'bg-danger' }
      return map[status] || 'bg-secondary'
    }

    const formatDetails = data => {
      if (!data) return '-'
      return Object.entries(data).map(([k, v]) => `${k}: ${v}`).join(' | ')
    }

    const formatDate = value => {
      if (!value) return '-'
      return new Date(value).toLocaleString()
    }

    const filteredRecords = computed(() => {
      const source = activeType.value === 'users' ? users.value : vendors.value
      return pendingOnly.value ? source.filter(item => item.verification_status === 'pending') : source
    })

    onMounted(() => {
      fetchData()
    })

    return { users, vendors, activeType, pendingOnly, filteredRecords, updateStatus, statusBadgeClass, formatDetails, formatDate }
  }
}
</script>
