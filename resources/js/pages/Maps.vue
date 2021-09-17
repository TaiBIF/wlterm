<template>
    <div>
        <div class="map-container">
            <l-map :zoom="zoom" :center="center" ref="map">
                <l-tile-layer :url="url"></l-tile-layer>
                <l-marker :lat-lng="marker.location"
                          :options="marker.options"
                          :name="marker.options.name"
                          v-for="(marker, index) in markers" :key="index"
                          v-on:click="currentMarker = marker.stationId"
                >
                    <l-popup>
                        <m-marker v-if="currentMarker === marker.stationId" :date="params.date" :marker="marker"></m-marker>
                        <div v-else>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                    </l-popup>
                    <l-icon>
                        <img src="/images/marker.png">
                    </l-icon>
                </l-marker>
            </l-map>
        </div>
        <div class="tool-bar py-3" v-if="Object.keys($route.query).length === 0">
            <div class="field font-bold">
                年份:
                <select class="mx-2 ring-1 ring-1 ring-gray-200" v-on:change="onUpdateDate">
                    <option value="">請選擇</option>
                    <option :value="year" v-for="year in yearOptions">{{year}}</option>
                </select>
            </div>
            <div class="field font-bold">
                調查項目:
                <select class="mx-2 ring-1 ring-1 ring-gray-200" v-on:change="onUpdateProjectIds">
                    <option>請選擇</option>
                    <option value="3,4,13,17,23">水質調查</option>
                    <option value="13">元素通量</option>
                    <option value="14">溫度監測</option>
                    <option value="1,15">藻類與碎屑</option>
                    <option value="5">濱岸植群研究</option>
                    <option value="6,18,24">水棲昆蟲研究</option>
                    <option value="7">陸棲昆蟲研究</option>
                    <option value="9,20">兩生類研究</option>
                    <option value="10,19">魚類研究</option>
                    <option value="11,21">鳥類研究</option>
                </select>
            </div>
        </div>
    </div>
</template>

<script>
    import 'leaflet/dist/leaflet.css';
    import { LMap, LTileLayer, LMarker, LIcon, LPopup } from 'vue2-leaflet';
    import MMarker from './../components/Marker';

    export default {
        name: "Maps",
        components: {
            LMap,
            LTileLayer,
            LMarker,
            LIcon,
            LPopup,
            MMarker,
        },
        data() {
            return {
                zoom: 13,
                center: [24.3601, 121.3146],
                url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                markers: [],
                yearOptions: [],
                currentMarker: '',
                markerInfo: 'asdfasd',
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
                            stationId: d.id,
                            icon: L.icon('/images/marker.png'),
                            location: [parseFloat(d.latitude), parseFloat(d.longitude)],
                            meta: d,
                            options: {
                                name: d.locality,
                                title: d.locality_chinese,
                                riseOnHover: true,
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
        height: calc(100vh - 4.2rem);
        overflow: hidden;
    }
    .tool-bar {
        padding: 8px;
        width: 100%;
        position: fixed;
        bottom: 0;
        z-index: 999;
        background-color: #ffffff69;

        .field {
            margin-right: 10px;
            display: inline-block;
        }
    }
</style>
