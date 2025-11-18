import auth from "@/router/middlewares/auth.js";
import ImmatriculationDemandCreate from "@/views/Demands/Immatriculation/ImmatriculationDemandCreate.vue";
import PrestigeLabelCreate from "@/views/Demands/PrestigeDemand/PrestigeLabelCreate.vue";
import PrestigeNumberCreate from "@/views/Demands/PrestigeDemand/PrestigeNumberCreate.vue";
import PrestigeNumberLabelCreate from "@/views/Demands/PrestigeDemand/PrestigeNumberLabelCreate.vue";
import permission from "@/router/middlewares/permission.js";
import ImmatriculationDemandUpdate from "@/views/Demands/Immatriculation/ImmatriculationDemandUpdate.vue";
import PrestigeNumberLabelUpdate from "@/views/Demands/PrestigeDemand/PrestigeNumberLabelUpdate.vue";
import PrestigeNumberUpdate from "@/views/Demands/PrestigeDemand/PrestigeNumberUpdate.vue";
import PrestigeLabelUpdate from "@/views/Demands/PrestigeDemand/PrestigeLabelUpdate.vue";
import DepositDemandUpdate from "@/views/Demands/Title/Deposit/DepositDemandUpdate.vue";

export default [
	{
		path: "demand/s/:id",
		name: "demand-show",
		props: true,
		component: () => import("@/views/Affiliate/AffiliateDemandShow.vue"),
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Détails d'une demande",
		},
	},
	{
		path: "/immatriculation/demands/create/:serviceId/:demandId?",
		name: "immatriculation-demands-create",
		component: ImmatriculationDemandCreate,
		props: true,
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Demande d'immatriculation",
		},
	},
	{
		path: "/immatriculation/demands/update/:serviceId/:demandId",
		name: "immatriculation-demands-update",
		component: ImmatriculationDemandUpdate,
		props: true,
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Modification demande d'immatriculation",
		},
	},
	{
		path: "/prestige/label/create/:serviceId/:demandId?",
		name: "prestige-label-demands-create",
		component: PrestigeLabelCreate,
		props: true,
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Demande d'immatriculation prestige",
		},
	},
	{
		path: "/prestige/label/update/:serviceId/:demandId?",
		name: "prestige-label-demands-update",
		component: PrestigeLabelUpdate,
		props: true,
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Modifier demande d'immatriculation prestige",
		},
	},
	{
		path: "/prestige/number/create/:serviceId/:demandId?",
		name: "prestige-number-demands-create",
		props: true,
		component: PrestigeNumberCreate,
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Demande d'immatriculation prestige numéro",
		},
	},
	{
		path: "/prestige/number/update/:serviceId/:demandId?",
		name: "prestige-number-demands-update",
		props: true,
		component: PrestigeNumberUpdate,
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Modifier demande d'immatriculation prestige numéro",
		},
	},
	{
		path: "/prestige/number-label/create/:serviceId/:demandId?",
		name: "prestige-number-label-demands-create",
		component: PrestigeNumberLabelCreate,
		props: true,
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Demande d'immatriculation prestige numéro label",
		},
	},
	{
		path: "/prestige/number-label/update/:serviceId/:demandId",
		name: "prestige-number-label-demands-update",
		component: PrestigeNumberLabelUpdate,
		props: true,
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Modifier demande prestige numéro label",
		},
	},
	{
		path: "/sale/demand/:serviceId/:demandId?",
		name: "sale-demands-create",
		component: () => import("@/views/Demands/Sale/SaleDemandCreate.vue"),
		props: true,
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Vente",
		},
	},
	{
		path: "/mutation/demand/:serviceId/:demandId?",
		name: "mutation-demands-create",
		component: () => import("@/views/Demands/Mutation/MutationDemandCreate.vue"),
		props: true,
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Mutation",
		},
	},
	{
		path: "/title-deposit/demand/:serviceId/:demandId?",
		name: "title-deposit-demands-create",
		component: () => import("@/views/Demands/Title/Deposit/DepositDemandCreate.vue"),
		props: true,
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Dépôt de titre",
		},
	},
	{
		path: "/title-deposit/demand/:serviceId/:demandId",
		name: "title-deposit-demands-update",
		component: DepositDemandUpdate,
		props: true,
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Modifier dépôt de titre",
		},
	},
	{
		path: "/title-recovery/demand/:serviceId/:demandId?",
		name: "title-recovery-demands-create",
		component: () => import("@/views/Demands/Title/Recovery/RecoveryDemandCreate.vue"),
		props: true,
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Reprise de titre",
		},
	},
	{
		path: "/reimmatriculation/demand/:serviceId/:demandId?",
		name: "reimmatriculation-demands-create",
		component: () => import("@/views/Demands/Reimmatriculation/ReimmatriculationDemandCreate.vue"),
		props: true,
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Ré-immatriculation",
		},
	},
	{
		path: "/tinted-window/demand/:serviceId/:demandId?",
		name: "tinted-window-demands-create",
		component: () => import("@/views/Demands/Authorization/TintedWindowDemandCreate.vue"),
		props: true,
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Autorisation de vitre teintée",
		},
	},
	{
		path: "/engraving/demand/:serviceId/:demandId?",
		name: "engraving-demands-create",
		component: () => import("@/views/Demands/Engraving/EngravingDemandCreate.vue"),
		props: true,
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Gravage de vitre teintée",
		},
	},
	{
		path: "/transformation/demand/:serviceId/:demandId?",
		name: "transformation-demands-create",
		props: true,
		component: () => import("@/views/Demands/Transformation/TransformationDemandCreate.vue"),
		meta: {
			menubar: "transformation",
			isAuth: true,
			middleware: [auth],
			title: "Transformation",
		},
	},
	{
		path: "/demand/new",
		name: "new-demand",
		component: () => import("@/views/global/NewDemand.vue"),
		props: true,
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Faire une demande",
		},
	},
	{
		path: "/demand/update",
		name: "update-demand",
		component: () => import("@/views/global/UpdateDemand.vue"),
		props: true,
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Mettre à jour une demande",
		},
	},
	{
		path: "/demand/:serviceId",
		name: "demand",
		component: () => import("@/views/DemandRedirect.vue"),
		props: true,
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Demande",
		},
	},
	{
		path: "/demand/s/:demandId",
		name: "demand-view",
		component: () => import("@/views/Demands/DemandView.vue"),
		props: true,
		meta: {
			menuRef: "demands",
			isAuth: true,
			middleware: [auth],
			title: "Détails de demande",
		},
	},
	{
		path: "/payment/:orderId",
		name: "payment_status",
		component: () => import("@/views/PaymentStatus.vue"),
		props: true,
		meta: {
			isAuth: true,
			middleware: [auth],
			title: "Paiement effectuée",
		},
	},
	{
		path: "/staff",
		name: "staff",
		component: () => import("@/views/Staff/StaffList.vue"),
		meta: {
			isAuth: true,
			middleware: [auth, permission],
			title: "Gestion du staff",
			permission: "browse-space-staff",
		},
	},
	{
		path: "/vehicle/:vin",
		name: "vehicle-view",
		component: () => import("@/views/global/VehicleView.vue"),
		props: true,
		meta: {
			isAuth: true,
			middleware: [auth],
			title: "Détails du véhicule",
		},
	},
];
