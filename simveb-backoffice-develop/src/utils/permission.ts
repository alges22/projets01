import { useUserSession } from "/@src/stores/userSession";
import { storeToRefs } from "pinia";

export function userHasPermissions() {
	const { permissions: userPermissions, roles: userRoles } = storeToRefs(useUserSession());

	/**
	 * @description Vérifie si les permissions de l'utilisateur contiennent la permission passée en paramètre
	 */
	const can = (permission: String) => {
		return userPermissions.value.includes(permission);
	};

	/**
	 * @description Vérifie si les permissions de l'utilisateur ne contiennent pas la permission passée en paramètre
	 */
	const cannot = (permission: string) => {
		return !can(permission);
	};

	/**
	 * @param {string[]} permissions
	 * @returns {boolean}
	 * @description Vérifie si les permissions de l'utilisateur contiennent une des permissions passée en paramètre
	 */
	const hasOnePermissions = (permissions: Array<string>): boolean => {
		return permissions.some((permission) => can(permission));
	};

	/**
	 * @description Vérifie si les rôles de l'utilisateur contiennent le rôle passée en paramètre
	 */
	const is = (role: string) => {
		return userRoles.value.some((roleUser: string) => role === roleUser.name || role === roleUser.label);
	};

	const isOneOf = (roles: Array<string>) => {
		return roles.some((role: string) => is(role));
	};

	return {
		can,
		cannot,
		hasOnePermissions,
		is,
		isOneOf,
	};
}
