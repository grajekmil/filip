# Dokumentacja Zapytań SQL dla Systemu Bibliotecznego

Niniejszy dokument zawiera szczegółowy opis przykładowych zapytań SQL (CRUD) wykorzystywanych w systemie bibliotecznym. Zapytania te obejmują pobieranie danych (SELECT), dodawanie nowych rekordów (INSERT), a także aktualizację (UPDATE) i usuwanie (DELETE).

## 1. Struktura Danych - Krótki Przegląd

System opiera się na relacyjnej bazie danych zawierającej następujące tabele:
*   **autorzy**: Przechowuje dane o autorach (imie, nazwisko).
*   **kategorie**: Przechowuje kategorie książek (np. poezja, dramat).
*   **polki**: Numeracja fizycznych półek w bibliotece.
*   **klient**: Dane czytelników (imie, nazwisko, email).
*   **ksiazki**: Główna tabela łącząca pozostałe. Zawiera klucze obce do autorów, kategorii, półek oraz (opcjonalnie) klienta, który wypożyczył książkę.

---

## 2. Zapytania SELECT (Odczyt Danych)

Zapytania `SELECT` służą do pobierania danych z bazy.

### 2.1. Pobranie wszystkich autorów
Pobiera pełną listę autorów zapisanych w bazie.
```sql
SELECT * FROM autorzy;
```

### 2.2. Pobranie wszystkich kategorii
Wyświetla dostępne kategorie książek.
```sql
SELECT * FROM kategorie;
```

### 2.3. Raport o książkach (Złączenia / JOIN)
To zapytanie jest kluczowe dla widoku użytkownika. Zamiast wyświetlać numery ID (np. `id_autora=1`), łączy dane z powiązanych tabel, aby wyświetlić czytelne informacje: tytuł, imię i nazwisko autora, nazwę kategorii, numer półki oraz dane osoby wypożyczającej (jeśli książka jest wypożyczona).

**Zastosowane złączenia:**
*   `JOIN` (Inner Join): Wyświetla książkę tylko, jeśli ma przypisanego autora, kategorię i półkę (jest to wymuszone strukturą NOT NULL w bazie).
*   `LEFT JOIN` (dla tabeli `klient`): Pozwala wyświetlić książkę nawet jeśli **nie jest** ona wypożyczona (wtedy pole `id_klienta` jest NULL, a dane klienta również zwrócą NULL). Gdybyśmy użyli tutaj zwykłego JOIN, zapytanie zwróciłoby tylko wypożyczone książki.

```sql
SELECT 
    k.id_ksiazki, 
    k.tytul,
    a.imie AS autor_imie, 
    a.nazwisko AS autor_nazwisko, 
    kat.nazwa_kategori, 
    p.numer_polki,
    kl.imie AS klient_imie, 
    kl.nazwisko AS klient_nazwisko
FROM ksiazki k
JOIN autorzy a ON k.id_autora = a.id_autora
JOIN kategorie kat ON k.id_kategori = kat.id_kategori
JOIN polki p ON k.id_polki = p.id_polki
LEFT JOIN klient kl ON k.id_klienta = kl.id_klienta;
```

### 2.4. Historia wypożyczeń klienta
Znajduje wszystkie książki aktualnie przypisane do klienta o `id_klienta = 1`.
```sql
SELECT * FROM ksiazki WHERE id_klienta = 1;
```

### 2.5. Wyszukiwanie książek (LIKE)
Wyszukuje książki, których tytuł zawiera frazę "Dziady". Znak procenta `%` oznacza "dowolny ciąg znaków", więc znajdzie np. "Dziady cz.III", "Stare Dziady" itp.
```sql
SELECT * FROM ksiazki WHERE tytul LIKE '%Dziady%';
```

### 2.6. Sprawdzenie dostępności
Wyświetla książki, które nie są aktualnie wypożyczone (pole `id_klienta` jest puste).
```sql
SELECT * FROM ksiazki WHERE id_klienta IS NULL;
```

