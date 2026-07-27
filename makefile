build:
	./vendor/bin/sail build

up:
	./vendor/bin/sail up -d

upsh:
	./vendor/bin/sail up -d && docker exec -it shop-php bash

down:
	./vendor/bin/sail down

shell:
	docker exec -it shop-php bash

restart:
	./vendor/bin/sail down && ./vendor/bin/sail up -d

restartsh:
	./vendor/bin/sail down && ./vendor/bin/sail up -d && docker exec -it shop-php bash

rebuild:
	./vendor/bin/sail down && ./vendor/bin/sail build && ./vendor/bin/sail up -d

log:
	docker logs -f --tail 20 shop-php
