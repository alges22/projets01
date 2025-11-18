import { defineStore } from "pinia";
import authClient from "@/assets/js/axios/auth-client";
import client from "@/assets/js/axios/client";
import Cookies from "js-cookie";

const CLIENT_ID = import.meta.env.VITE_CLIENT_ID;
const CLIENT_SECRET = import.meta.env.VITE_CLIENT_SECRET;

export const useAuthStore = defineStore("auth", {
	state: () => ({
		user: null,
		roles: [],
		permissions: null,
		data: {},
		isLoggedIn: false,
		loading: false,
		errors: {},
		online_profile: null,
		profiles: [],
		theme: "default",
	}),
	getters: {
		/*getUserName: () => {
			return this.identity.firstname + " " + this.identity.lastname;
		},
		getProfilePicture: () => {
			return this.identity.photo;
		},*/
	},
	actions: {
		login(data) {
			this.loading = true;
			this.errors = {};
			return new Promise((resolve, reject) => {
				authClient({
					method: "POST",
					data: {
						...data,
						grant_type: "password",
						client_id: CLIENT_ID,
						client_secret: CLIENT_SECRET,
					},
				})
					.then((response) => response.data)
					.then(async (response) => {
						this.isLoggedIn = true;
						Cookies.set("token", response.access_token, {
							domain: import.meta.env.VITE_COOKIE_DOMAIN,
						});
						resolve(response);
					})
					.catch((error) => {
						if (error.response) {
							this.errors = error.response.data.errors || {};
							reject(error.response.data.message);
						}
					})
					.finally(() => {
						this.loading = false;
					});
			});
		},
		logout() {
			return new Promise((resolve) => {
				client({
					method: "POST",
					url: "/logout",
				}).then(() => {
					this.user = null;
					this.permissions = [];
					this.profiles = [];
					this.online_profile = [];
					this.roles = [];
					this.isLoggedIn = false;
					this.theme = "default";
					Cookies.remove("token", {
						domain: import.meta.env.VITE_COOKIE_DOMAIN,
					});
					resolve(true);
				});
			});
		},
		resendInvitation(userID) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "POST",
					url: `/email/verification-notification/${userID}`,
				})
					.then((response) => {
						resolve(response.data);
					})
					.catch((error) => reject(error.response.data.message))
					.finally(() => {
						this.loading = false;
					});
			});
		},
		/**
		 * This function is used to send an OTP (One Time Password) to a user.
		 * It makes a POST request to the "/login/send-otp" endpoint with the user's NPI (National Provider Identifier) as data.
		 *
		 * @param {string} npi - The National Provider Identifier of the user.
		 * @param {boolean} resend - A boolean value to indicate if the OTP should be resent.
		 * @returns {Promise} - Returns a Promise that resolves with the server's response data if the request is successful.
		 * If the request fails, the Promise is rejected with the server's error message.
		 *
		 * @example
		 * sendOTP('1234567890')
		 *   .then(data => console.log(data))
		 *   .catch(error => console.error(error));
		 */
		sendOTP(npi, resend = false) {
			this.loading = true;
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
						if (error.response.data.errors) {
							this.errors = error.response.data.errors;
						}
						reject(error.response.data.message);
					})
					.finally(() => {
						this.loading = false;
					});
			});
		},
		fetchUser() {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: "/current-user",
				})
					.then((response) => {
						this.user = {
							...response.data.user.identity,
							id: response.data.user.id,
						};
						this.online_profile = response.data.user.online_profile;
						Cookies.set("profile", response.data.user.online_profile.type.code, {
							domain: import.meta.env.VITE_COOKIE_DOMAIN,
						});
						this.profiles = response.data.user.profiles;
						this.roles = response.data.roles;
						this.permissions = response.data.permissions;
						this.theme = response.data.user.online_profile.space?.template.toLowerCase() || "default";
						this.loading = false;
						resolve(response.data);
					})
					.catch((error) => {
						if (error.data && error.data.message) {
							reject(error.response.data.message);
						}
					});
			});
		},
		switchProfile(profile_id, code) {
			this.loading = true;
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
						this.loading = false;
					});
			});
		},
	},
});
