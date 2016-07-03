# LaravelShop (Интернет магазин)

Домашнее задание на курсе Loftschool php042016

###Установка

1. git clone https://github.com/visermort/larshop.git
2. cd larshop
3. cd laravelshop
4. composer install

###Задание выполняется в группе.

Создать сайт, используя верстку на базе Bootstrap. Тематика сайта — Интернет-каталог магазина «Все за 1010 рублей». Каждая модель имеет свою таблицу  категорий. Каждая модель содержит уникальные характеристики. Требуется реализовать админку для заполнения модели, организовать выдачу для пользователя. Админка должна быть доступна только авторизованному пользователю по заранее заданному адресу. Для заполнения данных разрешено использовать пакет Faker.

>Доступные модели:
>Напитки (drinks)
>Электротовары (electrics)
>Игрушечные 	модельки транспорта (transport_models)
>Компоненты 	для Arduino (arduino)
>Учебники (books)
>Корм для животных оптом (animal_food)
>Наручные часы (watch)
>Одежда (clothes)
>Шарфы и шапки (headclothes)
>Телефоны (phones)
>Линзы 	(contact_lenses)
>Другое ( согласовать с наставником )


Каждый товар, должен быть полностью редактируемым. Товар должен содержать не менее 4 уникальных характеристик. Разрешено использовать фантазию. Одна из характеристик должна быть представлена тегом select с редактируемыми возможными вариантами. Товар может иметь не менее одного изображения, кол-во изображений не ограничено. Все поля должны иметь проверку на соответствие типу(валидация). Изображения должны обрезаться в два или более размеров (превью и полное изображение). Перенос товара между моделями невозможен. Модель должна иметь гибкую фильтрацию, по каждой характеристике. Например модель линзы должна иметь подбор по уровню зрения на правый глаз, на левый глаз, а так же учитывать кривизну глаза (8.8 и 8.4). У каждого товара имеется кнопка “Заказать”, которая вышлет письмо с контактами пользователя на указанный в админке e-mail.