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
            is_national: false
        }
    },
  created() {
      setTimeout(() => {
          this.isNational();
      })
  },
  components: {
      NationalDashboard,
      StandardDashboard
  },
  methods: {
    async isNational() {
      this.is_national = await this.$store.getters["getRegion"].role === "NATIONAL";
    }
  }
};
</script>
