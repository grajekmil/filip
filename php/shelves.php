<?php
require_once 'config.php';
require_once 'templates/header.php';

$message = '';

// Obsługa usuwania (GET)
if (isset($_GET['delete'])) {
    $id_do_usuniecia = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM polki WHERE id_polki = ?");
        $stmt->execute([$id_do_usuniecia]);
        $message = '<div class="alert alert-success">Półka została usunięta.</div>';
    } catch (PDOException $e) {
        $message = '<div class="alert alert-error">Nie można usunąć półki: ' . $e->getMessage() . '</div>';
    }
}

// Obsługa dodawania (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numer_polki = trim($_POST['numer_polki']);

    if ($numer_polki) {
        try {
            $stmt = $pdo->prepare("INSERT INTO polki (numer_polki) VALUES (?)");
            $stmt->execute([$numer_polki]);
            $message = '<div class="alert alert-success">Półka nr ' . htmlspecialchars($numer_polki) . ' dodana.</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-error">Błąd: ' . $e->getMessage() . '</div>';
        }
    }
}

// Lista półek
$polki = $pdo->query("SELECT * FROM polki ORDER BY numer_polki")->fetchAll();
?>

<?= $message ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">

    <!-- Formularz Dodawania -->
    <div class="card">
        <h3>Dodaj Półkę</h3>
        <form method="POST" action="shelves.php">
            <div class="form-group">
                <label for="numer_polki">Numer Półki</label>
                <input type="number" id="numer_polki" name="numer_polki" required placeholder="np. 12">
            </div>
            <button type="submit" class="btn btn-success" style="width: 100%;">Dodaj</button>
        </form>
    </div>

    <!-- Lista -->
    <div class="card">
        <h3 style="margin-top: 0;">Lista Półek w Bibliotece</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Numer Półki</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($polki as $polka): ?>
                    <tr>
                        <td>
                            <?= $polka['id_polki'] ?>
                        </td>
                        <td><strong>Półka nr
                                <?= htmlspecialchars($polka['numer_polki']) ?>
                            </strong></td>
                        <td>
                            <a href="shelves.php?delete=<?= $polka['id_polki'] ?>" class="btn btn-danger"
                                style="padding: 4px 10px; font-size: 0.8rem;"
                                onclick="return confirm('Czy na pewno chcesz usunąć tę półkę? Usunie to również książki na niej stojące (CASCADE)!');">Usuń</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once 'templates/footer.php'; ?>