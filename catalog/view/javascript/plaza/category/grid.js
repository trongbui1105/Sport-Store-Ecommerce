$(document).ready(function () {
    if(localStorage.getItem('type') == null) {
        var type = $('#category-view-type').val();
        var cols = $('#category-grid-cols').val();

        if(type == "list") {
            category_view.initView(type, cols, 'btn-list');
        }

        if(type == 'grid') {
            category_view.initView(type, cols, 'btn-grid-' + cols);
        }
    } else {
        var type = localStorage.getItem('type');
        var cols = localStorage.getItem('cols');
        var element = localStorage.getItem('element');

        category_view.initView(type, cols, element);
    }
});

var category_view = {
    'initView' : function (type, cols, element) {
        category_view.changeView(type, cols, element);
    },

    'changeView' : function (type, cols, element) {
        if(type == "grid") {
            var column = parseInt(cols);
			if(column == 1) {
                $('#content .product-items').attr('class', 'product-layout product-grid grid-style col-xs-12 product-items');
            }
            if(column == 2) {
                $('#content .product-items').attr('class', 'product-layout product-grid grid-style col-lg-6 col-md-6 col-sm-6 col-xs-6 product-items');
            }
            if(column == 3) {
                $('#content .product-items').attr('class', 'product-layout product-grid grid-style col-lg-4 col-md-4 col-sm-4 col-xs-6 product-items');
            }
            if(column == 4) {
                $('#content .product-items').attr('class', 'product-layout product-grid grid-style col-lg-3 col-md-4 col-sm-4 col-xs-6 product-items');
            }
            if(column == 5) {
                $('#content .product-items').attr('class', 'product-layout product-grid grid-style col-divide-5 col-md-3 col-sm-3 col-xs-6 product-items');
            }
			category_view.customGrid();
        }

        if(type == "list") {
            $('#content .product-items').attr('class', 'product-layout product-list col-xs-12 product-items');
			category_view.customList();
        }

        $('.btn-custom-view').removeClass('active');
        $('.' + element).addClass('active');

        localStorage.setItem('type', type);
        localStorage.setItem('cols', cols);
        localStorage.setItem('element', element);
    },
	'customList' : function() {
		$(".product-thumb .product-item").each(function() {
			var caption_inner = $(this).find('.caption >.inner')
			var category_options = $(this).find('.category-options');
			var ratings = $(this).find('.ratings');
			var product_description = $(this).find('.product-description');
			var button_group = $(this).find('.button-group');
			// category_options.appendTo(caption_inner);
			// ratings.appendTo(caption_inner);
			// product_description.appendTo(caption_inner);
			// button_group.appendTo(caption_inner);
		});
	},
	
	'customGrid' : function() {
		$(".product-thumb .product-item").each(function() {
			var box_hover = $(this).find('.box-hover');
			var category_options = $(this).find('.category-options');
			var ratings = $(this).find('.ratings');
			var product_description = $(this).find('.product-description');
			var button_group = $(this).find('.button-group');
			// category_options.appendTo(box_hover);
			// ratings.appendTo(box_hover);
			// product_description.appendTo(box_hover);
			// button_group.appendTo(box_hover);
		});
	}
}