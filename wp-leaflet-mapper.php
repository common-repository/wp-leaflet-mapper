<?php
/*
Plugin Name: WP Leaflet Mapper
Plugin URI: 
Description: Supports switching from GMap to Leaflet. Display map on post screen and save LatLng as a custom field at GUI.
Version: 1.0.1
Author:sio
Author URI: https://www.ii-sys.jp
Text Domain: wp-leaflet-mapper
Domain Path: /languages/
License: GPL2
Copyright 2018 IISYS

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

load_plugin_textdomain( 'wp-leaflet-mapper', false, basename( dirname( __FILE__ ) ) . '/languages/' );

function add_ilm_meta_box() {
	$option_value = get_option('Leaflet_Mapper_Option');
	$post_type = explode(",",$option_value['ilm_post_type']);
	if(!$post_type) {
    $post_type = array('post','page');
  }
	foreach($post_type as $value){
		add_meta_box( 'leaflet_meta',__('Specify a location','wp-leaflet-mapper'),'ilm_map',$value,'normal','low' );
	}
}

function ilm_map($latlngzoom) {
  if (is_admin()) {
    if(get_post_meta($_GET['post'],'Lat_Long',TRUE)) {
      $latlngzoom = get_post_meta($_GET['post'],'Lat_Long',TRUE);
    } else if (!preg_match("/^[0-9]{1,3}(\.[0-9]{0,20})?,[0-9]{1,3}(\.[0-9]{0,20})?,[0-9]{1,2}$/", $latlngzoom)) {
      $latlngzoom = "33.5561751,131.4470381,14";
    }
    $map_array = explode(",", $latlngzoom);
    $iconurl = plugin_dir_url( __FILE__ ) . 'img/center_icon.png';
    $iconsize = array(62,62);
    $iconanchor = array(31,31);
    display_ilm_map($map_array, $iconurl, $iconsize, $iconanchor);
    echo '<input type="text" id="ilm_default_latlngzoom" name="ilm_default_latlngzoom" value="' . $latlngzoom . '" size="25" readonly />';
  } else {
    global $post;
    $latlngzoom = get_post_meta($post->ID,'Lat_Long',TRUE);
    $map_array = explode(",", $latlngzoom);
    $iconurl = plugin_dir_url( __FILE__ ) . 'img/location_icon.png';
    $iconsize = array(21,32);
    $iconanchor = array(9,32);
    display_ilm_map($map_array, $iconurl, $iconsize, $iconanchor);
  }
}
function display_ilm_map($map_array, $iconurl, $iconsize, $iconanchor) {
	$option_value = get_option('Leaflet_Mapper_Option');
  switch($option_value['ilm_select_maptile']) {
  case 1:
    $map_tile_url = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    $map_copy_html = '<small>&copy;</small> <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> | <a href="https://www.ii-sys.jp/" target="_blank">IISYS</a>';
    break;
  case 2:
    $map_tile_url = 'https://cyberjapandata.gsi.go.jp/xyz/std/{z}/{x}/{y}.png';
    $map_copy_html = '<a href="https://maps.gsi.go.jp/development/ichiran.html" target="_blank">' . __('GSI','wp-leaflet-mapper') . '</a> | <a href="https://www.ii-sys.jp/" target="_blank">IISYS</a>';
  }

  echo <<< EOF
<div id="map_wrap">
  <div id="leaflet_map" style="height:{$option_value['ilm_admin_map_height']}px;width:100%;"></div>
</div>
<script>
  var map = L.map('leaflet_map');
  L.tileLayer('{$map_tile_url}', {
    minZoom: {$option_value['ilm_min_zoom']},
    maxZoom: {$option_value['ilm_max_zoom']},
    attribution: '{$map_copy_html}',
  }).addTo(map);
  map.setView([$map_array[0], $map_array[1]], $map_array[2]);

  var centerIcon = L.icon({
    iconUrl: '{$iconurl}',
    iconSize: [{$iconsize[0]}, {$iconsize[1]}],
    iconAnchor: [{$iconanchor[0]}, {$iconanchor[1]}]
  });
  var marker = L.marker( map.getCenter(), {icon: centerIcon, zIndexOffset: 9999, interactive: false}).addTo(map);
EOF;
  if (is_admin()) {
  echo <<< EOF
  map.on('move', function(e) {
    marker.setLatLng(map.getCenter());
    var latlng_new = map.getCenter();
    var lat_new = Math.round(latlng_new.lat * 10000000) / 10000000;
    var lng_new = Math.round(latlng_new.lng * 10000000) / 10000000;
    var zoom_new = map.getZoom();
    document.getElementById("ilm_default_latlngzoom").value = lat_new + ',' + lng_new + ',' + zoom_new;
  });
</script>
EOF;
  } else {
    echo '</script>';
  }
}

function save_ilm_latlng($post_id) {
  if(!empty($_POST['ilm_default_latlngzoom'])) {
    update_post_meta($post_id, 'Lat_Long', $_POST['ilm_default_latlngzoom'] );
  }
}

function ilm_plugin_setthing_option_menu() {
	add_options_page('Leaflet-Mapper', __('Leaflet-Mapper','wp-leaflet-mapper'), 8, __FILE__, 'ilm_plugin_setting_option');
}

function ilm_plugin_setting_option() {
	if($_POST['action'] == "update") {
		$option_value['ilm_default_latlngzoom'] = $_POST['ilm_default_latlngzoom'];
		$option_value['ilm_admin_map_height'] = $_POST['ilm_admin_map_height'];
		$option_value['ilm_min_zoom'] = $_POST['ilm_min_zoom'];
		$option_value['ilm_max_zoom'] = $_POST['ilm_max_zoom'];
		$option_value['ilm_post_type'] = $_POST['ilm_post_type'];
		$option_value['ilm_select_maptile'] = $_POST['ilm_select_maptile'];
		update_option('Leaflet_Mapper_Option',$option_value);
	}
	$option_value = get_option('Leaflet_Mapper_Option');
	if(!$option_value['ilm_default_latlngzoom']) {
    $option_value['ilm_default_latlngzoom'] = '33.5561751,131.4470381,14';
  }
	if(!$option_value['ilm_admin_map_height']) {
    $option_value['ilm_admin_map_height'] = 250;
  }
	if(!$option_value['ilm_min_zoom']) {
    $option_value['ilm_min_zoom'] = 1;
  }
	if(!$option_value['ilm_max_zoom']) {
    $option_value['ilm_max_zoom'] = 18;
  }
	if(!$option_value['ilm_post_type']) {
    $option_value['ilm_post_type'] = "post,page";
  }
	if(!$option_value['ilm_select_maptile']) {
    $option_value['ilm_select_maptile'] = 1;
  }
  update_option('Leaflet_Mapper_Option',$option_value);
?>
<style>
div.inside li label{
display: inline-block;
  width: 120px;
}
</style>
<div class="metabox-holder has-right-sidebar">
 <div id="post-body">
  <div id="post-body-content">
   <div class="postbox">
    <h3><span><?php _e('Leaflet Mapper Options','wp-leaflet-mapper') ?></span></h3>
    <div class="inside">
      <form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
      <?php wp_nonce_field('update-options'); ?>
      <ul>
        <li>
          <label><?php _e('Default location','wp-leaflet-mapper') ?></label>
          <?php ilm_map($option_value['ilm_default_latlngzoom']); ?>
        </li>
        <li>
          <label><?php _e('Map height','wp-leaflet-mapper') ?></label>
          <input type="text" name="ilm_admin_map_height" value="<?php echo $option_value['ilm_admin_map_height']; ?>" />
          <span class="description">px</span>
        </li>
        <li>
          <label><?php _e('Minimum zoom level','wp-leaflet-mapper') ?></label>
          <input type="text" name="ilm_min_zoom" value="<?php echo $option_value['ilm_min_zoom']; ?>" />
        </li>
        <li>
          <label><?php _e('Maximum zoom level','wp-leaflet-mapper') ?></label>
          <input type="text" name="ilm_max_zoom" value="<?php echo $option_value['ilm_max_zoom']; ?>" />
        </li>
        <li>
          <label><?php _e('Post Type','wp-leaflet-mapper') ?></label>
          <input type="text" name="ilm_post_type" value="<?php echo $option_value['ilm_post_type']; ?>" />
          <span class="description"><?php _e('Describe the post type to display the map on the admin panel. More than one can be specified by comma (,).','wp-leaflet-mapper') ?></span>
        </li>
      <?php
        switch($option_value['ilm_select_maptile']) {
        case 1:
          $checked0 = 'checked';
          $checked1 = '';
          break;
        case 2:
          $checked0 = '';
          $checked1 = 'checked';
        }
      ?>
        <li>
          <label><?php _e('Select a map tile','wp-leaflet-mapper') ?></label>
          <input type="radio" name="ilm_select_maptile" value=1 <?php echo $checked0 ?> />
          <span class="description">Open Street Map</span>
          <input type="radio" name="ilm_select_maptile" value=2 <?php echo $checked1 ?> />
          <span class="description"><?php _e('GSI(Japan Map)','wp-leaflet-mapper') ?></span>
        </li>
      </ul>
      <input type="hidden" name="action" value="update" />
      <input type="hidden" name="page_options" value="new_option_name,some_other_option,option_etc" />
      <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
      </p>
      </form>
    </div>
   </div>
  </div>
 </div>
</div>
<?php
}

function ilm_header_add() {
  echo <<<EOF
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css" type='text/css' media='' />
<script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js"></script>
EOF;
}
function ilm_view() {
  ilm_map();
}

add_action('admin_head', 'ilm_header_add');
add_action('wp_head', 'ilm_header_add');

add_shortcode('lmap', 'ilm_view');

add_action('add_meta_boxes', 'add_ilm_meta_box');

add_action('save_post', 'save_ilm_latlng');

add_action('admin_menu', 'ilm_plugin_setthing_option_menu');
?>
