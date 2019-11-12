/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */


require('./bootstrap');

window.Vue = require('vue');
require('bootstrap-material-design');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their 'basename'.
 *
 * Eg. ./components/Index.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));
import VueRouter from 'vue-router';

//FONT-AWESOME ICONS
import {library} from '@fortawesome/fontawesome-svg-core';
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import {fas} from '@fortawesome/free-solid-svg-icons';
import {far} from '@fortawesome/free-regular-svg-icons';
import {fab} from '@fortawesome/free-brands-svg-icons';

//DATE-PICKER
import datepicker from 'v-calendar/lib/components/date-picker.common';

//MOMENT
import VueMoment from 'vue-moment';
import moment from 'moment-timezone';

//SCROLLBARS
import {OverlayScrollbarsComponent} from 'overlayscrollbars-vue';

//MODAL
import VueJSModal from 'vue-js-modal';

//CUSTOM COMPONENTS
import News from './components/News/NewsIndex';
import Twitter from './components/Twitter/TwitterIndex';

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.use(VueRouter);
Vue.use(VueMoment, {
    moment
});
Vue.use(VueJSModal, {dialog: true});

const routes = [
    {path: '/', redirect: '/news'},
    {name: 'news', path: '/news/:id?', component: News, meta: {title: 'Новости'}},
    {name: 'twitter', path: '/twitter/:id?', component: Twitter, meta: {title: 'Twitter'}},
];

const router = new VueRouter({
    routes: routes,
});

//Set title of current page by route
router.beforeEach((to, from, next) => {
    document.title = to.meta.title || '';
    next();
});

library.add(fas);
library.add(far);
library.add(fab);

Vue.component('font-awesome-icon', FontAwesomeIcon);
Vue.component('overlay-scrollbars', OverlayScrollbarsComponent);
Vue.component('v-date-picker', datepicker);

const app = new Vue({
    el    : '#root',
    router: router
});
