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
    } catch (e) {
    }

    return false;
}


function ajax(url, options, callback_or_redirect_url, error_callback) {


    $.post(url, options)
        .fail(function (jqXHR, textStatus, errorThrown) {
            console.log('Xhr error: ', jqXHR, textStatus, errorThrown);

            let error;
            let json = tryToParseJSON(jqXHR.responseText);
            if (json === false) {
                error = jqXHR.responseText;
            } else {
                if (typeof json.data === 'undefined') {
                    error = `<pre>${JSON.stringify(json, null, 2)}</pre>`;
                } else {
                    error = json.data;
                }
            }

            if (typeof error_callback === 'function') {
                error_callback(error);
            } else {
                show_error_modal(error, errorThrown);
            }

        })
        .done(function (response) {
            let json = tryToParseJSON(response);

            if (json === false) {

                // Send error report
                $.post('email/send_error_report', {
                    javascript_received_json_payload_that_caused_the_error: response
                });

                show_error_modal(response);

                return false;


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
                } else if (typeof callback_or_redirect_url === 'string') {
                    location.href = callback_or_redirect_url;
                } else if (callback_or_redirect_url === RELOAD) {
                    location.reload();
                }

            }

        });

}

$('table.clickable-rows tr').on('click', function () {
    window.location = $(this).data('href');
});


function show_error_modal(error, title = false) {
    $(".error-modal-body").html(error);
    if (title) {
        $(".error-modal-title").html(title);
    }
    error_modal.modal('show');
}
