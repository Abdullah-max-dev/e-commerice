<template>
  <UserMain>
    <div class="container-fluid px-4">
      <h1 class="my-4">Dashboard</h1>
         <div v-if="showVerificationPrompt" :class="statusClass">{{ statusMessage }}</div>
      <div class="row">
        <div class="col-xl-4 col-md-4 my-4">
          <div class="card bg-info text-white mb-4">
            <div class="card-body text-center">
              <img
                src="https://cdn-icons-png.flaticon.com/512/2922/2922510.png"
                style="width:150px; border-radius:50%;"
              />
              <div class="mt-2">Username</div>
            </div>
          </div>
        </div>

        <div class="col-xl-4 col-md-4 my-4">
          <div class="card bg-secondary text-white mb-4">
            <div class="card-body">
              <h5>Billing Address:</h5>
              <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-xl-12">
          <h4>Recent Orders</h4>
          <table class="table">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>Mark</td>
                <td>Otto</td>
                <td>Delivered</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </UserMain>
</template>

<script>
import axios from 'axios'
import { computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import UserMain from './layouts/UserMain.vue'

export default {
  components: { UserMain },


 setup() {
    const store = useStore()
    const verificationStatus = computed(() => store.state.verification_status)
    const statusMessage = computed(() => {
      const map = {
        unverified: 'Please complete your profile to get verified.',
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
