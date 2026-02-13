<template>
  <!-- Toggle button (mobile only) -->
  <button
    class="btn btn-dark d-md-none m-2"
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
        <router-link to="/vender-panel" class="nav-link text-light">
          <i class="fas fa-tachometer-alt me-2"></i>
          Dashboard
        </router-link>
      </li>

      <li class="nav-item">
        <a
          class="nav-link d-flex gap-2 text-light"
          data-bs-toggle="collapse"
          href="#productMenu"
        >
          <i class="fa-brands fa-shopify"></i>
          Product
        </a>

        <div class="collapse" id="productMenu">
          <ul class="nav flex-column ms-4">
            <li>
              <router-link class="nav-link text-light" to="/vender/add-product">
                Add
              </router-link>
            </li>
            <li>
              <router-link class="nav-link text-light" to="/vender/view-product">
                View
              </router-link>
            </li>
          </ul>
        </div>
      </li>
       <li class="nav-item">
        <router-link to="/vender/settings" class="nav-link text-light">
          <i class="fa-solid fa-gear me-2"></i>
          Settings
        </router-link>
      </li>

      <li class="nav-item mt-4">
        <router-link to="/" class="nav-link text-warning">
          <i class="fa-solid fa-store me-2"></i>
          Switch to user
        </router-link>
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
