<?php
require_once 'config.php';
require_once 'templates/header.php';

$message = '';

// Obsługa formularza (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $model = trim($_POST['model']);
    $marka_full = trim($_POST['marka_full']);
    $nazwa_kategorii = trim($_POST['nazwa_kategorii']);
    $numer_stacji = trim($_POST['status_stacji']); // Note: renamed from numer_polki in form

    if ($model && $marka_full && $nazwa_kategorii && $numer_stacji) {
        $parts = explode(' ', $marka_full, 2);
        $nazwa_marki = $parts[0];
        $kraj_marki = isset($parts[1]) ? $parts[1] : '';

        if (empty($kraj_marki)) {
            $message = '<div class="alert alert-error">Proszę podać co najmniej nazwę marki i kraj pochodzenia (oddzielone spacją).</div>';
        } else {
            try {
                // Wywołanie procedury składowanej Smart Insert
                $stmt = $pdo->prepare("CALL DodajRowerZMarka(?, ?, ?, ?, ?)");
                $stmt->execute([$nazwa_marki, $kraj_marki, $model, $numer_stacji, $nazwa_kategorii]);
                $message = '<div class="alert alert-success">Rower został dodany pomyślnie!</div>';
            } catch (PDOException $e) {
                $message = '<div class="alert alert-error">Błąd bazy danych: ' . $e->getMessage() . '</div>';
            }
        }
    } else {
        $message = '<div class="alert alert-error">Proszę wypełnić wszystkie pola.</div>';
    }
}

// Pobieranie danych do list
$marki = $pdo->query("SELECT DISTINCT CONCAT(nazwa, ' ', kraj) as full_name FROM marki ORDER BY nazwa")->fetchAll();
$kategorie = $pdo->query("SELECT * FROM kategorie ORDER BY nazwa_kategori")->fetchAll();
$stacje = $pdo->query("SELECT * FROM stacje ORDER BY numer_stacji")->fetchAll();
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2 style="border-bottom: 1px solid #30363d; padding-bottom: 15px; margin-top: 0;">Dodaj Nowy Rower</h2>

    <?= $message ?>

    <form method="POST" action="add_bike.php">
        <div class="form-group">
            <label for="model">Model Roweru</label>
            <input type="text" id="model" name="model" required placeholder="np. Roam 1">
        </div>

        <div class="form-group">
            <label for="marka_full">Marka (Nazwa i Kraj)</label>
            <input type="text" name="marka_full" id="marka_full" required placeholder="np. Giant Tajwan"
                list="marki_list" style="width: 100%;">
            <datalist id="marki_list">
                <?php foreach ($marki as $marka): ?>
                    <option value="<?= htmlspecialchars($marka['full_name']) ?>">
                        <?= htmlspecialchars($marka['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </datalist>
            <small style="color: #8b949e; display: block; margin-top: 5px;">Wpisz nazwę marki i kraj oddzielone
                spacją.</small>
        </div>

        <div class="form-group">
            <label for="nazwa_kategorii">Kategoria</label>
            <input type="text" id="nazwa_kategorii" name="nazwa_kategorii" required placeholder="Wpisz kategorię"
                list="kategorie_list">
            <datalist id="kategorie_list">
                <?php foreach ($kategorie as $kategoria): ?>
                    <option value="<?= htmlspecialchars($kategoria['nazwa_kategori']) ?>">
                        <?= htmlspecialchars($kategoria['nazwa_kategori']) ?>
                    </option>
                <?php endforeach; ?>
            </datalist>
            <small style="color: #8b949e; display: block; margin-top: 5px;">Wybierz z listy lub wpisz nową
                kategorię.</small>
        </div>

        <div class="form-group">
            <label for="status_stacji">Stacja</label>
            <input type="number" id="status_stacji" name="status_stacji" required placeholder="Numer stacji"
                list="stacje_list">
            <datalist id="stacje_list">
                <?php foreach ($stacje as $stacja): ?>
                    <option value="<?= htmlspecialchars($stacja['numer_stacji']) ?>">Stacja nr
                        <?= htmlspecialchars($stacja['numer_stacji']) ?>
                    </option>
                <?php endforeach; ?>
            </datalist>
            <small style="color: #8b949e; display: block; margin-top: 5px;">Wybierz z listy lub wpisz nowy
                numer.</small>
        </div>

        <div style="margin-top: 25px; text-align: right;">
            <a href="index.php" class="btn" style="color: #8b949e; margin-right: 10px;">Anuluj</a>
            <button type="submit" class="btn btn-primary">Zapisz Rower</button>
        </div>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>