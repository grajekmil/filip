# Jak działają skrypty aplikacji?

Ten dokument tłumaczy logikę działania i przepływ danych w skryptach PHP Systemu Wypożyczalni Rowerów.

---

## 1. Serce Systemu: `config.php`
Każdy inny plik w aplikacji zaczyna się od `require_once 'config.php'`.
*   **Co robi:** Nawiązuje połączenie z bazą danych MariaDB/MySQL.
*   **Logika:** Używa biblioteki **PDO**, która jest bezpieczniejsza od starego `mysqli`. Próbuje pobrać dane logowania ze zmiennych środowiskowych (jeśli używasz Dockera) lub używa domyślnych wartości (localhost, rent_rowery).

## 2. Spójny Wygląd: `templates/header.php` i `footer.php`
Aplikacja nie powtarza kodu HTML dla nawigacji i stylów na każdej stronie.
*   **Header:** Zawiera style CSS i menu. Automatycznie podświetla aktywną zakładkę, sprawdzając nazwę aktualnie otwartego pliku.
*   **Footer:** Zamyka główne kontenery strony.

## 3. Panel Główny: `index.php`
To tutaj widzisz listę wszystkich rowerów i statystyki.
*   **Statystyki:** Skrypt wykonuje kilka szybkich zapytań `COUNT(*)`, aby policzyć ilu jest klientów, marek i rowerów.
*   **Lista Rowerów:** Wykorzystuje zaawansowane zapytanie `JOIN`. Zamiast wyświetlać tylko ID, łączy tabele `rowery`, `marki`, `kategorie` i `klient`, aby wyświetlić pełne nazwy i stan wypożyczenia.

## 4. Inteligentne Dodawanie: `add_bike.php`
To najbardziej zaawansowany skrypt w systemie.
*   **Interfejs:** Używa tagów `<datalist>`, które podpowiadają istniejące marki i kategorie, ale pozwalają też wpisać zupełnie nowe.
*   **Logika "Smart Insert":** Zamiast wielu zapytań PHP, skrypt wywołuje jedną procedurę składowaną w bazie: `CALL DodajRowerZMarka(...)`.
*   **Co robi procedura:** Sama sprawdza, czy wpisana marka lub kategoria już istnieje. Jeśli nie – dodaje ją do słownika, a na końcu podpina nowy rower pod odpowiednie ID.

## 5. Zarządzanie Klientami i Stacjami (`clients.php`, `stations.php`)
Te pliki realizują podstawowy model CRUD (Tworzenie, Odczyt, Aktualizacja, Usuwanie).
*   **Tryby:** Plik `clients.php` potrafi przełączyć się w "tryb edycji" – po kliknięciu "Edytuj", formularz ładuje dane istniejącego klienta i zmienia przycisk na "Zaktualizuj".
*   **Bezpieczeństwo Usuwania:** Skrypty sprawdzają zależności. Np. nie możesz usunąć klienta, który ma aktualnie wypożyczony rower, co zapobiega błędom w danych.

## 6. System Wypożyczeń: `loans.php`
Zarządza przypisywaniem rowerów do klientów.
*   **Wypożyczenie:** Ustawia pole `id_klienta` w tabeli `rowery` na ID wybranego klienta. Taki rower automatycznie zmienia status na "Wypożyczony" na liście głównej.
*   **Zwrot:** Czyści pole `id_klienta` (ustawia na `NULL`). Rower znów staje się dostępny dla wszystkich.

---

## Podsumowanie Techniczne
Aplikacja opiera się na **bezpiecznych zapytaniach (Prepared Statements)**, co oznacza, że dane od użytkownika nigdy nie są bezpośrednio wklejane do zapytania SQL. Chroni to system przed atakami typu SQL Injection.
