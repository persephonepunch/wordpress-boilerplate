<?php
/**
 * Steel functions and definitions.
 *
 * Sets up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development
 * and http://codex.wordpress.org/Child_Themes), you can override certain
 * functions (those wrapped in a function_exists() call) by defining them first
 * in your child theme's functions.php file. The child theme's functions.php
 * file is included before the parent theme's file, so the child theme
 * functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * see http://codex.wordpress.org/Plugin_API
 *
 * @package WordPress
 * @subpackage Steel
 * @since Steel 1.0
 */

/**
 * Sets up the content width value based on the theme's design.
 * @see steel_content_width() for template-specific adjustments.
 */
if ( ! isset( $content_width ) )
	$content_width = 604;

/**
 * Adds support for a custom header image.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Steel only works in WordPress 3.6 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '3.6-alpha', '<' ) )
	require get_template_directory() . '/inc/back-compat.php';

/**
 * Sets up theme defaults and registers the various WordPress features that
 * Steel supports.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To add Visual Editor stylesheets.
 * @uses add_theme_support() To add support for automatic feed links, post
 * formats, and post thumbnails.
 * @uses register_nav_menu() To add support for a navigation menu.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Steel 1.0
 *
 * @return void
 */
function steel_setup() {
	/*
	 * Makes Steel available for translation.
	 *
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on Steel, use a find and
	 * replace to change 'steel' to the name of your theme in all
	 * template files.
	 */
	load_theme_textdomain( 'steel', get_template_directory() . '/languages' );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, icons, and column width.
	 */
	add_editor_style( array( 'css/editor-style.css', 'fonts/genericons.css', steel_fonts_url() ) );

	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	// Switches default core markup for search form, comment form, and comments
	// to output valid HTML5.
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );

	/*
	 * This theme supports all available post formats by default.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video'
	) );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'primary', __( 'Navigation Menu', 'steel' ) );

	/*
	 * This theme uses a custom image size for featured images, displayed on
	 * "standard" posts and pages.
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 604, 270, true );

	// This theme uses its own gallery styles.
	add_filter( 'use_default_gallery_style', '__return_false' );
}
add_action( 'after_setup_theme', 'steel_setup' );

/**
 * Returns the Google font stylesheet URL, if available.
 *
 * The use of Source Sans Pro and Bitter by default is localized. For languages
 * that use characters not supported by the font, the font can be disabled.
 *
 * @since Steel 1.0
 *
 * @return string Font stylesheet or empty string if disabled.
 */
function steel_fonts_url() {
	$fonts_url = '';

	/* Translators: If there are characters in your language that are not
	 * supported by Source Sans Pro, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$source_sans_pro = _x( 'on', 'Source Sans Pro font: on or off', 'steel' );

	/* Translators: If there are characters in your language that are not
	 * supported by Bitter, translate this to 'off'. Do not translate into your
	 * own language.
	 */
	$bitter = _x( 'on', 'Bitter font: on or off', 'steel' );

	if ( 'off' !== $source_sans_pro || 'off' !== $bitter ) {
		$font_families = array();

		if ( 'off' !== $source_sans_pro )
			$font_families[] = 'Source Sans Pro:300,400,700,300italic,400italic,700italic';

		if ( 'off' !== $bitter )
			$font_families[] = 'Bitter:400,700';

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);
		$fonts_url = add_query_arg( $query_args, "//fonts.googleapis.com/css" );
	}

	return $fonts_url;
}

/**
 * Enqueues scripts and styles for front end.
 *
 * @since Steel 1.0
 *
 * @return void
 */
