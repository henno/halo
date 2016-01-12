<h1><? __("Booking") ?> '<?= $booking['booking_name'] ?>'</h1>
<table class="table table-bordered">

    <tr>
        <th><? __("Booking") ?> ID</th>
        <td><?= $booking['booking_id'] ?></td>
    </tr>

    <tr>
        <th><? __("Booking") ?><? __("name") ?></th>
        <td><?= $booking['booking_name'] ?></td>
    </tr>

</table>

<!-- EDIT BUTTON -->
<? if ($auth->is_admin): ?>
    <form action="bookings/edit/<?= $booking['booking_id'] ?>">
        <div class="pull-right">
            <button class="btn btn-primary">
                <? __("Edit") ?>
            </button>
        </div>
    </form>
<? endif; ?>