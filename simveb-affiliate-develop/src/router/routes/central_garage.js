import auth from "@/router/middlewares/auth.js";
import CentralGarageDashboard from "@/views/CentralGarage/CentralGarageDashboard.vue";
import permission from "@/router/middlewares/permission.js";

export default [
	{
		path: "",
		name: "central-garage-dashboard",
		component: CentralGarageDashboard,
		meta: {
			isAuth: true,
			middleware: [auth],
			title: "Tableau de bord / GM Garage Centrale de l’État",
		},
	},
	{
		path: "gov-services",
		name: "gov-services",
		component: () => import("@/views/CentralGarage/GovServices.vue"),
		meta: {
			menuRef: "gov-services",
			isAuth: true,
			middleware: [auth, permission],
			permission: "browse-gov-vehicle",
			title: "GM Garage Centrale de l’État / Services",
		},
	},
	{
		path: "reform-declaration/create/:reformId?",
		name: "reform-declaration-create",
		component: () => import("@/views/CentralGarage/ReformDeclarationForm.vue"),
		props: true,
		meta: {
			menuRef: "gov-services",
			isAuth: true,
			middleware: [auth, permission],
			permission: "store-reform-declaration",
			title: "GM Garage Centrale de l’État / Déclaration de réforme",
		},
	},
	{
		path: "reform-declaration/:reformId",
		name: "reform-declaration",
		component: () => import("@/views/CentralGarage/ReformDeclarationView.vue"),
		props: true,
		meta: {
			menuRef: "gov-services",
			isAuth: true,
			middleware: [auth, permission],
			permission: "show-reform-declaration",
			title: "GM Garage Centrale de l’État / Déclaration de réforme",
		},
	},
];
