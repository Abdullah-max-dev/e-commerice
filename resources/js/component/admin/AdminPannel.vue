<template>
    <AdminMainLayout>
        <section class="dashboard-page">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                <div>
                <h2 class="dashboard-title mb-1">Welcome back, Admin 👋</h2>
                <p class="text-muted mb-0">Monitor platform growth, verification progress, and vendor activity in real time.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill text-bg-light border">Auto refresh: 30s</span>
                <span class="badge rounded-pill" :class="dashboardError ? 'text-bg-danger' : 'text-bg-success'">
                    {{ dashboardError ? 'Sync issue' : 'Live data' }}
                </span>
                </div>
            </div>
            <div class="row g-3 mb-4">
        <div v-for="card in summaryCards" :key="card.title" class="col-12 col-sm-6 col-xl-3">
          <article class="metric-card h-100" :style="{ '--accent': card.accent }">
            <div>
              <p class="metric-label">{{ card.title }}</p>
              <h3 class="metric-value">{{ card.value }}</h3>
              <small class="text-muted">{{ card.note }}</small>
            </div>
            <div class="metric-icon">{{ card.icon }}</div>
          </article>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-12 col-xl-7">
          <div class="surface-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0">Verification Overview</h5>
              <small class="text-muted">{{ lastUpdatedLabel }}</small>
            </div>

            <div v-if="dashboardLoading" class="text-muted">Loading chart data...</div>
            <div v-else class="chart-grid">
              <div v-for="item in verificationData" :key="item.label" class="chart-item">
                <div class="d-flex justify-content-between mb-2">
                  <strong>{{ item.label }}</strong>
                  <span class="text-muted">{{ item.percentage }}%</span>
                </div>
                <div class="progress custom-progress mb-2">
                  <div class="progress-bar" role="progressbar" :style="{ width: `${item.percentage}%` }" />
                </div>
                <div class="d-flex justify-content-between small text-muted">
                  <span>Verified: {{ item.verified }}</span>
                  <span>Pending: {{ item.pending }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-xl-5">
          <div class="surface-card h-100">
            <h5 class="mb-3">Newest Venders</h5>
            <div class="table-responsive">
              <table class="table align-middle modern-table mb-0">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Joined</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="vender in newestVenders" :key="vender.id">
                    <td>
                      <strong>{{ vender.name }}</strong>
                      <div class="small text-muted">{{ vender.email }}</div>
                    </td>
                    <td>
                      <span class="badge text-uppercase" :class="statusClass(vender.verification_status)">
                        {{ vender.verification_status }}
                      </span>
                    </td>
                    <td class="small text-muted">{{ formatDate(vender.created_at) }}</td>
                  </tr>
                  <tr v-if="!newestVenders.length && !dashboardLoading">
                    <td colspan="3" class="text-center text-muted py-4">No vender data available.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <p v-if="dashboardError" class="text-danger mt-3 mb-0">{{ dashboardError }}</p>
    </section>


    </AdminMainLayout>
</template>

<script>

import { computed, onMounted, onUnmounted } from 'vue'
import { useStore } from 'vuex'
import AdminMainLayout from './layouts/AdminMainLayout.vue'

export default {
  components: { AdminMainLayout },
  setup() {
    const store = useStore()
    let refreshTimer = null

    const dashboardLoading = computed(() => store.state.adminDashboard.loading)
    const dashboardError = computed(() => store.state.adminDashboard.error)
    const lastUpdated = computed(() => store.state.adminDashboard.lastUpdated)
    const totals = computed(() => store.getters['adminDashboard/totals'])
    const verificationData = computed(() => store.getters['adminDashboard/verificationData'])
    const newestVenders = computed(() => store.getters['adminDashboard/newestVenders'])

    const summaryCards = computed(() => [
      {
        title: 'Total Users',
        value: totals.value.users,
        note: `${totals.value.verifiedUsers} verified accounts`,
        icon: '👤',
        accent: '#4f46e5',
      },
      {
        title: 'Total Venders',
        value: totals.value.venders,
        note: `${totals.value.pendingVenders} pending approvals`,
        icon: '🏬',
        accent: '#0ea5e9',
      },
      {
        title: 'Categories',
        value: totals.value.categories,
        note: `${totals.value.activeCategories} highlighted categories`,
        icon: '🗂️',
        accent: '#f59e0b',
      },
      {
        title: 'Pending Reviews',
        value: totals.value.pendingUsers + totals.value.pendingVenders,
        note: 'Needs verification attention',
        icon: '⏳',
        accent: '#ec4899',
      },
    ])

    const statusClass = status => {
      const map = {
        verified: 'text-bg-success',
        pending: 'text-bg-warning',
        rejected: 'text-bg-danger',
        unverified: 'text-bg-secondary',
      }
      return map[status] || 'text-bg-secondary'
    }

    const formatDate = value => {
      if (!value) return '-'
      return new Date(value).toLocaleDateString()
    }

    const lastUpdatedLabel = computed(() => {
      if (!lastUpdated.value) return 'Waiting for first sync...'
      return `Updated ${new Date(lastUpdated.value).toLocaleTimeString()}`
    })

    onMounted(async () => {
      await store.dispatch('adminDashboard/fetchDashboardData')
      refreshTimer = store.dispatch('adminDashboard/startAutoRefresh')
    })

    onUnmounted(() => {
      if (refreshTimer) {
        clearInterval(refreshTimer)
      }
    })

    return {
      dashboardLoading,
      dashboardError,
      newestVenders,
      verificationData,
      summaryCards,
      statusClass,
      formatDate,
      lastUpdatedLabel,
    }
  },
}
</script>

<style scoped>
.dashboard-page {
  animation: fadeIn 0.35s ease;
}

.dashboard-title {
  font-weight: 700;
  color: #111827;
}

.metric-card {
  background: linear-gradient(145deg, #ffffff, #f9fafb);
  border: 1px solid rgba(15, 23, 42, 0.08);
  border-radius: 1rem;
  padding: 1.1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 12px 22px rgba(15, 23, 42, 0.06);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  border-left: 4px solid var(--accent);
}

.metric-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 18px 30px rgba(15, 23, 42, 0.1);
}

.metric-label {
  margin-bottom: 0.2rem;
  color: #64748b;
  font-size: 0.82rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

.metric-value {
  margin-bottom: 0.2rem;
  font-size: 1.8rem;
  font-weight: 700;
  color: #0f172a;
}

.metric-icon {
  font-size: 1.7rem;
  background: rgba(15, 23, 42, 0.04);
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: grid;
  place-items: center;
}

.surface-card {
  background: #fff;
  border-radius: 1rem;
  border: 1px solid rgba(15, 23, 42, 0.08);
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
  padding: 1rem;
}

.chart-grid {
  display: grid;
  gap: 1rem;
}

.chart-item {
  padding: 0.75rem;
  border-radius: 0.85rem;
  border: 1px solid rgba(15, 23, 42, 0.08);
  background: #fcfdff;
}

.custom-progress {
  height: 10px;
  border-radius: 10px;
  background-color: #e2e8f0;
}

.custom-progress .progress-bar {
  background: linear-gradient(90deg, #2563eb, #06b6d4);
  border-radius: 10px;
  transition: width 0.6s ease;
}

.modern-table thead th {
  color: #475569;
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-bottom: 1px solid #e2e8f0;
}

.modern-table tbody td {
  border-color: #f1f5f9;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 576px) {
  .metric-value {
    font-size: 1.5rem;
  }
}
</style>
