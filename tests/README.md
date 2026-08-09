```bash
XDEBUG_MODE=coverage php vendor/bin/phpunit --coverage-filter src/ --coverage-clover=coverage/coverage.xml --coverage-html=coverage/ --coverage-text --colors=always --testdox
```