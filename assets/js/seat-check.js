(function () {
    'use strict';
    document.addEventListener('DOMContentLoaded', function () {
        var qty = document.getElementById('quantity');
        if (!qty) {
            return;
        }
        var eventId = qty.getAttribute('data-event-id');
        var fb = document.getElementById('seat-feedback');
        var availEl = document.getElementById('avail-display');
        var base = document.body.getAttribute('data-base-url') || '';
        function check() {
            var q = parseInt(qty.value, 10);
            if (isNaN(q) || q < 1) {
                fb.textContent = 'Enter a valid quantity (1 or more).';
                fb.className = 'small text-muted';
                return;
            }
            var url = base + '/ajax/check_seats.php?event_id=' + encodeURIComponent(eventId) + '&quantity=' + encodeURIComponent(String(q));
            fetch(url, { credentials: 'same-origin' })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (!data.success) {
                        fb.textContent = data.message || 'Could not check seats.';
                        fb.className = 'small text-danger';
                        return;
                    }
                    availEl.textContent = String(data.available);
                    if (data.sufficient) {
                        fb.textContent = 'You can book ' + q + ' ticket(s). Remaining after booking: ' + data.remaining_after + '.';
                        fb.className = 'small text-success';
                    } else {
                        fb.textContent = 'Not enough seats. Available: ' + data.available + '.';
                        fb.className = 'small text-warning';
                    }
                })
                .catch(function () {
                    fb.textContent = 'Network error.';
                    fb.className = 'small text-danger';
                });
        }
        qty.addEventListener('input', check);
        qty.addEventListener('change', check);
        check();
    });
})();