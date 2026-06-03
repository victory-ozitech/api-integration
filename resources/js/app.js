import "./bootstrap";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";

import { createApp, h } from "vue";
import { router, Head } from "@inertiajs/vue3";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import { createInertiaApp, Link } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";



// ✅ ADD THIS HERE
window.asset = (path) => {
    return `/${path.replace(/^\/+/, '')}`;
};

// const pages = import.meta.glob("./Pages/**/*.vue");

const appName = import.meta.env.VITE_APP_NAME || "Social Arkive";

createInertiaApp({
    // resolve: (name) =>
    //     pages[`./Pages/${name}.vue`]().then((module) => module.default),
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue"),
        ),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) });

        vueApp
            .use(plugin)
            .use(ZiggyVue)
            .component("Link", Link)
            .component("Head", Head)
            .mount(el);

        // Make Inertia router available globally
        vueApp.config.globalProperties.$router = router;
        window.router = router;

        // 🧠 Set document.title from Laravel's props.title if available
        router.on("navigate", () => {
            const titleFromProps =
                vueApp.config.globalProperties.$page?.props?.title;
            if (titleFromProps) {
                document.title = `${titleFromProps} - ${appName}`;
            } else {
                document.title = appName; // fallback
            }
        });
    },
    progress: {
        color: "#118A53",
    },
});
