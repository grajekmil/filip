# Pełna Dokumentacja Techniczna Systemu Wypożyczalni Rowerów

Ten dokument opisuje wszystkie pliki stworzone w ramach projektu "System Wypożyczalni Rowerów". Projekt składa się z trzech głównych warstw: Bazy Danych (SQL), Aplikacji Webowej (PHP) oraz Infrastruktury (Docker).

---

## 1. Warstwa Bazy Danych (SQL)

Pliki odpowiedzialne za strukturę i logikę danych w bazie `wypozyczalnia`.

### 📄 `procedura_smart_insert.sql`
*   **Co to jest:** Plik definiujący Procedurę Składowaną `DodajRowerZMarka`.
*   **Jak działa:** To "inteligentna" funkcja wewnątrz bazy danych. Pozwala dodać rower podając nazwę marki, model, kategorię i numer stacji. Procedura sama sprawdza, czy te elementy istnieją – jeśli nie, to je tworzy w odpowiednich tabelach, a na końcu dodaje rower.
*   **Parametry procedury:**
    1.  `p_nazwa_marki` - Nazwa marki (VARCHAR 15)
    2.  `p_kraj_marki` - Kraj pochodzenia marki (VARCHAR 20)
    3.  `p_model` - Model roweru (VARCHAR 20)
    4.  `p_numer_stacji` - Numer stacji (INT)
    5.  `p_nazwa_kategorii` - Nazwa kategorii (VARCHAR 40)
*   **Przykład użycia:** `CALL DodajRowerZMarka('Giant', 'Tajwan', 'Roam 1', 2, 'Górski');`
*   **Kiedy używana:** Aktywnie wykorzystywana w formularzu dodawania rowerów (`add_bike.php`).

### 📄 `dokumentacja_zapytan.md`
*   **Co to jest:** Szczegółowy opis wszystkich zapytań i procedur w formacie czytelnym dla człowieka.
*   **Zawartość:** Przykłady zapytań CRUD (Create, Read, Update, Delete), złączeń JOIN, oraz pełna dokumentacja procedury Smart Insert.

---

## 2. Aplikacja Webowa (PHP)

Interfejs użytkownika znajdujący się w katalogu `php/`. System wykorzystuje nowoczesny design z ciemnym motywem i responsywnym układem.

### ⚙️ Konfiguracja i Wygląd

#### 📄 `php/config.php`
*   **Rola:** Serce połączenia z bazą danych.
*   **Działanie:** Używa biblioteki `PDO` do bezpiecznego połączenia z MySQL/MariaDB.
*   **Zmienne środowiskowe:**
    *   `DB_HOST` - Host bazy danych (domyślnie: `localhost`)
    *   `DB_NAME` - Nazwa bazy danych (domyślnie: `rent_rowery`)
    *   `DB_USER` - Użytkownik bazy (domyślnie: `root`)
    *   `DB_PASS` - Hasło do bazy (domyślnie: puste)
*   **Cechy:** Automatyczna detekcja środowiska (działa lokalnie i w Dockerze), obsługa błędów PDO, domyślny tryb FETCH_ASSOC.

#### 📄 `php/style.css`
*   **Rola:** Kompletny system stylów aplikacji.
*   **Funkcjonalności:**
    *   Ciemny motyw (Dark Mode) inspirowany GitHub
    *   Zmienne CSS dla łatwej personalizacji kolorów
    *   Responsywny układ grid dla statystyk
    *   Style dla formularzy, tabel, przycisków i kart
    *   Efekty hover i przejścia animowane
    *   Czcionka Inter z Google Fonts
*   **Główne komponenty:**
    *   `.container` - Główny kontener aplikacji
    *   `.card` - Karty z zaokrąglonymi rogami i cieniami
    *   `.stats-grid` - Siatka dla kafelków statystyk
    *   `.btn`, `.btn-primary`, `.btn-success`, `.btn-danger` - System przycisków
    *   `.alert-success`, `.alert-error` - Komunikaty systemowe

