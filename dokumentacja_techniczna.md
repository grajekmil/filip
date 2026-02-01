# Pełna Dokumentacja Techniczna Systemu Bibliotecznego

Ten dokument opisuje wszystkie pliki stworzone w ramach projektu "System Biblioteczny". Projekt składa się z trzech głównych warstw: Bazy Danych (SQL), Aplikacji Webowej (PHP) oraz Infrastruktury (Docker).

---

## 1. Warstwa Bazy Danych (SQL)

Pliki odpowiedzialne za strukturę i logikę danych w bazie `biblioteka`.

### 📄 `procedura_smart_insert.sql`
*   **Co to jest:** Plik definiujący Procedurę Składowaną `DodajKsiazkeZAutorem`.
*   **Jak działa:** To "inteligentna" funkcja wewnątrz bazy danych. Pozwala dodać książkę podając nazwę autora, kategorii i numer półki. Procedura sama sprawdza, czy te elementy istnieją – jeśli nie, to je tworzy w odpowiednich tabelach, a na końcu dodaje książkę.
*   **Parametry procedury:**
    1.  `p_imie_autora` - Imię autora (VARCHAR 15)
    2.  `p_nazwisko_autora` - Nazwisko autora (VARCHAR 20)
    3.  `p_tytul` - Tytuł książki (VARCHAR 20)
    4.  `p_numer_polki` - Numer półki (INT)
    5.  `p_nazwa_kategorii` - Nazwa kategorii (VARCHAR 40)
*   **Przykład użycia:** `CALL DodajKsiazkeZAutorem('Wisława', 'Szymborska', 'Wiersze', 2, 'poezja');`
*   **Kiedy używana:** Aktywnie wykorzystywana w formularzu dodawania książek (`add_book.php`).

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
    *   `DB_NAME` - Nazwa bazy danych (domyślnie: `biblioteka`)
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
    *   Tytuł strony: "System Biblioteczny"
    *   Ładowanie czcionki Inter z Google Fonts
    *   Ładowanie pliku `style.css`
    *   Logo aplikacji: "📚 Biblioteka"
    *   Menu nawigacyjne z trzema linkami:
        *   Książki (`index.php`)
        *   Półki (`shelves.php`)
        *   Klienci (`clients.php`)
    *   Aktywna zakładka jest podświetlana (klasa `.active`)

#### 📄 `php/templates/footer.php`
*   **Rola:** Szablon stopki strony.
*   **Zawartość:** Zamyka tagi `</main>`, `</div>` (container) i `</body>`, `</html>`.

### 🖥️ Podstrony (Moduły)

#### 📄 `php/index.php` (Dashboard - Strona Główna)
*   **Rola:** Główny panel zarządzania biblioteką.
*   **Funkcjonalności:**
    1.  **Statystyki** - Wyświetla trzy kafelki z liczbami:
        *   Liczba książek w systemie
        *   Liczba autorów
        *   Liczba zarejestrowanych klientów
    2.  **Lista książek** - Tabela z kompletnymi informacjami:
        *   ID książki
        *   Tytuł
        *   Autor (imię i nazwisko)
        *   Kategoria (w kolorowej etykiecie)
        *   Numer półki
        *   Status wypożyczenia:
            *   **Dostępna** (zielony) - książka nie jest wypożyczona
            *   **Wypożyczona** (czerwony) - z informacją kto wypożyczył
        *   Kolumna akcji (placeholder dla przyszłych funkcji edycji)
*   **Zapytanie SQL:** Używa złożonego JOIN łączącego 5 tabel (`ksiazki`, `autorzy`, `kategorie`, `polki`, `klient`) z LEFT JOIN dla klientów.
*   **Przycisk:** "+ Dodaj Książkę" prowadzi do `add_book.php`.

#### 📄 `php/add_book.php` (Formularz Dodawania Książki)
*   **Rola:** Zaawansowany formularz do dodawania nowych książek.
*   **Główna innowacja:** Wykorzystuje procedurę składowaną `DodajKsiazkeZAutorem` zamiast ręcznych INSERT.
*   **Funkcjonalności:**
    1.  **Pole tytułu** - Prosty input tekstowy
    2.  **Sekcja autora** - Hybrydowy system:
        *   Lista rozwijana z istniejącymi autorami
        *   Dwa pola tekstowe (imię, nazwisko) do ręcznego wprowadzenia
        *   JavaScript automatycznie wypełnia pola po wyborze z listy
        *   Możliwość dodania nowego autora "w locie"
    3.  **Sekcja kategorii:**
        *   Lista rozwijana z istniejącymi kategoriami
        *   Pole tekstowe do wprowadzenia nowej kategorii
        *   Automatyczne uzupełnianie po wyborze z listy
    4.  **Sekcja półki:**
        *   Lista rozwijana z istniejącymi półkami
        *   Pole numeryczne do wprowadzenia nowego numeru
        *   Automatyczne uzupełnianie po wyborze z listy
*   **Obsługa formularza (POST):**
    *   Walidacja wszystkich pól
    *   Wywołanie procedury: `CALL DodajKsiazkeZAutorem(?, ?, ?, ?, ?)`
    *   Wyświetlanie komunikatów sukcesu/błędu
    *   Obsługa wyjątków PDO
*   **JavaScript:**
    *   Funkcja `fillAuthor()` - wypełnia pola imienia i nazwiska
    *   Funkcja `fillInput()` - uniwersalna funkcja do kopiowania wartości z select do input
