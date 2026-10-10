<?php
declare(strict_types=1);


$pageTitle = 'H-Project';

?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle) ?></title>

    <link rel="stylesheet" href="/css/index.css">
</head>

<body>
    <header>
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
    </header>

    <main>
        <section id="chat">
            <div id="messages">
            </div>

            <form id="prompt-form">
                <textarea
                    id="prompt"
                    name="prompt"
                    placeholder="Enter a message..."
                    rows="4"
                ></textarea>

                <button type="submit">Send</button>
            </form>
        </section>
    </main>

    <script src="/js/index.js"></script>
</body>
</html>
