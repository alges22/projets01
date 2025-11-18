import auth from "@/router/middlewares/auth.js";
import GMDDashboard from "@/views/GMD/GMDDashboard.vue";
import permission from "@/router/middlewares/permission.js";

export default [
	{
		path: "gmd-dashboard",
		name: "gmd-dashboard",
		component: GMDDashboard,
		meta: {
			isAuth: true,
			middleware: [auth],
			title: "Tableau de bord / GM Diplomatie (Ministère)",
		},
	},
	{
		name: "gmd-services",
		path: "gmd-services",
		component: () => import("@/views/GMD/GMDServices.vue"),
		meta: {
			menuRef: "gmd-services",
			isAuth: true,
			middleware: [auth, permission],
			permission: "browse-gmd-vehicle",
			title: "GM Diplomatie (Ministère) / Services",
		},
	},
	{
		path: "gmd-vehicle/:id",
		name: "gmd-vehicle",
		props: true,
		component: () => import("@/views/GMD/GMDVehicle.vue"),
		meta: {
			menuRef: "gmd-services",
			isAuth: true,
			middleware: [auth, permission],
			permission: "show-gmd-vehicle",
			title: "GM Diplomatie / Véhicule",
		},
	},
];
