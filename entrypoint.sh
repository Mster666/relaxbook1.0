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

PORT="${PORT:-8080}"
export PORT

envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
