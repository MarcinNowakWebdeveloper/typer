[Powrót](../README.md)

# Backend

Backend aplikacji został zbudowany w oparciu o **PHP 8.4 i Symfony 8**. Aplikacja udostępnia REST API wykorzystywane przez frontend Vue.js.

Głównym celem przy projektowaniu backendu było oddzielenie logiki biznesowej od warstwy HTTP, ograniczenie zależności pomiędzy komponentami oraz przygotowanie kodu w taki sposób, aby można go było łatwo rozszerzać o kolejne funkcjonalności.

## Spis treści

- [Technologie](#technologie)
- [Architektura](#architektura)
- [Strategy Pattern – rozszerzalny system naliczania punktów](#strategy-pattern--rozszerzalny-system-naliczania-punktów)
    - [Recalculation istniejących punktów](#recalculation-istniejących-punktów)
    - [Dlaczego takie rozwiązanie?](#dlaczego-takie-rozwiązanie)
- [Złożona logika biznesowa – Relative Scoring](#złożona-logika-biznesowa--relative-scoring)
- [Enkapsulacja reguł biznesowych](#enkapsulacja-reguł-biznesowych)
- [Domain-specific exceptions](#domain-specific-exceptions)
- [Custom Symfony Security Authenticator](#custom-symfony-security-authenticator)
- [Proces rejestracji i aktywacji użytkownika](#proces-rejestracji-i-aktywacji-użytkownika)
- [API-first architecture](#api-first-architecture)
- [Serialization Groups](#serialization-groups)
- [Repository Pattern](#repository-pattern)
- [Świadome wykorzystanie Doctrine QueryBuilder](#świadome-wykorzystanie-doctrine-querybuilder)
- [PHP Enums zamiast magic strings](#php-enums-zamiast-magic-strings)
- [Strong typing i PHP 8.4](#strong-typing-i-php-84)
- [PHPStan i static analysis](#phpstan-i-static-analysis)
- [Validation](#validation)
- [Internationalization](#internationalization)
- [Database model i integralność danych](#database-model-i-integralność-danych)
- [Database migrations](#database-migrations)
- [Timestampable](#timestampable)
- [Logging](#logging)
- [Code quality](#code-quality)
- [Development environment](#development-environment)
- [Najważniejsze decyzje projektowe](#najważniejsze-decyzje-projektowe)
- [Podsumowanie](#podsumowanie)

## Technologie

| Obszar                  | Technologie                             |
| ----------------------- | --------------------------------------- |
| Language                | PHP 8.4                                 |
| Framework               | Symfony 8                               |
| ORM                     | Doctrine ORM                            |
| Database                | MariaDB                                 |
| Authentication          | Symfony Security / Custom Authenticator |
| Validation              | Symfony Validator                       |
| Serialization           | Symfony Serializer                      |
| Email verification      | SymfonyCasts VerifyEmail                |
| Static analysis         | PHPStan + PHPStan Doctrine              |
| Code style              | PHP-CS-Fixer                            |
| Development environment | DDEV / Docker                           |

---

# Architektura

Backend został podzielony na kilka odpowiedzialności:

```text
HTTP Request
     │
     ▼
 Controller
     │
     ▼
 Service / Manager
     │
     ├──────────────► Domain logic
     │
     ▼
 Repository
     │
     ▼
 Doctrine ORM
     │
     ▼
 MariaDB
```

Kontrolery odpowiadają przede wszystkim za obsługę warstwy HTTP, natomiast logika aplikacyjna została przeniesiona do serwisów i komponentów domenowych.

Dzięki temu kontroler nie musi znać szczegółów dotyczących sposobu zapisywania danych ani implementacji poszczególnych reguł biznesowych.

Przykładowo:

```text
GameController
      │
      ▼
PredictionsService
      │
      ▼
UserGameRepository
      │
      ▼
Doctrine
```

Takie rozdzielenie odpowiedzialności ułatwia testowanie, ponowne wykorzystanie kodu oraz późniejszą rozbudowę aplikacji.

---

# Strategy Pattern – rozszerzalny system naliczania punktów

Jednym z ważniejszych elementów backendu jest system naliczania punktów za typowania.

Aplikacja obsługuje różne sposoby punktowania, dlatego logika naliczania punktów została wydzielona za pomocą **Strategy Pattern**.

Podstawą jest:

```php
interface StrategyInterface
{
    public function getCode(): string;

    public function calculatePoints(...): void;
}
```

Poszczególne algorytmy są implementowane niezależnie:

```text
                StrategyInterface
                       │
          ┌────────────┴────────────┐
          │                         │
          ▼                         ▼
FixedPointsScoringStrategy   RelativeScoringStrategy
          │                         │
          └────────────┬────────────┘
                       ▼
                PointsCalculator
```

`PointsCalculator` nie jest uzależniony od konkretnej implementacji strategii.

Strategie są automatycznie wykrywane przez Symfony Dependency Injection:

```php
#[AutowireIterator('app.scoring_strategy')]
private iterable $strategies;
```

oraz rejestrowane poprzez konfigurację kontenera:

```yaml
_instanceof:
    App\Service\PointCountingStrategy\StrategyInterface:
        tags: ['app.scoring_strategy']
```

Dzięki temu dodanie nowego sposobu punktowania nie wymaga modyfikowania `PointsCalculator`.

Wystarczy utworzyć nową implementację:

```text
NewScoringStrategy
       │
       ▼
StrategyInterface
       │
       ▼
automatically discovered by Symfony
```

### Recalculation istniejących punktów

Mechanizm strategii został przygotowany również z myślą o sytuacji, w której do aplikacji zostanie dodany nowy algorytm naliczania punktów w trakcie rozgrywki.

Sama implementacja nowej strategii nie wystarcza, ponieważ istniejące typowania użytkowników muszą zostać przeliczone zgodnie z nowymi zasadami.

W tym celu przygotowana została dedykowana komenda Symfony CLI umożliwiająca ponowne przeliczenie punktów.

Przykładowy scenariusz:

```text
Dodanie nowej strategii
│
▼
NewScoringStrategy
│
▼
Rejestracja strategii w DI
│
▼
CLI command
│
▼
Recalculation existing predictions
│
▼
Updated points
```

Dzięki temu wprowadzenie nowego sposobu punktowania nie wymaga ręcznej modyfikacji istniejących danych.

Komenda może zostać wykorzystana zarówno podczas wdrażania nowej funkcjonalności, jak i w przypadku zmiany zasad istniejącej strategii.

Jest to szczególnie istotne w aplikacji, w której wynik działania algorytmu jest przechowywany w bazie danych i wpływa na ranking użytkowników.

### Dlaczego takie rozwiązanie?

Pozwala to zachować zasadę **Open/Closed Principle** – istniejący kod pozostaje zamknięty na modyfikacje, a system można rozszerzać poprzez dodawanie nowych implementacji.

Przykładowo w przyszłości można dodać:

```text
FixedPointsScoringStrategy
RelativeScoringStrategy
WorldCupScoringStrategy
ChampionshipScoringStrategy
CustomScoringStrategy
```

bez konieczności zmiany głównego mechanizmu kalkulacji.

To rozwiązanie pozwala również ograniczyć coupling pomiędzy mechanizmem uruchamiającym kalkulację a konkretnymi algorytmami biznesowymi.

---

# Złożona logika biznesowa – Relative Scoring

`RelativeScoringStrategy` jest przykładem logiki biznesowej, która wykracza poza prosty CRUD.

Algorytm analizuje typowania użytkowników względem rzeczywistego wyniku meczu.

Uwzględniane są między innymi:

* dokładny wynik,
* prawidłowy zwycięzca lub remis,
* różnica bramek,
* odległość od rzeczywistej liczby bramek,
* odległość od rzeczywistej różnicy bramek,
* ranking użytkowników,
* grupowanie użytkowników o takim samym wyniku,
* przyznawanie punktów za kolejne pozycje,
* obsługa remisów w rankingu.

Przykładowy fragment algorytmu grupuje typowania według odległości od rzeczywistego wyniku:

```php
$groups[$distance] ??= [];
$groups[$distance][] = $userGame;

ksort($groups);
```

Następnie grupy są przetwarzane według pozycji:

```php
foreach ($groups as $games) {
    ++$rank;

    if ($rank > 3) {
        break;
    }

    foreach ($games as $userGame) {
        $this->createUpdatePoints(
            $type->value,
            4 - $rank,
            $userGame
        );
    }
}
```

Istotne jest tutaj oddzielenie **algorytmu biznesowego** od mechanizmu zapisu punktów.

---

# Enkapsulacja reguł biznesowych

Reguły biznesowe nie są umieszczone wyłącznie w kontrolerach lub serwisach.

Część z nich została umieszczona bezpośrednio w modelu domenowym.

Przykładem jest `UserGame`, który kontroluje m.in. sytuacje związane z istniejącymi punktami oraz zgodnością strategii punktowania.

Zamiast pozwalać, aby dowolny fragment aplikacji zmieniał stan obiektu w niekontrolowany sposób, obiekt sam chroni swoje inwarianty.

Przykładowo:

```php
if (null === $game && !$this->userGamePoints->isEmpty()) {
    throw new PointsAlreadyAwardedException();
}
```

Pozwala to traktować encję nie tylko jako strukturę danych, ale również jako miejsce odpowiedzialne za określone reguły domenowe.

---

# Domain-specific exceptions

W przypadku naruszenia reguł biznesowych wykorzystywane są dedykowane wyjątki zamiast generycznych `Exception`.

Przykłady:

```text
PointsAlreadyAwardedException
MismatchStrategiesException
InvalidDataException
```

Dzięki temu kod wywołujący może rozróżnić konkretne sytuacje biznesowe.

Przykładowo:

```text
MismatchStrategiesException
        │
        └── punkty pochodzą z różnych strategii

PointsAlreadyAwardedException
        │
        └── próba wykonania operacji niedozwolonej
            po naliczeniu punktów
```

Pozwala to również zachować czytelniejszą semantykę kodu i uniknąć sytuacji, w której wszystkie błędy są reprezentowane przez jeden ogólny wyjątek.

---

# Custom Symfony Security Authenticator

Uwierzytelnianie API zostało zrealizowane za pomocą własnego Symfony Authenticatora.

Zamiast korzystać wyłącznie z gotowego mechanizmu formularzowego, aplikacja posiada własną implementację:

```php
class AppAuthenticator extends AbstractAuthenticator
```

Proces logowania wygląda następująco:

```text
POST /api/login
       │
       ▼
AppAuthenticator
       │
       ├── odczyt JSON payload
       │
       ├── walidacja danych
       │
       ├── wyszukanie użytkownika
       │
       ├── sprawdzenie potwierdzenia email
       │
       ├── sprawdzenie aktywacji konta
       │
       ▼
     Passport
       │
       ▼
PasswordCredentials
       │
       ▼
Authenticated User
```

Wykorzystywane są mechanizmy Symfony Security, takie jak:

* `AbstractAuthenticator`,
* `Passport`,
* `UserBadge`,
* `PasswordCredentials`.

Dzięki temu mechanizm uwierzytelniania pozostaje zintegrowany ze standardowym systemem Security Symfony, jednocześnie dostosowując sposób logowania do API.

---

# Proces rejestracji i aktywacji użytkownika

Rejestracja użytkownika jest procesem wieloetapowym.

Samo utworzenie konta nie pozwala jeszcze na zalogowanie.

```text
Registration
     │
     ▼
Email verification
     │
     ▼
Verified account
     │
     ▼
Administrator activation
     │
     ▼
Active account
     │
     ▼
Login
```

W modelu użytkownika rozdzielone zostały dwa niezależne stany:

```php
private bool $isVerified = false;
private bool $isActive = false;
```

Dzięki temu można niezależnie określić:

* czy użytkownik potwierdził adres email,
* czy administrator aktywował jego konto.

Authenticator podczas logowania weryfikuje oba warunki.

Jest to przykład modelowania procesu biznesowego za pomocą jawnych stanów zamiast opierania całej logiki na jednym polu.

---

# API-first architecture

Backend został zaprojektowany jako API dla niezależnego frontendu Vue.

```text
┌─────────────────────┐
│       Vue 3         │
│      Frontend       │
└──────────┬──────────┘
           │
           │ HTTP / JSON
           ▼
┌─────────────────────┐
│    Symfony API      │
├─────────────────────┤
│ Security            │
│ Controllers         │
│ Services            │
│ Domain logic        │
│ Repositories        │
│ Doctrine ORM        │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│       MariaDB       │
└─────────────────────┘
```

Backend nie jest więc bezpośrednio związany z warstwą prezentacji.

Ta separacja pozwala niezależnie rozwijać frontend i backend oraz potencjalnie wykorzystać to samo API przez inne klienty.

---

# Serialization Groups

Do kontrolowania reprezentacji danych zwracanych przez API wykorzystywane są Symfony Serializer Groups.

Przykład:

```php
#[Groups(['ranking:list'])]
```

oraz grupy przeznaczone dla konkretnych przypadków użycia:

```php
#[Groups([
    'admin:game:list',
    'admin:stage:group:view',
])]
```

Dzięki temu ta sama encja może być serializowana w różny sposób w zależności od kontekstu.

Pozwala to ograniczyć:

* ilość przesyłanych danych,
* przypadkowe ujawnianie pól,
* konieczność tworzenia osobnych struktur dla każdego prostego endpointu.

---

# Repository Pattern

Zapytania do bazy danych zostały wydzielone do dedykowanych repozytoriów.

Przykładowo `GameRepository` zawiera wyspecjalizowane metody związane z pobieraniem danych meczów i typowań:

```text
GameRepository
├── getFirstGame()
├── getUserGamesByGamesIdsAndUserId()
└── findWithGoalsSet()
```

Dzięki temu kontrolery i serwisy nie muszą zawierać szczegółów dotyczących sposobu budowania zapytań Doctrine.

---

# Świadome wykorzystanie Doctrine QueryBuilder

W przypadku bardziej złożonych operacji wykorzystywany jest Doctrine QueryBuilder.

Przykładowo dane mogą być pobierane zbiorczo za pomocą `IN` oraz odpowiednich `JOIN`, zamiast wykonywania osobnego zapytania dla każdego elementu.

Schematycznie:

```text
NIE:

for each game
    query database

TAK:

query database
    WHERE game.id IN (...)
    JOIN required relations
```

Takie podejście pozwala ograniczać liczbę zapytań do bazy i jest szczególnie istotne przy operacjach wykonywanych na wielu użytkownikach i meczach.

---

# PHP Enums zamiast magic strings

Typy strategii i typy przyznawanych punktów są reprezentowane za pomocą PHP Enums.

Pozwala to zastąpić rozsiane po kodzie wartości tekstowe przez jawnie zdefiniowany model:

```php
RelativeScoringStrategyTypeEnum
FixedPointsScoringStrategyTypesEnum
```

Zamiast:

```php
'correct_score'
```

kod może korzystać z:

```php
SomePointsTypeEnum::CORRECT_SCORE->value
```

Daje to lepszą kontrolę typów oraz ogranicza możliwość wprowadzenia literówki w nazwie wartości biznesowej.

---

# Strong typing i PHP 8.4

Projekt wykorzystuje nowoczesne możliwości PHP 8.4, między innymi:

* strict typing,
* typed properties,
* constructor property promotion,
* readonly classes,
* typed class constants,
* enums,
* nullable i union types,
* Attributes.

Przykładowo:

```php
readonly class PointsCalculator
```

oraz:

```php
private const string CODE = 'fixed_points_scoring';
```

Silne typowanie jest również wspierane przez szczegółowe PHPDoc, szczególnie w przypadku złożonych struktur tablic.

---

# PHPStan i static analysis

Projekt wykorzystuje **PHPStan** oraz rozszerzenia związane z Doctrine do statycznej analizy kodu.

PHPStan pozwala wykrywać problemy z typami i część błędów logicznych bez uruchamiania aplikacji.

W kodzie wykorzystywane są również PHPDoc array shapes.

Przykład:

```php
/**
 * @param array{
 *     stageGroupId: int,
 *     date: string,
 *     time: string,
 *     homeTeamId: int,
 *     awayTeamId: int,
 *     homeGoals: ?int,
 *     awayGoals: ?int
 * } $data
 */
```

Dzięki temu struktura danych przekazywanych pomiędzy komponentami jest jawnie opisana, a analiza statyczna może wykrywać niezgodności typów i brakujące pola.

Jest to szczególnie przydatne w aplikacjach, w których część danych pochodzi z dynamicznych struktur JSON.

---

# Validation

Dane wejściowe mogą być walidowane za pomocą Symfony Validator.

Przykładowo model żądania rejestracji definiuje wymagania dotyczące:

```text
name
email
password
```

oraz wykorzystuje constrainty takie jak:

```php
#[Assert\NotBlank]
#[Assert\Length(min: 3)]
#[Assert\Email]
```

Pozwala to przenieść część reguł walidacyjnych poza kontroler i wykorzystać standardowy mechanizm Symfony.

---

# Internationalization

Backend korzysta z Symfony Translation.

Komunikaty są przechowywane w osobnych domenach tłumaczeń, m.in.:

```text
errors
entities
emails
security
```

Zamiast hardcodowania komunikatów w kodzie:

```php
$this->translator->trans(...)
```

pozwala pobierać odpowiednią wersję komunikatu z systemu tłumaczeń.

Dzięki temu warstwa backendowa jest przygotowana do obsługi wielu języków.

---

# Database model i integralność danych

Relacje pomiędzy encjami są modelowane za pomocą Doctrine ORM.

Wykorzystywane są m.in.:

* `OneToMany`,
* `ManyToOne`,
* `ManyToMany`,
* cascading,
* `orphanRemoval`,
* foreign keys,
* indeksy,
* constraints.

Przykładowo:

```php
#[ORM\JoinColumn(
    nullable: false,
    onDelete: 'CASCADE'
)]
```

oraz:

```php
cascade: ['persist', 'remove'],
orphanRemoval: true
```

Część reguł dotyczących integralności danych jest więc zabezpieczona nie tylko w kodzie PHP, ale również na poziomie modelu persistence.

---

# Database migrations

Zmiany schematu bazy danych są zarządzane za pomocą Doctrine Migrations.

Każda zmiana struktury bazy jest reprezentowana jako osobna migracja posiadająca:

```php
public function up(): void
```

oraz:

```php
public function down(): void
```

Pozwala to:

* śledzić historię zmian schematu,
* odtwarzać strukturę bazy,
* wdrażać kolejne wersje aplikacji w kontrolowany sposób,
* odwracać zmiany w przypadku potrzeby rollbacku.

---

# Timestampable

Dla wybranych encji wykorzystywane jest Doctrine Extensions / Timestampable.

Pozwala to automatycznie zarządzać polami:

```text
createdAt
updatedAt
```

bez konieczności ręcznego ustawiania ich w każdym miejscu aplikacji.

---

# Logging

W aplikacji wykorzystywany jest standardowy PSR-3 `LoggerInterface`.

Logowanie jest stosowane m.in. podczas obsługi błędów procesów takich jak:

* uwierzytelnianie,
* rejestracja,
* potwierdzanie adresu email,
* naliczanie punktów.

Logowanie pozostaje niezależne od konkretnej implementacji loggera dzięki wykorzystaniu standardowego interfejsu PSR.

---

# Code quality

Oprócz samego działania aplikacji istotna była również jakość i utrzymywalność kodu.

W projekcie wykorzystywane są:

* **PHPStan** – static analysis,
* **PHPStan Doctrine** – analiza kodu korzystającego z Doctrine,
* **PHP-CS-Fixer** – automatyzacja standardu formatowania,
* **PHPUnit** – przygotowanie infrastruktury do testów,
* **strict typing**,
* **PHPDoc array shapes**,
* **dependency injection**,
* **SOLID principles** tam, gdzie mają uzasadnienie w strukturze domeny.

Celem nie było stosowanie wzorców projektowych dla samych wzorców, ale wykorzystanie ich tam, gdzie rozwiązują konkretny problem projektowy.

---

# Development environment

Środowisko developerskie zostało przygotowane z wykorzystaniem **DDEV / Docker**.

Pozwala to odizolować środowisko aplikacji od konfiguracji systemu hosta i zapewnić powtarzalne środowisko dla:

```text
PHP
MariaDB
Symfony
Frontend
Mail / development services
```

Dzięki temu uruchomienie projektu nie wymaga ręcznej konfiguracji całego środowiska PHP na komputerze dewelopera.

---

# Najważniejsze decyzje projektowe

Najważniejsze elementy backendu można podsumować następująco:

| Obszar                  | Rozwiązanie                                                |
| ----------------------- | ---------------------------------------------------------- |
| Architektura            | rozdzielenie Controller / Service / Repository             |
| Algorytmy biznesowe     | Strategy Pattern                                           |
| Dependency Injection    | Symfony DI + `AutowireIterator`                            |
| Domain logic            | reguły biznesowe enkapsulowane w odpowiednich komponentach |
| Błędy biznesowe         | custom domain exceptions                                   |
| Authentication          | własny Symfony Authenticator                               |
| Registration workflow   | email verification + administrator activation              |
| API                     | REST / JSON                                                |
| Serializacja            | Symfony Serializer Groups                                  |
| Persistence             | Doctrine ORM                                               |
| Database changes        | Doctrine Migrations                                        |
| Type safety             | PHP 8.4 + PHPStan + PHPDoc                                 |
| Enumeracje domenowe     | PHP Enums                                                  |
| Logging                 | PSR-3 LoggerInterface                                      |
| Development environment | DDEV / Docker                                              |

---

# Podsumowanie

Backend aplikacji **Typer** został zaprojektowany nie tylko jako warstwa CRUD dla aplikacji frontendowej, ale jako przykład wykorzystania Symfony do budowy aplikacji zawierającej rzeczywistą logikę biznesową.

Najważniejsze elementy, które chciałem pokazać w projekcie, to:

* projektowanie rozszerzalnych mechanizmów biznesowych,
* wykorzystanie Strategy Pattern,
* dependency injection i automatyczne tagowanie usług,
* separacja odpowiedzialności pomiędzy warstwami,
* modelowanie reguł domenowych,
* tworzenie własnego mechanizmu uwierzytelniania API,
* bezpieczny proces rejestracji i weryfikacji użytkownika,
* świadome wykorzystanie Doctrine,
* ograniczanie liczby zapytań do bazy,
* silne typowanie i static analysis,
* wykorzystanie nowoczesnych możliwości PHP,
* przygotowanie backendu jako niezależnego API dla aplikacji SPA.

Projekt ma dzięki temu pełnić nie tylko funkcję działającej aplikacji, ale również **praktycznej prezentacji sposobu projektowania i implementowania backendu w PHP/Symfony**.
