
    <template>
        <AdminMainLayout>
            <div id="layoutSidenav_content">
                <main class="container-fluid px-4">

                <!-- Page Title -->
                <h1 class="mt-4">Admin Dashboard</h1>
                <p class="text-muted">Users & Vendors Management</p>

                <!-- STAT CARDS -->
                <div class="row g-4 mt-3">

                    <div class="col-xl-6 col-md-6">
                    <div class="card bg-primary text-white shadow">
                        <div class="card-body text-center">
                        <h6>Total Users</h6>
                        <h2>15</h2>
                        </div>
                    </div>
                    </div>

                    <div class="col-xl-6 col-md-6">
                    <div class="card bg-success text-white shadow">
                        <div class="card-body text-center">
                        <h6>Total Vendors</h6>
                        <h2>5</h2>
                        </div>
                    </div>
                    </div>

                </div>

                <!-- USERS TABLE -->
                <div class="card shadow mt-5">
                    <div class="card-header">
                    <h5 class="mb-0">Users Management</h5>
                    </div>
                    <div class="card-body table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-dark">
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th width="150">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>1</td>
                            <td>John Doe</td>
                            <td>john@mail.com</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>
                            <button class="btn btn-sm btn-danger">Block</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                </div>

                <!-- VENDORS TABLE -->
                <div class="card shadow mt-5 mb-5">
                    <div class="card-header">
                    <h5 class="mb-0">Vendors Management</h5>
                    </div>
                    <div class="card-body table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-dark">
                        <tr>
                            <th>Vendor ID</th>
                            <th>Shop Name</th>
                            <th>Owner</th>
                            <th>Status</th>
                            <th width="150">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>5</td>
                            <td>Tech Store</td>
                            <td>Ali Khan</td>
                            <td><span class="badge bg-warning">Pending</span></td>
                            <td>
                            <button class="btn btn-sm btn-success">Approve</button>
                            <button class="btn btn-sm btn-danger ms-1">Reject</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                </div>

                </main>
            </div>
        </AdminMainLayout>
    </template>

    <script>
    import AdminMainLayout from './layouts/AdminMainLayout.vue';
        export default {
            components:{
                AdminMainLayout
            }
        }


</script>

<style>
html, body {
  height: 100%;
  margin: 0;
}

.layout-wrapper {
  height: 100vh;
  display: flex;
  flex-direction: column;
}

.layout-body {
  flex: 1;
  display: flex;
  min-height: 0;
}

/* Sidebar */
.admin-sidebar {
  width: 250px;
  background: #212529;
  color: white;
}

/* Content */
.content {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
  background: #f8f9fa;
}
</style>




