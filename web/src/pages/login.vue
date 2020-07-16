<template>
  <v-container class="primary pa-0 ma-0" id="container">
    <h2 class="white--text text-center py-12">Accedi a ROSBI</h2>
    <v-form v-if="!isLogged" @submit.prevent="doLogin()">
      <v-card class="mx-auto mb-12 elevation-12" max-width="374">
        <v-card-title class="secondary--text text-center">
          Login
        </v-card-title>
        <v-card-text class="my-4">
          <!-- <v-text-field
              label="Username"
              prepend-icon="mdi-face"
              color="purple"
            ></v-text-field> -->
          <v-text-field
            label="Email"
            prepend-icon="mdi-face"
            color="secondary"
            v-model="email"
          ></v-text-field>
          <v-text-field
            label="Password"
            prepend-icon="mdi-lock-outline"
            color="secondary"
            v-model="password"
            type="password"
          ></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-row justify="center">
            <v-btn type="submit" normal text color="primary"> Accedi </v-btn>
          </v-row>
        </v-card-actions>
      </v-card>
    </v-form>
    <v-form v-else @submit.prevent="selectRegion()">
      <v-card class="mx-auto mb-12 elevation-12" max-width="374">
        <v-card-title class="secondary--text text-center">
          Seleziona region
        </v-card-title>
        <v-card-text class="my-4">
          <v-select
            :items="regions"
            label="Seleziona region"
            v-model="region"
            @change="selectRegion()"
            item-text="name"
            return-object
          ></v-select>
        </v-card-text>
      </v-card>
    </v-form>

    <v-snackbar v-model="error" :timeout="timeout" top right>
      <v-icon color="primary">mdi-alert</v-icon>
      Username o password errate
      <v-btn color="white" icon @click="error = false">
        <v-icon>mdi-close</v-icon>
      </v-btn>
    </v-snackbar>
    <div class="d-flex justify-end"></div>
  </v-container>
</template>

<script>
import ApiServer from "../services/ApiServer";
import Utils from "../services/Utils";

