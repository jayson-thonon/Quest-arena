<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>QuestArena ⚔️ - Accueil</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
<style>
/* ---------- GLOBAL ---------- */
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
canvas#particles {
  position: absolute;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  z-index: 0;
}

/* ---------- CONTAINER ---------- */
.container {
  position: relative;
  z-index: 2;
  text-align: center;
  background: rgba(255,255,255,0.1);
  backdrop-filter: blur(10px);
  padding: 3rem 2.5rem;
  border-radius: 20px;
  box-shadow: 0 0 25px rgba(0,0,0,0.3);
  animation: fadeIn 1s ease;
  width: 90%;
  max-width: 500px;
}
@keyframes fadeIn {
  from {opacity:0; transform:scale(0.9);}
  to {opacity:1; transform:scale(1);}
}

h1 {
  font-size: 2rem;
  color: #00e0ff;
  margin-bottom: 1rem;
  animation: glow 2s infinite alternate;
}
@keyframes glow {
  from { text-shadow: 0 0 5px #00e0ff, 0 0 10px #007aff; }
  to { text-shadow: 0 0 20px #00e0ff, 0 0 40px #007aff; }
}

p {
  font-size: 1rem;
  margin-bottom: 2rem;
  opacity: 0.9;
}

.buttons {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
.buttons a {
  text-decoration: none;
  color: white;
  background: linear-gradient(90deg, #007aff, #00e0ff);
  padding: 0.8rem;
  border-radius: 12px;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 0 10px rgba(0,0,0,0.3);
}
.buttons a:hover {
  transform: scale(1.05);
  box-shadow: 0 0 15px #00e0ff;
}

/* Bouton secondaire */
a.secondary {
  background: rgba(255,255,255,0.15);
  border: 1px solid #00e0ff;
}
a.secondary:hover {
  background: rgba(0,224,255,0.2);
}
.footer {
  margin-top: 2rem;
  font-size: 0.9rem;
  opacity: 0.7;
}
</style>
</head>
<body>

<canvas id="particles"></canvas>

<div class="container">
  <h1>Bienvenue dans QuestArena ⚔️</h1>
  <p>Entre dans l’arène des héros, forge ton destin et combats pour la gloire !</p>

  <div class="buttons">
    <a href="Views/connexion.php">Se connecter</a>
    <a href="Views/inscription.php">Créer un compte</a>
    <a href="#" class="secondary">Découvrir l’univers</a>
  </div>

  <div class="footer">
    © <?= date('Y'); ?> QuestArena — développé avec ⚔️ et passion
  </div>
</div>

<script>
// ---------- ANIMATION PARTICULES ----------
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

for (let i = 0; i < 100; i++) particles.push(new Particle());

function animate() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  particles.forEach(p => { p.update(); p.draw(); });
  requestAnimationFrame(animate);
}
animate();
</script>

</body>
</html>
