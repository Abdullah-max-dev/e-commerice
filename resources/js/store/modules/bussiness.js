import axios from 'axios'

export default {
  namespaced: true,

  state: () => ({
    types: []
  }),

  mutations: {
    SET_TYPES(state, types) {
      state.types = types
    },

    ADD_TYPE(state, bussiness) {
      state.types.unshift(bussiness)
    },

    UPDATE_TYPE(state, updated) {
      const index = state.types.findIndex(item => item.id === updated.id)
      if (index !== -1) state.types[index] = updated
    },

     DELETE_TYPE(state, id) {
      state.types = state.types.filter(item => item.id !== id)
    }
  },

  actions: {
    async fetchTypes({ commit }) {
      const res = await axios.get('/api/bussiness-type')
      commit('SET_TYPES', res.data)
    },

    async addType({ commit }, data) {
      const res = await axios.post('/api/bussinesses', data)
      commit('ADD_TYPE', res.data)
    },

    async updateType({ commit }, payload) {
      const res = await axios.put(`/api/bussinesses/${payload.id}`, payload.data)
      commit('UPDATE_TYPE', res.data)
    },

    async deleteType({ commit }, id) {
      await axios.delete(`/api/bussinesses/${id}`)
      commit('DELETE_TYPE', id)
    }
  },

  getters: {
    types: state => state.types
  }
}
