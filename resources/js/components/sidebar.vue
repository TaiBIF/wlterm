<template>
    <div class="blog-sidebar">
        <ul class="list-group">
            <li class="list-group-item">
                <router-link to="/">計畫簡介</router-link>
                <ul class="sub-menu">
                    <li>
                        <a class="text-dark" href="/#project">計畫緣由</a>
                    </li>
                    <li>
                        <a class="text-dark" href="/#project-target">計畫目標</a>
                    </li>
                    <li>
                        <a class="text-dark" href="/#research-station">研究測站</a>
                    </li>
                    <li>
                        <a class="text-dark" href="/#participants">參與人員</a>
                    </li>
                </ul>
            </li>

            <router-link to="/photos">
                <li class="list-group-item">
                    現象影像
                </li>
            </router-link>

            <router-link to="/stations">
                <li class="list-group-item">
                    測站資料
                </li>
            </router-link>
            <router-link to="/maps">
                <li class="list-group-item">
                    衛星地圖
                </li>
            </router-link>
            <router-link to="/phylum">
                <li class="list-group-item">
                    門別統計
                </li>
            </router-link>
            <router-link to="/class">
                <li class="list-group-item">
                    剛別統計
                </li>
            </router-link>
            <router-link to="/order">
                <li class="list-group-item">
                    目別統計
                </li>
            </router-link>
            <router-link to="/family">
                <li class="list-group-item">
                    科別統計
                </li>
            </router-link>
            <router-link to="/species">
                <li class="list-group-item">
                    物種統計
                </li>
            </router-link>
            <router-link to="/occurrences">
                <li class="list-group-item">
                    物種紀錄
                </li>
            </router-link>
            <router-link to="/water-quality">
                <li class="list-group-item">
                    水質調查
                </li>
            </router-link>
            <router-link to="/element-flux">
                <li class="list-group-item">
                    元素通量
                </li>
            </router-link>
            <router-link to="/temperature">
                <li class="list-group-item">
                    溫度監測
                </li>
            </router-link>
            <router-link to="/algae-debris">
                <li class="list-group-item">
                    藻類與碎屑
                </li>
            </router-link>
            <router-link to="/river-discharge-estimation">
                <li class="list-group-item">
                    流量推估
                </li>
            </router-link>
        </ul>
    </div>
</template>

<script>
    export default {
        name: 'Sidebar'
    }
</script>

<style lang="scss" scoped>
    .blog-sidebar {
        background: white;
        z-index: 10000;
        width: 160px;
        display: inline-block;
        position: fixed;
    }

    @media(min-width:768px) {
        .blog-sidebar {
            left: 0;
        }
    }

    .list-group {
        color: #77757a;
        height: 100vh;
        border-right: 1px #eee solid;
        .router-link-active.router-link-exact-active {
            color: #007bff;
        }
    }

    .list-group-item {
        padding: .125rem 1.5rem;
        border: 0;
        font-size: 0.95rem;
        line-height: 2rem;
    }

    a {
        color: #77757a;
        &:hover {
            text-decoration: none;
        }
    }

    ul {
        .sub-menu {
            padding: 0.125rem 1.5rem;
        }
    }

    li {
        .active {
            border-left: 3px red solid;
        }
        list-style-type: none;
    }
</style>
