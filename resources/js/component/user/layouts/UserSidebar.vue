<template>
  <div class="bg-dark text-light p-3 sidebar">
    <ul class="nav flex-column gap-2">

      <li class="nav-item">
        <router-link to="/user-panel" class="nav-link text-light">
          <i class="fas fa-tachometer-alt me-2"></i>
          Dashboard
        </router-link>
      </li>

      <li class="nav-item">
        <router-link to="/user/order-history" class="nav-link text-light">
          <i class="fa-regular fa-clock me-2"></i>
          Order History
        </router-link>
      </li>

      <li class="nav-item">
        <router-link to="/user/settings" class="nav-link text-light">
          <i class="fa-solid fa-gear me-2"></i>
          Settings
        </router-link>
      </li>

      <!-- EXTRA (added for you) -->
      <li class="nav-item mt-4">
        <router-link to="/" class="nav-link text-warning">
          <i class="fa-solid fa-store me-2"></i>
          Back to Shop
        </router-link>
      </li>

    </ul>
  </div>
</template>

<script>
export default {}
</script>

<style scoped>
.sidebar {
  width: 230px;
  height: 100%;
  position: sticky;
  top: 0;
}
.nav-link.router-link-active {
  background: #0d6efd;
  border-radius: 6px;
}
</style>
