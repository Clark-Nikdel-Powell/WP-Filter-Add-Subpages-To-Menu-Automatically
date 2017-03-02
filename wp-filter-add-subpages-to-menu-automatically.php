<?php
namespace CNP;

/**
 * Filter in subpages to a menu automatically if add_subpages_automatically is set and true on the menu args.
 *
 * @param $items
 * @param $args
 *
 * @return array
 */
function filter_in_sub_pages( $items, $args ) {

	if ( ! isset( $args->add_subpages_automatically ) || false === $args->add_subpages_automatically ) {
		return $items;
	}

	global $post;
	$tmp = array();
	foreach ( $items as $key => $i ) {

		$tmp[] = $i;

		//if not page move on
		if ( 'page' !== $i->object ) {
			continue;
		}

		/*$page = get_post( $i->object_id );
		//if not parent page move on
		if ( ! isset( $page->post_parent ) || 0 !== $page->post_parent ) {
			continue;
		}*/

		$children = get_pages( array( 'child_of' => $i->object_id, 'sort_column' => 'menu_order' ) );

		foreach ( (array) $children as $c ) {

			if ( intval( $i->object_id ) === $c->post_parent ) {
				$menu_item_parent_id = $i->ID;
			} else {
				$menu_item_parent_id = $c->post_parent;
			}

			//set parent menu
			$c->menu_item_parent      = $menu_item_parent_id;
			$c->object_id             = $c->ID;
			$c->post_name             = $c->ID;
			$c->db_id                 = $c->ID;
			$c->object                = 'page';
			$c->type                  = 'post_type';
			$c->type_label            = 'Page';
			$c->url                   = get_permalink( $c->ID );
			$c->title                 = $c->post_title;
			$c->target                = '';
			$c->attr_title            = '';
			$c->description           = '';
			$c->classes               = array( '', 'menu-item', 'menu-item-type-post_type', 'menu-item-object-page' );
			$c->xfn                   = '';
			$c->current               = ( $post->ID == $c->ID ) ? true : false;
			$c->current_item_ancestor = ( $post->ID == $c->post_parent ) ? true : false;
			$c->current_item_parent   = ( $post->ID == $c->post_parent ) ? true : false;
			$tmp[]                    = $c;
		}
	}

	$items = $tmp;

	return $items;
}

add_filter( 'wp_nav_menu_objects', 'CNP\filter_in_sub_pages', 20, 2 );
