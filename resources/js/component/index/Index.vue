<template>
    <MainLayout />

    <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="0" class="active"></button>
    <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="1"></button>
    <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="2"></button>
  </div>

  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=1920&q=80"
           class="d-block w-100 carousel-img" alt="Shoes">
      <div class="carousel-caption d-none d-md-block">
        <h2 class="fw-bold">Step Up Your Style</h2>
        <p>Comfort & design that moves with you</p>
      </div>
    </div>

    <div class="carousel-item">
      <img src="https://images.unsplash.com/photo-1491553895911-0055eca6402d?w=1920&q=80"
           class="d-block w-100 carousel-img" alt="Fashion Products">
      <div class="carousel-caption d-none d-md-block">
        <h2 class="fw-bold">New Season Arrivals</h2>
        <p>Check out all the new trends</p>
      </div>
    </div>

    <div class="carousel-item">
      <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=1920&q=80"
           class="d-block w-100 carousel-img" alt="Electronics">
      <div class="carousel-caption d-none d-md-block">
        <h2 class="fw-bold">Latest Arrival</h2>
        <p>Smart gadgets for smart living</p>
      </div>
    </div>
  </div>

  <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>



<!-- Card deals -->


<section class="my-5">
  <div class="container">

    <div class="d-flex">
        <div class="p-2 flex-grow-1"><h1 >Top Deals</h1></div>
        <div class="p-2"><a href="#" class="text-decoration-none btn btn-success btn-sm">View all</a></div>

    </div>
    <div class="row row-cols-1 row-cols-sm-4 g-2   ">



      <div class="col d-flex ">
        <div class="card w-100 h-100 card-hover">
         <a href="#"> <img src="https://cdn.pixabay.com/photo/2019/11/05/16/32/hour-4603921_960_720.jpg" class="card-img-top"  alt="White Sneaker">
         <div class="card-body d-flex flex-column text-dark">
            <h5 class="card-title text-center">White Sneaker</h5>
            <h4 class="text-center">7999 PKR</h4>
          </div></a>
        </div>
      </div>

      <div class="col d-flex ">
        <div class="card w-100 h-100 card-hover">
          <a href="#">
            <img src="https://cdn.pixabay.com/photo/2020/04/14/09/53/nike-5041716_960_720.jpg" class="card-img-top"  alt="Nike Sneaker">
          <div class="card-body d-flex flex-column text-dark">
            <h5 class="card-title text-center">Nike Sneaker</h5>
            <h4 class="text-center">3999 PKR</h4>
          </div>
          </a>
        </div>
      </div>

      <div class="col d-flex">
        <div class="card w-100 h-100 card-hover">
          <a href="#">
            <img src="https://cdn.pixabay.com/photo/2011/03/01/03/55/iron-5112_960_720.jpg" class="card-img-top"  alt="Iron">
          <div class="card-body d-flex flex-column text-dark">
            <h5 class="card-title text-center">Electric Iron</h5>
            <h4 class="text-center">9999 PKR</h4>
          </div>
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Top electronics -->

<section class="my-5">
  <div class="container">

    <div class="d-flex">
        <div class="p-2 flex-grow-1"><h1 >Top Electronics</h1></div>
        <div class="p-2"><a href="#" class="text-decoration-none btn btn-success btn-sm">View all</a></div>

    </div>
    <div class="row row-cols-1 row-cols-md-4 g-2 ">



      <div class="col d-flex ">
        <div class="card w-100 h-100 card-hover">
         <a href="#"> <img src="https://cdn.pixabay.com/photo/2019/11/05/16/32/hour-4603921_960_720.jpg" class="card-img-top"  alt="White Sneaker">
         <div class="card-body d-flex flex-column text-dark">
            <h5 class="card-title text-center">White Sneaker</h5>
            <h4 class="text-center">7999 PKR</h4>
          </div></a>
        </div>
      </div>

      <div class="col d-flex ">
        <div class="card w-100 h-100 card-hover">
          <a href="#">
            <img src="https://cdn.pixabay.com/photo/2020/04/14/09/53/nike-5041716_960_720.jpg" class="card-img-top"  alt="Nike Sneaker">
          <div class="card-body d-flex flex-column text-dark">
            <h5 class="card-title text-center">Nike Sneaker</h5>
            <h4 class="text-center">3999 PKR</h4>
          </div>
          </a>
        </div>
      </div>

      <div class="col d-flex">
        <div class="card w-100 h-100 card-hover">
          <a href="#">
            <img src="https://cdn.pixabay.com/photo/2011/03/01/03/55/iron-5112_960_720.jpg" class="card-img-top"  alt="Iron">
          <div class="card-body d-flex flex-column text-dark">
            <h5 class="card-title text-center">Electric Iron</h5>
            <h4 class="text-center">9999 PKR</h4>
          </div>
          </a>
        </div>
      </div>

    </div>
  </div>
</section>
<!-- Popular Catagoriez -->

<section class="my-5">
  <div class="container">

    <div class="">

        <div class="p-2 flex-grow-1"><h1 >Popular Catagorize</h1></div>

        <div
            class="col-4 d-flex mt-4"
            v-for="product in popularProducts"
            :key="product.p_id"
            >
            <div class="card w-100 h-100 card-hover position-relative">

                <!-- Discount badge -->
                <div
                v-if="product.discount"
                class="badge bg-danger position-absolute"
                style="top:10px; right:10px; z-index:10;"
                >
                {{ Math.round(((product.p_price - product.final_price)/product.p_price)*100) }}% OFF
                </div>

                <a href="#">
                <!-- Product Image -->
                <img
                    :src="`/uploads/products/${product.p_image}`"
                    class="card-img-top"
                    :alt="product.p_name"
                    style="height:220px; object-fit:cover;"
                >

                <div class="card-body d-flex flex-column text-dark">
                    <h5 class="card-title text-center">{{ product.p_name }}</h5>

                    <!-- Price -->
                    <div class="text-center">
                    <span v-if="product.discount" class="text-danger fw-bold me-2">
                        Rs {{ product.final_price }}
                    </span>
                    <span v-if="product.discount" class="text-muted text-decoration-line-through">
                        Rs {{ product.p_price }}
                    </span>
                    <span v-else class="fw-bold">
                        Rs {{ product.p_price }}
                    </span>
                    </div>
                </div>
                </a>
            </div>
        </div>




    </div>

  </div>
