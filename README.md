# Zexon Ping API

Laravel API na registráciu zariadení a zaznamenávanie stavu ich batérie. Zariadenie posiela stav batérie v pingu. API sprístupňuje zoznam zariadení a detail zariadenia s údajmi z posledného pingu.

## Modely

| Model | Úloha |
| --- | --- |
| `Device` | Zariadenie s názvom a jedinečným UUID. Má viac pingov (`pings`) a sprístupňuje posledný ping (`lastPing`). |
| `Ping` | Záznam o stave batérie (`battery_percent`) a čase prijatia (`created_at`). Patrí jednému zariadeniu. |

## Migrácie

- `2026_11_25_163252_create_devices_table.php` vytvára tabuľku `devices` s interným ID, jedinečným UUID, názvom a časovými pečiatkami.
- `2026_11_25_163253_create_pings_table.php` vytvára tabuľku `pings` s cudzím kľúčom `device_id`, percentom batérie a časom vytvorenia.
- Predvolené migrácie Laravelu vytvárajú tabuľky pre používateľov, obnovu hesla, sessions, cache a fronty úloh.

## Lokálne spustenie

Potrebuješ PHP 8.5, Composer a MySQL. V PHP zapni rozšírenie `pdo_mysql` pre aplikáciu a `pdo_sqlite` pre testy.

1. V koreňovom adresári projektu nainštaluj závislosti.

   ```sh
   composer install
   ```

2. Skopíruj `.env.example` do `.env`. V PowerShelli použi `Copy-Item .env.example .env`, v Bash použi `cp .env.example .env`. Ak už máš `.env`, uprav existujúci súbor.

3. Spusť MySQL a vytvor prázdnu databázu `zexonpingapi`. V súbore `.env` nastav pripojenie podľa svojho prostredia. Príklad používa lokálny účet `root` s prázdnym heslom.

   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=zexonpingapi
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. Vygeneruj aplikačný kľúč a spusti migrácie.

   ```sh
   php artisan key:generate --no-interaction
   php artisan migrate --no-interaction
   ```

5. Spusť lokálny server.

   ```sh
   php artisan serve --no-interaction
   ```

   Použi adresu vypísanú v termináli a pridaj cestu endpointu, napríklad `/api`.

## Endpointy

Pri požiadavkách `POST` pošli telo vo formáte JSON s hlavičkou `Content-Type: application/json`.

| Metóda | Cesta | Popis |
| --- | --- | --- |
| GET | `/api` | Overí dostupnosť API a vráti `{"status":"ok"}`. |
| GET | `/api/devices` | Vráti zoznam zariadení s posledným zaznamenaným stavom batérie. |
| GET | `/api/devices/{uuid}` | Vráti detail zariadenia vrátane času a stavu batérie z posledného pingu. |
| POST | `/api/devices` | Zaregistruje zariadenie. Pošli jedinečné `uuid` a `name` s dĺžkou najviac 255 znakov. |
| POST | `/api/devices/{uuid}/ping` | Uloží ping pre registrované zariadenie. Pošli `battery_percent` ako celé číslo od 0 do 100. |

## Testy

```sh
php artisan test --no-interaction
```

Testy používajú podľa `phpunit.xml` databázu SQLite v pamäti. Pre PHPUnit musí byť zapnuté aj rozšírenie `mbstring`.
