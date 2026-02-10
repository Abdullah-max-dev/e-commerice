<template>
  <div class="layout-wrapper">
    <!-- Navbar -->
    <UserNavbar />

    <!-- Body -->
    <div class="layout-body">
      <!-- Sidebar -->
      <UserSidebar />

      <!-- Page Content -->
      <main class="content">
        <slot />
      </main>
    </div>
  </div>
</template>

<script>
import UserNavbar from './UserNavbar.vue'
import UserSidebar from './UserSidebar.vue'

export default {
  components: {
    UserNavbar,
    UserSidebar
  }
}
</script>

<style scoped>
/* Whole page */
.layout-wrapper {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* Navbar already takes height */
.layout-body {
  display: flex;
  flex: 1;
  background: #f8f9fa;
}

/* Sidebar width MUST match sidebar component */
.layout-body > *:first-child {
  width: 220px;
  flex-shrink: 0;
}

/* Main content */
.content {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
  background: #f8f9fa;
}
</style>
