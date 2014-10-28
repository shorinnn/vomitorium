<?php
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 30*24*3600)); ?>
var APP_URL = "{{ Config::get('app.url') }}";
var sortable_count = 15;
var allowed = ['.jpg', '.jpeg', '.gif', '.png', '.doc', '.docx', '.pdf', '.odt', '.zip'];
var allowed_img = ['.jpg', '.jpeg', '.gif', '.png'];