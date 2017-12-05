<template>
    <span :class="getClass()" v-text="role.display_name" ></span>
</template>


<script>
    export default {
        name: 'RoleLabel',
        props: {
            role: {
              type: Object,
              default: null
            }
        },
        methods: {
            getClass() {
                return 'label label-' + this.role.style
            }
        }

    }
</script>