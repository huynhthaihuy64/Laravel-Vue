<template>
  <nav class="navbar navbar-expand-lg navbar-light header">
    <router-link to="/">
      <img src="../../../../../public/images/image.png" height="60px" width="80px">
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
          <router-link to="/admin" class="nav-link">{{ $store.getters.localizedStrings.admin }}</router-link>
        </li>

      </ul>
    </div>
    <div class="d-flex text-black profile mr-5">
      <div class="flex-shrink-0" v-if="initialValue.edit_avatar">
        <img :src="initialValue.edit_avatar" alt="Generic placeholder image" class="avatar" />
      </div>
      <div class="flex-shrink-0" v-if="!initialValue.edit_avatar">
        <img src="../../../../../public/images/avatar.png" alt="Generic placeholder image" class="avatar" />
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
</style>