*   **Przyciski:** "Anuluj" (powrót do index.php) i "Zapisz Książkę" (submit).

#### 📄 `php/shelves.php` (Zarządzanie Półkami)
*   **Rola:** Moduł do zarządzania półkami bibliotecznymi.
*   **Layout:** Układ dwukolumnowy (grid 1fr 2fr):
    *   Lewa kolumna: Formularz dodawania
    *   Prawa kolumna: Lista półek
*   **Funkcjonalności:**
    1.  **Dodawanie półki (POST):**
        *   Formularz z jednym polem: numer półki (type="number")
        *   INSERT do tabeli `polki`
        *   Komunikat potwierdzenia
    2.  **Usuwanie półki (GET):**
        *   Przycisk "Usuń" przy każdej półce
        *   Parametr `?delete=id_polki`
        *   JavaScript `confirm()` z ostrzeżeniem o CASCADE
        *   DELETE z tabeli `polki`
*   **Tabela półek:**
    *   Kolumny: ID, Numer Półki, Akcje
    *   Sortowanie po numerze półki
*   **Ostrzeżenie CASCADE:** Usunięcie półki automatycznie usuwa wszystkie książki na niej stojące (relacja ON DELETE CASCADE w bazie).

#### 📄 `php/clients.php` (Zarządzanie Klientami)
*   **Rola:** Moduł rejestracji i zarządzania czytelnikami.
*   **Layout:** Układ dwukolumnowy (grid 1fr 2fr):
    *   Lewa kolumna: Formularz rejestracji
    *   Prawa kolumna: Baza klientów
*   **Funkcjonalności:**
    1.  **Rejestracja klienta (POST):**
        *   Formularz z trzema polami:
            *   Imię (type="text")
            *   Nazwisko (type="text")
            *   Email (type="email")
        *   INSERT do tabeli `klient`
        *   Walidacja wszystkich pól
        *   Komunikat potwierdzenia z imieniem i nazwiskiem
    2.  **Lista klientów:**
        *   Tabela z kolumnami: ID, Imię i Nazwisko, Email, Akcje
        *   Sortowanie alfabetyczne po nazwisku
        *   Placeholdery dla przyszłych funkcji edycji/usuwania
*   **Walidacja:** Wszystkie pola są wymagane (required), email ma walidację HTML5.

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
            *   `MYSQL_DATABASE`: biblioteka
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
        *   Nazwa kontenera: `biblioteka_app`
        *   Port: `9071:80` (aplikacja dostępna na http://localhost:9071)
        *   Zależności: `mariadb`
        *   Wolumeny:
            *   `./php:/var/www/html` - Live reload kodu PHP
        *   Zmienne środowiskowe:
            *   `DB_HOST`: mariadb
            *   `DB_NAME`: biblioteka
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

1.  **autorzy**
    *   `id_autora` (INT, PRIMARY KEY, AUTO_INCREMENT)
    *   `imie` (VARCHAR 15)
    *   `nazwisko` (VARCHAR 20)

2.  **kategorie**
    *   `id_kategori` (INT, PRIMARY KEY, AUTO_INCREMENT)
    *   `nazwa_kategori` (VARCHAR 40)

3.  **polki**
    *   `id_polki` (INT, PRIMARY KEY, AUTO_INCREMENT)
    *   `numer_polki` (INT)

4.  **klient**
    *   `id_klienta` (INT, PRIMARY KEY, AUTO_INCREMENT)
    *   `imie` (VARCHAR 15)
    *   `nazwisko` (VARCHAR 20)
    *   `adres_email` (VARCHAR 50)

5.  **ksiazki** (Tabela główna)
    *   `id_ksiazki` (INT, PRIMARY KEY, AUTO_INCREMENT)
    *   `id_autora` (INT, FOREIGN KEY → autorzy)
    *   `id_polki` (INT, FOREIGN KEY → polki, ON DELETE CASCADE)
    *   `id_kategori` (INT, FOREIGN KEY → kategorie)
    *   `tytul` (VARCHAR 20)
    *   `id_klienta` (INT, FOREIGN KEY → klient, NULL jeśli dostępna)

### Relacje

*   Książka **musi mieć** autora, kategorię i półkę (NOT NULL)
*   Książka **może być** wypożyczona przez klienta (NULL = dostępna)
*   Usunięcie półki **usuwa kaskadowo** wszystkie książki na niej (ON DELETE CASCADE)

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

*   ✅ System wypożyczeń z datami (wypożyczenie/zwrot)
*   ✅ Edycja i usuwanie książek
*   ✅ Edycja i usuwanie klientów
*   ✅ Wyszukiwarka książek (po tytule, autorze, kategorii)
*   ✅ Historia wypożyczeń klienta
*   ✅ Raporty i statystyki (najpopularniejsze książki, najaktywniejszi czytelnicy)
*   ✅ System rezerwacji książek
*   ✅ Powiadomienia email o zbliżającym się terminie zwrotu
*   ✅ Panel administratora z kontrolą dostępu
*   ✅ API REST dla integracji z innymi systemami

---

## 7. Podsumowanie

System Biblioteczny to kompletna aplikacja webowa do zarządzania biblioteką, zbudowana w czystym PHP bez frameworków. Wykorzystuje nowoczesne technologie (Docker, PDO, Procedury Składowane) i dobre praktyki programistyczne. Dzięki modularnej strukturze i przejrzystemu kodowi, system jest łatwy w rozbudowie i utrzymaniu.
