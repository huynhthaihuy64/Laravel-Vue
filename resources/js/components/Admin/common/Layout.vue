<template>
  <div :class="bodyClasses()">
    <MainHeader :is-active="isActive" v-on:updateActive="updateActive" />
    <div class="body-wrapper">
      <div class="content-wrapper">
        <router-view></router-view>
      </div>
      <Cart v-if="isActive === true" :is-active="isActive" v-on:updateActive="updateActive"/>
    </div>
    <MainFooter />
  </div>
</template>
  
<script>
import MainHeader from './Header.vue'
import MainFooter from './Footer.vue'
import Cart from '../../Cart.vue'

export default {
  components: { MainHeader, MainFooter, Cart },
  data() {
    return {
      isActive: this.isActive,
    }
  },
  methods: {
    updateActive(value) {
        this.isActive = value;
    },
    bodyClasses() {
      if(window.localStorage.getItem('darkMode') === 'true') {
        return "dark-mode"
      } else {
        return ""
      }
    }
  },
  mounted() {
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
  border-radius: 5px;
  margin-right: 17px;
}

.dark-mode {
  background-color: #282c34;
  color: #e6e6e6;
}
</style>
  