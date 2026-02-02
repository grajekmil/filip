# System Biblioteczny (PHP + MySQL)

Prosty system CRUD do zarządzania biblioteką, napisany w czystym PHP bez użycia frameworków.

## 🚀 Jak uruchomić?

### Wymagania
*   PHP (wersja 7.4 lub nowsza)
*   Baza danych MySQL/MariaDB

### Konfiguracja
1.  Upewnij się, że masz importowaną bazę danych `biblioteka`.
    *   Plik SQL: `../biblioteka.sql`
    *   Procedura (opcjonalnie): `../procedura_smart_insert.sql`
2.  Sprawdź ustawienia połączenia w pliku `config.php`:
    ```php
    $host = 'localhost';
    $dbname = 'biblioteka';
    $username = 'root';
    $password = '';
    ```

### Uruchomienie serwera
Najprostszy sposób to użycie wbudowanego serwera PHP. Będąc w tym katalogu, uruchom w terminalu:

```powershell
php -S localhost:8000
```

Następnie otwórz przeglądarkę pod adresem: [http://localhost:8000](http://localhost:8000)

## 📂 Struktura projektu

*   `index.php` - Panel główny, lista książek
*   `add_book.php` - Formularz dodawania książki
*   `shelves.php` - Zarządzanie półkami
*   `clients.php` - Zarządzanie klientami
*   `loans.php` - Zarządzanie wypożyczeniami (wypożycz/zwróć)
*   `style.css` - Arkusze stylów (Dark Mode)
*   `config.php` - Połączenie z bazą
