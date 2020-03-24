<template>
<div>
    <b-form-input class="channel" placeholder="channel" v-model="value.channel"></b-form-input>
    <b-form-input class="deadband" placeholder="deadband" v-model="value.deadband"></b-form-input>
</div>
</template>

<script>
    export default {
        name: "ChannelWidget",
        props: ['value']

    }
</script>

<style scoped>

input {
    margin-right: 1em;
    display: inline-block;
}
.channel {
    width: 20em;
}

.deadband {
    width: 8em;
}
</style>
