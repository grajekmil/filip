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
    SELECT id_autora INTO v_id_autora 
    FROM autorzy 
    WHERE imie COLLATE utf8mb4_general_ci = p_imie_autora COLLATE utf8mb4_general_ci 
      AND nazwisko COLLATE utf8mb4_general_ci = p_nazwisko_autora COLLATE utf8mb4_general_ci 
    LIMIT 1;
    
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
    SELECT id_kategori INTO v_id_kategorii 
    FROM kategorie 
    WHERE nazwa_kategori COLLATE utf8mb4_general_ci = p_nazwa_kategorii COLLATE utf8mb4_general_ci 
    LIMIT 1;

    IF v_id_kategorii IS NULL THEN
        INSERT INTO kategorie (nazwa_kategori) VALUES (p_nazwa_kategorii);
        SET v_id_kategorii = LAST_INSERT_ID();
    END IF;

    -- 4. Wstaw książkę
    INSERT INTO ksiazki (id_autora, id_polki, id_kategori, tytul, id_klienta)
    VALUES (v_id_autora, v_id_polki, v_id_kategorii, p_tytul, NULL);
    
END //

DELIMITER ;

-- PRZYKŁAD UŻYCIA:
-- CALL DodajKsiazkeZAutorem('Wisława', 'Szymborska', 'Wiersze', 2, 'poezja');
