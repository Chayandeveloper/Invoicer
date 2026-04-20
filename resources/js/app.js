import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { clerkPlugin } from '@clerk/vue';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(clerkPlugin, {
                publishableKey: import.meta.env.VITE_CLERK_PUBLISHABLE_KEY,
            })
            .mount(el);
    },
});