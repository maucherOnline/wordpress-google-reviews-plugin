<?php
if (! defined('ABSPATH'))
	exit;


/**
 * @var string $star_output
 * @var string $text
 * @var string $google_svg
 * @var string $profile_photo_url
 * @var string $author_url
 * @var string $name
 * @var array  $allowed_html
 */
?>

<div class="g-review">
	<div class="gr-inner-header">
		<img
			class="gr-profile"
			src="<?php echo esc_attr($profile_photo_url); ?>"
			width="50"
			height="50"
			alt=""
			data-imgtype="image/png"
			referrerpolicy="no-referrer"
		/>
		<img
			src="<?php echo esc_attr($google_svg); ?>"
			alt=""
			class="gr-google"
		/>
		<p><a href="<?php echo esc_attr($author_url); ?>" target="_blank"><?php echo esc_html($name); ?></a>
			<br>
			<span class="gr-stars"><?php echo wp_kses($star_output, $allowed_html); ?></span></p>
	</div>

	<div class="gr-inner-body">
		<p><?php echo esc_html($text); ?></p>
	</div>
</div>
