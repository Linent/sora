<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package YITH_Booking
 */

if ( !function_exists( 'yith_booking_posted_on' ) ) :
    /**
     * Prints HTML with meta information for the current post-date/time.
     */
    function yith_booking_posted_on() {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf(
            $time_string,
            esc_attr( get_the_date( DATE_W3C ) ),
            esc_html( get_the_date() ),
            esc_attr( get_the_modified_date( DATE_W3C ) ),
            esc_html( get_the_modified_date() )
        );

        printf(
            '<span class="posted-on">%1$s<a href="%2$s" rel="bookmark">%3$s</a></span>',
            yith_booking_svg( 'watch', 16 ),
            esc_url( get_permalink() ),
            $time_string
        );

    }
endif;

if ( !function_exists( 'yith_booking_posted_by' ) ) :
    /**
     * Prints HTML with meta information for the current author.
     */
    function yith_booking_posted_by() {
        printf(
            '<span class="byline">%1$s<span class="screen-reader-text">%2$s</span><span class="author vcard"><a class="url fn n" href="%3$s">%4$s</a></span></span>',
            /* translators: 1: SVG icon. 2: post author, only visible to screen readers. 3: author link. */
            yith_booking_svg( 'person', 16 ),
            __( 'Posted by', 'yith-booking' ),
            esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
            esc_html( get_the_author() )
        );

    }
endif;

if ( !function_exists( 'yith_booking_entry_footer' ) ) :
    /**
     * Prints HTML for entry footer
     */
    function yith_booking_entry_footer() {
        do_action( 'yith_booking_entry_footer' );
    }
endif;

if ( !function_exists( 'yith_booking_entry_meta' ) ) :
    /**
     * Prints HTML with meta information for the categories, tags and comments.
     */
    function yith_booking_entry_meta() {
        // Hide category and tag text for pages.
        if ( 'post' === get_post_type() ) {
            yith_booking_posted_by();
            yith_booking_posted_on();

            if ( yith_booking_categorized_blog() ) {

                /* translators: used between list items, there is a space after the comma */
                $categories_list = get_the_category_list( esc_html__( ', ', 'yith-booking' ) );
                if ( $categories_list ) {
                    printf( '<span class="cat-links">%1$s<span class="screen-reader-text">%2$s </span>%3$s</span>',
                            yith_booking_svg( 'archive', 16 ),
                            _x( 'Categories', 'Used before category names.', 'yith-booking' ),
                            $categories_list
                    );
                }
            }

            /* translators: used between list items, there is a space after the comma */
            $tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'yith-booking' ) );
            if ( $tags_list && !is_wp_error( $tags_list ) ) {
                printf( '<span class="tags-links">%1$s<span class="screen-reader-text">%2$s </span>%3$s</span>',
                        yith_booking_svg( 'tag', 16 ),
                        _x( 'Tags', 'Used before tag names.', 'yith-booking' ),
                        $tags_list
                );
            }
        }

        if ( !is_single() && !post_password_required() && ( comments_open() || get_comments_number() ) ) {
            echo '<span class="comments-link">';
            echo yith_booking_svg( 'comment', 16 );
            comments_popup_link(
                sprintf(
                    wp_kses(
                    /* translators: %s: post title */
                        __( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'yith-booking' ),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    get_the_title()
                )
            );
            echo '</span>';
        }

        edit_post_link(
            sprintf(
                wp_kses(
                /* translators: %s: Name of current post. Only visible to screen readers */
                    __( 'Edit <span class="screen-reader-text">%s</span>', 'yith-booking' ),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                get_the_title()
            ),
            '<span class="edit-link">' . yith_booking_svg( 'edit', 16 ),
            '</span>'
        );
    }
endif;

if ( !function_exists( 'yith_booking_post_thumbnail' ) ) :
    /**
     * Displays an optional post thumbnail.
     *
     * Wraps the post thumbnail in an anchor element on index views, or a div
     * element when on single views.
     */
    function yith_booking_post_thumbnail() {
        if ( post_password_required() || is_attachment() || !has_post_thumbnail() ) {
            return;
        }

        if ( is_singular() ) :
            ?>

            <div class="post-thumbnail">
                <?php the_post_thumbnail(); ?>
            </div><!-- .post-thumbnail -->

        <?php else : ?>

            <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
                <?php
                the_post_thumbnail( 'post-thumbnail', array(
                    'alt' => the_title_attribute( array(
                                                      'echo' => false,
                                                  ) ),
                ) );
                ?>
            </a>

        <?php
        endif; // End is_singular().
    }
endif;


if ( !function_exists( 'yith_booking_categorized_blog' ) ) :
    /**
     * Determines whether blog/site has more than one category.
     *
     * Create your own yith_booking_categorized_blog() function to override in a child theme.
     *
     * @return bool True if there is more than one category, false otherwise.
     */
    function yith_booking_categorized_blog() {
        if ( false === ( $all_the_cool_cats = get_transient( 'yith_booking_post_categories' ) ) ) {
            // Create an array of all the categories that are attached to posts.
            $all_the_cool_cats = get_categories( array(
                                                     'fields' => 'ids',
                                                     // We only need to know if there is more than one category.
                                                     'number' => 2,
                                                 ) );

            // Count the number of categories that are attached to the posts.
            $all_the_cool_cats = count( $all_the_cool_cats );

            set_transient( 'yith_booking_post_categories', $all_the_cool_cats );
        }

        if ( $all_the_cool_cats > 1 || is_preview() ) {
            // This blog has more than 1 category so it should return true.
            return true;
        } else {
            // This blog has only 1 category so it should return false.
            return false;
        }
    }
endif;

if ( !function_exists( 'yith_booking_categorized_blog_delete_transient' ) ) :
    /**
     * Delete transient for categories
     *
     * Create your own yith_booking_categorized_blog() function to override in a child theme.
     *
     * @return bool True if there is more than one category, false otherwise.
     */
    function yith_booking_categorized_blog_delete_transient() {
        delete_transient( 'yith_booking_post_categories' );
    }

    add_action( 'create_category', 'yith_booking_categorized_blog_delete_transient' );
    add_action( 'delete_category', 'yith_booking_categorized_blog_delete_transient' );
endif;
