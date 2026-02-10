<template>
  <UserMain>
    <div class="container-fluid px-4">
      <h1 class="my-4">Dashboard</h1>

      <div class="row">
        <div class="col-xl-4 col-md-4 my-4">
          <div class="card bg-info text-white mb-4">
            <div class="card-body text-center">
              <img
                src="https://cdn-icons-png.flaticon.com/512/2922/2922510.png"
                style="width:150px; border-radius:50%;"
              />
              <div class="mt-2">Username</div>
            </div>
          </div>
        </div>

        <div class="col-xl-4 col-md-4 my-4">
          <div class="card bg-secondary text-white mb-4">
            <div class="card-body">
              <h5>Billing Address:</h5>
              <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-xl-12">
          <h4>Recent Orders</h4>
          <table class="table">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>Mark</td>
                <td>Otto</td>
                <td>Delivered</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </UserMain>
</template>

<script>
import UserMain from './layouts/UserMain.vue'

export default {
  components: { UserMain },
 
}

</script>
