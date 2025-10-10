 clear cache of application
chmod +x app/Console
php artisan cache:clear || exit 1
chmod 777 -R storage/log

# run supervisor
supervisord -c /etc/supervisor.d/supervisord.ini
