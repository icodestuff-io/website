import { App, plugin } from '@inertiajs/inertia-vue'
import Vue from 'vue'
import {InertiaProgress} from "@inertiajs/progress";

// Since Inertia requests are made via XHR, there's no default browser loading indicator
// when navigating from one page to another. To solve this, Inertia provides an optional
// progress library, which shows a loading bar whenever you make an Inertia visit.
InertiaProgress.init();

Vue.use(plugin)

const el = document.getElementById('app')

new Vue({
    render: h => h(App, {
        props: {
            initialPage: JSON.parse(el.dataset.page),
            resolveComponent: name => require(`./Pages/${name}`).default,
        },
    }),
}).$mount(el)
