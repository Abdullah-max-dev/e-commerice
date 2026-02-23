<template>
  <div class="comment-item" :class="{ 'is-reply': depth > 0 }">
    <div class="d-flex justify-content-between align-items-center mb-1">
      <strong>{{ comment.user?.name || 'User' }}</strong>
      <small class="text-muted">{{ formatDate(comment.created_at) }}</small>
    </div>

    <div v-if="comment.rating" class="mb-1 text-warning">
      <span v-for="star in 5" :key="`star-${comment.id}-${star}`">{{ star <= comment.rating ? '★' : '☆' }}</span>
    </div>

    <p class="mb-2">{{ comment.comment }}</p>

    <button
      v-if="canReply"
      class="btn btn-sm btn-outline-secondary"
      @click="$emit('reply', comment)"
    >
      Reply
    </button>

    <div v-if="comment.replies?.length" class="reply-list mt-3">
      <ProductCommentItem
        v-for="reply in comment.replies"
        :key="reply.id"
        :comment="reply"
        :can-reply="canReply"
        :depth="depth + 1"
        @reply="$emit('reply', $event)"
      />
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProductCommentItem',
  props: {
    comment: {
      type: Object,
      required: true,
    },
    canReply: {
      type: Boolean,
      default: false,
    },
    depth: {
      type: Number,
      default: 0,
    },
  },
  emits: ['reply'],
  setup() {
    const formatDate = value => {
      if (!value) return ''

      return new Date(value).toLocaleString()
    }

    return {
      formatDate,
    }
  },
}
</script>

<style scoped>
.comment-item {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 12px;
  background: #fff;
}

.comment-item.is-reply {
  margin-left: 16px;
  border-left: 3px solid #d1d5db;
}

.reply-list {
  display: grid;
  gap: 10px;
}
</style>
