<template>
    
</template>

<script>
export default {

    props: {
        chapters: {
            type: Array,
            default: null
        }
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
    }
}
</script>