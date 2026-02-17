<template>
    <VenderMain>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="my-4">Dashboard</h1>

                    <div v-if="showVerificationPrompt" :class="statusClass">{{ statusMessage }}</div> <div class="row">
                        <div class="col-xl-4 col-md-4 my-4">
                            <div class="card bg-secondary text-dark    ">
                                <div class="card-body mx-auto my-4">
                                    <h5>Total Orders</h5>
                                </div>
                                <div class='text-center  mb-2'><h2>15</h2></div>


                            </div>
                        </div>

                        <div class="col-xl-4 col-md-4 my-4">
                            <div class="card bg-success text-dark    ">
                                <div class="card-body mx-auto my-4">
                                    <h5>Total Sale</h5>
                                </div>
                                <div class='text-center  mb-2'><h2>100000 PKR</h2></div>


                            </div>
                        </div>

                        <div class="col-xl-4 col-md-4 my-4">
                            <div class="card bg-warning text-dark    ">
                                <div class="card-body mx-auto my-4">
                                    <h5>Pending Order</h5>
                                </div>
                                <div class='text-center  mb-2'><h2>5</h2></div>


                            </div>
                        </div>
                    </div>
                    <div class='row'>
                        <div class="col-xl-12 col-md-6">
                            <h4>Recent Order</h4>
                            <div>
                                <table class="table">
                                    <thead>
                                        <tr>
                                        <th scope="col">Order ID</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                        <th scope="row">1</th>
                                        <td>Mark</td>
                                        <td>Otto</td>
                                        <td>@mdo</td>
                                        </tr>

                                    </tbody>
                                    </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </VenderMain>
</template>

<script>
import axios from 'axios'
    import { computed, onMounted } from 'vue'
    import { useStore } from 'vuex'
    import VenderMain from './layouts/VenderMain.vue';
    export default {
        components: {
            VenderMain
        },
        setup() {
            const store = useStore()
            const verificationStatus = computed(() => store.state.verification_status)
            const statusMessage = computed(() => {
                const map = {
                    unverified: 'Please complete your information to get verified.',
                    pending: 'Your verification request is pending admin review.',
                    verified: 'Your account is verified.',
                    rejected: 'Verification was rejected. Please update settings and resubmit.'
                }
                return map[verificationStatus.value] || map.unverified
            })
            const statusClass = computed(() => {
                const map = { unverified: 'alert-warning', pending: 'alert-info', verified: 'alert-success', rejected: 'alert-danger' }
                return `alert ${map[verificationStatus.value] || 'alert-warning'}`
            })

           const showVerificationPrompt = computed(() => verificationStatus.value !== 'verified')

            const syncVerificationStatus = async () => {
                const token = localStorage.getItem('token')
                if (!token) return
                const res = await axios.get('/api/me', { headers: { Authorization: `Bearer ${token}` } })
                store.dispatch('setVerificationStatus', res.data.data.verification_status || 'unverified')
            }

            onMounted(() => {
                syncVerificationStatus()
            })

            return { statusMessage, statusClass, showVerificationPrompt }
        }
        }
    
</script>
