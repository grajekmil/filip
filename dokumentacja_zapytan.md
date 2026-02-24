# Dokumentacja Zapytań SQL dla Systemu Wypożyczalni Rowerów

Niniejszy dokument zawiera szczegółowy opis przykładowych zapytań SQL (CRUD) wykorzystywanych w systemie wypożyczalni rowerów. Zapytania te obejmują pobieranie danych (SELECT), dodawanie nowych rekordów (INSERT), a także aktualizację (UPDATE) i usuwanie (DELETE).

## 1. Struktura Danych - Krótki Przegląd

System opiera się na relacyjnej bazie danych zawierającej następujące tabele:
*   **marki**: Przechowuje dane o markach rowerów (nazwa, kraj).
*   **kategorie**: Przechowuje kategorie rowerów (np. Górski, Miejski).
*   **stacje**: Numeracja fizycznych stacji (punktów odbioru/zwrotu).
*   **klient**: Dane klientów (imie, nazwisko, email).
*   **rowery**: Główna tabela łącząca pozostałe. Zawiera klucze obce do marek, kategorii, stacji oraz (opcjonalnie) klienta, który wypożyczył rower.

---

## 2. Zapytania SELECT (Odczyt Danych)

Zapytania `SELECT` służą do pobierania danych z bazy.

### 2.1. Pobranie wszystkich marek
Pobiera pełną listę marek zapisanych w bazie.
```sql
SELECT * FROM marki;
```

### 2.2. Pobranie wszystkich kategorii
Wyświetla dostępne kategorie rowerów.
```sql
SELECT * FROM kategorie;
```

### 2.3. Raport o rowerach (Złączenia / JOIN)
To zapytanie jest kluczowe dla widoku użytkownika. Zamiast wyświetlać numery ID (np. `id_marki=1`), łączy dane z powiązanych tabel, aby wyświetlić czytelne informacje: model, nazwę marki, nazwę kategorii, numer stacji oraz dane osoby wypożyczającej (jeśli rower jest wypożyczony).

**Zastosowane złączenia:**
*   `JOIN` (Inner Join): Wyświetla rower tylko, jeśli ma przypisaną markę, kategorię i stację.
*   `LEFT JOIN` (dla tabeli `klient`): Pozwala wyświetlić rower nawet jeśli **nie jest** on wypożyczony.

```sql
SELECT 
    r.id_roweru, 
    r.model,
    m.nazwa AS marka_nazwa, 
    kat.nazwa_kategori, 
    s.numer_stacji,
    kl.imie AS klient_imie, 
    kl.nazwisko AS klient_nazwisko
FROM rowery r
JOIN marki m ON r.id_marki = m.id_marki
JOIN kategorie kat ON r.id_kategori = kat.id_kategori
JOIN stacje s ON r.id_stacji = s.id_stacji
LEFT JOIN klient kl ON r.id_klienta = kl.id_klienta;
```

### 2.4. Historia wypożyczeń klienta
Znajduje wszystkie rowery aktualnie przypisane do klienta o `id_klienta = 1`.
```sql
SELECT * FROM rowery WHERE id_klienta = 1;
```

### 2.5. Wyszukiwanie rowerów (LIKE)
Wyszukuje rowery, których model zawiera frazę "Roam".
```sql
SELECT * FROM rowery WHERE model LIKE '%Roam%';
```

### 2.6. Sprawdzenie dostępności
Wyświetla rowery, które nie są aktualnie wypożyczone.
```sql
SELECT * FROM rowery WHERE id_klienta IS NULL;
```

---

## 3. Zapytania INSERT (Dodawanie Danych)

Służą do wstawiania nowych rekordów.

### 3.1. Dodanie marki
```sql
INSERT INTO marki (nazwa, kraj) VALUES ('Giant', 'Tajwan');
```

### 3.4. Dodanie stacji
```sql
INSERT INTO stacje (numer_stacji) VALUES (9);
```

### 3.5. Dodanie roweru
Podczas dodawania roweru musimy podać ID istniejącej marki, stacji i kategorii.
```sql
INSERT INTO rowery (id_marki, id_stacji, id_kategori, model, id_klienta) 
VALUES (1, 1, 1, 'Roam 1', NULL);
```

### 3.6. Dodanie roweru "słownie" (ZAAWANSOWANE)
Możemy pójść o krok dalej i dodać rower nie znając **żadnego** ID, opierając się tylko na nazwach.

