<h3><? __("Bookings") ?></h3>
<ul class="list-group">
    <? foreach ($bookings as $booking): ?>
        <li class="list-group-item">
            <a href="bookings/<?= $booking['booking_id'] ?>/<?= $booking['booking_name'] ?>"><?= $booking['booking_name'] ?></a>
        </li>
    <? endforeach ?>
</ul>

<?php if ($auth->is_admin): ?>
<h3><? __("Add new booking") ?></h3>

<form method="post" id="form">
    <form id="form" method="post">
        <table class="table table-bordered">
            <tr>
                <th><? __("Name") ?></th>
                <td><input type="text" name="data[booking_name]" placeholder=""/></td>
            </tr>
        </table>

        <button class="btn btn-primary" type="submit"><? __("Add") ?></button>
    </form>
    <?php endif; ?>
