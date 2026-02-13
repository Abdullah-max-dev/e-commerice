<template>
    <AdminMainLayout>
        <div class="container mt-4">
            <h4 class="mb-3">Category Management</h4>

            <!-- ADD / UPDATE FORM -->
            <div class="card mb-4">
            <div class="card-body">
                <form @submit.prevent="submitCategory">
                <div class="row g-2">
                    <div class="col-md-5">
                    <input
                        v-model="form.c_name"
                        type="text"
                        class="form-control"
                        placeholder="Category Name"
                    />
                    </div>

                    <div class="col-md-3">
                    <input
                        v-model="form.c_commission"
                        type="number"
                        class="form-control"
                        placeholder="Commission (%)"
                    />
                    </div>

                    <div class="col-md-4">
                    <button class="btn btn-primary w-100">
                        {{ editId ? 'Update' : 'Add' }} Category
                    </button>
                    </div>
                </div>

                <p v-if="error" class="text-danger mt-2">{{ error }}</p>
                </form>
            </div>
            </div>

            <!-- CATEGORY TABLE -->
            <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Commission</th>
                    <th>Top</th>
                    <th width="220">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="cat in categories" :key="cat.c_id">
                    <td>{{ cat.c_id }}</td>
                    <td>{{ cat.c_name }}</td>
                    <td>{{ cat.c_commission }}%</td>
                    <td>
                        <span
                        class="badge"
                        :class="cat.is_popular ? 'bg-success' : 'bg-secondary'"
                        >
                        {{ cat.is_popular ? 'YES' : 'NO' }}
                        </span>
                    </td>
                    <td>
                        <button
                        class="btn btn-sm btn-warning me-1"
                        @click="editCategory(cat)"
                        >
                        Edit
                        </button>

                        <button
                        class="btn btn-sm btn-danger me-1"
                        @click="deleteCategory(cat.c_id)"
                        >
                        Delete
                        </button>

                        <button
                        class="btn btn-sm btn-info"
                        @click="toggleTop(cat.c_id)"
                        >
                        Toggle Top
                        </button>
                    </td>
                    </tr>

                    <tr v-if="!categories.length">
                    <td colspan="5" class="text-center">No Categories</td>
                    </tr>
                </tbody>
                </table>
            </div>
            </div>
        </div>
    </AdminMainLayout>
</template>