---

## 3. Zapytania INSERT (Dodawanie Danych)

Służą do wstawiania nowych rekordów.

### 3.1. Dodanie autora
```sql
INSERT INTO autorzy (imie, nazwisko) VALUES ('Juliusz', 'Słowacki');
```

### 3.2. Dodanie kategorii
```sql
INSERT INTO kategorie (nazwa_kategori) VALUES ('biografia');
```

### 3.3. Dodanie klienta
```sql
INSERT INTO klient (imie, nazwisko, adres_email) VALUES ('Anna', 'Nowak', 'anna.nowak@example.com');
```

### 3.4. Dodanie półki
```sql
INSERT INTO polki (numer_polki) VALUES (9);
```

### 3.5. Dodanie książki
Podczas dodawania książki musimy podać ID istniejącego autora, półki i kategorii. Ostatni parametr (`NULL`) oznacza, że nowa książka nie jest od razu wypożyczona.
```sql
INSERT INTO ksiazki (id_autora, id_polki, id_kategori, tytul, id_klienta) 
VALUES (1, 1, 1, 'Pan Tadeusz', NULL);
```

### 3.6. Dodanie książki "słownie" (ZAAWANSOWANE)
Możemy pójść o krok dalej i dodać książkę nie znając **żadnego** ID, opierając się tylko na nazwach (Autora, Kategorii i numerze Półki). To bardzo przydatne przy imporcie danych z Excela/CSV, gdzie zazwyczaj mamy nazwy, a nie ID.

```sql
INSERT INTO ksiazki (id_autora, id_polki, id_kategori, tytul, id_klienta)
VALUES (
    (SELECT id_autora FROM autorzy WHERE imie = 'Adam' AND nazwisko = 'Mickiewicz' LIMIT 1), 
    (SELECT id_polki FROM polki WHERE numer_polki = 1 LIMIT 1),
    (SELECT id_kategori FROM kategorie WHERE nazwa_kategori = 'poezja' LIMIT 1),
    'Konrad Wallenrod',
    NULL
);
```
*Uwaga: `LIMIT 1` jest dodane dla bezpieczeństwa, aby upewnić się, że podzapytanie zwróci tylko jedną wartość (nawet jeśli w bazie jest dwóch Adamów Mickiewiczów).*

---

## 4. Zapytania UPDATE (Aktualizacja Danych)

Służą do modyfikacji istniejących wpisów, np. przy procesie wypożyczania.

### 4.1. Wypożyczenie książki
Przypisuje użytkownika o ID 2 do książki o ID 3.
```sql
UPDATE ksiazki SET id_klienta = 2 WHERE id_ksiazki = 3;
```

### 4.2. Zwrot książki
Usuwa przypisanie użytkownika (ustawia NULL).
```sql
UPDATE ksiazki SET id_klienta = NULL WHERE id_ksiazki = 3;
```

### 4.3. Zmiana danych klienta
Aktualizacja adresu e-mail.
```sql
UPDATE klient SET adres_email = 'nowy.email@example.com' WHERE id_klienta = 1;
```

---

## 5. Zapytania DELETE (Usuwanie Danych)

### 5.1. Usunięcie książki
Usuwa fizycznie rekord książki z bazy.
```sql
DELETE FROM ksiazki WHERE id_ksiazki = 5;
```

### 5.2. Usunięcie autora
**Uwaga:** Jeśli autor ma przypisane książki, baza danych może zablokować usunięcie (constraint violation), aby nie pozostawić "sierot" w tabeli książek. Aby usunąć takiego autora, należy najpierw usunąć jego książki lub zmienić im autora.
```sql
DELETE FROM autorzy WHERE id_autora = 5;
```

---

## 6. Procedura "Smart Insert" (Automatyzacja)

Pytasz, czy da się dodać książkę, podając autora, którego jeszcze nie ma w bazie, tak aby SQL sam go dodał?
**Tak, ale wymaga to Procedury Składowanej (Stored Procedure).**

