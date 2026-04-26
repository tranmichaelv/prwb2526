<?php
require_once 'utils/AppTime.php';
?>
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<?php if(Configuration::is_dev()):?>
    <div class="menu-time">
        <div class="menu-left">
            <i class="bi bi-clock"></i> 
            <?php $time = AppTime::get_current_datetime();
                strtotime($time);
                echo date("d/m/Y H:i")
            ?>
        </div>

        <div class="menu-right">
            <form method="post" action="time/advance">
                <input type="hidden" name="amount" value="1">
                <input type="hidden" name="unit" value="hour">
                <button type="submit">+1h</button>
            </form>

            <form method="post" action="time/advance">
                <input type="hidden" name="amount" value="1">
                <input type="hidden" name="unit" value="day">
                <button type="submit">+1d</button>
            </form>

            <form method="post" action="time/advance">
                <input type="hidden" name="amount" value="7">
                <input type="hidden" name="unit" value="day">
                <button type="submit">+1w</button>
            </form>

            <form method="post" action="time/reset">
                <button type="submit">Reset</button>
            </form>
        </div>
    </div>
<?php endif;?>
