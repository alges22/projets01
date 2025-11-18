<template>
	<div class="page-content-inner">
		<form class="form-layout is-separate" @submit.prevent="confirmPlateOrder">
			<div class="form-outer">
				<VLoader size="large" :active="loading">
					<div class="form-body">
						<template v-for="(shape, index) in row.plate_shapes" :key="index">
							<div class="form-section">
								<div class="form-section-inner has-padding-bottom">
									<h3 class="has-text-centered">{{ shape.name }}</h3>
									<div class="columns is-multiline">
										<VField>
											<div class="buttons">
												<VButton @click="addColor(shape)">Ajouter</VButton>
											</div>
										</VField>
										<div v-for="(color, indexC) in shape.colors" :key="indexC" class="column is-12">
											<div class="is-flex" :style="{ gap: '1rem' }">
												<VField v-slot="{ id }" :style="{ width: '50%' }">
													<VControl>
														<Multiselect
															v-model="color.id"
															:attrs="{ id }"
															placeholder="Choisissez une couleur"
															label="label"
															:options="
																formData.colors.filter(
																	(c) => !shape.colors.map((s) => s.id).includes(c.id)
																)
															"
															:searchable="true"
															track-by="label"
															value-prop="id"
															:max-height="145"
															autocomplete="off"
														>
															<template #singlelabel="{ value }">
																<div class="multiselect-single-label">
																	<div
																		:style="{ backgroundColor: value.color_code }"
																		class="color-box mr-1"
																	></div>
																	{{ value.label }}
																</div>
															</template>
															<template #option="{ option }">
																<div
																	:style="{ backgroundColor: option.color_code }"
																	class="color-box mr-1"
																></div>
																{{ option.label }}
															</template>
														</Multiselect>
													</VControl>
												</VField>
												<VField :style="{ width: '30%' }">
													<VControl>
														<VInput
															v-model="color.nb"
															type="number"
															placeholder="Quantité"
															:name="indexC + 'nb'"
														/>
													</VControl>
												</VField>
												<VIconButton
													v-if="shape.colors.length > 1"
													icon="trash-2"
													circle
													outlined
													color="danger"
													@click="shape.colors.splice(indexC, 1)"
												/>
											</div>
										</div>
									</div>
								</div>
							</div>
						</template>
					</div>
				</VLoader>
			</div>
			<div class="form-section-outer">
				<div class="button-wrap">
					<VButton
						:loading="formLoading || loading"
						:disabled="formLoading || loading"
						color="primary"
						raised
						type="submit"
						fullwidth
						bold
					>
						Confirmer la commande
					</VButton>
				</div>
			</div>
		</form>
	</div>
</template>

<script setup lang="ts">
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { storeToRefs } from "pinia";
	import { Notyf } from "notyf";
	import { returnPreviousPage } from "/@src/utils/helpers";
	import { useViewWrapper } from "/@src/stores/viewWrapper";
	import { useHead } from "@vueuse/head";
	import Swal from "sweetalert2/dist/sweetalert2.js";
	import { usePriceFormat } from "/@src/composable/priceFormat";

	const viewWrapper = useViewWrapper();
	viewWrapper.setPageTitle("Nouvelle commande de plaque");
	useHead({ title: "Commande de plaques | SIMVEB" });

	const printStore = useCrudStore();
	const { row, formLoading, url, formData } = storeToRefs(printStore);
	const loading = ref(true);
	const notyf = new Notyf();
	const router = useRouter();
	const { formatPrice } = usePriceFormat();

	const confirmPlateOrder = async () => {
		url.value = "plate-orders";
		const formData = {
			plate_shapes: row.value.plate_shapes
				.filter((shape) => shape.colors.length > 0)
				.map((shape) => {
					return {
						...shape,
						colors: shape.colors.filter((color) => color.id && color.nb && color.nb > 0),
					};
				}),
		};
		await printStore.createRow(formData).then(({ nb_plate, amount }) => {
			Swal.fire({
				icon: "warning",
				title: "Confirmation",
				text: `Vous êtes sur le point de lancer une commande de ${nb_plate}
				 plaques pour un total de ${formatPrice(amount)}.`,
				showCancelButton: true,
				confirmButtonText: "Confirmer",
				cancelButtonText: "Annuler",
			}).then((result) => {
				if (result.isConfirmed) {
					printStore.createRow({ ...formData, validate: true }).then(() => {
						notyf.success("Commande de plaque créée avec succès");
						router.back();
					});
				}
			});
		});
	};

	onBeforeMount(() => {
		url.value = "plate-orders";
		row.value = {
			plate_shapes: [],
		};
	});

	const addColor = (shape) => {
		shape.colors.push({
			id: null,
			nb: null,
		});
	};

	onMounted(() => {
		if (!formData.value) {
			printStore.loadCreateData().then((data) => {
				data.shapes.forEach((shape) => {
					row.value.plate_shapes.push({
						id: shape.id,
						name: shape.name,
						cost: shape.cost,
						colors: [
							{
								id: null,
								nb: null,
							},
						],
					});
				});
			});
		}
		loading.value = false;
	});

	onUnmounted(() => {
		loading.value = false;
		printStore.reset();
	});
