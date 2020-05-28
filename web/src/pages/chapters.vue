<template>
  <v-data-table
    :headers="headers"
    :items="chapters"
    :items-per-page="15"
    class="elevation-3"
  >
    <!-- <template v-slot:item.core_group_launch="{ item }">
      {{item}}
      <span>{{item.prev}}</span>
      <span>{{item.actual}}</span>
    </template>

    <template v-slot:item.chapter_launch="{ item }">
      <span>{{item.prev}}</span>
      <span>{{item.actual}}</span>
    </template> -->
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
        { text: "Core group", value: "core_group_launch" },
        { text: "Capitolo", value: "chapter_launch" },
        { text: "Azioni" }
      ],
      chapters: []
    };
  },
  middleware: "auth",
  created() {
    this.fetchChapters();
  },
  methods: {
    async fetchChapters() {
      this.chapters = await ApiServer.get("chapters");
      console.log(this.chapters);
      this.chapters = await Promise.resolve([
        {
          chapterLaunch: {
            prev: "01/02/2008",
            actual: "09/07/2020"
          },
          closureDate: "string",
          coreGroupLaunch: {
            prev: "09/07/2020",
            actual: "09/07/2020"
          },
          currentState: "PROJECT",
          director: {
            id: 0,
            fullName: "Luigi luigetti"
          },
          id: 0,
          members: 10,
          name: "Abn",
          suspDate: null,
          warning: null
        },
         {
          chapterLaunch: {
            prev: "01/02/2018",
            actual: "09/07/2020"
          },
          closureDate: null,
          coreGroupLaunch: {
            prev: "09/07/2020",
            actual: "09/07/2020"
          },
          currentState: "PROJECT",
          director: {
            id: 0,
            fullName: "Luigi poi"
          },
          id: 1,
          members: 100,
          name: "Saracap",
          suspDate: null,
          warning: null
        }
      ]);
    }
  }
};
</script>
