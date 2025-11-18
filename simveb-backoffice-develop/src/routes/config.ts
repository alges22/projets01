import BlacklistIndexVue from "/@src/pages/BlacklistIndex.vue";
import DocumentTypeIndexVue from "/@src/pages/config/DocumentTypeIndex.vue";
import RequiredDocumentTypeIndexVue from "/@src/pages/RequiredDocumentTypeIndex.vue";
import OwnerTypeIndexVue from "/@src/pages/OwnerTypeIndex.vue";
import VehicleCategoriesIndexVue from "/@src/pages/config/VehicleCategoriesIndex.vue";
import ImmatriculationFormatCreate from "/@src/pages/config/immatriculation-formats/ImmatriculationFormatCreate.vue";
import ImmatriculationFormatIndex from "/@src/pages/config/immatriculation-formats/ImmatriculationFormatIndex.vue";
import StaffIndex from "/@src/pages/config/staff/StaffIndex.vue";
import StaffCreate from "/@src/pages/config/staff/StaffCreate.vue";
import RoleIndex from "/@src/pages/config/roles/RoleIndex.vue";
import RoleCreate from "/@src/pages/config/roles/RoleCreate.vue";
import PermissionIndex from "/@src/pages/config/permissions/PermissionIndex.vue";
import permission from "/@src/routes/middlewares/permission";
import ZoneIndex from "/@src/pages/config/zones/ZoneIndex.vue";
import AffiliateCategoryIndex from "/@src/pages/config/affiliate-categories/AffiliateCategoryIndex.vue";
import AffiliateCategoryCreate from "/@src/pages/config/affiliate-categories/AffiliateCategoryCreate.vue";
import PlateColorCreate from "/@src/pages/config/plate-colors/PlateColorCreate.vue";
import PlateColorIndex from "/@src/pages/config/plate-colors/PlateColorIndex.vue";
import ParcsIndex from "/@src/pages/config/parcs/ParcsIndex.vue";
import FrontieresIndex from "/@src/pages/config/frontieres/FrontieresIndex.vue";
import FrontiereNew from "/@src/pages/config/frontieres/FrontiereNew.vue";
import FrontiereEdit from "/@src/pages/config/frontieres/FrontiereEdit.vue";
import DistrictIndex from "/@src/pages/config/zones/DistrictIndex.vue";
import VillagesIndex from "/@src/pages/config/zones/VillagesIndex.vue";
import ManagementCenterTypeCreate from "/@src/pages/config/management-center-type/ManagementCenterTypeCreate.vue";
import ManagementCenterIndex from "/@src/pages/config/management-centers/ManagementCenterIndex.vue";
import ManagementCenterCreate from "/@src/pages/config/management-centers/ManagementCenterCreate.vue";
import ManagePrices from "/@src/pages/config/manage-prices/ManagePrices.vue";
import ManagePricesCreate from "/@src/pages/config/manage-prices/ManagePricesCreate.vue";
import StaffShow from "/@src/pages/config/staff/StaffShow.vue";
import NumberTemplateIndex from "/@src/pages/config/number-templates/NumberTemplateIndex.vue";
import NumberTemplateCreate from "/@src/pages/config/number-templates/NumberTemplateCreate.vue";
import OrganisationIndex from "/@src/pages/config/organisations/OrganisationIndex.vue";
import OrganisationCreate from "/@src/pages/config/organisations/OrganisationCreate.vue";
import ManagementCenterTypeIndex from "/@src/pages/config/management-center-type/ManagementCenterTypeIndex.vue";
import AlertTypeIndex from "/@src/pages/config/alert-type/AlertTypeIndex.vue";
import AlertTypeCreate from "/@src/pages/config/alert-type/AlertTypeCreate.vue";
import TitleReasonsIndex from "/@src/pages/config/TitleReasonsIndex.vue";
import TownIndex from "/@src/pages/config/zones/TownIndex.vue";
import InstitutionIndex from "/@src/pages/config/institutions/InstitutionIndex.vue";
import InstitutionCreate from "/@src/pages/config/institutions/InstitutionCreate.vue";
import InstitutionTypeIndex from "/@src/pages/config/InstitutionTypeIndex.vue";
import auth from "/@src/routes/middlewares/auth";
import SpaceList from "/@src/pages/config/spaces/SpaceList.vue";

