<template>
    <div class="app-container">
        <navigation></navigation>
        <div>
            <sidebar></sidebar>
            <div class="main-content inline-block">
                <router-view></router-view>
            </div>
        </div>
    </div>
</template>

<script>
    import nav from './../components/nav';
    import Sidebar from "./../components/sidebar";

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
        padding: 0px;
        margin-left: 160px;
        overflow-anchor: none;
    }

    @media(max-width:768px) {
        .app-container {
            height: 100vh;
        }
    }
</style>
