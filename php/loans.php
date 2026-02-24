<?php
require_once 'config.php';
require_once 'templates/header.php';

$message = '';

// Obsługa zwrotu roweru (GET)
if (isset($_GET['return'])) {
    $id_roweru = $_GET['return'];
    try {
        $stmt = $pdo->prepare("UPDATE rowery SET id_klienta = NULL WHERE id_roweru = ?");
        $stmt->execute([$id_roweru]);
        $message = '<div class="alert alert-success">Rower został zwrócony.</div>';
    } catch (PDOException $e) {
        $message = '<div class="alert alert-error">Błąd: ' . $e->getMessage() . '</div>';
    }
}

// Obsługa wypożyczenia (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_roweru = $_POST['id_roweru'];
    $id_klienta = $_POST['id_klienta'];

    if ($id_roweru && $id_klienta) {
        try {
            $stmt = $pdo->prepare("UPDATE rowery SET id_klienta = ? WHERE id_roweru = ?");
            $stmt->execute([$id_klienta, $id_roweru]);
            $message = '<div class="alert alert-success">Rower został wypożyczony.</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-error">Błąd: ' . $e->getMessage() . '</div>';
        }
    } else {
        $message = '<div class="alert alert-error">Proszę wybrać rower i klienta.</div>';
    }
}

// Pobranie dostępnych rowerów
$available_bikes = $pdo->query("
    SELECT r.id_roweru, r.model, m.nazwa AS marka_nazwa 
    FROM rowery r 
    JOIN marki m ON r.id_marki = m.id_marki 
    WHERE r.id_klienta IS NULL 
    ORDER BY r.model
")->fetchAll();

// Pobranie klientów
$clients = $pdo->query("SELECT id_klienta, imie, nazwisko FROM klient ORDER BY nazwisko")->fetchAll();

// Pobranie aktualnych wypożyczeń
$loans = $pdo->query("
    SELECT r.id_roweru, r.model, m.nazwa AS marka_nazwa, kl.imie AS kl_imie, kl.nazwisko AS kl_nazwisko 
    FROM rowery r 
    JOIN marki m ON r.id_marki = m.id_marki 
    JOIN klient kl ON r.id_klienta = kl.id_klienta 
    ORDER BY r.id_roweru DESC
")->fetchAll();
?>

<?= $message ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">

    <!-- Formularz Wypożyczania -->
    <div class="card">
        <h3>Wypożycz Rower</h3>
        <form method="POST" action="loans.php">
            <div class="form-group">
                <label for="id_roweru">Rower (dostępne)</label>
                <select id="id_roweru" name="id_roweru" required>
                    <option value="">-- Wybierz rower --</option>
                    <?php foreach ($available_bikes as $bike): ?>
                        <option value="<?= $bike['id_roweru'] ?>">
                            <?= htmlspecialchars($bike['model'] . ' - ' . $bike['marka_nazwa']) ?>
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
                    <th>Rower</th>
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
                                    <?= htmlspecialchars($loan['model']) ?>
                                </strong><br>
                                <small style="color: #8b949e;">
                                    Marka: <?= htmlspecialchars($loan['marka_nazwa']) ?>
                                </small>
                            </td>
                            <td>
                                <?= htmlspecialchars($loan['kl_imie'] . ' ' . $loan['kl_nazwisko']) ?>
                            </td>
                            <td>
                                <a href="loans.php?return=<?= $loan['id_roweru'] ?>" class="btn btn-success"
                                    style="padding: 4px 10px; font-size: 0.8rem;"
                                    onclick="return confirm('Czy na pewno chcesz zwrócić ten rower?');">Zwróć</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</div>

<?php require_once 'templates/footer.php'; ?>