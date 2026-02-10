import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import { createApp } from 'vue'
import router from './router'
import App from './app.vue'
import Store from './store/index.js';

createApp(App).use(router).use(Store).mount('#app')



