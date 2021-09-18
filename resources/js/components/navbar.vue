<template>
    <header class="blog-header py-3 shadow-md">
        <div class="flex justify-center">
            <div class="title-container">
                <img src="/images/logo.jpg" class="logo">
                <a class="blog-header-logo text-dark text-lg" href="#">
                    武陵地區生態監測及生態模式建立
                    <small>雪霸國家公園委託辦理計畫</small>
                </a>
            </div>
        </div>
    </header>
</template>
<style lang="scss" scoped>
    .blog-header-logo {
        vertical-align: middle;
    }
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
            display: inline-block;
            vertical-align: middle;
        }

        .title-container {
            width: 100%;
            text-align: center;
        }
    }
</style>
