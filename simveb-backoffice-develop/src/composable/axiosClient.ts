import axios, { type RawAxiosRequestHeaders, type AxiosRequestHeaders } from "axios";

import { useUserSession } from "/@src/stores/userSession";
import { Notyf } from "notyf";
import Cookies from "js-cookie";

const notyf = new Notyf();
const API_URL = import.meta.env.VITE_API_URL;

// Here we set the base URL for all requests made to the api
const client = axios.create({
	baseURL: API_URL,
});

// We set an interceptor for each request to
// include Bearer token to the request if user is logged in
client.interceptors.request.use((config) => {
	const userSession = useUserSession();

	if (userSession.isLoggedIn) {
		config.headers = {
			...((config.headers as RawAxiosRequestHeaders) ?? {}),
			Authorization: `Bearer ${userSession.token}`,
		} as AxiosRequestHeaders;
	}

	return config;
});

client.interceptors.response.use(
	function (response) {
		// Any status code that lie within the range of 2xx cause this function to trigger
		// Do something with response data
		return response;
	},
	async function (error) {
		/*if (!error.data) {
	Alert.error("Erreur", error.message);
  } else {*/
		if (error.response.status === 401) {
			Cookies.remove("token", {
				domain: import.meta.env.VITE_COOKIE_DOMAIN,
			});
			Cookies.remove("profile", {
				domain: import.meta.env.VITE_COOKIE_DOMAIN,
			});
			localStorage.clear();
			window.location = "/login";
		}
		if (error.response.status === 403) {
			// window.location = "/403";
		}
		// Any status codes that falls outside the range of 2xx cause this function to trigger
		// Do something with response error

		if (!error.config.params || !error.config.params.noError) {
			if (error.response && error.response.data && error.response.data.message) {
				notyf.error(error.response.data.message);
			} else {
				notyf.error(error.message || "An error occurred");
				// await router.push({ name: "unknown-error" });
			}
		}
		// }
		return Promise.reject(error);
	},
);
export default client;
