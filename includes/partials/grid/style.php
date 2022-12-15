<?php

if (! defined('ABSPATH'))
	exit;


$flex_columns_css = '';
if ($db_grid_columns == 1) {
    $flex_columns_css = '100%';
}
elseif ($db_grid_columns == 2) {
    $flex_columns_css = 'calc(50% - 15px)';
}
else {
    $flex_columns_css = 'calc(33.333% - 20px)';
}

?>

<style>

    #g-review {
        /*grid-template-columns: <?php //echo esc_html($columns_css); ?>;*/


    }
    #g-review .g-review {
        flex-basis: <?php echo $flex_columns_css; ?>
    }


</style>
