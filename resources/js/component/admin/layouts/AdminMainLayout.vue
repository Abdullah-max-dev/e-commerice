<template>
  <div class="layout-wrapper d-flex flex-column vh-100">
    <!-- Navbar -->
    <AdminNavbar />

    <!-- Body -->
    <div class="layout-body d-flex flex-grow-1">
      <!-- Sidebar -->
      <AdminSidebar class="flex-shrink-0" />

      <!-- Page Content -->
      <main class="content flex-grow-1 p-4 overflow-auto">
        <slot />
      </main>
    </div>
  </div>
</template>

<script>
import AdminNavbar from './AdminNavbar.vue'
import AdminSidebar from './AdminSidebar.vue'

export default {
  components: {
    AdminNavbar,
    AdminSidebar,
  },
}
</script>

<style>
.layout-wrapper {
  min-height: 100vh;
}

.layout-body {
  display: flex;
  flex: 1;
  overflow: hidden;
}

.content {
  background-color: #f8f9fa; /* optional: light background for content */
}
</style>
