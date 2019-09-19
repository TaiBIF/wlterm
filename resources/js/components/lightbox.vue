<template>
    <div class="lightbox" v-if="isShow">
        <div class="modal show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="image-wrapper">
                        <img :src="url"/>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show" v-on:click="close"></div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "lightbox",
        props: {
            photos: {
                type: Array,
                default: [],
            }
        },
        data: () => {
            return {
                index: 0,
                isShow: false,
            }
        },
        computed: {
            url: function () {
                return this.photos[this.index];
            }
        },
        methods: {
            close() {
                this.isShow = false;
            },
            show(index) {
                this.isShow = true;
                this.index = index;
            }
        }
    }
</script>

<style lang="scss" scoped>
    .show {
        display: block;
    }

    .modal-dialog {
        z-index: 1050;
        width: fit-content;
        max-width: 100vw;
    }
</style>
