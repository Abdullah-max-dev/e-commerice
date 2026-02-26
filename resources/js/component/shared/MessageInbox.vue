<template>
  <div class="position-relative">
    <button class="btn btn-sm btn-outline-light position-relative" @click="toggleInbox">
      ✉️
      <span v-if="unreadCount > 0" class="badge rounded-pill bg-danger message-badge">{{ unreadCount }}</span>
    </button>

    <div v-if="isOpen" class="inbox-dropdown card shadow">
      <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Messages</strong>
        <button class="btn btn-sm btn-light" @click="fetchMessages">↻</button>
      </div>
      <div class="card-body p-0">
        <div v-if="loading" class="p-3 text-muted">Loading...</div>
        <div v-else-if="!messages.length" class="p-3 text-muted">No messages</div>
        <ul v-else class="list-group list-group-flush">
          <li
            v-for="message in messages"
            :key="message.id"
            class="list-group-item message-item"
            :class="{ 'unread-item': !message.is_read, 'warning-item': message.is_warning }"
            @click="handleRead(message)"
          >
            <div class="d-flex justify-content-between gap-2">
              <strong>{{ message.product_name || message.title || 'Report message' }}</strong>
              <small>{{ formatDate(message.created_at) }}</small>
            </div>
            <div class="small text-muted">Reason: {{ message.reason || '-' }}</div>
            <div class="small text-muted">Comment: {{ message.report_comment || message.message || '-' }}</div>
            <div class="small text-muted">Status: {{ message.status || '-' }}</div>
            <div class="mt-2 d-flex gap-2" v-if="role === 'vendor'">
              <button class="btn btn-sm btn-outline-secondary" @click.stop="archiveMessage(message.id)">Archive</button>
              <button class="btn btn-sm btn-outline-danger" @click.stop="deleteMessage(message.id)">Delete</button>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { onMounted, ref } from 'vue'

export default {
  setup() {
    const messages = ref([])
    const unreadCount = ref(0)
    const loading = ref(false)
    const isOpen = ref(false)
    const role = localStorage.getItem('role')

    const authHeaders = () => ({ Authorization: `Bearer ${localStorage.getItem('token')}` })

    const endpoints = {
      user: '/api/user/reports',
      vender: '/api/vendor/messages',
      vendor: '/api/vendor/messages',
      admin: '/api/admin/reports',
    }

    const fetchMessages = async () => {
      const endpoint = endpoints[role]
      if (!endpoint) return
      loading.value = true
      try {
        const { data } = await axios.get(endpoint, { headers: authHeaders() })

        if (role === 'admin') {
          messages.value = (data.data || []).map((r) => ({
            id: r.id,
            title: r.product?.p_name,
            product_name: r.product?.p_name,
            reason: r.reason,
            report_comment: r.message,
            status: r.status,
            created_at: r.created_at,
            is_read: r.status !== 'pending',
            is_warning: false,
          }))
          unreadCount.value = messages.value.filter((message) => !message.is_read).length
        } else {
          messages.value = data.data?.data || []
          unreadCount.value = data.unread_count || 0
        }
      } finally {
        loading.value = false
      }
    }

    const handleRead = async message => {
      if (role === 'admin') {
        message.is_read = true
        unreadCount.value = messages.value.filter(item => !item.is_read).length
        return
      }

      if (role === 'user' || role === 'vender' || role === 'vendor') {
        const endpoint = role === 'user' ? `/api/user/reports/read/${message.id}` : `/api/vendor/messages/read/${message.id}`
        await axios.post(endpoint, {}, { headers: authHeaders() })
        message.is_read = true
        unreadCount.value = Math.max(unreadCount.value - 1, 0)
      }
    }

    const archiveMessage = async id => {
      await axios.post(`/api/vendor/messages/archive/${id}`, {}, { headers: authHeaders() })
      await fetchMessages()
    }

    const deleteMessage = async id => {
      await axios.delete(`/api/vendor/messages/${id}`, { headers: authHeaders() })
      await fetchMessages()
    }

    const toggleInbox = async () => {
      isOpen.value = !isOpen.value
      if (isOpen.value) {
        await fetchMessages()
      }
    }

    const formatDate = date => (date ? new Date(date).toLocaleString() : '-')

    onMounted(fetchMessages)

    return { messages, unreadCount, loading, isOpen, role, toggleInbox, fetchMessages, handleRead, archiveMessage, deleteMessage, formatDate }
  },
}
</script>

<style scoped>
.inbox-dropdown { position: absolute; right: 0; top: 40px; width: 360px; z-index: 999; }
.message-badge { position: absolute; top: -8px; right: -8px; }
.message-item { cursor: pointer; }
.unread-item { background: #f1f7ff; }
.warning-item { border-left: 4px solid #f39c12; }
</style>
