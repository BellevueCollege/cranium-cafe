<?php
/*
Plugin Name: Cranium Cafe Cards
Plugin URI:  https://github.com/bellevuecollege/cranium-cafe-cards/
Description: Displays 'contact' cards for Cranium Cafe using a shortcode. Based on code from Bemidji State University.
Version:     v0.0.0.1
Author:      Bellevue College ITS & Bemidji State University
Author URI:  https://www.bellevuecollege.edu
Text Domain: bccranium
*/


/**********************************************************************************
***********************************************************************************
Copyright (c) 2016
Organization & Copyright Holder: Bemidji State University, Bemidji MN USA
Author: Kody Hagen <kjhagen@bemidjistate.edu>
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of the <organization> nor the
      names of its contributors may be used to endorse or promote products
      derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
***********************************************************************************
**********************************************************************************/

	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit;
	
	add_action( 'init', array( 'BCCranium_Cranium_Cafe', 'init' ));

	class BCCranium_Cranium_Cafe {

		/**
		* Init the class
		*/
		public static function init() {
			$class = __CLASS__;
			if ( empty( $GLOBALS[ $class ] ) ) {
				$GLOBALS[ $class ] = new $class;
			}
		}





		/**
		* Init function to register all used hooks
		*
		* @since 1.8
		* @return BCCranium_Cranium_Cafe
		*/
		public function __construct() {
			// global $bccranium_custom_admin;
			// $this->bccranium = $bccranium_custom_admin;
			// if (method_exists($this->bccranium, 'bccranium_get_option')) $this->bccranium_cranium_cafe = $this->bccranium->bccranium_get_option('bccranium_cranium_cafe');
			
			// if (!empty($this->bccranium_cranium_cafe)) {
				add_shortcode( 'craniumcafe-user', 					array($this, 'add_cranium_cafe_user'));
				add_shortcode( 'craniumcafe-group', 				array($this, 'add_cranium_cafe_group'));
				add_action ( 'init', 								array($this, 'shortcode_ui_shortcode_register_user'), 99);
				add_action ( 'init', 								array($this, 'shortcode_ui_shortcode_register_group'), 99);
			// }
		}




		/**
		* Function to check if the Shortcake (shortcode UI) Plugin is installed and activated.
		*
		* @since 2.0.1
		* @return bool
		*/
		function check_dependencies() {
			//Only run if the shortcode-ui plugin is installed
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );			
			if (is_plugin_active("shortcode-ui/shortcode-ui.php")) {
				return true;
			} else {
				return false;
			}
		}





		/**
		* Function to register the cranium-cafe-shortcode
		*
		* @since 1.8
		* @return string
		*/
		function add_cranium_cafe_user( $atts ){
			//These are the defaults to use if they are not specified in the shortcode
	 		$args = shortcode_atts(
				array(
					'username' => ''
				), $atts );

			$cranium_cafe_code = "";
			if (array_key_exists('username', $args)) {
				$this->username = $args["username"];

				$cranium_cafe_code = '
				<div id="cafeUser">
				<a class="cafe-card" href="https://my.craniumcafe.com/'.$this->username.'" data-username="'.$this->username.'">Chat using Cranium Cafe</a>
				</div>
				';
				// call the function that adds the CraniumCafe.init script below the external-cafe.js
				add_action( 'wp_footer', 					array($this, 'bccranium_cranium_cafe_user_init_footer_js' ), 100 );
				
				return $cranium_cafe_code;
			}
			return; 
		}





		/**
		* Function to register the cranium-cafe-shortcode
		*
		* @since 1.8
		* @return string
		*/
		function add_cranium_cafe_group( $atts ){
			//These are the defaults to use if they are not specified in the shortcode
	 		$args = shortcode_atts(
				array(
					'apiid' => '',
					'divid' => 'cafeUsers',
				), $atts );

			$cranium_cafe_code = "";
			if (array_key_exists('apiid', $args)) {
				$this->apiid = $args["apiid"];
				$this->divid = $args["divid"];

				$cranium_cafe_code = '
				<div id="cafeUsers">
				</div>
				';
				// call the function that adds the CraniumCafe.init script below the external-cafe.js
				add_action( 'wp_footer', 					array($this, 'bccranium_cranium_cafe_group_init_footer_js' ), 100 );
				
				$this->bccranium_cranium_cafe_group_external_js();
				return $cranium_cafe_code;
			}
			return; 
		}





		/**
		* Function to register the cranium-cafe-group-shortcode with the shortcode-ui plugin
		*
		* @since 1.8
		* @return string
		*/
		function shortcode_ui_shortcode_register_group() {
			$shortcode_ui = $this->check_dependencies();
			if ($shortcode_ui) {
				shortcode_ui_register_for_shortcode (
		        'craniumcafe-group',
			        array(
			            // Display label. String. Required.
			            'label' => 'CraniumCafe Groups',
			            // Icon/image for shortcode. Optional. src or dashicons-$icon. Defaults to carrot.
			            'listItemImage' => '<img src="' . plugin_dir_url( __FILE__ ) . 'images/cranium-cafe-logo-groups.jpg" >',
			            // Available shortcode attributes and default values. Required. Array.
			            // Attribute model expects 'attr', 'type' and 'label'
			            // Supported field types: text, checkbox, textarea, radio, select, email, url, number, and date.
			            'attrs' => array(
			                array(
			                    'label' => 'For a CraniumCafe Group, please enter the CraniumCafe Group ID',
			                    'attr'  => 'apiid',
			                    'type'  => 'number'
			                ),
			            ),
			        )
			    );
			}
		}



		/**
		* Function to register the cranium-cafe-user-shortcode with the shortcode-ui plugin
		*
		* @since 1.8
		* @return string
		*/
		function shortcode_ui_shortcode_register_user() {
			$shortcode_ui = $this->check_dependencies();
			if ($shortcode_ui) {
				shortcode_ui_register_for_shortcode (
		        'craniumcafe-user',
			        array(
			            // Display label. String. Required.
			            'label' => 'CraniumCafe Users',
			            // Icon/image for shortcode. Optional. src or dashicons-$icon. Defaults to carrot.
			            'listItemImage' => '<img src="' . plugin_dir_url( __FILE__ ) . 'images/cranium-cafe-logo-users.jpg" >',
			            // Available shortcode attributes and default values. Required. Array.
			            // Attribute model expects 'attr', 'type' and 'label'
			            // Supported field types: text, checkbox, textarea, radio, select, email, url, number, and date.
			            'attrs' => array(
			                array(
			                    'label' => 'For a CraniumCafe User Card, please enter your CraniumCafe Username.',
			                    'attr'  => 'username',
			                    'type'  => 'text'
			                ),
			                
			            ),
			        )
			    );
			}
		}




		/**
		* Function to echo the card init js with the appid and divid in it 
		*
		* @since 1.8
		* @return string
		*/
		function bccranium_cranium_cafe_group_init_footer_js() {
			echo '
			<script type="text/javascript" charset="utf-8">
			    CraniumCafe.init({
			        apiId: "'.$this->apiid.'", // App ID from Cranium Cafe
			        divId: "'.$this->divid.'"  // HTML element ID to inject user list
			    });
			</script>
			';
		}





		/**
		* Function to echo the card init js with the appid and divid in it 
		*
		* @since 1.8
		* @return string
		*/
		function bccranium_cranium_cafe_user_init_footer_js() {
			echo '
			<script type="text/javascript">!function(d, s, id) { var js, cjs = d.getElementsByTagName(s)[0]; if (!d.getElementById(id)) { js = d.createElement(s); js.id = id; js.src = "//platform.craniumcafe.com/cafe-card.js"; cjs.parentNode.insertBefore(js, cjs); } }(document, "script", "craniumcafe-card-kit");</script>
			';
		}





		/**
		* Enqueue the cranium cafe external script
		*
		* @since 1.8
		* @uses wp_enqueue_script
		*/
		function bccranium_cranium_cafe_group_external_js() {
		    wp_enqueue_script('bccranium-cranium-cafe', '//platform.craniumcafe.com/external-cafe.js', array(), NULL, NULL );
		}



		
	} // end class

