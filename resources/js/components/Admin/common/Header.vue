<template>
  <nav class="navbar navbar-expand-lg navbar-light header">
    <div class="col-6 d-flex justify-content-center">
      <router-link to="/">
        <img src="../../../../../public/images/image.png" height="60px" width="80px" class="image-contain">
      </router-link>
      <button class="navbar-toggler ml-auto" type="button" data-toggle="collapse" data-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item ml-5">
            <router-link to="/" class="nav-link">{{ $store.getters.localizedStrings.shop }}</router-link>
          </li>
          <li class="nav-item ml-5">
            <router-link to="/about" class="nav-link">{{ $store.getters.localizedStrings.about }}</router-link>
          </li>
          <li class="nav-item ml-5">
            <router-link to="/contact" class="nav-link">{{ $store.getters.localizedStrings.contact }}</router-link>
          </li>
          <li class="nav-item ml-5">
            <div class="dropdown w-50 d-flex justify-content-center w-100">
              <button @click="listCategory()" class="nav-link">Category</button>
              <div id="myCategory" class="dropdown-category scrollable-menu mt-5 w-25" role="menu">
                <div class="content-dropdown container mt-4 w-100 mb-2" v-for="(item) in menus" :key="item.id">
                  <a @click="goToMenuDetail(item.id)" class="w-100 content-dropdown ">
                    {{ item.name }}
                  </a>
                </div>
              </div>
            </div>
          </li>
          <li class="nav-item ml-5" v-if="(user.role_id === 1 || user.role_id === 2) && user">
            <router-link to="/admin" class="nav-link">{{ $store.getters.localizedStrings.admin }}</router-link>
          </li>
          <li class="nav-item ml-5" v-if="user">
            <a-space>
              <a-select :value="currency" ref="select" style="width: 120px" @change="handleChange">
                <a-select-option v-for="(item, index) in listCurrency" :value="item.code" :key="index">{{ item.code
                  }}</a-select-option>
              </a-select>
            </a-space>
          </li>
        </ul>
      </div>
    </div>
    <div class="col-3">
      <div class="dropdown w-50 d-flex justify-content-center w-100">
        <button @click="searchToggle()">Search<i class="zmdi zmdi-search ml-2"></i></button>
        <div id="myDropdown" class="dropdown-content scrollable-menu mt-4 w-100" role="menu">
          <input type="text" placeholder="Search.." v-model="searchText" id="myInput" @keyup="search()">
          <div class="content-dropdown container border border-primary mt-4 w-100" v-for="(item) in searchArr"
            :key="item.id">
            <a @click="goToDetail(item.id)" class="w-100 content-dropdown-item ">
              <div class="col-12">
                <div class="row">
                  <div class="col-6">
                    <img :src="item.file" class="image-contain h-50 w-100">
                  </div>
                  <div class="col-6">
                    <b class="ml-4">{{ item.name }}</b><br />
                    <p class="ml-4 mt-4">{{ item.price_sale }}</p>
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
    <div id="google_translate_element" class="col-1 mt-3"></div>
    <div class="d-flex text-black profile mr-5 col-2 d-flex justify-content-end">
      <div class="flex-shrink-0" v-if="user.avatar">
        <img :src="user.avatar" alt="Generic placeholder image" class="avatar" />
      </div>
      <div class="flex-shrink-0" v-else>
        <img src="../../../../../storage/app/public/avatar/reynolds.immanuel.jpg" alt="Generic placeholder image"
          class="image-contain avatar" />
      </div>
      <div class="info">
        <a-dropdown>
          <div class="ant-dropdown-link">
            <div class="name mt-3 ml-2">{{ user.name || 'Guess' }}</div>
          </div>
          <template #overlay>
            <a-menu v-if="user">
              <a-menu-item class="menu-item ml-1">
                <router-link :to="{ name: 'profile' }">
                  <IconProfile />{{ $store.getters.localizedStrings.profile.title }}
                </router-link>
              </a-menu-item>
              <a-menu-item class="menu-item js-show-cart" style="position: relative;">
                <a @click=" handleClick"><font-awesome-icon :icon="['fas', 'cart-shopping']" />
                  <span class="bubble">{{ countCarts }}</span>
                  Show Cart</a>
              </a-menu-item>
              <a-menu-item class="menu-item ml-1 menu-item">
                <a @click="handleChat"><font-awesome-icon :icon="['fas', 'message']" />Chat</a>
              </a-menu-item>
              <a-menu-item class="menu-item ml-1 menu-item-last" @click="handleLogout">
                <IconLogout />
                <div class="logout"><span> {{ $store.getters.localizedStrings.logout }} </span></div>
              </a-menu-item>
            </a-menu>
            <a-menu v-else>
              <a-menu-item class="menu-item ml-1">
                <router-link :to="{ name: 'SignIn' }">
                  <IconProfile />Login
                </router-link>
              </a-menu-item>
              <a-menu-item class="menu-item ml-1">
                <router-link :to="{ name: 'SignUp' }">
                  <IconProfile />Register
                </router-link>
              </a-menu-item>
            </a-menu>
          </template>
        </a-dropdown>
      </div>
    </div>
  </nav>
