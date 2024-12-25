const RELOAD = 33;

/**
 * Sends POST request expecting JSON response with "status" field
 * @param {string} url - Server endpoint
 * @param {Object} payload - Data to send
 * @param {Function|string|number} onSuccessOrRedirect - Callback or redirect URL or RELOAD
 * @param {Function} [onError] - Error callback
 */
async function ajax(url, payload, onSuccessOrRedirect, onError) {
    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
            body: JSON.stringify(payload)
        });

        const text = await res.text();
        if (!text.trim()) {
            showModal('Error', 'Server returned an empty response');
            return;
        }

        let json;
        try {
            json = JSON.parse(text);
        } catch {
            showModal(res.ok ? 'Error' : `Error ${res.status}`, text);
            return;
        }

        const {status, data} = json;

        if (+status >= 200 && +status < 300) {
            if (typeof onSuccessOrRedirect === 'function') onSuccessOrRedirect(json);
            else if (onSuccessOrRedirect === RELOAD) location.reload();
            else if (typeof onSuccessOrRedirect === 'string') location.href = onSuccessOrRedirect;
        } else onError?.(json) || showModal(res.ok ? 'Error' : `Error ${res.status}`, data || 'Unknown error');

    } catch (err) {
        showModal('Error', `Network error: ${err.message}`);
        onError?.(err);
    }
}

document.querySelectorAll('table.clickable-rows tr').forEach(row =>
    row.addEventListener('click', () => window.location = row.dataset.href)
);

function showModal(title, content) {
    $('#error-modal').modal({
        title,
        content,
        classContent: 'centered',
        class: 'small'
    }).modal('show');
}