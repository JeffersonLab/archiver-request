<template>
    <div>
        <b-btn variant="primary" @click="$bvModal.show('learn-more')">Learn more</b-btn>
        <b-modal id="learn-more" title="Archiver Resources">
            <p>Please use the resource links below to learn more about the mya archiver service:</p>
            <ul>
                <li>
                    <a target="_blank" href="http://devweb/controls_web/certified/archive/Mya_requests.pdf">
                        Core and User set discussion</a>
                </li>
                <li>
                    <a target="_blank" href="http://devweb/controls_web/certified/archive/Archive_user.pdf">
                        User set request tool</a>
                </li>
                <li>
                    <a target="_blank" href="http://devweb/controls_web/certified/mya/MyaDeadBands.pdf">
                        Dead Bands</a>
                </li>
            </ul>
        </b-modal>
    </div>
</template>

<script>
    export default {
        name: "LearnMore",
        methods: {

        }
    }
</script>

<style scoped>

</style>
