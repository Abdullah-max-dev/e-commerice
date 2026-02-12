import axios from 'axios'
export function normalizeProductImage(product) {
    let img = ''

    if (product.main_image?.image) {
        img = product.main_image.image
    } else if (product.p_image) {
        img = product.p_image
    } else {
        img = 'default-product.png'
    }

    // prepend uploads only if needed
    if (!img.startsWith('/') && !img.startsWith('http')) {
        img = `/uploads/products/${img}`
    }

    return img
}
export function normalizeProduct(product) {
    return {
        ...product,
        p_image: normalizeProductImage(product),
        final_price: product.final_price ?? product.p_price
    }
}

export function normalizeProductList(products) {
    return (products || []).map(normalizeProduct)
}


export default {
    namespaced: true,

    state() {
        return {
            products: [],
            currentProduct: null,
            error: null,
            success: null
        }
    },

    mutations: {
        SET_PRODUCTS(state, products) {
            state.products = products
        },

        ADD_PRODUCT(state, product) {
            state.products.unshift(product)
        },

        REMOVE_PRODUCT(state, productId) {
            state.products = state.products.filter(p => p.p_id !== productId)
        },

        SET_CURRENT_PRODUCT(state, product) {
            state.currentProduct = product
        },

        UPDATE_PRODUCT(state, updatedProduct) {
            const index = state.products.findIndex(
                p => p.p_id === updatedProduct.p_id
            )
            if (index !== -1) {
                state.products[index] = updatedProduct
            }
        },

        SET_ERROR(state, error) {
            state.error = error
        },

        SET_SUCCESS(state, msg) {
            state.success = msg
        },

        CLEAR_STATUS(state) {
            state.error = null
            state.success = null
        }
    },

    actions: {
        async addProduct({ commit, rootState }, form) {
            commit('CLEAR_STATUS')

            try {
                const formData = new FormData()

                formData.append('c_id', form.c_id)
                formData.append('p_name', form.p_name)
                formData.append('p_price', form.p_price)
                formData.append('p_stock', form.p_stock)
                formData.append('p_description', form.p_description)

                form.images.forEach((image, index) => {
                    formData.append(`images[${index}]`, image)
                })

                const res = await axios.post(
                    '/api/vender/add-product',
                    formData,
                    {
                        headers: {
                            Authorization: `Bearer ${rootState.token}`,
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                )

                commit('ADD_PRODUCT', res.data.product)
                commit('SET_SUCCESS', 'Product added successfully ✅')

                return true

            } catch (e) {
                commit(
                    'SET_ERROR',
                    e.response?.data?.message ||
                    Object.values(e.response?.data?.errors || {}).flat().join(', ') ||
                    'Something went wrong'
                )
                return false
            }
        },

        async fetchVendorProducts({ commit, rootState }) {
            try {
                const res = await axios.get('/api/vender/products', {
                    headers: {
                        Authorization: `Bearer ${rootState.token}`
                    }
                })

                commit('SET_PRODUCTS', res.data.products)
            } catch {
                commit('SET_ERROR', 'Failed to load products')
            }
        },

        async deleteProduct({ commit, rootState }, p_id) {
            try {
                await axios.delete(`/api/vender/products/${p_id}`, {
                    headers: {
                        Authorization: `Bearer ${rootState.token}`
                    }
                })

                commit('REMOVE_PRODUCT', p_id)
                commit('SET_SUCCESS', 'Product deleted successfully')
            } catch {
                commit('SET_ERROR', 'Failed to delete product')
            }
        },

        async fetchProduct({ commit, rootState }, id) {
            try {
                const res = await axios.get(`/api/vender/products/${id}`, {
                    headers: {
                        Authorization: `Bearer ${rootState.token}`
                    }
                })

                commit('SET_CURRENT_PRODUCT', res.data.product)
            } catch {
                commit('SET_ERROR', 'Failed to fetch product')
            }
        },

        async updateProduct({ commit, rootState }, { id, form }) {
            commit('CLEAR_STATUS')

            try {
                const formData = new FormData()

                // 1️⃣ NORMAL FIELDS
                Object.keys(form).forEach(key => {
                    if (
                        key !== 'images' &&
                        key !== 'remove_images' &&
                        form[key] !== null &&
                        form[key] !== '' &&
                        form[key] !== undefined
                    ) {
                        formData.append(key, form[key])
                    }
                })

                // 2️⃣ NEW IMAGES
                if (form.images && form.images.length) {
                    form.images.forEach((image, index) => {
                        formData.append(`images[${index}]`, image)
                    })
                }

                // 3️⃣ REMOVED IMAGES ✅ (THIS FIXES YOUR ERROR)
                if (form.remove_images && form.remove_images.length) {
                    form.remove_images.forEach(id => {
                        formData.append('remove_images[]', id)
                    })
                }

                const res = await axios.post(
                    `/api/vender/products/${id}`,
                    formData,
                    {
                        headers: {
                            Authorization: `Bearer ${rootState.token}`,
                            'Content-Type': 'multipart/form-data'
                        },
                        params: {
                            _method: 'PUT'
                        }
                    }
                )

                commit('UPDATE_PRODUCT', res.data.product)
                commit('SET_SUCCESS', 'Product updated successfully ✅')

                return true

            } catch (e) {
                commit(
                    'SET_ERROR',
                    e.response?.data?.message ||
                    Object.values(e.response?.data?.errors || {}).flat().join(', ') ||
                    'Update failed'
                )
                return false
            }
        },
        // discount
        async addDiscount({ commit, rootState }, { id, form }) {
            commit('CLEAR_STATUS')

            try {
                const res= await axios.post(
                    `/api/vender/products/${id}/discount`,
                    form,
                    {
                        headers: {
                            Authorization: `Bearer ${rootState.token}`
                        }
                    }
                )
                commit('UPDATE_PRODUCT', res.data.product)
                commit('SET_SUCCESS', 'Discount saved successfully ✅')
                return true

            } catch (e) {
                commit(
                    'SET_ERROR',
                    e.response?.data?.message ||
                    Object.values(e.response?.data?.errors || {}).flat().join(', ') ||
                    'Failed to save discount'
                )
                return false
            }
        }




    },

    getters: {
        products: state =>
             normalizeProductList(state.products),

        currentProduct: state =>
            state.currentProduct
                ?  normalizeProduct(state.currentProduct)
                :null,

        topDeals: (state, getters) =>
            getters.products.filter(p => p.is_top_deal === 1),

        popularProducts: (state, getters) =>
            getters.products.filter(p => p.is_popular === 1),

        error: state => state.error,
        success: state => state.success
    }


}
