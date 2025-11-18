import { createRouter, createWebHistory } from "vue-router";
import BasicLayout from "@/layouts/BasicLayout.vue";
import { useTitle } from "@vueuse/core";
import { useAuthStore } from "@/stores/auth.js";
import auth from "@/router/middlewares/auth.js";
import middlewarePipeline from "@/router/middlewares/middlewarePipeline.js";
import { redirectMappedDashboard, redirectMappedLayout } from "@/router/middlewares/dashboardRolesMapper.js";
import authRoutes from "@/router/routes/auth";
import affiliateRoutes from "@/router/routes/affiliat";
import approvedRoutes from "@/router/routes/approved";
import centralGarageRoutes from "@/router/routes/central_garage.js";
import policeRoutes from "@/router/routes/police";
import interpolRoutes from "@/router/routes/interpol";
import commonRoutes from "@/router/routes/common";
import SampleLayout from "@/layouts/SampleLayout.vue";
import auctioneer from "@/router/routes/auctioneer.js";
import gma from "@/router/routes/gma.js";
import gmd from "@/router/routes/gmd.js";
import bank from "@/router/routes/bank.js";
import distributor from "./routes/distributor";
import court from "@/router/routes/court.js";

const APP_NAME = import.meta.env.VITE_APP_NAME;

const router = createRouter({
	history: createWebHistory(),
	linkExactActiveClass: "side-menu--active",
	scrollBehavior(to, from, savedPosition) {
		if (savedPosition) {
			return savedPosition;
		} else {
			return { top: 0 };
		}
	},
	routes: [
		{
			path: "/",
			meta: {
				// permission: "sig",
			},
			name: "root",
			component: BasicLayout,
			children: [
				{
					path: "",
					name: "home",
					meta: {
						middleware: [auth],
					},
					components: {
						sidebar: redirectMappedLayout,
						default: SampleLayout,
					},
					children: [
						{
							path: "",
							name: "dashboard",
							redirect: redirectMappedDashboard(),
						},
						...affiliateRoutes,
						...approvedRoutes,
						...policeRoutes,
						...interpolRoutes,
						...centralGarageRoutes,
						...commonRoutes,
						...auctioneer,
						...gma,
						...gmd,
						...bank,
						...distributor,
						...court,
					],
				},
			],
		},
		...authRoutes,
	],
});

router.beforeEach(async (to, from, next) => {
	if (!to.meta.middleware) {
		return next();
	}
	const middleware = to.meta.middleware;
	const store = useAuthStore();
	/*if (!store.user && to.meta.middleware.includes(auth, redirectIfLoggedIn)) {
		if (Cookies.get("token")) {
			await store.fetchUser();
		} else {
			Alert.warn("Vous devez être connecté pour accéder à cette page.");
			if (!to.meta.middleware.includes(guest)) {
				return next({ name: "login" });
			}
		}
	}*/

	const context = {
		to,
		from,
		next,
		store,
	};
	return middleware[0]({
		...context,
		next: middlewarePipeline(context, middleware, 1),
	});
});

router.afterEach((to) => {
	const title = useTitle();
	if (to.meta.title) {
		title.value = `${to.meta.title} | ${APP_NAME}`;
	}
});

export default router;
