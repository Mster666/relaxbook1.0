set -e

umask 000

mkdir -p \
  storage/app/public \
  storage/framework/views \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/logs \
  bootstrap/cache

rm -rf public/storage
ln -s ../storage/app/public public/storage

chmod -R 0777 storage bootstrap/cache || true

php -S 0.0.0.0:${PORT:-8080} -t public server.php

