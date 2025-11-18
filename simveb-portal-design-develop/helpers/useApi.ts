import axios, {
	type RawAxiosRequestHeaders,
	type AxiosInstance,
	type AxiosRequestHeaders,
} from "axios";

import {Notyf} from "notyf";

let api: AxiosInstance;

export function createApi() {
	// Here we set the base URL for all requests made to the api
	api = axios.create({
		baseURL: import.meta.env.VITE_API_URL ?? "http://localhost:8000/api",
	});

	// We set an interceptor for each request to
	// include Bearer token to the request if user is logged in
	api.interceptors.request.use((config) => {
		const token = useCookie('token');

		if (token.value) {
			// check if value exists
			config.headers = {
				...((config.headers as RawAxiosRequestHeaders) ?? {}),
				Authorization: `Bearer ${token.value}`,
			} as AxiosRequestHeaders;
		}

		return config;
	});

	api.interceptors.response.use(
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
				// window.location = "/login";
				// localStorage.clear();
			}
			if (error.response.status === 403) {
				// window.location = "/403";
			}
			// Any status codes that falls outside the range of 2xx cause this function to trigger
			// Do something with response error

			if (!error.config.params || !error.config.params.noError) {
				if (
					error.response &&
					error.response.data &&
					error.response.data.message
				) {
					// notyf.error(error.response.data.message);
				} else {
					// notyf.error(error.message || "An error occurred");
					// await router.push({ name: "unknown-error" });
				}
			}
			// }
			return Promise.reject(error);
		}
	);

	return api;
}

export function useApi() {
	if (!api) {
		createApi();
	}
	return api;
}
