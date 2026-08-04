[Powrót](../README.md)

## Frontend

Frontend aplikacji **Typer** został zbudowany jako niezależna aplikacja SPA w oparciu o **Vue 3**. Komunikuje się z backendem Symfony 8 poprzez REST API.

Głównym założeniem było rozdzielenie warstwy prezentacji od backendu oraz stworzenie struktury, która pozwala rozwijać kolejne widoki i funkcjonalności bez przenoszenia logiki biznesowej do komponentów UI.

## Spis treści

- [Technologie](#technologie)
- [Architektura frontendu](#architektura-frontendu)
- [Composition API](#composition-api)
- [Modułowa organizacja funkcjonalności](#modułowa-organizacja-funkcjonalności)
- [Vue Router i kontrola dostępu](#vue-router-i-kontrola-dostępu)
- [Lazy loading widoków](#lazy-loading-widoków)
- [Pinia — centralizacja stanu](#pinia--centralizacja-stanu)
- [Inicjalizacja użytkownika](#inicjalizacja-użytkownika)
- [Wydzielona warstwa komunikacji z API](#wydzielona-warstwa-komunikacji-z-api)
- [Composables — wydzielanie logiki reaktywnej](#composables--wydzielanie-logiki-reaktywnej)
- [Persystencja ustawień](#persystencja-ustawień)
- [Globalny system Toast](#globalny-system-toast)
- [Własna dyrektywa Vue](#własna-dyrektywa-vue)
- [Internacjonalizacja](#internacjonalizacja)
- [Vite](#vite)
- [Development i produkcja](#development-i-produkcja)
- [ESLint + Prettier](#eslint--prettier)
- [Bootstrap jako warstwa UI](#bootstrap-jako-warstwa-ui)
- [Responsywność](#responsywność)
- [Wizualizacja danych](#wizualizacja-danych)
- [Separacja komponentów](#separacja-komponentów)
- [Layout jako osobna warstwa](#layout-jako-osobna-warstwa)
- [Podejście do odpowiedzialności komponentów](#podejście-do-odpowiedzialności-komponentów)
- [Przykładowy przepływ danych](#przykładowy-przepływ-danych)
- [Najważniejsze decyzje projektowe](#najważniejsze-decyzje-projektowe)
- [Podsumowanie](#podsumowanie)

## Technologie

| Obszar                | Technologie                  |
| --------------------- |------------------------------|
| Framework             | Vue 3                        |
| Build tool            | Vite                         |
| State management      | Pinia                        |
| Routing               | Vue Router                   |
| UI framework          | Bootstrap 5                  |
| Icons                 | Bootstrap Icons              |
| Charts                | Apache ECharts / vue-echarts |
| HTTP                  | Axios                        |
| Internationalization  | Vue I18n                     |
| Language              | JavaScript                   |
| Backend communication | REST API / JSON              |

---

## Architektura frontendu

Jedną z ważniejszych decyzji było odejście od organizowania całego kodu wyłącznie według typu pliku.

Zamiast struktury:

components/
views/
services/
...

funkcjonalności zostały pogrupowane przede wszystkim według odpowiedzialności biznesowej:

```text
assets/vue/
├── components/
├── composables/
├── directive/
├── layouts/
├── modules/
│   ├── admin/
│   ├── auth/
│   ├── charts/
│   ├── dashboard/
│   ├── i18n/
│   ├── regulations/
│   └── user/
├── router/
├── services/
├── stores/
└── styles/
```

Taki podział pozwala utrzymać kod związany z konkretną funkcjonalnością w jednym miejscu. Przykładowo część administracyjna oraz część użytkownika mają własne moduły, a moduły posiadają własne komponenty, strony i tłumaczenia.

Dlaczego taki podział?

Przy rozbudowywaniu aplikacji łatwiej znaleźć kod odpowiedzialny za daną funkcjonalność:

```text
modules/user/stage/
modules/user/joker/
modules/user/profile/
modules/admin/users/
modules/admin/config/
```
Dzięki temu struktura projektu odzwierciedla strukturę funkcjonalną aplikacji, a nie tylko technologię.

---
## Composition API

Komponenty Vue wykorzystują <script setup> oraz Composition API.

Przykładem jest główny komponent aplikacji, który korzysta ze store'a autoryzacji i na tej podstawie decyduje, czy należy wyświetlić loader, czy właściwą zawartość aplikacji.

```vue
<script setup>
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
</script>

<template>
    <AppLoader v-if="!auth.initialized" />
    <router-view v-else />
</template>
```

Pozwala to oddzielić odpowiedzialność:

* App.vue odpowiada za najwyższy poziom aplikacji,
* store odpowiada za stan autoryzacji,
* router odpowiada za nawigację,
* poszczególne strony odpowiadają za konkretne przypadki użycia.

---
## Modułowa organizacja funkcjonalności

Frontend został podzielony na moduły odpowiadające funkcjonalnościom aplikacji.

Przykładowo:
```text
modules/
├── admin/
│   ├── config/
│   └── users/
├── auth/
├── dashboard/
├── charts/
├── regulations/
└── user/
├── joker/
├── profile/
└── stage/
```

Pozwala to uniknąć sytuacji, w której wszystkie komponenty i widoki trafiają do jednego katalogu.

Jest to szczególnie istotne w aplikacji, która posiada zarówno część użytkownika, jak i panel administracyjny.

---
## Vue Router i kontrola dostępu

Routing został zorganizowany centralnie przy użyciu Vue Router.

Trasy wykorzystują metadane określające wymagania dostępu:

```
meta: {
    requiresAuth: true,
}
```
oraz:
```
meta: {
    requiresAdmin: true,
}
```
Dzięki temu informacja o wymaganiach dostępu znajduje się bezpośrednio przy definicji trasy, zamiast być powielana w poszczególnych komponentach.

---
## Navigation Guard

Kontrola dostępu została zrealizowana poprzez globalny beforeEach.

Przed przejściem na stronę router:

* sprawdza, czy stan użytkownika został już zainicjalizowany,
* w razie potrzeby pobiera aktualnego użytkownika,
* sprawdza wymaganie autoryzacji,
* sprawdza uprawnienia administratora,
* blokuje dostęp do stron przeznaczonych wyłącznie dla niezalogowanych użytkowników.

W efekcie logika autoryzacji nie musi być implementowana osobno w każdej stronie.

```text
Route
│
▼
Navigation Guard
│
├── requiresAuth ──► sprawdzenie użytkownika
│
├── requiresAdmin ─► sprawdzenie roli
│
└── guestOnly ────► sprawdzenie czy użytkownik jest wylogowany
```

---
## Lazy loading widoków

Nie wszystkie strony administracyjne są ładowane od razu.

Część tras wykorzystuje dynamiczny import:

component: () =>
import('@/modules/admin/config/TeamsPage.vue')

Dotyczy to między innymi stron konfiguracji zespołów, grup, etapów oraz gier.

Pozwala to wykorzystać mechanizm code splittingu Vite/Rollup i nie ładować całego kodu aplikacji podczas pierwszego wejścia użytkownika.

W praktyce oznacza to podejście:

```
Initial load
│
├── kod potrzebny do uruchomienia aplikacji
│
└── pozostałe moduły ładowane w razie potrzeby
```

Jest to szczególnie sensowne dla panelu administracyjnego, z którego korzysta znacznie mniejsza część użytkowników.

---
## Pinia — centralizacja stanu

Globalny stan aplikacji został wydzielony do store'ów Pinia.

Obecnie wykorzystywane są między innymi:
```
stores/
├── auth.js
├── settings.js
└── toast.js
```

---
## Auth Store

Store autoryzacji odpowiada za:

* przechowywanie aktualnego użytkownika,
* określenie, czy użytkownik jest zalogowany,
* określenie, czy użytkownik posiada rolę administratora,
* pobranie aktualnego użytkownika,
* logowanie,
* wylogowanie.

Przykładowe computed/gettery:
```vue
isLogged: (state) => !!state.user,
```
```vue
isAdmin: (state) =>
state.user?.roles?.includes('ROLE_ADMIN') ?? false,
```

Dzięki temu komponenty nie muszą implementować własnej logiki sprawdzania roli użytkownika.

---
## Inicjalizacja użytkownika

Jednym z istotnych elementów architektury jest rozróżnienie:

```text
initialized
```

od:

```text
isLogged
```

initialized informuje, czy aplikacja zakończyła próbę ustalenia aktualnego użytkownika.

Dzięki temu aplikacja nie musi chwilowo traktować użytkownika jako niezalogowanego podczas oczekiwania na odpowiedź API.

Schemat wygląda następująco:

```text
Start aplikacji
│
▼
initialized = false
│
▼
loadUser()
│
├── użytkownik znaleziony
│       └── user = ...
│
└── brak sesji
└── user = null
│
▼
initialized = true
│
▼
router-view
```

Mechanizm ten jest wykorzystywany również przez router, który przed wykonaniem kontroli dostępu upewnia się, że stan autoryzacji został zainicjalizowany.

---
## Wydzielona warstwa komunikacji z API

Komunikacja z backendem została wydzielona do osobnej instancji Axios:

```text
services/
└── api.js
```

Instancja posiada wspólną konfigurację:

```vue
const api = axios.create({
    baseURL: '/',
    withCredentials: true,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
    },
})
```



Dzięki temu komponenty nie muszą za każdym razem konfigurować Axios od nowa.

Zamiast:

```vue
axios.get(...)
```

cała aplikacja korzysta z jednego, centralnego klienta:
```vue
api.get(...)
api.post(...)
api.put(...)
api.delete(...)
```

Jest to również dobre miejsce do późniejszego dodania wspólnej obsługi:

* błędów HTTP,
* interceptors,
* odświeżania sesji,
* logowania requestów,
* wspólnych nagłówków.

---
## Composables — wydzielanie logiki reaktywnej

Powtarzalna logika została wydzielona do composables.

Obecna struktura zawiera między innymi:
```text
composables/
├── flagRichRegistry.js
├── textColor.js
├── useCountdown.js
└── useTeams.js
```

---
### Przykład — useCountdown

Mechanizm odliczania czasu został wyjęty poza komponenty do:
```text
composables/useCountdown.js
```

Composable utrzymuje wspólny czas i udostępnia funkcję:
```text
getCountdown(dateString)
```

zwracającą:
```text
{
    days,
    hours,
    minutes,
    seconds,
    finished
}
```



Dzięki temu komponent wykorzystujący odliczanie nie musi znać szczegółów obliczania różnicy pomiędzy datami.

Jest to przykład zasady:

komponent powinien przede wszystkim opisywać UI i interakcje, a nie zawierać całą logikę biznesową pomocniczą.

---
## Persystencja ustawień

Niektóre ustawienia użytkownika są przechowywane w localStorage.

Store ustawień korzysta z reaktywności Vue:

```vue
const pointCountingStrategies = ref(localStorage.getItem('pointCountingStrategies') ?? null)

watch(pointCountingStrategies, (value) => {
    localStorage.setItem(
        'pointCountingStrategies',
        value
    )
})
```

Dzięki temu:

```text
zmiana ustawienia
│
▼
Vue ref
│
▼
watch()
│
▼
localStorage
```

Nie trzeba ręcznie wywoływać zapisu w każdym miejscu, w którym zmieniana jest wartość.

---
## Globalny system Toast

Powiadomienia użytkownika zostały również wydzielone do osobnego store'a:
```text
stores/toast.js
```

Store zarządza kolekcją komunikatów i posiada mechanizm automatycznego usuwania powiadomienia po 5 sekundach.

Dzięki temu dowolna część aplikacji może zgłosić komunikat bez bezpośredniego zarządzania komponentem odpowiedzialnym za jego wyświetlenie.

Architektura wygląda następująco:

```text
Dowolny komponent
│
▼
Toast Store
│
▼
AppToast
│
▼
UI
```

Jest to przykład rozdzielenia źródła stanu od prezentacji stanu.

---
## Własna dyrektywa Vue

Bootstrap Tooltip został zintegrowany z Vue poprzez własną dyrektywę:
```text
directive/
└── tooltip.js
```

Dyrektywa tworzy instancję Bootstrap Tooltip po zamontowaniu elementu:

```vue
mounted(el) {
    new Tooltip(el, {
        trigger: 'click',
    })
}
```

Dzięki temu w komponentach można korzystać z deklaratywnego API Vue zamiast ręcznie inicjalizować tooltipy w każdym komponencie.

Dyrektywa jest rejestrowana globalnie podczas uruchamiania aplikacji:
```vue
app.directive('tooltip', tooltip)
```

---
## Internacjonalizacja

Aplikacja wykorzystuje Vue I18n.

Tłumaczenia zostały pogrupowane według modułów funkcjonalnych:

```text
modules/
├── admin/
│   ├── users/locales/
│   └── config/locales/
├── auth/locales/
├── dashboard/locales/
├── user/
│   ├── joker/locales/
│   ├── profile/locales/
│   └── stage/locales/
└── charts/locales/
```

Dodatkowo komponenty posiadają własną przestrzeń tłumaczeń.

Pozwala to uniknąć jednego dużego pliku zawierającego wszystkie teksty aplikacji.
Taki podział ułatwia późniejsze dodawanie kolejnych języków oraz rozwijanie poszczególnych modułów.

---
## Vite

Vite odpowiada za development server oraz produkcyjny build frontendu.

Konfiguracja zawiera między innymi:

* Vue plugin,
* integrację z Symfony,
* SVG loader,
* alias @,
* osobny katalog buildów,
* konfigurację development servera,
* HMR,
* CORS.

Build trafia do:
```text
public/build
```

a głównym entry pointem jest:
```text
assets/vue/main.js
```

---
## Alias:

```vue
'@': path.resolve(__dirname, './assets/vue')
```

pozwala stosować importy niezależne od głębokości aktualnego pliku:
```vue
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth'
```

zamiast:
```vue
import api from '../../../services/api'
```

To poprawia czytelność i ułatwia przenoszenie plików.

---
## Development i produkcja

Ta sama konfiguracja Vite obsługuje zarówno development, jak i build produkcyjny.

Dostępne są między innymi:
```text
npm run dev
```

oraz:

```text
npm run build
```
Dodatkowo projekt posiada osobne komendy dotyczące jakości kodu:

```text
npm run lint
npm run lint:fix
npm run format
```

Dzięki temu formatowanie oraz kontrola jakości kodu są częścią standardowego workflow projektu.

---
## ESLint + Prettier

Frontend wykorzystuje dwa uzupełniające się narzędzia:

**ESLint**

Odpowiada za analizę kodu JavaScript/Vue i wykrywanie potencjalnych problemów.

**Prettier**

Odpowiada za jednolite formatowanie kodu.

---
## Bootstrap jako warstwa UI

Bootstrap 5 został wykorzystany przede wszystkim jako baza layoutu oraz responsywności.

Jednocześnie nie wszystkie elementy interfejsu są traktowane jako gotowe komponenty Bootstrap.

W projekcie znajduje się własna warstwa CSS:


```text
styles/
└── app.css
```

Pozwala to połączyć gotowy system grid/utilities z własnymi wymaganiami wizualnymi aplikacji.

---
## Responsywność

Interfejs został zaprojektowany z myślą zarówno o dużych ekranach, jak i urządzeniach mobilnych.

W praktyce oznaczało to wykorzystanie:

* Bootstrap Grid,
* utility classes,
* breakpointów,
* własnych reguł CSS,
* responsywnych rozmiarów elementów,
* alternatywnych sposobów prezentowania części interfejsu na małych ekranach.

Szczególnie istotne było dostosowanie aplikacji sportowej do urządzeń mobilnych, ponieważ typowy użytkownik może korzystać z aplikacji podczas meczu, a więc przede wszystkim z telefonu.

---
## Wizualizacja danych

Do prezentowania danych statystycznych wykorzystany został:

```text
ECharts
+
vue-echarts
```

Biblioteki znajdują się bezpośrednio w zależnościach frontendu.

Pozwala to prezentować dane dotyczące między innymi wyników i punktacji w formie wykresów zamiast ograniczać interfejs wyłącznie do tabel.

---
## Separacja komponentów

Komponenty zostały wydzielone według odpowiedzialności.

Przykładowo:

```text
components/
├── navigation/
├── loaders/
├── toasts/
...
```

Natomiast strony należące do konkretnych funkcjonalności znajdują się w:

```text
modules/
```

Pozwala to odróżnić:

**Reusable UI components**

od:

**Business/application pages**

Jest to istotne przy rozwijaniu aplikacji, ponieważ komponent wielokrotnego użytku nie jest związany bezpośrednio z konkretnym widokiem biznesowym.

---
## Layout jako osobna warstwa

Główny layout aplikacji został wydzielony do:


```text
layouts/
```

Routing może więc umieszczać strony aplikacji we wspólnym layoucie, podczas gdy strony takie jak logowanie czy rejestracja mogą funkcjonować poza główną częścią aplikacji.

Dzięki temu navbar, główny kontener i pozostałe elementy wspólnego layoutu nie muszą być powielane w każdej stronie.

---
## Podejście do odpowiedzialności komponentów

Jedną z zasad stosowanych podczas tworzenia frontendu było ograniczanie odpowiedzialności pojedynczego komponentu.

Logika została rozdzielona pomiędzy:


| Odpowiedzialność            | Warstwa             |
|:----------------------------|:--------------------|
| UI                          | Vue components      |
| Strony	                      | modules/*/*Page.vue |
| Routing	                     | router/             |
| Stan globalny	               | stores/             |
| Komunikacja z backendem	     | services/           |
| Reużywalna logika reaktywna	 | composables/        |
| Tłumaczenia	                 | locales/            |
| Globalne dyrektywy          | directive/          |
| Layout	                      | layouts/            |
| Style globalne	              | styles/             |

Dzięki temu poszczególne warstwy mają jasno określone zadania.

---
## Przykładowy przepływ danych

Przykładowy przepływ podczas logowania wygląda następująco:

```text
LoginPage
│
▼
Auth Store
│
▼
Axios API Service
│
▼
Symfony API
│
▼
Auth Store.loadUser()
│
▼
user state
│
├── Router Guard
│
└── UI
```
Każdy element ma tutaj inną odpowiedzialność:

* komponent zbiera dane od użytkownika,
* store zarządza stanem,
* service odpowiada za komunikację HTTP,
* backend wykonuje operację,
* router kontroluje dostęp,
* komponenty reagują na zmianę stanu.

---
## Najważniejsze decyzje projektowe

Najważniejsze rozwiązania zastosowane w frontendzie:

#### 1. Feature-oriented architecture

Kod jest grupowany według funkcjonalności, a nie tylko według rozszerzenia pliku.

#### 2. Centralny stan aplikacji

Pinia odpowiada za dane, które muszą być dostępne w wielu miejscach aplikacji.

#### 3. Centralny klient API

Axios został skonfigurowany w jednym miejscu.

#### 4. Route guards

Autoryzacja i uprawnienia są kontrolowane na poziomie routera.

#### 5. Lazy loading

Część modułów administracyjnych jest ładowana dopiero podczas wejścia na daną stronę.

#### 6. Reusable composables

Powtarzalna logika została wydzielona z komponentów.

#### 7. Modułowe tłumaczenia

Tłumaczenia są powiązane z funkcjonalnościami aplikacji.

#### 8. Globalne mechanizmy UI

Toast i tooltip są dostępne poprzez wspólną warstwę aplikacji.

#### 9. Code quality tooling

ESLint i Prettier są częścią projektu, a nie tylko opcjonalnymi narzędziami lokalnymi.

#### 10. Responsive UI

Interfejs został przygotowany z uwzględnieniem urządzeń mobilnych.

---
## Podsumowanie

Frontend aplikacji Typer nie miał być jedynie demonstracją znajomości Vue.

Projekt miał pokazać sposób myślenia charakterystyczny dla większych aplikacji:

* dzielenie systemu na odpowiedzialności,
* unikanie duplikacji,
* centralizację wspólnych mechanizmów,
* reużywanie logiki,
* kontrolę dostępu,
* lazy loading,
* separację UI od logiki aplikacji,
* świadome zarządzanie stanem,
* przygotowanie projektu do dalszego rozwoju.

W rezultacie frontend jest zorganizowany tak, aby dodanie kolejnej funkcjonalności nie wymagało ingerowania w jedną dużą, centralną strukturę aplikacji.

* Technologie i narzędzia
* Vue 3
* Composition API
* Vue Router
* Pinia
* Axios
* Vue I18n
* Bootstrap 5
* Bootstrap Icons
* ECharts
* vue-echarts
* Vite
* Sass
* ESLint
* Prettier

Konfiguracja projektu znajduje się w package.json, a konfiguracja Vite w vite.config.js.


