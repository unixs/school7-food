<?php

require_once "./lib.php";

date_default_timezone_set('Europe/Moscow');
setlocale(LC_ALL, 'ru_RU.UTF-8');


checkWebAccess();

loadConfig();

checkNeedWork();

$lastIndex = loadLastIndex(DST_PATH);

copyFiles(SRC_SS_PATH, SRC_SM_PATH, DST_PATH, $lastIndex);

updateLastIndex(DST_PATH, $lastIndex);

writeFrontendData(DST_PATH);
