import client from "@/assets/js/axios/client.js";

const getDashboardStats = async () => {
	return new Promise((resolve, reject) => {
		client({
			method: "GET",
			url: "dashboard-stats",
		})
			.then((response) => {
				resolve(response.data);
			})
			.catch((error) => {
				reject(error.response.data.message);
			});
	});
};

export { getDashboardStats };
