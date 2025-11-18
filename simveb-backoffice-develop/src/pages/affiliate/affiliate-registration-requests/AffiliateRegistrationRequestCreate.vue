<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { returnPreviousPage } from "/@src/utils/helpers";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { useNotyf } from "/@src/composable/useNotyf";

	const router = useRouter();
	const notyf = useNotyf();
	const crudStore = useCrudStore();
	const { url, formLoading, errors } = storeToRefs(crudStore);

	const item = ref({
		type: "company",
		profile_type_id: null,
		border_id: null,
		social_reason: null,
		seat: null,
		telephone: null,
		email: null,
		ifu: null,
		rccm: null,
		first_member_npi: null,
	});

	const documents = ref([]);
	const company_profile_types = ref([]);
	const state_profile_types = ref([]);
	const affiliate_types = ref([]);
	const borders = ref([]);
	const police_id = ref(null);

	onBeforeMount(() => {
		url.value = "/affiliate-registration-requests";
	});

	onMounted(() => {
		crudStore.loadCreateData().then((res) => {
			// positions.value = res.positions;
			profile_types.value = res.profile_types;
			affiliate_types.value = res.types;
			company_profile_types.value = res.company_profile_types;
			state_profile_types.value = res.state_profile_types;
			borders.value = res.borders;
			police_id.value = res.state_profile_types.find((item) => item.code == "police").id;
			// requiredDocumentTypes.value = res.required_document_types;
		});
	});

	const profile_types = computed(() => {
		return item.value.type == "company" ? company_profile_types.value : state_profile_types.value;
	});

	const submit = () => {
		/*let data = new FormData();

		Object.entries(item.value).forEach(([key, value]) => {
			if (key == "leader") {
				Object.entries(value).forEach(([index, elem]) => {
					data.append(`leader[${index}]`, elem);
				});
			} else {
				data.append(key, value);
			}
		});

		Object.entries(documents.value).forEach(([key, value]) => {
			data.append("documents[" + key + "][type_id]", value.type_id);
			data.append("documents[" + key + "][file]", value.content);
		});*/

		crudStore.createWithFile(item.value).then(() => {
			notyf.success("Enregistrement effectué avec succès!");
			router.push({ name: "affiliate_registration_requests" });
		});
	};

	const handleFileUpload = (e) => {
		let isNew = true;
		let lastIndex = null;
		Object.entries(documents.value).forEach(([key, value]) => {
			if (value.name == e.target.name) {
				isNew = false;
				lastIndex = key;
			}
		});
		if (isNew) {
			documents.value.push({
				type_id: e.target.dataset.type_id,
				name: e.target.name,
				content: e.target.files[0],
			});
		} else {
			documents.value[lastIndex] = {
				type_id: e.target.dataset.type_id,
				name: e.target.name,
				content: e.target.files,
			};
		}
	};
