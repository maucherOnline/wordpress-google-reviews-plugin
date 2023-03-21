<?php
global $allowed_html;
$upgrade_link = get_site_url().'/wp-admin/admin.php?page=google-reviews-pricing';
$developer_console_link = 'https://developers.google.com/maps/documentation/places/web-service/place-id#find-id';
$docs_link = 'https://reviewsembedder.com/docs/how-to-overwrite-styles/?utm_source=wp_backend&utm_medium=how_to_page&utm_campaign=documentation';
?>

<h1><?php _e('How to use the premium version', 'grwp'); ?></h1>
<ol>
    <li><?php _e('Enter your business name', 'grwp'); ?></li>
    <li><?php _e('Click \'Search business\'', 'grwp'); ?></li>
    <li><?php _e('Choose your business from the list', 'grwp'); ?></li>
    <li><?php _e('Choose your preferred language', 'grwp'); ?></li>
    <li><?php _e('Click the \'Pull reviews\' button', 'grwp'); ?></li>
    <li><?php _e('Wait until the process is finished. The page will refresh automatically', 'grwp'); ?></li>
    <li><?php _e('Now you should already see your reviews. If not, click the \'Pull reviews\' button again', 'grwp'); ?></li>
    <li><?php _e('Configure styling and layout options', 'grwp'); ?></li>
    <li><?php _e('Hit \'save\'', 'grwp'); ?></li>
    <li><?php _e('You will see your changes applied right after saving', 'grwp'); ?></li>
    <li><?php _e('Use the shortcode <code>[google-reviews]</code> to show your reviews on pages, posts, Elementor, Beaver Builder, Bakery Builder etc.', 'grwp'); ?></li>
    <li>
		<?php
		echo
		wp_kses(
			sprintf(
				__('Check the <a href="%s" target="_blank">documentation</a>, to learn how to modify the shortcode output', 'grwp'),
				$docs_link)
			, $allowed_html
		);
		?>
    </li>
</ol>

<h1><?php _e('Video tutorial', 'grwp'); ?></h1>
<iframe style="display:block;" width="560" height="315" src="https://www.youtube-nocookie.com/embed/y2pWCW_cuNk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
