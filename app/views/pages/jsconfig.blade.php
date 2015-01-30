<?php
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 30*24*3600)); ?>
var APP_URL = "{{ Config::get('app.js_url') }}";
var sortable_count = 15;
var allowed = ['.jpg', '.jpeg', '.gif', '.png', '.doc', '.docx', '.pdf', '.odt', '.zip'];
var allowed_img = ['.jpg', '.jpeg', '.gif', '.png'];

var autosave_yes_button_label = '{{{sys_settings('autosave_yes_button_label')}}}';
var autosave_no_button_label = '{{{sys_settings('autosave_no_button')}}}';