import auth from "@/router/middlewares/auth.js";
import BankDashboard from "@/views/Bank/BankDashboard.vue";

export default [
	{
		path: "bank-dashboard",
		name: "bank-dashboard",
		component: BankDashboard,
		meta: {
			isAuth: true,
			middleware: [auth],
			title: "Tableau de bord/Banque",
		},
	}
];
