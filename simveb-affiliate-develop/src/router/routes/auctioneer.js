import auth from "@/router/middlewares/auth.js";
import AuctionDeclarations from "@/views/Auctioneer/AuctionDeclaration/AuctionDeclarations.vue";
import AuctionDeclarationsCreate from "@/views/Auctioneer/AuctionDeclaration/AuctionDeclarationsCreate.vue";
import AuctionDeclarationsShow from "@/views/Auctioneer/AuctionDeclaration/AuctionDeclarationsShow.vue";
import AuctioneerDashboard from "@/views/Auctioneer/AuctioneerDashboard.vue";
import permission from "@/router/middlewares/permission.js";

export default [
	{
		path: "",
		name: "auctioneer-dashboard",
		component: AuctioneerDashboard,
		meta: {
			menuRef: "auctioneer-dashboard",
			isAuth: true,
			middleware: [auth],
			title: "Tableau de bord Commissaire Priseur",
		},
	},
	{
		path: "/auctioneer/declarations",
		name: "auction-declarations",
		component: AuctionDeclarations,
		meta: {
			menuRef: "auction-declaration",
			isAuth: true,
			middleware: [auth, permission],
			permission: "browse-auction-sale-declaration",
			title: "Déclarations de vente aux enchères",
		},
	},
	{
		path: "/auctioneer/declarations/create",
		name: "auction-declarations-create",
		component: AuctionDeclarationsCreate,
		meta: {
			menuRef: "auction-declaration",
			isAuth: true,
			middleware: [auth, permission],
			permission: "store-auction-sale-declaration",
			title: "Déclaration de vente aux enchères",
		},
	},
	{
		path: "/auctioneer/declarations/:id/show",
		name: "auction-declarations-show",
		component: AuctionDeclarationsShow,
		meta: {
			menuRef: "auction-declaration",
			isAuth: true,
			middleware: [auth, permission],
			permission: "show-auction-sale-declaration",
			title: "Détails de vente aux enchères",
		},
	},
];
