import LoginView from "@/views/Auth/LoginView.vue";
import guest from "@/router/middlewares/guest.js";

export default [
	{
		path: "/login",
		name: "login",
		component: LoginView,
		meta: {
			isAuth: true,
			middleware: [guest],
			title: "Connexion",
			transition: "page-left",
		},
	},
];
