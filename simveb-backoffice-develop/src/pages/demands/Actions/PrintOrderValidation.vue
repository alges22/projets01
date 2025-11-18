<template>
	<VModal
		is="form"
		:open="modalIsOpen"
		actions="right"
		size="big"
		title="Validation de l'impression"
		@submit.prevent="validatePrintOrder"
		@close="close"
	>
		<template #content>
			<div class="modal-form">
				<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 tab-details-inner">
					<div v-if="demand.vehicle.nb_plate > 1" class="p-4 border-2 border-dashed">
						<label
							aria-labelledby="front-images"
							for="front-images"
							class="required text-primary-dark text-2xl text-center"
						>
							Plaque avant
						</label>
						<div class="col-span-full">
							<FileDropZone
								:disabled="loading"
								:errors="errors.images"
								name="front-images"
								:multiple="false"
								accept="image/*"
								@update:model-value="(file: File) => addFile(file, true)"
							/>
						</div>
					</div>
					<div class="p-4 border-2 border-dashed">
						<label
							aria-labelledby="back-images"
							for="back-image"
							class="required text-primary-dark text-2xl text-center"
						>
							Plaque arrière
						</label>
						<div class="col-span-full">
							<FileDropZone
								:disabled="loading"
								:errors="errors.images"
								name="back-image"
								:multiple="false"
								accept="image/*"
								@update:model-value="(file: File) => addFile(file, false)"
							/>
						</div>
					</div>
				</div>

				<div class="mt-4">
					<VField label="Laissez vos observations ici">
						<VControl fullwidth :errors="errors.observations || []">
							<VTextarea v-model="form.observations" name="observations" />
						</VControl>
					</VField>
				</div>
			</div>
		</template>
		<template #action>
			<VButton :disabled="formLoading" color="danger" raised type="button" @click="rejectPrintOrder">
				Rejeter
			</VButton>
			<VButton :loading="formLoading" :disabled="formLoading" color="primary" raised type="submit">
				Confirmer
			</VButton>
		</template>
	</VModal>
</template>

<script lang="ts" setup>
	import { useCrudStore } from "/src/stores/modules/crud";
	import { storeToRefs } from "pinia";
	import { Notyf } from "notyf";
	import { useDemandStore } from "/@src/stores/modules/demand";

	const emit = defineEmits(["submit", "close"]);
	const props = defineProps({
		open: {
			type: Boolean,
			required: true,
		},
	});

	const crudStore = useCrudStore();
	const { formLoading, url, loading, errors } = storeToRefs(crudStore);
	const demandStore = useDemandStore();
	const { demand } = storeToRefs(demandStore);
	const notyf = new Notyf();
	const modalIsOpen = ref(false);

	const form = ref({
		images: {
			front: null,
			back: null,
		},
	});

	const validatePrintOrder = async () => {
		url.value = "print-orders/validate-or-reject";
		if (demand.value.vehicle.nb_plate > 1 && !form.value.images.front) {
			errors.value.images = ["Veuillez ajouter les images de la plaque avant"];
			return;
		}
		if (!form.value.images.back) {
			errors.value.images = ["Veuillez ajouter les images de la plaque arrière"];
			return;
		}
		await crudStore
			.createWithFile({
				...form.value,
				print_order_id: demand.value.print_order.id,
				action: "validate",
				images: [form.value.images.front, form.value.images.back].filter((i) => i),
			})
			.then(async () => {
				notyf.success("Demande d'impression validée avec succès");
				await demandStore.getDemand(demand.value.id);
				close();
			});
	};

	const rejectPrintOrder = async () => {
		url.value = "print-orders/validate-or-reject";
		await crudStore
			.createWithFile({
				print_order_id: props.order.id,
				action: "reject",
			})
			.then(async (res) => {
				notyf.success(res.message);
				await demandStore.getDemand(demand.value.id);
				close();
			});
	};

	const addFile = (file: File, isFront = true) => {
		isFront ? (form.value.images.front = file) : (form.value.images.back = file);
	};

	const close = () => {
		form.value = {
			images: {
				front: null,
				back: null,
			},
		};
		modalIsOpen.value = false;
		emit("close");
	};

	watch(
		() => props.open,
		(value) => {
			modalIsOpen.value = value;
		},
	);
</script>

<style lang="scss">
	.card-grid {
		.columns {
			margin-inline-start: -0.5rem !important;
			margin-inline-end: -0.5rem !important;
			margin-top: -0.5rem !important;
		}

		.column {
			padding: 0.5rem !important;
		}
	}

	.card-grid-v2 {
		.card-grid-item {
			.card {
				border: 1px solid var(--fade-grey-dark-4);
				box-shadow: none;
				border-radius: var(--radius-large);

				.card-header {
					box-shadow: none;
					border-bottom: 1px solid var(--fade-grey-dark-4);

					.card-header-title {
						display: flex;
						align-items: center;

						.meta {
							margin-inline-start: 10px;
							line-height: 1.2;

							span {
								display: block;
								font-weight: 400;

								&:first-child {
									font-family: var(--font-alt);
									font-size: 0.95rem;
									color: var(--dark-text);
									font-weight: 600;
								}

								&:nth-child(2) {
									font-size: 0.9rem;
									color: var(--light-text);
								}
							}
						}
					}
				}

				.card-image {
					img {
						object-fit: cover;
					}
				}

				.card-content {
					border-top: 1px solid var(--fade-grey-dark-4);
					padding: 1rem;

					.card-content-flex {
						display: flex;
						align-items: center;
						justify-content: space-between;

						.card-info {
							h3 {
								font-family: var(--font-alt);
								font-size: 1rem;
								color: var(--dark-text);
								font-weight: 600;
							}

							p {
								font-size: 0.9rem;

								svg {
									position: relative;
									top: 0;
									height: 14px;
									width: 14px;
									margin-inline-end: 4px;
								}
							}
						}
					}
				}

				.card-footer {
					a {
						font-family: var(--font);
						color: var(--light-text);
						padding: 1rem 0.75rem;
						transition: all 0.3s; // transition-all test

						&:hover {
							background: var(--fade-grey-light-4);
							color: var(--primary);
						}
					}
				}
			}
		}
	}

	.is-dark {
		.card-grid-v2 {
			.card-grid-item {
				border-color: var(--dark-sidebar-light-12);

				.card {
					background: var(--dark-sidebar-light-6);
					border-color: var(--dark-sidebar-light-12);

					.card-header {
						border-color: var(--dark-sidebar-light-12);
					}

					.card-content {
						border-color: var(--dark-sidebar-light-12);

						.avatar-stack {
							.avatar {
								border-color: var(--dark-sidebar-light-6);
							}
						}
					}

					.card-footer {
						border-color: var(--dark-sidebar-light-12);

						a {
							border-color: var(--dark-sidebar-light-12);

							&:hover,
							&:focus {
								background: var(--dark-sidebar-light-2);
								color: var(--primary);
							}
						}
					}
				}
			}
		}
	}
</style>
