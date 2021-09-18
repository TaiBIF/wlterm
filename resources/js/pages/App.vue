<template>
    <div class="app-container">

        <div>
            <navbar></navbar>
            <sidebar></sidebar>
            <div class="main-content inline-block">
                <router-view></router-view>
            </div>
        </div>
    </div>
</template>

<script>
    import Navbar from "./../components/navbar";
    import Sidebar from "./../components/sidebar";

    export default {
        name: 'App',
        components: {
            Sidebar,
            Navbar,
        }
    }
</script>

<style lang="scss" scoped>
    @import './../../sass/app';

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
