const FEDAPAY_PUBLIC_KEY = import.meta.env.VITE_FEDAPAY_PUBLIC_KEY;

import {onMounted} from "vue";
onMounted(() => {
    const Fedapay = window.Fedapay;
})

/**
 * This function launches the FedaPay checkout process.
 *
 *
 * @returns {Promise} - Returns a Promise that resolves with the transaction if approved, or rejects with the reason if not approved.
 * @param {string} paymentReference - Reference of the order or payment - trx_cyA_1709840912904
 * @param {string} paymentDescription - A little description of the commande. The description will be shown on header of the invoice.
 * @param {string|number} paymentAmount - Amount in string
 * @param {Object} user
 * @param {string} user.email - User's email
 * @param {string} user.lastname - User's last name
 * @param {string} user.firstname - User's first name
 * @param {string} user.phone - User's phone number
 */
export const launchFedapayCheckout = (paymentReference, paymentDescription, paymentAmount, user) => {

    const customer = {
        email: user.email,
        lastname: user.lastname,
        firstname: user.firstname,
        phone_number: {
            number: user.phone,
            country: "BJ",
        },
        currency: "XOF",
    };

    return new Promise((resolve, reject) => {
        const options = {
            public_key: FEDAPAY_PUBLIC_KEY,
            environment: "sandbox",
            locale: "fr",
            transaction: {
                amount: paymentAmount.toString(),
                description: paymentDescription,
                custom_metadata: {
                    reference: paymentReference,
                },
            },
            customer: customer,
            onComplete: async function ({ reason, transaction }) {
                if (transaction.status === "approved") {
                    resolve(transaction);
                } else {
                    reject(reason);
                }
            },
        };
        let widget = FedaPay.init(options);
        widget.open();
    });
};