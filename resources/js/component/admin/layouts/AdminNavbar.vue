<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand fw-bold" href="#">User Panel</a>

    <div class="ms-auto d-flex align-items-center gap-3">
      <span class="text-light">Hi, Abdullah</span>

      <img
        src="https://cdn-icons-png.flaticon.com/512/2922/2922510.png"
        class="rounded-circle"
        style="width:40px;height:40px;"
      />

      <button @click="logout" class="btn btn-sm btn-outline-light">
        Logout
      </button>
    </div>
  </nav>
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
