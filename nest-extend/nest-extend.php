<?php

/*
Plugin Name: Nest Extend
Description: Nest Extend provides a wrapper around the Nest API.
Version: 0.1.0
Author: Brandon Eckenrode
Author URI: https://github.com/beckenrode/
License: GPLv2 or later
Text Domain: wordpress
*/

class NestExtend
{
    private $nest_extend_options;
    private $nest_service_endpoint_client_id;
    private $nest_service_endpoint_client_secret;

    public function __construct()
    {
        add_action('admin_menu', array($this, 'nest_extend_add_plugin_page'));
        add_action('admin_init', array($this, 'nest_extend_page_init'));
        add_action('wp_ajax_nest_extend', array($this, 'nest_extend_request'));
        add_action('wp_ajax_nopriv_nest_extend', array($this, 'nest_extend_request'));

        /** Product ID **/
        $this->nest_service_endpoint_client_id = '';
        /** Secret **/
        $this->nest_service_endpoint_client_secret = '';

        $this->nest_service_endpoint_rest = 'https://developer-api.nest.com/%s?auth=%s';
        $this->nest_service_endpoint_code = 'https://home.nest.com/login/oauth2?client_id=%s&state=%s';
        $this->nest_service_endpoint_token = 'https://api.home.nest.com/oauth2/access_token?code=%s&client_id=%s&client_secret=%s&grant_type=authorization_code';
    }

    public function nest_extend_add_plugin_page()
    {
        $load_hook = add_options_page('Nest Extend', 'Nest Extend', 'manage_options', 'nest-extend', array($this, 'nest_extend_create_admin_page'));
        add_action('load-' . $load_hook, array($this, 'nest_extend_plugin_page_update'));
    }

    public function nest_extend_plugin_page_update()
    {
        $plugin_page = isset($_GET['page']) ? $_GET['page'] : false;
        $plugin_page_updated = isset($_GET['settings-updated']) ? $_GET['settings-updated'] : false;

        if ($plugin_page == 'nest-extend' && $plugin_page_updated) {
            $this->nest_extend_options = get_option('nest_extend_settings');

            if (empty($this->nest_extend_options['txt_pincode'])) {
                return;
            }

            $this->nest_extend_exchange_auth_code();
        }
    }

    public function nest_extend_request()
    {
        $this->nest_extend_options = get_option('nest_extend_settings');
        $nest_extend_method = isset($_POST['method']) ? $_POST['method'] : false;

        if ($nest_extend_method) {
            $response = wp_remote_get(sprintf($this->nest_service_endpoint_rest, 'devices', $this->nest_extend_options['access_token']));

            if (is_wp_error($response)) {
                wp_send_json_error($response->get_error_message());
            } else {
                wp_send_json_success(json_decode($response['body']));
            }

        }

        wp_send_json_error('No method specified');
    }

    public function nest_extend_exchange_auth_code()
    {

        $service_endpoint = sprintf($this->nest_service_endpoint_token, $this->nest_extend_options['txt_pincode'], $this->nest_service_endpoint_client_id, $this->nest_service_endpoint_client_secret);

        $args = array();
        $response = wp_remote_post($service_endpoint, $args);

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
        } else {
            $response['body'] = json_decode($response['body']);
            if (isset($response['body']['access_token'])) {
                foreach ($response['body'] as $rKey => $rVal) {
                    $this->nest_extend_options[$rKey] = $rVal;
                }
                update_option('nest_extend_settings', $this->nest_extend_options);
            } else {
                $error_message = 'error retrieving access token';
            }
        }
    }

    public function nest_extend_page_init()
    {
        register_setting('nest_extend_option_group', 'nest_extend_settings', array($this, 'nest_extend_sanitize'));
        add_settings_section('nest_extend_setting_section', 'Settings', array($this, 'nest_extend_section_info'), 'nest-extend-admin');
        add_settings_field('btn_connect_with_nest', 'Connect With Nest:', array($this, 'btn_connect_with_nest_callback'), 'nest-extend-admin', 'nest_extend_setting_section');
        add_settings_field('txt_pincode', 'Pincode:', array($this, 'txt_pincode_callback'), 'nest-extend-admin', 'nest_extend_setting_section');
        add_settings_field('txt_access_token', null, array($this, 'noop'), 'nest-extend-admin', 'nest_extend_setting_section');
    }

    public function nest_extend_sanitize($input)
    {

        foreach ($input as $iKey => $iVal) {
            $input[$iKey] = sanitize_text_field($input[$iKey]);
        }

        return $input;
    }

    public function nest_extend_create_admin_page()
    {
        $this->nest_extend_options = get_option('nest_extend_settings');
        require('views/' . __FUNCTION__ . '.php');
    }

    public function nest_extend_section_info()
    {
        require('views/' . __FUNCTION__ . '.php');
    }

    public function btn_connect_with_nest_callback()
    {
        require('views/' . __FUNCTION__ . '.php');
    }

    public function txt_pincode_callback()
    {
        require('views/' . __FUNCTION__ . '.php');
    }

    public function noop()
    {
        return;
    }

}

$nest_extend = new NestExtend();