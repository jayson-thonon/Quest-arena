<?php
session_start();
require_once '../Classes/Joueur.php';

// ------------------- LOGIQUE D'INSCRIPTION -------------------
$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = trim($_POST['pseudo']);
    $email = trim($_POST['email']);
    $motDePasse = trim($_POST['mot_de_passe']);

    $joueur = new Joueur($pseudo, $email, $motDePasse);

    if ($joueur->inscrire()) {
        // ✅ Redirection propre (pas d’output avant)
        header("Location: connexion.php?inscription=ok");
        exit;
    } else {
        $erreur = "❌ Erreur lors de l’inscription (email déjà utilisé ou autre problème).";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inscription - QuestArena ⚔️</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
<style>
/* ------------------- GLOBAL ------------------- */
* {
  margin: 0; padding: 0;
  box-sizing: border-box;
  font-family: 'Poppins', sans-serif;
}
body {
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: radial-gradient(circle at top left, #0a1f3a, #001a1a);
  overflow: hidden;
  color: #fff;
}

/* ------------------- PARTICULES ------------------- */
canvas#particles {
  position: absolute;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  z-index: 0;
}

/* ------------------- FORMULAIRE ------------------- */
.register-card {
  position: relative;
  z-index: 2;
  background: rgba(255,255,255,0.1);
  backdrop-filter: blur(10px);
  padding: 2.5rem;
  border-radius: 20px;
  box-shadow: 0 0 25px rgba(0,0,0,0.3);
  width: 360px;
  text-align: center;
  animation: fadeIn 1s ease;
}
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.9); }
  to { opacity: 1; transform: scale(1); }
}

h1 {
  font-size: 1.8rem;
  color: #00e0ff;
  margin-bottom: 1.5rem;
  cursor: pointer;
  transition: transform .3s ease;
}
h1:hover { transform: scale(1.1) rotate(-3deg); }
h1::after {
  content: "🛡️";
  margin-left: 0.4rem;
  display: inline-block;
  transition: transform .4s ease;
}
h1:hover::after { transform: rotate(45deg); }

input {
  width: 100%;
  padding: 0.6rem 0.8rem;
  margin-bottom: 1rem;
  border: none;
  border-radius: 10px;
  background: rgba(255,255,255,0.15);
  color: #fff;
  outline: none;
  transition: 0.3s;
}
input:focus {
  background: rgba(255,255,255,0.25);
  box-shadow: 0 0 8px #00e0ff;
}

button {
  width: 100%;
  background: linear-gradient(90deg, #007aff, #00e0ff);
  border: none;
  border-radius: 10px;
  padding: 0.7rem;
  color: #fff;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}
button:hover {
  transform: scale(1.05);
  box-shadow: 0 0 15px #00e0ff;
}

.message {
  margin-top: 1rem;
  font-size: 0.95rem;
}
.message a {
  color: #00e0ff;
  text-decoration: none;
}
.message a:hover {
  text-decoration: underline;
}

.erreur {
  color: #ff6b6b;
  margin-bottom: 1rem;
}
.succes {
  color: #00ff88;
  margin-bottom: 1rem;
}
</style>
</head>
<body>

<canvas id="particles"></canvas>

<div class="register-card">
  <h1>Créer un compte QuestArena</h1>

  <?php if (!empty($erreur)): ?>
      <p class="erreur"><?= $erreur ?></p>
  <?php endif; ?>

  <form method="post">
    <input type="text" name="pseudo" placeholder="Pseudo" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
    <button type="submit">S'inscrire</button>
  </form>

  <div class="message">
    🔹 Déjà un compte ? <a href="connexion.php">Connecte-toi ici</a><br>
    <a href="../index.php">⬅ Retour à l’accueil</a>
  </div>
</div>

<script>
// ------------------- ANIMATION PARTICULES -------------------
const canvas = document.getElementById("particles");
const ctx = canvas.getContext("2d");
let particles = [];

function resize() {
  canvas.width = innerWidth;
  canvas.height = innerHeight;
}
window.addEventListener("resize", resize);
resize();

class Particle {
  constructor() {
    this.x = Math.random() * canvas.width;
    this.y = Math.random() * canvas.height;
    this.size = Math.random() * 3;
    this.speedX = (Math.random() - 0.5);
    this.speedY = (Math.random() - 0.5);
  }
  update() {
    this.x += this.speedX;
    this.y += this.speedY;
    if (this.x < 0 || this.x > canvas.width) this.speedX *= -1;
    if (this.y < 0 || this.y > canvas.height) this.speedY *= -1;
  }
  draw() {
    ctx.beginPath();
    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
    ctx.fillStyle = "#00e0ff";
    ctx.fill();
  }
}

for (let i = 0; i < 100; i++) {
  particles.push(new Particle());
}

function animate() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  particles.forEach(p => { p.update(); p.draw(); });
  requestAnimationFrame(animate);
}
animate();
</script>

</body>
</html>
