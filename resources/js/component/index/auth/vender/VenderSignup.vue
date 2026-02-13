<template>
    <MainLayout />
    <div class="container-fluid bg-light p-5">
        <h1 class="text-center text-secondary"><i class="fa-solid fa-user"></i>User register</h1>
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
                            <div class="alert alert-success" v-if="message">
                                {{ message }}
                            </div>
                            <form @submit.prevent="vender_register">
                                <div class="row">
                                    <div class="col-lg-12 mb-3">
                                        <label for="fullname" class="form-label">Full Name</label>
                                        <input type="text"  class="form-control" id='name'name='name' placeholder='Full Name' v-model="form.name">

                                    </div>

                                    <div class="col-lg-12mb-3">
                                        <label for="exampleInputEmail1" class="form-label">Email address</label>
                                        <input type="email"  class="form-control" id="exampleInputEmail1"name='email' placeholder='abc@wxample.com' aria-describedby="emailHelp" v-model="form.email">

                                    </div>

                                    <div class="col-lg-12 mb-3">
                                        <label for="password" class="form-label">password</label>
                                        <input type="text"   class="form-control" id='password'name='password' placeholder='*****' v-model="form.password">

                                    </div>
                                    <div class="col-lg-12 mb-3">
                                        <label for="confirmpassword" class="form-label">Confirm password</label>
                                        <input type="text" class="form-control" id='password_confirmation' name='password_confirmation' placeholder='******' v-model="form.password_confirmation">

                                    </div>




                                    <button type ='submit' class='btn btn-primary text-white form-control form-control-lg' >signup</button>
                                    <div class='ms-4'>Have an account? <router-link to="/user-login" class='text-decoration-none'>Login</router-link></div>
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


    export default{
        components:{
            MainLayout

        },
        setup(){

            let router = useRouter();
            let form = reactive({
                name : '',
                email : '',
                password : '',
                password_confirmation : ''
            });
            let message = ref('')
            let errors = ref([])
           const vender_register = async () => {
                errors.value = []

                try {
                    const res = await axios.post('/api/vender-signup', form)

                    if (res.data.success) {
                    localStorage.setItem('token', res.data.data.token)
                    localStorage.setItem('role', res.data.data.role)
                    localStorage.setItem('verification_status', res.data.data.verification_status || 'unverified')
                    message.value = res.data.message

                    }

                } catch (e) {
                    if (e.response?.data) {
                        errors.value = Object.values(e.response.data.message).flat()
                    } else {
                        errors.value = ['Something went wrong']
                    }
                }


            }

            return {
                form,
                vender_register,
                errors,
                message


            }
        }
    }

</script>