#### 📄 `php/templates/header.php`
*   **Rola:** Szablon nagłówka strony.
*   **Zawartość:**
    *   Meta tagi HTML5 (charset UTF-8, viewport)
    *   Tytuł strony: "Wypożyczalnia Rowerów"
    *   Ładowanie czcionki Inter z Google Fonts
    *   Ładowanie pliku `style.css`
    *   Logo aplikacji: "🚲 Wypożyczalnia"
    *   Menu nawigacyjne z czterema linkami:
        *   Rowery (`index.php`)
        *   Wypożyczenia (`loans.php`)
        *   Stacje (`stations.php`)
        *   Klienci (`clients.php`)
    *   Aktywna zakładka jest podświetlana (klasa `.active`)

#### 📄 `php/templates/footer.php`
*   **Rola:** Szablon stopki strony.
*   **Zawartość:** Zamyka tagi `</main>`, `</div>` (container) i `</body>`, `</html>`.

### 🖥️ Podstrony (Moduły)

#### 📄 `php/index.php` (Dashboard - Strona Główna)
*   **Rola:** Główny panel zarządzania wypożyczalnią.
*   **Funkcjonalności:**
    1.  **Statystyki** - Wyświetla kafelki z liczbami:
        *   Liczba rowerów w systemie
        *   Wypożyczone rowery
        *   Dostępne rowery
        *   Liczba marek
        *   Liczba zarejestrowanych klientów
    2.  **Lista rowerów** - Tabela z kompletnymi informacjami:
        *   ID roweru
        *   Model
        *   Marka (nazwa i kraj)
        *   Kategoria (w kolorowej etykiecie)
        *   Numer stacji
        *   Status wypożyczenia:
            *   **Dostępny** (zielony) - rower nie jest wypożyczony
            *   **Wypożyczony** (czerwony) - z informacją kto wypożyczył
*   **Zapytanie SQL:** Używa złożonego JOIN łączącego tabele (`rowery`, `marki`, `kategorie`, `stacje`, `klient`).
*   **Przycisk:** "+ Dodaj Rower" prowadzi do `add_bike.php`.

#### 📄 `php/add_bike.php` (Formularz Dodawania Roweru)
*   **Rola:** Zaawansowany formularz do dodawania nowych rowerów.
*   **Główna innowacja:** Wykorzystuje procedurę składowaną `DodajRowerZMarka`.
*   **Funkcjonalności:**
    1.  **Pole modelu** - Prosty input tekstowy.
    2.  **Sekcja marki:**
        *   Pojedyncze pole tekstowe na Nazwę i Kraj.
        *   Używa `<datalist>` do sugerowania istniejących marek.
    3.  **Sekcja kategorii i stacji (HTML5 Datalist):**
        *   Pozwala to na wybór z listy podpowiedzi lub szybkie wpisanie nowej wartości.
*   **Obsługa formularza (POST):**
    *   Wywołanie procedury: `CALL DodajRowerZMarka(?, ?, ?, ?, ?)`.
*   **Przyciski:** "Anuluj" (powrót do index.php) i "Zapisz Rower" (submit).

#### 📄 `php/stations.php` (Zarządzanie Stacjami)
*   **Rola:** Moduł do zarządzania stacjami (punktami odbioru).
*   **Layout:** Układ dwukolumnowy.
*   **Funkcjonalności:**
    1.  **Dodawanie stacji (POST):**
        *   INSERT do tabeli `stacje`
    2.  **Usuwanie stacji (GET):**
        *   Parametr `?delete=id_stacji`
*   **Ostrzeżenie CASCADE:** Usunięcie stacji automatycznie usuwa wszystkie rowery do niej przypisane.

#### 📄 `php/loans.php` (Zarządzanie Wypożyczeniami)
*   **Rola:** Moduł do zarządzania wypożyczeniami rowerów.
*   **Funkcjonalności:**
    1.  **Wypożyczanie rowerów:**
        *   Formularz umożliwiający przypisanie roweru do klienta.
        *   Wybór roweru i klienta z list rozwijanych (SELECT).
        *   Aktualizacja pola `id_klienta` w tabeli `rowery`.
    2.  **Zwracanie rowerów:**
        *   Przycisk "Zwróć" przy wypożyczonych rowerach.
        *   Ustawienie `id_klienta` na `NULL` dla danego roweru.
    3.  **Lista wypożyczeń:**
        *   Tabela wyświetlająca aktualnie wypożyczone rowery.
        *   Informacje o rowerze (model, marka) i kliencie (imię, nazwisko).
