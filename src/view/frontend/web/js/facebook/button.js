window.fbAsyncInit = function() {
    FB.init({
        appId      : devgenii_socialconnect.appid,
        xfbml      : true,
        version    : 'v2.7'
    });
};

function devgenii_socialconnect_signin_callback(response) {
    if (response.authResponse) {
        // User accepted

        // Successful login, do ajax request
        jQuery.ajax({
            type: 'POST',
            url:  devgenii_socialconnect.ajaxurl,
            data: {
                // CSRF protection
                state:  devgenii_socialconnect.state,

                // Short lived token
                access_token: response.authResponse.accessToken,

                // When this token expires
                expires_in: response.authResponse.expiresIn
            },
            dataType: 'json',
            success: function(data) {
                window.location.replace(data.redirect);
            }
        });
    } else {
        // User canceled - This currently doesn't work due to Facebook API issue
        console.log('User cancelled login or did not fully authorize.');
    }
}