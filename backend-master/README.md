# Trips backend - ветка для разработчков!

Быстрые ссылки на документацию:
  - [Описание методов API для приложения](https://data.dev.trips.im/api/trips-documentation) - документация сгенерирована автоматически, исходя из комментариев в коде. (Swagger)
  - [Описание методов websocket'a](https://data.dev.trips.im/wss-doc/index.html) - документация сгенерирована библиотекой [AsyncAPI](https://asyncapi.io)

Оглавление : 
- [Требования к серверу](#requirements)
- [Установка ПО](#installation)
    - [NginX](#nginx)
    - [PHP](#php)
    - [MySQL](#mysql)
    - [PHPMyAdmin](#phpmyadmin)
    - [Composer](#composer)
    - [Supervisor](#supervisor)
    - [Redis](#redis)
    - [Images Optimization](#imgoptim)
- [Установка бекенда](#backinstallation)
- [Ручной деплой](#manual_deploy)
- [Автоматический деплой](#auto_deploy)

# <a name="requirements">Требования к серверу</a>:
  - Ubuntu 20.04
  - PHP 7.3.24
  - Amazon Aurora (MySQL 8)
  
### <b>Важно</b>:
В панели управления сервером нужно открыть порты:
- 465 SMTP для исходящей почты
- 6001 для websocket

### Используем такие библиотеки (вендор/версия/описание):
```sh
composer show
```
- ankitpokhrel/tus-php                  v2.1.2   A pure PHP server and client for the tus resumable upload ...
- aws/aws-sdk-php                       3.173.28 AWS SDK for PHP - Use Amazon Web Services in your PHP project
- beyondcode/laravel-server-timing      1.2.0    Add Server-Timing header information from within your Lara...
- beyondcode/laravel-websockets         1.9.0    An easy to use WebSocket server
- cboden/ratchet                        v0.4.3   PHP WebSocket library
- darkaonline/l5-swagger                8.0.0    Swagger integration to Laravel 5
- laravel/framework                     v7.28.3  The Laravel Framework.
- laravel/passport                      v9.3.0   Laravel Passport provides OAuth2 server support to Laravel.
- laravel/socialite                     v5.0.0   Laravel wrapper around OAuth 1 & OAuth 2 libraries.
- socialiteproviders/apple              v3.0.0   Apple OAuth2 Provider for Laravel Socialite
- socialiteproviders/facebook           v1.0     Facebook OAuth2 Provider for Laravel Socialite
- socialiteproviders/github             v1.0     GitHub OAuth2 Provider for Laravel Socialite
- socialiteproviders/google             v3.1.0   Google OAuth2 Provider for Laravel Socialite
- socialiteproviders/manager            v3.6     Easily add new or override built-in providers in Laravel S...
- socialiteproviders/vkontakte          v4.1.0   VKontakte OAuth2 Provider for Laravel Socialite
- swagger-api/swagger-ui                v3.42.0   Swagger UI is a collection of HTML, Javascript, and CSS a...
- swiftmailer/swiftmailer               v6.2.5   Swiftmailer, free feature-rich PHP mailer
- textalk/websocket                     1.5.2    WebSocket client

### Контроль версий базы данных:
- Описание всех таблиц можно увидеть в папке: database/migrations
- Полная очистка БД: php artisan migrate:refresh
- Модель App\City описывает города, которые мы спарсили с WikiData, поэтому она использует соединение с базой данных wikidata

# <a name="installation">Установка ПО</a>:

## <a name="nginx">NginX</a>
Установка:
```sh
sudo apt update
sudo apt install nginx
sudo systemctl status nginx 
sudo systemctl is-enabled nginx
```
Создаем конфиг для нового сайта:
```sh
sudo nano /etc/nginx/sites-available/data.dev.trips.im
```
Вот готовый конфиг, нужно только подставить адрес нужного домена и проверить правильно ли указан путь к сертификатам:
```sh
server {

    listen 443 ssl;
    listen [::]:443 ssl;

    root /var/www/html/data.dev.trips.im/public;
    server_name data.dev.trips.im;

    ssl_certificate /etc/letsencrypt/live/dev.trips.im/fullchain.pem; # managed by Certbot
    ssl_certificate_key /etc/letsencrypt/live/dev.trips.im/privkey.pem; # managed by Certbot
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

    access_log  /var/log/nginx/data.dev.trips.im_access.log;
    error_log  /var/log/nginx/data.dev.trips.im_error.log;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
 
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_pass unix:/var/run/php/php7.3-fpm.sock;
        fastcgi_read_timeout 600;
   }
 
   location ~ /\.(?!well-known).* {
        deny all;
   } 

   client_max_body_size 10m;

}

```


Проверяем настройки и если все ок, то перезагружаем:
```sh
sudo nginx -t
sudo systemctl restart nginx
```
Если оказалось 80 порт занят apache'м, то останавливаем его и удаляем:
```sh
sudo service apache2 stop 
apt remove apache2
```
Проверить занятые порты можно этой командой:
```sh
sudo apt-get install net-tools
sudo netstat -tulpn
```

## <a name="php">PHP</a>
<b>Важно!</b> Нам нужна версия php 7.3, потому что как оказалось в версии 7.4  [функция implode() стала deprecated](https://www.php.net/manual/en/migration74.deprecated.php#migration74.deprecated.core.implode-reverse-parameters), и у некоторых библиотек это вызывает ошибку. Поэтому проверяем установленную версию:
```sh
php -v
```
Если показывает не 7.3.*, то меняем версию:
```sh
sudo add-apt-repository -y ppa:ondrej/php

sudo apt install php7.3 php7.3-fpm php7.3-common php7.3-zip php7.3-curl php7.3-xml php7.3-xmlrpc php7.3-json php7.3-mysql php7.3-pdo php7.3-gd php7.3-imagick php7.3-ldap php7.3-imap php7.3-mbstring php7.3-intl php7.3-cli php7.3-recode php7.3-tidy php7.3-bcmath php7.3-opcache
```
Открываем конфиги:
```sh
sudo nano /etc/php/7.3/fpm/php.ini
```
И увеличиваем время выполнения скриптов и максимальный объем передаваемых данных:
```sh
max_execution_time = 30
upload_max_filesize = 10M
post_max_size = 10M
```
Проверяем включен ли PHP и перезагружаем веб-сервер:
```sh
sudo systemctl is-enabled php7.3-fpm
sudo systemctl restart php7.3-fpm
sudo systemctl restart nginx
```

Можно вывести страницу конфигурации PHP на экран браузера, так удобнее:
```sh
sudo mkdir /var/www/html/data.dev.trips.im/public ; echo "<?php phpinfo(); ?>" | sudo tee /var/www/html/data.dev.trips.im/public/index.php
```
Если страничка открылась [data.dev.trips.im](https://data.dev.trips.im) - значит установка прошла успешно.

## <a name="mysql">MySQL</a>
<b>(Этот шаг можно пропустить, если используется Amazon Aurora)</b>

Установка:
```sh
sudo apt install mysql-server
sudo mysql_secure_installation
sudo systemctl restart mysql
sudo systemctl enable mysql
mysql -V
```
Дальше следуем инструкциям на экране, и после установки переходим к настройке:
```sh
sudo mysql
```
Если что-то идет не так, то стоит проверить открыт ли порт 3306. Создаем суперпользователя:
```sh
CREATE DATABASE trips_database;
CREATE USER 'db_admin'@'%' IDENTIFIED BY 'пароль';
GRANT ALL PRIVILEGES ON trips_database.* to db_admin@'%';
GRANT ALL PRIVILEGES ON *.* to db_admin@'%';
FLUSH PRIVILEGES;
exit;
```
Полезная команда для сброса пароля суперпользователя:
```sh
ALTER USER 'db_admin'@'%' IDENTIFIED WITH mysql_native_password BY 'новый_пароль'; 
```
Открываем настройки MySQL: 
```sh
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf; 
```
Конфиг MySQL для текущего сервера с 1 ГБ ОЗУ я взял отсюда [https://ruhighload.com/mycnfexample?ram=1](https://ruhighload.com/mycnfexample?ram=1), можно просто скопировать как есть:
```sh
[client]
port                        = 3306
socket                      = /var/run/mysqld/mysqld.sock

[mysqld_safe]
socket                      = /var/run/mysqld/mysqld.sock
nice                        = 0

[mysqld]
user                        = mysql
pid-file                    = /var/run/mysqld/mysqld.pid
socket                      = /var/run/mysqld/mysqld.sock
port                        = 3306

basedir                     = /usr
datadir                     = /var/lib/mysql
tmpdir                      = /tmp
language                    = /usr/share/mysql/english
bind-address                = 127.0.0.1

skip-external-locking

max_allowed_packet          = 16M
key_buffer_size             = 16M
innodb_buffer_pool_size     = 512M
innodb_file_per_table       = 1
innodb_flush_method         = O_DIRECT
innodb_flush_log_at_trx_commit  = 0

max_connections             = 132

slow_query_log		        = 1
slow_query_log              = /var/log/mysql/mysql-slow.log
long_query_time             = 1

expire_logs_days            = 10
max_binlog_size             = 100M

[mysqldump]
quick
quote-names
max_allowed_packet          = 16M
```
И перезапускаем:
```sh
sudo systemctl restart mysql
```

## <a name="phpmyadmin">PHPMyAdmin</a>
Установка:
```sh
sudo apt install phpmyadmin
```
На первом вопросе нажимает ESC, потом YES и указываем проль от пользователя MySQL.
Расшарим ссылку:
```sh
sudo ln -s  /usr/share/phpmyadmin /var/www/html/data.dev.trips.im/public/phpmyadmin
sudo chmod 775 -R /usr/share/phpmyadmin/
sudo chown root:www-data -R /usr/share/phpmyadmin/
```

## <a name="composer">Composer</a>
Установка:
```sh
cd
curl -sS https://getcomposer.org/installer -o composer-setup.php
HASH=`curl -sS https://composer.github.io/installer.sig`
echo $HASH
```
Это одна команда, большая просто:
```sh
php -r "if (hash_file('SHA384', 'composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
```
```sh
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
sudo /usr/local/bin/composer self-update 1.10.17
composer

```

## <a name="supervisor">Supervisor</a>
Установка:
```sh
sudo apt install supervisor
```
Автозапуск:
```sh
sudo systemctl enable supervisor
```

Создаем файл конфига для веб-сокета:
```sh
sudo nano /etc/supervisor/conf.d/websockets.conf
```
Скопируйте готовый конфиг для веб-сокета:
```sh
[program:websockets]
command=/usr/bin/php /var/www/html/data.dev.trips.im/artisan websockets:serve
numprocs=1
autostart=true
autorestart=true
user=root
```
Создаем файл конфига для воркера:
```sh
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```
Скопируйте готовый конфиг для воркера:
```sh
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=/usr/bin/php /var/www/html/data.dev.trips.im/artisan  queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=root
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/html/data.dev.trips.im/worker.log
stopwaitsecs=3600
```



## <a name="redis">Redis</a>
Установка:
```sh
sudo apt install redis-server
```
Автозапуск:
```sh
sudo systemctl enable redis-server.service
```


## <a name="imgoptim">Images Optimization</a>

```sh
sudo apt-get install jpegoptim
sudo apt-get install optipng
sudo apt-get install pngquant
sudo apt-get install gifsicle
sudo apt-get install webp
```

# <a name="backinstallation">Установка бэкенда</a>



Загрузите исходные коды из репозитория в папку сайта. Перейдите в папку сайта:
```sh
cd /var/www/html/data.dev.trips.im
```
Cкопируйте файл с настройками окружения:
```sh
sudo cp /var/www/html/data.dev.trips.im/.env.example /var/www/html/data.dev.trips.im/.env;
```
Откройте файл с настройками, и введите все необходимые данные:
```sh
sudo nano /var/www/html/data.dev.trips.im/.env;
```
Выполните установку компонентов:
```sh
sudo composer install
```
Выполните установку таблиц в базу данных:
```sh
sudo php artisan migrate
```
Сделайте веб-сервер владельцем указанных папок:
```sh
sudo chown -R www-data:www-data /var/www/html/data.dev.trips.im/storage ; 
sudo chown -R www-data:www-data /var/www/html/data.dev.trips.im/bootstrap ; 
sudo chown -R www-data:www-data /var/www/html/data.dev.trips.im/vendor ;
```
Установите ключи шифрования:
```sh
sudo php artisan passport:install
```

Документация по ключах шифрования: [https://laravel.com/docs/5.8/passport](https://laravel.com/docs/5.8/passport)

Последним шагом запустите оптимизацию приложения:
```sh
sudo php artisan optimize
```
И перезагрузите сервер. После этой команды подождите минуту, и проверьте запустились ли все сервисы:
```sh
sudo reboot
```








# <a name="manual_deploy">Ручной деплой</a>:

После обновления кода на сервере, нужно **обязательно** перезапустить службы, чтобы изменения вступили в силу. Для этого перейдите в папку проекта и выполните все перечисленные команды.

1. Сбросьте кеш приложения:
    ```sh
    sudo php artisan optimize
    ```
2. Обновите структуру базы данных:
    ```sh
    sudo php artisan migrate
    ```
3. Перезапустите веб-сокет:
    ```sh
    sudo supervisorctl restart websockets
    ```
4. Перезапустите планировщих задач:
    ```sh
    sudo supervisorctl restart laravel-worker:*
    ```
    
# <a name="auto_deploy">Автоматический деплой</a>: 
  

1. Создайте группу деплоя.    
    ```sh
    sudo groupadd deployGroup
    ``` 
2. Создайте нового пользователя Ubuntu для сервиса Gitlab и добавьте его в группу деплоя:    
    ```sh
    sudo -m useradd deployer; sudo usermod -a -G deployGroup deployer
    ```
3. Дайте пользователю deployer возможность выполнять команды sudo без ввода пароля. Выполните:
    ```sh
    sudo visudo
    ``` 
4. Добавьте в конец файла:  
    ```sh
    deployer ALL=(ALL) NOPASSWD: ALL
    ```     
4. Сгенерируйте ключи для пользователя deployer
    ```sh
    ssh-keygen
    ```
5. После ввода этой команды вы должны увидеть следующий вывод:
    
    ```sh
    Generating public/private rsa key pair.
    Enter file in which to save the key (/your_home/.ssh/id_rsa):
    ```
6. Нажмите Enter для сохранения пары ключей в директорию .ssh/ внутри домашней директории.
Пароль задавать не нужно, потому что это пользователь для Gitlab.    

7. Скопируйте публичный ключ из ~/.ssh/id_rsa.pub в ~/.ssh/authorized_keys".
Скопируйте закрытый ключ из ~/.ssh/id_rsa в Gitlab. Для этого нужно зайти в настройки репозитория и нажать "Add variable":
![Alt text](public/images/deploy_manual/cd-cd-variables.png?raw=true "Переменные окружения")
<b>Важно:</b> при копировании нажмите CTRL+A и затем CTRL+C. В приватном ключе есть 1 пустая строка, которая может не выделиться, если попытаться копировать мышкой.  
 
8. Дайте имя переменной SSH_PRIVATE_KEY и снимите галочки как показано на скриншоте.
![Alt text](public/images/deploy_manual/private_key.png?raw=true "Переменные окружения")

9. Добавьте еще 2 переменные. SSH_USER: deployer и SSH_DEV_SERVER: 15.237.23.22. Галочки нужно убрать у всех.![Alt text](public/images/deploy_manual/variables_params.png?raw=true "Переменные окружения")

10. Создайте папку:
    ```sh
    sudo mkdir /var/www/html/deployer/data_dev_branch/releases; cd /var/www/html/deployer/data_dev_branch
    ```
11. Клонируйте ветку dev из репозитория.
    ```sh
    git clone -b dev git@gitlab.com:tripsdev/backend.git
    ```
12. Поместите баш скрипт в ту же папку:
    ```sh
    cp /home/vlad/deploy_dev_branch.sh /var/www/html/deployer/data_dev_branch/deploy_dev_branch.sh
    ```
12. Добавьте баш скрипту право на выполнение:
    ```sh
    cd /var/www/html/deployer/data_dev_branch; sudo chmod ugo+x deploy_dev_branch.sh
    ```
Готово! Теперь после каждого пуша Gitlab будет запускать выполнение баш скрипта, и новый код автоматически установится.    
<b>Важно:</b> Переменные окружения находятся в файле .env. Если изменились переменные окружения, то перед запуском деплоя следует сначала обновить эти данные!