*   **Walidacja:** Sprawdzenie dostępności roweru przed wypożyczeniem.

#### 📄 `php/clients.php` (Zarządzanie Klientami)
*   **Rola:** Kompletny moduł do zarządzania klientami (pełne CRUD).
*   **Funkcjonalności:**
    1.  **Dodawanie i Edycja:**
        *   Formularz automatycznie przełącza się w "Tryb Edycji".
    2.  **Usuwanie z zabezpieczeniem:**
        *   Można usunąć klienta tylko, jeśli nie ma on obecnie żadnych wypożyczonych rowerów.

---

## 3. Infrastruktura (Docker)

Pliki pozwalające uruchomić całość w izolowanym środowisku kontenerowym.

### 🐳 `Dockerfile`
*   **Obraz bazowy:** `php:8.2-apache`
*   **Instalowane rozszerzenia:**
    *   `pdo_mysql` - Wymagane do połączenia z bazą danych przez PDO
*   **Konfiguracja:** Domyślny katalog `/var/www/html` jest mapowany na lokalny katalog `php/`.

### 🐳 `docker-compose.yaml`
*   **Wersja:** 3.8
*   **Usługi:**

    1.  **mariadb** (Baza Danych)
        *   Obraz: `mariadb:10.11`
        *   Nazwa kontenera: `mariadb`
        *   Restart: `unless-stopped`
        *   Zmienne środowiskowe:
            *   `MYSQL_ROOT_PASSWORD`: Ewhmgtw2
            *   `MYSQL_DATABASE`: rent_rowery
        *   Wolumeny:
            *   `mariadb_data` - Trwałe przechowywanie danych
            *   Opcjonalnie: `/docker-entrypoint-initdb.d/` dla automatycznego importu SQL
        *   Sieć: `backend`

    2.  **phpmyadmin** (Panel Administracyjny)
        *   Obraz: `phpmyadmin:apache`
        *   Nazwa kontenera: `phpmyadmin`
        *   Port: `8081:80`
        *   Zależności: `mariadb`
        *   Zmienne środowiskowe:
            *   `PMA_HOST`: mariadb
            *   `PMA_PORT`: 3306
            *   `PMA_USER`: root
            *   `PMA_PASSWORD`: Ewhmgtw2
        *   Wolumeny: `phpmyadmin_sessions`
        *   Sieć: `backend`

    3.  **app** (Aplikacja PHP)
        *   Build: Z lokalnego `Dockerfile`
        *   Nazwa kontenera: `rent_rowery_app`
        *   Port: `9071:80` (aplikacja dostępna na http://localhost:9071)
        *   Zależności: `mariadb`
        *   Wolumeny:
            *   `./php:/var/www/html` - Live reload kodu PHP
        *   Zmienne środowiskowe:
            *   `DB_HOST`: mariadb
            *   `DB_NAME`: rent_rowery
            *   `DB_USER`: root
            *   `DB_PASS`: Ewhmgtw2
        *   Sieć: `backend`

*   **Wolumeny:**
    *   `mariadb_data` - Persystencja danych bazy
    *   `phpmyadmin_sessions` - Sesje phpMyAdmin

*   **Sieci:**
    *   `backend` - Sieć bridge łącząca wszystkie kontenery

### 🚀 Uruchamianie Systemu

**Uruchomienie wszystkich usług:**
```bash
docker-compose up -d
```

**Dostęp do aplikacji:**
*   Aplikacja główna: http://localhost:9071
*   phpMyAdmin: http://localhost:8081

**Zatrzymanie systemu:**
```bash
docker-compose down
```

**Zatrzymanie z usunięciem wolumenów (UWAGA: usuwa dane!):**
```bash
docker-compose down -v
```

---

## 4. Struktura Bazy Danych

### Tabele

1.  **marki**
    *   `id_marki` (INT, PRIMARY KEY, AUTO_INCREMENT)
    *   `nazwa` (VARCHAR 15)
    *   `kraj` (VARCHAR 20)

2.  **kategorie**
    *   `id_kategori` (INT, PRIMARY KEY, AUTO_INCREMENT)
    *   `nazwa_kategori` (VARCHAR 40)

3.  **stacje**
    *   `id_stacji` (INT, PRIMARY KEY, AUTO_INCREMENT)
    *   `numer_stacji` (INT)

4.  **klient**
    *   `id_klienta` (INT, PRIMARY KEY, AUTO_INCREMENT)
    *   `imie` (VARCHAR 15)
    *   `nazwisko` (VARCHAR 20)
    *   `adres_email` (VARCHAR 50)

5.  **rowery** (Tabela główna)
    *   `id_roweru` (INT, PRIMARY KEY, AUTO_INCREMENT)
    *   `id_marki` (INT, FOREIGN KEY → marki)
    *   `id_stacji` (INT, FOREIGN KEY → stacje, ON DELETE CASCADE)
    *   `id_kategori` (INT, FOREIGN KEY → kategorie)
    *   `model` (VARCHAR 20)
    *   `id_klienta` (INT, FOREIGN KEY → klient, NULL jeśli dostępny)

### Relacje

*   Rower **musi mieć** markę, kategorię i stację (NOT NULL)
*   Rower **może być** wypożyczony przez klienta (NULL = dostępny)
*   Usunięcie stacji **usuwa kaskadowo** wszystkie rowery do niej przypisane (ON DELETE CASCADE)

---

## 5. Bezpieczeństwo i Dobre Praktyki

### Zastosowane zabezpieczenia:

1.  **PDO Prepared Statements** - Wszystkie zapytania SQL używają parametrów wiązanych (`?`), co chroni przed SQL Injection.
2.  **htmlspecialchars()** - Wszystkie dane wyświetlane użytkownikowi są escapowane, chroniąc przed XSS.
3.  **Walidacja danych** - Sprawdzanie czy pola formularzy nie są puste przed zapisem.
4.  **Try-Catch** - Obsługa wyjątków PDO z wyświetlaniem przyjaznych komunikatów.
5.  **Zmienne środowiskowe** - Wrażliwe dane (hasła) są przechowywane w zmiennych środowiskowych, nie w kodzie.

### Rekomendacje dla produkcji:

*   Zmienić domyślne hasło bazy danych
*   Dodać HTTPS (certyfikat SSL)
*   Implementować sesje użytkowników i autoryzację
*   Dodać logowanie działań (audit log)
*   Regularnie tworzyć kopie zapasowe bazy danych

---

## 6. Przyszłe Rozszerzenia

Możliwe funkcjonalności do dodania:

*   ✅ Edycja i usuwanie klientów
*   ✅ Uproszczony formularz dodawania książek (Datalist)
*   ✅ Inteligentne zarządzanie autorami (pojedyncze pole)
*   ✅ Statystyki wypożyczeń na dashboardzie
*   [ ] System wypożyczeń z datami (wypożyczenie/zwrot)
*   [ ] Edycja i usuwanie książek
*   [ ] Wyszukiwarka książek (po tytule, autorze, kategorii)
*   [ ] Historia wypożyczeń klienta
*   [ ] Raporty i statystyki
*   [ ] System rezerwacji książek
*   [ ] Powiadomienia email
*   [ ] Logowanie administratora

---

## 7. Podsumowanie

System Wypożyczalni Rowerów to kompletna aplikacja webowa do zarządzania wypożyczalnią, zbudowana w czystym PHP bez frameworków. Wykorzystuje nowoczesne technologie (Docker, PDO, Procedury Składowane) i dobre praktyki programistyczne. Dzięki modularnej strukturze i przejrzystemu kodowi, system jest łatwy w rozbudowie i utrzymaniu.
