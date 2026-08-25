<?php
/**
 * The main template file
 *
 * This is the entry point for the React SPA.
 * All routing is handled frontend-side by React Router.
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/public/zenis/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/public/zenis/css/animate.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/public/zenis/css/mobile_menu.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/public/zenis/css/nice-select.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/public/zenis/css/scroll_button.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/public/zenis/css/slick.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/public/zenis/css/venobox.min.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/public/zenis/css/select2.min.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/public/zenis/css/jquery.pwstabs.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/public/zenis/css/range_slider.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/public/zenis/css/multiple-image-video.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/public/zenis/css/animated_barfiller.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/public/zenis/css/custom_spacing.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/public/zenis/css/style.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/public/zenis/css/responsive.css">

    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    
    <!-- React Root -->
    <div id="root"></div>

    <!-- Zenis Core JS -->
    <script src="<?php echo get_template_directory_uri(); ?>/public/zenis/js/jquery-3.7.1.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/public/zenis/js/bootstrap.bundle.min.js"></script>
    <!-- Zenis Plugins JS -->
    <script src="<?php echo get_template_directory_uri(); ?>/public/zenis/js/jquery.waypoints.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/public/zenis/js/jquery.countup.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/public/zenis/js/jquery.nice-select.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/public/zenis/js/select2.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/public/zenis/js/simplyCountdown.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/public/zenis/js/slick.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/public/zenis/js/venobox.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/public/zenis/js/wow.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/public/zenis/js/jquery.marquee.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/public/zenis/js/jquery.pwstabs.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/public/zenis/js/scroll_button.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/public/zenis/js/jquery.youtube-background.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/public/zenis/js/range_slider.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/public/zenis/js/sticky_sidebar.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/public/zenis/js/multiple-image-video.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/public/zenis/js/animated_barfiller.js"></script>

    <?php wp_footer(); ?>
</body>
</html>
