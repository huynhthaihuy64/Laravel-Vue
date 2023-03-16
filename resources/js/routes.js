import Vue from "vue";
import VueRouter from "vue-router";
import { getUserInfo, isAuthenticated, noAuth, permissionHome, middlewarePipeline} from "./auth";

Vue.use(VueRouter)

const routes = [
  {
    name: 'SignIn',
    path: '/signIn',
    component: require("./components/SignIn.vue").default,
    meta: {
      middleware: [noAuth]
    }
  },
  {
    name: 'Forgot',
    path: '/forgot',
    component: require("./components/ForgotPassword.vue").default,
    meta: {
      middleware: [noAuth]
    }
  },
  {
    name: 'Reset',
    path: '/reset/:token',
    component: require("./components/ResetPassword.vue").default,
    meta: {
      middleware: [noAuth]
    }
  },
  {
    name: 'SignUp',
    path: '/signUp',
    component: require("./components/SignUp.vue").default,
    meta: {
      middleware: [noAuth]
    }
  },
  {
    path: '/api/callback/:provider',
    component: {
      template: '<div class="auth-component"></div>'
    }
  },
  {
    path: '',
    component: require("./components/Admin/common/Layout.vue").default,
    meta: {
      middleware: [permissionHome],
    },
    redirect: {
      name: 'Home'
    },
    children: [
      {
        name: 'Home',
        path: '/',
        component: require("./components/HomeComponent.vue").default,
        meta: {
          middleware: [permissionHome],
        },
      },
      {
        name: 'profile',
        path: '/profile',
        component: require("./components/Admin/users/Profile.vue").default,
        meta: {
          middleware: [permissionHome],
        },
      },
      {
        name: 'cart',
        path: '/cart',
        component: require("./components/CartPage.vue").default,
        meta: {
          middleware: [permissionHome],
        },
      },
      {
        name: 'contact',
        path: '/contact',
        component: require("./components/Contact.vue").default,
        meta: {
          middleware: [permissionHome],
        },
      },
      {
        name: 'about',
        path: '/about',
        component: require("./components/AboutComponent.vue").default,
        meta: {
          middleware: [permissionHome],
        },
      },
      {
        name: 'productDetail',
        path: '/productDetail/:id',
        component: require("./components/ProductDetail.vue").default,
        meta: {
          middleware: [permissionHome],
        },
      },
    ]
  },
  {
    name: 'admin',
    path: '/admin',
    component: require("./components/Admin/common/AdminLayout.vue").default,
    meta: {
      middleware: [permissionHome],
    },
    children: [
      {
        name: 'users',
        path: 'users',
        component: require("./components/Admin/users/User.vue").default,
        meta: {
          middleware: [permissionHome],
        },
      },
      {
        name: 'menus',
        path: 'menus',
        component: require("./components/Admin/menus/Menu.vue").default,
        meta: {
          middleware: [permissionHome],
        },
      },
      {
        name: 'adminPage',
        path: '/admin',
        component: require("./components/Admin/Index.vue").default,
        meta: {
          middleware: [permissionHome],
        },
      },
      {
        name: 'products',
        path: 'products',
        component: require("./components/Admin/products/Product.vue").default,
        meta: {
          middleware: [permissionHome],
        },
      },
      {
        name: 'sliders',
        path: 'sliders',
        component: require("./components/Admin/sliders/Slider.vue").default,
        meta: {
          middleware: [permissionHome],
        },
      },
    ]
  },
]

const router = new VueRouter({
  mode: 'history',
  routes
})

router.beforeEach((to, from, next) => {
  if (!to.meta.middleware) {
    return next();
  }
  const user = getUserInfo();
  const isLoggedIn = isAuthenticated();
  const { middleware } = to.meta;
  const context = { to, from, next, user, isLoggedIn };
  return middleware[0]({
    ...context,
    next: middlewarePipeline(context, middleware, 1)
  });
});

export default router