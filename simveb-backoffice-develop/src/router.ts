import { createRouter, createWebHistory } from "vue-router";
import { useUserSession } from "./stores/userSession";
import NotFoundVue from "./pages/errors/NotFound.vue";
import middlewarePipeline from "/@src/routes/middlewares/middlewarePipeline";
import authRoutes from "/@src/routes/auth";
import { useHead } from "@vueuse/head";
import { useViewWrapper } from "/@src/stores/viewWrapper";
import AdminLayout from "/@src/layouts/AdminLayout.vue";
import Cookies from "js-cookie";
import spaceConfig from "../space-config";
import auth from "/@src/routes/middlewares/auth";
import { Notyf } from "notyf";
import guest from "/@src/routes/middlewares/guest";
import homeVue from "/@src/pages/home.vue";
import configsRoutes from "/@src/routes/config";
import affiliateRoutes from "/@src/routes/affiliate";
import routes from "/@src/routes/routes";
import permission from "/@src/routes/middlewares/permission";
import configsVue from "/@src/pages/configs.vue";

const APP_NAME = import.meta.env.VITE_APP_NAME;
const notyf = new Notyf();

const router = createRouter({
	history: createWebHistory(),
	linkActiveClass: "active",
	routes: [
		{
			path: "/",
			name: "home",
			component: AdminLayout,
			meta: {
				middleware: [auth],
			},
			redirect: { name: "admin_home" },
			children: [
				{
					path: "",
					name: "admin_home",
					component: homeVue,
					meta: {
						// middleware: [auth],
						permission: "dashboard",
						title: "Tableau de bord",
					},
				},
				{
					path: "config",
					children: [
						{
							path: "",
							name: "config",
							component: configsVue,
							meta: {
								middleware: [auth, permission],
								permission: "access-config",
								pageTitle: "Configuration",
							},
						},
						...configsRoutes,
					],
				},
				...affiliateRoutes,
				...routes,
			],
		},
		{
			path: "/:pathMatch(.*)*",
			name: "not-found",
			component: NotFoundVue,
		},
		...authRoutes,
	],
	// handle scroll behavior between routes
	scrollBehavior: (to, from, savedPosition) => {
		// Scroll to heading on click
		if (to.hash) {
			if (to.hash === "#") {
				return {
					top: 0,
					behavior: "smooth",
				};
			}

			const el = document.querySelector(to.hash);

			// vue-router does not incorporate scroll-margin-top on its own.
			if (el) {
				const top = parseFloat(getComputedStyle(el).scrollMarginTop);
				if (el instanceof HTMLElement) {
					el.focus();
				}

				return {
					el: to.hash,
					behavior: "smooth",
					top,
				};
			}

			return {
				el: to.hash,
				behavior: "smooth",
			};
		}

		// Scroll to top of window
		if (savedPosition) {
			return savedPosition;
		} else if (to.path !== from.path) {
			return { top: 0 };
		}
	},
});

router.beforeEach(async (to, from, next) => {
	if (!to.meta.middleware) {
		return next();
	}
	const middleware = to.meta.middleware;
	const store = useUserSession();
	const token = Cookies.get("token");

	if (!store.user && to.meta.middleware.includes(auth)) {
		if (token) {
			await store.fetchUser().then(() => {
				const online_profile = store.online_profile.type.code;
				if (spaceConfig[online_profile] !== window.location.origin) {
					if (import.meta.env.VITE_COOKIE_DOMAIN !== "localhost") {
						window.open(spaceConfig[online_profile], "_self");
					}
				}
				return next();
			});
		} else {
			notyf.error("Vous devez être connecté pour accéder à cette page.");
			if (!to.meta.middleware.includes(guest)) {
				return next({ name: "login" });
			}
		}
	}

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
	if (to.meta.pageTitle) {
		useHead({
			title: `${to.meta.pageTitle} | ${APP_NAME}`,
		});
		const viewWrapper = useViewWrapper();
		viewWrapper.setPageTitle(to.meta.pageTitle);
	}
});

export default router;
