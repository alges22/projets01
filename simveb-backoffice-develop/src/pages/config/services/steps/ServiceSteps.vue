<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import draggable from "vuedraggable";

	const props = defineProps({
		update: {
			type: Boolean,
			default: false,
		},
	});

	const emit = defineEmits(["next", "prev"]);

	const crudStore = useCrudStore();
	const { row: service, formData, formLoading } = storeToRefs(crudStore);

	const selectedSteps = ref([]);

	const bindSteps = () => {
		if (props.update) {
			selectedSteps.value = service.value.steps.map((step, index: KeyIndex) => {
				return {
					id: step.id,
					label: step.label,
					position: index + 1,
					duration: step.pivot.duration / 60 / 60,
					process_type: step.pivot?.process_type,
				};
			});
		} else {
			resetSteps();
		}
	};

	const resetSteps = () => {
		selectedSteps.value = formData?.value.steps.map((step, index: KeyIndex) => {
			return {
				id: step.id,
				label: step.label,
				position: index + 1,
				duration: null,
				process_type: "automatic",
			};
		});
	};

	const removeStep = (step) => {
		selectedSteps.value = selectedSteps.value.filter((s) => s.id !== step);
	};

	const submit = () => {
		service.value.steps = selectedSteps.value.map((step, index) => {
			return {
				step_id: step.id,
				position: index + 1,
				duration: step.duration,
				process_type: step.process_type,
			};
		});
		emit("next");
	};

	onMounted(() => {
		bindSteps();
	});
</script>

<template>
	<div class="form-fieldset" style="width: 100%">
		<div style="width: 100%" class="list-widget is-straight">
			<div class="widget-head">
				<div>
					<h3 class="dark-inverted">Les étapes</h3>
					<p>Glissez pour ordonner</p>
				</div>
				<VButton @click="resetSteps">Réinitialiser</VButton>
			</div>

			<div class="inner-list">
				<div class="icon-timeline">
					<draggable v-model="selectedSteps" item-key="id">
						<template #item="{ element: step, index }">
							<div class="timeline-item">
								<div class="timeline-icon">
									<i aria-hidden="true" class="fa-light fa-arrow-up-arrow-down" />
								</div>
								<div class="timeline-content">
									<p>{{ step.label }}</p>
								</div>
								<div class="timeline-input">
									<VField>
										<VControl>
											<VInput
												v-model="step.duration"
												:name="'step-duration-' + index"
												type="number"
												class="is-rounded"
												placeholder="Durée en heures"
												min="0"
											/>
										</VControl>
									</VField>
								</div>
								<div class="timeline-content">
									<span>
										<input
											type="radio"
											:name="step.id"
											value="automatic"
											v-model="step.process_type"
										/>
										<label for="" @click="step.process_type = 'automatic'" class="is-size-6 ml-1"
											>Automatique</label
										>
									</span>
									<span class="ml-2">
										<input
											type="radio"
											:name="step.id"
											value="manual"
											v-model="step.process_type"
										/>
										<label for="" class="is-size-6 ml-1" @click="step.process_type = 'manual'"
											>Manuel</label
										>
									</span>
								</div>
								<div class="timeline-actions">
									<VIconButton
										icon="xmark"
										raised
										color="danger"
										circle
										light
										@click="removeStep(step.id)"
									/>
								</div>
							</div>
						</template>
					</draggable>
				</div>
			</div>
		</div>

		<div class="column is-12 flex justify-between">
			<VButton
				color="primary"
				raised
				:loading="formLoading"
				tabindex="0"
				type="button"
				icon="fa-light fa-arrow-left"
				@click="$emit('prev')"
			>
				Précedent
			</VButton>
			<VButton color="primary" raised :loading="formLoading" tabindex="0" type="button" @click="submit">
				Suivant
				<i class="ms-2 fa-light fa-arrow-right"></i>
			</VButton>
		</div>
	</div>
</template>

