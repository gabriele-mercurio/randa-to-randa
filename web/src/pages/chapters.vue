<template>
  <v-data-table
    :headers="headers"
    :items="chapters"
    :items-per-page="15"
    class="elevation-3"
  >
    <template v-slot:item.core_group_launch="{ date }">
      <span>{{date.prev}}</span>
      <span>{{date.actual}}</span>
    </template>

    <template v-slot:item.chapter_launch="{ date }">
      <span>{{date.prev}}</span>
      <span>{{date.actual}}</span>
    </template>
  </v-data-table>
</template>

<script>
import ApiServer from "../services/ApiServer";
export default {
  data() {
    return {
      headers: [
        { text: "Nome", value: "name" },
        { text: "Stato", value: "current_state" },
        { text: "Direttore", value: "director.firstname" },
        { text: "Membri", value: "members" },
        { text: "Core group" , value: "core_group_launch"},
        { text: "Capitolo", value: "chapter_launch"},
        { text: "Azioni" }
      ],
      chapters: []
    };
  },
  created() {
    alert("ciao");d
    this.fetchChapters();
  },
  methods: {
    async fetchChapters() {
      let test = await ApiServer.get("chapters?region=1");
      console.log(test);
      debugger;
    }
  }
};
</script>
