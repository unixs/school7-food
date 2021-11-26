<?php

require_once "lib.php";

date_default_timezone_set(TIMEZONE);
setlocale(LC_ALL, 'ru_RU.UTF-8');


checkAccess();

loadConfig();

checkNeedWork();

$lastIndex = loadLastIndex(DST_PATH);

copyFiles(SRC_SS_PATH, SRC_SM_PATH, DST_PATH, $lastIndex);

updateLastIndex(DST_PATH, $lastIndex);

$raw = processFrontendData(DST_PATH);

$data = prepareFrontendData($raw);

dumpFrontendData($data);

// Done!
// I am finished & smoke... 🚬
