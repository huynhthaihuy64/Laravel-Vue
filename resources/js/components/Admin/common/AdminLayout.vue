<template>
  <div :class="bodyClasses()">
    <MainHeader />
    <div class="body-wrapper">
      <Sidebar class="sidebar" />
      <div class="content-wrapper">
        <router-view></router-view>
      </div>
    </div>
  </div>
</template>

<script>
import MainHeader from './Header.vue'
import Sidebar from './Sidebar.vue'

export default {
  components: { MainHeader, Sidebar },

  methods: {
    bodyClasses() {
      if(window.localStorage.getItem('darkMode') === 'true') {
        return "dark-mode"
      } else {
        return ""
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.body-wrapper {
  display: flex;
  gap: 17px;
  margin-top: 10px;
}

.sidebar {
  flex: 0 0 30%;
}

.content-wrapper {
  box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
  flex: 1;
  border-radius: 5px;
}

.dark-mode {
  background-color: #282c34;
  color: #e6e6e6;
}
</style>
