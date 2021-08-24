#!/bin/bash

mkdir /var/log/kriekon
# See src/APIError.php
touch /var/log/kriekon/kriekon_api.log

php /home/kriekon/api/src/scripts/dev/generate_environment.php