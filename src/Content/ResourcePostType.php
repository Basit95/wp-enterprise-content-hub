<?php

namespace EnterpriseContentHub\Content;

final class ResourcePostType {
	public const POST_TYPE = 'ech_resource';
	public function register(): void {
		add_action( 'init', array( $this, 'register_post_type' ) );
	}
	public function register_post_type(): void {
		$labels = array(
			'name'               => __( 'Resources', 'wp-enterprise-content-hub' ),
			'singular_name'      => __( 'Resource', 'wp-enterprise-content-hub' ),
			'add_new'            => __( 'Add New', 'wp-enterprise-content-hub' ),
			'add_new_item'       => __( 'Add New Resource', 'wp-enterprise-content-hub' ),
			'edit_item'          => __( 'Edit Resource', 'wp-enterprise-content-hub' ),
			'new_item'           => __( 'New Resource', 'wp-enterprise-content-hub' ),
			'view_item'          => __( 'View Resource', 'wp-enterprise-content-hub' ),
			'search_items'       => __( 'Search Resources', 'wp-enterprise-content-hub' ),
			'not_found'          => __( 'No resources found.', 'wp-enterprise-content-hub' ),
			'not_found_in_trash' => __( 'No resources found in trash.', 'wp-enterprise-content-hub' ),
			'menu_name'          => __( 'Resources', 'wp-enterprise-content-hub' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'show_in_rest'       => true,
			'has_archive'        => true,
			'rewrite'            => array(
				'slug' => 'resources',
			),
			'menu_icon'          => 'dashicons-media-document',
			'supports'           => array(
				'title',
				'editor',
				'excerpt',
				'thumbnail',
				'author',
				'revisions',
			),
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'query_var'          => true,

			'capability_type'    => array(
				'enterprise_resource',
				'enterprise_resources',
			),

			'map_meta_cap'       => true,

			'capabilities'       => array(
				'edit_post'              => 'edit_enterprise_resource',
				'read_post'              => 'read_enterprise_resource',
				'delete_post'            => 'delete_enterprise_resource',

				'edit_posts'             => 'edit_enterprise_resources',
				'edit_others_posts'      => 'edit_others_enterprise_resources',
				'publish_posts'          => 'publish_enterprise_resources',
				'read_private_posts'     => 'read_private_enterprise_resources',

				'delete_posts'           => 'delete_enterprise_resources',
				'delete_private_posts'   => 'delete_private_enterprise_resources',
				'delete_published_posts' => 'delete_published_enterprise_resources',
				'delete_others_posts'    => 'delete_others_enterprise_resources',

				'edit_private_posts'     => 'edit_private_enterprise_resources',
				'edit_published_posts'   => 'edit_published_enterprise_resources',
			),
		);

		register_post_type( self::POST_TYPE, $args );
	}
}
