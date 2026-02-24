# System Wypożyczalni Rowerów (Full Stack PHP)

Nowoczesny, responsywny system do zarządzania wypożyczalnią rowerów zbudowany w czystym PHP bez użycia frameworków. Projekt wykorzystuje architekturę kontenerową (Docker), bezpieczne połączenia z bazą danych (PDO) oraz zaawansowaną logikę SQL (Procedury Składowane).

## ✨ Główne Funkcje

- **Dashboard Statystyk**: Podgląd na żywo liczby rowerów, wypożyczeń, dostępnych maszyn, marek i klientów.
- **Zarządzanie Rowerami**: Pełna lista z dynamicznym statusem dostępności (model, marka, stacja).
- **Inteligentne Dodawanie**: Formularz z autozupełnianiem (Datalist) i automatycznym rozpoznawaniem marek/stacji.
- **Baza Klientów**: Pełny moduł CRUD z zabezpieczeniem przed usuwaniem osób z aktywnymi wypożyczeniami.
- **Zarządzanie Stacjami**: Organizacja punktów odbioru rowerów z kaskadowym usuwaniem.
- **Dark Mode**: Elegancki, ciemny interfejs inspirowany nowoczesnymi standardami UI.

## 🚀 Szybki Start (Docker)

Najprostszy sposób na uruchomienie projektu to użycie Docker Compose.

1.  **Sklonuj repozytorium**
2.  **Uruchom kontenery**:
    ```bash
    docker-compose up -d
    ```
3.  **Otwórz w przeglądarce**:
    - Aplikacja: [http://localhost:9071](http://localhost:9071)
    - phpMyAdmin: [http://localhost:8081](http://localhost:8081)

## 🛠️ Technologie

- **Backend**: PHP 8.2 (Vanilla)
- **Baza Danych**: MariaDB 10.11
- **Stylizacja**: Vanilla CSS (Custom Properties, Grid, Flexbox)
- **Konteneryzacja**: Docker & Docker Compose

## 📂 Struktura Projektu

- `/php` - Kod źródłowy aplikacji webowej
- `/php/templates` - Komponenty wspólne (nagłówek, stopka)
- `/php/loans.php` - Moduł wypożyczeń/zwrotów
- `docker-compose.yaml` - Konfiguracja infrastruktury
- `procedura_smart_insert.sql` - Logika biznesowa (Smart Insert)
- `dokumentacja_techniczna.md` - Szczegółowy opis architektury

## 🔒 Bezpieczeństwo

Aplikacja implementuje:
- Ochronę przed **SQL Injection** poprzez PDO Prepared Statements.
- Ochronę przed **XSS** poprzez escapowanie danych wyjściowych (`htmlspecialchars`).
- Bezpieczną konfigurację opartą o zmienne środowiskowe.

---
*Projekt stworzony w celach edukacyjnych i demonstracyjnych (refaktoryzowany z bazy bibliotecznej).*
