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

    -- 1. Sprawdź czy marka istnieje, jeśli nie - dodaj ją
    SELECT id_marki INTO v_id_marki 
    FROM marki 
    WHERE nazwa COLLATE utf8mb4_polish_ci = p_nazwa_marki COLLATE utf8mb4_polish_ci 
    LIMIT 1;
    
    IF v_id_marki IS NULL THEN
        INSERT INTO marki (nazwa, kraj) VALUES (p_nazwa_marki, p_kraj_marki);
        SET v_id_marki = LAST_INSERT_ID();
    END IF;

    -- 2. Sprawdź czy stacja istnieje, jeśli nie - dodaj ją
    SELECT id_stacji INTO v_id_stacji FROM stacje WHERE numer_stacji = p_numer_stacji LIMIT 1;

    IF v_id_stacji IS NULL THEN
        INSERT INTO stacje (numer_stacji) VALUES (p_numer_stacji);
        SET v_id_stacji = LAST_INSERT_ID();
    END IF;
    
    -- 3. Sprawdź czy kategoria istnieje, jeśli nie - dodaj ją
    SELECT id_kategori INTO v_id_kategorii 
    FROM kategorie 
    WHERE nazwa_kategori COLLATE utf8mb4_polish_ci = p_nazwa_kategorii COLLATE utf8mb4_polish_ci 
    LIMIT 1;

    IF v_id_kategorii IS NULL THEN
        INSERT INTO kategorie (nazwa_kategori) VALUES (p_nazwa_kategorii);
        SET v_id_kategorii = LAST_INSERT_ID();
    END IF;

    -- 4. Wstaw rower
    INSERT INTO rowery (id_marki, id_stacji, id_kategori, model, id_klienta)
    VALUES (v_id_marki, v_id_stacji, v_id_kategorii, p_model, NULL);
    
END //

DELIMITER ;

-- PRZYKŁAD UŻYCIA:
-- CALL DodajRowerZMarka('Giant', 'Tajwan', 'Roam 1', 2, 'Górski');
