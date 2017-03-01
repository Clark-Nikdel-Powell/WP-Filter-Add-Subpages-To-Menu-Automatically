<?php
namespace CNP;

/**
 * Filter in subpages to a menu automatically if add_subpages_automatically is set and true on the menu args.
 *
 * @param $item_output
 * @param $item
 * @param $depth
 * @param $args
 *
 * @return string
 */
function filter_in_sub_pages( $item_output, $item, $depth, $args ) {

	if ( ! isset( $args->add_subpages_automatically ) || false === $args->add_subpages_automatically ) {
		return $item_output;
	}

	// if not page move on.
	if ( 'page' !== $item->object ) {
		return $item_output;
	}

	$page = get_post( $item->object_id );
	// if not parent page move on.
	if ( ! isset( $page->post_parent ) || 0 !== $page->post_parent ) {
		return $item_output;
	}

	$pages_args = [
		'sort_column' => 'menu_order',
		'child_of'    => $item->object_id,
		'title_li'    => '',
		'echo'        => false,
		'context'     => 'menu',
		'walker'      => new Filterable_Walker_Page(),
	];

	$children = wp_list_pages( $pages_args );
	if ( '' !== $children ) {
		$item_output .= '<ul class="sub-menu">' . $children . '</ul>';
	}

	return $item_output;
}

add_filter( 'walker_nav_menu_start_el', 'CNP\filter_in_sub_pages', 20, 4 );