function steel_scripts_styles() {
	// Adds JavaScript to pages with the comment form to support sites with
	// threaded comments (when in use).
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	// Adds Masonry to handle vertical alignment of footer widgets.
	if ( is_active_sidebar( 'sidebar-1' ) )
		wp_enqueue_script( 'jquery-masonry' );

	// Loads JavaScript file with functionality specific to Steel.
	wp_enqueue_script( 'steel-script', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '2013-07-18', true );

	// Add Open Sans and Bitter fonts, used in the main stylesheet.
	wp_enqueue_style( 'steel-fonts', steel_fonts_url(), array(), null );

	// Add Genericons font, used in the main stylesheet.
	wp_enqueue_style( 'genericons', get_template_directory_uri() . '/fonts/genericons.css', array(), '2.09' );

	// Loads our main stylesheet.
	wp_enqueue_style( 'steel-style', get_stylesheet_uri(), array(), '2013-07-18' );

	// Loads the Internet Explorer specific stylesheet.
	wp_enqueue_style( 'steel-ie', get_template_directory_uri() . '/css/ie.css', array( 'steel-style' ), '2013-07-18' );
	wp_style_add_data( 'steel-ie', 'conditional', 'lt IE 9' );
}
add_action( 'wp_enqueue_scripts', 'steel_scripts_styles' );

/**
 * Creates a nicely formatted and more specific title element text for output
 * in head of document, based on current view.
 *
 * @since Steel 1.0
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function steel_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'steel' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'steel_wp_title', 10, 2 );

/**
 * Registers two widget areas.
 *
 * @since Steel 1.0
 *
 * @return void
 */
function steel_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Main Widget Area', 'steel' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Appears in the footer section of the site.', 'steel' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Secondary Widget Area', 'steel' ),
		'id'            => 'sidebar-2',
		'description'   => __( 'Appears on posts and pages in the sidebar.', 'steel' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'steel_widgets_init' );

if ( ! function_exists( 'steel_paging_nav' ) ) :
/**
 * Displays navigation to next/previous set of posts when applicable.
 *
 * @since Steel 1.0
 *
 * @return void
 */
function steel_paging_nav() {
	global $wp_query;

	// Don't print empty markup if there's only one page.
	if ( $wp_query->max_num_pages < 2 )
		return;
	?>
	<nav class="navigation paging-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Posts navigation', 'steel' ); ?></h1>
		<div class="nav-links">

			<?php if ( get_next_posts_link() ) : ?>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'steel' ) ); ?></div>
			<?php endif; ?>

			<?php if ( get_previous_posts_link() ) : ?>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'steel' ) ); ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'steel_post_nav' ) ) :
/**
 * Displays navigation to next/previous post when applicable.
*
* @since Steel 1.0
*
* @return void
*/
function steel_post_nav() {
	global $post;

	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous )
		return;
	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'steel' ); ?></h1>
		<div class="nav-links">

			<?php previous_post_link( '%link', _x( '<span class="meta-nav">&larr;</span> %title', 'Previous post link', 'steel' ) ); ?>
			<?php next_post_link( '%link', _x( '%title <span class="meta-nav">&rarr;</span>', 'Next post link', 'steel' ) ); ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'steel_entry_meta' ) ) :
/**
 * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
 *
 * Create your own steel_entry_meta() to override in a child theme.
 *
 * @since Steel 1.0
 *
 * @return void
 */
function steel_entry_meta() {
	if ( is_sticky() && is_home() && ! is_paged() )
		echo '<span class="featured-post">' . __( 'Sticky', 'steel' ) . '</span>';

	if ( ! has_post_format( 'link' ) && 'post' == get_post_type() )
		steel_entry_date();

	// Translators: used between list items, there is a space after the comma.
	$categories_list = get_the_category_list( __( ', ', 'steel' ) );
	if ( $categories_list ) {
		echo '<span class="categories-links">' . $categories_list . '</span>';
	}

	// Translators: used between list items, there is a space after the comma.
	$tag_list = get_the_tag_list( '', __( ', ', 'steel' ) );
	if ( $tag_list ) {
		echo '<span class="tags-links">' . $tag_list . '</span>';
	}

	// Post author
	if ( 'post' == get_post_type() ) {
		printf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( __( 'View all posts by %s', 'steel' ), get_the_author() ) ),
			get_the_author()
		);
	}
}
endif;

if ( ! function_exists( 'steel_entry_date' ) ) :
/**
 * Prints HTML with date information for current post.
 *
 * Create your own steel_entry_date() to override in a child theme.
 *
 * @since Steel 1.0
 *
 * @param boolean $echo Whether to echo the date. Default true.
 * @return string The HTML-formatted post date.
 */
