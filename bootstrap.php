<?php
// オートロードを設定するためのファイル
require 'core/ClassLoader.php';
$loader = new ClassLoader();
//ディレクトリを登録
$loader->registerDir(dirname(__FILE__) . '/core');
$loader->registerDir(dirname(__FILE__) . '/models');
$loader->register();
