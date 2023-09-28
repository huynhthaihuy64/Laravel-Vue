<template>
  <a-row>
    <a-col :span="5" class="sidebar">
      <a-menu class="menu" :defaultSelectedKeys="[routeName]" @click="handleToRoute">
        <a-menu-item :key="item.key" v-for="item in menuItem">
          <font-awesome-icon :icon="item.icon" />
          <span class="text-wrap"> {{ item.desc }} </span>
        </a-menu-item>
      </a-menu>
      <footer class="footer">
        <div class="help">
          <a-icon type="question-circle" />
          <div class="help-desc">
            <span class="text"> {{$store.getters.localizedStrings.side_bar.help}}  </span>
            <i class="fas fa-external-link-alt"></i>
          </div>
        </div>
      </footer>
    </a-col>
  </a-row>
</template>

<script>

export default {
  data() {
    return {
      menuItem: [
        { key: 'adminPage', icon: ['fas', 'dashboard'], desc: 'DashBoard' },
        { key: 'users', icon: ['fas', 'user-secret'], desc: this.$store.getters.localizedStrings.side_bar.user },
        { key: 'menus', icon: ['fas', 'list-alt'], desc: this.$store.getters.localizedStrings.side_bar.menu  },
        { key: 'products', icon: ['fas', 'shop'], desc: this.$store.getters.localizedStrings.side_bar.product  },
        { key: 'sliders', icon: ['fas', 'sliders'], desc: this.$store.getters.localizedStrings.side_bar.slide  },
        { key: 'customers', icon: ['fas', 'user-tie'], desc: this.$store.getters.localizedStrings.side_bar.customer  },
        { key: 'uploadMultiple', icon: ['fas', 'file-upload'], desc: this.$store.getters.localizedStrings.side_bar.multipleFile  },
      ],
      routeName: null,
    }
  },
  created() {
    this.routeName = this.$route.name
  },
  methods: {
    handleToRoute(e) {
      if (this.$route.name === e.key) return
      this.$router.push({ name: e.key })
    },
  },
}
</script>

<style lang="scss" scoped>
.sidebar {
  border-radius: 5px;
  box-shadow: 0px 4px 4px rgb(0 0 0 / 25%);
  background-color: #fff;
  padding: 12px 10px;
  display: flex;
  flex-direction: column;
  position: fixed;
  left: 0;
  top: 70px;
  z-index: 99;
  height: 95%;
}

.menu {
  border: none;
  color: #0b68a2;
  flex: 1;

  .ant-menu-item {
    height: auto;
    border-radius: 7px;
    font-size: 16px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 12px;
    line-height: 20px;

    &:hover {
      background-color: #1280bf;
      color: #fff;

      .icon {
        fill: #fff;
      }
    }

    .icon {
      fill: #0b68a2;
    }
  }
}

.ant-menu:not(.ant-menu-horizontal) .ant-menu-item-selected {
  background-color: #1280bf;
  color: #fff;

  .icon {
    fill: #fff;
  }
}

.footer {
  min-height: 88px;
  padding-top: 19px;
  padding-bottom: 13px;

  // padding-left: 15px;
  // padding-right: 15px;
  &::before {
    content: '';
    display: block;
    background-color: #e9e9e9;
    height: 1px;
    width: 82%;
    position: relative;
    top: -19px;
    left: 50%;
    transform: translateX(-50%);
  }
}

.help {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;

  &-desc {
    cursor: pointer;
    font-size: 12px;
    color: #1976d2;
    display: flex;
    gap: 6px;
    align-items: center;

    .text {
      text-decoration: underline;
    }
  }
}</style>
