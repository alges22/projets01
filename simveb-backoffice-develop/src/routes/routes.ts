import permission from "/@src/routes/middlewares/permission";
import auth from "/@src/routes/middlewares/auth";
import OrdersList from "/@src/pages/orders/OrdersList.vue";
import OrderView from "/@src/pages/orders/OrderView.vue";
import LogsList from "/@src/pages/activity-logs/LogsList.vue";
import LogView from "/@src/pages/activity-logs/LogView.vue";
import AccreditationsList from "/@src/pages/accreditations/AccreditationsList.vue";
import AccreditationView from "/@src/pages/accreditations/AccreditationView.vue";
import AccreditationCreate from "/@src/pages/accreditations/AccreditationCreate.vue";
import AggregatorIndex from "/@src/pages/aggregators/AggregatorIndex.vue";
import AggregatorCreate from "/@src/pages/aggregators/AggregatorCreate.vue";
import AggregatorEdit from "/@src/pages/aggregators/AggregatorEdit.vue";
import VehicleSearch from "/@src/pages/vehicles/vehicles/VehicleSearch.vue";
import GrayCardDemandIndexVue from "/@src/pages/gray-card-demands/GrayCardDemandIndex.vue";
import GrayCardDemandShowVue from "/@src/pages/gray-card-demands/GrayCardDemandShow.vue";
import DemandsList from "/@src/pages/demands/DemandsList.vue";
import DemandView from "/@src/pages/demands/DemandView.vue";
import ReImmatriculationDemandsIndexVue from "/@src/pages/re-immatriculation-demands/ReImmatriculationDemandsIndex.vue";
import ReImmatriculationDemandsShowVue from "/@src/pages/re-immatriculation-demands/ReImmatriculationDemandsShow.vue";
import UserShow from "/@src/pages/user/UserShow.vue";

