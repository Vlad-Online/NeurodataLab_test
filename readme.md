# Тестовое задание
Необходимо поднять сервис, позволяющий добавлять пользователя в БД и
выдающий результат обработки запроса.

Сервис должен быть написан на любом удобном фреймворке (в идеале – на
symfony4) и развернут в докере.
Для получения запросов сервис должен слушать очередь RabbitMQ (можно
использовать готовые бандлы). Для RabbitMQ параметры host, port, vhost,
exchange, queue, user, password должны задаваться в конфиге или через
командную строку. (RabbitMQ крутится в отдельном докере)
Сервис должен общаться с уже подготовленной базой данных postgresql.
Host, port, db_name, user, password для БД должны задаваться в конфиге
или через командную строку. (PostgresQL крутится в отдельном докере)
Структура базы данных:
- id character(10) NOT NULL
- name character(100) NOT NULL
- email character(100) NOT NULL
- location character(100) NOT NULL

Запрос на добавление пользователя будет приходить в виде словаря с
ключами:
- ‘action’: ‘add_user’
- ‘name’: ‘some_name’
- ‘email’: ‘some_email’
- ‘location’: ‘some_city’
- ‘reply_to’: {‘queue’: ‘some_queue’, ‘exchange’: ‘some_exchange’

После получения, проверки корректности и выполнения входящего запроса,
система должна по роутинг из reply_to поля запроса выслать информацию.

При успешном выполнении:
Id: id человека в таблице; error_code: 0, error_msg: ‘’Id: id человека в таблице; error_code: 0, error_msg: ‘’
  
При ошибке: 
id: null, error_code: 1, error_msg: “error description”

Гарантируется, что во входящем запросе всегда есть поле reply_to с
правильной структурой. Присутствие и правильность других полей не
гарантируется.
В качестве результата:
- Код проекта, например на github
- Код для сборки докера
- (желательно) Собранный докер
- Readme с общим описанием системы и работы с ней

# Описание
Сервис разработан с использованием
- Фреймворка Laravel 6.2
- БД PostgresSQL 12
- Брокера сообщений RabbitMQ 3.8
и развернут в Docker контейнерах

Для работы с протоколом AMQP используется бандл rabbitevents https://github.com/nuwber/rabbitevents

# Установка
`git clone https://github.com/Vlad-Online/NeurodataLab_test.git`
# Настройка
Парамерты прослушиваемой очереди задаются в файле 
./service/config/queue.php

    'rabbitmq' => [    
        'exchange'       => env('RABBITMQ_EXCHENGE', 'events'),`
        'host'           => env('RABBITMQ_HOST', 'rabbitmq'),
        'port'           => env('RABBITMQ_PORT', 5672),
        'user'           => env('RABBITMQ_USER', 'guest'),
        'pass'           => env('RABBITMQ_PASSWORD', 'guest'),
        'vhost'          => env('RABBITMQ_VHOST', '/'),
        'queue'          => env('RABBITMQ_QUEUE', 'user.add'),
        'logging'        => [
            'enabled'        => env('RABBITEVENTS_LOG_ENABLED', false),
            'level'          => env('RABBITEVENTS_LOG_LEVEL', 'info'),  
        ]
    ]
			
Параметры подключения к базе данных задаются в файле
./service/config/database.php

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'postgres'),
            'username' => env('DB_USERNAME', 'postgres'),
            'password' => env('DB_PASSWORD', 'password'),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

# Запуск
Сборка контейнеров

`docker-compose build`

При первом запуске необходимо установить зависимости

 `docker-compose run --no-deps --entrypoint "composer install" service`

Запуск

 `docker-compose up`
 
# Отладка
Для отправки тестового сообщения используйте следующую команду

`docker-compose exec service php artisan userqueue:send`

Команда принимает следующие параметры

    Options:
      -u, --user[=USER]                      User name
      -e, --email[=EMAIL]                    User email
      -l, --location[=LOCATION]              User location
          --reply_queue[=REPLY_QUEUE]        Reply queue name
          --reply_exchange[=REPLY_EXCHANGE]  Reply exchange name
      -a, --action[=ACTION]                  Action name [default: "add_user"]
      -h, --help                             Display this help message
      -q, --quiet                            Do not output any message

Для отладки в вашей IDE (по протоколу xdebug) замените строчку в файле ./service/Dockerfile

`ENTRYPOINT php artisan rabbitevents:listen user.add`

На следующую

`ENTRYPOINT php -dxdebug.remote_host=< IP хост машины > artisan rabbitevents:listen user.add`

Панель управления RabbitMQ Management Console доступна по адресу http://localhost:15672
Логин/Пароль guest
