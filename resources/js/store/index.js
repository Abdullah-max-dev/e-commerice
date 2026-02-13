import { createStore } from 'vuex'
import category from './modules/category'
import product from './modules/product'
import bussiness from './modules/bussiness'

const Store = createStore({
    modules: {
        category,
        product,
        bussiness
    },
    state() {
        return {
            token: localStorage.getItem('token') || null,
            role: localStorage.getItem('role') || null
        }
    },

    mutations: {
        UPDATE_TOKEN(state, payload) {
            state.token = payload.token
            state.role  = payload.role

            localStorage.setItem('token', payload.token)
            localStorage.setItem('role', payload.role)
        },

        CLEAR_AUTH(state) {
            state.token = null
            state.role  = null

            localStorage.removeItem('token')
            localStorage.removeItem('role')
        }
    },

    actions: {
        setToken({ commit }, payload) {
            // payload = { token, role }
            commit('UPDATE_TOKEN', payload)
        },

        removeToken({ commit }) {
            commit('CLEAR_AUTH')
        }
    },

    getters: {
        isAdmin:  state => state.role === 'admin',
        isUser:   state => state.role === 'user',
        isVendor: state => state.role === 'vender', // 🔥 MATCH SPELLING
        isLoggedIn: state => !!state.token
    }
})

export default Store
