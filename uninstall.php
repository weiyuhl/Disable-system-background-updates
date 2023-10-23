<?php
defined("WP_UNINSTALL_PLUGIN") or die();

array_map('unlink', glob(WP_CONTENT_DIR . "/uploads/Disable-system-background-updates/*"));
unlink(WP_CONTENT_DIR . "/uploads/Disable-system-background-updates");