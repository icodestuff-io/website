#!/bin/sh
echo "Restarting Redis Server"
sudo /etc/init.d/redis-server restart

if [ ! -f /vagrant/.env ]; then
    echo "Creating your env file."
    cp /vagrant/.env.example /vagrant/.env
    echo "Generating your encryption key"
    php /vagrant/artisan key:generate
    echo "Seeding your database"
    php /vagrant/artisan migrate --seed
fi

if [ ! -f /etc/supervisor/conf.d/hoopspots.conf ]; then
    DAEMON="
[program: icodestuff]
command=php /home/vagrant/code/artisan horizon
stdout_logfile=/home/vagrant/code/storage/logs/supervisord.log
redirect_stderr=true
autostart=true
autorestart=true
    "
    echo "Creating the Icodestuff horizon daemon configuration"
    echo "$DAEMON" | sudo tee -a /etc/supervisor/conf.d/icodestuff.conf
    echo "Starting Supervisor"
    sudo supervisorctl reread
    sudo supervisorctl update
fi
