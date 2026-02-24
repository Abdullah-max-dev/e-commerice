import axios from 'axios'

const REFRESH_MS = 30000

export default {
  namespaced: true,

  state: () => ({
    users: [],
    venders: [],
    categories: [],
    loading: false,
    error: null,
    lastUpdated: null,
  }),

  mutations: {
    SET_LOADING(state, loading) {
      state.loading = loading
    },
    SET_ERROR(state, error) {
      state.error = error
    },
    SET_DATA(state, payload) {
      state.users = payload.users
      state.venders = payload.venders
      state.categories = payload.categories
      state.lastUpdated = new Date().toISOString()
    },
  },

  actions: {
    async fetchDashboardData({ commit, state }) {
      if (!state.lastUpdated) {
        commit('SET_LOADING', true)
      }
      commit('SET_ERROR', null)

      const token = localStorage.getItem('token')
      const config = token
        ? { headers: { Authorization: `Bearer ${token}` } }
        : {}
      const fetchCategories = async () => {
        try {
          return await axios.get('/api/categories', config)
        } catch (error) {
          if (error?.response?.status === 403) {
            return axios.get('/api/categories-list')
          }

          throw error
        }
      }
      try {
        const [usersRes, vendersRes, categoriesRes] = await Promise.all([
          axios.get('/api/admin/users', config),
          axios.get('/api/admin/venders', config),
          fetchCategories(),
        ])

        commit('SET_DATA', {
          users: usersRes.data?.data || [],
          venders: vendersRes.data?.data || [],
          categories: categoriesRes.data?.data || categoriesRes.data || [],
        })
      } catch (error) {
        commit('SET_ERROR', error?.response?.data?.message || 'Unable to load admin dashboard data.')
      } finally {
        commit('SET_LOADING', false)
      }
    },

    startAutoRefresh({ dispatch }) {
      return setInterval(() => {
        dispatch('fetchDashboardData')
      }, REFRESH_MS)
    },
  },

  getters: {
    totals: state => {
      const verifiedUsers = state.users.filter(item => item.verification_status === 'verified').length
      const pendingUsers = state.users.filter(item => item.verification_status === 'pending').length
      const verifiedVenders = state.venders.filter(item => item.verification_status === 'verified').length
      const pendingVenders = state.venders.filter(item => item.verification_status === 'pending').length
      const activeCategories = state.categories.filter(item => item.is_popular).length

      return {
        users: state.users.length,
        venders: state.venders.length,
        categories: state.categories.length,
        verifiedUsers,
        pendingUsers,
        verifiedVenders,
        pendingVenders,
        activeCategories,
      }
    },

    verificationData: (state, getters) => {
      const totals = getters.totals
      const userPercentage = totals.users ? Math.round((totals.verifiedUsers / totals.users) * 100) : 0
      const venderPercentage = totals.venders ? Math.round((totals.verifiedVenders / totals.venders) * 100) : 0

      return [
        { label: 'Users', verified: totals.verifiedUsers, pending: totals.pendingUsers, percentage: userPercentage },
        { label: 'Venders', verified: totals.verifiedVenders, pending: totals.pendingVenders, percentage: venderPercentage },
      ]
    },

    newestVenders: state => [...state.venders]
      .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
      .slice(0, 5),
  },
}
