# Frontend

Projekt został napisany od podstaw jako aplikacja SPA wykorzystująca Vue 3.

## Architektura

Frontend został zorganizowany modułowo. Poszczególne sekcje aplikacji posiadają własne komponenty, strony oraz composables odpowiedzialne za logikę biznesową.

W projekcie wykorzystano:

* Vue 3 Composition API
* `<script setup>`
* modułową strukturę katalogów
* lazy loading tras
* dynamiczny import komponentów
* composables do wydzielania logiki
* komunikację z REST API przy użyciu Axios

## Routing

* Vue Router
* zagnieżdżone trasy
* route guards
* przekazywanie parametrów
* lazy loading stron

## Zarządzanie stanem

Pinia wykorzystywana jest do:

* autoryzacji użytkownika
* przechowywania ustawień aplikacji
* współdzielenia danych pomiędzy komponentami

## Komunikacja z API

* Axios
* centralna konfiguracja klienta HTTP
* obsługa JWT
* globalna obsługa błędów

## Komponenty

Projekt zawiera wiele wielokrotnego użytku komponentów, m.in.:

* formularze
* autocomplete
* upload plików
* paginację
* modalne okna dialogowe
* ładowanie danych (loadery)
* tabelki
* elementy dashboardu

## Internacjonalizacja

Zastosowano Vue I18n.

Cechy:

* wielojęzyczność
* tłumaczenia ładowane modułowo
* rozdzielenie tłumaczeń według ekranów aplikacji

## Wizualizacja danych

Do prezentacji statystyk wykorzystano ECharts.

Przykłady:

* wykresy rankingów
* wykresy punktacji
* statystyki użytkowników

## Stylowanie

* Bootstrap 5
* Bootstrap Icons
* własne komponenty UI
* responsywny układ
* wykorzystanie Flexbox

## Dobre praktyki

W projekcie zastosowano między innymi:

* Composition API
* podział logiki na composables
* wielokrotne wykorzystanie komponentów
* wydzielenie warstwy komunikacji z API
* czytelny podział odpowiedzialności komponentów
* lazy loading
* modularną strukturę katalogów
* zarządzanie stanem za pomocą Pinia
* obsługę asynchroniczności z async/await
* integrację z backendem Symfony poprzez REST API
