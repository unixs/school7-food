<?php

const LASTINDEX_FILENAME = "LASTINDEX";
const SS_FLAG = 'ss';
const SM_FLAG = 'sm';
const FILE_REGEXP = "/^\d{4}-\d{2}-\d{2}-(". SS_FLAG ."|". SM_FLAG .")/";
const TIMEZONE = "Europe/Moscow";



function checkAccess() {
  $cli = getenv("ACCESS_BY_CLI");

  if (!$cli) {
    print ":P\n";

    die();
  }
}

function checkNeedWork(): bool {
  $today = date_create("now");
  date_time_set($today, 0, 0, 0);

  foreach (SKIP as $row) {
    $items = explode("|", $row);

    foreach ($items as &$item) {
      $item = date_create($item);
    }

    $size = sizeof($items);

    if ($size == 0) {
      continue;
    }

    if ($size == 1) {
      if ($today == $items[0]) {
        goto skip;
      }
    }
    else {
      if ($today <= $items[1] && $today >= $items[0]) {
        goto skip;
      }
    }
  }

  return true;

  skip:

  trigger_error("Work was skipped by config date or period.", E_USER_NOTICE);

  exit(0);
}

function loadConfig() {
  $config_path = realpath("food.ini");
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

function prepareFrontendData(array $data): array {
  usort($data, function ($a, $b) {
    return ($a['ctime'] > $b['ctime']) ? -1 : 1;
  });

  $data = array_reduce($data, function ($result, $item) {
    if ($item['flag'] == SS_FLAG) {
      $result[SS_FLAG][] = $item;
    }
    else {
      $result[SM_FLAG][] = $item;
    }

    return $result;
  }, ['ss' => [], 'sm' => []]);

  return array_slice($data, 0, DUMP_LAST * 2); // 10 for ss & 10 for sm
}

function processFrontendData(string $path) {
  $dst = realpath($path);

  if (!$dst) {
    trigger_error("Wrong dst directory [". $path ."]". E_USER_ERROR);
  }

  if (!is_writable($dst)) {
    trigger_error("Destination directory [". $dst ."] is unwritable", E_USER_ERROR);
  }

  $dst_files = scandir($dst);

  if (!$dst_files) {
    trigger_error("Unable to scan destination directory [". $dst ."]", E_USER_ERROR);
  }


  $actual = array_filter($dst_files, function ($file) {
    return (bool) preg_match(FILE_REGEXP, $file, $matches);
  });


  return array_map(function ($file) use ($dst) {
    $filepath = realpath($dst ."/". $file);

    $ctime = date_create("@". filectime($filepath), timezone_open(TIMEZONE));
    $ctime = date_time_set($ctime, 0, 0, 0);
    $md5 = md5_file($filepath);

    $matches = [];
    preg_match(FILE_REGEXP, $file, $matches);

    return [
      "filename" => $file,
      "ctime" => $ctime,
      "flag" => $matches['1'],
      "md5" => $md5
    ];
  }, $actual);
}

function dumpFrontendData(array $data) {
  print json_encode($data, JSON_PRETTY_PRINT);
}
