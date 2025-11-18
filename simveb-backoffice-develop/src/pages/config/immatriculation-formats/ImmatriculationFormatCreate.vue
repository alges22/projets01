<template>
	<div class="page-content-inner">
		<form class="form-layout is-separate" @submit.prevent="handleSubmit">
			<div class="form-outer">
				<div class="form-body">
					<div class="form-section">
						<div class="form-section-inner has-padding-bottom">
							<h3 class="">Catégorie de véhicule</h3>
							<VField v-if="categories.length > 0" class="mb-5">
								<VControl>
									<Multiselect
										required
										:errors="errors.vehicle_category_id"
										v-model="format.vehicle_category_id"
										:options="categories"
										label="label"
										mode="single"
										name="vehicle_category_id"
										placeholder="Choisissez la catégorie du véhicule"
										value-prop="id"
									/>
								</VControl>
							</VField>

							<h3 class="">Profile associé</h3>
							<VField v-if="profiles.length > 0" class="mb-5">
								<VControl>
									<Multiselect
										:errors="errors.profile_type_id"
										v-model="format.profile_type_id"
										:options="profiles"
										label="name"
										mode="single"
										name="profile_type_id"
										placeholder="Choisissez le type de profil"
										value-prop="id"
									/>
								</VControl>
							</VField>

							<h3 class="">Composants de l'immatriculation</h3>
							<!--							<VField>-->
							<!--								<div class="buttons">-->
							<!--									<VButton @click="false">Ajouter</VButton>-->
							<!--								</div>-->
							<!--							</VField>-->

							<draggable v-model="componentConfigs" item-key="id" @end="updatePosition">
								<template #item="{ element: config }">
									<div class="form-section-output">
										<div
											class="output"
											@keydown.space.prevent="() => (config.toggle = !config.toggle)"
											@click.prevent="() => (config.toggle = !config.toggle)"
										>
											<i aria-hidden="true" class="fa-light fa-arrow-up-arrow-down mr-4"></i>
											<span>{{ config.description }}</span>
											<div class="action">
												<VIconButton
													icon="trash"
													color="danger"
													light
													@click="
														componentConfigs.splice(componentConfigs.indexOf(config), 1)
													"
												/>
											</div>
										</div>
										<Transition name="fade-fast">
											<div v-if="config.toggle" class="fieldset">
												<div class="columns my-3">
													<div class="column is-6">
														<VField>
															<VLabel>Taille</VLabel>
															<VControl>
																<VInput
																	v-model="config.size"
																	:name="'size-' + config.id"
																	placeholder="Taille"
																	title="La taille du composant sur l'immatriculation"
																	type="number"
																/>
															</VControl>
														</VField>
													</div>
													<div class="column is-6">
														<VField>
															<VLabel>Valeur</VLabel>
															<template v-if="config.possible_values">
																<VControl>
																	<Multiselect
																		v-model="config.value"
																		:name="'value-' + config.id"
																		:options="config.possible_values"
																		placeholder="Valeur"
																		title="La valeur du composant sur l'immatriculation"
																	/>
																</VControl>
															</template>
															<template v-else>
																<VControl>
																	<VInput
																		v-model="config.value"
																		:name="'value-' + config.id"
																		placeholder="Valeur"
																		title="La valeur du composant sur l'immatriculation"
																		type="text"
																	/>
																</VControl>
															</template>
														</VField>
													</div>
												</div>
											</div>
										</Transition>
									</div>
								</template>
							</draggable>
						</div>
					</div>

					<div class="form-section">
						<div class="form-section-inner">
							<h3 class="has-text-centered">Aperçu</h3>
							<div class="columns is-multiline">
								<div class="column is-12">
									<SquarePlateView
										:immatriculation-number="
											[...componentConfigs]
												.sort((a, b) => a.position - b.position)
												.map((config) => config.value)
										"
										bg-color="#F5F5DC"
										text-color="#000"
									/>
									<div class="mt-4"></div>
									<RectanglePlateView
										:immatriculation-number="
											[...componentConfigs]
												.sort((a, b) => a.position - b.position)
												.map((config) => config.value)
										"
										bg-color="#F5F5DC"
										text-color="#000"
									/>
								</div>
							</div>
						</div>
						<div class="button-wrap mt-4">
							<VButton bold color="primary" fullwidth raised type="submit">
								{{ update ? "Mettre à jour" : "Créer" }}
							</VButton>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</template>

