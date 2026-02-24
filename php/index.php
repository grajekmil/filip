<?php
require_once 'config.php';
require_once 'templates/header.php';

// Pobranie statystyk
$stats = [
    'bikes' => $pdo->query("SELECT COUNT(*) FROM rowery")->fetchColumn(),
    'brands' => $pdo->query("SELECT COUNT(*) FROM marki")->fetchColumn(),
    'clients' => $pdo->query("SELECT COUNT(*) FROM klient")->fetchColumn(),
    'borrowed' => $pdo->query("SELECT COUNT(*) FROM rowery WHERE id_klienta IS NOT NULL")->fetchColumn(),
    'available' => $pdo->query("SELECT COUNT(*) FROM rowery WHERE id_klienta IS NULL")->fetchColumn()
];

// Pobranie listy rowerów z pełnymi danymi (JOIN)
$sql = "SELECT 
            r.id_roweru, 
            r.model,
            m.nazwa AS marka_nazwa, 
            m.kraj AS marka_kraj, 
            kat.nazwa_kategori, 
            s.numer_stacji,
            kl.imie AS klient_imie, 
            kl.nazwisko AS klient_nazwisko
        FROM rowery r
        JOIN marki m ON r.id_marki = m.id_marki
        JOIN kategorie kat ON r.id_kategori = kat.id_kategori
        JOIN stacje s ON r.id_stacji = s.id_stacji
        LEFT JOIN klient kl ON r.id_klienta = kl.id_klienta
        ORDER BY r.id_roweru DESC";

$stmt = $pdo->query($sql);
$bikes = $stmt->fetchAll();
?>

<div class="stats-grid">
    <div class="stat-card">
        <div>Liczba Rowerów</div>
        <div class="stat-number">
            <?= $stats['bikes'] ?>
        </div>
    </div>
    <div class="stat-card">
        <div>Wypożyczone</div>
        <div class="stat-number" style="color: #da3633;">
            <?= $stats['borrowed'] ?>
        </div>
    </div>
    <div class="stat-card">
        <div>Dostępne</div>
        <div class="stat-number" style="color: #238636;">
            <?= $stats['available'] ?>
        </div>
    </div>
    <div class="stat-card">
        <div>Marki</div>
        <div class="stat-number">
            <?= $stats['brands'] ?>
        </div>
    </div>
    <div class="stat-card">
        <div>Klienci</div>
        <div class="stat-number">
            <?= $stats['clients'] ?>
        </div>
    </div>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">Lista Rowerów</h2>
        <a href="add_bike.php" class="btn btn-primary">+ Dodaj Rower</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Model</th>
                <th>Marka</th>
                <th>Kategoria</th>
                <th>Stacja</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bikes as $bike): ?>
                <tr>
                    <td>
                        <?= $bike['id_roweru'] ?>
                    </td>
                    <td><strong>
                            <?= htmlspecialchars($bike['model']) ?>
                        </strong></td>
                    <td>
                        <?= htmlspecialchars($bike['marka_nazwa']) ?> (<?= htmlspecialchars($bike['marka_kraj']) ?>)
                    </td>
                    <td><span
                            style="background: rgba(88, 166, 255, 0.15); color: #58a6ff; padding: 2px 8px; border-radius: 12px; font-size: 0.85rem;">
                            <?= htmlspecialchars($bike['nazwa_kategori']) ?>
                        </span></td>
                    <td>Nr
                        <?= $bike['numer_stacji'] ?>
                    </td>
                    <td>
                        <?php if ($bike['klient_imie']): ?>
                            <span style="color: #da3633;">Wypożyczony</span>
                            <div style="font-size: 0.8rem; color: #8b949e;">
                                przez:
                                <?= htmlspecialchars($bike['klient_imie'] . ' ' . $bike['klient_nazwisko']) ?>
                            </div>
                        <?php else: ?>
                            <span style="color: #238636;">Dostępny</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'templates/footer.php'; ?>