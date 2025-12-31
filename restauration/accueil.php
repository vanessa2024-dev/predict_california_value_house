<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Connexion - Les Délices du Saphirs</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--fond-principal), var(--violet-degrade));
            color: var(--texte);
            background-attachment: fixed;
            
        }

        .login-box {
            width: 340px;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            font-size: 22px;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            font-weight: bold;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            margin-bottom: 15px;
            border: 1px solid #bbb;
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #4A148C; /* violet nuit soft */
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 15px;
            cursor: pointer;
        }

        button:hover {
            background: #6A1B9A;
        }

        .footer-text {
            text-align: center;
            margin-top: 15px;
            font-size: 13px;
            color: #777;
        }
    </style>
</head>

<body>

    <div class="login-box">
        <h2>Connexion</h2>

        <form action="login.php" method="POST" onsubmit="return mfValidation();"> 
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" placeholder="Entrez votre nom" required>

            <label for="email">email</label>
            <input type="text" id="email" name="email" placeholder="Entrez votre addresse email" required>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>

            <button type="submit">Se connecter</button>
        </form>

        <div class="footer-text">
            Les Délices du Saphirs © 2025
        </div>
    </div>
<script src="codeacc.js"></script>
</body>
</html>