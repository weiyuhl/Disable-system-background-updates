<?php
/*
Plugin Name: Disable-WP-system-updates（禁止后台更新）
Description: 用于禁用插件、主题或核心自动更新
Version: 1.0
Author: 和离
Author URI: https://heliq.cn
License: GPLv3
*/
defined('ABSPATH') or die('Unauthorized Access'); // 如果常量 ABSPATH 未定义，则终止执行并显示 "Unauthorized Access" 的错误信息

if (!class_exists('Da_updates')) {

    class Da_updates
    {
        // 设置插件相关的目录路径
        public $dau_dir = WP_CONTENT_DIR . '/uploads/Disable-system-background-updates';

        public function register()
        {
            // 添加菜单页面和过滤器
            add_action('admin_menu', array($this, 'add_admin_pages'));
            add_filter('clean_url', [$this, 'add_async_attribute'], 11, 1);
            add_filter("plugin_row_meta", [$this, "meta"], 10, 2);
            add_filter('plugin_action_links', [$this, 'ads_action_links'], 10, 5);
        }

        public function add_admin_pages()
        {
            // 添加设置页面，并在特定条件下移除更新页面
            add_submenu_page('tools.php', '禁止更新设置', '禁止更新设置', 'manage_options', 'Disable-system-background-updates', [$this, 'view']);
            if (file_exists("$this->dau_dir/disable-all.php") || file_exists("$this->dau_dir/hide-notification.php") || file_exists("$this->dau_dir/disable-core.php") && file_exists("$this->dau_dir/disable-theme.php") && file_exists("$this->dau_dir/disable-plugin.php")) {
                remove_submenu_page('index.php', 'update-core.php');
            }
        }

        public function view()
        {
            // 显示设置页面的内容
            require_once plugin_dir_path(__FILE__) . 'Set-up/Set-up.php';
        }

        public function activate()
        {
            // 激活插件时刷新重写规则
            flush_rewrite_rules();
        }

        public function deactivate()
        {
            // 停用插件时刷新重写规则
            flush_rewrite_rules();
        }

        public function add_async_attribute($url)
        {
            // 给URL添加异步加载属性
            return str_replace("'", "' async='async", $url);
        }

        public function meta($links = [], $file = "")
        {
            if (strpos($file, "Disable-system-background-updates/Disable-system-background-updates.php") !== false) {
                $new_link = [
                    "donation" => '<a href="https://github.com/hekailiu-2512/Disable-system-background-updates" target="_blank">GITHUB</a>'
                ];

                // 添加一个新的链接到插件行元数据中
                $links = array_merge($links, $new_link);
            }

            return $links;
        }

        public function ads_action_links($links, $plugin_file)
        {
            $plugin = plugin_basename(__FILE__);

            if ($plugin === $plugin_file) {
                $ads_links = [
                    '<a href="' . admin_url('tools.php?page=Disable-system-background-updates') . '">设置</a>',
                ];

                // 添加一个新的链接到插件操作链接中
                $links = array_merge($ads_links, $links);
            }
            return $links;
        }

    }

    if (class_exists('Da_updates')) {
        // 实例化 Da_updates 类的对象，并注册插件的功能
        $disable_auto_updates = new Da_updates();
        $disable_auto_updates->register();
        register_activation_hook(__FILE__, [$disable_auto_updates, 'activate']);
        register_deactivation_hook(__FILE__, [$disable_auto_updates, 'deactivate']);
    } else {
        die('Plugin internal code conflict'); // 如果 Da_updates 类不存在，则终止执行并显示错误信息
    }

    $dau_services = ['disable-all', 'disable-plugin', 'disable-theme', 'disable-core', 'disable-admin-notice', 'hide-notification'];

    foreach ($dau_services as $service) {
        if (file_exists("$disable_auto_updates->dau_dir/$service.php")) {
            include_once "$disable_auto_updates->dau_dir/$service.php";
        }
    }

}