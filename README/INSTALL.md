# Instalacja dev

## 1. Klonowanie repozytorium

```bash
git clone https://github.com/MarcinNowakWebdeveloper/typer.git
cd typer
```

## 2. Odpalenie kontenera

```bash
ddev start
```

## 3. Instalacja zależności PHP

Wejście do kontenera:

```bash
ddev ssh
```

instalacja:

```bash
composer install
```

## 4. Instalacja zależności Node

```bash
npm install
```

## 5. Konfiguracja środowiska

Skopiuj plik:

```bash
cp .env .env.local
```

Następnie skonfiguruj:

* połączenie z bazą danych
* JWT
* adres aplikacji
* mailer
* ustawienia aplikacji (App)

## 6. Utworzenie bazy danych

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

## 7. Uruchomienie frontendu

```bash
npm run dev
```
