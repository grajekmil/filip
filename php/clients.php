<?php
require_once 'config.php';
require_once 'templates/header.php';

$message = '';
$editMode = false;
$editClient = null;

// Obsługa usuwania (GET)
if (isset($_GET['delete'])) {
    $id_do_usuniecia = $_GET['delete'];
    try {
        // Sprawdź czy klient ma wypożyczone książki
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM ksiazki WHERE id_klienta = ?");
        $stmt->execute([$id_do_usuniecia]);
        $liczba_wypozyczen = $stmt->fetchColumn();

        if ($liczba_wypozyczen > 0) {
            $message = '<div class="alert alert-error">Nie można usunąć klienta. Klient ma ' . $liczba_wypozyczen . ' aktywnych wypożyczeń. Najpierw zwróć wszystkie książki.</div>';
        } else {
            $stmt = $pdo->prepare("DELETE FROM klient WHERE id_klienta = ?");
            $stmt->execute([$id_do_usuniecia]);
            $message = '<div class="alert alert-success">Klient został usunięty.</div>';
        }
    } catch (PDOException $e) {
        $message = '<div class="alert alert-error">Błąd: ' . $e->getMessage() . '</div>';
    }
}

// Obsługa edycji - pobranie danych (GET)
if (isset($_GET['edit'])) {
    $id_do_edycji = $_GET['edit'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM klient WHERE id_klienta = ?");
        $stmt->execute([$id_do_edycji]);
        $editClient = $stmt->fetch();
        if ($editClient) {
            $editMode = true;
        } else {
            $message = '<div class="alert alert-error">Klient nie został znaleziony.</div>';
        }
    } catch (PDOException $e) {
        $message = '<div class="alert alert-error">Błąd: ' . $e->getMessage() . '</div>';
    }
}

// Obsługa formularza (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imie = trim($_POST['imie']);
    $nazwisko = trim($_POST['nazwisko']);
    $email = trim($_POST['email']);
    $id_klienta = isset($_POST['id_klienta']) ? $_POST['id_klienta'] : null;

    if ($imie && $nazwisko && $email) {
        try {
            if ($id_klienta) {
                // Tryb edycji - UPDATE
                $stmt = $pdo->prepare("UPDATE klient SET imie = ?, nazwisko = ?, adres_email = ? WHERE id_klienta = ?");
                $stmt->execute([$imie, $nazwisko, $email, $id_klienta]);
                $message = '<div class="alert alert-success">Dane klienta ' . htmlspecialchars($imie . ' ' . $nazwisko) . ' zostały zaktualizowane.</div>';
            } else {
                // Tryb dodawania - INSERT
                $stmt = $pdo->prepare("INSERT INTO klient (imie, nazwisko, adres_email) VALUES (?, ?, ?)");
                $stmt->execute([$imie, $nazwisko, $email]);
                $message = '<div class="alert alert-success">Klient ' . htmlspecialchars($imie . ' ' . $nazwisko) . ' dodany.</div>';
            }
        } catch (PDOException $e) {
            $message = '<div class="alert alert-error">Błąd: ' . $e->getMessage() . '</div>';
        }
    } else {
        $message = '<div class="alert alert-error">Proszę wypełnić wszystkie pola.</div>';
    }
}

// Lista klientów
$klienci = $pdo->query("SELECT * FROM klient ORDER BY nazwisko")->fetchAll();
?>

<?= $message ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">

    <!-- Formularz Dodawania/Edycji -->
    <div class="card">
        <h3><?= $editMode ? 'Edytuj Klienta' : 'Zarejestruj Klienta' ?></h3>
        <form method="POST" action="clients.php">
            <?php if ($editMode): ?>
                <input type="hidden" name="id_klienta" value="<?= $editClient['id_klienta'] ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="imie">Imię</label>
                <input type="text" id="imie" name="imie" required placeholder="Jan" 
                    value="<?= $editMode ? htmlspecialchars($editClient['imie']) : '' ?>">
            </div>
            <div class="form-group">
                <label for="nazwisko">Nazwisko</label>
                <input type="text" id="nazwisko" name="nazwisko" required placeholder="Kowalski"
                    value="<?= $editMode ? htmlspecialchars($editClient['nazwisko']) : '' ?>">
            </div>
            <div class="form-group">
                <label for="email">Adres Email</label>
                <input type="email" id="email" name="email" required placeholder="jan@example.com"
                    value="<?= $editMode ? htmlspecialchars($editClient['adres_email']) : '' ?>">
            </div>
            
            <?php if ($editMode): ?>
                <div style="display: flex; gap: 10px;">
                    <a href="clients.php" class="btn" style="flex: 1; text-align: center; text-decoration: none; color: #8b949e;">Anuluj</a>
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Zapisz Zmiany</button>
                </div>
            <?php else: ?>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Zarejestruj</button>
            <?php endif; ?>
        </form>
    </div>

    <!-- Lista -->
    <div class="card">
        <h3 style="margin-top: 0;">Baza Klientów</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imię i Nazwisko</th>
                    <th>Email</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($klienci as $klient): ?>
                    <tr>
                        <td>
                            <?= $klient['id_klienta'] ?>
                        </td>
                        <td><strong>
                                <?= htmlspecialchars($klient['imie'] . ' ' . $klient['nazwisko']) ?>
                            </strong></td>
                        <td>
                            <?= htmlspecialchars($klient['adres_email']) ?>
                        </td>
                        <td>
                            <a href="clients.php?edit=<?= $klient['id_klienta'] ?>" style="color: #8b949e; margin-right: 10px;">Edytuj</a>
                            <a href="clients.php?delete=<?= $klient['id_klienta'] ?>" 
                               style="color: #da3633;"
                               onclick="return confirm('Czy na pewno chcesz usunąć klienta <?= htmlspecialchars($klient['imie'] . ' ' . $klient['nazwisko']) ?>?');">Usuń</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once 'templates/footer.php'; ?>