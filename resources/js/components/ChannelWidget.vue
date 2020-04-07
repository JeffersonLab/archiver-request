<template>
    <div class="channel-definition">
        <b-input-group prepend="channel:" class="d-inline-flex">
            <b-form-input  class="channel" v-model="value.channel"></b-form-input>
        </b-input-group>
        <b-input-group prepend="deadband:" class="d-inline-flex">
            <b-form-input class="deadband" v-model="value.deadband"></b-form-input>
        </b-input-group>
    </div>
</template>

<script>
    export default {
        name: "ChannelWidget",
        props: ['value']

    }
</script>

<style scoped>
    div.input-group{
        max-width: 45%;
    }
    .channel-definition{
        width: 100%;
    }
    input {
        display: inline-flex;
    }

    .channel {
        width: 20em;
    }

    .deadband {
        width: 8em;
    }
</style>
