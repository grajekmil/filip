# Pełna Dokumentacja Techniczna Systemu Bibliotecznego

Ten dokument opisuje wszystkie pliki stworzone w ramach projektu "System Biblioteczny". Projekt składa się z trzech głównych warstw: Bazy Danych (SQL), Aplikacji Webowej (PHP) oraz Infrastruktury (Docker).

---

## 1. Warstwa Bazy Danych (SQL)

Pliki odpowiedzialne za strukturę i logikę danych w bazie `biblioteka`.

### 📄 `biblioteka.sql`
*   **Co to jest:** Zrzut (dump) całej bazy danych.
*   **Jak działa:** Zawiera polecenia `CREATE TABLE`, które tworzą tabele (`autorzy`, `ksiazki`, `polki`, `kategorie`, `klient`) oraz `INSERT`, które wypełniają je przykładowymi danymi.
*   **Kiedy używany:** Przy pierwszym uruchomieniu projektu (automatycznie importowany przez Dockera).

### 📄 `zapytania_crud.sql`
*   **Co to jest:** Zbiór przykładowych zapytań SQL typu CRUD (Create, Read, Update, Delete).
*   **Jak działa:** Służy jako "ściąga" dla programisty. Pokazuje jak pobrać książki z pełnymi danymi (używając `JOIN`), jak dodać nowego autora czy jak wypożyczyć książkę.

### 📄 `procedura_smart_insert.sql`
*   **Co to jest:** Plik definiujący Procedurę Składowaną `DodajKsiazkeZAutorem`.
*   **Jak działa:** To "inteligentna" funkcja wewnątrz bazy danych. Pozwala dodać książkę podając nazwę autora, kategorii i numer półki. Procedura sama sprawdza, czy te elementy istnieją – jeśli nie, to je tworzy w odpowiednich tabelach, a na końcu dodaje książkę.

### 📄 `dokumentacja_zapytan.md`
*   **Co to jest:** Szczegółowy opis wszystkich zapytań i procedur w formacie czytelnym dla człowieka.

---

## 2. Aplikacja Webowa (PHP)

Interfejs użytkownika znajdujący się w katalogu `php/`.

### ⚙️ Konfiguracja i Wygląd

#### 📄 `php/config.php`
*   **Rola:** Serce połączenia z bazą danych.
*   **Działanie:** Używa biblioteki `PDO` do bezpiecznego połączenia z MySQL. Jest "inteligentny" – sprawdza zmienne środowiskowe, dzięki czemu działa zarówno lokalnie (XAMPP/PHP Server), jak i w kontenerze Docker.

#### 📄 `php/style.css`
*   **Rola:** Wygląd aplikacji.
*   **Działanie:** Zawiera definicje kolorów (zmienne CSS `--primary-color`, itp.) oraz style dla Ciemnego Motywu (Dark Mode). Odpowiada za wygląd kart, tabel, przycisków i formularzy.

#### 📄 `php/templates/header.php` i `footer.php`
*   **Rola:** Szablony wielokrotnego użytku.
*   **Działanie:** `header.php` zawiera początek kodu HTML (meta tagi, ładowanie CSS) i menu nawigacyjne. `footer.php` zamyka stronę. Dzięki nim nie musimy kopiować tego samego kodu do każdego pliku.

### 🖥️ Podstrony (Moduły)

#### 📄 `php/index.php` (Dashboard)
*   **Rola:** Strona główna.
*   **Działanie:**
    1.  Wyświetla kafelki ze statystykami (liczba książek, autorów).
    2.  Pobiera listę książek z bazy (używając skomplikowanego `JOIN` z `zapytania_crud.sql`).
    3.  Wyświetla tabelę, w której status wypożyczenia jest kolorowany (zielony/czerwony).

#### 📄 `php/add_book.php`
*   **Rola:** Formularz dodawania książki.
*   **Działanie:**
    1.  Pobiera z bazy listę autorów, półek i kategorii.
    2.  Wyświetla je w listach rozwijanych (`<select>`), co ułatwia wybór.
    3.  Po wysłaniu formularza, bezpiecznie zapisuje nową książkę w bazie.

#### 📄 `php/shelves.php`
*   **Rola:** Zarządzanie półkami.
*   **Działanie:** Pozwala dodać nową półkę (tylko numer) oraz usunąć istniejącą.
*   **Ważne:** Przycisk usuwania jest "niebezpieczny" – usunięcie półki usunie też wszystkie książki, które na niej stoją (działanie kaskadowe bazy danych).

#### 📄 `php/clients.php`
*   **Rola:** Zarządzanie czytelnikami.
*   **Działanie:** Prosty formularz rejestracji klienta (Imię, Nazwisko, Email) oraz lista istniejących klientów.

---

## 3. Infrastruktura (Docker)

Pliki pozwalające uruchomić całość w izolowanym środowisku.

### 🐳 `Dockerfile`
*   **Co robi:** To "przepis" na stworzenie serwera dla naszej aplikacji.
*   **Szczegóły:** Pobiera oficjalny obraz PHP z Apache (`php:8.2-apache`), a następnie doinstalowuje rozszerzenie `pdo_mysql`, które jest niezbędne do łączenia się z bazą danych (domyślny obraz PHP go nie ma).

### 🐳 `docker-compose.yml`
*   **Co robi:** Dyrygent, który uruchamia wszystkie usługi naraz.
*   **Usługi:**
    1.  `mariadb`: Baza danych. Przy starcie automatycznie ładuje plik `biblioteka.sql`.
    2.  `phpmyadmin`: Panel graficzny do zarządzania bazą (na porcie 8081).
    3.  `app`: Nasza aplikacja PHP (zbudowana z `Dockerfile`), wystawiona na porcie 9071.
