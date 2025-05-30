<?php
if (! defined('ABSPATH'))
	exit;

/**
 * @var string $star_output
 * @var string $google_svg
 * @var array  $allowed_html
 * @var array  $review
 * @var bool   $link_user_profiles
 */
?>

<div class="g-review">
	<div class="gr-inner-header">
		<img
			class="gr-profile"
			src="<?php echo esc_attr($review['profile_photo_url']); ?>"
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
		<p>
            <?php if ($link_user_profiles) : ?>
            <a href="<?php echo esc_attr($review['author_url']); ?>" target="_blank"><?php echo esc_html($review['name']); ?></a>
            <?php else : ?>
                <span><?php echo esc_html($review['name']); ?></span>
            <?php endif; ?>
			<br>
			<span class="gr-stars"><?php echo wp_kses($star_output, $this->allowed_html); ?></span>
        </p>
	</div>

	<div class="gr-inner-body">
		<p><?php echo esc_html($review['text']); ?></p>
	</div>
</div>


