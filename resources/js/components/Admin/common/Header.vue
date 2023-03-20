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
          <li class="nav-item ml-5" v-if="initialValue.role === 0">
            <router-link to="/admin" class="nav-link">{{ $store.getters.localizedStrings.admin }}</router-link>
          </li>
        </ul>
      </div>
    </div>
    <div class="col-4">
      <div class="dropdown w-50 d-flex justify-content-start w-100">
        <button @click="searchToggle()">Search<i class="zmdi zmdi-search ml-2"></i></button>
        <div id="myDropdown" class="dropdown-content scrollable-menu mt-4 w-100" role="menu">
          <input type="text" placeholder="Search.." v-model="searchText" id="myInput" @keyup="search()">
          <div class="content-dropdown container mt-4 w-100" v-for="(item) in searchArr" :key="item.id">
            <a @click="goToDetail(item.id)" class="w-100 content-dropdown ">
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
    <div class="d-flex text-black profile mr-5 col-2 d-flex justify-content-end">
      <div class="flex-shrink-0" v-if="initialValue.edit_avatar">
        <img :src="initialValue.edit_avatar" alt="Generic placeholder image" class="avatar" />
      </div>
      <div class="flex-shrink-0" v-if="!initialValue.edit_avatar">
        <img src="../../../../../public/images/avatar.png" alt="Generic placeholder image" class="image-contain avatar" />
      </div>
      <div class="info">
        <a-dropdown>
          <div class="ant-dropdown-link">
            <div class="name mt-3 ml-2">{{ initialValue.edit_name || 'Admin' }}</div>
          </div>
          <template #overlay>
            <a-menu>
              <a-menu-item class="menu-item ml-1">
                <router-link :to="{ name: 'profile' }">
                  <IconProfile />{{ $store.getters.localizedStrings.profile.title }}
                </router-link>
              </a-menu-item>
              <a-menu-item class="menu-item js-show-cart">
                <a @click="handleClick"><font-awesome-icon :icon="['fas', 'cart-shopping']" />Show Cart</a>
              </a-menu-item>
              <a-menu-item class="menu-item ml-1 menu-item-last" @click="handleLogout">
                <IconLogout />
                <div class="logout"><span> {{ $store.getters.localizedStrings.logout }} </span></div>
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
import { revokeUser, getUserInfo } from "../../../auth"
import httpRequest from '../../../axios'
export default {
  components: { IconProfile, IconLogout, IconLogo },
  props: {
    isActive: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      routeName: '',
      name: '',
      initialValue: {
        edit_name: '',
        edit_id: 1,
        edit_avatar: [],
        role: 1,
      },
      searchText: '',
      searchArr: {},
    }
  },
  computed: {
    userInfo() {
      const userInfo = JSON.parse(getUserInfo())
      return userInfo
    },
  },
  methods: {
    handleClick() {
      this.isActive = !this.isActive;
      this.$emit("updateActive", this.isActive);
    },
    searchToggle() {
      document.getElementById("myDropdown").classList.toggle("show");
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
        httpRequest.get('/api/admin/users/logout')
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
    goToDetail(id) {
      this.$router.push({ name: "productDetail", params: { id: id } });
    },

    getUser() {
      httpRequest
        .get('/api/admin/users/currentUser')
        .then((response) => {
          this.initialValue.edit_name = response.data.name;
          this.initialValue.edit_id = response.data.id;
          this.initialValue.edit_avatar = response.data.avatar;
          this.initialValue.role = response.data.admin;
        })
    },
  },
  mounted() {
    console.log('Component mounted.')
    this.getUser()
  },
  created() {
    this.routeName = this.$route.name
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

/* Dropdown Button */
.dropbtn {
  background-color: #04AA6D;
  color: white;
  padding: 16px;
  font-size: 16px;
  border: none;
  cursor: pointer;
}

/* Dropdown button on hover & focus */
.dropbtn:hover,
.dropbtn:focus {
  background-color: #3e8e41;
}

/* The search field */
#myInput {
  box-sizing: border-box;
  background-position: 14px 12px;
  background-repeat: no-repeat;
  font-size: 16px;
  padding: 14px 20px 12px 45px;
  border: none;
  border-bottom: 1px solid #ddd;
}

/* The search field when it gets focus/clicked on */
#myInput:focus {
  outline: 3px solid #ddd;
}

/* The container <div> - needed to position the dropdown content */
.dropdown {
  position: relative;
  display: inline-block;
  z-index: 999;
}

/* Dropdown Content (Hidden by Default) */
.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f6f6f6;
  min-width: 300px;
  border: 1px solid #ddd;
  z-index: 1;
}

/* Links inside the dropdown */
.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

/* Change color of dropdown links on hover */
.dropdown-content a:hover {
  background-color: #f1f1f1
}
.content-dropdown {
  max-height: 150px;
}
/* Show the dropdown menu (use JS to add this class to the .dropdown-content container when the user clicks on the dropdown button) */
.show {
  display: block;
}
</style>