</section>

<!-- recently viewed-->

<section class="my-5">
  <div class="container">

    <div class="d-flex">
        <div class="p-2 flex-grow-1"><h1 >Recent View</h1></div>
        <div class="p-2"><a href="#" class="text-decoration-none btn theme-login btn-sm">View all</a></div>

    </div>
    <div class="row row-cols-1 row-cols-md-4 g-2 ">



      <div class="col d-flex ">
        <div class="card w-100 h-100 card-hover">
         <a href="#"> <img src="https://cdn.pixabay.com/photo/2019/11/05/16/32/hour-4603921_960_720.jpg" class="card-img-top"  alt="White Sneaker">
         <div class="card-body d-flex flex-column text-dark">
            <h5 class="card-title text-center">White Sneaker</h5>
            <h4 class="text-center">7999 PKR</h4>
          </div></a>
        </div>
      </div>

      <div class="col d-flex ">
        <div class="card w-100 h-100 card-hover">
          <a href="#">
            <img src="https://cdn.pixabay.com/photo/2020/04/14/09/53/nike-5041716_960_720.jpg" class="card-img-top"  alt="Nike Sneaker">
          <div class="card-body d-flex flex-column text-dark">
            <h5 class="card-title text-center">Nike Sneaker</h5>
            <h4 class="text-center">3999 PKR</h4>
          </div>
          </a>
        </div>
      </div>

      <div class="col d-flex">
        <div class="card w-100 h-100 card-hover">
          <a href="#">
            <img src="https://cdn.pixabay.com/photo/2011/03/01/03/55/iron-5112_960_720.jpg" class="card-img-top"  alt="Iron">
          <div class="card-body d-flex flex-column text-dark">
            <h5 class="card-title text-center">Electric Iron</h5>
            <h4 class="text-center">9999 PKR</h4>
          </div>
          </a>
        </div>
      </div>

    </div>
  </div>
</section>



</template>



<script>
import MainLayout from './layouts/MainLayout.vue'
import axios from 'axios'
import { ref, onMounted } from 'vue'

export default {
  components: {
    MainLayout
  },

  setup() {
    const popularProducts = ref([])

    // Fetch popular products from API
    const getPopularProducts = async () => {
      try {
        const res = await axios.get('/api/popular-products')
        let products = res.data.products || []

        // Ensure each product has required fields
        popularProducts.value = products.map(product => {
          return {
            ...product,
            // Fallbacks
            p_image: product.p_image || 'default-product.png',
            discount: product.discount || false,
            final_price: product.final_price ?? product.p_price
          }
        })
      } catch (e) {
        console.error('Error loading popular products', e)
      }
    }

    onMounted(() => {
      getPopularProducts()
    })

    return {
      popularProducts
    }
  }
}
</script>


<style>
/* ---------- GLOBAL ---------- */
h1 {
  font-weight: 700;
  font-size: 1.4rem;
  margin-bottom: 0;
}

section {
  margin-top: 30px;
}

/* ---------- CAROUSEL ---------- */
.carousel-img {
  height: 55vh;
  width: 100%;
  object-fit: cover;
}

.carousel-caption {
  background: rgba(0, 0, 0, 0.5);
  padding: 12px 16px;
  border-radius: 8px;
}

.carousel-caption h2 {
  font-size: 1.6rem;
  margin-bottom: 4px;
}

.carousel-caption p {
  font-size: 0.9rem;
  margin-bottom: 0;
}

@media (max-width: 768px) {
  .carousel-img {
    height: 38vh;
  }
}

/* ---------- SECTION HEADER ---------- */
.d-flex > h1 {
  font-size: 1.3rem;
}

/* ---------- CARDS ---------- */
.card {
  border-radius: 12px;
  box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
  overflow: hidden; /* IMPORTANT for image rounding */
  transition: all 0.2s ease;
  background: #fff;
}

/* FULL IMAGE */
.card img {
  width: 100%;
  height: 220px;        /* consistent image height */
  object-fit: cover;   /* fill card without stretching */
  display: block;
}

/* CARD BODY */
.card-body {
  padding: 10px 12px;
  text-align: center;
}

.card-body h5 {
  font-size: 0.95rem;
  margin-bottom: 4px;
  font-weight: 600;
  color: #212529;
}

.card-body h4 {
  font-size: 1.05rem;
  font-weight: 700;
  color: #198754;
  margin-bottom: 0;
}

/* ---------- HOVER EFFECT ---------- */
.card-hover:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 22px rgba(0, 0, 0, 0.12);
}

/* ---------- BUTTONS ---------- */
.btn-sm {
  padding: 4px 12px;
  font-size: 0.75rem;
  border-radius: 20px;
}

/* ---------- GRID SPACING ---------- */
.row {
  --bs-gutter-x: 0.5rem;
  --bs-gutter-y: 0.5rem;
}

/* ---------- MOBILE ---------- */
@media (max-width: 576px) {
  .card img {
    height: 180px;
  }

  h1 {
    font-size: 1.2rem;
  }
}
</style>
