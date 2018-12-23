<?php
date_default_timezone_set('Asia/Novosibirsk');
require 'util/util.php';

$data = prepareGallery($argv[1]);
file_put_contents('gallery.code', $data);