</script>
<template>
	<CreateFormWrapper :col="10" @submit="submit">
		<template #form-head-inner>
			<div class="left">
				<h3>Enregistrement d'affilié</h3>
				<p>Création</p>
			</div>
			<div class="right">
				<div class="buttons">
					<VButton
						icon="fa-light fa-arrow-left rem-100"
						light
						dark-outlined
						type="reset"
						@click="returnPreviousPage($router)"
					>
						Retour
					</VButton>
					<VButton color="primary" raised :loading="formLoading" tabindex="0" type="submit">
						Enregistrer
					</VButton>
				</div>
			</div>
		</template>
		<template #form-body>
			<div class="form-section-inner">
				<h3 class="has-text-centered">Type d'affilié</h3>
				<div class="columns is-multiline">
					<div class="column is-12">
						<VField>
							<VControl>
								<div class="radio-boxes">
									<VControl v-for="type in affiliate_types" :key="type.key" class="radio-box">
										<input
											v-model="item.type"
											type="radio"
											name="affiliate_type"
											:value="type.key"
										/>
										<div class="radio-box-inner">
											<VIconBox size="xl">
												<i
													class="lnil"
													:class="type.key == 'company' ? ' lnil-apartment' : 'lnil-folder'"
												></i>
											</VIconBox>
											<p>{{ type.value }}</p>
										</div>
									</VControl>
								</div>
							</VControl>
						</VField>
					</div>
				</div>
			</div>

			<div class="form-fieldset mt-4">
				<div class="fieldset-heading">
					<h4>Type de profile</h4>
					<p>Veuillez choisir le type qui correspond à l'affilié</p>
				</div>
				<div class="columns is-multiline">
					<div class="column is-12">
						<VField>
							<VControl>
								<div class="radio-pills">
									<div v-for="profile in profile_types" :key="profile.code" class="radio-pill">
										<input
											v-model="item.profile_type_id"
											type="radio"
											name="profile_type_id"
											:value="profile.id"
										/>
										<div class="radio-pill-inner">
											<span>{{ profile.name }}</span>
										</div>
									</div>
								</div>
							</VControl>
						</VField>
					</div>
					<div v-if="item.profile_type_id === police_id" class="column is-12">
						<div class="mb-1 has-text-medium">Frontière d'activité</div>
						<VField horizontal>
							<VControl fullwidth>
								<v-select
									v-model="item.border_id"
									:options="borders"
									label="name"
									:reduce="(item) => item.id"
								>
								</v-select>
							</VControl>
						</VField>
					</div>
				</div>
			</div>

			<div class="form-fieldset">
				<div class="fieldset-heading">
					<h4>Informations de l'affilié</h4>
					<p>Veuillez fournir les informations de l'affilié</p>
				</div>

				<div class="columns is-multiline">
					<div class="column is-6">
						<VField>
							<VLabel>Raison sociale</VLabel>
							<VControl icon="briefcase" :errors="errors.social_reason || []">
								<VInput
									v-model="item.social_reason"
									type="text"
									autocomplete="social_reason"
									name="social_reason"
								/>
							</VControl>
						</VField>
					</div>
					<div class="column is-6">
						<VField>
							<VLabel>Téléphone</VLabel>
							<VControl icon="phone" :errors="errors.telephone || []">
								<VInput
									v-model="item.telephone"
									type="tel"
									placeholder=""
									autocomplete="tel"
									name="telephone"
									inputmode="tel"
								/>
							</VControl>
						</VField>
					</div>
					<div class="column is-6">
						<VField>
							<VLabel>IFU</VLabel>
							<VControl :errors="errors.ifu || []">
								<VInput
									v-model="item.ifu"
									type="text"
									pattern="[0-9]{13}"
									name="ifu"
									maxlength="13"
									placeholder="Immatriculation Fiscale Unique"
									autocomplete="ifu"
								/>
							</VControl>
						</VField>
					</div>
					<div class="column is-6">
						<VField>
							<VLabel>RCCM</VLabel>
							<VControl :errors="errors.rccm || []">
								<VInput
									v-model="item.rccm"
									name="rccm"
									type="text"
									placeholder="Registre du Commerce et du Crédit Mobilier"
									autocomplete="rccm"
								/>
							</VControl>
						</VField>
					</div>
					<div class="column is-12">
						<VField>
							<VLabel>Adresse</VLabel>
							<VControl>
								<VInput v-model="item.seat" type="text" placeholder="" name="seat" />
							</VControl>
						</VField>
					</div>
					<div class="column is-12">
						<VField>
							<VLabel>Adresse Email</VLabel>
							<VControl icon="mail">
								<VInput
									v-model="item.email"
									type="email"
									name="email"
									placeholder=""
									autocomplete="email"
									inputmode="email"
								/>
							</VControl>
						</VField>
					</div>
					<div class="column is-12 mt-4">
						<VField>
							<VLabel>NPI du dirigeant</VLabel>
							<VControl>
								<VInput
									v-model="item.first_member_npi"
									type="text"
									name="first_member_npi"
									placeholder=""
									autocomplete="npi"
									pattern="[0-9]{10}"
									maxlength="10"
								/>
							</VControl>
						</VField>
					</div>
				</div>
			</div>
		</template>
	</CreateFormWrapper>
</template>

<style lang="scss">
	.radio-pills {
		display: flex;

		.radio-pill {
			margin-right: 15px;
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
				height: 40px;
				width: 120px;
				background: var(--white);
				border: 1px solid var(--fade-grey-dark-3);
				border-radius: 8px;
				font-family: var(--font), serif;
				font-weight: 600;
				font-size: 0.9rem;
				transition: color 0.3s, background-color 0.3s, border-color 0.3s, height 0.3s, width 0.3s;
			}
		}
	}
	.form-fieldset {
		max-width: 550px;
	}

	.form-section-inner {
		flex: 1;
		display: inline-block;
		width: 100%;
		background-color: var(--white);
		border-radius: var(--radius-large);
		transition: all 0.3s;

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
			justify-content: space-evenly;
			margin-inline-start: -8px;
			margin-inline-end: -8px;

			.radio-box {
				position: relative;
				width: calc(20% - 16px);
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
					transition: color 0.3s, background-color 0.3s, border-color 0.3s, height 0.3s, width 0.3s;
					padding: 10px 10px;

					p {
						font-family: var(--font-alt);
					}
					.v-icon {
						margin: 0 auto 10px;
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
</style>
