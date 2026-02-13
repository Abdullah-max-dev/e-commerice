<template>
  <UserMain>
    <div class="container mt-4">
      <h4>User Verification Settings</h4>

      <div class="alert mt-3" :class="statusClass">
        {{ statusMessage }}
      </div>

      <form class="card p-3 mt-3" @submit.prevent="submitVerification">
        <h5>Profile Verification Form</h5>
        <div class="row mt-3">
          <div class="col-md-6 mb-3">
            <label class="form-label">Phone</label>
            <input v-model="form.phone" type="text" class="form-control" />
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">National ID</label>
            <input v-model="form.national_id" type="text" class="form-control" />
          </div>
          <div class="col-md-12 mb-3">
            <label class="form-label">Address</label>
            <textarea v-model="form.address" class="form-control"></textarea>
          </div>
          <div class="col-md-12 mb-3">
            <label class="form-label">Document URL</label>
            <input v-model="form.document_url" type="url" class="form-control" placeholder="https://..." />
          </div>
        </div>

        <div v-if="errors.length" class="alert alert-danger">
          <ul class="mb-0">
            <li v-for="(err, i) in errors" :key="i">{{ err }}</li>
          </ul>
        </div>

        <button class="btn btn-primary" type="submit">Submit For Verification</button>
      </form>
    </div>
  </UserMain>
</template>

<script>
import axios from 'axios'
import UserMain from './layouts/UserMain.vue'
import { computed, reactive, ref } from 'vue'
import { useStore } from 'vuex'

export default {
  components: { UserMain },
  setup() {
    const store = useStore()
    const errors = ref([])
    const form = reactive({
      phone: '',
      national_id: '',
      address: '',
      document_url: '',
    })

    const verificationStatus = computed(() => store.state.verification_status)
    const statusMessage = computed(() => {
      const map = {
        unverified: 'Please complete your profile to get verified.',
        pending: 'Your verification request is pending admin review.',
        verified: 'Your account is verified.',
        rejected: 'Your verification was rejected. Please update details and re-submit.',
      }
      return map[verificationStatus.value] || map.unverified
    })

    const statusClass = computed(() => {
      const map = {
        unverified: 'alert-warning',
        pending: 'alert-info',
        verified: 'alert-success',
        rejected: 'alert-danger',
      }
      return map[verificationStatus.value] || 'alert-warning'
    })

    const submitVerification = async () => {
      errors.value = []
      try {
        const token = localStorage.getItem('token')
        const res = await axios.post('/api/verification/submit', form, {
          headers: { Authorization: `Bearer ${token}` },
        })
        store.dispatch('setVerificationStatus', res.data.data.verification_status)
      } catch (e) {
        if (e.response?.data?.message) {
          errors.value = Object.values(e.response.data.message).flat()
        } else {
          errors.value = ['Failed to submit verification form.']
        }
      }
    }

    return { form, errors, submitVerification, statusMessage, statusClass }
  },
}
</script>
