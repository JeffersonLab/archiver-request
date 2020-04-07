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

<style>
    .channel-definition{
        width: 100%;
    }
    .channel-definition .input-group{
        max-width: 45%;
    }


    .channel-definition .form-control.channel {
        flex-basis: 60%;
    }

    .channel-definition .form-control.deadband {
        flex-basis: 30%;
    }
</style>