export default {
  data() {
    return {
      email: "",
      password: "",
      error: false,
      regions: [],
      regionId: null,
      timeout: 3000,
      region: null,
      isLogged: false,
      logo: require("@/assets/images/logo_mercurio.png")
    };
  },
  created() {
    setTimeout(() => {
      if (this.getToken()) {
        this.isLogged = true;
        if (this.getRegion()) {
          this.$router.push("/home");
        } else {
          this.fetchRegions();
        }
      }
    });
  },
  methods: {
    async doLogin() {
      Utils.removeFromStorage("region");
      let response = await ApiServer.login(this.email, this.password);
      if (response["token"] && response["user"]) {
        if (
          true ||
          response["user"].id === "0fa8da5c-eebc-4502-b6bc-bae5826f9acd" ||
          response["user"].id === "30618bc8-e2f4-462b-a393-1756077903c8" ||
          response["user"].id === "d6bdb0db-bdfd-42bc-a1a0-235e9ea2c7a0" ||
          response["user"].id === "fbf43313-81e7-4b81-a5e6-b50bd9013f92" ||
          response["user"].id === "2af76638-9670-4a58-a1f7-d2e62ffa27f6" ||
          response["user"].id === "3156e792-0d6a-4aec-ae94-3ee532fd8196" ||
          response["user"].id === "4428daf5-eaa5-4959-a6b4-b3b3d283e782" ||
          response["user"].id === "21df95d0-998e-4384-98df-56a4cda1c151" ||
          response["user"].id === "3138b059-49f5-4958-8dc8-b37b8820c941" ||
          response["user"].id === "954ac12a-5cac-4561-baf7-c3f53b25f409" ||
          response["user"].id === "4275e8e7-d645-45e5-a0cc-83ce921dcebd" ||
          response["user"].id === "2105a634-7029-4532-9c0a-f86f45020d4a" ||
          response["user"].id === "8b077516-678c-461b-8978-60a77616785e" ||
          response["user"].id === "d8b1699c-5446-4b21-9646-ab024f10492c" ||
          response["user"].id === "0b983d83-9d85-4882-bf11-f0c841e0cdd6" ||
          response["user"].id === "e77cf2a4-d835-428e-bb6f-b8bcfa88e100" ||
          response["user"].id === "15e81b46-7480-4af8-90d9-db1364fc90e9" ||
          response["user"].id === "985ed75d-669d-48c9-8e49-2136eb603e9a" ||
          response["user"].id === "14caff0b-bcf9-419e-9602-cf05475beed6" ||
          response["user"].id === "010a40d4-9aeb-4e5d-b1cd-62a3da974976" ||
          response["user"].id === "3bedd260-a2ad-4b1e-a837-66cdf87d49b7" ||
          response["user"].id === "5bc81298-b18e-4a37-a6d3-6883d31561a8" ||
          response["user"].id === "08f4cae4-8ec6-4ef6-9802-57db761821a1" ||
          response["user"].id === "b4f172a2-b862-4f12-a15a-d173ce78229b" ||
          response["user"].id === "f78cd505-4ba6-40eb-ae1a-22bbbbe175c2" ||
          response["user"].id === "b23e43d4-4470-4c89-a3dd-24acd64802b5" ||
          response["user"].id === "31f8ed73-5e0c-44bd-bec3-109a6e36c86a" ||
          response["user"].id === "14caba61-d142-4cb5-8c21-9b4c67bd91cc" ||
          response["user"].id === "c966dd81-07ef-4466-b7f9-1400350963fa" ||
          response["user"].id === "f0a499b3-9096-47b1-8201-70747f4c462e" ||
          response["user"].id === "45d3fb2a-f597-419e-bb4a-bee6374a6c63" ||
          response["user"].id === "87a7c937-c9a5-48db-a42a-00b522e033d7" ||
          response["user"].id === "d67cbbcd-c17f-435f-aec2-5a44930f78ba" ||
          response["user"].id === "0e28397f-2a7e-41bd-ae62-72baf2aa9fd7" ||
          response["user"].id === "f483d2e5-7cae-4cf6-8ca5-971fe84b35a5" ||
          response["user"].id === "b2beccc3-5e02-427c-9bfd-3e5ee4beb880" ||
          response["user"].id === "ecaa3705-d778-4daa-9ac9-d0f5b71359e9" ||
          response["user"].id === "34761f97-6903-48e5-ac72-ab222d826008" ||
          response["user"].id === "e279a58c-ec70-492c-b3dd-764292572cef" ||
          response["user"].id === "aa8b63b9-3e09-4086-bc9a-5416f2836e54" ||
          response["user"].id === "974e8621-4a84-4347-8159-95f437e26dba" ||
          response["user"].id === "cfc35450-7177-4d7c-9393-1a86608ad8cc" ||
          response["user"].id === "14f8a354-fe38-455a-9ff5-763d2967712e" ||
          response["user"].id === "e72b60b6-99ee-42e9-945d-dbcbf99ad4f0" ||
          response["user"].id === "ff9b05f1-d8eb-4035-b607-5ffc1cb2e933" ||
          response["user"].id === "3c0a4540-c724-4ada-93b4-84f544c3b9b3" ||
          response["user"].id === "96ee8328-a382-4dd4-b87f-b2eac0bd93f0" ||
          response["user"].id === "a96823f2-e0d1-4275-a347-c7c620883ee7" ||
          response["user"].id === "0082aa30-a586-4fb0-8f01-45ef8d434926"
        ) {
          this.$store.commit("setUser", response["user"]);
          this.$store.commit("setToken", response["token"]);
          this.fetchRegions();
          this.isLogged = true;
        } else {
          this.error = true;
        }
      } else {
        this.error = true;
      }
    },

    getToken() {
      return this.$store.getters["getToken"];
    },
    getRegion() {
      return this.$store.getters["getRegion"];
    },

    goToHome() {
      this.$router.push({
        path: "/chapters"
      });
    },

    async fetchRegions() {
      this.regions = await ApiServer.get("regions");
    },

    selectRegion() {
      this.$store.commit("setRegion", this.region);
      this.goToHome();
    }
  }
};
</script>
<style scoped>
#container {
  width: 100%;
  max-width: 100%;
  height: 100%;
}
</style>
