<template>
	<div class="mt-8 bg-white p-8 rounded-lg">
		<TabGroup class="mb-4">
			<TabList class="nav-tabs flex justify-center items-end !border-none">
				<Tab class="w-full tab-button" list-class="me-2" tag="button">Unicolore</Tab>
				<Tab class="w-full tab-button" list-class="me-2" tag="button">Bicolore</Tab>
				<Tab class="w-full tab-button" list-class="me-2" tag="button">Tricolore</Tab>
				<Tab class="w-full tab-button" list-class="me-2" tag="button">Peinture personnalisé</Tab>
			</TabList>
			<TabPanels class="">
				<TabPanel class="leading-relaxed p-5">
					<div
						class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 bg-[#F7F7F7] py-12 px-12 lg:px-12 rounded-2xl"
					>
						<label
							v-for="i in 12"
							:key="i"
							class="has-[:checked]:border-success has-[:checked]:bg-green-50 hover:bg-green-50 flex items-center justify-center rounded-lg bg-blue-50 border-primary border h-24 cursor-pointer"
						>
							<span class="w-full mx-4">
								<img
									class="w-full h-full object-cover rounded-xl"
									src="@/assets/images/car/car_9.png"
									alt="vehicle"
								/>
							</span>
							<span class="form-check ml-auto mr-4">
								<input class="form-check-input" name="car-choice" type="radio" :checked="i === 1" />
							</span>
						</label>
					</div>
				</TabPanel>
			</TabPanels>
		</TabGroup>
	</div>

	<div class="intro-y col-span-12">
		<div class="text-right mt-4">
			<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$router.go(-1)">
				Annuler
			</button>
			<button
				class="btn btn-primary w-36"
				type="button"
				@click="$router.push('/affiliate/dashboard/transformation/FileForm')"
			>
				Suivant
			</button>
		</div>
	</div>
</template>

<script setup>
	import { TabGroup, TabPanel, TabList, TabPanels, Tab } from "@/global-components/tab/index.js";
</script>

<style scoped></style>
