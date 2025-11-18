export default {
	affiliate: import.meta.env.VITE_AFFILIATE_URL,
	police: import.meta.env.VITE_POLICE_URL,
	interpol: import.meta.env.VITE_INTERPOL_URL,
	bank: import.meta.env.VITE_BANK_URL,
	central_garage: import.meta.env.VITE_GC_URL,
	distributor: import.meta.env.VITE_DISTR_URL,
	auctioneer: import.meta.env.VITE_COMMISSAIRE_URL,
	approved: import.meta.env.VITE_APPROVED_URL,
	gma: import.meta.env.VITE_GMA_URL,
	gmd: import.meta.env.VITE_GMD_URL,
	court: import.meta.env.VITE_JUSTICE_URL,
	user: import.meta.env.VITE_PORTAL_URL,
	anatt: import.meta.env.VITE_ADMIN_URL,
};

export const serviceMap = {
	IMMATRICULATION_STANDARD: "immatriculation-demands-create",
	IMMATRICULATION_PRESTIGE_LABEL: "prestige-label-demands-create",
	IMMATRICULATION_PRESTIGE_NUMBER: "prestige-number-demands-create",
	IMMATRICULATION_PRESTIGE_NUMBER_LABEL: "prestige-number-label-demands-create",
	MUTATION: "mutation-demands-create",
	SALE_DECLARATION: "sale-demands-create",
	VEHICLE_TRANSFORMATION: "transformation-demands-create",
	TITLE_DEPOSIT: "title-deposit-demands-create",
	TITLE_RECOVERY: "title-recovery-demands-create",
	TINTED_WINDOW_AUTHORIZATION: "tinted-window-demands-create",
	GLASS_ENGRAVING: "engraving-demands-create",
	RE_IMMATRICULATION: "reimmatriculation-demands-create",
};

export const updateServiceMap = {
	IMMATRICULATION_STANDARD: "immatriculation-demands-update",
	IMMATRICULATION_PRESTIGE_LABEL: "prestige-label-demands-update",
	IMMATRICULATION_PRESTIGE_NUMBER: "prestige-number-demands-update",
	IMMATRICULATION_PRESTIGE_NUMBER_LABEL: "prestige-number-label-demands-update",
	MUTATION: "mutation-demands-update",
	SALE_DECLARATION: "sale-demands-update",
	TRANSFORMATION: "transformation-demands-update",
	TITLE_DEPOSIT: "title-deposit-demands-update",
	TITLE_RECOVERY: "title-recovery-demands-update",
	// TINTED_WINDOW_AUTHORIZATION: "tinted-window-demands-update",
	// GLASS_ENGRAVING: "engraving-demands-update",
	RE_IMMATRICULATION: "reimmatriculation-demands-update",
};

/*
VITE_AFFILIATE_URL=https://affiliatetest-simveb.anatt.bj    VITE_POLICE_URL=https://policetest-simveb.anatt.bj    VITE_INTERPOL_URL=https://interpoltest-simveb.anatt.bj    VITE_BANK_URL=https://banquetest-simveb.anatt.bj    VITE_GC_URL=https://gctest-simveb.anatt.bj    VITE_DISTR_URL=https://concessionairetest-simveb.anatt.bj    VITE_COMMISSAIRE_URL=https://commissairetest-simveb.anatt.bj    VITE_APPROVED_URL=https://agreetest-simveb.anatt.bj    VITE_GMA_URL=https://gmatest-simveb.anatt.bj    VITE_GMD_URL=https://gmdtest-simveb.anatt.bj    VITE_JUSTICE_URL=https://justicetest-simveb.anatt.bj    VITE_PORTAL_URL=https://ptest-simveb.anatt.bj    VITE_ADMIN_URL=https://officetest-simveb.anatt.bj
*/
