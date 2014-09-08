Space
=====

#Команды:

## Общие

version - показывает текущую версию

server - показывает текущий сервер

server [url] - устанавливает новый сервер

test - тестирует связь с сервером

test -d - тестирует подключение к базе данных

## Игровые

### Аккаунт

register -l [login] -p [password] -n [name] - регистрация на сервере

user -n [login] -p [password] - логин пользователя

user -n [name] - информаци о пользователе

user -o - разлогинивание

my - информация о пользователе

my planets - список ваших планет

### Планеты

planet - информация о текущей планете

planet [planetName] - информация о планете

planet [planetName] -u - использовать планету [planetName] как текущую

planet -n - список ближайших планет возле текущей планеты

planet [planetName] -n - список ближайших планет возле [planetName]

planet -t [taskName] - установить планетарное задание для текущей планеты

planet [planetName] -t [taskName] - установить планетарное задание для [planetName]

planet -s [scienceName] - освоить технологию для текущей планеты

planet [planetName] -s [scienceName] - освоить технологию для [planetName]

planet -k - список освоенных технологий для текущей планеты

planet [planetName] -k - список освоенных технологий для [planetName]

planet -a - список доступных для изучения технологий для текущей планеты

planet [planetName] -a - список доступных для изучения технологий для [planetName]

### Наука

science [scienceName] - информация о технологии

science -a - список всех технологий

science [scienceName] -с [planetName] - проверяет доспупность изучения технологии для [planetName]