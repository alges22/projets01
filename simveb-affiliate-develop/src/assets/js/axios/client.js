import Axios from "axios";
import Alert from "@/components/notification/alert.js";
import Cookies from "js-cookie";

const API_URL = import.meta.env.VITE_API_URL;

Axios.withCredentials = false;

// get token from cookies
const token = Cookies.get("token");

const client = Axios.create({
	baseURL: API_URL,
	json: true,
	headers: {
		"Content-Type": "application/json",
		Authorization: "Bearer " + token,
	},
});

client.interceptors.response.use(
	async function (response) {
		// Any status code that lie within the range of 2xx cause this function to trigger
		// Do something with response data
		return response;
	},
	async function (error) {
		if (error.response && error.response.status === 401) {
			Alert.error("Vous n'avez pas les autorisations qu'il faut");
			Cookies.remove("token");
			Cookies.remove("profile");
			window.location = "/login";
		} else if (error.response && error.response.status === 403) {
			if (error.response.data && error.response.data.message) {
				Alert.error(error.response.data.message);
			}
			// window.location = "/403";
		} else if (!error.config.params || !error.config.params.noError) {
			if (error.response && error.response.data && error.response.data.message) {
				Alert.error(error.response.data.message);
			} else {
				Alert.error(error.message || "Une erreur s'est produite");
				// await router.push({ name: "unknown-error" });
			}
		}
		return Promise.reject(error);
	}
);
export default client;
