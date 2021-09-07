<template>
    <div class="row">
        <div class="map-container">
            <div class="tool-bar" v-if="Object.keys($route.query).length === 0">
                <div class="field">
                    年份:
                    <select v-on:change="onUpdateDate">
                        <option value="">請選擇</option>
                        <option :value="year" v-for="year in yearOptions">{{year}}</option>
                    </select>
                </div>
                <div class="field">
                    調查項目:
                    <select v-on:change="onUpdateProjectIds">
                        <option>請選擇</option>
                        <option value="5">濱岸植群研究</option>
                        <option value="6,18,24">水棲昆蟲研究</option>
                        <option value="7">陸棲昆蟲研究</option>
                        <option value="9,20">兩生類研究</option>
                        <option value="10,19">魚類研究</option>
                        <option value="11,21">鳥類研究</option>
                    </select>
                </div>
            </div>
            <l-map :zoom="zoom" :center="center" ref="map">
                <l-tile-layer :url="url"></l-tile-layer>
                <l-marker :lat-lng="marker.location"
                          :options="marker.options"
                          :name="marker.options.name"
                          v-for="(marker, index) in markers" :key="index">
                    <l-popup>
                        {{ marker.options.title }}
                        <br/>查看:
                        <router-link :to="`/occurrences?locality_chinese=${marker.options.title}&date=${params.date}&projectIds=${params.projectIds}`">
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
                yearOptions: [],
                params: {
                    date: '',
                    projectIds: [],
                }
            };
        },
        mounted() {
            this.$refs.map.mapObject.invalidateSize();
            this.$http.get('/api/occurrences-years')
                .then(({data: { years }}) => {
                    this.yearOptions = years;
                });

            this.onRefreshMap();
        },
        methods: {
            onRefreshMap() {
                let urlParams = new URLSearchParams(window.location.search);
                urlParams.append('project_ids', this.params.projectIds);
                urlParams.append('date', this.params.date);
                this.$http.get(`/api/stations-location?${urlParams.toString()}`).then(res => {
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
            onUpdateDate(e) {
                if (e.target.value === '') {
                    this.params.date = '';
                } else {
                    this.params.date = e.target.value;
                }
                this.onRefreshMap();
            },
            onUpdateProjectIds(e) {
                if (e.target.value === '') {
                    this.params.projectIds = [];
                } else {
                    this.params.projectIds = e.target.value.split(',');
                }

                this.onRefreshMap();
            }
        }
    }
</script>

<style lang="scss" scoped>
    .map-container {
        width: calc(100vw - 160px);
        height: calc(100vh - 67px);
    }
    .tool-bar {
        padding: 8px;

        .field {
            margin-right: 10px;
            display: inline-block;
        }
    }
</style>
