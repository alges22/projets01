import auth from "@/router/middlewares/auth.js";
import BlackListVehicles from "@/views/Interpol/BlackListVehicules.vue";
import PendingBlacklistVehicles from "@/views/Interpol/PendingBlacklistVehicules.vue";
import permission from "@/router/middlewares/permission.js";

export default [
	{
		path: "interpol-dashboard",
		name: "interpol-dashboard",
		component: () => import("@/views/Interpol/InterpolDashboard.vue"),
		meta: {
			menuRef: "interpol-dashboard",
			isAuth: true,
			middleware: [auth],
			title: "Tableau de bord d'Interpol",
		},
	},
	{
		path: "interpol/demand/s/:id",
		name: "interpol-demand",
		props: true,
		component: () => import("@/views/Interpol/InterpolDemandShow.vue"),
		meta: {
			menuRef: "interpol-dashboard",
			isAuth: true,
			middleware: [auth, permission],
			permission: "show-im-demand",
			title: "Détails d'une demande",
		},
	},
	{
		path: "blacklist-vehicles/pending",
		name: "pending-blacklist-vehicles",
		component: PendingBlacklistVehicles,
		meta: {
			menuRef: "blacklist",
			isAuth: true,
			middleware: [auth, permission],
			permission: "browse-blacklist-vehicle",
			title: "Liste noire",
		},
	},
	{
		path: "blacklist-vehicles",
		name: "blacklist-vehicles",
		component: BlackListVehicles,
		meta: {
			menuRef: "blacklist",
			isAuth: true,
			middleware: [auth, permission],
			permission: "browse-blacklist-vehicle",
			title: "Liste noire",
		},
	},
];
