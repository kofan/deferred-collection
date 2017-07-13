PHPLINT=./bin/phplint.sh
PHPUNIT=./vendor/bin/phpunit
PHPCPD=./vendor/bin/phpcpd
PHPMD=./vendor/bin/phpmd
PHP_CS_FIXER=./vendor/bin/php-cs-fixer
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
	$(PHP_CS_FIXER) fix --dry-run --diff --verbose
.PHONY: phpcs-check

phpcs-fix:
	$(PHP_CS_FIXER) fix
.PHONY: phpcs-fix

# Phan doesn't work correctly with iterable types yet
phan:
	$(PHAN)
.PHONY: phan

# Run phpunit without reports
phpunit:
	$(PHPUNIT)
.PHONY: phpunit

# Run phpunit and generate coverage html and xml report as well as junit format report
phpunit-coverage:
	$(PHPUNIT) \
	--coverage-html $(TEST_REPORTS_DIR)/coverage-html \
	--coverage-clover $(TEST_REPORTS_DIR)/clover.xml \
	--log-junit $(TEST_REPORTS_DIR)/phpunit.xml
.PHONY: phpunit-coverage

phpcpd:
	$(PHPCPD) src/ tests/ --min-lines=4 --min-tokens=30 --progress
.PHONY: phpcpd

# PHPMD doesn't work correctly with PHP 7.1 yet because of the PHP_Depend issue
# https://github.com/pdepend/pdepend/issues/297
phpmd:
	$(PHPMD) src/,tests/ text phpmd.xml
.PHONY: phpmd

# Submit coverage report to Coveralls servers, see .coveralls.yml
coveralls:
	composer require satooshi/php-coveralls:dev-master --no-update --no-progress
	php vendor/bin/coveralls -v
.PHONY: coveralls

# --- Dev macros ---
check: phplint phpcs-check phpcpd
before_commit: check phpunit

.PHONY: check before_commit

# --- CI commands ---
ci_before_build:
	bin/ast_install.sh
	composer self-update
	$(MAKE) composer
ci_build: check phpunit-coverage
ci_after_build: coveralls

.PHONY: ci_before_build ci_build ci_after_build

