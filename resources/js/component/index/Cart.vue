<template>
    <MainLayout />
    <div class="container-fluid bg-light p-5">
    <h1 class="text-center text-secondary"><i class="fa-solid fa-cart-arrow-down"></i>Cart List</h1>
</div>
<!-- cart list -->
<section>
    <div class='container'>
        <div class="row">
            <div class="col-lg-12">
            <table class="table">
                    <thead>
                        <tr>
                        <th scope="col"><h4>Product</h4></th>
                        <th scope="col"><h4>Price</h4></th>
                        <th scope="col"><h4>Qauntity</h4></th>
                        <th scope="col"><h4>Subtotal</h4></th>
                        <th scope="col"><h4>Remove</h4></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <th>
                            <div class="d-flex gap-3">
                                <div>
                                    <img src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=1600&q=80" style='width:70px' class='rounded-3' alt="">
                                </div>
                                <div>
                                    <h5>Laptop</h5>
                                </div>
                            </div>
                        </th>
                        <td>4999 PKR</td>
                        <td>
                            <div class='d-flex gap-3 mb-3'>
                            <!-- <p>Quantity</p> -->
                                <div >
                                    <span class='btn btn-secondary rounded-start-pill p-1'><i class="fa-solid fa-minus  "></i></span>
                                    <span class='mx-2'>01</span>
                                    <span class='btn btn-secondary rounded-end-pill p-1 '><i class="fa-solid fa-plus "></i></span>

                                </div>
                            </div>
                        </td>
                        <td>4999 PKR</td>
                        <td><button type="button" class="btn-close" aria-label="Close"></button></td>
                        </tr>
                         <tr>
                        <th>
                            <div class="d-flex gap-3">
                                <div>
                                    <img src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=1600&q=80" style='width:70px' alt=""class='rounded-3'>
                                </div>
                                <div>
                                    <h5>Laptop</h5>
                                </div>
                            </div>
                        </th>
                        <td>4999 PKR</td>
                        <td>
                            <div class='d-flex gap-3 mb-3'>
                    <!-- <p>Quantity</p> -->
                                <div >
                                    <span class='btn btn-secondary rounded-start-pill p-1'><i class="fa-solid fa-minus  "></i></span>
                                    <span class='mx-2'>01</span>
                                    <span class='btn btn-secondary rounded-end-pill p-1 '><i class="fa-solid fa-plus "></i></span>

                                </div>
                            </div>
                        </td>
                        <td>4999 PKR</td>
                        <td><button type="button" class="btn-close" aria-label="Close"></button></td>
                        </tr>
                         <tr>
                        <th>
                            <div class="d-flex gap-3">
                                <div>
                                    <img src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=1600&q=80" style='width:70px' alt=""class='rounded-3'>
                                </div>
                                <div>
                                    <h5>Laptop</h5>
                                </div>
                            </div>
                        </th>
                        <td>4999 PKR</td>
                        <td>
                            <div class='d-flex gap-3 mb-3'>
                    <!-- <p>Quantity</p> -->
                                <div >
                                    <span class='btn btn-secondary rounded-start-pill p-1'><i class="fa-solid fa-minus  "></i></span>
                                    <span class='mx-2'>01</span>
                                    <span class='btn btn-secondary rounded-end-pill p-1 '><i class="fa-solid fa-plus "></i></span>

                                </div>
                            </div>
                        </td>
                        <td>4999 PKR</td>
                        <td><button type="button" class="btn-close" aria-label="Close"></button></td>
                        </tr>

                    </tbody>
                </table>
            </div>
            <div class="col-lg-5 ms-auto my-5">
                <div>
                    <h3>Price Detail</h3>
                </div>
                <div class='d-flex'>
                    <div><h5>Subtotal</h5></div>
                    <div class='ms-auto'><h5>4999 PKR</h5></div>
                </div>
                <div class='d-flex'>
                    <div><h5>Discount</h5></div>
                    <div class='ms-auto'><h5>99 PKR</h5></div>
                </div>
                <div class='d-flex'>
                    <div><h5>Delivery Charges</h5></div>
                    <div class='ms-auto'><h5>Free</h5></div>
                </div><hr>
                <div class='d-flex'>
                    <div><h4>Total</h4></div>
                    <div class='ms-auto'><h4>4900 PKR</h4></div>
                </div>
                <div class='mt-3'><a href="{{url('/checkout/product')}}"class="btn theme-login btn-lg mt-3 text-light rounded-pill w-100">Buy Now<i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
    import MainLayout from './layouts/MainLayout.vue';
    export default{
        components:{
            MainLayout
        }
    }
</script>


