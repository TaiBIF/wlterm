<template>
    <div class="row">
        <div class="map-container">
            <l-map :zoom="zoom" :center="center" ref="map">
                <l-tile-layer :url="url"></l-tile-layer>
                <l-marker :lat-lng="marker.location"
                          :options="marker.options"
                          :name="marker.options.name"
                          v-for="(marker, index) in markers" :key="index">
                    <l-popup>
                        {{ marker.options.title }}
                        <br/>查看:
                        <router-link :to="`/occurrences?locality_chinese=${marker.options.title}`">
                            <i class="fas fa-external-link-alt"></i>
                        </router-link>
                    </l-popup>
                    <l-icon>
                        <img src="/images/marker.png">
                    </l-icon>
                </l-marker>
            </l-map>
        </div>
    </div>
</template>

<script>
    import 'leaflet/dist/leaflet.css';
    import { LMap, LTileLayer, LMarker, LIcon, LPopup } from 'vue2-leaflet';
    export default {
        name: "Maps",
        components: {
            LMap,
            LTileLayer,
            LMarker,
            LIcon,
            LPopup,
        },
        data() {
            return {
                zoom: 13,
                center: [24.3601, 121.3146],
                url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                markers: [],
            };
        },
        mounted() {
            this.$refs.map.mapObject.invalidateSize();
            this.$http.get('/api/stations-location').then(res => {
                this.markers = res.data.map(d => {
                    return {
                        icon: L.icon('/images/marker.png'),
                        location: [parseFloat(d.latitude), parseFloat(d.longitude)],
                        options: {
                            name: d.locality,
                            title: d.locality_chinese,
                            riseOnHover: true,
                            onClick: this.clickMarker
                        }
                    };
                });
            });
        },
    }
</script>

<style scoped>
    .map-container {
        width: calc(100vw - 160px);
        height: calc(100vh - 67px);
    }
</style>
