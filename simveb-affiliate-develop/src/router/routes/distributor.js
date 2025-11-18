import auth from "@/router/middlewares/auth.js";
import permission from "@/router/middlewares/permission.js";
import MotorcycleList from "@/views/Distributor/Motorcycle/MotorcycleList.vue";
import MotorcycleView from "@/views/Distributor/Motorcycle/MotorcycleView.vue";
import DistributorDashboard from "@/views/Distributor/DistributorDashboard.vue";

export default [
	{
		path: "distributor-dashboard",
		name: "distributor-dashboard",
		component: DistributorDashboard,
		meta: {
			isAuth: true,
			middleware: [auth],
			title: "Tableau de bord du concessionaire",
		},
	},
	{
		path: "distributor/motorcycles",
		name: "motorcycles-list",
		component: MotorcycleList,
		meta: {
			menuRef: "motorcycles",
			isAuth: true,
			middleware: [auth, permission],
			permission: "browse-motorcycle",
			title: "Gestion des véhicles à 2 ou 3 roues",
		},
	},
	{
		path: "distributor/motorcycles/:id",
		name: "motorcycles-detail",
		component: MotorcycleView,
		props: true,
		meta: {
			menuRef: "motorcycles",
			isAuth: true,
			middleware: [auth, permission],
			permission: "show-motorcycle",
			title: "Gestion des véhicles à 2 ou 3 roues",
		},
	},
];
