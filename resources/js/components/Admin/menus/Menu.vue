<template>
  <div class="category-wrapper">
    <Content />
  </div>
</template>
  
<script>
import Content from './Content.vue'

export default {
  components: {
    Content,
  },
}
</script>
<style lang="scss" scoped>
.category-wrapper {
  display: flex;
  gap: 16px;
  padding-right: 12px;
  font-size: 16px;
  height: calc(100vh - 110px);
}
</style>