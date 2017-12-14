const RELOAD = 33;
var error_modal = $("#error-modal");

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

                // Send error report
                $.post('email/send_error_report', {
                    javascript_received_json_payload_that_caused_the_error: response
                });

                show_error_modal(response);

                return false;

            } else if (json.status === 401) {

                if (typeof error_callback === 'function') {

                    error_callback(json);
                }

            } else if (json.status === 403) {


                if (typeof error_callback === 'function') {

                    error_callback(json);
                }
                else {

                    $(".error-modal-body").html(SERVER_ERROR_FORBIDDEN);

                    error_modal.modal('show');
                }


            } else if (json.status === 500) {

                // Send error report
                $.post('email/send_error_report', {
                    javascript_received_json_payload_that_caused_the_error: json
                });


                if (typeof error_callback === 'function') {
                    error_callback(json);
                } else {
                    show_error_modal(json.data);
                }

                return false;


            } else if (json.status.toString()[0] !== '2') {

                if (typeof error_callback === 'function') {
                    error_callback(json);
                } else {
                    show_error_modal(json.data);
                }

            } else {

                if (typeof callback_or_redirect_url === 'function') {
                    callback_or_redirect_url(json);
                }

                else if (typeof callback_or_redirect_url === 'string') {
                    location.href = callback_or_redirect_url;
                }

                else if (callback_or_redirect_url === RELOAD) {
                    location.reload();
                }

            }

        });

}

$('table.clickable-rows tr').on('click', function () {
    window.location = $(this).data('href');
});


function show_error_modal(error) {
    $(".error-modal-body").html(window.location.hostname === 'localhost' || window.location.hostname.slice(-4) === '.dev' ? error : SERVER_ERROR_OTHER);
    error_modal.modal('show');
}

/* Tooltipify the entire DOM */
$(document).tooltip();