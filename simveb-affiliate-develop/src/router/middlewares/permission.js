import Alert from "@/components/notification/alert.js";

export default function permission({ store, next, to }) {
	let permissions = store.permissions;
	if (
		(to.meta.permission && !permissions.includes(to.meta.permission)) ||
		(to.meta.permissions && !to.meta.permissions.some((p) => permissions.includes(p)))
	) {
		Alert.error("Vous n'avez pas la permission d'accéder à cette page");
		return false;
	}
	return next();
}
