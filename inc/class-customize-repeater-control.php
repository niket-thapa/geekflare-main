<?php
/**
 * Custom Repeater Control for WordPress Customizer
 *
 * Allows dynamic adding/removing of items in the customizer.
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Custom Repeater Control Class
 */
if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Main_Customize_Repeater_Control' ) ) {
	class Main_Customize_Repeater_Control extends WP_Customize_Control {

	/**
	 * Control type
	 *
	 * @var string
	 */
	public $type = 'repeater';

	/**
	 * Label for the add button
	 *
	 * @var string
	 */
	public $button_label = '';

	/**
	 * Enqueue control scripts and styles
	 */
	public function enqueue() {
		$script_path = get_stylesheet_directory() . '/src/js/customizer-repeater.js';
		wp_enqueue_script(
			'main-customizer-repeater',
			get_stylesheet_directory_uri() . '/src/js/customizer-repeater.js',
			array( 'jquery', 'customize-controls' ),
			time(),
			false // Load in header for customizer
		);

		wp_enqueue_style(
			'main-customizer-repeater',
			get_stylesheet_directory_uri() . '/src/css/customizer-repeater.css',
			array( 'customize-controls' ),
			wp_get_theme()->get( 'Version' )
		);
	}

	/**
	 * Render the control's content
	 */
	public function render_content() {
		if ( empty( $this->button_label ) ) {
			$this->button_label = __( 'Add Item', 'main' );
		}

		$values = json_decode( $this->value(), true );
		if ( ! is_array( $values ) ) {
			$values = array();
		}
		?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<?php endif; ?>

		<ul class="main-repeater-control-list" data-type="<?php echo esc_attr( $this->type ); ?>">
			<?php
			if ( ! empty( $values ) ) {
				foreach ( $values as $key => $value ) {
					$this->render_item( $key, $value );
				}
			}
			?>
		</ul>

		<input type="hidden" class="main-repeater-setting" <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>" />
		<button type="button" class="button button-secondary main-repeater-add"><?php echo esc_html( $this->button_label ); ?></button>
		<?php
	}

	/**
	 * Render a single repeater item
	 *
	 * @param int   $key Item index.
	 * @param array $value Item values.
	 */
	protected function render_item( $key, $value ) {
		$image = isset( $value['image'] ) ? $value['image'] : '';
		$alt   = isset( $value['alt'] ) ? $value['alt'] : '';
		?>
		<li class="main-repeater-control-item">
			<div class="main-repeater-control-item-header">
				<span class="main-repeater-control-item-title">
					<?php echo esc_html( ! empty( $alt ) ? $alt : sprintf( __( 'Partner %d', 'main' ), $key + 1 ) ); ?>
				</span>
				<button type="button" class="button-link main-repeater-remove" aria-label="<?php esc_attr_e( 'Remove', 'main' ); ?>">
					<?php esc_html_e( 'Remove', 'main' ); ?>
				</button>
			</div>
			<div class="main-repeater-control-item-content">
				<div class="main-repeater-control-field">
					<label>
						<span class="customize-control-title"><?php esc_html_e( 'Logo Image', 'main' ); ?></span>
						<div class="main-repeater-image-container">
							<?php if ( ! empty( $image ) ) : ?>
								<img src="<?php echo esc_url( $image ); ?>" alt="" class="main-repeater-image-preview" />
							<?php else : ?>
								<div class="main-repeater-image-placeholder">
									<?php esc_html_e( 'No image selected', 'main' ); ?>
								</div>
							<?php endif; ?>
							<div class="main-repeater-image-buttons">
								<button type="button" class="button main-repeater-upload-image">
									<?php esc_html_e( 'Select Image', 'main' ); ?>
								</button>
								<button type="button" class="button main-repeater-remove-image" style="<?php echo empty( $image ) ? 'display:none;' : ''; ?>">
									<?php esc_html_e( 'Remove', 'main' ); ?>
								</button>
							</div>
							<input type="hidden" class="main-repeater-image-url" value="<?php echo esc_attr( $image ); ?>" />
						</div>
					</label>
				</div>
				<div class="main-repeater-control-field">
					<label>
						<span class="customize-control-title"><?php esc_html_e( 'Alt Text', 'main' ); ?></span>
						<input type="text" class="main-repeater-alt-text" value="<?php echo esc_attr( $alt ); ?>" placeholder="<?php esc_attr_e( 'Partner name', 'main' ); ?>" />
					</label>
				</div>
			</div>
		</li>
		<?php
	}
	}
} // End if class_exists check
