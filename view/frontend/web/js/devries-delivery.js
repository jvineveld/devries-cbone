define([
        "jquery",
        "matchMedia"
    ],
    function($) {

        console.log("I'm working");

        var url = window.location.origin + '/cbone/json/multiple/';
        var allSkuForms = document.querySelectorAll('form[data-product-sku]');
        if(allSkuForms.length === 0) {
            allSkuForms = document.querySelectorAll('.control.qty input[data-cart-item-id]');
        }
        var skus = [];

        allSkuForms.forEach(function(sku) {
            var amount = 1;
            if ($(this).attr('value') > 1) {
                amount = $(this).attr('value')
            }
            skus.push([$(sku).attr('data-product-sku'), amount]);
        })

        console.log(skus);
    });