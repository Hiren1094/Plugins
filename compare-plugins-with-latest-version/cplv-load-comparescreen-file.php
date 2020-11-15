<?php defined('ABSPATH') || exit; ?>
<?php
if (!defined('CPLV_VERSION')) {
    exit;
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
       <?php    
        wp_enqueue_style( 'bootstrap-min-css', self::pfcv_retry_plugins_path('css/bootstrap.min.css'), array(), CPLV_VERSION, 'all' );
        wp_enqueue_style( 'load-compare-css', self::pfcv_retry_plugins_path('css/load-compare-screen.css'), array(), CPLV_VERSION, 'all' );
        wp_enqueue_style( 'bv-main-css', self::pfcv_retry_plugins_path('css/all.css'), array(), CPLV_VERSION, 'all' );
        wp_enqueue_script('bootstrap-min-js', self::pfcv_retry_plugins_path('js/bootstrap.min.js'), array('jquery'));
        wp_enqueue_script('compare-screen-js', self::pfcv_retry_plugins_path('js/compare-screen.js'), array('jquery'));

        wp_head();
        
        ?>
       
       
        <?php

         ?>
    </head>
    <body>

        <?php
        if (!is_user_logged_in()) {

            wp_die("You can't access this page");
        }

        $pfcv_absp_path = ABSPATH;
        require_once($pfcv_absp_path . 'wp-admin/includes/plugin.php' );
        require_once($pfcv_absp_path . 'wp-admin/includes/file.php' );
        require_once($pfcv_absp_path . 'wp-admin/includes/misc.php' );
        require_once(CPLV_CURRENT_PLUGIN_DIR . 'cplv-load-common-function.php' );
        require_once (CPLV_CURRENT_PLUGIN_DIR . 'lib/cplv.class.Diff.php');


        $pfcv_plugin = '';
        if (sanitize_text_field($_GET['pfcvplugin'])) {

            $pfcv_plugin = wp_unslash(sanitize_text_field($_GET['pfcvplugin']));
        }

        $pfcv_files = !empty(sanitize_text_field($_GET['pfcvfile'])) ? sanitize_text_field($_GET['pfcvfile']) : '';
        $pfcv_get_plugins = get_plugin_files($pfcv_plugin);

        $editable_extensions = pfcv_get_plugin_file_editable_extensions($pfcv_plugin);
        $plugin_editable_files = array();
        foreach ($pfcv_get_plugins as $plugin_file) {
            if (preg_match('/\.([^.]+)$/', $plugin_file, $matches) && in_array($matches[1], $editable_extensions)) {
                $plugin_editable_files[] = $plugin_file;
            }
        }
        if (!empty($plugin_editable_files)) {
            ?>

            <div class="header">
                <a href="https://www.brainvire.com/" target="_blank"><img src="<?php echo CPLV_PLUGIN_URL . "images/bv_logo.png" ?>" alt="logo"></a>
                <a class="modal-btn" data-toggle="modal" data-target="#myModal" title="File Information"><i class="fa fa-info-circle"></i></a>
            </div>

            <div id="container-fluid">
                <div class="row">
                    <div class="col-2 collapse show d-md-flex bg-light min-vh-100" id="sidebar">    
                        <ul class="nav flex-column flex-nowrap" role="tree" aria-labelledby="plugin-files-label">
                            <li class="nav-item" role="treeitem" tabindex="-1" aria-expanded="true" aria-level="1" aria-posinset="1" aria-setsize="1">
                                <ul role="group">
                                    <?php pfcv_print_plugin_file_tree(wp_make_plugin_file_tree($plugin_editable_files)); ?>
                                </ul>
                        </ul>
                    </div>


                    <div id="comparescreen" class="col-lg-10 px-0">
                        <?php
                        $extractfolder = CPLV_CURRENT_PLUGIN_DIR . 'extract';
                        $latestfilearr = $extractfolder . '/' . $pfcv_files;
                        $currentfilearr = CPLV_PLUGIN_DIR . $pfcv_files;
                        $nofilefound = CPLV_CURRENT_PLUGIN_DIR . 'cplv-file-not-found.txt';
                        $getfileMTime = @filemtime($currentfilearr);
                        $getfileMTime = date('l jS \of F Y h:i:s A', $getfileMTime);
                        
                        if (!is_dir($extractfolder)) { ?>

                            <div class="alert alert-danger alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">x</a>
                                <strong>Error:</strong> Unable to create directory <?php echo str_replace('\\', '/', $extractfolder); ?>
                            </div> 

                            <?php
                        }

                        if (file_exists($currentfilearr)) {

                            $get_currentfilearr = $currentfilearr;
                        } else {

                            $get_currentfilearr = $nofilefound;
                        }

                        if (file_exists($latestfilearr)) {

                            $get_latestfilearr = $latestfilearr;
                        } else {

                            $get_latestfilearr = $nofilefound;
                        }

                        echo PfcvDiff::toTable(PfcvDiff::compareFiles($get_currentfilearr, $get_latestfilearr));
                        ?>
                    </div>
                </div>
            </div>    
            <?php
        } else {

            wp_die("Sorry, that file cannot be edited.");
        }
        ?>

        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog modal-lg">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="list">
                            <?php
                            echo "<h4 class='change file'>Last Changed: " . $getfileMTime . '</h4>';
                            echo "<h4 class='file_name file'>File Name: " . str_replace('\\', '/', CPLV_PLUGIN_DIR . $pfcv_files) . '</h4>';
                            /* echo "<h4 class='file marginbottomzero'>Total Files In The Both Version: Current Version (" . pfcv_get_totalnumber_of_file($pfcv_plugin) . ") And Latest Version (" . pfcv_get_totalnumber_of_file(CPLV_TEMP_FOLDER . $pfcv_plugin) . ")</h4>"; */
                            echo "<span class='file notecss'><strong>Note:</strong> We have consider only this (" . implode(', ', pfcv_get_plugin_file_editable_extensions($pfcv_plugin)) . ") extension of files</span>"
                            ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </body>
    <?php wp_footer();?>
</html>