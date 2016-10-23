<?php if($display_city_name) :?>

<?php $i = $display_city_name ;?>

<?php require("information.php"); ?>


<?php elseif ($display_city_name == NULL) :?>

<div class="wrap">
<?php for( $i = 1; $i<=4; $i++ ):?>


<?php require("information.php"); ?>

<?php endfor; ?>
    
    </div>

<?php endif; ?>