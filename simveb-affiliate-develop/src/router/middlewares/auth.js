import Cookies from "js-cookie";
import Alert from "@/components/notification/alert.js";
import spaceConfig from "@/../space-config.js";

export default async function auth({ next, store }) {
	let token = Cookies.get("token");
	if (!store.user) {
		if (token) {
			await store.fetchUser().then(() => {
				const online_profile = store.online_profile.type.code;
				if (spaceConfig[online_profile] !== window.location.origin) {
					if (import.meta.env.VITE_COOKIE_DOMAIN === "localhost") {
						Alert.warn(
							"Ce profile n'est pas autorisé à accéder à cet espace. Ceci ne fonctionnera qu'en développement."
						);
					} else {
						window.open(spaceConfig[online_profile], "_self");
					}
				}
				return next();
			});
		} else {
			Alert.warn("Vous devez être connecté pour accéder à cette page.");
			return next({ name: "login" });
		}
	}
	return next();
}
