import { storeToRefs } from "pinia";

export function userHasPermissions() {
	const { permissions: userPermissions, roles: userRoles } = storeToRefs(useUserStore());

	/**
	 * @description Vérifie si les permissions de l'utilisateur contiennent la permission passée en paramètre
	 * @param {string} permission
	 * @returns {boolean}
	 */
	const can = (permission) => {
		return userPermissions.value.includes(permission);
	};

	/**
	 * @param {string} permission
	 * @returns {boolean}
	 * @description Vérifie si les permissions de l'utilisateur ne contiennent pas la permission passée en paramètre
	 */
	const cannot = (permission) => {
		return !can(permission);
	};

	/**
	 * @param {string[]} permissions
	 * @returns {boolean}
	 * @description Vérifie si les permissions de l'utilisateur contiennent une des permissions passée en paramètre
	 */
	const hasOnePermissions = (permissions) => {
		return permissions.some((permission) => can(permission));
	};

	/**
	 * @description Vérifie si les rôles de l'utilisateur contiennent le rôle passée en paramètre
	 * @param role
	 * @returns {Boolean}
	 */
	const is = (role) => {
		return userRoles.value.some((roleUser) => role === roleUser.name || role === roleUser.label);
	};

	const isOneOf = (roles) => {
		return roles.some((role) => is(role));
	};

	return {
		can,
		cannot,
		hasOnePermissions,
		is,
		isOneOf,
	};
}
