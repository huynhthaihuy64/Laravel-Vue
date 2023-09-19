import Antd from "ant-design-vue";
import "ant-design-vue/dist/antd.css";
import router from "./routes";
import store from './store'
/*Import SocialAuth Vue */
import VueSocialauth from 'vue-social-auth';

/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'

/* import the  progressbar*/
import VueProgressBar from "vue-progressbar";

/* import the  v-form*/
import { Form } from "vform";

/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

/* import vue-sweetalert2*/
import Swal from "sweetalert2";

/* import specific icons */
import { faUserSecret, faChevronRight, faChevronLeft, faShop, faSliders, faListAlt, faDashboard, faRegistered, faFileExcel, faCartShopping, faUserTie, faMailForward, faMessage } from '@fortawesome/free-solid-svg-icons'
import { faTwitter, faFacebook, faStackOverflow, faGithub, faGoogle } from '@fortawesome/free-brands-svg-icons'
import Vue from "vue";
require("./bootstrap");

window.Vue = require("vue");

window.Form = Form;
var numeral = require("numeral");
Vue.filter("formatNumber", function (value) {
  return numeral(value).format("0,0"); // displaying other groupings/separators is possible, look at the docs
});

library.add(faUserSecret, faChevronRight, faChevronLeft, faShop, faSliders, faListAlt, faDashboard, faRegistered, faFileExcel, faCartShopping, faUserTie, faMailForward, faMessage)
library.add(faTwitter, faFacebook, faStackOverflow, faGithub, faGoogle)
Vue.component('font-awesome-icon', FontAwesomeIcon)

const Toast = Swal.mixin({
  toast: true,
  position: "top-end",
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  onOpen: (toast) => {
    toast.addEventListener("mouseenter", Swal.stopTimer);
    toast.addEventListener("mouseleave", Swal.resumeTimer);
  },
});
window.Swal = Swal;
window.Toast = Toast;

Vue.use(Antd);
Vue.use(VueProgressBar, {
  color: "rgb(143, 255, 199)",
  failedColor: "red",
  height: "3px",
});
Vue.config.productionTip = false;

var filter = function(text, length, clamp){
  clamp = clamp || '...';
  var node = document.createElement('div');
  node.innerHTML = text;
  var content = node.textContent;
  return content.length > length ? content.slice(0, length) + clamp : content;
};

Vue.filter('truncate', filter);
// end Filter

/* Vue Auth */
Vue.use(VueSocialauth, {
  providers: {
      google: {
          clientId: '867907508492-bgr2og6orrollalmo3vrb5j8mvm0c20a.apps.googleusercontent.com',
          client_secret: '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',
          redirectUri: 'http://localhost:8083/api/callback/google'
      }
  }
});

/* */
Vue.component(
  'example-component', 
  require('./components/ExampleComponent.vue')
);

const app = new Vue({
  el: "#app",
  store,
  router,
});