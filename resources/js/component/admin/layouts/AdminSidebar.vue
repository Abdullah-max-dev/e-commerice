<template>
  <!-- Toggle button (mobile only) -->
  <button
    class="btn btn-dark d-md-none m-2 "
    @click="isOpen = !isOpen"
  >
    <i class="fas fa-bars"></i>
  </button>

  <!-- Sidebar -->
  <div
    class="bg-dark text-light vh-100 p-3 sidebar"
    :class="{ show: isOpen }"
  >
    <ul class="nav flex-column gap-2">

      <li class="nav-item">
        <router-link to="/user" class="nav-link text-light">
          <i class="fas fa-tachometer-alt me-2"></i>
          Dashboard
        </router-link>
      </li>

        <li class="nav-item">
            <router-link to = '/admin/category' class="text-decoration-none text-light m-3">
            <i class="fa-solid fa-layer-group"></i>
            Category
            </router-link>



        </li>
        <li class="nav-item">
        <a
          class="nav-link d-flex gap-2 text-light"
          data-bs-toggle="collapse"
          href="#productMenu"
        >
          <i class="fa-brands fa-shopify"></i>
          Vender
        </a>

        <div class="collapse" id="productMenu">
          <ul class="nav flex-column ms-4">
            <li>
              <router-link class="nav-link text-light" to="/admin/bussiness-type">
                Bussiness Type
              </router-link>
            </li>
          </ul>
        </div>
      </li>


    </ul>
  </div>
</template>

<script>
export default {
  data() {
    return {
      isOpen: false
    }
  }
}
</script>

<style scoped>
.sidebar {
  width: 230px;
  position: fixed;
  left: -230px;
  top: 0;
  transition: all 0.3s ease;
  z-index: 1000;
}

/* show sidebar */
.sidebar.show {
  left: 0;
}

/* desktop view */
@media (min-width: 768px) {
  .sidebar {
    left: 0;
    position: static;
  }
}
.nav-link.router-link-active {
  background: #0d6efd;
  border-radius: 6px;
}
</style>
