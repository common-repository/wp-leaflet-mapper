=== WP Leaflet Mapper ===
Contributors: iisys
Donate link: https://www.ii-sys.jp
Tags: leaflet, map, gmap
Requires at least: 4.6
Tested up to: 5.1
Stable tag: 1.0.1
Requires PHP: 7.0.0
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Support for switching from <strong>GMap to Leaflet</strong>. Display a map on the management screen and save LatLng as a custom field at GUI.

== Description ==
Registration support plug-in using Leaflet.
I made it to change from GMap, and to register easily from the management screen with the contents of 1 post 1 map.
Corresponds to OpenStreetMap or Geographical map.
A map is displayed on the management screen (POST, PAGE, custom post), so specify the location and zoom with the GUI operation.
Geocoding is not implemented because there was no practical level service other than Google API.
【日本語】
Leafletを使った登録支援プラグインです。
GMapからの乗り換えで、1投稿1地図のコンテンツで管理画面から簡単に登録するために作りました。
OpenStreetMapまたは地理院地図に対応しています。
管理画面（POST,PAGE,カスタム投稿）に地図が表示されますので、GUI操作で場所とズームを指定します。
Geocodingは、Google API以外で実用レベルのサービスが無かったため、搭載していません。
[日本語の記事](https://www.ii-sys.jp/release/1246)

== Installation ==
1. After extraction, upload the Plugin to your `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Settings => Leaflet-Mapper screen to configure the plugin.
[日本語の記事](https://www.ii-sys.jp/release/1246)

# How to use
1. The map is displayed at the bottom of each post page, so drag the mouse to align the position, and use the wheel to make the zoom magnification nice.
2. Write the short code <strong>[lmap]</strong> where you want to display the map and save it.
[日本語の記事](https://www.ii-sys.jp/release/1246)

== Screenshots ==
1. Plugin general settings
2. Post screen
3. Web content image

== Changelog ==
= 1.0.1 =
* Readme fixes

= 1.0.0 =
* New Release

== FAQ ==
= Is there a limit on the number of impressions? =
There is no particular restriction, but please check the OpenStreetMap or the Geographical Survey Map for details.
= I can not see the details of my neighborhood in OpenStreetMap. =
How about activities it as a mapper of OpenStreetMap?
I just registered.

== Upgrade Notice ==
= 1.0.1 =
* Readme fixes

= 1.0.0 =
* New Release