PHPLINT=./bin/phplint.sh
PHPUNIT=./vendor/bin/phpunit
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

metrics: phpunit
	$(PHPMETRICS) \
	--report-html=$(TEST_REPORTS_DIR)/metrics \
	--junit=$(TEST_REPORTS_DIR)/phpunit-report.xml \
	--extenstions=php \
	./src
.PHONY: metrics

check: phplint phpcs-check phpunit phan

