<template>
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
					<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ demandInfo.service }}</p>
				</div>
			</div>
			<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
				<div class="flex items-center pb-2 mt-2 border-b">
					<p class="w-1/2 text-lg leading-5">Référence</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ demandInfo.reference }}</p>
				</div>
			</div>
			<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
				<div class="flex items-center pb-2 mt-2 border-b">
					<p class="w-1/2 text-lg leading-5">Statut</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">
						<StatusComponent :status="demandInfo.status" :status-text="demandInfo.status_label">
							<i
								v-if="demandInfo.status === 'in_cart'"
								class="fa-light fa-arrow-down-from-arc w-4 h-4 mr-2"
							/>
						</StatusComponent>
					</p>
				</div>
			</div>
			<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
				<div class="flex items-center pb-2 mt-2 border-b">
					<p class="w-1/2 text-lg leading-5">Date de la demande</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ demandInfo.date }}</p>
				</div>
			</div>
		</div>
		<div class="col-span-12 py-3 px-8 rounded-sm">
			<div class="intro-y flex items-center">
				<h2 class="text-primary text-2xl">Étapes</h2>
			</div>
			<ProgressStepBar
				:steps="
					demandInfo.steps.map((step) => {
						return {
							name: step.label,
							done: step.is_done,
							current: step.is_current,
						};
					})
				"
			/>
		</div>
		<div class="col-span-12 py-3 px-8 rounded-sm">
			<div class="intro-y flex items-center">
				<h2 class="text-primary text-2xl">Documents</h2>
			</div>
			<div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
				<dd class="mt-2 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
					<FilesList :files="demandInfo.files" />
				</dd>
			</div>
		</div>
		<!--
		<VehicleInfoCard :vehicle-info="demandInfo.vehicle_info" :loading="loading" />

		<OwnerInfoCard :owner-info="demandInfo.owner_info" />-->
	</div>
</template>

<script setup>
	import StatusComponent from "@/components/StatusComponent.vue";
	import ProgressStepBar from "@/components/ProgressStepBar.vue";
	import FilesList from "@/components/FilesList.vue";

	defineProps({
		demandInfo: {
			type: Object,
			required: true,
		},
	});
</script>
