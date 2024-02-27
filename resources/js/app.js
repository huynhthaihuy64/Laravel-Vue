import Antd from "ant-design-vue";
import "ant-design-vue/dist/antd.css";
import router from "./routes";
import store from "./store";
/*Import SocialAuth Vue */
import VueAuthenticate from "vue-authenticate";

/* import the fontawesome core */
import { library } from "@fortawesome/fontawesome-svg-core";

/* import the  progressbar*/
import VueProgressBar from "vue-progressbar";

/* import the  v-form*/
import { Form } from "vform";

/* import font awesome icon component */
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

/* import vue-sweetalert2*/
import Swal from "sweetalert2";

/* import specific icons */
import {
    faUserSecret,
    faChevronRight,
    faChevronLeft,
    faShop,
    faSliders,
    faListAlt,
    faDashboard,
    faRegistered,
    faFileExcel,
    faCartShopping,
    faUserTie,
    faMailForward,
    faMessage,
    faFileUpload,
    faDownload,
    faLink,
} from "@fortawesome/free-solid-svg-icons";
import {
    faTwitter,
    faFacebook,
    faStackOverflow,
    faGithub,
    faGoogle,
} from "@fortawesome/free-brands-svg-icons";
import Vue from "vue";
require("./bootstrap");

window.Vue = require("vue");

window.Form = Form;
var numeral = require("numeral");
Vue.filter("formatNumber", function (value) {
      if (!isNaN(value)) {
          // Sử dụng .toFixed(5) để định dạng số với tối đa 5 chữ số thập phân
          return numeral(value).format("0,0.[00000]");
      } else {
          // Nếu value không phải là số, trả về giá trị ban đầu
          return value;
      }
});

library.add(
    faUserSecret,
    faChevronRight,
    faChevronLeft,
    faShop,
    faSliders,
    faListAlt,
    faDashboard,
    faRegistered,
    faFileExcel,
    faCartShopping,
    faUserTie,
    faMailForward,
    faMessage,
    faFileUpload,
    faDownload,
    faLink
);
library.add(faTwitter, faFacebook, faStackOverflow, faGithub, faGoogle);
Vue.component("font-awesome-icon", FontAwesomeIcon);

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

var filter = function (text, length, clamp) {
    clamp = clamp || "...";
    var node = document.createElement("div");
    node.innerHTML = text;
    var content = node.textContent;
    return content.length > length ? content.slice(0, length) + clamp : content;
};

Vue.filter("truncate", filter);
// end Filter

/* Vue Auth */
Vue.use(VueAuthenticate, {
    baseUrl: "http://localhost:8083", // URL của Laravel backend
    providers: {
        google: {
            clientId:
                "163572698065-pi80unsfbobj2sf3tmr1n8nqm0pnjm0d.apps.googleusercontent.com",
            redirectUri: "http://localhost:8083/auth/google/callback", // URL của VueJS frontend
        },
    },
});

/* */
Vue.component(
    "example-component",
    require("./components/ExampleComponent.vue")
);

const app = new Vue({
    el: "#app",
    store,
    router,
});
