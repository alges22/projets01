<template></template>

<script setup>
	import { useRouter } from "vue-router";
	import { onMounted, ref } from "vue";
	import client from "@/assets/js/axios/client.js";

	const props = defineProps({
		serviceId: String,
	});
	const services = ref([]);

	const router = useRouter();
	switch (props.serviceId) {
		case "1":
			router.push({ name: "affiliate-demands" });
			break;
		case "2":
			router.push({ name: "affiliate-certificates" });
			break;
		case "3":
			router.push({ name: "affiliate-wages" });
			break;
		default:
			router.push({ name: "affiliate-dashboard" });
	}

	const fetchServices = () => {
		return new Promise((resolve) => {
			client({
				url: "/services",
				method: "GET",
			}).then((response) => {
				resolve(response.data);
			});
		});
	};

	const redirect = () => {
		const serviceCode = services.value.find((service) => service.id === props.serviceId).code;
		switch (serviceCode) {
			case "demands":
				router.push({ name: "affiliate-demands" });
				break;
			case "certificates":
				router.push({ name: "affiliate-certificates" });
				break;
			case "wages":
				router.push({ name: "affiliate-wages" });
				break;
			default:
				router.push({ name: "affiliate-dashboard" });
		}
	};

	onMounted(async () => {
		services.value = await fetchServices();
		redirect();
	});
</script>

<style scoped></style>
