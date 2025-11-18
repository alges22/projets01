export default function permission({ store, next, to }) {
	const permissions = store.permissions;
	if (!permissions.includes(to.meta.permission)) {
		return next({
			name: "login",
		});
	}
	return next();
}
