$(document).ready(function () {
    $('.product-zoom-image').on('click', function () {
        var pos = $('#light-box-position').val();

        ptzoom.openLightBox(pos);
    });

    $('.sub-image').on('click', function () {
        var pos = $(this).data('pos');
        $('#light-box-position').val(pos);
		$('.additional-images .item').removeClass('img-active');
		$(this).closest('.item').addClass('img-active');
    });

    ptzoom.initAdditionalImagesSlider();
});

var ptzoom = {
    'initAdditionalImagesSlider'  : function () {
        if($('.additional-images').length) {
            $('.additional-images').swiper({
                loop: false,
                spaceBetween: 0,
                nextButton: '.additional-button-next',
                prevButton: '.additional-button-prev',
                speed: 300,
                slidesPerView: 4,
                autoPlay: false
            });
        }
    },

    'openLightBox' : function (position) {
        var product_id = $('#product-identify').val();
        var flag = false;

        $.ajax({
            url : 'index.php?route=plaza/zoom/openLightbox&product_id=' + product_id,
            type: 'get',
            success : function (json) {
                $('.lightbox-container').html(json['html']).show(500);
                ptzoom.showSlides(position);
                flag = true;
            },
            complete: function () {
                if(!flag) {
                    ptzoom.closeLightBox();
                }
            }
        });
    },

    'showSlides' : function (position) {
        var i;
        var slides = $(".mySlides");

        if (position > slides.length) {position = 1}
        if (position < 1) {position = slides.length}

        $('#light-box-position').val(position);

        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }

        slides[position-1].style.display = "block";
    },

    'plusSlides' : function (n) {
        var position = parseInt($('#light-box-position').val());

        ptzoom.showSlides(position += n);
    },

    'closeLightBox': function () {
        $('.lightbox-container').hide().html('');
    }
}