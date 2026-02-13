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
          <div class="card-header"><h5 class="mb-0">Users Verification</h5></div>
          <div class="card-body table-responsive">
            <table class="table table-striped align-middle">
              <thead class="table-dark">
                <tr>
                  <th>ID</th><th>Name</th><th>Email</th><th>Status</th><th>Details</th><th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="u in users" :key="u.id">
                  <td>{{ u.id }}</td>
                  <td>{{ u.name }}</td>
                  <td>{{ u.email }}</td>
                  <td><span class="badge text-uppercase" :class="statusBadgeClass(u.verification_status)">{{ u.verification_status }}</span></td>
                  <td><small>{{ formatDetails(u.verification_data) }}</small></td>
                  <td>
                    <button class="btn btn-sm btn-success me-1" @click="updateStatus('users', u.id, 'verified')">Approve</button>
                    <button class="btn btn-sm btn-danger" @click="updateStatus('users', u.id, 'rejected')">Reject</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="card shadow mt-5 mb-5">
          <div class="card-header"><h5 class="mb-0">Vendors Verification</h5></div>
          <div class="card-body table-responsive">
            <table class="table table-striped align-middle">
              <thead class="table-dark">
                <tr>
                  <th>ID</th><th>Name</th><th>Email</th><th>Status</th><th>Details</th><th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="v in vendors" :key="v.id">
                  <td>{{ v.id }}</td>
                  <td>{{ v.name }}</td>
                  <td>{{ v.email }}</td>
                  <td><span class="badge text-uppercase" :class="statusBadgeClass(v.verification_status)">{{ v.verification_status }}</span></td>
                  <td><small>{{ formatDetails(v.verification_data) }}</small></td>
                  <td>
                    <button class="btn btn-sm btn-success me-1" @click="updateStatus('vendors', v.id, 'verified')">Approve</button>
                    <button class="btn btn-sm btn-danger" @click="updateStatus('vendors', v.id, 'rejected')">Reject</button>
                  </td>
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
import AdminMainLayout from './layouts/AdminMainLayout.vue'

export default {
  components: { AdminMainLayout },
  data() {
    return { users: [], vendors: [] }
  },
  methods: {
    authHeader() {
      return { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` } }
    },
    async fetchData() {
      const [usersRes, vendorRes] = await Promise.all([
        axios.get('/api/admin/users', this.authHeader()),
        axios.get('/api/admin/vendors', this.authHeader()),
      ])
      this.users = usersRes.data.data
      this.vendors = vendorRes.data.data
    },
    async updateStatus(type, id, status) {
      await axios.patch(`/api/admin/${type}/${id}/verification`, { verification_status: status }, this.authHeader())
      await this.fetchData()
    },
    statusBadgeClass(status) {
      const map = { unverified: 'bg-warning', pending: 'bg-info', verified: 'bg-success', rejected: 'bg-danger' }
      return map[status] || 'bg-secondary'
    },
    formatDetails(data) {
      if (!data) return '-'
      return Object.entries(data).map(([k, v]) => `${k}: ${v}`).join(' | ')
    },
  },
  mounted() {
    this.fetchData()
  },
}
</script>
