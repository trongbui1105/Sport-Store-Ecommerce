//<![CDATA[
$(window).load(function () {
	ptquickview.initQuickViewContainer();
});

var ptquickview = {
	'initQuickViewContainer' : function () {
		$('body').append('<div class="quickview-container"></div>');
		$('div.quickview-container').load('index.php?route=plaza/quickview/appendcontainer');
	},

	'appendCloseFrameLink' : function () {
		$('div#quickview-content').prepend("<a href='javascript:void(0);' class='a-qv-close' onclick='ptquickview.closeQVFrame()'>" + $('#qv-text-close').val() + "</a>");
	},

	'closeQVFrame' : function () {
		$('.quickview-container').hide();
    	$('.quickview-load-img').hide();
    	$('#quickview-content').css('visibility','hidden');
	},

	'ajaxView'	: function (url) {
		if(url.search('route=product/product') != -1) {
			url = url.replace('route=product/product', 'route=plaza/quickview');
		} else {
			url = 'index.php?route=plaza/quickview/seoview&ourl=' + url;
		}

		$.ajax({
			url 		: url,
			type		: 'get',
			beforeSend	: function() {
				$('.quickview-load-img').show();
				$('.quickview-container').show();
			},
			success		: function(json) {
				if(json['success'] == true) {
					$('.quickview-load-img').hide();
					$('#quickview-content').html(json['html']);
					$('#quickview-content').css({'visibility':'visible'});
					ptquickview.appendCloseFrameLink();
				}
			}
		});
	}
};
//]]>
