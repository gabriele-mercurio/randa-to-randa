<template>
     <line-chart
      :chartdata="chapters" />
</template>

<script>
import LineChart from './DonutChart.vue'

export default {

    data() {
        return {
            headers: {

            },
            chapters: {}
        }
    },
    components: {
        LineChart
    },
    whatch: {
        chapters: {
            "handler": function(oldVal, newVal) {
                debugger;
            },
            immediate: true
        }
    },
    created() {
        this.chapters = {
            project: 34,
            core_group: 13,
            chapter: 90,
            closed:0,
            suspended: 5
        }
    }
}
</script>