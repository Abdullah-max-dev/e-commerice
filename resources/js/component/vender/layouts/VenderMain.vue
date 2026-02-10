<template>
  <div class="layout-wrapper">
    <!-- Navbar -->
    <VenderNavbar />

    <!-- Body -->
    <div class="layout-body">
      <!-- Sidebar -->
      <VenderSidebar />

      <!-- Page Content -->
      <main class="content">
        <slot />
      </main>
    </div>
  </div>
</template>

<script>
import VenderNavbar from './VenderNavbar.vue'
import VenderSidebar from './VenderSidebar.vue'

export default {
  components: {
    VenderNavbar,
    VenderSidebar
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
