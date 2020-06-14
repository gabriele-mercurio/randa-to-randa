<template>
  <span class="circle" :class="color"></span>
</template>

<script>
export default {
  props: {
    color: {
      type: String,
      default: "red"
    }
  }
};
</script>
<style lang="scss" scoped>
@import "../assets/variables";

.circle {
  width: 20px;
  height: 20px;
  border-radius: 50%;
}
.circle.red {
  background-color: $lightred;
}
.circle.green {
  background-color: $lightgreen;
}
.circle.yellow {
  background-color: $lightyellow;
}
.circle.grey {
  background-color: $lightgrey;
}
</style>
