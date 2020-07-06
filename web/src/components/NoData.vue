<template>
    <div class="d-flex flex-column justify-center align-center pa-10">
        <v-img width="300px" :src="image"></v-img>
        <h3>{{message}}</h3>
    </div>
</template>
<script>
export default {
    data() {
        return {
            image: require('@/assets/images/undraw_no_data_qbuo.svg')
        }
    },
    props: {
        message: {
            default: ""
        }
    }
}
</script>