function steel_entry_date( $echo = true ) {
	if ( has_post_format( array( 'chat', 'status' ) ) )
		$format_prefix = _x( '%1$s on %2$s', '1: post format name. 2: date', 'steel' );
	else
		$format_prefix = '%2$s';

	$date = sprintf( '<span class="date"><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a></span>',
		esc_url( get_permalink() ),
		esc_attr( sprintf( __( 'Permalink to %s', 'steel' ), the_title_attribute( 'echo=0' ) ) ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( sprintf( $format_prefix, get_post_format_string( get_post_format() ), get_the_date() ) )
	);

	if ( $echo )
		echo $date;

	return $date;
}
endif;

if ( ! function_exists( 'steel_the_attached_image' ) ) :
/**
 * Prints the attached image with a link to the next attached image.
 *
 * @since Steel 1.0
 *
 * @return void
 */
function steel_the_attached_image() {
	$post                = get_post();
	$attachment_size     = apply_filters( 'steel_attachment_size', array( 724, 724 ) );
	$next_attachment_url = wp_get_attachment_url();

	/**
	 * Grab the IDs of all the image attachments in a gallery so we can get the URL
	 * of the next adjacent image in a gallery, or the first image (if we're
	 * looking at the last image in a gallery), or, in a gallery of one, just the
	 * link to that image file.
	 */
	$attachment_ids = get_posts( array(
		'post_parent'    => $post->post_parent,
		'fields'         => 'ids',
		'numberposts'    => -1,
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'order'          => 'ASC',
		'orderby'        => 'menu_order ID'
	) );

	// If there is more than 1 attachment in a gallery...
	if ( count( $attachment_ids ) > 1 ) {
		foreach ( $attachment_ids as $attachment_id ) {
			if ( $attachment_id == $post->ID ) {
				$next_id = current( $attachment_ids );
				break;
			}
		}

		// get the URL of the next image attachment...
		if ( $next_id )
			$next_attachment_url = get_attachment_link( $next_id );

		// or get the URL of the first image attachment.
		else
			$next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
	}

	printf( '<a href="%1$s" title="%2$s" rel="attachment">%3$s</a>',
		esc_url( $next_attachment_url ),
		the_title_attribute( array( 'echo' => false ) ),
		wp_get_attachment_image( $post->ID, $attachment_size )
	);
}
endif;

/**
 * Returns the URL from the post.
 *
 * @uses get_url_in_content() to get the URL in the post meta (if it exists) or
 * the first link found in the post content.
 *
 * Falls back to the post permalink if no URL is found in the post.
 *
 * @since Steel 1.0
 *
 * @return string The Link format URL.
 */
function steel_get_link_url() {
	$content = get_the_content();
	$has_url = get_url_in_content( $content );

	return ( $has_url ) ? $has_url : apply_filters( 'the_permalink', get_permalink() );
}

/**
 * Extends the default WordPress body classes.
 *
 * Adds body classes to denote:
 * 1. Single or multiple authors.
 * 2. Active widgets in the sidebar to change the layout and spacing.
 * 3. When avatars are disabled in discussion settings.
 *
 * @since Steel 1.0
 *
 * @param array $classes A list of existing body class values.
 * @return array The filtered body class list.
 */
function steel_body_class( $classes ) {
	if ( ! is_multi_author() )
		$classes[] = 'Author';

	if ( is_active_sidebar( 'sidebar-2' ) && ! is_attachment() && ! is_404() )
		$classes[] = 'Sidebar';

	if ( is_home() || is_front_page() ) {
		$classes[] = 'Home';
	}
	return $classes;
}
add_filter( 'body_class', 'steel_body_class' );

/**
 * strips the posts_class() in order to format it to my
 * styleguide requirements
 */
function strip_post_class( $arr ) {
	$tmp = array();
	for ( $i = 0; $i < count( $arr ); $i++ ) {
		// post ID
		$arr[0] = null;
		// post type
		$arr[1] = ucwords( $arr[1] );
		// post type duplicated as 'post-[post_type]'
		$arr[2] = null;
		// post status
		$arr[3] = null;
		// post format
		$arr[4] = str_replace( 'format-', '', $arr[4] );
		if ( $arr[$i] === 'hentry' ) {
			$arr[$i] = null;
		}
		if ( substr( $arr[$i], 0, 8 ) === 'category' ) {
			$arr[$i] = str_replace( 'category-', '', $arr[$i] );
		}
		if ( substr( $arr[$i], 0, 3 ) === 'tag' ) {
			$arr[$i] = str_replace( 'tag-', '', $arr[$i] );
		}
		if ( $arr[$i] !== null ) {
			array_push( $tmp, $arr[$i] ); 
		}
	}
	return implode(' ', $tmp);
}

/**
 * Adjusts content_width value for video post formats and attachment templates.
 *
 * @since Steel 1.0
 *
 * @return void
 */
function steel_content_width() {
	global $content_width;

	if ( is_attachment() )
		$content_width = 724;
	elseif ( has_post_format( 'audio' ) )
		$content_width = 484;
}
add_action( 'template_redirect', 'steel_content_width' );

/**
 * Add postMessage support for site title and description for the Customizer.
 *
 * @since Steel 1.0
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 * @return void
 */
function steel_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
}
add_action( 'customize_register', 'steel_customize_register' );

