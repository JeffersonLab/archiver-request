<template>
    <v-jstree :data="data" show-checkbox @item-click="itemClick"></v-jstree>
</template>

<script>
    import VJstree from 'vue-jstree'
    export default {
        name: "GroupsTree",
        props: ['data', 'value'],
        components: {
            VJstree,
        },
        methods: {
            itemClick(node,item,e){
                console.log(item.path);
            }
        }
    }
</script>

<style>
div.tree-anchor:hover{
    background-color: lightgray;
    cursor: pointer;
}

</style>
