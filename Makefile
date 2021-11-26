export APPNAME=food7

default: build

build: clean $(APPNAME).phar

$(APPNAME).phar:
	php --define phar.readonly=0 build.php

clean:
	rm $(APPNAME).phar

