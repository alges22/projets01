import { defineStore } from "pinia";

export const useActionStore = defineStore("action", {
	state: () => ({
		service: {},
		step: {},
		action: {},
		createData: {},
		nextPosition: 0,
	}),
});
