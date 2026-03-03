<template>
  <div class="card w-100 h-100 card-hover position-relative">
    <div
      v-if="product.discount"
      class="badge bg-danger position-absolute"
      style="top: 10px; right: 10px; z-index: 10"
    >
      {{ Math.round(((product.p_price - product.final_price) / product.p_price) * 100) }}% OFF
    </div>

    <RouterLink :to="'/product/' + product.p_id" @click="$emit('open', product)">
      <img :src="product.p_image" class="card-img-top" :alt="product.p_name" />
      <div class="card-body d-flex flex-column text-dark text-center">
        <h5 class="card-title">{{ product.p_name }}</h5>
        <div>
          <span v-if="product.discount" class="text-danger fw-bold me-2">Rs {{ product.final_price }}</span>
          <span v-if="product.discount" class="text-muted text-decoration-line-through">Rs {{ product.p_price }}</span>
          <span v-else class="fw-bold">Rs {{ product.p_price }}</span>
        </div>
      </div>
    </RouterLink>
  </div>
</template>

<script>
export default {
  name: 'ProductCard',
  props: {
    product: {
      type: Object,
      required: true,
    },
  },
  emits: ['open'],
}
</script>
