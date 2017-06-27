PHPUNIT=./vendor/bin/phpunit
TEST_REPORTS_DIR=./tests/_resources/reports/

phplint:
	./bin/phplint.sh src/ tests/
.PHONY: phplint

phpcs-check:
	php-cs-fixer fix --dry-run --diff
.PHONY: phpcs-check

phpcs-fix:
	php-cs-fixer fix
.PHONY: phpcs-fix

composer:
	composer validate
	composer install --prefer-source --no-interaction --dev
.PHONY: composer

phpunit-no-report:
	$(PHPUNIT)
.PHONY: phpunit-no-report

phpunit:
	$(PHPUNIT) --log-junit $(TEST_REPORTS_DIR)/phpunit-report.xml
.PHONY: phpunit

phpunit-coverage:
	$(PHPUNIT) \
	--coverage-html $(TEST_REPORTS_DIR)/html \
	--coverage-clover $(TEST_REPORTS_DIR)/clover/clover.xml \
	--log-junit $(TEST_REPORTS_DIR)/phpunit-report.xml
.PHONY: phpunit-coverage

check: phplint phpcs-check phpunit

