<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Biblioteczny</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <header>
            <div class="logo">
                <a href="index.php" class="logo-link">
                    <h1>📚 Biblioteka</h1>
                </a>
            </div>
            <nav>
                <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
                <a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">Książki</a>
                <a href="loans.php" class="<?= ($current_page == 'loans.php') ? 'active' : '' ?>">Wypożyczenia</a>
                <a href="shelves.php" class="<?= ($current_page == 'shelves.php') ? 'active' : '' ?>">Półki</a>
                <a href="clients.php" class="<?= ($current_page == 'clients.php') ? 'active' : '' ?>">Klienci</a>
            </nav>
        </header>
        <main>