.PHONY: install test cs cs-fix cc

install:
	composer install

test:
	php bin/phpunit --configuration phpunit.xml.dist --testdox

cs:
	vendor/bin/php-cs-fixer fix --dry-run --diff

cs-fix:
	vendor/bin/php-cs-fixer fix

cc:
	php bin/console cache:clear
