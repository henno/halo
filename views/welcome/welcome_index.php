<!-- Code for ajax -->
<script type="text/javascript">
    function clickme() {
        $.post("<?=BASE_URL?>welcome", function (data) {
            $(".result").html(data);
        });
    }
</script>


placeholder for welcome view

<!-- Button for executing ajax -->
<input type="button" onclick="clickme()" value="Make ajax request"/>

<!-- DIV to show ajax result -->
<div class="result"></div>

