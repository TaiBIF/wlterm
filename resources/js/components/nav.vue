<template>
    <header class="blog-header py-3">
        <div class="row flex-nowrap justify-content-between align-items-center">
            <div class="title-container">
                <img src="/images/logo.jpg" class="logo">
                <a class="blog-header-logo text-dark" href="#">
                    武陵地區生態監測及生態模式建立
                    <small>雪霸國家公園委託辦理計畫</small>
                </a>
            </div>
        </div>
    </header>
</template>

<script>
    export default {
        name: 'navigation'
    }
</script>
<style lang="scss" scoped>
    .blog-header {
        overflow: hidden;
        position: sticky;
        background: white;
        top: 0;
        left: 0;
        width: 100vw;
        z-index: 99;
        .logo {
            width: 35px;
            height: 35px;
        }

        .title-container {
            width: 100%;
            text-align: center;
        }
    }

</style>