</script>

<style lang="scss">
	@import "/@src/scss/abstracts/all";

	.color-box {
		width: 20px;
		height: 20px;
		border-radius: 10%;
	}
	.is-navbar {
		.form-layout {
			margin-top: 30px;
		}
	}

	.form-layout {
		max-width: 740px;
		margin: 0 auto;

		&.is-separate {
			max-width: 1040px;

			.form-outer {
				background: none;
				border: none;

				.form-body {
					display: flex;

					.form-section {
						flex-grow: 2;
						padding: 10px;
						width: 50%;

						.form-section-inner {
							@include vuero-s-card;

							padding: 40px;

							&.has-padding-bottom {
								padding-bottom: 60px;
								height: 100%;
							}

							> h3 {
								font-family: var(--font-alt);
								font-size: 1.2rem;
								font-weight: 600;
								color: var(--dark-text);
								margin-bottom: 30px;
							}

							.columns {
								.column {
									padding-top: 0.25rem;
									padding-bottom: 0.25rem;
								}
							}

							.radio-boxes {
								display: flex;
								justify-content: space-between;
								margin-inline-start: -8px;
								margin-inline-end: -8px;

								.radio-box {
									position: relative;
									width: calc(50% - 16px);
									margin: 8px;

									&:focus-within {
										border-radius: 3px;
										outline-offset: var(--accessibility-focus-outline-offset);
										outline-width: var(--accessibility-focus-outline-width);
										outline-style: var(--accessibility-focus-outline-style);
										outline-color: var(--primary);
									}

									input {
										position: absolute;
										top: 0;
										inset-inline-start: 0;
										height: 100%;
										width: 100%;
										opacity: 0;
										cursor: pointer;

										&:checked {
											+ .radio-box-inner {
												background: var(--primary);
												border-color: var(--primary);
												box-shadow: var(--primary-box-shadow);

												.fee,
												p {
													color: var(--smoke-white);
												}
											}
										}
									}

									.radio-box-inner {
										background: var(--white);
										border: 1px solid var(--fade-grey-dark-3);
										text-align: center;
										border-radius: var(--radius);
										font-family: var(--font);
										font-weight: 600;
										font-size: 0.9rem;
										transition: color 0.3s, background-color 0.3s, border-color 0.3s, height 0.3s,
											width 0.3s;
										padding: 30px 20px;

										.fee {
											font-family: var(--font);
											font-weight: 700;
											color: var(--dark-text);
											font-size: 2.4rem;
											line-height: 1;

											span {
												&::after {
													content: "$";
													position: relative;
													top: -10px;
													font-size: 1.5rem;
												}
											}
										}

										p {
											font-family: var(--font-alt);
										}
									}
								}
							}

							.control {
								> p {
									padding-top: 12px;

									> span {
										display: block;
										font-size: 0.9rem;

										span {
											font-weight: 500;
											color: var(--dark-text);
										}
									}
								}
							}
						}

						.form-section-outer {
							.checkboxes {
								padding: 16px 0;

								.checkbox {
									padding: 0;
									font-size: 0.9rem;
								}
							}

							.button-wrap {
								.button {
									min-height: 60px;
									font-size: 1.05rem;
									font-weight: 600;
									font-family: var(--font-alt);
								}
							}
						}
					}
				}
			}
		}
	}

	.is-dark {
		.form-layout {
			&.is-separate {
				.form-outer {
					background: none !important;

					.form-body {
						.form-section {
							.form-section-inner {
								@include vuero-card--dark;

								> h3 {
									color: var(--dark-dark-text);
								}

								.radio-boxes {
									.radio-box {
										input:checked + .radio-box-inner {
											background: var(--primary);
											border-color: var(--primary);
											box-shadow: var(--primary-box-shadow);

											.fee,
											p {
												color: var(--smoke-white);
											}
										}

										.radio-box-inner {
											background: var(--dark-sidebar-light-2);
											border-color: var(--dark-sidebar-light-12);

											.fee {
												color: var(--dark-dark-text);
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}

	@media only screen and (width <= 767px) {
		.form-layout {
			&.is-separate {
				.form-outer {
					.form-body {
						padding-inline-start: 0;
						padding-inline-end: 0;
						flex-direction: column;

						.form-section {
							width: 100%;

							.form-section-inner {
								padding: 30px;
							}
						}
					}
				}
			}
		}
	}

	@media only screen and (width >= 768px) and (width <= 1024px) and (orientation: portrait) {
		.form-layout {
			&.is-separate {
				.form-outer {
					.form-body {
						padding-inline-start: 0;
						padding-inline-end: 0;

						// flex-direction: column;

						.form-section {
							// width: 100%;

							.form-section-inner {
								padding: 30px;
							}
						}
					}
				}
			}
		}
	}
</style>
