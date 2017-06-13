<template>
 <span :class="getClass()" v-text="labeltext" ></span>
</template>


<script>
    export default {
        name: 'RoleLabel',
        props: ['labelstyle', 'labeltext'],
        methods: {
            getClass() {
                return 'label label-' + this.labelstyle
            }
        }

    }
</script>