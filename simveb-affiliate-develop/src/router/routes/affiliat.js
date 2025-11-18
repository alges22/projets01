import AffiliateDashboard from "@/views/Affiliate/AffiliateDashboard.vue";
import auth from "@/router/middlewares/auth.js";
import AffiliateDemands from "@/views/Affiliate/AffiliateDemands.vue";

export default [
	{
		path: "affiliate-dashboard",
		name: "affiliate-dashboard",
		component: AffiliateDashboard,
		meta: {
			isAuth: true,
			middleware: [auth],
			title: "Tableau de bord de l'affilié",
		},
	},
	{
		path: "/af/demands",
		name: "affiliate-demands",
		component: AffiliateDemands,
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Demandes",
		},
	},
];
