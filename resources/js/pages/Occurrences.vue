<template>
    <div class="px-4">
        <h6 class="page-title sticky left-[180px] w-[400px]">
            生物物種調查紀錄 <small class="text-muted">共 {{ total }} 筆</small>
        </h6>
        <div class="map-container">
            <l-map ref="map" :center="map.center" :options="{tap :false}" :zoom="map.zoom">
                <l-tile-layer :url="map.url"></l-tile-layer>
                <l-marker v-for="(marker, index) in map.markers"
                          :key="index"
                          :lat-lng="marker.location"
                          :name="marker.options.name" :options="marker.options"
                          v-on:click="currentMarker = marker.stationId"
                >
                    <l-popup>
                        <div v-if="currentMarker === marker.stationId">
                            <span class="font-bold leading-7">{{ marker.options.title }}</span>
                        </div>
                        <div v-else>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                    </l-popup>
                    <l-icon>
                        <img src="/images/marker.png">
                    </l-icon>
                </l-marker>
            </l-map>
        </div>
        <sheet
            v-model="sheetValues"
            :columns="columns"
            :data="occurrences"
            :is-loading="isLoading"
            v-on:search="search"
            v-on:sort="sort"
        >
            <template v-slot:functions="props">
                <router-link :to="`/records/${props.datum.record_id}?type=occurrence`" target="_blank">
                    內容
                </router-link>
            </template>
        </sheet>
        <div class="myexcel text-muted caption">
            生物物種調查紀錄&nbsp;共 {{ total }} 筆
        </div>
    </div>
</template>

<script>
import 'leaflet/dist/leaflet.css';
import { LMap, LTileLayer, LMarker, LIcon, LPopup } from 'vue2-leaflet';
import sheet from "../components/sheet";
import queryString from 'querystring';

export default {
    name: 'Occurrences',
    components: {
        sheet,
        LMap,
        LTileLayer,
        LMarker,
        LIcon,
        LPopup,
    },
    data() {
        return {
            map: {
                zoom: 12,
                center: [24.3601, 121.3146],
                url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                markers: [],
            },
            currentMarker: '',
            occurrences: [],
            isLoading: false,
            currentPage: 0,
            isEnd: false,
            perPage: 50,
            sortBy: '',
            direction: '',
            columns: [
                {type: 'text', title: '測站', width: '50', name: 'sid', searchable: true},
                {type: 'text', title: '門名', width: '120', name: 'phylum', searchable: true},
                {type: 'text', title: '綱名', width: '120', name: 'class', searchable: true},
                {type: 'text', title: '目名', width: '100', name: 'order', searchable: true},
                {type: 'text', title: '科名', width: '100', name: 'family', searchable: true},
                {type: 'text', title: '學名', width: '100', name: 'scientific_name', searchable: true},
                {type: 'text', title: '原紀錄學名', width: '100', name: 'record_use_name', searchable: true},
                {type: 'text', title: '中文名', width: '100', name: 'chinese', searchable: true},
                {type: 'text', title: '調查日 ', width: '100', name: 'date', searchable: true},
                {type: 'text', title: '地點 ', width: '100', name: 'locality_chinese', searchable: true},
                {type: 'text', title: '緯度 ', width: '80', name: 'latitude', searchable: true},
                {type: 'text', title: '經度 ', width: '80', name: 'longitude', searchable: true},
                {type: 'text', title: '調查者', width: '80', name: 'collector_chinese', searchable: true},
                {type: 'text', title: '調查法', width: '100', name: 'examine_way', searchable: true},
                {type: 'text', title: '鑒定者', width: '80', name: 'identified_by_chinese', searchable: true},
            ],
            total: 0,
            sheetValues: {
                searchParams: {}
            }
        }
    },
    created() {
        this.sheetValues.searchParams = this.$route.query;
    },
    mounted() {
        this.search();

        const app = this;
        const intersectionObserver = new IntersectionObserver(function (entries) {
            if (entries[0].intersectionRatio > 0) {
                app.loadMore();
            }
        });
        // Start observing
        intersectionObserver.observe(document.querySelector('.caption'));
    },
    methods: {
        onRemoveProjectIds() {
            const query = {...this.$route.query};
            delete query.projectIds;
            this.$router.replace({path: this.$route.path, query});
            delete this.sheetValues.searchParams.projectIds;
            this.search();
        },
        fetchData(callback) {
            if (this.isLoading || this.isEnd) {
                return;
            }
            this.isLoading = true;

            const page = this.currentPage + 1;

            this.$http.get(`/api/occurrences?page=${ page }&sort=${ this.sortBy }&direction=${ this.direction }&${ queryString.stringify(this.sheetValues.searchParams) }`)
                .then(({data: {data, total, currentPage, perPage}}) => {
                    if (perPage > data.length || 0 === data.length) {
                        this.isEnd = true;
                    }

                    callback(data);
                    this.total = total;
                    this.currentPage = currentPage;
                    this.isLoading = false;
                });
        },
        sort(column, direction) {
            this.sortBy = this.columns[column].name;
            this.direction = direction ? 'desc' : 'asc';
            this.search();
        },
        loadMore() {
            this.fetchData(data => {
                this.occurrences = this.occurrences.concat(data);
            })
        },
        fetchMarkers() {
            this.$http.get(`/api/occurrences-list-map?${ queryString.stringify(this.sheetValues.searchParams) }`)
                .then(({data: { data }}) => {
                    this.map.markers = data.map(d => {
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
        search() {
            window.scrollTo(0, 0);

            this.fetchMarkers();

            this.isEnd = false;
            this.currentPage = 0;
            this.occurrences = [];
            this.fetchData(data => {
                this.occurrences = data;
            })
        }
    }
}
</script>


<style lang="scss" scoped>
@import './../../sass/fix-table';

.map-container {
    width: calc(100vw - 200px);
    height: 300px;
    overflow: hidden;
    z-index: 0;
    position: sticky;
    left: 180px;
    .vue2leaflet-map {
        z-index: 0;
    }
}

.tag {
    background-color: gray;
    color: white;
    border-radius: 100px;
    padding: 2px 10px;

    span {
        cursor: pointer;
    }
}
</style>
