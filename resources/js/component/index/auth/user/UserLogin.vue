    <template>
        <MainLayout />
        <div class="container-fluid bg-light p-5">
            <h1 class="text-center text-secondary"><i class="fa-solid fa-user"></i>user login</h1>
        </div>
        <section>
            <div class='container'>
                <div class='row'>
                    <div class='col-lg-12'>
                        <div class='row'>
                            <div class='col-lg-6'>
                                <div>
                                    <img src="https://imgs.search.brave.com/Pn5f9dJuFKzKFIInqbgeCSap3oeSaR5x9-U6IQcsg1U/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9pbWcu/ZnJlZXBpay5jb20v/cHJlbWl1bS12ZWN0/b3IvY29tcHV0ZXIt/bG9naW4tY29uY2Vw/dC1pbGx1c3RyYXRp/b25fMTE0MzYwLTc5/NjIuanBnP3NlbXQ9/YWlzX2h5YnJpZCZ3/PTc0MCZxPTgw" alt="" class='img-fluid '>
                                </div>
                            </div>
                            <div class='col-lg-6 my-5'>

                                <form @submit.prevent="login">
                                    <div class="row">
                                        <div class="col-lg-12 mb-3">
                                            <label for="exampleInputEmail1" class="form-label">Email address</label>
                                            <input type="email" class="form-control" id="exampleInputEmail1" name='email' placeholder='email or phone' aria-describedby="emailHelp" v-model="form.email">

                                        </div>
                                        <div class="col-lg-12 mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="text" class="form-control" id='password' name='password' placeholder='Enter password' v-model="form.password">

                                        </div>
                                        <button  type='submit' class='btn btn-primary text-white form-control form-control-lg' >Login</button>
                                        <div class='ms-4'>Don't have an account? <router-link to="/user-signup" class = 'text-decoration-none'> signup</router-link></div>

                                    </div>


                                </form>
                                <div v-if="errors.length" class="alert alert-danger">
                                    <ul>
                                        <li v-for="(err, i) in errors" :key="i">
                                        {{ err }}
                                        </li>
                                    </ul>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
    </section>



    </template>
    <script>

        import MainLayout from '../../layouts/MainLayout.vue';
        import { reactive , ref } from 'vue';
        import { useRouter } from 'vue-router';
        import axios from 'axios';
        import { useStore } from "vuex";

        export default{
            components:{
                MainLayout

            },
            setup(){
                let store = useStore();
                let router = useRouter();
                let form = reactive({
                    email : '',
                    password : ''
                });
                let errors = ref([])
                const login = async () => {
                    errors.value = []

                    try {
                        const res = await axios.post('/api/login', form)

                        if (res.data.success) {
                            const data = res.data.data

                            localStorage.setItem('token', data.token)
                            localStorage.setItem('role', data.role)
                            localStorage.setItem('verification_status', data.verification_status || 'unverified')

                            // ⚡ reactive
                            store.dispatch('setToken', data)

                        if (res.data.data.role === 'admin') {
                            router.push('/admin-panel')
                        }
                        else if (res.data.data.role === 'vender') {
                            router.push('/vender-panel')
                        }else{
                           router.push('/user-panel')
                        }

                        }

                    } catch (e) {
                        if (e.response && e.response.data && e.response.data.message) {
                            errors.value = Object.values(e.response.data.message).flat()
                        } else {
                            errors.value = ['Something went wrong']
                        }
                    }


                }
                return {
                    form,
                    login,
                    errors

                }
            }
        }

    </script>
