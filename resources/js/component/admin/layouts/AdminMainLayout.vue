<template>
  <div class="layout-wrapper">
    <!-- Navbar -->
    <AdminNavbar />

    <!-- Body -->
    <div class="layout-body">
      <!-- Sidebar -->
      <AdminSidebar />

      <!-- Page Content -->
    <main class="content">
        <slot />
    </main>
    </div>
  </div>
</template>




<script>
    import AdminNavbar from './AdminNavbar.vue';
    import AdminSidebar from './AdminSidebar.vue';
    export default{
        components:{
            AdminNavbar,
            AdminSidebar
        }
    }

</script>
