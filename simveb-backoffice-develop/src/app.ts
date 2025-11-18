import { createApp as createClientApp } from "vue";

import { createHead } from "@vueuse/head";
import { createPinia } from "pinia";
import createRouter from "./router";
import App from "./App.vue";
import "./styles";
import "vue-select/dist/vue-select.css";
import client from "/@src/composable/axiosClient";
import VSelect from "vue-select";
import VueApexCharts from "vue3-apexcharts";
import VueDayjS from "vue3-dayjs-plugin";

import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

export type VueroAppContext = Awaited<ReturnType<typeof createApp>>;
export type VueroPlugin = (vuero: VueroAppContext) => void | Promise<void>;

const plugins = import.meta.glob<{ default: VueroPlugin }>("./plugins/*.ts", {
	eager: true,
});

// this is a helper function to define plugins with autocompletion
export function definePlugin(plugin: VueroPlugin) {
	return plugin;
}

export async function createApp() {
	const app = createClientApp(App);
	const router = createRouter;

	const head = createHead();
	app.use(head);

	const pinia = createPinia();
	app.use(pinia).use(VueDayjS);
	/* const options = {
    //confirmButtonColor: '#41b882',
    //cancelButtonColor: '#ff7674',
  };*/

	const vuero = {
		app,
		client,
		router,
		head,
		pinia,
	};

	// app.config.globalProperties.$dayjs = dayjs;

	app.provide("vuero", vuero);
	app.component("VSelect", VSelect);
	app.component(VueApexCharts);
	app.component("VueDatePicker", VueDatePicker);

	for (const path in plugins) {
		try {
			const { default: plugin } = plugins[path];
			await plugin(vuero);
		} catch (error) {
			console.error(`Error while loading plugin "${path}".`);
			console.error(error);
		}
	}

	// use router after plugin registration, so we can register navigation guards
	app.use(router);

	return vuero;
}
