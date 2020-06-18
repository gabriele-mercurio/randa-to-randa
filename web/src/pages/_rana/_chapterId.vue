<template>
  <v-container>
    <h3 class="py-4">
      Capitolo: <span class="font-italic font-weight-light">{{ chapter.name }}</span>
    </h3>
    <h3 class="pb-4">
      Membri inizali: <span class="font-italic font-weight-light">3</span>
      Membri finali: <span class="font-italic font-weight-light">3</span>
    </h3>
    <Rana :rana="rana"/>
  </v-container>
</template>
<script>
import Utils from "../../services/Utils";
import ApiServer from "../../services/ApiServer";
import Rana from "../../components/Rana";

export default {
  components: {
    Rana
  },
  data() {
    return {
      chapter: {},
      rana: {
        id: "0c0030c9-a1cb-4b02-bd5d-5d7871809123",
        chapter: {
          chapterLaunch: {
            actual: null,
            prev: "2020-07-15"
          },
          closureDate: null,
          coreGroupLaunch: {
            actual: "2020-03-18",
            prev: "2020-01-24"
          },
          currentState: "CORE_GROUP",
          director: {
            fullName: "Utente 15",
            id: "13b607b6-cc34-4126-8d26-0c2612ebdbce"
          },
          id: "d747b3c7-6b0e-48aa-9e41-b433e7e8a14f",
          members: 101,
          name: "Chapter 10",
          resume: {
            actual: null,
            prev: null
          },
          suspDate: null
        },
        randa: {
          currentTimeslot: "T0",
          id: "8ecbe165-d9fe-4020-a054-edaf6622018d",
          region: {
            id: "5530017b-b398-4fd5-adde-a8599f3d4981",
            name: "Regione 10"
          },
          year: 2020
        }
      }
    };
  },
  created() {
    setTimeout(() => {
      let chapterId = this.$route.params.chapterId || false;
      this.fetchChapter(chapterId);
    });
  },
  methods: {
    async fetchChapter(chapterId) {
      if (!chapterId) {
        alert("Nessun capitolo specificato");
      }
      this.chapter = await ApiServer.get("chapter/" + chapterId);
      if (this.chapter.error) {
        alert("Errore nella get del capitolo");
      }
    },

    async fetchRana(chapterId) {
      //this.rana = ApiServer.get("rana");
    }
  }
};
</script>