Standardowe polecenie `INSERT` działa tylko na jednej tabeli naraz. Aby zrobić "logikę" (sprawdź -> jeśli nie ma to dodaj -> potem dodaj książkę), musimy napisać mały program w SQL (procedurę).

### 6.1. Jak używać?
Po wgraniu procedury do bazy, dodawanie książek z automatycznym tworzeniem autora wygląda tak:
```sql
CALL DodajKsiazkeZAutorem('Wisława', 'Szymborska', 'Wiersze', 2, 'poezja');
```
Parametry (kolejno):
1.  **Imię autora** (`'Wisława'`)
2.  **Nazwisko autora** (`'Szymborska'`)
3.  **Tytuł książki** (`'Wiersze'`)
4.  **Numer półki** (`2`)
5.  **Nazwa kategorii** (`'poezja'`)

### 6.2. Kod źródłowy procedury
Poniżej znajduje się pełny kod procedury, który wykonuje całą logikę (do skopiowania, jeśli nie chcesz używać osobnego pliku `.sql`):

```sql
DELIMITER //

CREATE PROCEDURE DodajKsiazkeZAutorem(
    IN p_imie_autora VARCHAR(15),
    IN p_nazwisko_autora VARCHAR(20),
    IN p_tytul VARCHAR(20),
    IN p_numer_polki INT,
    IN p_nazwa_kategorii VARCHAR(40)
)
BEGIN
    DECLARE v_id_autora INT;
    DECLARE v_id_polki INT;
    DECLARE v_id_kategorii INT;

    -- 1. Sprawdź czy autor istnieje, jeśli nie - dodaj go
    SELECT id_autora INTO v_id_autora FROM autorzy 
    WHERE imie = p_imie_autora AND nazwisko = p_nazwisko_autora LIMIT 1;
    
    IF v_id_autora IS NULL THEN
        INSERT INTO autorzy (imie, nazwisko) VALUES (p_imie_autora, p_nazwisko_autora);
        SET v_id_autora = LAST_INSERT_ID();
    END IF;

    -- 2. Sprawdź czy półka istnieje, jeśli nie - dodaj ją
    SELECT id_polki INTO v_id_polki FROM polki WHERE numer_polki = p_numer_polki LIMIT 1;

    IF v_id_polki IS NULL THEN
        INSERT INTO polki (numer_polki) VALUES (p_numer_polki);
        SET v_id_polki = LAST_INSERT_ID();
    END IF;
    
    -- 3. Sprawdź czy kategoria istnieje, jeśli nie - dodaj ją
    SELECT id_kategori INTO v_id_kategorii FROM kategorie WHERE nazwa_kategori = p_nazwa_kategorii LIMIT 1;

    IF v_id_kategorii IS NULL THEN
        INSERT INTO kategorie (nazwa_kategori) VALUES (p_nazwa_kategorii);
        SET v_id_kategorii = LAST_INSERT_ID();
    END IF;

    -- 4. Wstaw książkę
    INSERT INTO ksiazki (id_autora, id_polki, id_kategori, tytul, id_klienta)
    VALUES (v_id_autora, v_id_polki, v_id_kategorii, p_tytul, NULL);
    
END //

DELIMITER ;
```

### 6.3. Jak wgrać procedurę?
Masz dwie opcje, aby uruchomić to u siebie:

**Opcja A: Import pliku (Najprostsza)**
1.  W phpMyAdmin wejdź w zakładkę **Import**.
2.  Wybierz plik `procedura_smart_insert.sql`.
3.  Kliknij **Wykonaj (Go)**.

**Opcja B: Ręczne wklejenie kodu**
1.  W phpMyAdmin wejdź w zakładkę **SQL**.
2.  Skopiuj cały kod z sekcji **6.2** powyżej.
3.  Wklej go do okna zapytania.
4.  Kliknij **Wykonaj (Go)**.

*Ważne: Kod procedury wgrywasz tylko RAZ. Potem używasz jej wielokrotnie samą komendą `CALL`.*
