import auth from "@/router/middlewares/auth.js";
import GMADashboard from "@/views/GMA/GMADashboard.vue";
import permission from "@/router/middlewares/permission.js";

export default [
	{
		path: "gma-dashboard",
		name: "gma-dashboard",
		component: GMADashboard,
		meta: {
			isAuth: true,
			middleware: [auth],
			title: "Tableau de bord / GM Affaires intérieures",
		},
	},
	{
		path: "gma-services",
		name: "gma-services",
		component: () => import("@/views/GMA/GMAServices.vue"),
		meta: {
			menuRef: "gma-services",
			isAuth: true,
			middleware: [auth, permission],
			permission: "browse-gma-vehicle",
			title: "GM Affaires intérieures / Services",
		},
	},
	{
		path: "gma-vehicle/:id",
		name: "gma-vehicle",
		props: true,
		component: () => import("@/views/GMA/GMAVehicle.vue"),
		meta: {
			menuRef: "gma-services",
			isAuth: true,
			middleware: [auth, permission],
			permission: "show-gma-vehicle",
			title: "GM Affaires intérieures / Véhicule",
		},
	},
];
