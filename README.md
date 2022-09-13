
# Sepp Test

## Version: 1.0.0

### Installation
Update environment **Docker** variables in `.env` file, especially draws attention to the `WEB_SERVER_PORT`, `WEB_SERVER_SECURE_PORT` and `MAGENTO_URL_HOST`.

Install / Update project dependencies using **composer**:

    composer install
 
deploy all **modman** modules:

    ./tools/modman deploy-all

create and start docker containers:

    docker-compose up -d --build --remove-orphans
