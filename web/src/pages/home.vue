<template>
  <NationalDashboard v-if="is_national" />
  <StandardDashboard v-else />
</template>



<script>
import NationalDashboard from "../components/NationalDashboard";
import StandardDashboard from "../components/StandardDashboard";
export default {
    data() {
        return {
            is_national: true
        }
    },
  created() {
      setTimeout(() => {
          this.isNational();
      }, 2000);
  },
  components: {
      NationalDashboard,
      StandardDashboard
  },
  methods: {
    async isNational() {
      this.is_national = await this.$store.getters["getIsNational"];
      debugger;
    }
  }
};
</script>
