import Vue from 'vue';
import VueRouter from 'vue-router';

import Index from './pages/Index';
import Record from './pages/Record';

Vue.use(VueRouter);

const router = new VueRouter ({
    mode: 'history',
    scrollBehavior() {
        return { x: 0, y: 0 };
    },
    routes: [
        {
            path: '/',
            name: 'home',
            component: Index,
        },
        {
            path: '/photos',
            name: 'photos',
            component: () => import(/* webpackChunkName: "photos" */'./pages/Photos'),
        },
        {
            path: '/stations',
            name: 'stations',
            component: () => import(/* webpackChunkName: "stations" */'./pages/Stations'),
        },
        {
            path: '/maps',
            name: 'maps',
            component: () => import(/* webpackChunkName: "maps" */'./pages/Maps'),
        },
        {
            path: '/simpling-events',
            name: 'simpling-events',
            component: () => import(/* webpackChunkName: "simpling-events" */'./pages/SimplingEvents'),
        },
        {
            path: '/projects/:projectId/stations/:stationId/simpling-events',
            name: 'simpling-event',
            component: () => import(/* webpackChunkName: "simpling-event-group" */'./pages/SimplingEvent'),
        },
        {
            path: '/phylum',
            name: 'phylum',
            component: () => import(/* webpackChunkName: "phylum" */'./pages/Phylum'),
        },
        {
            path: '/class',
            name: 'class',
            component: () => import(/* webpackChunkName: "class" */'./pages/Class'),
        },
        {
            path: '/order',
            name: 'order',
            component: () => import(/* webpackChunkName: "order" */'./pages/Order'),
        },
        {
            path: '/family',
            name: 'family',
            component: () => import(/* webpackChunkName: "family" */'./pages/Family'),
        },
        {
            path: '/species',
            name: 'species',
            component: () => import(/* webpackChunkName: "species" */'./pages/Species'),
        },
        {
            path: '/occurrences',
            name: 'occurrences',
            component: () => import(/* webpackChunkName: "occurrences" */'./pages/Occurrences'),
        },
        {
            path: '/occurrences/:recordId',
            name: 'occurrence',
            component: () => import(/* webpackChunkName: "occurrence" */'./pages/Occurrence'),
        },
        {
            path: '/water-quality',
            name: 'water-quality',
            component: () => import(/* webpackChunkName: "water-quality" */'./pages/WaterQuality'),
        },
        {
            path: '/records/:recordId',
            name: 'record',
            component: () => import(/* webpackChunkName: "record" */'./pages/Record'),
        },
        {
            path: '/element-flux',
            name: 'element-flux',
            component: () => import(/* webpackChunkName: "element-flux" */'./pages/ElementFlux'),
        },
        {
            path: '/temperature',
            name: 'temperature',
            component: () => import(/* webpackChunkName: "temperature" */'./pages/Temperature'),
        },
        {
            path: '/algae-debris',
            name: 'algae-debris',
            component: () => import(/* webpackChunkName: "algaeDebris" */'./pages/AlgaeDebris'),
        },
        {
            path: '/river-discharge-estimation',
            name: 'river-discharge-estimation',
            component: () => import(/* webpackChunkName: "river-discharge-estimation" */'./pages/RiverDischargeEstimation'),
        },
        {
            path: '/river-habitats',
            name: 'river-habitats',
            component: () => import(/* webpackChunkName: "river-habitat" */'./pages/RiverHabitat'),
        },
        {
            path: '/river-sections',
            name: 'river-sections',
            component: () => import(/* webpackChunkName: "river-sections" */'./pages/RiverSections'),
        },
        {
            path: '/river-sections/:sectionId/:date',
            name: 'river-section',
            component: () => import(/* webpackChunkName: "river-section" */'./pages/RiverSection'),
        },
    ]
});

export default router;
