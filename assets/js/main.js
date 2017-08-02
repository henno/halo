function tryToParseJSON(jsonString) {
    try {
        var o = JSON.parse(jsonString);

        // Handle non-exception-throwing cases:
        // Neither JSON.parse(false) or JSON.parse(1234) throw errors, hence the type-checking,
        // but... JSON.parse(null) returns null, and typeof null === "object",
        // so we must check for that, too. Thankfully, null is falsey, so this suffices:
        if (o && typeof o === "object") {
            return o;
        }
    }
    catch (e) {
    }

    return false;
}


function ajax(url, options, callback_or_redirect_url, error_callback) {

    $.post(url, options)
        .fail(function (jqXHR, textStatus, errorThrown) {
            console.log('Xhr error: ', jqXHR, textStatus, errorThrown);
        })
        .done(function (response) {
            var json = tryToParseJSON(response);

            if (json === false) {
                console.log(response);
                alert(SERVER_ERROR_UNKNOWN_FORMAT);
                return false;
            }

            else if (json.status === 401) {

                // Display modal
                $("#mdlLogin").modal();
                console.log('Need to log in.');

                if (typeof error_callback === 'function') {
                    error_callback(json);
                }

            }


            else if (json.status === 403) {

                alert(SERVER_ERROR_FORBIDDEN);

                console.log('Unauthorized');

                if (typeof error_callback === 'function') {
                    error_callback(json);
                }


            }

            else if (json.status !== 200) {


                if (typeof error_callback === 'function') {

                    error_callback(json);

                }
                else {
                    alert(SERVER_ERROR_OTHER + ': ' + response);
                }


            }
            else {

                if (typeof callback_or_redirect_url === 'function') {
                    callback_or_redirect_url(json);
                }

                if (typeof callback_or_redirect_url === 'string') {
                    location.href = callback_or_redirect_url;
                }
            }

        });
}

$('table.clickable-rows tr').on('click', function () {
    window.location = $(this).data('href');
});

/* Tooltipify the entire DOM */
$(document).tooltip();