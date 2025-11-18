import auth from "@/router/middlewares/auth.js";
import ApprovedDashboard from "@/views/Approved/ApprovedDashboard.vue";
import PlateOrders from "@/views/Approved/PlateOrder/PlateOrders.vue";
import PlateOrderShow from "@/views/Approved/PlateOrder/PlateOrderShow.vue";
import PrintOrderCreate from "@/views/Approved/PrintOrder/PrintOrderCreate.vue";
import PrintOrderConfirmation from "@/views/Approved/PrintOrder/PrintOrderConfirmation.vue";
import PrintOrders from "@/views/Approved/PrintOrder/PrintOrders.vue";
import PrintOrderView from "@/views/Approved/PrintOrder/PrintOrderView.vue";
import permission from "@/router/middlewares/permission.js";
import PlateList from "@/views/Approved/PlateOrder/PlateList.vue";

export default [
	{
		path: "approved-dashboard",
		name: "approved-dashboard",
		component: ApprovedDashboard,
		meta: {
			isAuth: true,
			middleware: [auth],
			title: "Tableau de bord de l'agrée",
			menuRef: "approved-dashboard",
		},
	},
	{
		path: "plate-orders",
		name: "plate-orders",
		component: PlateOrders,
		meta: {
			isAuth: true,
			middleware: [auth, permission],
			permissions: ["browse-plate-order", "show-plate-order", "store-plate-order"],
			title: "Commandes de plaques",
			menuRef: "plate-orders",
		},
	},
	{
		path: "plates",
		name: "plate-list",
		component: PlateList,
		meta: {
			isAuth: true,
			middleware: [auth, permission],
			permissions: ["browse-plate", "show-plate", "store-plate"],
			title: "Inventaire",
			menuRef: "plate-orders",
		},
	},
	{
		path: "plate-orders/:id/show",
		name: "plate-orders-show",
		component: PlateOrderShow,
		meta: {
			isAuth: true,
			middleware: [auth, permission],
			permission: "show-plate-order",
			title: "Détails de vente aux enchères",
			menuRef: "plate-orders",
		},
	},
	{
		path: "print-orders/new",
		name: "print-orders-create",
		component: PrintOrderCreate,
		meta: {
			isAuth: true,
			middleware: [auth, permission],
			permission: "print-plate",
			title: "Nouvelle demande d'impression",
			menuRef: "print-orders",
		},
	},
	{
		path: "print-orders/confirm/:id",
		name: "print-orders-confirm",
		component: PrintOrderConfirmation,
		props: true,
		meta: {
			isAuth: true,
			middleware: [auth, permission],
			permission: "confirm-print-order-affectation",
			title: "Confirmation demande d'impression",
			menuRef: "print-orders",
		},
	},
	{
		path: "print-orders/:id",
		name: "print-orders-view",
		component: PrintOrderView,
		props: true,
		meta: {
			isAuth: true,
			middleware: [auth, permission],
			permission: "show-print-order",
			title: "Impression de plaque",
			menuRef: "print-orders",
		},
	},
	{
		path: "print-orders",
		name: "print-orders",
		component: PrintOrders,
		meta: {
			isAuth: true,
			middleware: [auth, permission],
			permissions: ["browse-print-order", "show-print-order"],
			title: "Impressions de plaque",
			menuRef: "print-orders",
		},
	},
];
