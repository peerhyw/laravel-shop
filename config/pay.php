<?php

return [
	'alipay' => [
		'app_id' => '2016092100564637',
		
		'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyiVhPqHAuMwHOeadFn6L5HsTy+F6inWSIdrkxHQ3T66iDMhSm4GDqqGb6srZ/+qJq0QMS3C/WDTyfmHF4OrVgJ2eHT+2BEASLSkWXUaUU8mrvEg4ns52eWhFvXlI+/RYeUf3WZaoCFyOeeCMIE3vHQSTQcGzIb7jV2b9HuiZ4lYTXDkXF5HfwijnM/vI8jzHBG3QJwCnJUj0RRJOEHH5lS7STZu5GaAf6J3nLPJa7iGl06hBfBKV1Y6r6xvehvB86p4z9filEVwq1wprdcCzFerxPEM2Gwj1/O6H5KHnPDD9HxUMcRZtUlEmeede9+4HrmTn8fvwnIBUgRP9J1gEDQIDAQAB',
		
		'private_key' => 'MIIEowIBAAKCAQEA6xpTvmekWGqBK11dCIh5VfbosFYhmVpndkEiu6ljECY3R9BAEinnuS87/y5x0uWxBCtzHehsHIp1plLJ35sJKUF8ilVuNu4HI48vtYOpmyw+ECLgv5YE+AZH01uBPZ0Jmu1MRaRY0tBUZ5gLgr806vTvXb5qj0LKQjDeaAfW6D/omvt1sql3h+kzazLozAokpMwbspZoE6oEJvWkPFZyVwD9on0ImOtuY+RaJSbuDH4CB1c3b+XZXpwiPVUPZ0ZkSKPH7dIc6HdmuV0AgSA/s+Xw31URYS1xuF0WPsupfzskTgcJensBSlvIUbU0fWlU+akFN5Dqy1R/PMzX4QaDvQIDAQABAoIBABdtyTipVWxmOLccl7/Y8daKQ4gHHVQN+U+EkDSJXdDnLg0fCLOGr4v51A3LEBbHQwu6VL9/QP7bIXxQtcNtzMzqtMGIX/JjaBy4ETYSh91p3El5YFJXXellntTGQqvkMWfaDAbIqP1hO3gTY9pEub5MaGo8JpxeOI++FPvjBByiyOAhUPs9GMP6nMBgsdjvw5m5iI2RR+OI2+FgKAsb8i2N3+7hCVqn9ZMq+SdK6ktjeJ7WTS4X08B6XESldNbKl525TE+s36nqxg1Oaml+QUFDyGkr4igA71Cdr+pZaughLTeoPhWzuQlJFJRsGoZRXma0oT0bHWszBj91ObW8VlUCgYEA+n7x9RzMn+GSNlGo0DyOrSMTjnxSnO0NwufmvIgfMb6KvqN2ftEDJvk8f+BVkfjaH+sK+PjfRF7MeY3jRXI8WWQmGV+RUJJimTI7zP8TAtz5eYpfonJfnilUF1O05nuWbzfay+ilyQCELYqQF9ZPaoUSFcm0jrVMQnFlSO6ETicCgYEA8ETLkMqeorDUeJKki6jHr2wyNdYek7uIP28c9ewctEmYGAEQpF50DH9eqHAq8UpUgfJR91hLYUkbPfMmUHlQgQWdyIt2fFeNupedE67eIcXU/9vxqFKZA8ASQ8mFzHqG0zxzbwAGbFe2rxAGnkNO4gZnWfqRCli6r9wpnpa0sXsCgYEAoikwBZehOBdVCekPOc8aJidA78q3yHMFp5lsm7wbRZ6uPv+fJDW2rrJGYhoeCyNoQaVtMwQZtS8Re9dIu5paSxw0NdTSQ7CgN8B3ShwOeJoIvo+/nXAhSkhC8d70iwiSuGkWMU7olBjLeJfs9CF+w9xleslbI89mENVL1kziRZ0CgYA8d0O3m5ZSoSfHdDgewYoHVrZIICuvyBkgxajHJvWOVZsGJ3Z1tyODZaZ/w0K7WJZt4XrJQZou0IQfkgqJZA5jefNcXeSipDIRzEgGhRJ3816ISWTGlIrXi8XM83FNpWQ60tLGE36KU4SxB9pyh8PZU/08grdvoCbTTRfYRlsQ8wKBgCGGa/nLNeNfUKhTMkMfLrdtsjyTE0f1nE3/Ke5UToU1JFa1DnKo+EbawOdEhTPVQOLe3202kPhmOp47Ouv9LiK2WqwYVf3HoWug1NQAWzsCNjCuWo5l+SftLcsNlGyE+FZTwmlqPLK4SeRqZh+rz4gr1GiKPZ9KMBMyyCXE975G',
		
		'log' => [
			'file' => storage_path('logs/alipay.log'),
		],
	],

	'wechat' => [
		'app_id' => '',
		'mch_id' => '',
		'key' => '',
		'cert_client' => '',
		'cert_key' => '',
		'log' => [
			'file' => storage_path('logs/wechat_pay.log'),
		],
	],
];