import Axios from "axios";
import Alert from "@/components/notification/alert.js";

const LOGIN_URL = import.meta.env.VITE_LOGIN_URL;

Axios.withCredentials = false;
const authClient = Axios.create({
	baseURL: LOGIN_URL,
	json: true,
	headers: {
		"Content-Type": "application/json",
	},
});

authClient.interceptors.response.use(
	function (response) {
		return response;
	},
	function (error) {
		if (!error.config.params || !error.config.params.noError) {
			if (error.response && error.response.data && error.response.data.message) {
				Alert.error(error.response.data.message);
			} else {
				Alert.error(error.message || "An error occurred");
			}
		}
		return Promise.reject(error);
	}
);
export default authClient;
