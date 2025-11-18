import Cookies from "js-cookie";

export default function redirectIfLoggedIn({ store, next }) {
	const token = Cookies.get("token");
	if (token) {
		return next({
			name: "admin_home",
		});
	}
	return next();
	// const roles = store.roles;

	// return next({
	//   // name: 'admin_home',
	// });

	// TODO : Redéfinir le nom des rôles en rapport avec le backend

	// if (roles.filter((role) => role.name === "admin").length > 0) {
	// 	return next({
	// 		name: "admin",
	// 	});
	// } else if (roles.filter((role) => role.name === "staff").length > 0) {
	// 	return next({
	// 		name: "sig",
	// 	});
	// } else if (roles.filter((role) => role.name === "client").length > 0) {
	// 	return next({
	// 		name: "starter",
	// 	});
	// } else {
	// 	return next({
	// 		name: "pricing",
	// 	});
	// }
}
