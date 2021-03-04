<style>
    .pause, .leave {
        background-color: pink;
    }
    .play, .timeupdate{
        background-color: lightgreen;
    }
    body > div.container > a:not(.active) {
        background-color: white !important;
        color: black !important;
        border: 0;
    }
    body > div.container > a.active {

    }
</style>

<br>
<table class="table table table-nonfluid table-bordered table-hover pria-log bordered">
    <tr>
        <th><?= __('Time') ?></th>
        <th><?= __('User') ?></th>
        <th><?= __('Activity') ?></th>
    </tr>
    <?php foreach ($log as $row): ?>
        <tr class="<?=$row['activityName']?>">
            <td><?=$row['activityLogTimestamp']?></td>
            <td><?=$row['name']?></td>
            <td><?=__($row['activityDescription'], "activities.activityDescription")?></td>
        </tr>
    <?php endforeach ?>
</table>