<?php
require_once 'config.php';
require_once 'templates/header.php';

$message = '';

// Obsługa zwrotu książki (GET)
if (isset($_GET['return'])) {
    $id_ksiazki = $_GET['return'];
    try {
        $stmt = $pdo->prepare("UPDATE ksiazki SET id_klienta = NULL WHERE id_ksiazki = ?");
        $stmt->execute([$id_ksiazki]);
        $message = '<div class="alert alert-success">Książka została zwrócona.</div>';
    } catch (PDOException $e) {
        $message = '<div class="alert alert-error">Błąd: ' . $e->getMessage() . '</div>';
    }
}

// Obsługa wypożyczenia (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_ksiazki = $_POST['id_ksiazki'];
    $id_klienta = $_POST['id_klienta'];

    if ($id_ksiazki && $id_klienta) {
        try {
            $stmt = $pdo->prepare("UPDATE ksiazki SET id_klienta = ? WHERE id_ksiazki = ?");
            $stmt->execute([$id_klienta, $id_ksiazki]);
            $message = '<div class="alert alert-success">Książka została wypożyczona.</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-error">Błąd: ' . $e->getMessage() . '</div>';
        }
    } else {
        $message = '<div class="alert alert-error">Proszę wybrać książkę i klienta.</div>';
    }
}

// Pobranie dostępnych książek
$available_books = $pdo->query("
    SELECT k.id_ksiazki, k.tytul, a.imie, a.nazwisko 
    FROM ksiazki k 
    JOIN autorzy a ON k.id_autora = a.id_autora 
    WHERE k.id_klienta IS NULL 
    ORDER BY k.tytul
")->fetchAll();

// Pobranie klientów
$clients = $pdo->query("SELECT id_klienta, imie, nazwisko FROM klient ORDER BY nazwisko")->fetchAll();

// Pobranie aktualnych wypożyczeń
$loans = $pdo->query("
    SELECT k.id_ksiazki, k.tytul, a.imie AS a_imie, a.nazwisko AS a_nazwisko, kl.imie AS kl_imie, kl.nazwisko AS kl_nazwisko 
    FROM ksiazki k 
    JOIN autorzy a ON k.id_autora = a.id_autora 
    JOIN klient kl ON k.id_klienta = kl.id_klienta 
    ORDER BY k.id_ksiazki DESC
")->fetchAll();
?>

<?= $message ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">

    <!-- Formularz Wypożyczania -->
    <div class="card">
        <h3>Wypożycz Książkę</h3>
        <form method="POST" action="loans.php">
            <div class="form-group">
                <label for="id_ksiazki">Książka (dostępne)</label>
                <select id="id_ksiazki" name="id_ksiazki" required>
                    <option value="">-- Wybierz książkę --</option>
                    <?php foreach ($available_books as $book): ?>
                        <option value="<?= $book['id_ksiazki'] ?>">
                            <?= htmlspecialchars($book['tytul'] . ' - ' . $book['imie'] . ' ' . $book['nazwisko']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_klienta">Klient</label>
                <select id="id_klienta" name="id_klienta" required>
                    <option value="">-- Wybierz klienta --</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= $client['id_klienta'] ?>">
                            <?= htmlspecialchars($client['imie'] . ' ' . $client['nazwisko']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Wypożycz</button>
        </form>
    </div>

    <!-- Lista Wypożyczeń -->
    <div class="card">
        <h3 style="margin-top: 0;">Aktualne Wypożyczenia</h3>
        <table>
            <thead>
                <tr>
                    <th>Książka</th>
                    <th>Klient</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($loans)): ?>
                    <tr>
                        <td colspan="3" style="text-align: center; color: #8b949e;">Brak aktywnych wypożyczeń.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($loans as $loan): ?>
                        <tr>
                            <td>
                                <strong>
                                    <?= htmlspecialchars($loan['tytul']) ?>
                                </strong><br>
                                <small style="color: #8b949e;">
                                    <?= htmlspecialchars($loan['a_imie'] . ' ' . $loan['a_nazwisko']) ?>
                                </small>
                            </td>
                            <td>
                                <?= htmlspecialchars($loan['kl_imie'] . ' ' . $loan['kl_nazwisko']) ?>
                            </td>
                            <td>
                                <a href="loans.php?return=<?= $loan['id_ksiazki'] ?>" class="btn btn-success"
                                    style="padding: 4px 10px; font-size: 0.8rem;"
                                    onclick="return confirm('Czy na pewno chcesz zwrócić tę książkę?');">Zwróć</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once 'templates/footer.php'; ?>