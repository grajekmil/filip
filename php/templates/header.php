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
                <h1>📚 Biblioteka</h1>
            </div>
            <nav>
                <a href="index.php"
                    class="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Książki</a>
                <a href="shelves.php"
                    class="<?= (basename($_SERVER['PHP_SELF']) == 'shelves.php') ? 'active' : '' ?>">Półki</a>
                <a href="clients.php"
                    class="<?= (basename($_SERVER['PHP_SELF']) == 'clients.php') ? 'active' : '' ?>">Klienci</a>
            </nav>
        </header>
        <main>