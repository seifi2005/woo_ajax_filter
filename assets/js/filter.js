jQuery(document).ready(function ($) {

    let ajaxRequest = null;

    function loadProducts() {
        // لغو درخواست قبلی در صورت وجود
        if (ajaxRequest) {
            ajaxRequest.abort();
        }

        let categories = [];
        $('.filter-category:checked').each(function () {
            categories.push($(this).val());
        });

        let attrs = [];
        $('.filter-attr:checked').each(function () {
            attrs.push($(this).val());
        });

        ajaxRequest = $.ajax({
            url: wooFilter.ajax_url,
            type: 'POST',
            data: {
                action: 'woo_advanced_filter',
                categories: categories,
                attrs: attrs,
                min: $('#price-min').val(),
                max: $('#price-max').val()
            },
            beforeSend() {
                let target = $('.products, ul.products, .woocommerce ul.products').first();
                if (target.length) {
                    target.css('opacity', '0.5');
                } else {
                    $('#woo-filter-results').html('<p class="loading">در حال بارگذاری...</p>');
                }
            },
            success(response) {
                let products = $(response).find('.products, ul.products').first();
                let target = $('.products, ul.products, .woocommerce ul.products').first();
                
                if (target.length && products.length) {
                    target.replaceWith(products);
                } else if (target.length) {
                    target.html($(response).html());
                } else {
                    $('#woo-filter-results').html(response);
                }

                let scrollTarget = $('.products, ul.products, .woocommerce ul.products').first();
                if (!scrollTarget.length) {
                    scrollTarget = $('#woo-filter-results');
                }
                
                if (scrollTarget.length) {
                    $('html, body').animate({
                        scrollTop: scrollTarget.offset().top - 100
                    }, 200);
                }
            },
            error(xhr, status, error) {
                if (status !== 'abort') {
                    console.error('خطا در بارگذاری محصولات:', error);
                    let target = $('.products, ul.products, .woocommerce ul.products').first();
                    if (target.length) {
                        target.css('opacity', '1');
                    }
                }
            },
            complete() {
                ajaxRequest = null;
            }
        });
    }

    // رویداد تغییر برای فیلترها
    $(document).on('change', '.filter-category, .filter-attr', loadProducts);

    // اسلایدر قیمت با debounce
    let priceTimeout;
    if ($("#price-slider").length) {
        $("#price-slider").slider({
            range: true,
            min: 0,
            max: 5000000,
            values: [0, 5000000],
            slide: function (event, ui) {
                $("#min-price").text(ui.values[0].toLocaleString('fa-IR'));
                $("#max-price").text(ui.values[1].toLocaleString('fa-IR'));
                $("#price-min").val(ui.values[0]);
                $("#price-max").val(ui.values[1]);
            },
            change: function() {
                clearTimeout(priceTimeout);
                priceTimeout = setTimeout(loadProducts, 500);
            }
        });

        $("#min-price").text('0');
        $("#max-price").text('5,000,000');
        $("#price-min").val(0);
        $("#price-max").val(5000000);
    }
});
