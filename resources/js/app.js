
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import Toastr from 'vue-toastr';


require('./bootstrap');
require('vue-toastr/src/vue-toastr.scss');

window.Vue = require('vue');

Vue.use(require('vue-resource'));
Vue.use(Toastr);

Vue.http.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('pagination', require('./components/Pagination'));
Vue.component('movies', require('./components/movies/Movies'));

const app = new Vue({
    el: '#app'
});
