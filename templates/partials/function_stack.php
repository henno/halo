<?php foreach (get_function_stack() as $function_call): ?>
    <b><?= $function_call['location'] ?></b>
    <?= $function_call['function'] ?><br>
    <?= empty($function_call['params']) ? '' : "<pre>$function_call[params]</pre>" ?>
<?php endforeach ?>
