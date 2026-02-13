import { createStore } from 'vuex'
import category from './modules/category'
import product from './modules/product'


const Store = createStore({
    modules: {
        category,
        product
    },
    state() {
        return {
            token: localStorage.getItem('token') || null,
            role: localStorage.getItem('role') || null,
            verification_status: localStorage.getItem('verification_status') || 'unverified'        }
    },

    mutations: {
        UPDATE_TOKEN(state, payload) {
            state.token = payload.token
            state.role  = payload.role
             state.verification_status = payload.verification_status || 'unverified'

            localStorage.setItem('token', payload.token)
            localStorage.setItem('role', payload.role)
            localStorage.setItem('verification_status', state.verification_status)
        },

        UPDATE_VERIFICATION_STATUS(state, status) {
            state.verification_status = status
            localStorage.setItem('verification_status', status)
        },

        CLEAR_AUTH(state) {
            state.token = null
            state.role  = null
            state.verification_status = 'unverified'

            localStorage.removeItem('token')
            localStorage.removeItem('role')
            localStorage.removeItem('verification_status')
        }
    },

    actions: {
        setToken({ commit }, payload) {
            commit('UPDATE_TOKEN', payload)
        },
        setVerificationStatus({ commit }, status) {
            commit('UPDATE_VERIFICATION_STATUS', status)
        },
        removeToken({ commit }) {
            commit('CLEAR_AUTH')
        }
    },

    getters: {
        isAdmin:  state => state.role === 'admin',
        isUser:   state => state.role === 'user',
        isVendor: state => state.role === 'vender',
        isLoggedIn: state => !!state.token,
        isVerified: state => state.verification_status === 'verified'
    }
})

export default Store
