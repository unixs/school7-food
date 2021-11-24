<?php

date_default_timezone_set('Europe/Moscow');
setlocale(LC_ALL, 'ru_RU.UTF-8');

const SRC_SS_PATH = "/home/alexander/Develop/tmp/src/ss";
const SRC_SM_PATH = "/home/alexander/Develop/tmp/src/sm";
const DST_PATH = "/home/alexander/Develop/tmp/dst";
const DST_FILETYPE = "xlsx";
const LASTINDEX_FILENAME = "LASTINDEX";
const MAX_LASTINDEX = 4;
const CLEANUP_REGEXP = "^\d{4}-\d{2}-\d{2}-";


function checkWebAccess() {
  $cli = getenv("ACCESS_BY_CLI");

  if (!$cli) {
    print ":P";

    die();
  }
}

function checkNeedWork() {
  return null;
}

function loadConfig() {
  $config_path = realpath(__DIR__ ."/food.ini");
  $result = parse_ini_file($config_path, true);

  if ($result === false) {
    trigger_error("Unable read config file by path [". $config_path ."]", E_USER_ERROR);
  }

  foreach ($result['main'] as $key =>$value) {
    define($key, $value);
  }

  define('SKIP', $result['skip']['periods']);
}

function getDstFilename(string $suffix): string {
  return date("Y-m-d") ."-". $suffix .".". DST_FILETYPE;
}

function nextIndex($lastIndex): int {
  return $lastIndex == MAX_LASTINDEX ? 1 : $lastIndex + 1;
}

function getFullLastIndexPath(string $path): string {
  static $is_checked = false;

  $full_path = realpath($path ."/". LASTINDEX_FILENAME);

  if ($is_checked) {
    goto ret;
  }

  if (!$full_path) {
    trigger_error("Wrong or unexpected LASTINDEX path with path - [". $path ."] & filename - [". LASTINDEX_FILENAME ."]", E_USER_ERROR);
  }

  if (!is_readable($full_path)) {
    trigger_error("Unable read LASTINDEX file by path [". $full_path ."]", E_USER_ERROR);
  }

  if (!is_writable($full_path)) {
    trigger_error("Unable write LASTINDEX file by path [". $full_path ."]", E_USER_ERROR);
  }

  $is_checked = true;

  ret:

  return $full_path;
}

function loadLastIndex(string $path): int {
  $full_path = getFullLastIndexPath($path);

  return (int) file_get_contents($full_path);
}

function copyFile(string $from, string $to, string $suffix) {

  $src_path = $from .".". DST_FILETYPE;
  $src = realpath($src_path);

  if (!$src) {
    trigger_error("Unable to open src file [". $src_path ."]", E_USER_ERROR);
  }

  $dst = realpath($to);

  if (!$dst) {
    trigger_error("Unable to find dst dir [". $to ."]", E_USER_ERROR);
  }

  $dst_file = $dst ."/". getDstFilename($suffix);

  print "COPY FILE FROM: ". $src ." TO: ". $dst_file . PHP_EOL;
  copy($src, $dst_file);
}

function copyFiles(string $src_ss, string $src_sm, string $dst, int $lastIndex) {
  $next_index = nextIndex($lastIndex);

  // ss
  copyFile($src_ss ."/". $next_index, $dst, "ss");

  // sm
  copyFile($src_sm ."/". $next_index, $dst, "sm");
}

function updateLastIndex(string $path, int $lastIndex = MAX_LASTINDEX): bool {
  $full_path = getFullLastIndexPath($path);
  $new_index = nextIndex($lastIndex);
  $result = file_put_contents($full_path, $new_index);

  if ($result === false) {
    trigger_error("Unable write LASTINDEX by path [". $full_path ."]", E_USER_ERROR);
  }

  if ($result == 0) {
    trigger_error("Unexpected result while write LASTINDEX by path [". $full_path ."]", E_USER_ERROR);
  }

  return true;
}

function writeFrontendData(string $path) {

}

checkWebAccess();
loadConfig();
checkNeedWork();
$lastIndex = loadLastIndex(DST_PATH);
copyFiles(SRC_SS_PATH, SRC_SM_PATH, DST_PATH, $lastIndex);
updateLastIndex(DST_PATH, $lastIndex);
writeFrontendData(DST_PATH);


