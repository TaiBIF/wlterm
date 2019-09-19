<template>
    <div class="app-container">
        <navigation></navigation>
        <sidebar></sidebar>
        <div class="main-content">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    import nav from '@/js/components/nav';
    import Sidebar from "@/js/components/sidebar";

    export default {
        name: 'App',
        components: {
            Sidebar,
            'navigation': nav,
        }
    }
</script>

<style lang="scss" scoped>
    .app-container {
        display: inline-block;
    }

    .main-content {
        padding: 0px 12px;
        margin-left: 165px;
        overflow-anchor: none;
        display: inline-block;
    }

    @media(max-width:768px) {
        .app-container {
            height: 100vh;
        }
    }
</style>
