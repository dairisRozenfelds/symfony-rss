import '../css/app.scss';

import Vue from 'vue';
import Index from '../vue/components/Index';
import Login from '../vue/components/Login';
import Register from '../vue/components/Register';

new Vue({
    components: {
        Index,
        Login,
        Register
    }
}).$mount("#app");
