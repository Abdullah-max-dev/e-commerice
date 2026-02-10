<template>
    <div>
            <nav class="navbar navbar-expand-lg d-flex theme-navbar">
        <div class="container">
            <router-link class="navbar-brand" to = '/'><h2 class='text-light'>E-Commerce</h2></router-link>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div >


            <form class="d-flex bg-light border-1 rounded-2    " role="search">
                <div class='input-group'>
                    <input class="form-control" style="width:300px;" type="search" placeholder="Search fot Products" aria-label="Search"/>
                <button class="btn text-secondary " type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>

            </form>
            </div>
            <div class="d-flex">
                <router-link to = '/vender-register'
                v-if="!$store.getters.isVendor && !$store.getters.isAdmin"
                class="btn btn-warning px-4 py-2"
                @click="handleBecomeSeller"
                >
                Become a Seller
                </router-link>

                <router-link to = "/cart" class='btn btn-success btn-md py-2 mx-1 rounded-pill'><i class="fa-solid fa-cart-shopping"></i> Cart</router-link>
                <router-link
                v-if="!$store.getters.isLoggedIn"
                to="/user-login"
                class="btn theme-login btn-sm py-2 text-light rounded-pill"
                >
                <i class="fa-solid fa-user"></i> Login
                </router-link>
                <div v-if="$store.getters.isLoggedIn" class="dropdown">
                    <button
                        class="btn profile-btn dropdown-toggle"
                        data-bs-toggle="dropdown"
                    >
                        <i class="fa-solid fa-user"></i> Profile
                    </button>

                    <ul class="dropdown-menu profile-menu">
                        <li v-if="$store.getters.isUser">
                        <router-link class="dropdown-item" to="/user-panel">
                            User Panel
                        </router-link>
                        </li>

                        <li v-if="$store.getters.isVendor">
                        <router-link class="dropdown-item" to="/vender-panel">
                            Vender Panel
                        </router-link>
                        </li>

                        
                        <li><hr class="dropdown-divider" /></li>

                        <li>
                        <button class="dropdown-item logout-btn" @click="logout">
                            Logout
                        </button>
                        </li>
                    </ul>
                </div>


            </div>
        </div>
        </nav>

        <!-- Feature Navbar -->
        <nav class="navbar navbar-expand-lg  shadow p-3  bg-body-tertiary rounded">
        <div class="container-fluid">

            <div class="collapse navbar-collapse justify-content-center  gap-5" id="navbarNavAltMarkup">







            </div>
        </div>
        </nav>

    </div>
</template>

<script>
    import { useRouter } from 'vue-router';
    import { useStore } from 'vuex';
    export default {
        setup(){
            let store = useStore();
            const router = useRouter();
            function logout(){
                store.dispatch('removeToken');
                localStorage.removeItem('token');

                router.push({path:'/'})
            }
            return{
                logout
            }
        }
    }
</script>

<style scoped>
/* Main theme navbar */
.theme-navbar {
  background: linear-gradient(90deg, #0f2027, #203a43, #2c5364);
  padding: 12px 0;
}

/* Brand */
.navbar-brand h2 {
  font-weight: 700;
  margin: 0;
}

/* Search box */
.navbar form {
  max-width: 100%;
}

.navbar input[type="search"] {
  border: none;
  box-shadow: none;
}

.navbar input[type="search"]:focus {
  outline: none;
  box-shadow: none;
}

/* Buttons */
.theme-login {
  background-color: #ff6a00;
  border: none;
}

.theme-login:hover {
  background-color: #e65c00;
}

/* Links */
.navbar a {
  font-size: 14px;
}

/* Cart button */
.navbar .btn-success {
  background-color: #28a745;
  border: none;
}

/* Mobile responsiveness */
@media (max-width: 991px) {
  .navbar .container {
    flex-direction: column;
    gap: 12px;
  }

  .navbar form {
    width: 100%;
  }

  .navbar input[type="search"] {
    width: 100% !important;
  }

  .navbar a,
  .navbar button {
    width: 100%;
    text-align: center;
  }
}

/* Feature navbar */
.navbar.bg-body-tertiary {
  background-color: #ffffff !important;
}
/* Profile button */
.profile-btn {
  background: #ffffff;
  color: #000;
  border-radius: 30px;
  padding: 6px 14px;
  font-weight: 500;
  border: none;
}

.profile-btn:hover {
  background: #f1f1f1;
}

/* Dropdown menu */
.profile-menu {
  background: #1e1e1e;
  border-radius: 10px;
  padding: 8px;
  border: none;
  min-width: 180px;
}

/* Dropdown items */
.profile-menu .dropdown-item {
  color: #fff;
  padding: 10px 12px;
  border-radius: 6px;
}

.profile-menu .dropdown-item:hover {
  background: #333;
  color: #fff;
}

/* Divider */
.profile-menu .dropdown-divider {
  border-color: #444;
}

/* Logout button */
.logout-btn {
  color: #ff4d4d !important;
}

/* Become a Seller button */
.seller-btn {
  background: linear-gradient(135deg, #ff9800, #ff5722);
  color: #fff;
  border-radius: 30px;
  padding: 6px 16px;
  font-weight: 500;
  border: none;
}

.seller-btn:hover {
  opacity: 0.9;
}

</style>

