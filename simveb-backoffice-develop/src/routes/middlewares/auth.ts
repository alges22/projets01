import Cookies from "js-cookie";

export default async function auth({ store, next }) {
	const token = Cookies.get("token");
	if (!token) {
		return next({
			name: "login",
		});
	}
	return next();
}
