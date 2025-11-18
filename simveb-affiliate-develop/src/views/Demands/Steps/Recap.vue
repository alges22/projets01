<template>
	<div class="intro-y flex items-center mt-4">
		<h2 class="text-lg font-semibold m-auto">Récapitulatif</h2>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-4 bg-white p-2 xl:p-4 rounded-md">
		<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
			<div class="intro-y flex items-center">
				<h2 class="text-primary text-2xl">Service</h2>
			</div>
		</div>
		<div class="col-span-12 grid grid-cols-12 gap-6">
			<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
				<div class="flex items-center pb-2 mt-2 border-b">
					<p class="w-1/2 text-lg leading-5">Type de service</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ service }}</p>
				</div>
			</div>
		</div>

		<slot></slot>
	</div>
	<div class="intro-y col-span-12">
		<div class="flex align-center justify-end mt-4">
			<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$emit('prev')">
				Annuler
			</button>
			<BasicButton
				class="btn-primary w-56"
				type="button"
				:loading="loading || cartLoading"
				:loading-text="cartLoading ? 'Traitement de la commande' : 'Chargement'"
				@click="addToCart"
			>
				Ajouter au panier
			</BasicButton>
		</div>
	</div>
</template>

<script setup>
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import BasicButton from "@/components/BasicButton.vue";
	import { useCartStore } from "@/stores/cart.js";

	defineEmits(["next", "prev"]);

	defineProps({
		service: {
			type: String,
			required: true,
		},
	});

	const demandStore = useDemandStore();
	const { formLoading: loading, demand } = storeToRefs(demandStore);
	const { cartModalOpen, cartLoading } = storeToRefs(useCartStore());

	const addToCart = () => {
		demandStore.createDemand(demand.value, true).then(() => {
			cartModalOpen.value = true;
		});
	};
</script>
