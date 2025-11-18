import "./assets/css/theme.css";
import "./assets/js/theme.js";
import { createApp } from "vue";
import App from "./App.vue";
import { loadFonts } from "./plugins/webfontloader";
import router from "./router";
import { createPinia } from "pinia";
import { tooltip } from "@/helpers/tooltip";
import VueDayjS from "vue3-dayjs-plugin";

import * as Sentry from "@sentry/vue";

const app = createApp(App);
const isProduction = import.meta.env.PROD;

if (isProduction) {
	Sentry.init({
		app,
		dsn: "https://2f01fbdd3df1beb4830ae5db64cb82eb@o4504199028408320.ingest.us.sentry.io/4507737160810496",

		integrations: [Sentry.browserTracingIntegration({ router }), Sentry.replayIntegration()],

		// Set tracesSampleRate to 1.0 to capture 100%
		// of transactions for tracing.
		// We recommend adjusting this value in production
		tracesSampleRate: 1.0,

		// Set `tracePropagationTargets` to control for which URLs trace propagation should be enabled
		tracePropagationTargets: ["localhost", import.meta.env.VITE_AFFILIATE_URL],

		// Capture Replay for 10% of all sessions,
		// plus for 100% of sessions with an error
		replaysSessionSampleRate: 0.1,
		replaysOnErrorSampleRate: 1.0,
	});
}

loadFonts().then((r) => r);

app.use(createPinia()).use(router).use(VueDayjS).directive("tooltip", tooltip).mount("#app");
