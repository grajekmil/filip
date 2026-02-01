<?php
require_once 'config.php';
require_once 'templates/header.php';

$message = '';

// Obsługa dodawania (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imie = trim($_POST['imie']);
    $nazwisko = trim($_POST['nazwisko']);
    $email = trim($_POST['email']);

    if ($imie && $nazwisko && $email) {
        try {
            $stmt = $pdo->prepare("INSERT INTO klient (imie, nazwisko, adres_email) VALUES (?, ?, ?)");
            $stmt->execute([$imie, $nazwisko, $email]);
            $message = '<div class="alert alert-success">Klient ' . htmlspecialchars($imie . ' ' . $nazwisko) . ' dodany.</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-error">Błąd: ' . $e->getMessage() . '</div>';
        }
    }
}

// Lista klientów
$klienci = $pdo->query("SELECT * FROM klient ORDER BY nazwisko")->fetchAll();
?>

<?= $message ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">

    <!-- Formularz Dodawania -->
    <div class="card">
        <h3>Zarejestruj Klienta</h3>
        <form method="POST" action="clients.php">
            <div class="form-group">
                <label for="imie">Imię</label>
                <input type="text" id="imie" name="imie" required placeholder="Jan">
            </div>
            <div class="form-group">
                <label for="nazwisko">Nazwisko</label>
                <input type="text" id="nazwisko" name="nazwisko" required placeholder="Kowalski">
            </div>
            <div class="form-group">
                <label for="email">Adres Email</label>
                <input type="email" id="email" name="email" required placeholder="jan@example.com">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Zarejestruj</button>
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
                            <a href="#" style="color: #8b949e; margin-right: 10px;">Edytuj</a>
                            <a href="#" style="color: #da3633;">Usuń</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once 'templates/footer.php'; ?>