<?php
require_once 'config.php';
require_once 'templates/header.php';

$message = '';

// Obsługa usuwania (GET)
if (isset($_GET['delete'])) {
    $id_do_usuniecia = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM stacje WHERE id_stacji = ?");
        $stmt->execute([$id_do_usuniecia]);
        $message = '<div class="alert alert-success">Stacja została usunięta.</div>';
    } catch (PDOException $e) {
        $message = '<div class="alert alert-error">Nie można usunąć stacji: ' . $e->getMessage() . '</div>';
    }
}

// Obsługa dodawania (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numer_stacji = trim($_POST['numer_stacji']);

    if ($numer_stacji) {
        try {
            $stmt = $pdo->prepare("INSERT INTO stacje (numer_stacji) VALUES (?)");
            $stmt->execute([$numer_stacji]);
            $message = '<div class="alert alert-success">Stacja nr ' . htmlspecialchars($numer_stacji) . ' dodana.</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-error">Błąd: ' . $e->getMessage() . '</div>';
        }
    }
}

// Lista stacji
$stacje = $pdo->query("SELECT * FROM stacje ORDER BY numer_stacji")->fetchAll();
?>

<?= $message ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">

    <!-- Formularz Dodawania -->
    <div class="card">
        <h3>Dodaj Stację</h3>
        <form method="POST" action="stations.php">
            <div class="form-group">
                <label for="numer_stacji">Numer Stacji</label>
                <input type="number" id="numer_stacji" name="numer_stacji" required placeholder="np. 12">
            </div>
            <button type="submit" class="btn btn-success" style="width: 100%;">Dodaj</button>
        </form>
    </div>

    <!-- Lista -->
    <div class="card">
        <h3 style="margin-top: 0;">Lista Stacji w Wypożyczalni</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Numer Stacji</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stacje as $stacja): ?>
                    <tr>
                        <td>
                            <?= $stacja['id_stacji'] ?>
                        </td>
                        <td><strong>Stacja nr
                                <?= htmlspecialchars($stacja['numer_stacji']) ?>
                            </strong></td>
                        <td>
                            <a href="stations.php?delete=<?= $stacja['id_stacji'] ?>" class="btn btn-danger"
                                style="padding: 4px 10px; font-size: 0.8rem;"
                                onclick="return confirm('Czy na pewno chcesz usunąć tę stację? Usunie to również rowery do niej przypisane (CASCADE)!');">Usuń</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once 'templates/footer.php'; ?>