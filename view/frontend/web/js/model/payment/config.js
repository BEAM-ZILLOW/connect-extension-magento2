/*browser:true*/
/*global define*/

define([],
    function () {
        'use strict';

        let config = {};

        /**
         * Initialize configuration with window.checkoutConfig object
         * @param configObject {object}
         */
        let init = function (configObject) {
            config = configObject;
        };

        /**
         * Get hostedCheckout index url
         * @returns {string}
         */
        let getHostedCheckoutUrl = function () {
            return config.hostedCheckoutPageUrl;
        };

        /**
         * Get Url that redirects to the success page url
         * @returns {string}
         */
        let getInlineSuccessUrl = function () {
            return config.inlineSuccessUrl;
        };

        /**
         * Get current locale
         * @returns {string}
         */
        let getLocale = function () {
            return config.locale;
        };

        /**
         * Get customized payment product group titles by product code
         * @returns {object}
         */
        let getGroupTitles = function () {
            return config.paymentMethodGroupTitles;
        };

        /**
         * Get image url of a loading spinner.
         * @return {string}
         */
        let getLoaderImage = function () {
            return config.loaderImage;
        };

        /**
         * Whether to use inline fields for payments.
         * @return {boolean}
         */
        let useInlinePayments = function () {
            return Boolean(Number(config.useInlinePayments));
        };

        return {
            init: init,
            getHostedCheckoutUrl: getHostedCheckoutUrl,
            getInlineSuccessUrl: getInlineSuccessUrl,
            getLocale: getLocale,
            getGroupTitles: getGroupTitles,
            useInlinePayments: useInlinePayments,
            getLoaderImage: getLoaderImage,
        };
    }
);