<script lang="ts" setup>
	import { useImmatriculationFormatStore } from "/@src/stores/modules/immatriculation-format";
	import { storeToRefs } from "pinia";
	import { useViewWrapper } from "/@src/stores/viewWrapper";
	import { Notyf } from "notyf";
	import SquarePlateView from "/src/components/ImmatriculationPlateView.vue";
	import draggable from "vuedraggable";

	const viewWrapper = useViewWrapper();
	const update = ref(false);

	const notyf = new Notyf();
	const props = defineProps({
		id: {
			type: String,
			required: false,
		},
	});

	const store = useImmatriculationFormatStore();
	const { format, categories, components, profiles, errors } = storeToRefs(store);
	const route = useRoute();
	const componentConfigs = ref([]);
	const router = useRouter();

	const bindConfigComponent = () => {
		if (update.value) {
			componentConfigs.value = components.value.map((component, index: KeyIndex) => {
				return {
					id: component.id,
					size: component.pivot?.length ?? component.default_length,
					code: component.code,
					position: index + 1,
					description: component.description,
					possible_values: component.possible_values,
					value: component.pivot.value,
					toggle: false,
				};
			});
		} else {
			componentConfigs.value = components.value.map((component, index: KeyIndex) => {
				return {
					id: component.id,
					size: null,
					code: component.code,
					position: index + 1,
					description: component.description,
					possible_values: component.possible_values,
					value: component.possible_values ? component.possible_values[0] : "",
					toggle: false,
				};
			});
		}
	};

	/*const addComponent = () => {
	     componentConfigs.value.push({
	         id: componentConfigs.value.id,
	         size: null,
	         code: "",
	         position: componentConfigs.value.length + 1,
	         description: "",
	         possible_values: [],
	         value: "",
	         toggle: false,
	     });
	 };*/

	const handleSubmit = async () => {
		let data = {
			components: [
				...componentConfigs.value.map((component) => {
					return {
						id: component.id,
						position: component.position,
						value: component.value,
					};
				}),
			],
			vehicle_category_id: format.value.vehicle_category_id,
			profile_type_id: format.value.profile_type_id,
		};
		if (update.value) {
			await store.updateFormat(format.value.id, data).then(() => {
				notyf.success("Format d'immatriculation modifié avec succès");
				router.back();
			});
		} else {
			await store.createFormat(data).then(async () => {
				notyf.success("Format d'immatriculation crée avec succès");
				await router.push({ name: "immatriculation_formats" });
			});
		}
	};

	const updatePosition = (evt) => {
		// ATTENTION : c'est grave ce qui est fait ici.
		const element = componentConfigs.value.find((comp) => comp.position == evt.oldIndex + 1);
		const movedElement = componentConfigs.value.find((comp) => comp.position == evt.newIndex + 1);

		element.position = evt.newIndex + 1;
		movedElement.position = evt.oldIndex + 1;
	};

	onMounted(async () => {
		if (props.id) {
			update.value = true;
			await store.editFormat(props.id);
			viewWrapper.setPageTitle("Mettre à jour le format d'immatriculation");
		} else {
			viewWrapper.setPageTitle("Création de format d'immatriculation");
			await store.loadCreateData().then(() => {});
		}
		bindConfigComponent();
	});
</script>

