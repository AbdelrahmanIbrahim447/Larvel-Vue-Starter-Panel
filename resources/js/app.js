
require('./bootstrap');

window.Vue = require('vue');
import VueRouter from 'vue-router'; // import vue-router
import moment from 'moment'; // import moment
import {routes} from './components/routes'; // import defines vue routes
import VueProgressBar from 'vue-progressbar' // import progress bar animation
import Swal from 'sweetalert2' // import sweet alert messages
import Gate from './Gates' // import Gate.js
import { 
  HasError,
  AlertError,
  AlertErrors, 
  AlertSuccess,
  Form,
} from 'vform'

const userType = window.user; // get auth user data
Vue.prototype.$gate = new Gate(userType) // make gate instance

const swal = require('sweetalert2') // define sweet alert
window.swal = swal; // make sweet alert global 

const Toast = Swal.mixin({ // swal config options for adding users
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 6000,
  onOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer)
    toast.addEventListener('mouseleave', Swal.resumeTimer)
  }
})

window.toast = Toast; // assign toast to global

window.Form = Form; // define new form package
Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)
Vue.component(AlertErrors.name, AlertErrors)
Vue.component(AlertSuccess.name, AlertSuccess)
// pagination component
Vue.component('pagination', require('laravel-vue-pagination'));

Vue.use(VueRouter); // use vueRouter

// make uppercase filter
Vue.filter('upperCase',function(text){
	return text.toUpperCase();
});
Vue.filter('FormatDate',function(date){
	return moment(date).format('MMMM Do YYYY');
});

 //make event instance
export const eventBus = new Vue();

// progress  bar config
const options = {
  color: 'green',
  failedColor: '#874b4b',
  thickness: '3px',
  transition: {
    speed: '0.9s',
    opacity: '0.5s',
    termination: 900
  },
  autoRevert: true,
  location: 'top',
  inverse: false
}
Vue.use(VueProgressBar, options) // use progress bar



// make vue router instance
const router = new VueRouter({ 
	mode:'history',
	routes: routes
});

const app = new Vue({
    el: '#app',
    router:router, // init vueRouter
    data:{
      search:''
    },
    methods:{
      searchit: _.debounce(()=>{
        eventBus.$emit('searching');
      },2000),
    }
});
