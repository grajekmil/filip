<?php
require_once 'config.php';
require_once 'templates/header.php';

$message = '';

// Obsługa formularza (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tytul = trim($_POST['tytul']);
    $imie_autora = trim($_POST['imie_autora']);
    $nazwisko_autora = trim($_POST['nazwisko_autora']);
    $nazwa_kategorii = trim($_POST['nazwa_kategorii']);
    $numer_polki = trim($_POST['numer_polki']);

    if ($tytul && $imie_autora && $nazwisko_autora && $nazwa_kategorii && $numer_polki) {
        try {
            // Wywołanie procedury składowanej Smart Insert
            $stmt = $pdo->prepare("CALL DodajKsiazkeZAutorem(?, ?, ?, ?, ?)");
            $stmt->execute([$imie_autora, $nazwisko_autora, $tytul, $numer_polki, $nazwa_kategorii]);
            $message = '<div class="alert alert-success">Książka została dodana pomyślnie!</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-error">Błąd bazy danych: ' . $e->getMessage() . '</div>';
        }
    } else {
        $message = '<div class="alert alert-error">Proszę wypełnić wszystkie pola.</div>';
    }
}

// Pobieranie danych do list
$autorzy = $pdo->query("SELECT * FROM autorzy ORDER BY nazwisko")->fetchAll();
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
            <label for="imie_autora">Autor</label>
            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <input type="text" name="imie_autora" id="imie_autora" required placeholder="Imię"
                        list="imiona_list" style="width: 100%;">
                    <datalist id="imiona_list">
                        <?php foreach ($autorzy as $autor): ?>
                            <option value="<?= htmlspecialchars($autor['imie']) ?>">
                                <?= htmlspecialchars($autor['imie']) ?>
                            </option>
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <div style="flex: 1;">
                    <input type="text" name="nazwisko_autora" id="nazwisko_autora" required placeholder="Nazwisko"
                        list="nazwiska_list" style="width: 100%;">
                    <datalist id="nazwiska_list">
                        <?php foreach ($autorzy as $autor): ?>
                                <option value="<?= htmlspecialchars($autor['nazwisko']) ?>"><?= htmlspecialchars($autor['nazwisko']) ?>
                            </option>
                        <?php endforeach; ?>
                    </datalist>
                </div>
            </div>
            <small style="color: #8b949e; display: block; margin-top: 5px;">Zacznij pisać aby zobaczyć sugestie lub
                wpisz nowego autora.</small>
        </div>

        <div class="form-group">
            <label for="nazwa_kategorii">Kategoria</label>
            <input type="text" id="nazwa_kategorii" name="nazwa_kategorii" required placeholder="Wpisz kategorię"
                list="kategorie_list">
            <datalist id="kategorie_list">
                <?php foreach ($kategorie as $kategoria): ?>
                        <option value="<?= htmlspecialchars($kategoria['nazwa_kategori']) ?>"><?= htmlspecialchars($kategoria['nazwa_kategori']) ?>
                            </option>
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
                        <option value="<?= htmlspecialchars($polka['numer_polki']) ?>">Półka nr <?= htmlspecialchars($polka['numer_polki']) ?>
                            </option>
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