<?php

namespace EnterpriseContentHub\Content;

final class ResourceTaxonomy {
	public const TAXONOMY = 'ech_department';
	public function register(): void {
		add_action( 'init', array( $this, 'register_taxonomy' ) );
	}

	public function register_taxonomy(): void {

		$labels = array(
			'name'          => __( 'Departments', 'wp-enterprise-content-hub' ),
			'singular_name' => __( 'Department', 'wp-enterprise-content-hub' ),
			'search_items'  => __( 'Search Departments', 'wp-enterprise-content-hub' ),
			'all_items'     => __( 'All Departments', 'wp-enterprise-content-hub' ),
			'edit_item'     => __( 'Edit Department', 'wp-enterprise-content-hub' ),
			'update_item'   => __( 'Update Department', 'wp-enterprise-content-hub' ),
			'add_new_item'  => __( 'Add New Department', 'wp-enterprise-content-hub' ),
			'new_item_name' => __( 'New Department Name', 'wp-enterprise-content-hub' ),
			'menu_name'     => __( 'Departments', 'wp-enterprise-content-hub' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'resource-department' ),
		);

		register_taxonomy( self::TAXONOMY, array( ResourcePostType::POST_TYPE ), $args );
	}
}
