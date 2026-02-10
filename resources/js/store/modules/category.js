import axios from 'axios'

export default {
  namespaced: true,

  state: () => ({
    categories: []
  }),

  mutations: {
    SET_CATEGORIES(state, categories) {
      state.categories = categories
    },

    ADD_CATEGORY(state, category) {
      state.categories.unshift(category)
    },

    UPDATE_CATEGORY(state, updated) {
      const index = state.categories.findIndex(
        c => c.c_id === updated.c_id
      )
      if (index !== -1) state.categories[index] = updated
    },

    DELETE_CATEGORY(state, c_id) {
      state.categories = state.categories.filter(c => c.c_id !== c_id)
    }
  },

  actions: {
    async fetchCategories({ commit }) {
      const res = await axios.get('/api/categories-list')
      commit('SET_CATEGORIES', res.data)
    },

    async addCategory({ commit }, data) {
      const res = await axios.post('/api/categories', data)
      commit('ADD_CATEGORY', res.data)
    },

    async updateCategory({ commit }, payload) {
      const res = await axios.put(
        `/api/categories/${payload.c_id}`,
        payload.data
      )
      commit('UPDATE_CATEGORY', res.data)
    },

    async deleteCategory({ commit }, c_id) {
      await axios.delete(`/api/categories/${c_id}`)
      commit('DELETE_CATEGORY', c_id)
    },

    async toggleTop({ dispatch }, c_id) {
      await axios.patch(`/api/categories/${c_id}/toggle-top`)
      dispatch('fetchCategories')
    }
  },

  getters: {
    categories: state => state.categories
  }
}
