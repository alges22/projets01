import auth from "@/router/middlewares/auth.js";
import OppositionsList from "@/views/Court/Oppositions/OppositionsList.vue";
import OppositionIssue from "@/views/Court/Oppositions/OppositionIssue.vue";
import OppositionsShow from "@/views/Court/Oppositions/OppositionsShow.vue";
import CourtDashboard from "@/views/Court/CourtDashboard.vue";
import PledgeList from "@/views/Court/Pledge/PledgeList.vue";
import PledgeShow from "@/views/Court/Pledge/PledgeShow.vue";
import PledgeIssue from "@/views/Court/Pledge/PledgeIssue.vue";
import PledgeLift from "@/views/Court/Pledge/PledgeLift.vue";
import PledgeLiftList from "@/views/Court/PledgeLift/PledgeLiftList.vue";
import PledgeLiftShow from "@/views/Court/PledgeLift/PledgeLiftShow.vue";
import PledgeCanLift from "@/views/Court/Pledge/PledgeCanLift.vue";

export default [
	{
		path: "court-dashboard",
		name: "court-dashboard",
		component: CourtDashboard,
		meta: {
			isAuth: true,
			middleware: [auth],
			title: "Tableau de bord/Tribunal",
		},
	},
	{
		path: "pledge",
		name: "pledge-issue",
		component: PledgeIssue,
		meta: {
			menuRef: "pledge",
			isAuth: true,
			middleware: [auth],
			title: "Émettre un gage",
		},
	},
	{
		path: "pledge/:id",
		name: "pledge-show",
		component: PledgeShow,
		meta: {
			menuRef: "pledge",
			isAuth: true,
			middleware: [auth],
			title: "Récapitulatif de la demande de gage",
		},
	},
	{
		path: "pledge/lift/:id",
		name: "pledge-lift",
		component: PledgeLift,
		meta: {
			menuRef: "pledge",
			isAuth: true,
			middleware: [auth],
			title: "Lever un gage",
		},
	},
	{
		path: "pledge/can-lift/",
		name: "pledge-can-lift",
		component: PledgeCanLift,
		meta: {
			menuRef: "pledge",
			isAuth: true,
			middleware: [auth],
			title: "Sélectionnez le gage à lever",
		},
	},
	{
		path: "pledge-lifts",
		name: "pledge-lifts",
		component: PledgeLiftList,
		meta: {
			menuRef: "pledge",
			isAuth: true,
			middleware: [auth],
			title: "Demandes de levée de gage",
		},
	},
	{
		path: "pledge-lifts/:id",
		name: "pledge-lifts-show",
		component: PledgeLiftShow,
		meta: {
			menuRef: "pledge",
			isAuth: true,
			middleware: [auth],
			title: "Détails de la demandes de levée de gage",
		},
	},
	{
		path: "court-pledges",
		name: "court-pledges",
		component: PledgeList,
		meta: {
			menuRef: "court-pledge",
			isAuth: true,
			middleware: [auth],
		},
	},
	{
		path: "court-pledges/:id",
		name: "court-pledges-show",
		component: PledgeShow,
		meta: {
			menuRef: "court-pledge",
			isAuth: true,
			middleware: [auth],
			title: "Détails d'une demande de mise en gage",
		},
	},
	{
		path: "oppositions",
		name: "oppositions",
		component: OppositionsList,
		meta: {
			menuRef: "opposition",
			isAuth: true,
			middleware: [auth],
			title: "Mes demandes d'oppositions",
		},
	},
	{
		path: "opposition",
		name: "opposition-issue",
		component: OppositionIssue,
		meta: {
			menuRef: "opposition",
			isAuth: true,
			middleware: [auth],
			title: "Émettre une opposition",
		},
	},
	{
		path: "opposition/:id",
		name: "opposition-show",
		component: OppositionsShow,
		meta: {
			menuRef: "opposition",
			isAuth: true,
			middleware: [auth],
			title: "Détails d'une opposition",
		},
	},
];
