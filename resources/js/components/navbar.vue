<template>
    <header class="blog-header py-3 shadow-md">
        <div class="flex justify-center">
            <div class="flex flex-1 justify-center">
                <div class="flex items-center">
                    <img src="/images/logo.jpg" class="logo">
                    <a class="blog-header-logo text-dark text-lg" href="#">
                        武陵地區生態監測及生態模式建立
                        <small>雪霸國家公園委託辦理計畫</small>
                    </a>
                </div>
            </div>
            <div class="flex w-[50px] items-center text-lg">
                <a href='https://wlterm-intra.biodiv.tw' >
                    登入
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
<script lang="ts">
</script>
