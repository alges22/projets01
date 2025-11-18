import { defineStore } from "pinia";
import { useCrudStore } from "./crud";
import client from "/@src/composable/axiosClient";

export const useServiceStore = defineStore("service", {
	state: () => ({
		activeStep: 0,
		lastStep: 1,
		// data de la req create
		vehicleCaracteristicCategoryModelType: "",
		vehicleCategoryModelType: "",
		vehicleOwnerTypeModelType: "",
		vehicleCaracteristicCategories: [],
		vehicleOwnerTypes: [],
		services: [],
		vehicleCategories: [],
		types: [],
		documents: [],
		organizations: [],
		steps: [],
		serviceIsChildService: false,
		serviceHaveExtraService: false,
		// si c'est un update
		update: false,
		serviceId: "",
	}),
	getters: {},
	actions: {
		setActiveStep(activeStep: number) {
			this.activeStep = activeStep;
		},
		chargeData() {
			const crudStore = useCrudStore();
			if (this.vehicleCaracteristicCategories.length == 0) {
				crudStore.url = "/services";
				crudStore.loadCreateData().then((res: any) => {
					// caracteristique du vehicule
					this.vehicleCaracteristicCategories = res.vehicle_characteristic_categories.vehicle_characteristics;
					this.vehicleCaracteristicCategoryModelType = res.vehicle_characteristic_categories.model_type;
					this.vehicleCaracteristicCategories.forEach((element: any) => {
						element.vehicle_characteristics.forEach((el: any) => {
							el["price"] = 0;
							el["checked"] = false;
						});
					});
					// type de propriétaire de vehicle
					this.vehicleOwnerTypes = res.vehicle_owner_types.owner_types;
					this.vehicleOwnerTypeModelType = res.vehicle_owner_types.model_type;
					this.vehicleOwnerTypes.forEach((element: any) => {
						element["price"] = 0;
						element["checked"] = false;
					});
					// caegory de vehicle
					this.vehicleCategories = res.vehicle_categories.categories;
					this.vehicleCategoryModelType = res.vehicle_categories.model_type;
					this.vehicleCategories.forEach((element: any) => {
						element["price"] = 0;
						element["checked"] = false;
					});
					// services
					this.services = res.services;
					// type de services
					this.types = res.types;
					// type de document
					this.documents = res.documents;
					// organizations
					this.organizations = res.organizations;
					// step
					this.steps = res.steps;
				});
			}
			if (this.update) {
				crudStore.fetchRow(this.serviceId).then((res: any) => {
					// this.serviceDocuments = res.documents.map((d) => d.id);
					this.serviceSteps = res.steps
						.sort((a: any, b: any) => a.pivot.position - b.pivot.position)
						.map((s: any) => {
							return {
								id: s.id,
								label: s.label,
								position: s.pivot.position,
								duration: s.pivot.duration,
								process_type: s.pivot.process_type,
							};
						});
					this.serviceImage = null;
					this.serviceCode = res.code;
					this.serviceTypeId = res.type.id;
					this.serviceName = res.name;
					this.serviceCost = res.cost;
					this.serviceDescription = res.description;
					this.serviceExtract = res.extract;
					this.serviceProcedures = res.procedures;
					this.serviceColor = res.color;
					this.serviceDuration = res.duration;
					this.serviceIsChildService = res.is_child;
					this.serviceWhoCanApply = res.who_can_apply;
					this.serviceLink = res.link;
					this.serviceTargetOrganizationId = res.target_organization_id;
					this.vehicleCategoryId = res.vehicle_category_id;
					(this.serviceDocuments = res.documents.map((el: any) => el.id)), (this.steps = res.steps);
					this.serviceCanBeDemanded = res.can_be_demanded;
					this.serviceIsActive = res.is_active;
					this.serviceHaveExtraService = res.service_extra_services.length == 0 ? false : true;
					this.serviceParentServiceId = res.parent_service_id;
					this.vehicleOwnerTypes.forEach((element) => {
						res.vehicle_owner_types.forEach((_element) => {
							if (_element.id == element.id) {
								(element["checked"] = true), (element["price"] = _element.pivot.price);
							}
						});
					});
					this.vehicleCategories.forEach((element) => {
						res.vehicle_categories.forEach((_element) => {
							if (_element.id == element.id) {
								(element["checked"] = true), (element["price"] = _element.pivot.price);
							}
						});
					});
					this.vehicleCaracteristicCategories.forEach((element: any) => {
						element.vehicle_characteristics.forEach(($el: any) => {
							res.vehicle_characteristics.forEach((_el) => {
								_el.characteristics.forEach((el) => {
									if ($el.id == el.id) {
										($el["checked"] = true), ($el["price"] = el.pivot.price);
									}
								});
							});
						});
					});

					this.serviceSteps = res.steps;
				});
			}
		},
		createByService(id: string) {
			const url = "/action/create-by-services/" + id;
			client.get(url).then((response: any) => {
				// console.log(response.data);
			});
		},
		async sendPriceVariation(serviceId: string, data: any) {
			const crudStore = useCrudStore();
			return await crudStore.updateWithFile(serviceId, data);
		},
		changeStatus(serviceId: string, status: boolean) {
			const _data = {
				is_active: status,
				_method: "put",
			};
			return new Promise((resolve, reject) => {
				client
					.post("/toggle-service/" + serviceId, _data)
					.then((res) => res.data)
					.then((data) => resolve(data))
					.catch((err) => reject(err));
			});
		},
	},
});
