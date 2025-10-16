import './bootstrap';

import Vue from 'vue';
window.Vue = require('vue').default;

import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue'
Vue.use(BootstrapVue);
Vue.use(BootstrapVueIcons);


import MainForm from "./components/MainForm";
import LearnMore from "./components/LearnMore";

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    components: { MainForm, LearnMore },

});
