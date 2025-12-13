<?php
/**
 * Template part for displaying post meta bar (date, disclosure, share)
 *
 * @package Main
 * @since 1.0.0
 */

if ( 'post' !== get_post_type() ) {
	return;
}
?>
<div class="post-meta-bar relative">
	<div class="post-meta-info">
		<span class="post-meta-updated">
			Last Updated :
			<time datetime="<?php echo esc_attr( get_the_modified_date( 'Y-m-d' ) ); ?>">
				<?php echo esc_html( get_the_modified_date( 'j M, Y' ) ); ?>
			</time>
		</span>
		<?php
		// Show affiliate disclosure only for buying guide template
		if ( function_exists( 'main_is_buying_guide_template' ) && main_is_buying_guide_template() ) {
			$show_affiliate_disclosure = get_post_meta( get_the_ID(), 'show_affiliate_disclosure', true );
			if ( $show_affiliate_disclosure ) :
				?>
				<button type="button" class="post-meta-disclosure bg-gray-100 rounded-xl">
					<span class="post-meta-disclosure__icon" aria-hidden="true">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 18 18">
							<g stroke="#252b37" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
								<path d="M9 15.75a6.75 6.75 0 1 0 0-13.5 6.75 6.75 0 0 0 0 13.5M9 6h.008" />
								<path d="M8.25 9H9v3h.75" />
							</g>
						</svg>
					</span>
					Affiliate Disclosure
				</button>
				<div class="disclosure-drop absolute p-4 text-sm rounded-2xl bg-white text-gray-800 border-gray-200 border shadow-[0px_56px_23px_rgba(191,191,191,0.01),0px_32px_19px_rgba(191,191,191,0.05),0px_14px_14px_rgba(191,191,191,0.09),0px_4px_8px_rgba(191,191,191,0.1)] [&_p]:m-0 w-full z-20">
					<p>Geekflare is supported by our audience. We may earn commissions from buying links on this site.</p>
				</div>
				<?php
			endif;
		}
		?>
	</div>
	<div class="post-meta-share">
		<span class="post-meta-share__label">Share:</span>
		<div class="post-meta-share__icons">
			<a 
				href="<?php echo esc_url( main_get_social_share_url( 'linkedin' ) ); ?>" 
				class="share-icon share-icon--linkedin" 
				aria-label="Share on LinkedIn"
				target="_blank"
				rel="noopener noreferrer"
			>
				<img src="<?php echo esc_url( main_get_image_url( 'linkedin.svg' ) ); ?>" alt="LinkedIn" width="20" height="20" />
			</a>
			<a 
				href="<?php echo esc_url( main_get_social_share_url( 'twitter' ) ); ?>" 
				class="share-icon share-icon--twitter" 
				aria-label="Share on X"
				target="_blank"
				rel="noopener noreferrer"
			>
				<img src="<?php echo esc_url( main_get_image_url( 'x.svg' ) ); ?>" alt="X" width="20" height="20" />
			</a>
			<a 
				href="<?php echo esc_url( main_get_social_share_url( 'facebook' ) ); ?>" 
				class="share-icon share-icon--facebook" 
				aria-label="Share on Facebook"
				target="_blank"
				rel="noopener noreferrer"
			>
				<img src="<?php echo esc_url( main_get_image_url( 'facebook.svg' ) ); ?>" alt="Facebook" width="20" height="20" />
			</a>
		</div>
	</div>
</div>

