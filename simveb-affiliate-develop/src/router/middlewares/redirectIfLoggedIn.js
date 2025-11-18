export default function redirectIfLoggedIn({ next }) {
	return next({
		name: "admin",
	});
}