```sql
INSERT INTO rowery (id_marki, id_stacji, id_kategori, model, id_klienta)
VALUES (
    (SELECT id_marki FROM marki WHERE nazwa = 'Giant' LIMIT 1), 
    (SELECT id_stacji FROM stacje WHERE numer_stacji = 1 LIMIT 1),
    (SELECT id_kategori FROM kategorie WHERE nazwa_kategori = 'Górski' LIMIT 1),
    'Roam 1',
    NULL
);
```
*Uwaga: `LIMIT 1` jest dodane dla bezpieczeństwa, aby upewnić się, że podzapytanie zwróci tylko jedną wartość (nawet jeśli w bazie jest dwóch Adamów Mickiewiczów).*

---

## 4. Zapytania UPDATE (Aktualizacja Danych)

Służą do modyfikacji istniejących wpisów, np. przy procesie wypożyczania.

### 4.1. Wypożyczenie roweru
Przypisuje użytkownika o ID 2 do roweru o ID 3.
```sql
UPDATE rowery SET id_klienta = 2 WHERE id_roweru = 3;
```

### 4.2. Zwrot roweru
Usuwa przypisanie użytkownika.
```sql
UPDATE rowery SET id_klienta = NULL WHERE id_roweru = 3;
```

### 4.3. Zmiana danych klienta
Aktualizacja adresu e-mail.
```sql
UPDATE klient SET adres_email = 'nowy.email@example.com' WHERE id_klienta = 1;
```

---

## 5. Zapytania DELETE (Usuwanie Danych)

### 5.1. Usunięcie roweru
Usuwa rekord roweru z bazy.
```sql
DELETE FROM rowery WHERE id_roweru = 5;
```

### 5.2. Usunięcie marki
```sql
DELETE FROM marki WHERE id_marki = 5;
```

---

## 6. Procedura "Smart Insert" (Automatyzacja)

Pytasz, czy da się dodać rower, podając markę, której jeszcze nie ma w bazie, tak aby SQL sam ją dodał?
**Tak, ale wymaga to Procedury Składowanej (Stored Procedure).**

Standardowe polecenie `INSERT` działa tylko na jednej tabeli naraz. Aby zrobić "logikę" (sprawdź -> jeśli nie ma to dodaj -> potem dodaj rower), musimy napisać mały program w SQL (procedurę).

### 6.1. Jak używać?
Po wgraniu procedury do bazy, dodawanie rowerów z automatycznym tworzeniem marki wygląda tak:
```sql
CALL DodajRowerZMarka('Giant', 'Tajwan', 'Roam 1', 2, 'Górski');
```
Parametry (kolejno):
1.  **Nazwa marki** (`'Giant'`)
2.  **Kraj marki** (`'Tajwan'`)
3.  **Model roweru** (`'Roam 1'`)
4.  **Numer stacji** (`2`)
5.  **Nazwa kategorii** (`'Górski'`)

### 6.2. Kod źródłowy procedury
```sql
DELIMITER //

CREATE PROCEDURE DodajRowerZMarka(
    IN p_nazwa_marki VARCHAR(15),
    IN p_kraj_marki VARCHAR(20),
    IN p_model VARCHAR(20),
    IN p_numer_stacji INT,
    IN p_nazwa_kategorii VARCHAR(40)
)
BEGIN
    DECLARE v_id_marki INT;
    DECLARE v_id_stacji INT;
    DECLARE v_id_kategorii INT;

    -- 1. Sprawdź czy marka istnieje
    SELECT id_marki INTO v_id_marki FROM marki 
    WHERE nazwa = p_nazwa_marki LIMIT 1;
    
    IF v_id_marki IS NULL THEN
        INSERT INTO marki (nazwa, kraj) VALUES (p_nazwa_marki, p_kraj_marki);
        SET v_id_marki = LAST_INSERT_ID();
    END IF;

    -- 2. Sprawdź czy stacja istnieje
    SELECT id_stacji INTO v_id_stacji FROM stacje WHERE numer_stacji = p_numer_stacji LIMIT 1;

    IF v_id_stacji IS NULL THEN
        INSERT INTO stacje (numer_stacji) VALUES (p_numer_stacji);
        SET v_id_stacji = LAST_INSERT_ID();
    END IF;
    
    -- 3. Sprawdź czy kategoria istnieje
    SELECT id_kategori INTO v_id_kategorii FROM kategorie WHERE nazwa_kategori = p_nazwa_kategorii LIMIT 1;

    IF v_id_kategorii IS NULL THEN
        INSERT INTO kategorie (nazwa_kategori) VALUES (p_nazwa_kategorii);
        SET v_id_kategorii = LAST_INSERT_ID();
    END IF;

    -- 4. Wstaw rower
    INSERT INTO rowery (id_marki, id_stacji, id_kategori, model, id_klienta)
    VALUES (v_id_marki, v_id_stacji, v_id_kategorii, p_model, NULL);
    
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
