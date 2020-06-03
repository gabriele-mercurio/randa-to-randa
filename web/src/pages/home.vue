<template>
  <v-row class="d-flex">
      <v-col cols="5">
        <v-card class="elevation-13">
        <v-card-title>Lista capitoli</v-card-title>
        <v-card-text>
            <ChaptersList :short="true"/>
        </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="3">
    <v-card>
      <v-card-title>Composizione capitoli</v-card-title>
      <v-card-text>
        <ChaptersStats />
      </v-card-text>
    </v-card>
      </v-col>
  </v-row>
</template>
<script>
import ChaptersList from "../components/ChaptersList";
import ChaptersStats from "../components/ChaptersStats";

export default {
  components: {
    ChaptersStats,
    ChaptersList
  },
};
</script>
