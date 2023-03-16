# Введение

Данный репозиторий является результатом выполненного тестового задания.
В условиях реализации было запрещено использовать какие-либо фреймворки или библиотеки, в связи с чем задача выполнена в процедурном стиле (чтобы не подключать композер с автозагрузкой классов или не писать собственный "огород" из подключаемых классов)

# Версия 2.0

Во второй версии для создания иерархической (древовидной) структуры, была реализована модель Adjacency List, которая подразумевает использование рекурсии.
Однако, в целях оптимизации, я постарался не включать в рекурсию запросы к БД.

# Версия 2.1

Реализован простейший функционал шаблонизатора

# Установка проекта

- Скачать дистрибутив в корень сайта
- Создать локально БД
- Импортировать в нее дамп `dump.sql` из корня сайта
- В файле `config.php` прописать параметры подключения к БД