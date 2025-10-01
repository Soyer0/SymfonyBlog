# Symfony Blog

Простий блог на Symfony з Docker.

## Функціонал

- **Пости**
    - Список постів
    - Перегляд посту
    - Додавання посту (title, slug, content, tags)
- **Теги**
    - Список тегів
    - Додавання тегу (name)
- Bootstrap для стилізації форм і навігації
- Flash-повідомлення про успішні дії

---

## Архітектура
```
symfony/
├─ docker/              # Docker файли (php, nginx, node)
├─ src/
│  ├─ Controller/       # Контролери (PostController, TagController)
│  ├─ Entity/           # Сутності (Post, Tag)
│  ├─ Form/             # Форми (PostType, TagType)
│  └─ Repository/       # Репозиторії Doctrine
├─ templates/           # Twig шаблони
├─ migrations/          # Doctrine міграції
└─ public/              # Публічні файли (CSS, JS)
```
## Запуск у Docker

```bash
# Скопіювати приклад .env і підправити якщо потрібно
cp .env.example .env

# Підняти контейнери
docker-compose up -d --build

# Встановити залежності Symfony
docker-compose exec php composer install

# Встановити npm пакети та збирати CSS
docker-compose run --rm node sh -c "npm install && npm run build"

# Створити базу даних та застосувати міграції
docker-compose exec php php bin/console doctrine:database:create
docker-compose exec php php bin/console doctrine:migrations:migrate
```

Сайт буде доступний: http://localhost:8080/
