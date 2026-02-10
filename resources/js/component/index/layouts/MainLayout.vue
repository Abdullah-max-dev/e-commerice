<template>
    
  <div>
    <Navbar />

    <main>
      <slot />
    </main>

    <Foot />
  </div>
</template>



<script>
    import Navbar from './Navbar.vue';
    import Foot from './Foot.vue';
    export default{
        components:{
            Navbar,
            Foot
        }
    }

</script>
