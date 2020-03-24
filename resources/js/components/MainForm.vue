<template>
    <b-form class="mya-request" @submit="onSubmit">
        <fieldset class="common-fields rounded">
            <h3 class="d-block text-center">Basic Info</h3>
            <b-form-group label-cols-md="3" label="Deployment:" label-size="lg"
            description="There are two independent Mya deployments to choose from. EPICS networks are
segregated to avoid channel name collisions. Most EPICS channels exist in the
operational network, however test stands and certain fiefdoms like the SRF
facility are in the developmental network.">
                <b-form-radio-group
                    v-model="form.deployment"
                    :options="formFields.deployments"
                    name="deployment"
                    buttons
                    button-variant="primary"
                ></b-form-radio-group>
            </b-form-group>

            <b-form-group label-cols-md="3" label="Requester:" label-size="lg">
                <b-form-input class="username" placeholder="JLab username" v-model="form.username"> </b-form-input>
            </b-form-group>

            <b-form-group label-cols-md="3" label="Request Type:" label-size="lg">
                <b-form-radio-group
                    v-model="form.requestType"
                    :options="formFields.requestTypes"
                    name="request-type"
                    buttons
                    button-variant="primary"
                ></b-form-radio-group>
            </b-form-group>

        </fieldset>

        <fieldset class="channel-selection rounded">
            <h3 class="d-block text-center">Channel Selection</h3>
            <b-form-group  label-cols-md="3" label="Method:" label-size="lg" >
                <b-form-radio-group
                    v-model="form.selectMethod"
                    :options="formFields.selectMethods"
                    name="select-methods"
                    buttons
                    button-variant="primary">
                </b-form-radio-group>
            </b-form-group>
            <b-form-group  label-cols-md="3" label="Channels" label-size="lg"
                :description="selectMethodDescription">


                <b-form-group class="channels" v-if="form.selectMethod =='form'">
                        <channel-widget   v-for="(item, index) in form.channels" v-model="form.channels[index]" key="index">
                        </channel-widget>
                    <b-button @click="addChannel" variant="outline-info" class=mb-2 title="add another channel">
                    <b-icon-plus></b-icon-plus>Channel
                    </b-button>
                </b-form-group>


                    <b-textarea v-if="form.selectMethod=='bulk'"
                                v-model="form.bulk"
                                :trim="true"
                                max-rows="10"
                    ></b-textarea>

                    <b-form-file v-if="form.selectMethod=='file'"
                        v-model="form.file"
                        placeholder="Choose a file or drop it here..."
                        drop-placeholder="Drop file here..."
                    ></b-form-file>


            </b-form-group>

        </fieldset>

        <fieldset class="channel-metadata rounded" v-if="form.requestType != 'change-deadbands'">
            <h3 class="d-block text-center">Channel Metadata</h3>
            <b-form-group  label-cols-md="3" label="Group Selection" label-size="lg"
            description="All Core Set channels must belong to an organizational archive group. You may
select an existing group name from the drop down list, or choose to suggest a new group name
when proposing the creation of a new archive group.">

                <v-select class="text-muted" v-model="form.group" v-if="! wantsNewGroup"
                          placeholder="start typing or scroll to search.." :options="archiverGroups"></v-select>
                <b-form-input  v-model="form.group" v-if="wantsNewGroup" placeholder="Group Name"></b-form-input>
                <b-form-checkbox class="new-group-toggle text-muted" v-model="form.newGroup" value="1" >
                   Request a New Group</b-form-checkbox>
            </b-form-group>


            <b-form-group  label-cols-md="3" label="Duration (weeks):" label-size="lg"
                           description="(optional) A duration that the entire request will last. After the specified
amount of time has passed, archiving of the channels will cease. Any previously
accumulated history will remain, but no more will be gathered."
            >
                <b-form-input
                    v-model="form.duration"
                    type="number"
                ></b-form-input>
            </b-form-group>

            <b-form-group  label-cols-md="3" label="Keep Span (weeks):" label-size="lg"
                           description="(optional) Specify a span of time for which archived data will be kept. Channel
history older than this span will continually be purged to free up disk space."
                           >

                <b-form-input
                    v-model="form.keep"
                    type="number"
                ></b-form-input>
            </b-form-group>


        </fieldset>

        <div class="btn-block">
            <b-button type="submit" variant="primary">
                Submit
            </b-button>
        </div>

    </b-form>
</template>

<script>
    import vSelect from 'vue-select'
    import ChannelWidget from "./ChannelWidget";

    export default {
        name: "MainForm",
        components: {
            vSelect,
            ChannelWidget
        },
        data() {
            return {
                form: {
                    deployment: 'OPS',
                    username: '',
                    requestType: 'add-channels',
                    selectMethod: 'form',
                    file: null,
                    bulk: '',
                    channels: [
                        {channel: '', deadband:''}
                    ],
                    group: null,
                    newGroup: false,
                    duration: null,
                    keep: null,

                },
                formFields: {
                    deployments: [
                        'OPS',
                        'DEV'
                    ],
                    requestTypes: [
                        {text: 'Add Channels', value: 'add-channels'},
                        {text: 'Change Deadbands', value: 'change-deadbands'},
                        {text: 'Change Metadata', value: 'change-metadata'},
                    ],
                    selectMethods: [
                        {text: 'Form Fields', value: 'form'},
                        {text: 'Bulk Edit', value: 'bulk'},
                        {text: 'File Upload', value: 'file'},
                    ]
                },
            }
        },
        computed: {
            selectMethodDescription(){
                switch (this.form.selectMethod){
                    case "form" : return "Enter channels (with optional deadbands)";
                    case "bulk" : return "Enter channels (followed by whitespace and optional deadband) one per line";
                    case "file" : return "Choose a plain text file containing channel names (followed by whitespace and optional deadband) one per line";
                    default : return ""
                }
            },
            wantsNewGroup(){
              return this.form.newGroup;
            },
            archiverGroups(){
                console.log(window.archiverGroups);
                return window.archiverGroups;
            }
        },
        methods: {
            onSubmit(evt) {
                evt.preventDefault()
                alert(JSON.stringify(this.form))
            },
            addChannel(){
                this.form.channels.push({channel:'', deadband:''});
            }
        }
    }
</script>

<style>
    .v-select {
        max-width: 55%;
        background-color: white;
    }

    .vs__search{
        color: darkgray;
    }
    .username {
        width: 12em;
    }
    .new-group-toggle {
        max-width: 30%;
        text-align: left;
        align-items: start;
        justify-content: left;
    }
    form.mya-request {
        width: 100%;
    }
    fieldset {
        padding: 0.5em;
        width: 90%;
    }
    .channels fieldset{
        padding-left: 0;
        margin-left: -0.5em;
    }
    .common-fields .btn-primary,
    .channel-selection .btn-primary {
        background-color: white;
        color: gray;
    }
    fieldset.channel-selection textarea {
        min-width: 80%;
    }
    fieldset.common-fields,
    fieldset.channel-selection,
    fieldset.channel-metadata{
        border: 1px solid black;
        width: 100%;
        margin-bottom: 1em;
    }
    input[type="number"] {
        max-width: 6em;
    }

</style>
