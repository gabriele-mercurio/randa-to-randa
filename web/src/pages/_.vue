<template></template>
<script>
export default {
  created() {
    setTimeout(() => {
      this.$router.push("home");
    });
  }
};
</script>