</template>
  
<script>
import IconProfile from "../../icon/icon-profile.vue";
import IconLogout from "../../icon/icon-logout.vue"
import IconLogo from "../../icon/icon-logo.vue";
import { revokeUser, isAuthenticated } from "../../../auth"
import httpRequest from '../../../axios'
import { onMounted, reactive, ref } from "vue";
import { EventBus } from '../../../eventBus';
export default {
  components: { IconProfile, IconLogout, IconLogo },
  props: {
    isActive: {
      type: Boolean,
      default: false
    },
  },
  setup() {
    const user = reactive({ 
      name: null,
      id: null,
      avatar: null,
      role_id: null 
    });

    const countCarts = ref(0);

    if (isAuthenticated()) {
      async function getUser() {
        try {
          const response = await httpRequest.get('/api/admin/users/currentUser');
          user.name = response.data.data.name;
          user.id = response.data.data.id;
          user.avatar = response.data.data.avatar;
          user.role_id = response.data.data.role_id;
        } catch (error) {
          console.error('Error fetching user:', error);
        }
      }

      async function countCart() {
        try {
          const response = await httpRequest.get('/api/carts');
          countCarts.value = response.data.carts.length;
        } catch (error) {
          console.error('Error fetching carts:', error);
        }
      }

      async function updateCart() {
        EventBus.$on('update-cart', () => {
          countCart()
        });
      }
      onMounted(() => {
        getUser();
        countCart();
        updateCart();
      });
    }
    return { user, countCarts }
  },
  data() {
    return {
      routeName: '',
      menus: {},
      listCurrency: {},
      name: '',
      searchText: '',
      searchArr: {},
      currency: ''
    }
  },
  methods: {
    handleClick() {
      this.isActive = !this.isActive;
      this.$emit("updateActive", this.isActive);
    },
    searchToggle() {
      document.getElementById("myDropdown").classList.toggle("show");
    },
    listCategory() {
      document.getElementById("myCategory").classList.toggle("show");
    },
    search() {
      httpRequest.post('/api/products/search', {
        name: this.searchText
      })
        .then((response) => {
          this.searchArr = response.data;
        });
    },
    async handleLogout() {
      try {
        httpRequest.post('/api/admin/users/logout')
          .then(
            ({ response }) => {
              Toast.fire({
                icon: 'success',
                title: 'Logout ' + this.$store.getters.localizedStrings.success,
              });
              revokeUser()
              this.$router.push({ name: 'SignIn' })
            });
      }
      catch (error) {
        Toast.fire({
          icon: 'error',
          title: this.$store.getters.localizedStrings.error,
        });
      }
    },
    handleChat() {
      this.$router.push({ name: "listFriend" });
      this.$router.go();
    },
    goToDetail(id) {
      this.$router.push({ name: "productDetail", params: { id: id } });
      this.$router.go();
    },

    goToMenuDetail(id) {
      const url = `/categories/${id}`;
      window.location.href = url;
    },
    getMenu() {
      httpRequest
        .get('/api/menus/list')
        .then(
          ({ data }) => (
            (this.menus = data.data)
          )
        );
    },
    getCurrency() {
      httpRequest
        .get('/api/currency/current-currency')
        .then(
          ({ data }) => (
            (this.currency = data.currency)
          )
        );
    },
    getListCurrency() {
      httpRequest
        .get('/api/currency')
        .then(
          ({ data }) => (
            (this.listCurrency = data.currency)
          )
        );
    },
    handleChange(value) {
      httpRequest.post('/api/currency', {
        currency: value
      })
        .then(response => {
          this.info = response
          Toast.fire({
            icon: 'success',
            title: 'Change Currency Success'
          });
          setTimeout(function () {
            window.location.reload();
          }, 2000);
        }).catch(function (error) {
          Toast.fire({
            icon: 'error',
            title: 'Change Currency Error'
          });
          setTimeout(function () {
            window.location.reload();
          }, 2000);
        });
    },
  },
  mounted() {
    if (isAuthenticated()) {
      this.getCurrency()
      this.getListCurrency()
    }
    this.getMenu()
  },
  created() {
    this.$Progress.start()
    this.routeName = this.$route.name
    this.$Progress.finish()
  }
}
</script>
  
