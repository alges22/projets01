<template>
	<div class="intro-y grid grid-cols-12 gap-5 mt-4 divide-x">
		<div class="intro-y col-span-12 lg:col-span-7">
			<h2 class="text-lg font-bold mr-auto">Choisissez un template</h2>

			<div class="grid grid-cols-12 gap-5 mt-5">
				<template v-for="(label, index) in formData.number_templates" :key="index">
					<label
						:for="`template#${index}`"
						class="has-[:checked]:border-success has-[:checked]:border-2 has-[:checked]:bg-green-50 hover:bg-green-50 col-span-12 sm:col-span-3 2xl:col-span-2 box p-5 cursor-pointer zoom-in text-center"
					>
						<input
							:id="`template#${index}`"
							v-model="template"
							type="radio"
							name="template"
							:value="label.template"
							class="hidden"
							@change="loadSuggestions"
						/>
						<span class="font-bold text-xl tracking-[.4em]">{{ label.template }}</span>
					</label>
				</template>
			</div>

			<hr class="mt-5" />

			<div class="lg:flex mt-5 intro-y">
				<h2 class="text-lg font-medium mr-auto">Liste des suggestions</h2>
				<div class="relative">
					<input
						v-model="filter"
						type="search"
						class="form-control py-3 px-4 w-full lg:w-64 box pr-10"
						placeholder="Rechercher un numéro"
						:minlength="4"
						:maxlength="4"
						:disabled="suggestionLoading || !template"
					/>
					<i class="fa-light fa-search w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0 text-slate-500" />
				</div>
			</div>
			<div class="grid grid-cols-12 gap-5 mt-5 pt-5 h-96 dashboard-card overflow-y-scroll">
				<span v-if="!template" class="col-span-12 flex-center">Veuillez choisir un template</span>
				<span v-else-if="suggestionLoading" class="col-span-12 flex-center">Recherche des suggestions...</span>
				<span v-else-if="filteredSuggestions.length <= 0" class="col-span-12 flex-center">
					Aucune suggestion trouvé, veuillez choisir un autre template
					{{ filter ? "ou changer de filtre" : "" }}
				</span>
				<template v-else>
					<TransitionGroup name="list">
						<template v-for="(label, index) in filteredSuggestions" :key="index">
							<label
								:for="`template-number#${index}`"
								class="h-16 has-[:checked]:border-success has-[:checked]:border-2 has-[:checked]:bg-green-50 hover:bg-green-50 col-span-12 sm:col-span-3 2xl:col-span-2 box p-5 cursor-pointer zoom-in text-center"
							>
								<input
									:id="`template-number#${index}`"
									v-model="demand.desired_number"
									type="radio"
									name="number"
									:value="label"
									class="hidden"
								/>
								<span class="font-bold text-xl tracking-[.4em]">{{ label }}</span>
							</label>
						</template>
					</TransitionGroup>
				</template>
			</div>
		</div>

		<form class="col-span-12 lg:col-span-5 ps-4" @submit.prevent="submit">
			<label class="h-2 text-lg font-bold mr-auto" for="desired_number">Ou entrer le numéro désiré</label>
			<div class="box flex p-5 mt-5">
				<TextInputGroup
					v-model="desiredNumber"
					name="desired_number"
					class="py-3 px-4 w-full bg-slate-100 border-slate-200/60 pr-10"
					:errors="errors.desired_number || []"
					required
					placeholder="Ex: 1234"
					:disabled="loading"
					:minlength="4"
					:maxlength="4"
					pattern="^[0-9]{4}$"
				/>
				<BasicButton class="btn-primary ml-2" type="submit" :loading="loading">Vérifier</BasicButton>
			</div>
		</form>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-5">
		<div class="intro-y col-span-12">
			<div class="flex align-center justify-end mt-5">
				<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$emit('prev')">
					Retour
				</button>
				<BasicButton
					:disabled="!update && !demand.desired_number"
					:loading="formLoading"
					class="btn-primary w-36"
					@click="$emit('next')"
				>
					Valider
				</BasicButton>
			</div>
		</div>
	</div>
</template>

<script setup>
	import { computed, onMounted, ref } from "vue";
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import client from "@/assets/js/axios/client.js";
	import Alert from "@/components/notification/alert.js";
	import { debounce } from "@/helpers/utils.js";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import BasicButton from "@/components/BasicButton.vue";

	const emit = defineEmits(["next", "prev"]);

	const demandStore = useDemandStore();
	const { demand, formData, formLoading, update } = storeToRefs(demandStore);
	const loading = ref(false);
	const errors = ref({});
	const template = ref(null);
	const suggestionLoading = ref(false);
	const suggestions = ref([]);
	const desiredNumber = ref("");
	const filter = ref(null);

	const filteredSuggestions = computed(() => {
		return filter.value
			? suggestions.value.filter((suggestion) => suggestion.includes(filter.value))
			: suggestions.value;
	});

	const submit = () => {
		loading.value = true;
		errors.value = {};
		client({
			method: "POST",
			url: "client/check-number",
			data: { number: desiredNumber.value },
		})
			.then((response) => {
				Alert.success(response.data.message);
				demand.value.desired_number = desiredNumber.value;
				emit("next");
			})
			.catch((error) => {
				errors.value = error.response.data.errors || [];
			})
			.finally(() => {
				loading.value = false;
			});
	};

	const loadSuggestions = debounce(() => {
		suggestionLoading.value = true;
		client({
			method: "POST",
			url: "client/suggest-numbers",
			data: { template: template.value },
		})
			.then((response) => {
				suggestions.value = response.data;
			})
			.finally(() => {
				suggestionLoading.value = false;
			});
	}, 500);

	onMounted(() => {});
</script>

<style>
	.list-move,
	.list-enter-active,
	.list-leave-active {
		transition: all 0.2s ease;
	}

	.list-enter-from,
	.list-leave-to {
		opacity: 0;
		transform: translateX(30px);
	}

	.list-leave-active {
		position: absolute;
	}
</style>
