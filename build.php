<?php

try {
  $pharFile = getenv("APPNAME") .'.phar';

  if (file_exists($pharFile)) {
    unlink($pharFile);
  }

  $phar = new Phar($pharFile);

  $phar->startBuffering();

  $defaultStub = $phar->createDefaultStub('main.php');

  $phar->buildFromDirectory(__DIR__ . '/src');

  $stub = "#!/usr/bin/env php \n" . $defaultStub;

  $phar->setStub($stub);

  $phar->stopBuffering();

  $phar->compressFiles(Phar::GZ);

  chmod(__DIR__ . '/'. $pharFile, 0755);

  echo "$pharFile successfully created" . PHP_EOL;
}
catch (Exception $e) {
  echo $e->getMessage();
}
