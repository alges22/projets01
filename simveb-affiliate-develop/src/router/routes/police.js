import auth from "@/router/middlewares/auth.js";
import BlackListDashboard from "@/views/Police/BlackListVehicules.vue";
import permission from "@/router/middlewares/permission.js";

export default [
	{
		path: "police-dashboard",
		name: "police-dashboard",
		component: () => import("@/views/Police/PoliceDashboard.vue"),
		meta: {
			isAuth: true,
			middleware: [auth],
			title: "Tableau de bord de la Police",
		},
	},
	{
		path: "verifs",
		name: "immatriculation-verifs",
		component: () => import("@/views/Police/Verification/ImmatriculationVerifs.vue"),
		meta: {
			menuRef: "check-vehicle",
			isAuth: true,
			middleware: [auth, permission],
			permission: "browse-vehicle-passage",
			title: "Vérification des immatriculations",
		},
	},
	{
		path: "check-vehicle",
		name: "check-vehicle",
		component: () => import("@/views/Police/Verification/VehicleInfo.vue"),
		meta: {
			menuRef: "check-vehicle",
			isAuth: true,
			middleware: [auth, permission],
			permission: "show-vehicle-passage",
			title: "Vérification du véhicule",
		},
	},
	{
		path: "register-vehicle",
		name: "register-vehicle",
		component: () => import("@/views/Police/Passage/PassageForm.vue"),
		meta: {
			menuRef: "check-vehicle",
			isAuth: true,
			middleware: [auth, permission],
			permission: "store-vehicle-passage",
			title: "Enregistrer un passage",
		},
	},
	{
		path: "alerts",
		name: "alert-dashboard",
		component: () => import("@/views/Police/Alerts/AlertDashboard.vue"),
		meta: {
			menuRef: "alert",
			isAuth: true,
			middleware: [auth, permission],
			permission: "browse-vehicle-alert",
			title: "Alertes",
		},
	},
	{
		path: "vehicle-alert/:id",
		name: "vehicle-detail",
		component: () => import("@/views/Police/VehiclePassageDetail.vue"),
		props: true,
		meta: {
			menuRef: "alert",
			isAuth: true,
			middleware: [auth, permission],
			permission: "show-vehicle-alert",
			title: "Détails",
		},
	},
	{
		path: "blacklist",
		name: "blacklist",
		component: BlackListDashboard,
		meta: {
			isAuth: true,
			middleware: [auth, permission],
			permission: "browse-blacklist-vehicle",
			title: "Liste noire",
		},
	},
];
