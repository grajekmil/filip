<?php
require_once 'config.php';
require_once 'templates/header.php';

$message = '';

// Obsługa formularza (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tytul = trim($_POST['tytul']);
    $autor_full = trim($_POST['autor_full']);
    $nazwa_kategorii = trim($_POST['nazwa_kategorii']);
    $numer_polki = trim($_POST['numer_polki']);

    if ($tytul && $autor_full && $nazwa_kategorii && $numer_polki) {
        // Podział autora na imię i nazwisko (po pierwszej spacji)
        $parts = explode(' ', $autor_full, 2);
        $imie_autora = $parts[0];
        $nazwisko_autora = isset($parts[1]) ? $parts[1] : '';

        if (empty($nazwisko_autora)) {
            $message = '<div class="alert alert-error">Proszę podać co najmniej imię i nazwisko autora (oddzielone spacją).</div>';
        } else {
            try {
                // Wywołanie procedury składowanej Smart Insert
                $stmt = $pdo->prepare("CALL DodajKsiazkeZAutorem(?, ?, ?, ?, ?)");
                $stmt->execute([$imie_autora, $nazwisko_autora, $tytul, $numer_polki, $nazwa_kategorii]);
                $message = '<div class="alert alert-success">Książka została dodana pomyślnie!</div>';
            } catch (PDOException $e) {
                $message = '<div class="alert alert-error">Błąd bazy danych: ' . $e->getMessage() . '</div>';
            }
        }
    } else {
        $message = '<div class="alert alert-error">Proszę wypełnić wszystkie pola.</div>';
    }
}

// Pobieranie danych do list
$autorzy = $pdo->query("SELECT DISTINCT CONCAT(imie, ' ', nazwisko) as full_name FROM autorzy ORDER BY nazwisko")->fetchAll();
$kategorie = $pdo->query("SELECT * FROM kategorie ORDER BY nazwa_kategori")->fetchAll();
$polki = $pdo->query("SELECT * FROM polki ORDER BY numer_polki")->fetchAll();
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2 style="border-bottom: 1px solid #30363d; padding-bottom: 15px; margin-top: 0;">Dodaj Nową Książkę</h2>

    <?= $message ?>

    <form method="POST" action="add_book.php">
        <div class="form-group">
            <label for="tytul">Tytuł Książki</label>
            <input type="text" id="tytul" name="tytul" required placeholder="np. W pustyni i w puszczy">
        </div>

        <div class="form-group">
            <label for="autor_full">Autor (Imię i Nazwisko)</label>
            <input type="text" name="autor_full" id="autor_full" required placeholder="np. Adam Mickiewicz"
                list="autorzy_list" style="width: 100%;">
            <datalist id="autorzy_list">
                <?php foreach ($autorzy as $autor): ?>
                    <option value="<?= htmlspecialchars($autor['full_name']) ?>"><?= htmlspecialchars($autor['full_name']) ?></option>
                <?php endforeach; ?>
            </datalist>
            <small style="color: #8b949e; display: block; margin-top: 5px;">Wpisz imię i nazwisko oddzielone spacją.</small>
        </div>

        <div class="form-group">
            <label for="nazwa_kategorii">Kategoria</label>
            <input type="text" id="nazwa_kategorii" name="nazwa_kategorii" required placeholder="Wpisz kategorię"
                list="kategorie_list">
            <datalist id="kategorie_list">
                <?php foreach ($kategorie as $kategoria): ?>
                    <option value="<?= htmlspecialchars($kategoria['nazwa_kategori']) ?>"><?= htmlspecialchars($kategoria['nazwa_kategori']) ?></option>
                <?php endforeach; ?>
            </datalist>
            <small style="color: #8b949e; display: block; margin-top: 5px;">Wybierz z listy lub wpisz nową
                kategorię.</small>
        </div>

        <div class="form-group">
            <label for="numer_polki">Półka</label>
            <input type="number" id="numer_polki" name="numer_polki" required placeholder="Numer półki"
                list="polki_list">
            <datalist id="polki_list">
                <?php foreach ($polki as $polka): ?>
                    <option value="<?= htmlspecialchars($polka['numer_polki']) ?>">Półka nr <?= htmlspecialchars($polka['numer_polki']) ?></option>
                <?php endforeach; ?>
            </datalist>
            <small style="color: #8b949e; display: block; margin-top: 5px;">Wybierz z listy lub wpisz nowy
                numer.</small>
        </div>

        <div style="margin-top: 25px; text-align: right;">
            <a href="index.php" class="btn" style="color: #8b949e; margin-right: 10px;">Anuluj</a>
            <button type="submit" class="btn btn-primary">Zapisz Książkę</button>
        </div>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>