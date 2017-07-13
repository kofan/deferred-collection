PHPLINT=./bin/phplint.sh
PHPUNIT=./vendor/bin/phpunit
PHPMD=./vendor/bin/phpmd
PHPMETRICS=./vendor/bin/phpmetrics
PHAN=./vendor/bin/phan
TEST_REPORTS_DIR=./tests/_resources/reports

composer:
	composer validate
	composer install --prefer-source --no-interaction --dev
.PHONY: composer

phplint:
	$(PHPLINT) src/ tests/
.PHONY: phplint

phpcs-check:
	php-cs-fixer fix --dry-run --diff
.PHONY: phpcs-check

phpcs-fix:
	php-cs-fixer fix
.PHONY: phpcs-fix

# Phan doesn't work correctly with iterable types yet
phan:
	$(PHAN)
.PHONY: phan

phpunit-no-report:
	$(PHPUNIT)
.PHONY: phpunit-no-report

phpunit:
	$(PHPUNIT) --log-junit $(TEST_REPORTS_DIR)/phpunit-report.xml
.PHONY: phpunit

phpunit-coverage:
	$(PHPUNIT) \
	--coverage-html $(TEST_REPORTS_DIR)/coverage-html \
	--coverage-clover $(TEST_REPORTS_DIR)/clover.xml \
	--log-junit $(TEST_REPORTS_DIR)/phpunit-report.xml
.PHONY: phpunit-coverage

# PHPMD doesn't work correctly with PHP 7.1 yet because of the PHP_Depend issue
# https://github.com/pdepend/pdepend/issues/297
phpmd:
	$(PHPMD) src/,tests/ text phpmd.xml
.PHONY: phpmd

check: phplint phpcs-check phpunit phpmd