/**
 * Binds JavaScript handlers to make Customizer preview reload changes
 * asynchronously.
 *
 * @since Steel 1.0
 */
function steel_customize_preview_js() {
	wp_enqueue_script( 'steel-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array( 'customize-preview' ), '20130226', true );
}
add_action( 'customize_preview_init', 'steel_customize_preview_js' );

function custom_excerpt_more( $excerpt ) {
	return str_replace( ' [...]', '&hellip;', $excerpt );
}
add_filter( 'wp_trim_excerpt', 'custom_excerpt_more' );

add_action('publish_post', 'create_bitly');

// create bitly url when post is published
function create_bitly( $postID ) {
	$settings = json_decode( file_get_contents( $_SERVER['DOCUMENT_ROOT'] . '/config/settings.json'), true );
	global $wpdb;

	// here we get the permalink to your post
	$url = get_permalink( $postID ); 
	// This is the API call to fetch the shortened URL
	$bitly = 'https://api-ssl.bitly.com/v3/shorten?access_token=' . $settings['bitly'] . '&longUrl=' . urlencode( $url );

	// We are using cURL
	$curl = curl_init();
	curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 5 );
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $curl, CURLOPT_URL, $bitly );
	$results = json_decode( curl_exec( $curl ) );
	curl_close( $curl );

	// adding the short URL to a custom field called bitlyURL
	update_post_meta( $postID, 'bitlyURL', $results->data->url ); 
}

// add the short url to the head
function bitly_shortlink() {
	global $post;
	$url = get_post_meta( $post->ID, 'bitlyURL', true );

	return ( ! empty( $url ) ) ? $url : get_bloginfo( 'url' ) . '?p=' . $post->ID;
}

// filtering the WP function
add_filter('pre_get_shortlink', 'get_bitly_shortlink'); 

function get_bitly_shortlink() {
	global $post;
	$url = get_post_meta( $post->ID, 'bitlyURL', true );

	if( ! empty( $url ) ) {
		return $url;
	} else {
		return null;
	}
}

function shortlink_pretty_url( $url ) {
	$arr = parse_url( $url );
	return $arr['host'] . $arr['path'];
}

function stylesheet_url( $path ) {
	return esc_url( home_url( 'ui/stylesheets/' . $path ) );
}

function image_path_url( $path ) {
	return esc_url( home_url( 'ui/images/' . $path ) );
}

function favicon_url( $path ) {
	return esc_url( home_url( 'ui/images/favicons/' . $path ) );
}

// this is the relative path from WP_CONTENT_DIR to your uploads directory
// update_option( 'upload_path', '../media/uploads' );
// this is the actual path to the image uploads, needs to be absolute;
// update_option( 'upload_url_path', WP_HOME . '/media/uploads' );
if ( get_option( 'upload_path' ) == null || get_option( 'upload_path' ) == 'content/uploads' || get_option( 'upload_path' ) == 'wp-content/uploads' ) {
	update_option( 'upload_path', '../media/uploads' );
	update_option( 'upload_url_path', WP_HOME . '/media/uploads' );
}

