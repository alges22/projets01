import AffiliateRegistrationRequestIndex from "/@src/pages/affiliate/affiliate-registration-requests/AffiliateRegistrationRequestIndex.vue";
import AffiliateRegistrationRequestCreate from "/@src/pages/affiliate/affiliate-registration-requests/AffiliateRegistrationRequestCreate.vue";
import AffiliateRegistrationRequestShow from "/@src/pages/affiliate/affiliate-registration-requests/AffiliateRegistrationRequestShow.vue";
import AffiliateIndex from "/@src/pages/affiliate/affiliates/AffiliateIndex.vue";
import AffiliateShow from "/@src/pages/affiliate/affiliates/AffiliateShow.vue";
import AffiliateStaffIndex from "/@src/pages/affiliate/affiliate-staff/AffiliateStaffIndex.vue";
import permission from "/@src/routes/middlewares/permission";
import auth from "/@src/routes/middlewares/auth";

export default [
	{
		path: "affiliate-registration-requests",
		name: "affiliate_registration_requests",
		component: AffiliateRegistrationRequestIndex,
		meta: {
			pageTitle: "Liste des demandes d'enregistrement d'affilié",
			middleware: [auth, permission],
			permission: "browse-affiliate-registration-request",
		},
	},
	{
		path: "affiliate-registration-requests/create",
		name: "affiliate_registration_request_create",
		component: AffiliateRegistrationRequestCreate,
		meta: {
			pageTitle: "Demande d'enregistrement d'affilié",
			middleware: [auth, permission],
			permission: "store-affiliate-registration-request",
		},
	},
	{
		path: "affiliate-registration-requests/:id",
		name: "affiliate_registration_request_show",
		component: AffiliateRegistrationRequestShow,
		meta: {
			pageTitle: "Demande d'enregistrement d'affilié",
			middleware: [auth, permission],
			permission: "show-affiliate-registration-request",
		},
	},
	{
		path: "affiliates",
		name: "affiliates",
		component: AffiliateIndex,
		meta: {
			pageTitle: "Liste des affiliés",
			middleware: [auth, permission],
			permission: "browse-affiliate",
		},
	},
	{
		path: "affiliates/:id",
		name: "affiliate_show",
		component: AffiliateShow,
		meta: {
			pageTitle: "Détail d'un affilié",
			middleware: [auth, permission],
			permission: "show-affiliate",
		},
	},
	{
		path: "affiliate/staff",
		name: "afifliate_staff",
		component: AffiliateStaffIndex,
		meta: {
			pageTitle: "Liste du personnel",
			middleware: [auth, permission],
			permission: "browse-affiliate-staff",
		},
	},
];
