<!-- Code for ajax -->
<script type="text/javascript">
    function clickme() {
        $.post("<?=BASE_URL?>welcome", function (data) {
            $(".result").html(data);
        });
    }
</script>


placeholder for welcome view



<!-- AJAX EXAMPLE -->
<!-- Button for executing ajax -->
<input type="button" onclick="clickme()" value="Make ajax request"/>

<!-- DIV to show ajax result -->
<div class="result"></div>



<!-- POST EXAMPLE -->

<!-- Button for executing ajax -->
<form method="post">
    <input type="text" name="foobar"/>
    <input type="submit" value="Make ajax request"/>
</form>

