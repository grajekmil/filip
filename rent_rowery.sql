-- SQL Dump for Bicycle Rental System
-- Base database: `rent_rowery`

CREATE DATABASE IF NOT EXISTS `rent_rowery`;
USE `rent_rowery`;

-- Usuwanie starych tabel (ważne przy re-imporcie, aby uniknąć śmieci z bazy biblioteki)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `rowery`;
DROP TABLE IF EXISTS `marki`;
DROP TABLE IF EXISTS `kategorie`;
DROP TABLE IF EXISTS `klient`;
DROP TABLE IF EXISTS `stacje`;
SET FOREIGN_KEY_CHECKS = 1;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `rent_rowery`
--

DELIMITER $$
--
-- Procedury
--
CREATE PROCEDURE `DodajRowerZMarka` (IN `p_nazwa_marki` VARCHAR(15), IN `p_kraj_marki` VARCHAR(20), IN `p_model` VARCHAR(20), IN `p_numer_stacji` INT, IN `p_nazwa_kategorii` VARCHAR(40))   BEGIN
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
    
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `marki`
--

CREATE TABLE `marki` (
  `id_marki` int(11) NOT NULL,
  `nazwa` varchar(15) NOT NULL,
  `kraj` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `marki`
--

INSERT INTO `marki` (`id_marki`, `nazwa`, `kraj`) VALUES
(1, 'Giant', 'Tajwan'),
(2, 'Trek', 'USA'),
(3, 'Specialized', 'USA'),
(4, 'Cannondale', 'USA'),
(8, 'Scott', 'Szwajcaria'),
(9, 'Kross', 'Polska'),
(10, 'Canyon', 'Niemcy');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kategorie`
--

CREATE TABLE `kategorie` (
  `id_kategori` int(11) NOT NULL,
  `nazwa_kategori` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `kategorie`
--

INSERT INTO `kategorie` (`id_kategori`, `nazwa_kategori`) VALUES
(1, 'Górski'),
(3, 'Szosowy'),
(4, 'Miejski'),
(5, 'Elektryczny'),
(6, 'Gravel');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `klient`
--

CREATE TABLE `klient` (
  `id_klienta` int(11) NOT NULL,
  `imie` varchar(10) NOT NULL,
  `nazwisko` varchar(20) NOT NULL,
  `adres_email` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rowery`
--

CREATE TABLE `rowery` (
  `id_roweru` int(11) NOT NULL,
  `id_marki` int(11) NOT NULL,
  `id_stacji` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `model` varchar(20) NOT NULL,
  `id_klienta` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `stacje`
--

CREATE TABLE `stacje` (
  `id_stacji` int(11) NOT NULL,
  `numer_stacji` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Indeksy dla tabeli `marki`
--
ALTER TABLE `marki`
  ADD PRIMARY KEY (`id_marki`);

--
-- Indeksy dla tabeli `kategorie`
--
ALTER TABLE `kategorie`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeksy dla tabeli `klient`
--
ALTER TABLE `klient`
  ADD PRIMARY KEY (`id_klienta`),
  ADD UNIQUE KEY `klient_unique` (`imie`,`nazwisko`,`adres_email`);

--
-- Indeksy dla tabeli `rowery`
--
ALTER TABLE `rowery`
  ADD PRIMARY KEY (`id_roweru`),
  ADD KEY `id_marki` (`id_marki`),
  ADD KEY `id_stacji` (`id_stacji`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `id_klienta` (`id_klienta`);

--
-- Indeksy dla tabeli `stacje`
--
ALTER TABLE `stacje`
  ADD PRIMARY KEY (`id_stacji`),
  ADD UNIQUE KEY `numer_stacji` (`numer_stacji`);

--
-- AUTO_INCREMENT dla tabeli `marki`
--
ALTER TABLE `marki`
  MODIFY `id_marki` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `kategorie`
--
ALTER TABLE `kategorie`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `klient`
--
ALTER TABLE `klient`
  MODIFY `id_klienta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `rowery`
--
ALTER TABLE `rowery`
  MODIFY `id_roweru` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `stacje`
--
ALTER TABLE `stacje`
  MODIFY `id_stacji` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ograniczenia dla tabeli `rowery`
--
ALTER TABLE `rowery`
  ADD CONSTRAINT `rowery_marki_fk` FOREIGN KEY (`id_marki`) REFERENCES `marki` (`id_marki`),
  ADD CONSTRAINT `rowery_klient_fk` FOREIGN KEY (`id_klienta`) REFERENCES `klient` (`id_klienta`) ON DELETE SET NULL,
  ADD CONSTRAINT `rowery_kategorie_fk` FOREIGN KEY (`id_kategori`) REFERENCES `kategorie` (`id_kategori`) ON DELETE CASCADE,
  ADD CONSTRAINT `rowery_stacje_fk` FOREIGN KEY (`id_stacji`) REFERENCES `stacje` (`id_stacji`) ON DELETE CASCADE;

COMMIT;