<style lang="scss" scoped>
.menu {
  margin-right: 30px;
}

.menu-item {
  padding: 8px 15px 10px 15px;
  display: flex;
  font-size: 12px;
  font-weight: 700;
  line-height: calc(16 / 12);

  a {
    margin: unset !important;
    padding: unset !important;
  }

  svg {
    margin-right: 11px;
  }

  &-last {
    padding: 12px 15px 14px 15px;
    border-top: 0.5px solid #efefef;
  }
}

.avatar {
  height: 50px;
  border-radius: 60%;
}

.navbar {
  position: static;
  z-index: 99;
  top: 0;
  left: 0;
  right: 0;
}

.ant-dropdown-link {
  cursor: pointer;
}

#myInput {
  box-sizing: border-box;
  background-position: 14px 12px;
  background-repeat: no-repeat;
  font-size: 16px;
  padding: 14px 20px 12px 45px;
  border: none;
  border-bottom: 1px solid #ddd;
}

#myInput:focus {
  outline: 3px solid #ddd;
}

.dropdown {
  position: relative;
  display: inline-block;
  z-index: 999;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f6f6f6;
  min-width: 300px;
  border: 1px solid #ddd;
  z-index: 1;
}

.dropdown-category {
  display: none;
  position: absolute;
  background-color: #f6f6f6;
  min-width: 200px;
  border: 1px solid #ddd;
  z-index: 1;
}

.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

.dropdown-content a:hover {
  background-color: #f1f1f1
}

.content-dropdown {
  max-height: 150px;
}

.content-dropdown-item {
  max-height: 145px;
}

.content-dropdown.container:hover {
  border-color: transparent;
  /* Đặt màu border khi hover thành transparent để không hiển thị */
}

.show {
  display: block;
}

.bubble {
  position: absolute;
  top: -5px;
  /* Điều chỉnh vị trí theo ý của bạn */
  right: -10px;
  /* Điều chỉnh vị trí theo ý của bạn */
  background-color: red;
  /* Màu sắc của bong bóng */
  color: white;
  border-radius: 50%;
  /* Làm cho bong bóng tròn */
  width: 20px;
  height: 20px;
  text-align: center;
  line-height: 20px;
  /* Để chữ hiển thị ở giữa */
  font-size: 15px;
  /* Kích thước của chữ */
}
</style>