function breadcrumbs( $id ) {
	$html = '<ol class="Breacrumbs">';
	$html .= '<li class="Icon solo item"><a href="' . esc_url( home_url() ) . '" title="' . __( 'Return back to the home page', 'twentytwelve' ) . '">&#x2302;</a></li>';
}

function the_breadcrumbs() {

	/* === OPTIONS === */
	$text['home']     = 'Home'; // text for the 'Home' link
	$text['category'] = '%s'; // text for a category page
	$text['search']   = 'Search Results for "%s"'; // text for a search results page
	$text['tag']      = '%s'; // text for a tag page
	$text['author']   = 'Articles by %s'; // text for an author page
	$text['404']      = '404'; // text for the 404 page

	$show_current   = 1; // 1 - show current post/page/category title in breadcrumbs, 0 - don't show
	$show_title     = 1; // 1 - show the title for the links, 0 - don't show
	$before         = '<li class="item" itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><b itemprop="title">'; // tag before the current crumb
	$after          = '</b></li>'; // tag after the current crumb
	/* === END OF OPTIONS === */

	global $post;
	$home_link    = home_url('/');
	$link_before  = '<li class="item" itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breacrumb">';
	$link_after   = '</li>';
	$link_attr    = ' rel="directory" itemprop="url"';
	$link         = $link_before . '<a href="%1$s" ' . $link_attr . '><span itemprop="title">%2$s</span></a>' . $link_after;
	$parent_id    = $parent_id_2 = $post->post_parent;
	$frontpage_id = get_option('page_on_front');

	if ( ! ( is_home() || is_front_page() ) ) {

		echo '<ol class="Breadcrumbs" itemscope itemtype="http://data-vocabulary.org/Breadcrumb">';
		echo '<li class="Icon solo item" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="' . esc_url( home_url() ) . '" rel="home" itemprop="url" title="' . __( 'Return back to the home page', 'twentytwelve' ) . '">&#x2302;</a></li>';

		if ( is_category() ) {
			$this_cat = get_category(get_query_var('cat'), false);
			if ($this_cat->parent != 0) {
				$cats = get_category_parents($this_cat->parent, TRUE, $delimiter);
				if ($show_current == 0) $cats = preg_replace("#^(.+)$delimiter$#", "$1", $cats);
				$cats = str_replace('">', '"><span itemprop="title">', $cats);
				$cats = str_replace('</a>', '</span></a>' . $link_after, $cats);
				$cats = str_replace('<a', $link_before . '<a' . $link_attr . '<span itemprop="title">', $cats);
				if ($show_title == 0) $cats = preg_replace('/ title="(.*?)"/', '', $cats);
				echo $cats;
			}
			if ($show_current == 1) echo $before . sprintf( ucwords( $text['category'] ), single_cat_title('', false)) . $after;

		} elseif ( is_search() ) {
			echo $before . sprintf( ucwords( $text['search'] ), get_search_query()) . $after;

		} elseif ( is_day() ) {
			echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
			echo sprintf($link, get_month_link(get_the_time('Y'),get_the_time('m')), get_the_time('F')) . $delimiter;
			echo $before . get_the_time('d') . $after;

		} elseif ( is_month() ) {
			echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
			echo $before . get_the_time('F') . $after;

		} elseif ( is_year() ) {
			echo $before . get_the_time('Y') . $after;

		} elseif ( is_single() && !is_attachment() ) {
			if ( get_post_type() != 'post' ) {
				$post_type = get_post_type_object(get_post_type());
				$slug = $post_type->rewrite;
				printf($link, $home_link . '/' . $slug['slug'] . '/', $post_type->labels->singular_name);
				if ($show_current == 1) echo $delimiter . $before . get_the_title() . $after;
			} else {
				$cat = get_the_category(); $cat = $cat[0];
				$cats = get_category_parents($cat, TRUE, '');
				$cats = str_replace('">', '"><span itemprop="title">', $cats);
				$cats = str_replace('</a>', '</span></a>' . $link_after, $cats);
				$cats = str_replace('<a', $link_before . '<a' . $link_attr, $cats);
				if ($show_title == 0) $cats = preg_replace('/ title="(.*?)"/', '', $cats);
				echo $cats;
				if ($show_current == 1) echo $before . get_the_title() . $after;
			}

		} elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
			$post_type = get_post_type_object(get_post_type());
			echo $before . $post_type->labels->singular_name . $after;

		} elseif ( is_attachment() ) {
			$parent = get_post($parent_id);
			$cat = get_the_category($parent->ID); 
			if ( count( $cat ) > 0 ) {
				$cat = $cat[0];
				$cats = get_category_parents($cat, TRUE, '');
				$cats = str_replace('">', '"><span itemprop="title">', $cats);
				$cats = str_replace('</a>', '</span></a>' . $link_after, $cats);
				$cats = str_replace('<a', $link_before . '<a' . $link_attr, $cats);
				if ($show_title == 0) $cats = preg_replace('/ title="(.*?)"/', '', $cats);
				echo $cats;
				printf($link, get_permalink($parent), $parent->post_title);
				if ($show_current == 1) echo $delimiter . $before . get_the_title() . $after;
			} else {
				echo '<li class="item" itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><b itemprop="title">' . $parent->post_title . '</b></li>';
			}

		} elseif ( is_page() && !$parent_id ) {
			if ($show_current == 1) echo $before . get_the_title() . $after;

		} elseif ( is_page() && $parent_id ) {
			if ($parent_id != $frontpage_id) {
				$breadcrumbs = array();
				while ($parent_id) {
					$page = get_page($parent_id);
					if ($parent_id != $frontpage_id) {
						$breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
					}
					$parent_id = $page->post_parent;
				}
				$breadcrumbs = array_reverse($breadcrumbs);
				for ($i = 0; $i < count($breadcrumbs); $i++) {
					echo $breadcrumbs[$i];
					if ($i != count($breadcrumbs)-1) echo $delimiter;
				}
			}
			if ($show_current == 1) {
				if ($show_home_link == 1 || ($parent_id_2 != 0 && $parent_id_2 != $frontpage_id)) echo $delimiter;
				echo $before . get_the_title() . $after;
			}

		} elseif ( is_tag() ) {
			echo $before . sprintf( ucwords( $text['tag'] ), single_tag_title('', false)) . $after;

		} elseif ( is_author() ) {
	 		global $author;
			$userdata = get_userdata($author);
			echo $before . sprintf( ucwords( $text['author'] ), $userdata->display_name) . $after;

		} elseif ( is_404() ) {
			echo $before . ucwords( $text['404'] ) . $after;
		}

		if ( get_query_var('paged') ) {
			if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
			echo __('Page') . ' ' . get_query_var('paged');
			if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
		}

		echo '</ol>';

	}
}
//  add subtitle to posts and pages
function the_subtitle( $id ) {
	$subtitle = get_post_meta ( $id, 'subtitle', true );
	if ( ! empty( $subtitle ) ) {
		echo '<h2 class="subtitle">' . $subtitle . '</h2>';
	}
}
function search_url_rewrite() {
	if ( is_search() && ! empty( $_GET['s'] ) ) {
		wp_redirect( home_url( "/search/" ) . urlencode( get_query_var( 's' ) ) );
		exit();
	}   
}
add_action( 'template_redirect', 'search_url_rewrite' );

/**
 * removes the rss feed, comments rss feed, rsd_link, 
 * wlwmanifest_link, & wp_generator
 *
 * i added the rss feed, rsd_link, wlwmanifest_link
 * on the header.php
 */
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
// trying to figure out how to get these 2 back
// in to the header.php
//remove_filter( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
remove_action( 'wp_head', 'locale_stylesheet' );
remove_action( 'wp_head', 'noindex', 1 );
remove_action( 'wp_head', 'wp_print_styles', 8 );
remove_action( 'wp_head', 'wp_print_head_scripts', 9 );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rel_canonical' );
remove_action( 'wp_footer', 'wp_print_footer_scripts', 20 );
// removes the link tag for the wp.me shortlink that gets generated
remove_action( 'wp_head', 'shortlink_wp_head', 10 );
// removing the default shortlink
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 ); 
remove_action( 'wp_print_footer_scripts', '_wp_footer_scripts' );