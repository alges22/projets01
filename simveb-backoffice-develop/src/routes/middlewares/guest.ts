import Cookies from "js-cookie";

export default function guest({ next }) {
	const token = Cookies.get("token");
	if (token != null) {
		return next({
			name: "home",
		});
	}
	return next();
}
