<template>
        <v-select
            class="align-middle mb-2 mr-2 mb-sm-0 filters-select"
            v-model="selection"
            placeholder="type to search.."
            :options="users"
            :clearable="true"
            :filterable="false"
            :searchable="true"
            :clearSearchOnSelect="true"
            label="username"
            :multiple="multiple"
            @search="newQuery"
            @change="resetOptions"
        >
            <template slot="no-options">{{noOptionsText}}</template>

            <template v-slot:option="option">
                {{ formattedOption(option) }}
            </template>

        </v-select>

</template>

<script>
    /**
     * A component to select one or more users.
     *
     */
    import vSelect from 'vue-select';
    export default {
        name: "StaffSelect",
        props: {
            /**
             * Array of user objects containing id and username properties at a minimum.
             * @model
             */
            value: {default: null},
            /**
             * The name of a URL route to query
             */
            url: {type: String, default: '/data/staff'},
            /**
             * Whether to allow multiple selections or not.
             */
            multiple : {type: Boolean, default: false},
        },
        components: {
            vSelect
        },
        data(){
            return {
                query: '',
                users: [],
            }
        },
        computed:{
            selection: {
                get(){
                    return this.value;
                },
                set(v) {
                    this.$emit('input', v);
                }
            },
            noOptionsText(){
                if (this.query.length < 3){
                    return 'Start typing a name to see options.\n'
                }
                return 'Sorry, no matching users\n'
            }
        },
        methods:{
            // When the query value changes, fetch new results from
            // the API - in practice this action should be debounced
            newQuery(search, loading) {
                this.query = search
                if (search.length > 2){
                    loading(true);
                    axios.get(this.url,{params: {q: search}})
                        .then((res) => {
                            this.users = res.data.data  //fist data is axios, second is laravel
                            loading(false);
                        })
                }else{
                    this.users = [];
                    loading(false);
                }

            },
            formattedOption(option){
                if (option.firstname && option.lastname){
                    return `${option.firstname} ${option.lastname} (${option.username})`
                }
                return option.username
            },
            resetOptions(){
                this.users = []
                this.query = ''
                this.newQuery(this.query,  ()=>{})
            }

        }


    }
</script>

<style scoped>
.filters-select {
    min-width: 12em;
}
</style>
