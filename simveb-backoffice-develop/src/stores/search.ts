import { defineStore } from "pinia";
import client from "/@src/composable/axiosClient";
import { trim } from "/@src/utils/helpers";

export const useSearchStore = defineStore("search", {
	state: () => ({
		url: "",
		query: "",
		enabled: false,
		loading: false,
		placeholder: null,
	}),
	getters: {},
	actions: {
		search(query: string) {
			this.loading = true;
			return new Promise((resolve, reject) => {
				client({
					method: "GET",
					url: this.url + "?query=" + trim(query),
				})
					.then((response) => {
						resolve(response.data);
					})
					.catch((error) => {
						reject(error.response.data.message);
					})
					.finally(() => {
						this.loading = false;
					});
			});
		},
		reset() {
			this.query = "";
			this.enabled = false;
			this.loading = false;
		},
	},
});
