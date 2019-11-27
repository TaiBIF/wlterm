<template>
    <div>
        <lightbox ref="lightbox" :photos="photos"></lightbox>
        <div v-for="(photo, index) in photos" class="photo-wrapper">
            <img :src="`/images/${photo}`"
                 class="photo"
                 v-on:click="() => { showLightbox(index) }"
            />
        </div>
    </div>
</template>

<script>
    import lightbox from '@/js/components/lightbox';
    export default {
        name: "Photos",
        components: {
            lightbox,
        },
        data() {
            return {
                photos: [],
            }
        },
        created() {
            this.$http.get('/api/photos')
                .then(({ data }) => {
                    this.photos = data.data;
                });
        },
        methods: {
            showLightbox(index) {
                this.$refs.lightbox.show(index);
            }
        }
    }
</script>

<style lang="scss" scoped>
    .photo-wrapper {
        display: inline-block;
        width: 25%;
        cursor: pointer;
        .photo {
            width: 100%;
            height: 180px;
            padding: 4px;
            object-fit: cover;
        }
    }
</style>
