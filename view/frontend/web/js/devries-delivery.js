define([
        "jquery",
    ],
    function($) {
        'use strict'

        return function(config) {
            var inStockMessage = config.inStock,
                outOfStockMessage = config.outStock;

            console.log(outOfStockMessage);

            $(document).ready(function() {

                var url = window.location.origin + '/cbone/json/multiple'; // grab the base url and add the API location.
                var allSkuForms = document.querySelectorAll('form[data-product-sku]'); // grab all the 'add to cart' buttons, because it has an SKU data attribute.
                if (allSkuForms.length === 0) { // if you're in the checkout, the data attribute is named differently (yay for consistency, not)
                    allSkuForms = document.querySelectorAll('.control.qty input[data-cart-item-id]'); // yup, that's the data attribute in the checkout.
                }
                var skus = {}; // create an empty object in which we will store all the skus.

                allSkuForms.forEach(function(sku) { // loop over all the addtocart buttons.
                    var amount = 1; // the initial amount you want to order is 1 (on the category catalog page)
                    if ($(sku).attr('value') > 1) { // however in the checkout you can change this to more and we need to send this in order to check if there's enough stock available.
                        amount = $(sku).attr('value'); // change the amount if it's more than 1 (only doing this in the checkout).
                    }
                    if ($(sku).attr('data-product-sku') !== undefined) { // if the data-product-sku is undefined that means we're in the checkout.
                        var singleSku = $(sku).attr('data-product-sku');
                        skus[singleSku] = amount; // put the sku code as a key with the amount as a value.
                    } else { // we're in the checkout so that data attribute is named differently...
                        var singleSku = $(sku).attr('data-cart-item-id');
                        skus[singleSku] = amount; // same thing, put it in the sku object.
                    }
                })

                $.post(url, skus, function(data) { // post the sku object to the API, which will in turn give us data.
                    var parseddata = JSON.parse(data); // parse the data so we can loop through it with a for... in...
                    for (var prop in parseddata) {
                        var obj = parseddata[prop]; // that's the value of the SKU. 1 meaning it's true (in stock), 0 meaning false (not in stock)
                        if (obj === 1) {
                            var theForm = $('form[data-product-sku="' + prop + '"]'); // find the add to cart button which has the data-product-sku;
                            if (theForm === undefined) { // we're in the checkout if it's undefined as said before.
                                theForm = $('form[data-cart-item-id="' + prop + '"]')
                            }
                            if ($('body').hasClass('catalog-product-view')) {
                                var deliverySpan = $('.product-info-right .catalog-delivery');
                            } else {
                                var deliverySpan = $(theForm).parent().children('.catalog-delivery'); // find the delivery div so we can replace its HTML.
                            }
                            $(deliverySpan).html('<span>' + inStockMessage + '</span>'); // if it's in stock we deliver it within 24 hours.
                        } else {
                            var theForm = $('form[data-product-sku="' + prop + '"]'); // find the add to cart button which has the data-product-sku;
                            if (theForm === undefined) { // we're in the checkout if it's undefined as said before.
                                theForm = $('form[data-cart-item-id="' + prop + '"]')
                            }
                            if ($('body').hasClass('catalog-product-view')) {
                                var deliverySpan = $('.product-info-right .catalog-delivery');
                            } else {
                                var deliverySpan = $(theForm).parent().children('.catalog-delivery'); // find the delivery div so we can replace its HTML.
                            }
                            $(deliverySpan).html('<span><strong>Levertijd:</strong> ' + outOfStockMessage + '</span>'); // if it's false it's not in stock and it will take more time to deliver.
                        }
                    }
                })

            })
        }
})