export default [
	{
		path: "print/orders",
		name: "print_order",
		component: () => import("/@src/pages/PrintOrders/PrintOrderList.vue"),
		meta: {
			middleware: [auth, permission],
			permission: "browse-print-order",
			pageTitle: "Dossiers d'impression de plaques",
		},
	},
	{
		path: "print/order/:id",
		name: "print_order_detail",
		component: () => import("/@src/pages/PrintOrders/PrintOrderShow.vue"),
		meta: {
			middleware: [auth, permission],
			permission: "show-print-order",
			pageTitle: "Détail de l'impression de plaques",
		},
	},
	{
		path: "plate-orders",
		name: "plate_order_list",
		component: () => import("/@src/pages/PlateOrders/PlateOrderList.vue"),
		meta: {
			middleware: [auth, permission],
			permission: "browse-plate-order",
			pageTitle: "Commandes de plaques",
		},
	},
	{
		path: "plate-orders/:id",
		name: "plate_order_detail",
		component: () => import("/@src/pages/PlateOrders/PlateOrderShow.vue"),
		meta: {
			middleware: [auth, permission],
			permission: "show-plate-order",
			pageTitle: "Détail de la commande de plaque",
		},
	},
	{
		path: "plate-orders/new",
		name: "plate_order_create",
		component: () => import("/@src/pages/PlateOrders/PlateOrderForm.vue"),
		meta: {
			middleware: [auth, permission],
			permission: "store-plate-order",
		},
	},
	{
		path: "plates",
		name: "plate_list",
		component: () => import("/@src/pages/Plates/PlateList.vue"),
		meta: {
			middleware: [auth, permission],
			permission: "browse-plate",
			pageTitle: "Liste des plaques",
		},
	},
	{
		path: "taches",
		name: "taches_list",
		component: () => import("/@src/pages/taches/DemandsList.vue"),
		meta: {
			middleware: [auth, permission],
			permission: "browse-im-demand",
			pageTitle: "Mes tâches",
		},
	},
	{
		path: "traitements",
		name: "traitements_list",
		component: () => import("/@src/pages/traitements/DemandsList.vue"),
		meta: {
			middleware: [auth, permission],
			permission: "browse-im-demand",
			pageTitle: "Mes traitements",
		},
	},
	{
		path: "orders",
		name: "orders_list",
		component: OrdersList,
		meta: {
			middleware: [auth, permission],
			permission: "browse-order",
			pageTitle: "Les commandes",
		},
	},
	{
		path: "orders/:orderId",
		name: "orders_show",
		component: OrderView,
		props: true,
		meta: {
			pageTitle: "Dossier d'une commande",
			middleware: [auth, permission],
			permission: "show-order",
		},
	},
	{
		path: "logs",
		name: "logs_list",
		component: LogsList,
		meta: {
			middleware: [auth, permission],
			permission: "browse-activity-log",
			pageTitle: "Journalisations",
		},
	},
	{
		path: "logs/:logId",
		name: "logs_show",
		props: true,
		component: LogView,
		meta: {
			middleware: [auth, permission],
			permission: "browse-activity-log",
			pageTitle: "Dossier d'une journalisation",
		},
	},
	{
		path: "plate-duplicates",
		name: "plate_duplicates",
		component: () => import("/@src/pages/plate-duplicate/PlateDuplicateList.vue"),
		meta: {
			middleware: [auth, permission],
			permission: "browse-plate-duplicate",
			pageTitle: "Dossiers de demande de duplicata de plaques",
		},
	},
	{
		path: "plate-duplicates/:id",
		name: "plate_duplicates_show",
		component: () => import("/@src/pages/plate-duplicate/PlateDuplicateShow.vue"),
		meta: {
			pageTitle: "Dossier d'une demande de duplicata de plaque d'immatriculation",
			middleware: [auth, permission],
			permission: "show-plate-duplicate",
		},
	},
	{
		path: "gray-cards-duplicates",
		name: "gray_card_duplicates",
		component: () => import("/@src/pages/gray-card-duplicate/GrayCardDuplicateList.vue"),
		meta: {
			middleware: [auth, permission],
			permission: "browse-card-duplicate",
			pageTitle: "Dossiers de demande de duplicata de carte grise",
		},
	},
	{
		path: "gray-cards-duplicate/:id",
		name: "gray_card_duplicate_show",
		component: () => import("/@src/pages/gray-card-duplicate/GrayCardDuplicateShow.vue"),
		meta: {
			pageTitle: "Dossier d'une demande de duplicata de carte grise",
			middleware: [auth, permission],
			permission: "show-card-duplicate",
		},
	},
	{
		path: "accreditations",
		name: "accreditations_list",
		component: AccreditationsList,
		meta: {
			middleware: [auth, permission],
			permission: "browse-accreditation",
			pageTitle: "Les accréditations",
		},
	},
	{
		path: "accreditations/:accreditationId",
		name: "accreditations_show",
		component: AccreditationView,
		props: true,
		meta: {
			pageTitle: "Dossier d'une accreditation",
			middleware: [auth, permission],
			permission: "show-accreditation",
		},
	},
	{
		path: "accreditations/create",
		name: "accreditations_create",
		component: AccreditationCreate,
		meta: {
			pageTitle: "Faire une accreditation",
			middleware: [auth, permission],
			permission: "store-accreditation",
		},
	},
	{
		path: "spaces-registration-request",
		name: "spaces_registration_request_list",
		component: () => import("/@src/pages/SpacesRegistrationRequest/SpaceRegistrationList.vue"),
		meta: {
			pageTitle: "Demandes d'enregistrement d'espace",
			middleware: [auth, permission],
			permission: "browse-space-registration-request",
		},
	},
	{
		path: "spaces-registration-request-create",
		name: "spaces_registration_request_create",
		component: () => import("/@src/pages/SpacesRegistrationRequest/SpaceRegistrationCreate.vue"),
		meta: {
			pageTitle: "Enregistrement d'espaces",
			middleware: [auth, permission],
			permission: "store-space-registration-request",
		},
	},
	{
		path: "spaces-registration-request-show/:id",
		name: "spaces_registration_request_show",
		props: true,
		component: () => import("/@src/pages/SpacesRegistrationRequest/SpaceRegistrationShow.vue"),
		meta: {
			pageTitle: "Détails de la demande d'enregistrement",
			middleware: [auth, permission],
			permission: "show-space-registration-request",
		},
	},
	{
		path: "aggregators",
		name: "aggregators",
		component: AggregatorIndex,
		meta: {
			middleware: [auth, permission],
			permission: "browse-document-type",
			pageTitle: "Aggrégateurs de paiement",
		},
	},
	{
		path: "aggregators/create",
		name: "aggregators_create",
		component: AggregatorCreate,
		meta: {
			middleware: [auth, permission],
			permission: "store-document-type",
			pageTitle: "Aggrégateurs de paiement",
		},
	},
	{
		path: "aggregator/edit/:id",
		name: "aggregator_edit",
		component: AggregatorEdit,
		meta: {
			middleware: [auth],
			permission: null,
			pageTitle: "Aggrégateur de paiement",
		},
	},
	{
		path: "vehicles/:vin?",
		name: "vehicles_list",
		component: VehicleSearch,
		meta: {
			middleware: [auth, permission],
			permission: "show-vehicle",
			pageTitle: "Requête globale sur un véhicule",
		},
	},
	{
		path: "gray-card-demands",
		name: "gray_card_demands",
		component: GrayCardDemandIndexVue,
		meta: {
			middleware: [auth, permission],
			permission: "browse-gay-card-demand",
		},
	},
	{
		path: "gray-card-demands/:id",
		name: "gray_card_demands_show",
		component: GrayCardDemandShowVue,
		meta: {
			middleware: [auth, permission],
			permission: "show-gay-card-demand",
			pageTitle: "Dossiers de demande de carte grise",
		},
	},
	{
		path: "demands",
		name: "demands_list",
		component: DemandsList,
		meta: {
			middleware: [auth, permission],
			permission: "browse-im-demand",
			pageTitle: "Les demandes",
		},
	},
	{
		path: "demands/:demandId",
		name: "demands_show",
		component: DemandView,
		props: true,
		meta: {
			pageTitle: "Dossier d'une demande",
			middleware: [auth, permission],
			permission: "show-im-demand",
		},
	},
	{
		path: "re-immatriculation-demands/:wheels/wheels",
		name: "re_immatriculation_demands",
		component: ReImmatriculationDemandsIndexVue,
		meta: {
			pageTitle: "Dossiers de demande de ré-immatriculation",
		},
	},
	{
		path: "re-immatriculation-demands/:wheels/wheels",
		name: "re_immatriculation_demands_show",
		component: ReImmatriculationDemandsShowVue,
		meta: {},
	},
	{
		path: "pledge-issue-request",
		name: "pledges_issue_request_list",
		component: () => import("/@src/pages/PledgesIssueRequest/List.vue"),
		meta: {
			pageTitle: "Demandes d'émission de gage",
			middleware: [auth, permission],
			permission: "browse-pledge",
		},
	},
	{
		path: "pledge-issue-request-show/:id",
		name: "pledge_issue_request_show",
		props: true,
		component: () => import("/@src/pages/PledgesIssueRequest/Show.vue"),
		meta: {
			pageTitle: "Détails de la demande d'emission de dage",
			middleware: [auth, permission],
			permission: "show-pledge",
		},
	},
	{
		path: "pledge-lift-issue-request",
		name: "pledges_lift_issue_request_list",
		component: () => import("/@src/pages/PledgesLiftIssueRequest/List.vue"),
		meta: {
			pageTitle: "Demandes de levées de gage",
			middleware: [auth, permission],
			permission: "browse-pledge-lift",
		},
	},
	{
		path: "pledge-lift-issue-request-show/:id",
		name: "pledge_lift_issue_request_show",
		props: true,
		component: () => import("/@src/pages/PledgesLiftIssueRequest/Show.vue"),
		meta: {
			pageTitle: "Détails de la demande de levée de dage",
			middleware: [auth, permission],
			permission: "show-pledge-lift",
		},
	},
	{
		path: "users/:id",
		name: "users_show",
		component: UserShow,
		meta: {
			middleware: [auth, permission],
			permission: "browse-im-demand",
		},
	},
	{
		path: "oppositions",
		name: "oppositions",
		component: () => import("/@src/pages/Opposition/List.vue"),
		meta: {
			pageTitle: "Demandes d'oppositions",
			middleware: [auth, permission],
			permission: "browse-opposition",
		},
	},
	{
		path: "oppositions/:id",
		name: "oppositions_show",
		component: () => import("/@src/pages/Opposition/Show.vue"),
		meta: {
			pageTitle: "Détails de l'opposition",
			middleware: [auth, permission],
			permission: "show-opposition",
		},
	},
];
