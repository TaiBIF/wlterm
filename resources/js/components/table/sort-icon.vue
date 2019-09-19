<template>
    <div class="sort-container" v-on:click="$emit('change-sort', target)">
        <i class="fas fa-sort" v-if="sort === '' || sort !== target"></i>
        <i class="fas fa-sort-up" v-if="sort === target && direction === 'asc'"></i>
        <i class="fas fa-sort-down" v-if="sort === target && direction === 'desc'"></i>
    </div>
</template>

<script>
    export default {
        name: 'sort-icon',
        props: {
            target: {
                required: true,
            },
            sort: {
                required: true,
            },
            direction: {
                default: 'asc'
            }
        }
    }
</script>

<style lang="scss" scoped>
    .sort-container {
        display: inline-block;
        cursor: pointer;
    }
</style>
