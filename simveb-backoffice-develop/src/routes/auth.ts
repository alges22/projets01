import auth from "/@src/routes/middlewares/auth";
import loginVue from "/@src/pages/auth/login.vue";
import redirectIfLoggedIn from "/@src/routes/middlewares/redirectIfLoggedIn";
import AuthLayout from "/@src/layouts/AuthLayout.vue";

export default [
	{
		path: "/auth",
		redirect: { name: "login" },
		component: AuthLayout,
		children: [
			{
				path: "/login",
				name: "login",
				component: loginVue,
				meta: {
					isAuth: false,
					middleware: [redirectIfLoggedIn],
					title: "Connexion",
				},
			},
			{
				path: "/logout",
				name: "logout",
				meta: {
					isAuth: true,
					middleware: [auth],
				},
				redirect: () => {
					localStorage.removeItem("token");
					return "/login";
				},
			},
		],
	},
	// {
	// 	path: "/verify/:id/:token",
	// 	name: "verify_email",
	// 	meta: {
	// 		middleware: [guest],
	// 	},
	// 	component: VerifyEmailView,
	// 	props: true,
	// },
];