export default [
	{
		path: "blacklist",
		name: "blacklist",
		component: BlacklistIndexVue,
		meta: {
			middleware: [auth, permission],
			permission: "browse-blacklist-person",
			pageTitle: "Liste noire",
		},
	},
	{
		path: "document-types",
		name: "document_types",
		component: DocumentTypeIndexVue,
		meta: {
			middleware: [auth, permission],
			permission: "browse-document-type",
			pageTitle: "Types de document",
		},
	},
	{
		path: "required-document-types",
		name: "required_document_types",
		component: RequiredDocumentTypeIndexVue,
		meta: {
			middleware: [auth, permission],
			permission: "browse-required-document-type",
			pageTitle: "Types de document requis",
		},
	},
	{
		path: "owner-types",
		name: "owner_types",
		component: OwnerTypeIndexVue,
		meta: {
			middleware: [auth, permission],
			permission: "browse-owner-type",
			pageTitle: "Types de propriétaire",
		},
	},
	{
		path: "vehicle-categories",
		name: "vehicle_categories",
		component: VehicleCategoriesIndexVue,
		meta: {
			middleware: [auth, permission],
			permission: "browse-vehicle-category",
			pageTitle: "Catégories de véhicule",
		},
	},
	{
		path: "parcs",
		name: "parcs",
		component: ParcsIndex,
		meta: {
			middleware: [auth, permission],
			permission: "browse-park",
			pageTitle: "Parcs",
		},
	},
	{
		path: "management-centers",
		name: "management_centers",
		component: ManagementCenterIndex,
		meta: {
			middleware: [auth, permission],
			permission: "browse-management-center",
			pageTitle: "Centres de gestion",
		},
	},
	{
		path: "management-centers/create/:centerId?",
		name: "management_centers_create",
		component: ManagementCenterCreate,
		props: true,
		meta: {
			middleware: [auth, permission],
			permission: "store-management-center",
			pageTitle: "Centre de gestion",
		},
	},
	{
		path: "management-center-types",
		name: "management_center_types",
		component: ManagementCenterTypeIndex,
		meta: {
			middleware: [auth, permission],
			permission: "browse-management-center-type",
			pageTitle: "Les types de centre de gestion",
		},
	},
	{
		path: "management-center-types/create/:centerId?",
		name: "management_center_types_create",
		component: ManagementCenterTypeCreate,
		props: true,
		meta: {
			middleware: [auth, permission],
			permission: "store-management-center-type",
			pageTitle: "Types de centre de gestion",
		},
	},
	{
		path: "institutions",
		name: "institutions",
		component: InstitutionIndex,
		meta: {
			middleware: [auth, permission],
			permission: "browse-institution",
			pageTitle: "Institutions",
		},
	},
	{
		path: "institutions/create/:id?",
		name: "institutions_create",
		component: InstitutionCreate,
		props: true,
		meta: {
			middleware: [auth, permission],
			permission: "store-institution",
			pageTitle: "Institution",
		},
	},
	{
		path: "institutions-type",
		name: "institutions_type",
		component: InstitutionTypeIndex,
		meta: {
			middleware: [auth, permission],
			permission: "browse-institution-type",
			pageTitle: "Type d'institutions",
		},
	},
	{
		path: "frontieres",
		name: "frontieres",
		component: FrontieresIndex,
		meta: {
			middleware: [auth, permission],
			pageTitle: "Frontières",
			permission: "browse-border",
		},
	},
	{
		path: "frontieres/new",
		name: "frontieres_new",
		component: FrontiereNew,
		meta: {
			middleware: [auth, permission],
			pageTitle: "Nouvelle frontière",
			permission: "store-border",
		},
	},
	{
		path: "frontieres/edit/:id",
		name: "frontieres_edit",
		component: FrontiereEdit,
		meta: {
			middleware: [auth, permission],
			pageTitle: "Modifier frontière",
			permission: "update-border",
		},
	},
	{
		path: "immatriculation-formats",
		name: "immatriculation_formats",
		component: ImmatriculationFormatIndex,
		meta: {
			pageTitle: "Formats d'immatriculation",
			middleware: [auth, permission],
			permission: "browse-immatriculation-format",
		},
	},
	{
		path: "immatriculation-formats/create/:id?",
		name: "immatriculation_formats_create",
		component: ImmatriculationFormatCreate,
		props: true,
		meta: {
			pageTitle: "Format d'immatriculation",
			middleware: [auth, permission],
			permission: "store-immatriculation-format",
		},
	},
	{
		path: "organizations",
		name: "organizations",
		component: OrganisationIndex,
		meta: {
			pageTitle: "Liste des organisations",
			middleware: [auth, permission],
			permission: "browse-organization",
		},
	},
	{
		path: "organizations/create/:id?",
		name: "organization_create",
		component: OrganisationCreate,
		meta: {
			pageTitle: "Création d'une organisation",
			middleware: [auth, permission],
			permission: "store-organization",
			// permission: 'update-service', //check when permission is array, then check id param
		},
	},
	{
		path: "staff",
		name: "staff",
		component: StaffIndex,
		meta: {
			pageTitle: "Membres du staff",
			middleware: [auth, permission],
			permission: "browse-staff",
		},
	},
	{
		path: "staff/create/:id?",
		name: "staff_create",
		component: StaffCreate,
		meta: {
			pageTitle: "Staff",
			middleware: [auth, permission],
			permission: "store-staff",
		},
	},
	{
		path: "staff/show/:id",
		name: "staff_show",
		component: StaffShow,
		meta: {
			pageTitle: "Staff",
			middleware: [auth, permission],
			permission: "show-staff",
		},
	},
	{
		path: "roles",
		name: "roles",
		component: RoleIndex,
		meta: {
			pageTitle: "Liste des roles",
			middleware: [auth, permission],
			permission: "browse-role",
		},
	},
	{
		path: "roles/create/:id?",
		name: "role_create",
		component: RoleCreate,
		meta: {
			pageTitle: "Role",
			middleware: [auth, permission],
			permission: "store-role",
			// permission: 'update-role',
		},
	},
	{
		path: "permissions",
		name: "permissions",
		component: PermissionIndex,
		meta: {
			pageTitle: "Liste des permissions",
			middleware: [auth, permission],
			permission: "browse-permission",
		},
	},
	{
		path: "affiliate-categories",
		name: "affiliate_categories",
		component: AffiliateCategoryIndex,
		meta: {
			pageTitle: "Liste des catégories d'affilié",
			middleware: [auth, permission],
			permission: "browse-affiliate-category",
		},
	},
	{
		path: "affiliate-categories/create/:id?",
		name: "affiliate_category_create",
		component: AffiliateCategoryCreate,
		meta: {
			pageTitle: "Catégorie d'affilié",
			middleware: [auth, permission],
			permission: "store-affiliate-category",
		},
	},
	{
		path: "plate-colors/create/:id?",
		name: "plate_color_create",
		component: PlateColorCreate,
		meta: {
			pageTitle: "Couleur de plaque",
			middleware: [auth, permission],
			permission: "store-plate-color",
		},
	},
	{
		path: "plate-colors",
		name: "plate_colors",
		component: PlateColorIndex,
		meta: {
			pageTitle: "Liste des couleurs de plaque",
			middleware: [auth, permission],
			permission: "browse-plate-color",
		},
	},
	{
		path: "zones",
		name: "zones",
		component: ZoneIndex,
		meta: {
			pageTitle: "Liste des zones",
			middleware: [auth, permission],
			permission: "browse-zone",
		},
	},
	{
		path: "towns",
		name: "towns",
		component: TownIndex,
		meta: {
			middleware: [auth, permission],
			permission: "browse-town",
			pageTitle: "Liste des communes",
		},
	},
	{
		path: "districts",
		name: "districts",
		component: DistrictIndex,
		meta: {
			middleware: [auth, permission],
			permission: "browse-district",
			pageTitle: "Liste des arrondissements",
		},
	},
	{
		path: "villages",
		name: "villages",
		component: VillagesIndex,
		meta: {
			middleware: [auth, permission],
			permission: "browse-village",
			pageTitle: "Quartiers ou Villages",
		},
	},
	{
		path: "number-template",
		name: "number_template",
		component: NumberTemplateIndex,
		meta: {
			middleware: [auth, permission],
			permission: "browse-number-template",
			pageTitle: "Modèle de numéro",
		},
	},
	{
		path: "number-template/create/:id?",
		name: "number_template_create",
		component: NumberTemplateCreate,
		meta: {
			middleware: [auth, permission],
			permission: "store-number-template",
		},
	},
	{
		path: "alert-types",
		name: "alert_types",
		component: AlertTypeIndex,
		meta: {
			middleware: [auth, permission],
			permission: "browse-alert-type",
			pageTitle: "Type d'alerte",
		},
	},
	{
		path: "alert-types/create/:id?",
		name: "alert_types_create",
		component: AlertTypeCreate,
		meta: {
			middleware: [auth, permission],
			permission: "store-alert-type",
		},
	},
	{
		path: "title-reasons",
		name: "title_reasons",
		component: TitleReasonsIndex,
		meta: {
			middleware: [auth, permission],
			permission: "browse-title-reason",
			pageTitle: "Motifs de titre",
		},
	},
	{
		path: "manage-prices",
		name: "manage_prices",
		component: ManagePrices,
		meta: {
			middleware: [auth, permission],
			permission: "browse-management-center",
		},
	},
	{
		path: "manage-prices/create",
		name: "manage_prices_create",
		component: ManagePricesCreate,
		meta: {
			middleware: [auth, permission],
			permission: "store-management-center",
		},
	},
	{
		path: "vehicle-types",
		name: "vehicle_types",
		component: () => import("/@src/pages/config/vehicle-types/VehicleTypeIndex.vue"),
		meta: {
			pageTitle: "Liste des types de véhicule",
			middleware: [auth, permission],
			permission: "browse-vehicle-type",
		},
	},
	{
		path: "vehicle-types/create/:id?",
		name: "vehicle_type_create",
		component: () => import("/@src/pages/config/vehicle-types/VehicleTypeCreate.vue"),
		meta: {
			pageTitle: "Type de véhicule",
			middleware: [auth, permission],
			permission: "store-vehicle-type",
		},
	},
	{
		path: "vehicle-brands",
		name: "vehicle_brands",
		component: () => import("/@src/pages/config/vehicle-brands/VehicleBrandIndex.vue"),
		meta: {
			pageTitle: "Liste des marques de véhicule",
			middleware: [auth, permission],
			permission: "browse-vehicule-brand",
		},
	},
	{
		path: "vehicle-brands/create/:id?",
		name: "vehicle_brand_create",
		component: () => import("/@src/pages/config/vehicle-brands/VehicleBrandCreate.vue"),
		meta: {
			pageTitle: "Marque de véhicule",
			middleware: [auth, permission],
			permission: "store-vehicule-brand",
		},
	},
	{
		path: "vehicle-powers",
		name: "vehicle_powers",
		component: () => import("/@src/pages/config/VehiclePowerIndex.vue"),
		meta: {
			pageTitle: "Liste des puissances de véhicule",
			middleware: [auth, permission],
			permission: "browse-vehicule-power",
		},
	},
	{
		path: "vehicle-energy-sources",
		name: "vehicle_energy_sources",
		component: () => import("/@src/pages/config/vehicle-energy-sources/EnergySourceIndex.vue"),
		meta: {
			pageTitle: "Liste des sources d'énergie de véhicule",
			middleware: [auth, permission],
			permission: "browse-vehicule-energy-source",
		},
	},
	{
		path: "vehicle-energy-sources/create/:id?",
		name: "vehicle_energy_source_create",
		component: () => import("/@src/pages/config/vehicle-energy-sources/EnergySourceCreate.vue"),
		meta: {
			pageTitle: "Source d'énergie de véhicule",
			middleware: [auth, permission],
			permission: "store-vehicule-energy-source",
		},
	},
	{
		path: "plate-shapes",
		name: "plate_shapes",
		component: () => import("/@src/pages/config/plate-shapes/PlateShapeIndex.vue"),
		meta: {
			pageTitle: "Liste des formes de plaque",
			middleware: [auth, permission],
			permission: "browse-plate-shape",
		},
	},
	{
		path: "plate-shapes/create/:id?",
		name: "plate_shape_create",
		component: () => import("/@src/pages/config/plate-shapes/PlateShapeCreate.vue"),
		meta: {
			pageTitle: "Forme de plaque",
			middleware: [auth, permission],
			permission: "store-plate-shape",
		},
	},
	{
		path: "vehicle-characteristic-categories",
		name: "vehicle_characteristic_categories",
		component: () =>
			import("/@src/pages/config/vehicle-characteristic-categories/VehicleCharacteristicCategoryIndex.vue"),
		meta: {
			pageTitle: "Liste des catégories de caractéristiques de véhicule",
			middleware: [auth, permission],
			permission: "browse-vehicle-characteristic-category",
		},
	},
	{
		path: "vehicle-characteristic-categories/create/:id?",
		name: "vehicle_characteristic_category_create",
		component: () =>
			import("/@src/pages/config/vehicle-characteristic-categories/VehicleCharacteristicCategoryCreate.vue"),
		meta: {
			pageTitle: "Catégorie de caractéristique de véhicule",
			middleware: [auth, permission],
			permission: "store-vehicle-characteristic-category",
		},
	},
	{
		path: "vehicle-characteristics",
		name: "vehicle_characteristics",
		component: () => import("/@src/pages/config/vehicle-characteristics/VehicleCharacteristicIndex.vue"),
		meta: {
			pageTitle: "Liste des caractéristiques de véhicule",
			middleware: [auth, permission],
			permission: "browse-vehicle-characteristic",
		},
	},
	{
		path: "vehicle-characteristics/create/:id?",
		name: "vehicle_characteristic_create",
		component: () => import("/@src/pages/config/vehicle-characteristics/VehicleCharacteristicCreate.vue"),
		meta: {
			pageTitle: "Caractéristique de véhicule",
			middleware: [auth, permission],
			permission: "store-vehicle-characteristic",
		},
	},
	{
		path: "anatt-service-types",
		name: "anatt_service_types",
		component: () => import("/@src/pages/config/anatt-service-types/AnattServiceTypeIndex.vue"),
		meta: {
			pageTitle: "Liste des types de service de l'ANATT",
			middleware: [auth, permission],
			permission: "browse-anatt-service-type",
		},
	},
	{
		path: "anatt-service-types/create/:id?",
		name: "anatt_service_type_create",
		component: () => import("/@src/pages/config/anatt-service-types/AnattServiceTypeCreate.vue"),
		meta: {
			pageTitle: "Type de service de l'ANATT",
			middleware: [auth, permission],
			permission: "store-anatt-service-type",
		},
	},
	{
		path: "immatriculation-types",
		name: "immatriculation_types",
		component: () => import("/@src/pages/config/immatriculation_types/ImmatriculationTypesIndex.vue"),
		meta: {
			pageTitle: "Liste des types d'immatriculations",
			middleware: [auth, permission],
			permission: "browse-immatriculation-type",
		},
	},
	{
		path: "immatriculation-types/creation/:id?",
		name: "immatriculation_types_creation",
		component: () => import("/@src/pages/config/immatriculation_types/ImmatriculationTypesCreation.vue"),
		meta: {
			pageTitle: "Créer un type d'immatriculation",
			middleware: [auth, permission],
			permission: "store-immatriculation-type",
		},
	},
	{
		path: "immatriculation_types/:id",
		name: "immatriculation_type_show",
		component: () => import("/@src/pages/config/immatriculation_types/ImmatriculationTypesShow.vue"),
		meta: {
			pageTitle: "Voir",
			middleware: [auth, permission],
			permission: "show-immatriculation-type",
		},
	},
	{
		path: "reserved-plate-numbers",
		name: "reserved-plate-numbers",
		component: () => import("/@src/pages/config/reserved-plate-numbers/ReservedPlateNumbersIndex.vue"),
		meta: {
			pageTitle: "Liste des plaques réservés",
			middleware: [auth, permission],
			permission: "browse-reserved-plate-number",
		},
	},
	{
		path: "reserved-plate-numbers/creation/:id?",
		name: "reserved-plate-numbers_creation",
		component: () => import("/@src/pages/config/reserved-plate-numbers/ReservedPlateNumbersCreation.vue"),
		meta: {
			pageTitle: "Créer un plaque d'immatriculation réservé",
			middleware: [auth, permission],
			permission: "store-reserved-plate-number",
		},
	},
	{
		path: "reserved-plate-numbers/:id",
		name: "reserved-plate-numbers_show",
		component: () => import("/@src/pages/config/reserved-plate-numbers/ReservedPlateNumbersShow.vue"),
		meta: {
			pageTitle: "Voir",
			middleware: [auth, permission],
			permission: "show-reserved-plate-number",
		},
	},
	{
		path: "services",
		name: "services",
		component: () => import("/@src/pages/config/services/ServiceIndex.vue"),
		meta: {
			pageTitle: "Liste des service",
			middleware: [auth, permission],
			permission: "browse-service",
		},
	},
	{
		path: "services/creation/:serviceId?",
		name: "service_creation",
		component: () => import("/@src/pages/config/services/ServiceCreation.vue"),
		props: true,
		meta: {
			pageTitle: "Service",
			middleware: [auth, permission],
			permission: "store-service",
		},
	},
	{
		path: "service/:serviceId/:stepId/actions",
		name: "actions",
		props: true,
		component: () => import("/@src/pages/config/services/actions/ActionList.vue"),
		meta: {
			pageTitle: "Liste des actions",
			// middleware: [auth, permission],
			// permission: "browse-service",
		},
	},
	{
		path: "service/:serviceId/:stepId/actions/creation/:actionId?",
		name: "action_creation",
		props: true,
		component: () => import("/@src/pages/config/services/actions/ActionCreate.vue"),
		meta: {
			pageTitle: "Action",
			// middleware: [auth, permission],
		},
	},
	{
		path: "service/:serviceId/:stepId/actions/creation/:actionId",
		name: "action_show",
		props: true,
		component: () => import("/@src/pages/config/services/actions/ActionView.vue"),
		meta: {
			pageTitle: "Action",
			// middleware: [auth, permission],
		},
	},
	{
		path: "services/:serviceId",
		name: "service_show",
		component: () => import("/@src/pages/config/services/ServiceView.vue"),
		props: true,
		meta: {
			pageTitle: "Détails",
			middleware: [auth, permission],
			permission: "show-service",
		},
	},
	{
		path: "pricing/:serviceId?",
		name: "pricing_management",
		component: () => import("/@src/pages/config/pricing/PricingConfiguration.vue"),
		props: true,
		meta: {
			pageTitle: "Tarification du service",
			middleware: [auth, permission],
			permission: "store-price",
		},
	},
	{
		path: "meta-data",
		name: "meta_data",
		component: () => import("/@src/pages/config/meta-data/MetaDataIndex.vue"),
		meta: {
			pageTitle: "Méta données",
			middleware: [auth, permission],
			permission: "browse-meta-data",
		},
	},
	{
		path: "spaces/:spaceId",
		name: "space_view",
		props: true,
		component: () => import("/@src/pages/config/spaces/SpaceView.vue"),
		meta: {
			pageTitle: "Espace",
			middleware: [auth, permission],
			permission: "show-space",
		},
	},
	{
		path: "spaces",
		name: "spaces",
		component: SpaceList,
		meta: {
			pageTitle: "Espaces",
			middleware: [auth, permission],
			permission: "browse-space",
		},
	},
];
