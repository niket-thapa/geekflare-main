<?php
/**
 * SVG Upload Handler Class
 *
 * Main class for handling SVG uploads, MIME type fixes, and media library display.
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SVG_Handler
 *
 * Handles all SVG-related functionality in WordPress.
 */
class SVG_Handler {

	/**
	 * Sanitizer instance
	 *
	 * @var SVG_Sanitizer
	 */
	private $sanitizer;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->sanitizer = new SVG_Sanitizer();

		// Enable SVG uploads globally (with permission check in the filter)
		add_filter( 'upload_mimes', array( $this, 'add_svg_mime_types' ) );

		// Also enable in specific contexts for compatibility
		$this->init_upload_hooks();

		// Fix MIME type detection
		add_filter( 'wp_check_filetype_and_ext', array( $this, 'fix_mime_type' ), 75, 5 );

		// Sanitize SVG files on upload
		add_filter( 'wp_handle_upload_prefilter', array( $this, 'sanitize_upload' ), 10, 1 );
		add_filter( 'wp_handle_sideload_prefilter', array( $this, 'sanitize_upload' ), 10, 1 );

		// Fix media library display
		add_filter( 'wp_prepare_attachment_for_js', array( $this, 'fix_media_library_display' ), 10, 3 );
		add_filter( 'wp_get_attachment_image_src', array( $this, 'fix_image_dimensions' ), 10, 4 );
		add_filter( 'admin_post_thumbnail_html', array( $this, 'fix_featured_image' ), 10, 3 );
		add_filter( 'get_image_tag', array( $this, 'fix_image_tag' ), 10, 6 );

		// Skip SVG regeneration
		add_filter( 'wp_generate_attachment_metadata', array( $this, 'skip_regeneration' ), 10, 2 );
		add_filter( 'wp_get_attachment_metadata', array( $this, 'fix_metadata_errors' ), 10, 2 );
		add_filter( 'wp_calculate_image_srcset_meta', array( $this, 'disable_srcset' ), 10, 4 );

