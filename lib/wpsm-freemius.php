<?php
if ( ! function_exists( 'msfs_fs' ) ) {
    // Create a helper function for easy SDK access.
    function msfs_fs($plugin_dir) {
        global $msfs_fs;

        if ( ! isset( $msfs_fs ) ) {
            // Include Freemius SDK.
            require_once $plugin_dir . '/freemius/start.php';

            $msfs_fs = fs_dynamic_init(array(
                'id'                  => '7232',
                'slug'                => 'multilist-subscribe-for-sendy',
                'type'                => 'plugin',
                'public_key'          => 'pk_f1aa43ddeaeeb21797a2b9af85a87',
                'is_premium'          => false,
                'has_addons'          => false,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'slug'           => 'edit.php?post_type=sendyemailtemplates',
                    'first-path'     => 'edit.php?post_type=sendyemailtemplates',
                    'account'        => false,
                ),
            ));
        }

        // Signal that SDK was initiated.
        do_action( 'msfs_fs_loaded' );

        return $msfs_fs;
    }
}