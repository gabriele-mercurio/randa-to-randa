<template>
<v-container>
    <v-card class="mx-auto mb-12 elevation-12" max-width="374">
      <v-card-title class="secondary--text text-center">
        Seleziona regione
      </v-card-title>
      <v-card-text class="my-4">
        <v-select
          :items="regions"
          label="Seleziona region"
          v-model="region"
          @change="selectRegion()"
          item-text="name"
        ></v-select>
      </v-card-text>
    </v-card>
  </v-container>

  
</template>

<script>
import ApiServer from "../services/ApiServer";

export default {
  data() {
    return {
      region: null,
      regions: [],
    };
  },
  created() {
      this.fetchRegions();
  },
  methods: {
    selectRegion() {
      localStorage.setItem("region", this.region.name);
      this.$router.push({
        path: "/home"
      });
    },

    async fetchRegions() {
      this.regions = await Promise.resolve([
          {
            name: "Sardegna"
          },
          {
            name: "Abruzzo"
          }
        ]);
    }
  }
};
</script>
