<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votre compte client</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 8px;
        }
        .btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bienvenue, {{ $first_name }} {{ $last_name }} !</h2>

        <p>Votre compte client a été créé avec succès.</p>

        <p>Vos identifiants de connexion :</p>
        <ul>
            <li><strong>Email :</strong> {{ $email }}</li>
            <li><strong>Mot de passe temporaire :</strong> {{ $password }}</li>
        </ul>

        <p>Pour des raisons de sécurité, nous vous recommandons de modifier votre mot de passe lors de votre première connexion.</p>

        <a href="{{ url('/login') }}" class="btn">Se connecter</a>

        <p>Si vous n'avez pas fait cette demande, veuillez nous contacter.</p>

        <p>Cordialement,<br>L'équipe</p>
    </div>
</body>
</html>