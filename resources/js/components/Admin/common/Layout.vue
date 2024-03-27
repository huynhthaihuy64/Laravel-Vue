<template>
  <div :class="bodyClasses()">
    <MainHeader :is-active="isActive" v-on:updateActive="updateActive" />
    <div class="body-wrapper">
      <div class="content-wrapper mx-0 px-0">
        <router-view :user="user"></router-view>
      </div>
      <Cart v-if="isActive === true" :is-active="isActive" v-on:updateActive="updateActive" class="cart"/>
    </div>
    <!-- <button id=" fixedButton" class="bg-primary" @click="toggleChatBox">
          <img class="image-chat" src="../../../../../public/images/Chatbox-PR.png">
          </button> -->
    <MainFooter />
    <!-- <ChatBox v-if="showChatBox"></ChatBox> -->
  </div>
</template>

<script>
import MainHeader from './Header.vue'
import MainFooter from './Footer.vue'
import Cart from '../../Cart.vue'
import httpRequest from '../../../axios'
// import ChatBox from './ChatBox.vue'
export default {
  components: { MainHeader, MainFooter, Cart },
  data() {
    return {
      user: {},
      isActive: this.isActive,
      // showChatBox: false,
    }
  },
  methods: {
    // Toggle Chatbox
    // toggleChatBox() {
    //   this.showChatBox = !this.showChatBox;
    //   this.$store.dispatch('toggleConfirmModal', true); // Thêm dòng này
    // },
    updateActive(value) {
        this.isActive = value;
    },
    bodyClasses() {
      if(window.localStorage.getItem('darkMode') === 'true') {
        return "dark-mode"
      } else {
        return ""
      }
    },
    getUser() {
      httpRequest
        .get('/api/admin/users/currentUser')
        .then((response) => {
          this.user = response.data
        })
    },
  },
  mounted() {
    this.getUser();
    this.bodyClasses();
  }
}
</script>
  
<style lang="scss" scoped>
.body-wrapper {
  display: flex;
  gap: 17px;
}

.content-wrapper {
  box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
  flex: 1;
  position: relative;
  z-index:1;
  border-radius: 5px;
  margin-right: 17px;
}

.dark-mode {
  background-color: #282c34;
  color: #e6e6e6;
}

#fixedButton {
  position: fixed;
  bottom: 100px;
  right: 20px;
  z-index: 9999;
  display: block;
  transition: opacity 0.3s ease;
}

#fixedButton.show {
  opacity: 1;
}

.image-chat {
  border-radius: 20%;
  height:100px;
  width: 100px;
  object-fit: cover;
}

.cart {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 2;
}
</style>
  