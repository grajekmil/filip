-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mariadb:3306
-- Generation Time: Feb 02, 2026 at 09:17 AM
-- Wersja serwera: 10.11.15-MariaDB-ubu2204
-- Wersja PHP: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `biblioteka`
--

DELIMITER $$
--
-- Procedury
--
CREATE DEFINER=`root`@`%` PROCEDURE `DodajKsiazkeZAutorem` (IN `p_imie_autora` VARCHAR(15), IN `p_nazwisko_autora` VARCHAR(20), IN `p_tytul` VARCHAR(20), IN `p_numer_polki` INT, IN `p_nazwa_kategorii` VARCHAR(40))   BEGIN
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
    
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `autorzy`
--

CREATE TABLE `autorzy` (
  `id_autora` int(11) NOT NULL,
  `imie` varchar(15) NOT NULL,
  `nazwisko` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `autorzy`
--

INSERT INTO `autorzy` (`id_autora`, `imie`, `nazwisko`) VALUES
(1, 'Zbigniew', 'Herbert'),
(2, 'Siergiej', 'Jesienin'),
(3, 'Henryk', 'Sienkiewicz'),
(4, 'Adam', 'Mickiewicz'),
(8, 'Juliusz', 'Słowacki'),
(9, 'Jan', 'Brzechwa'),
(10, 'Fiodor', 'Dostojewski');

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
(1, 'poezja'),
(3, 'dramat'),
(4, 'fantastyka'),
(5, 'Sci-Fi'),
(6, 'powieść');

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

--
-- Zrzut danych tabeli `klient`
--

INSERT INTO `klient` (`id_klienta`, `imie`, `nazwisko`, `adres_email`) VALUES
(1, 'Filip', 'Szostak-Sobaslki', 'szostak.dev@gmail.com'),
(5, 'jan', 'Kowalski', 'jan.kowalski@gmail.com'),
(4, 'Mariusz', 'Szostak-Sobalski', 'mariusz.szostak@gmail.com');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `ksiazki`
--

CREATE TABLE `ksiazki` (
  `id_ksiazki` int(11) NOT NULL,
  `id_autora` int(11) NOT NULL,
  `id_polki` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `tytul` varchar(20) NOT NULL,
  `id_klienta` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `ksiazki`
--

INSERT INTO `ksiazki` (`id_ksiazki`, `id_autora`, `id_polki`, `id_kategori`, `tytul`, `id_klienta`) VALUES
(1, 4, 1, 3, 'Dziady cz.III', NULL),
(2, 3, 2, 6, 'Que Vadis', NULL),
(3, 2, 3, 1, 'Tavern Moscow', NULL),
(4, 1, 3, 1, 'Wiersze Zebrane', NULL),
(11, 8, 1, 3, 'Balladyna', NULL),
(12, 9, 8, 1, 'Wiersze', NULL),
(13, 10, 4, 6, 'Zbrodnia i Kara', NULL),
(14, 10, 1, 6, 'Białe Noce', NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `polki`
--

CREATE TABLE `polki` (
  `id_polki` int(11) NOT NULL,
  `numer_polki` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `polki`
--

INSERT INTO `polki` (`id_polki`, `numer_polki`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `autorzy`
--
ALTER TABLE `autorzy`
  ADD PRIMARY KEY (`id_autora`);

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
  ADD UNIQUE KEY `imie` (`imie`,`nazwisko`,`adres_email`);

--
-- Indeksy dla tabeli `ksiazki`
--
ALTER TABLE `ksiazki`
  ADD PRIMARY KEY (`id_ksiazki`),
  ADD KEY `id_autora` (`id_autora`,`id_polki`,`id_kategori`,`id_klienta`),
  ADD KEY `id_klienta` (`id_klienta`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `id_polki` (`id_polki`);

--
-- Indeksy dla tabeli `polki`
--
ALTER TABLE `polki`
  ADD PRIMARY KEY (`id_polki`),
  ADD UNIQUE KEY `numer_polki` (`numer_polki`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `autorzy`
--
ALTER TABLE `autorzy`
  MODIFY `id_autora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT dla tabeli `kategorie`
--
ALTER TABLE `kategorie`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT dla tabeli `klient`
--
ALTER TABLE `klient`
  MODIFY `id_klienta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT dla tabeli `ksiazki`
--
ALTER TABLE `ksiazki`
  MODIFY `id_ksiazki` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT dla tabeli `polki`
--
ALTER TABLE `polki`
  MODIFY `id_polki` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `ksiazki`
--
ALTER TABLE `ksiazki`
  ADD CONSTRAINT `ksiazki_ibfk_1` FOREIGN KEY (`id_autora`) REFERENCES `autorzy` (`id_autora`),
  ADD CONSTRAINT `ksiazki_ibfk_2` FOREIGN KEY (`id_klienta`) REFERENCES `klient` (`id_klienta`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `ksiazki_ibfk_3` FOREIGN KEY (`id_kategori`) REFERENCES `kategorie` (`id_kategori`) ON DELETE CASCADE,
  ADD CONSTRAINT `ksiazki_ibfk_4` FOREIGN KEY (`id_polki`) REFERENCES `polki` (`id_polki`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
