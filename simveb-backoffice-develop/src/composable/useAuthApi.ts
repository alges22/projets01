import axios, { type AxiosInstance } from "axios";

import { Notyf } from "notyf";

let authApi: AxiosInstance;
const notyf = new Notyf();

export function createApi() {
	// Here we set the base URL for all requests made to the authApi
	authApi = axios.create({
		baseURL: import.meta.env.VITE_API_URL,
	});

	authApi.interceptors.response.use(
		function (response) {
			// Any status code that lie within the range of 2xx cause this function to trigger
			// Do something with response data
			return response;
		},
		async function (error) {
			notyf.error(error.response.data.message);
			return Promise.reject(error);
		},
	);

	return authApi;
}

export function useAuthApi() {
	if (!authApi) {
		createApi();
	}
	return authApi;
}
