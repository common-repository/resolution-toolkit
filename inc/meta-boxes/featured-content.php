<?php

if( Resolution_Toolkit::version_compare( '1.3' ) ) {

	add_action( 'kopa_admin_metabox_advanced_field', '__return_true' );
	add_action( 'admin_init', 'resolution_toolkit_register_metaboxes' );
	add_action( 'resolution_lite_before_print_post', 'resolution_toolkit_check_post_featured_content' );
	add_action( 'kf_rl_featured_content_saved', 'resolution_toolkit_validate_featured_content', 10, 1 );

	function resolution_toolkit_register_metaboxes() {
		$args = array(
		    'id'          => 'rl_featured_content',
		    'title'       => esc_html__( 'Featured content', 'resolution-toolkit'),
		    'desc'        => '',
		    'pages'       => array( 'post' ),
		    'context'     => 'normal',
		    'priority'    => 'high',
		    'fields'      => array(
		    	array(
					'title'   => esc_html__( 'Status', 'resolution-toolkit' ),
					'label'   => esc_html__( 'Is show featured content on the top of post?', 'resolution-toolkit' ),
					'type'    => 'checkbox',
					'id'      => 'rl_featured_status',
					'default' => 1
		        ),	
		        array(
		            'title' => esc_html__( 'Gallery', 'resolution-toolkit' ),
		            'type'  => 'gallery',
		            'id'    => 'rl_gallery'
		        ),	      
		        array(
					'title' => esc_html__( 'Video', 'resolution-toolkit' ),
					'desc'  => esc_html__( 'Enter youtube, vimeo link', 'resolution-toolkit' ),
					'type'  => 'text',
					'id'    => 'rl_video'
		        ),
		        array(
					'title' => esc_html__( 'Audio', 'resolution-toolkit' ),
					'desc'  => esc_html__( 'Upload or select existsing audio file.', 'resolution-toolkit' ),
					'type'  => 'upload',
					'mimes' => 'audio/mpeg',
					'id'    => 'rl_audio'
		        ),
		    )
		);

		if( function_exists( 'kopa_register_metabox' ) ){
			kopa_register_metabox( $args );
		}
	}

	function resolution_toolkit_check_post_featured_content( $post_id = 0 ) {
		if( !$post_id ){
			global $post;
			$post_id = $post->ID;
		}			

		$is_check = metadata_exists( 'post', $post_id, 'rl_featured_is_check' );

		if( !$is_check ) {
			update_post_meta( $post_id, 'rl_featured_status', true );
			resolution_toolkit_update_featured_content( $post_id );
		}
	}
	
	function  resolution_toolkit_update_featured_content( $post_id = 0 ){
		$post_format  = get_post_format( $post_id );
		$post_content = get_post_field( 'post_content', $post_id );

		$featured_value = '';
		$featured_type  = '';

		switch ( $post_format ) {
			case 'gallery':
				$featured = get_post_meta( $post_id, 'rl_gallery', true );

				if( ! $featured ){
					$featured = resolution_lite_content_get_media( $post_content, false, array( 'gallery') );														
					$featured = ( isset( $featured[0]['atts']['ids'] ) && !empty( $featured[0]['atts']['ids'] ) ) ? $featured[0]['atts']['ids'] : false;
				}
				
				if( $featured ){
					$featured_value = $featured;
					$featured_type  = 'gallery';
				}
				break;
			case 'audio':
				$featured = get_post_meta( $post_id, 'rl_audio', true );

				if( !$featured ){
					$featured = resolution_lite_content_get_media( $post_content, false, array( 'audio') );
					$featured = ( isset( $featured[0]['atts']['mp3'] ) && !empty( $featured[0]['atts']['mp3'] ) ) ? $featured[0]['atts']['mp3'] : false;
				}

				if( $featured ){							
					$featured_value = $featured;
					$featured_type  = 'audio';
				}
				break;
			case 'video':
				$featured = get_post_meta( $post_id, 'rl_video', true );

				if( !$featured ){
					$featured = resolution_lite_content_get_media( $post_content, false, array( 'embed') );							
					$featured = ( isset( $featured[0]['url'] ) && !empty( $featured[0]['url'] ) ) ? $featured[0]['url'] : false;							
				}
				
				if( $featured ){							
					$featured_value = $featured;
					$featured_type  = 'video';
				}
				break;
		}

		if( !$featured_value ) {
			if( has_post_thumbnail( $post_id ) ){
				$featured_type = 'thumbnail';
			}
		}

		update_post_meta( $post_id, 'rl_featured_is_check', true );
		update_post_meta( $post_id, 'rl_featured_type', $featured_type );
		update_post_meta( $post_id, 'rl_featured', $featured_value );
	}

	function resolution_toolkit_validate_featured_content( $post_id ){					
		resolution_toolkit_update_featured_content( $post_id );
	}	

}
