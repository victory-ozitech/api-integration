import "./bootstrap";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";

const pages = import.meta.glob("./Pages/**/*.vue");

createInertiaApp({
    resolve: (name) =>
        pages[`./Pages/${name}.vue`]().then((module) => module.default),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});
