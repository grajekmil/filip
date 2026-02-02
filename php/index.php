<?php
require_once 'config.php';
require_once 'templates/header.php';

// Pobranie statystyk
$stats = [
    'books' => $pdo->query("SELECT COUNT(*) FROM ksiazki")->fetchColumn(),
    'authors' => $pdo->query("SELECT COUNT(*) FROM autorzy")->fetchColumn(),
    'clients' => $pdo->query("SELECT COUNT(*) FROM klient")->fetchColumn(),
    'borrowed' => $pdo->query("SELECT COUNT(*) FROM ksiazki WHERE id_klienta IS NOT NULL")->fetchColumn(),
    'available' => $pdo->query("SELECT COUNT(*) FROM ksiazki WHERE id_klienta IS NULL")->fetchColumn()
];

// Pobranie listy książek z pełnymi danymi (JOIN)
$sql = "SELECT 
            k.id_ksiazki, 
            k.tytul,
            a.imie AS autor_imie, 
            a.nazwisko AS autor_nazwisko, 
            kat.nazwa_kategori, 
            p.numer_polki,
            kl.imie AS klient_imie, 
            kl.nazwisko AS klient_nazwisko
        FROM ksiazki k
        JOIN autorzy a ON k.id_autora = a.id_autora
        JOIN kategorie kat ON k.id_kategori = kat.id_kategori
        JOIN polki p ON k.id_polki = p.id_polki
        LEFT JOIN klient kl ON k.id_klienta = kl.id_klienta
        ORDER BY k.id_ksiazki DESC";

$stmt = $pdo->query($sql);
$books = $stmt->fetchAll();
?>

<div class="stats-grid">
    <div class="stat-card">
        <div>Liczba Książek</div>
        <div class="stat-number">
            <?= $stats['books'] ?>
        </div>
    </div>
    <div class="stat-card">
        <div>Autorzy</div>
        <div class="stat-number">
            <?= $stats['authors'] ?>
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
        <h2 style="margin: 0;">Lista Książek</h2>
        <a href="add_book.php" class="btn btn-primary">+ Dodaj Książkę</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tytuł</th>
                <th>Autor</th>
                <th>Kategoria</th>
                <th>Półka</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($books as $book): ?>
                <tr>
                    <td>
                        <?= $book['id_ksiazki'] ?>
                    </td>
                    <td><strong>
                            <?= htmlspecialchars($book['tytul']) ?>
                        </strong></td>
                    <td>
                        <?= htmlspecialchars($book['autor_imie'] . ' ' . $book['autor_nazwisko']) ?>
                    </td>
                    <td><span
                            style="background: rgba(88, 166, 255, 0.15); color: #58a6ff; padding: 2px 8px; border-radius: 12px; font-size: 0.85rem;">
                            <?= htmlspecialchars($book['nazwa_kategori']) ?>
                        </span></td>
                    <td>Nr
                        <?= $book['numer_polki'] ?>
                    </td>
                    <td>
                        <?php if ($book['klient_imie']): ?>
                            <span style="color: #da3633;">Wypożyczona</span>
                            <div style="font-size: 0.8rem; color: #8b949e;">
                                przez:
                                <?= htmlspecialchars($book['klient_imie'] . ' ' . $book['klient_nazwisko']) ?>
                            </div>
                        <?php else: ?>
                            <span style="color: #238636;">Dostępna</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'templates/footer.php'; ?>