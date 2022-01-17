var ptnewsletter = {
    'saveMail'  : function (mail_button) {
        var mail = mail_button.closest('.newsletter-content').find('.newsletter_email').val();
        var container = mail_button.closest('.newsletter-container');

        $.ajax({
            url: 'index.php?route=plaza/newsletter/subscribe',
            type: 'post',
            data: {
                mail : mail
            },
            beforeSend: function () {
                mail_button.button('loading');
                container.find('.newsletter-notification').removeClass().addClass('newsletter-notification').html('');
            },
            success: function (json) {
                if(json['status'] == true) {
                    container.find('.newsletter-notification').addClass('success').html(json['success']);
                    if($('.cbk_newsletter').is(':checked')) {
                        ptnewsletter.setCookie("mail", "existed", 1);
                    }
                } else {
                    container.find('.newsletter-notification').addClass('error').html(json['error']);
                }
            },
            complete: function () {
                mail_button.button('reset');
            }
        });
    },

    'closePopup': function () {
        if($('.cbk_newsletter').is(':checked')) {
            ptnewsletter.setCookie("mail", "existed", 1);
        }

        $('.newsletter-popup').hide();

    },

    'setCookie' : function (cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires;
    },

    'getCookie' : function(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    },

    'checkCookie' : function () {
        var user = ptnewsletter.getCookie("mail");
        if (user != "") {
            $('.newsletter-popup').hide();
        }
    }
}