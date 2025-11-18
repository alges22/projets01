import { acceptHMRUpdate, defineStore } from "pinia";
import { ref, computed } from "vue";
import client from "/@src/composable/axiosClient";
import { useAuthApi } from "../composable/useAuthApi";
import Cookies from "js-cookie";

const CLIENT_ID = import.meta.env.VITE_CLIENT_ID;
const CLIENT_SECRET = import.meta.env.VITE_CLIENT_SECRET;

// export type UserData = Record<string, any> | null
export type UserData = {
	id: string | null;
	name: string | null;
	email: string | null;
	username: string | null;
	identity_id: string | null;
	email_verified_at: string | null;
	disabled_at: string | null;
	created_at: string | null;
};

export const useUserSession = defineStore("userSession", () => {
	// token will be synced with local storage
	// @see https://vueuse.org/core/usestorage/
	const token = ref(Cookies.get("token"));
	const user = ref(null);
	const loading = ref(false);
	const isLoggedIn = computed(() => token.value !== undefined && token.value !== "");
	const identity = ref(null);
	const staff = ref(null);
	const roles = ref(null);
	const profiles = ref(null);
	const online_profile = ref(null);
	const permissions = ref(null);
	const affiliateLeader = ref(null);
	const affiliateMember = ref(null);

	function setUser(newUser: any) {
		user.value = newUser;
	}

	function setToken(newToken: string) {
		token.value = newToken;
	}

	function setLoading(newLoading: boolean) {
		loading.value = newLoading;
	}

	async function logoutUser() {
		token.value = undefined;
		user.value = null;
	}

	function logout() {
		return new Promise((resolve) => {
			setLoading(true);
			client({
				method: "POST",
				url: "/logout",
			}).then(() => {
				user.value = null;
				permissions.value = null;
				identity.value = null;
				staff.value = null;
				roles.value = null;
				token.value = undefined;
				setToken("");
				setLoading(false);
				localStorage.clear();
				resolve(true);
			});
		});
	}

	function login(data: any) {
		setLoading(true);
		return new Promise((resolve, reject) => {
			useAuthApi()({
				url: "/login",
				method: "POST",
				data: {
					...data,
					grant_type: "password",
					client_id: CLIENT_ID,
					client_secret: CLIENT_SECRET,
				},
			})
				.then((response) => {
					Cookies.set("token", response.data.access_token, {
						domain: import.meta.env.VITE_COOKIE_DOMAIN,
					});
					resolve(response.data);
				})
				.catch((error) => {
					reject(error.response.data);
				})
				.finally(() => {
					setLoading(false);
				});
		});
	}

	function fetchUser() {
		setLoading(true);
		return new Promise((resolve, reject) => {
			client({
				method: "GET",
				url: "/current-user",
			})
				.then((response) => {
					user.value = {
						...response.data.user.identity,
						id: response.data.user.id,
					};
					permissions.value = response.data.permissions;
					identity.value = response.data.user.identity;
					profiles.value = response.data.user.profiles;
					online_profile.value = response.data.user.online_profile;
					roles.value = response.data.roles;
					resolve(response.data);
				})
				.catch((error) => reject(error.response.data.message))
				.finally(() => {
					setLoading(false);
				});
		});
	}

	function sendOTP(npi: string, resend = false) {
		setLoading(true);
		return new Promise((resolve, reject) => {
			client({
				method: "POST",
				url: resend ? "/login/resend-otp" : "/login/send-otp",
				data: { npi },
			})
				.then((response) => {
					resolve(response.data);
				})
				.catch((error) => {
					reject(error.response.data.message);
				})
				.finally(() => {
					setLoading(false);
				});
		});
	}

	function switchProfile(profile_id, code: string) {
		setLoading(true);
		return new Promise((resolve, reject) => {
			client({
				method: "PUT",
				url: "/change-space",
				data: { profile_id },
			})
				.then(async (response) => {
					Cookies.set("profile", code, {
						domain: import.meta.env.VITE_COOKIE_DOMAIN,
					});
					resolve(response.data);
				})
				.catch((error) => {
					if (error.data && error.data.message) {
						this.errors = error.response.data.errors;
					}
					reject(error.response.data.message);
				})
				.finally(() => {
					setLoading(false);
				});
		});
	}

	return {
		user,
		token,
		isLoggedIn,
		loading,
		staff,
		identity,
		affiliateLeader,
		affiliateMember,
		roles,
		profiles,
		online_profile,
		permissions,
		logoutUser,
		setUser,
		fetchUser,
		setToken,
		setLoading,
		login,
		logout,
		sendOTP,
		switchProfile,
	} as const;
});

/**
 * Pinia supports Hot Module replacement so you can edit your stores and
 * interact with them directly in your app without reloading the page.
 *
 * @see https://pinia.esm.dev/cookbook/hot-module-replacement.html
 * @see https://vitejs.dev/guide/api-hmr.html
 */
if (import.meta.hot) {
	import.meta.hot.accept(acceptHMRUpdate(useUserSession, import.meta.hot));
}
