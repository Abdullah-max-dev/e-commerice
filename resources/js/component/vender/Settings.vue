<template>



     <VenderMain>
    <div class="container mt-4">
      <h4>Vender Verification Settings</h4>

      <div class="alert mt-3" :class="statusClass">
        {{ statusMessage }}
      </div>

      <form class="card p-3 mt-3" @submit.prevent="submitVerification">
        <h5>Business Verification Form</h5>
        <div class="row mt-3">
          <div class="col-md-6 mb-3">
            <label class="form-label">Business Name</label>
            <input v-model="form.business_name" type="text" class="form-control" />
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Phone no</label>
            <input v-model="form.business_type" type="text" class="form-control" />
          </div>
          <div class="col-md-12 mb-3">
            <label class="form-label">Business Address</label>
            <textarea v-model="form.business_address" class="form-control"></textarea>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">National ID</label>
            <input v-model="form.tax_id" type="text" class="form-control" />
          </div>
          <div class="col-md-6 mb-3">
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

 </VenderMain>
</template>
<script>
import axios from 'axios'
import VenderMain from './layouts/VenderMain.vue'
import { computed, reactive, ref } from 'vue'
import { useStore } from 'vuex'

export default {
  components: { VenderMain },
  setup() {
    const store = useStore()
    const errors = ref([])
    const form = reactive({
      business_name: '',
      business_type: '',
      business_address: '',
      tax_id: '',
      document_url: '',
    })

    const verificationStatus = computed(() => store.state.verification_status)

    const statusMessage = computed(() => {
      const map = {
        unverified: 'Please complete your information to get verified.',
        pending: 'Your verification request is pending admin review.',
        verified: 'Your account is verified. You now have full access.',
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
