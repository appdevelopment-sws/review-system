# Docker Environment for Review System Backend

This repository includes a production-ready, containerized Docker development environment tailored for the Laravel 11/13 backend (PHP 8.4).

---

## Architecture Overview

The multi-container stack consists of:

| Service        | Container Name             | Image / Base               | Internal Port | Host Port | Purpose                                             |
| -------------- | -------------------------- | -------------------------- | ------------- | --------- | --------------------------------------------------- |
| **app**        | `review_system_app`        | PHP 8.4 FPM (`Dockerfile`) | `9000`        | -         | Laravel API Backend (PHP-FPM, Composer, extensions) |
| **nginx**      | `review_system_nginx`      | `nginx:alpine`             | `80`          | `8000`    | Web server & FastCGI reverse proxy                  |
| **db**         | `review_system_db`         | `mysql:8.0`                | `3306`        | `3306`    | MySQL database with persistent volume               |
| **redis**      | `review_system_redis`      | `redis:alpine`             | `6379`        | `6379`    | Cache, session store, and queue engine              |
| **queue**      | `review_system_queue`      | PHP 8.4 FPM (`Dockerfile`) | -             | -         | Background queue worker (`artisan queue:work`)      |
| **phpmyadmin** | `review_system_phpmyadmin` | `phpmyadmin/phpmyadmin`    | `80`          | `8080`    | Web GUI for database inspection                     |

---

## Quickstart Guide

### 1. Configure Environment File

Copy the provided `.env.docker` to `.env` (or configure your `.env`):

```bash
cp .env.docker .env
```

Key environment settings for Docker:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=review_db
DB_USERNAME=review_user
DB_PASSWORD=secret

REDIS_HOST=redis
REDIS_PORT=6379
QUEUE_CONNECTION=redis
CACHE_STORE=redis
```

### 2. Build and Start Containers

Start all services in detached mode:

```bash
docker compose up -d --build
# or using Makefile:
make build
```

Verify all containers are healthy and running:

```bash
docker compose ps
# or
make ps
```

### 3. Install Dependencies & Generate App Key (If needed)

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
```

### 4. Run Migrations & Seeders

```bash
docker compose exec app php artisan migrate
# or with seeders:
docker compose exec app php artisan migrate --seed
# or using Makefile:
make migrate
```

### 5. Access the Services

- **Laravel API / Web**: [http://localhost:8000](http://localhost:8000)
- **phpMyAdmin**: [http://localhost:8080](http://localhost:8080)
    - **Server**: `db`
    - **Username**: `review_user` (or `root`)
    - **Password**: `secret` (or root password `root_secret`)
    - **Database**: `review_db`

---

## Daily Workflow & Helpful Commands

### Using Make (Recommended)

| Command              | Action                                                                   |
| -------------------- | ------------------------------------------------------------------------ |
| `make up`            | Start containers in background                                           |
| `make down`          | Stop containers                                                          |
| `make restart`       | Restart containers                                                       |
| `make build`         | Rebuild images and start containers                                      |
| `make ps`            | Check status of running containers                                       |
| `make logs`          | Stream logs of all containers (`make logs s=app` for specific container) |
| `make bash`          | Open bash shell in `app` container                                       |
| `make migrate`       | Run database migrations                                                  |
| `make seed`          | Run database seeders                                                     |
| `make migrate-fresh` | Drop all tables and re-run migrations + seeds                            |
| `make test`          | Run PHPUnit test suite                                                   |
| `make clean`         | Stop containers and wipe database/redis volumes                          |

### Direct Docker Compose Commands

- **Open shell inside app container**:

    ```bash
    docker compose exec app bash
    ```

- **Run any Artisan command**:

    ```bash
    docker compose exec app php artisan route:list
    docker compose exec app php artisan tinker
    ```

- **View real-time logs**:

    ```bash
    docker compose logs -f app
    docker compose logs -f nginx
    docker compose logs -f db
    ```

- **Stop containers without deleting data**:
    ```bash
    docker compose down
    ```

---

## Production & Scaling Notes

1. **Volume Persistence**: MySQL data is stored in Docker volume `db_data` and Redis in `redis_data`. Data persists across container restarts and updates.
2. **PHP OPcache & Upload limits**: Custom configured in `docker/php/local.ini` (512MB RAM, 64MB upload size).
3. **Nginx Buffers**: Optimized in `docker/nginx/default.conf` to handle large payload sizes and fast response routing.