<style lang="scss">
	@import "/@src/scss/abstracts/all";

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
							height: calc(100% - 38px - 1rem);

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

	.form-section {
		&.is-active {
			display: block;
		}

		+ .form-section {
		}

		.form-section-title {
			font-family: var(--font-alt);
			font-weight: 600;
			color: var(--dark-text);
			margin-bottom: 1rem;

			button {
				position: relative;
				top: 4px;
				padding: 0;
				height: 18px;
				width: 18px;
				border: none;
				//background: none;
				cursor: pointer;
				margin-inline-start: 0.25rem;

				svg {
					height: 18px;
					width: 18px;
					pointer-events: none;
				}
			}
		}

		.fieldset {
			padding: 0.75rem;
			border-radius: 0.5rem;
			border: 1px solid var(--border);
			background: var(--widget-grey-dark-3);

			.fieldset-separator {
				margin: 1.5rem 0;
				border-top: 1px solid var(--border);
			}
		}

		.field {
			> label {
				margin-bottom: 0.25rem;
				display: inline-block;
			}

			> .buttons {
				padding: 2rem 0;
			}
		}

		.flex-label {
			display: flex;
			align-items: center;
			height: 100%;

			h4 {
				font-family: var(--font);
				font-weight: 500;
				color: var(--dark-text);
			}
		}

		.subcontrol {
			display: flex;
			align-items: center;
			justify-content: flex-end;
			min-width: 175px;
			padding-inline-end: 1rem;

			.checkbox {
				padding: 0;
			}
		}

		.input-button {
			height: 44px;
			width: 100%;
			border: 2px dashed var(--border);
			border-radius: 0.65rem;
			background: var(--widget-grey-dark-3);
			display: flex;
			align-items: center;
			padding-inline-start: calc(0.75em - 1px);
			padding-inline-end: calc(0.75em - 1px);
			padding-top: 0;
			padding-bottom: 0;
			color: var(--dark-text);
			cursor: pointer;
			transition: color 0.3s, background-color 0.3s, border 0.3s, box-shadow 0.3s;

			&:focus-visible {
				outline-offset: var(--accessibility-focus-outline-offset);
				outline-width: var(--accessibility-focus-outline-width);
				outline-style: var(--accessibility-focus-outline-style);
				outline-color: var(--accessibility-focus-outline-color);
			}

			&:hover {
				background: var(--white);
				border: 2px solid var(--primary);
				color: var(--primary);
				box-shadow: var(--light-box-shadow);
			}

			svg {
				height: 18px;
				width: 16px;
			}

			span {
				font-family: var(--font);
				margin-inline-start: 0.75rem;
			}
		}

		.options {
			display: flex;
			flex-wrap: wrap;
			margin-inline-start: -0.5rem;
			margin-inline-end: -0.5rem;

			.option {
				position: relative;
				width: calc(33.3% - 1rem);
				margin: 0.5rem;

				&:focus-within {
					border-radius: 4px;
					outline-offset: var(--accessibility-focus-outline-offset);
					outline: var(--accessibility-focus-outline-color) var(--accessibility-focus-outline-style)
						var(--accessibility-focus-outline-width);
				}

				input {
					position: absolute;
					top: 0;
					inset-inline-start: 0;
					height: 100%;
					width: 100%;
					z-index: 1;
					opacity: 0;
					cursor: pointer;

					&:checked {
						~ .indicator {
							transform: scale(1);
						}

						~ .option-inner {
							border-color: var(--primary);
							box-shadow: var(--light-box-shadow);

							i {
								color: var(--primary);
							}
						}
					}
				}

				.indicator {
					position: absolute;
					top: 1rem;
					inset-inline-end: 1rem;
					display: flex;
					justify-content: center;
					align-items: center;
					height: 20px;
					width: 20px;
					color: var(--white);
					background: var(--primary);
					border-radius: 50%;
					transform: scale(0);
					transition: transform 0.3s;

					svg {
						height: 14px;
						width: 14px;
						stroke-width: 3px;
					}
				}

				.option-inner {
					padding: 1.5rem;
					background: var(--white);
					border: 1px solid var(--border);
					border-radius: 0.5rem;
					transition: border 0.3s, box-shadow 0.3s;

					h4 {
						color: var(--dark-text);
						font-weight: 600;
						font-family: var(--font-alt);
					}

					p {
						font-size: 0.9rem;
					}

					i {
						font-size: 2.25rem;
						color: var(--light-text);
						margin-bottom: 0.25rem;
					}
				}
			}
		}

		.validation-box {
			display: flex;
			padding: 2rem;
			background: var(--white);
			border: 1px solid var(--border);
			border-radius: 0.5rem;
			transition: border 0.3s, box-shadow 0.3s;

			.box-content {
				h3 {
					font-family: var(--font-alt);
					font-size: 1.25rem;
					font-weight: 600;
					margin-bottom: 0.75rem;
				}

				p {
					font-size: 1rem;
				}
			}

			.box-illustration {
				position: relative;
				height: 100px;
				min-width: 40%;

				img {
					position: absolute;
					inset-inline-end: 0;
					bottom: 0;
					max-height: 180px;
				}
			}
		}

		.form-section-output {
			margin-top: 1.5rem;

			.output {
				height: 52px;
				width: 100%;
				border: 1px solid var(--border);
				border-radius: 0.65rem;
				background: var(--white);
				display: flex;
				align-items: center;
				padding-inline-start: calc(1em - 1px);
				padding-inline-end: calc(1em - 1px);
				padding-top: 0;
				padding-bottom: 0;
				color: var(--dark-text);
				cursor: pointer;
				transition: color 0.3s, background-color 0.3s, border 0.3s, box-shadow 0.3s;

				&:not(:last-child) {
					margin-bottom: 1rem;
				}

				> svg {
					height: 18px;
					width: 18px;
					margin-inline-end: 0.75rem;
					color: var(--light-text);
				}

				> span {
					font-weight: 500;
					font-family: var(--font);
					color: var(--dark-text);
				}

				.action {
					margin-inline-start: auto;

					button {
						display: flex;
						justify-content: center;
						align-items: center;
						height: 38px;
						width: 38px;
						min-width: 38px;
						background: none;
						border: none;
						padding: 0;
						cursor: pointer;
						border-radius: 0.5rem;
						transition: background-color 0.3s;

						&:hover,
						&:focus {
							background: var(--widget-grey-dark-1);
						}

						svg {
							height: 18px;
							width: 18px;
							stroke-width: 1.5px;
						}
					}
				}
			}
		}
	}

	.radio-pills {
		display: flex;
		justify-content: start;
		align-content: space-between;
		flex-wrap: wrap;

		.radio-pill {
			position: relative;

			input {
				position: absolute;
				top: 0;
				inset-inline-start: 0;
				height: 100%;
				width: 100%;
				opacity: 0;
				cursor: pointer;

				&:checked {
					+ .radio-pill-inner {
						background: var(--primary);
						border-color: var(--primary);
						box-shadow: var(--primary-box-shadow);
						color: var(--white);
					}
				}
			}

			.radio-pill-inner {
				display: flex;
				align-items: center;
				justify-content: center;
				width: 80px;
				height: 40px;
				background: var(--white);
				border: 1px solid var(--fade-grey-dark-3);
				border-radius: 8px;
				font-family: var(--font);
				font-weight: 600;
				margin-bottom: 10px;
				font-size: 0.9rem;
				transition: color 0.3s, background-color 0.3s, border-color 0.3s, height 0.3s, width 0.3s;
			}
		}
	}
</style>