		// Enqueue admin styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
	}

	/**
	 * Initialize upload hooks for specific contexts
	 * (Kept for compatibility, but upload_mimes is now added globally)
	 */
	private function init_upload_hooks() {
		// Media upload tabs hook (for compatibility)
		add_filter( 'media_upload_tabs', array( $this, 'enable_svg_from_media_tabs' ) );
	}

	/**
	 * Enable SVG from media upload tabs
	 *
	 * @param array $tabs Media upload tabs.
	 * @return array
	 */
	public function enable_svg_from_media_tabs( $tabs ) {
		// Filter is already added globally, just return tabs
		return $tabs;
	}

	/**
	 * Add SVG MIME types
	 *
	 * @param array $mimes Existing MIME types.
	 * @return array Modified MIME types.
	 */
	public function add_svg_mime_types( $mimes ) {
		if ( $this->user_can_upload_svg() ) {
			$mimes['svg']  = 'image/svg+xml';
			$mimes['svgz'] = 'image/svg+xml';
		}
		return $mimes;
	}

	/**
	 * Check if current user can upload SVG files
	 *
	 * @return bool True if user can upload SVG.
	 */
	private function user_can_upload_svg() {
		/**
		 * Filter to determine if current user can upload SVG files
		 *
		 * @param bool $can_upload Default capability check.
		 * @return bool
		 */
		$can_upload = current_user_can( 'upload_files' );
		return apply_filters( 'main_theme_svg_upload_capability', $can_upload );
	}

	/**
	 * Fix MIME type detection for SVG files
	 *
	 * @param array  $data     File data array.
	 * @param string $file     Full path to the file.
	 * @param string $filename The name of the file.
	 * @param array  $mimes    Array of MIME types.
	 * @param string $real_mime Real MIME type.
	 * @return array Modified file data.
	 */
	public function fix_mime_type( $data, $file, $filename, $mimes, $real_mime = '' ) {
		// If WordPress already detected the type, return it
		if ( ! empty( $data['ext'] ) && ! empty( $data['type'] ) ) {
			return $data;
		}

		// Get file extension
		$wp_file_type = wp_check_filetype( $filename, $mimes );
		$ext          = ! empty( $wp_file_type['ext'] ) ? $wp_file_type['ext'] : '';
		
		if ( empty( $ext ) ) {
			$file_parts = explode( '.', $filename );
			$ext        = strtolower( end( $file_parts ) );
		}

		// Set SVG MIME type
		if ( 'svg' === $ext ) {
			$data['ext']  = 'svg';
			$data['type'] = 'image/svg+xml';
		} elseif ( 'svgz' === $ext ) {
			$data['ext']  = 'svgz';
			$data['type'] = 'image/svg+xml';
		}

		return $data;
	}

	/**
	 * Sanitize SVG file on upload
	 *
	 * @param array $file File array from upload.
	 * @return array Modified file array.
	 */
	public function sanitize_upload( $file ) {
		// Ensure we have a proper file path
		if ( ! isset( $file['tmp_name'] ) || ! isset( $file['name'] ) ) {
			return $file;
		}

		$file_name = $file['name'];
		$file_type = isset( $file['type'] ) ? $file['type'] : '';

		// Check file type (filters are already in place globally)
		$wp_file_type = wp_check_filetype_and_ext( $file['tmp_name'], $file_name );

		$detected_type = ! empty( $wp_file_type['type'] ) ? $wp_file_type['type'] : $file_type;

		// Also check by extension as fallback
		$file_parts = explode( '.', $file_name );
		$ext        = strtolower( end( $file_parts ) );

		// Check if it's an SVG
		if ( 'image/svg+xml' === $detected_type || 'svg' === $ext || 'svgz' === $ext ) {
			// Check user permissions
			if ( ! $this->user_can_upload_svg() ) {
				$file['error'] = __( 'Sorry, you are not allowed to upload SVG files.', 'main' );
				return $file;
			}

			// Sanitize the SVG file
			$svg_content = file_get_contents( $file['tmp_name'] );
			
			if ( false !== $svg_content ) {
				$sanitized = $this->sanitizer->sanitize( $svg_content );
				
				if ( false === $sanitized ) {
					$file['error'] = __( 'Sorry, this SVG file could not be sanitized for security reasons.', 'main' );
					return $file;
				}

				// Write sanitized content back
				file_put_contents( $file['tmp_name'], $sanitized );
			}
		}

		return $file;
	}

	/**
	 * Fix media library display for SVG files
	 *
	 * @param array      $response   Attachment data.
	 * @param int|object $attachment Attachment ID or object.
	 * @param array      $meta       Attachment metadata.
	 * @return array Modified response.
	 */
	public function fix_media_library_display( $response, $attachment, $meta ) {
		if ( 'image/svg+xml' !== $response['mime'] ) {
			return $response;
		}

		$dimensions = $this->get_svg_dimensions( is_object( $attachment ) ? $attachment->ID : $attachment );

		if ( $dimensions ) {
			$response['width']  = $dimensions['width'];
			$response['height'] = $dimensions['height'];
		} else {
			$response['width']  = 200;
			$response['height'] = 200;
		}

		// Set image sizes
		$image_sizes = apply_filters(
			'image_size_names_choose',
			array(
				'full'      => __( 'Full Size', 'main' ),
				'thumbnail' => __( 'Thumbnail', 'main' ),
				'medium'    => __( 'Medium', 'main' ),
				'large'     => __( 'Large', 'main' ),
			)
		);

		$sizes = array();
		foreach ( $image_sizes as $size => $label ) {
			$default_height = 200;
			$default_width  = 200;

			if ( 'full' === $size && $dimensions ) {
				$default_height = $dimensions['height'];
				$default_width  = $dimensions['width'];
			}

			$sizes[ $size ] = array(
				'height'      => $default_height,
				'width'       => $default_width,
				'url'         => $response['url'],
				'orientation' => ( $default_width > $default_height ) ? 'landscape' : 'portrait',
			);
		}

		$response['sizes'] = $sizes;
		$response['icon']  = $response['url'];
		$response['image'] = array(
			'src'    => $response['url'],
			'width'  => $response['width'],
			'height' => $response['height'],
		);
		$response['thumb'] = array(
			'src'    => $response['url'],
			'width'  => 150,
			'height' => 150,
		);

		return $response;
	}

	/**
	 * Fix image dimensions for SVG files
	 *
	 * @param array|false $image         Image data or false.
	 * @param int         $attachment_id Attachment ID.
	 * @param string|array $size          Image size.
	 * @param bool         $icon          Whether image is an icon.
	 * @return array|false Modified image data.
	 */
	public function fix_image_dimensions( $image, $attachment_id, $size, $icon ) {
		if ( 'image/svg+xml' === get_post_mime_type( $attachment_id ) ) {
			$dimensions = $this->get_svg_dimensions( $attachment_id, $size );

			if ( $dimensions ) {
				$image[1] = $dimensions['width'];
				$image[2] = $dimensions['height'];
			} else {
				$image[1] = 100;
				$image[2] = 100;
			}
		}

		return $image;
	}

	/**
	 * Fix featured image display
	 *
	 * @param string   $content      Featured image HTML.
	 * @param int      $post_id      Post ID.
	 * @param int|null $thumbnail_id Thumbnail attachment ID.
	 * @return string Modified HTML.
	 */
	public function fix_featured_image( $content, $post_id, $thumbnail_id = null ) {
		if ( $thumbnail_id && 'image/svg+xml' === get_post_mime_type( $thumbnail_id ) ) {
			$content = sprintf( '<span class="svg-featured-image">%s</span>', $content );
		}

		return $content;
	}

	/**
	 * Fix image tag for SVG files
	 *
	 * @param string       $html  Image HTML.
	 * @param int          $id    Attachment ID.
	 * @param string       $alt   Alt text.
	 * @param string       $title Title.
	 * @param string       $align Alignment.
	 * @param string|array $size  Image size.
	 * @return string Modified HTML.
	 */
	public function fix_image_tag( $html, $id, $alt, $title, $align, $size ) {
		if ( 'image/svg+xml' === get_post_mime_type( $id ) ) {
			$dimensions = null;

			if ( is_array( $size ) ) {
				$width  = $size[0];
				$height = $size[1];
			} elseif ( 'full' === $size ) {
				$dimensions = $this->get_svg_dimensions( $id );
				if ( $dimensions ) {
					$width  = $dimensions['width'];
					$height = $dimensions['height'];
				} else {
					$width  = false;
					$height = false;
				}
			} else {
				$width  = get_option( "{$size}_size_w", false );
				$height = get_option( "{$size}_size_h", false );
			}

			if ( $width && $height ) {
				$html = str_replace( 'width="1"', sprintf( 'width="%s"', $width ), $html );
				$html = str_replace( 'height="1"', sprintf( 'height="%s"', $height ), $html );
			} else {
				$html = str_replace( 'width="1"', '', $html );
				$html = str_replace( 'height="1"', '', $html );
			}

			$html = str_replace( '/>', ' role="img" />', $html );
		}

		return $html;
	}

	/**
	 * Skip SVG regeneration (SVGs don't need thumbnails)
	 *
	 * @param array $metadata      Attachment metadata.
	 * @param int   $attachment_id Attachment ID.
	 * @return array Modified metadata.
	 */
	public function skip_regeneration( $metadata, $attachment_id ) {
		if ( 'image/svg+xml' !== get_post_mime_type( $attachment_id ) ) {
			return $metadata;
		}

		$svg_path   = get_attached_file( $attachment_id );
		$upload_dir = wp_upload_dir();
		$relative_path = str_replace( trailingslashit( $upload_dir['basedir'] ), '', $svg_path );
		$filename      = basename( $svg_path );

		$dimensions = $this->get_svg_dimensions( $attachment_id );

		if ( ! $dimensions ) {
			return $metadata;
		}

		$metadata = array(
			'width'  => intval( $dimensions['width'] ),
			'height' => intval( $dimensions['height'] ),
			'file'   => $relative_path,
		);

		// Create sizes array
		$sizes = array();
		$additional_sizes = wp_get_additional_image_sizes();

		foreach ( get_intermediate_image_sizes() as $size_name ) {
			$size_data = array(
				'width'  => '',
				'height' => '',
				'crop'   => false,
				'file'   => $filename,
			);

			if ( isset( $additional_sizes[ $size_name ]['width'] ) ) {
				$size_data['width'] = intval( $additional_sizes[ $size_name ]['width'] );
			} else {
				$size_data['width'] = get_option( "{$size_name}_size_w" );
			}

			if ( isset( $additional_sizes[ $size_name ]['height'] ) ) {
				$size_data['height'] = intval( $additional_sizes[ $size_name ]['height'] );
			} else {
				$size_data['height'] = get_option( "{$size_name}_size_h" );
			}

			if ( isset( $additional_sizes[ $size_name ]['crop'] ) ) {
				$size_data['crop'] = $additional_sizes[ $size_name ]['crop'];
			} else {
				$size_data['crop'] = get_option( "{$size_name}_crop" );
			}

			$sizes[ $size_name ] = $size_data;
		}

		$metadata['sizes'] = $sizes;

		return $metadata;
	}

	/**
	 * Fix metadata errors for SVG files
	 *
	 * @param array|bool $data    Metadata or false.
	 * @param int        $post_id Attachment ID.
	 * @return array|bool Fixed metadata.
	 */
	public function fix_metadata_errors( $data, $post_id ) {
		if ( is_wp_error( $data ) ) {
			$data = wp_generate_attachment_metadata( $post_id, get_attached_file( $post_id ) );
			wp_update_attachment_metadata( $post_id, $data );
		}

		return $data;
	}

	/**
	 * Disable srcset for SVG files
	 *
	 * @param array  $image_meta    Image metadata.
	 * @param array  $size_array    Size array.
	 * @param string $image_src     Image source.
	 * @param int    $attachment_id Attachment ID.
	 * @return array Modified metadata.
	 */
	public function disable_srcset( $image_meta, $size_array, $image_src, $attachment_id ) {
		if ( $attachment_id && 'image/svg+xml' === get_post_mime_type( $attachment_id ) && is_array( $image_meta ) ) {
			$image_meta['sizes'] = array();
		}

		return $image_meta;
	}

	/**
	 * Get SVG dimensions
	 *
	 * @param int         $attachment_id Attachment ID.
	 * @param string|array $size          Image size (optional).
	 * @return array|false Dimensions array or false.
	 */
	private function get_svg_dimensions( $attachment_id, $size = null ) {
		/**
		 * Filter to short-circuit dimension calculation
		 *
		 * @param bool|array $dimensions   Dimensions or false.
		 * @param int        $attachment_id Attachment ID.
		 * @param string|array $size        Image size.
		 * @return bool|array
		 */
		$short_circuit = apply_filters( 'main_theme_svg_pre_dimensions', false, $attachment_id, $size );
		if ( false !== $short_circuit ) {
			return $short_circuit;
		}

		if ( ! function_exists( 'simplexml_load_file' ) ) {
			return false;
		}

		$svg      = get_attached_file( $attachment_id );
		$metadata = wp_get_attachment_metadata( $attachment_id );
		$width    = 0;
		$height   = 0;

		// Use cached metadata if available
		if ( $svg && ! empty( $metadata['width'] ) && ! empty( $metadata['height'] ) ) {
			$width  = floatval( $metadata['width'] );
			$height = floatval( $metadata['height'] );
		} elseif ( $svg ) {
			// Parse SVG file
			$xml = @simplexml_load_file( $svg );

			if ( ! $xml ) {
				return false;
			}

			$attributes = $xml->attributes();
			$viewbox_width  = null;
			$viewbox_height = null;
			$attr_width     = null;
			$attr_height    = null;

			// Get viewBox dimensions
			if ( isset( $attributes->viewBox ) ) {
				$viewbox_parts = explode( ' ', (string) $attributes->viewBox );
				if ( isset( $viewbox_parts[2], $viewbox_parts[3] ) ) {
					$viewbox_width  = floatval( $viewbox_parts[2] );
					$viewbox_height = floatval( $viewbox_parts[3] );
				}
			}

			// Get width and height attributes
			if ( isset( $attributes->width, $attributes->height ) ) {
				$width_str  = (string) $attributes->width;
				$height_str = (string) $attributes->height;

				// Skip if percentage-based
				if ( strpos( $width_str, '%' ) === false && strpos( $height_str, '%' ) === false ) {
					if ( is_numeric( $width_str ) && is_numeric( $height_str ) ) {
						$attr_width  = floatval( $width_str );
						$attr_height = floatval( $height_str );
					}
				}
			}

			/**
			 * Filter to determine which attributes to use first
			 *
			 * @param bool   $use_width_height Use width/height attributes first.
			 * @param string $svg              SVG file path.
			 * @param int    $attachment_id     Attachment ID.
			 * @return bool
			 */
			$use_width_height = apply_filters( 'main_theme_svg_use_width_height_attributes', false, $svg, $attachment_id );

			if ( $use_width_height ) {
				if ( null !== $attr_width && null !== $attr_height ) {
					$width  = $attr_width;
					$height = $attr_height;
				} elseif ( null !== $viewbox_width && null !== $viewbox_height ) {
					$width  = $viewbox_width;
					$height = $viewbox_height;
				}
			} else {
				if ( null !== $viewbox_width && null !== $viewbox_height ) {
					$width  = $viewbox_width;
					$height = $viewbox_height;
				} elseif ( null !== $attr_width && null !== $attr_height ) {
					$width  = $attr_width;
					$height = $attr_height;
				}
			}

			if ( ! $width || ! $height ) {
				return false;
			}
		}

		$dimensions = array(
			'width'       => $width,
			'height'      => $height,
			'orientation' => ( $width > $height ) ? 'landscape' : 'portrait',
		);

		/**
		 * Filter SVG dimensions
		 *
		 * @param array  $dimensions    Dimensions array.
		 * @param string $svg           SVG file path.
		 * @param int    $attachment_id Attachment ID.
		 * @return array
		 */
		return apply_filters( 'main_theme_svg_dimensions', $dimensions, $svg, $attachment_id );
	}

	/**
	 * Enqueue admin styles for SVG display
	 */
	public function enqueue_admin_styles() {
		$css_file = MAIN_THEME_DIR . '/inc/svg-upload/svg-admin-styles.css';
		if ( file_exists( $css_file ) ) {
			wp_enqueue_style(
				'main-theme-svg-admin',
				MAIN_THEME_URI . '/inc/svg-upload/svg-admin-styles.css',
				array(),
				'1.0.0'
			);
		}
	}
}