<style lang="scss">
	.form-fieldset {
		width: 60%;
		max-width: 100% !important;
	}

	@import "/@src/scss/abstracts/all";

	.list-widget {
		@include vuero-l-card;

		padding: 30px;

		&:not(:last-child) {
			margin-bottom: 1.5rem;
		}

		&.is-straight {
			@include vuero-s-card;
		}

		.widget-head {
			display: flex;
			align-items: center;
			justify-content: space-between;
			height: 32px;
			margin-bottom: 10px;

			h3 {
				color: var(--dark-text);
				font-size: 1.1rem;
				font-weight: 500;
			}
		}

		.inner-list {
			padding: 10px 0;

			.inner-list-item {
				+ .inner-list-item {
					margin-top: 24px;
				}
			}
		}
	}

	.is-dark {
		.list-widget {
			@include vuero-card--dark;
		}
	}

	.list-widget {
		.icon-timeline {
			.timeline-item {
				position: relative;
				display: flex;
				padding-bottom: 30px;
				cursor: grab;

				&.sortable-choosen {
					opacity: 0.5;
					background: var(--primary);
				}

				&::after {
					content: "";
					position: absolute;
					top: 36px;
					inset-inline-start: 18px;
					width: 1px;
					height: calc(100% - 36px);
					border-inline-start: 1px solid var(--fade-grey-dark-3);
				}

				.timeline-icon {
					position: relative;
					height: 36px;
					width: 36px;
					display: flex;
					justify-content: center;
					align-items: center;
					background: var(--white);
					border: 1px solid var(--fade-grey-dark-3);
					border-radius: var(--radius-rounded);
					color: var(--light-text);
					box-shadow: var(--light-box-shadow);

					&::after {
						content: "";
						position: absolute;
						top: 17px;
						inset-inline-start: 40px;
						width: 20px;
						height: 1px;
						border-top: 1px solid var(--fade-grey-dark-3);
					}

					&.is-squared {
						border-radius: 10px;

						img {
							border-radius: 10px;
						}
					}

					&.is-primary {
						background: var(--primary);
						border-color: var(--primary);
						box-shadow: var(--primary-box-shadow);

						svg {
							color: var(--smoke-white);
						}
					}

					&.is-info {
						background: var(--info);
						border-color: var(--info);
						box-shadow: var(--info-box-shadow);

						svg {
							color: var(--smoke-white);
						}
					}

					&.is-success {
						background: var(--success);
						border-color: var(--success);
						box-shadow: var(--success-box-shadow);

						svg {
							color: var(--smoke-white);
						}
					}

					&.is-orange {
						background: var(--orange);
						border-color: var(--orange);
						box-shadow: var(--orange-box-shadow);

						svg {
							color: var(--smoke-white);
						}
					}

					&.is-yellow {
						background: var(--yellow);
						border-color: var(--yellow);

						svg {
							color: var(--smoke-white);
						}
					}

					img {
						display: block;
						height: 28px;
						width: 28px;
						border-radius: var(--radius-rounded);
					}

					svg {
						height: 16px;
						width: 16px;
						stroke-width: 1.6px;
					}
				}

				.timeline-content {
					margin-inline-start: 34px;
					line-height: 1.2;
					display: flex;
					align-items: center;
					width: 200px;
					justify-content: center;

					span {
						font-size: 0.85rem;
						color: var(--light-text);
					}

					p {
						font-family: var(--font-alt);
						font-size: 0.95rem;
						font-weight: 500;
						color: var(--dark-text);
					}
					&::after {
						content: "";
						position: absolute;
						top: 17px;
						inset-inline-start: 240px;
						width: 40px;
						height: 1px;
						border-top: 1px solid var(--fade-grey-dark-3);
					}
				}

				.timeline-input {
					margin-inline-start: 34px;
					line-height: 1.2;
					display: flex;
					align-items: center;
					width: 200px;
				}
				.timeline-actions {
					margin-inline-start: auto;
				}
			}
		}
	}

	.is-dark {
		.list-widget {
			.icon-timeline {
				.timeline-item {
					&::after {
						border-color: var(--dark-sidebar-light-12) !important;
					}

					.timeline-icon:not(.is-primary, .is-info, .is-success, .is-orange, .is-yellow) {
						background: var(--dark-sidebar-light-3) !important;
						border-color: var(--dark-sidebar-light-12) !important;
					}

					.timeline-icon {
						&::after {
							border-color: var(--dark-sidebar-light-12) !important;
						}

						&.is-primary {
							background: var(--primary);
							border-color: var(--primary);
							box-shadow: var(--primary-box-shadow);

							svg {
								color: var(--smoke-white);
							}
						}
					}

					.timeline-content {
						p {
							color: var(--dark-dark-text);
						}
					}
				}
			}
		}
	}
</style>
