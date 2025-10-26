<?php
session_start();
require_once '../Classes/Database.php';
require_once '../Classes/Personnage.php';

if (!isset($_SESSION['joueur_id'])) {
    header("Location: connexion.php");
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

$stmt = $pdo->prepare("SELECT * FROM personnage WHERE joueur_id = ?");
$stmt->execute([$_SESSION['joueur_id']]);
$persoData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$persoData) {
    echo "<p style='color:red;'>❌ Aucun personnage trouvé pour ce joueur.</p>";
    exit;
}

$monPerso = new Personnage(
    $persoData['joueur_id'],
    $persoData['nom'],
    $persoData['niveau'],
    $persoData['points_vie'],
    $persoData['attaque'],
    $persoData['defense']
);
function h($s){return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Mon Personnage - QuestArena ⚔️</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
<style>
/* ======= GLOBAL ======= */
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{
  background:radial-gradient(circle at top left,#0a1f3a,#001a1a);
  min-height:100vh;color:#fff;overflow-x:hidden;
}
canvas#particles{position:fixed;top:0;left:0;width:100%;height:100%;z-index:0;}

/* ======= MENU ======= */
header{
  position:sticky;top:0;z-index:5;
  display:flex;justify-content:center;gap:2rem;
  background:rgba(0,0,0,0.5);
  backdrop-filter:blur(8px);
  padding:1rem;
  border-bottom:1px solid rgba(255,255,255,0.1);
}
header a{
  text-decoration:none;color:#ffd700;font-weight:600;transition:.3s;
}
header a:hover{
  color:#00e0ff;text-shadow:0 0 10px #00e0ff;
}

/* ======= CONTAINER ======= */
.container{
  position:relative;z-index:2;
  max-width:900px;margin:3rem auto;padding:2rem;
  background:rgba(255,255,255,0.08);
  backdrop-filter:blur(12px);
  border-radius:20px;
  box-shadow:0 0 25px rgba(0,0,0,0.4);
  animation:fadeIn 1s ease;
  text-align:center;
}
@keyframes fadeIn{from{opacity:0;transform:translateY(30px);}to{opacity:1;transform:translateY(0);}}

/* ======= HERO SECTION ======= */
.hero-image{
  width:180px;height:180px;
  border-radius:50%;
  overflow:hidden;
  margin:0 auto 1rem;
  box-shadow:0 0 25px #00e0ff;
  animation:float 4s ease-in-out infinite;
}
@keyframes float{0%,100%{transform:translateY(0);}50%{transform:translateY(-10px);}}
.hero-image img{
  width:100%;height:100%;object-fit:cover;
  border-radius:50%;
}
h1{color:#00e0ff;margin-bottom:1rem;font-size:1.8rem;text-shadow:0 0 10px #00e0ff;}
h2{color:#ffd700;margin-bottom:1rem;}

/* ======= STATS ======= */
.stats-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
  gap:15px;
  margin-top:2rem;
}
.stat{
  background:rgba(255,255,255,0.1);
  border-radius:12px;
  padding:1rem;
  text-align:center;
  box-shadow:0 0 10px rgba(0,0,0,0.3);
  transition:.3s;
}
.stat:hover{
  transform:translateY(-5px);
  box-shadow:0 0 15px #00e0ff;
}
.stat strong{display:block;margin-bottom:.3rem;color:#ffd700;font-size:1.1rem;}

/* ======= ACTION BUTTONS ======= */
.actions{
  margin-top:2rem;
  display:flex;
  flex-wrap:wrap;
  justify-content:center;
  gap:1rem;
}
.btn{
  display:inline-block;
  text-decoration:none;
  background:linear-gradient(90deg,#007aff,#00e0ff);
  padding:.8rem 1.5rem;
  border-radius:12px;
  color:white;
  font-weight:600;
  transition:all .3s ease;
}
.btn:hover{
  transform:scale(1.05);
  box-shadow:0 0 15px #00e0ff;
}

/* ======= RESPONSIVE ======= */
@media(max-width:600px){
  .hero-image{width:130px;height:130px;}
  h1{font-size:1.5rem;}
}

/* ======= FOOTER ======= */
.footer{text-align:center;margin-top:2rem;color:#999;font-size:.9rem;}
</style>
</head>
<body>

<canvas id="particles"></canvas>

<header>
  <a href="profil.php">🧙 Profil</a>
  <a href="personnage.php">⚔️ Personnage</a>
  <a href="arene.php">🏟️ Arène</a>
  <a href="combat.php">🪶 Combats</a>
  <a href="quetes.php">📜 Quêtes</a>
  <a href="classement.php">🏆 Classement</a>
  <a href="../index.php">🚪 Déconnexion</a>
</header>

<div class="container">
  <div class="hero-image">
    <img src="https://cdn.pixabay.com/photo/2021/03/09/15/36/warrior-6081885_1280.png" alt="Personnage">
  </div>

  <h1>Mon Personnage</h1>
  <h2><?= h($monPerso->getNom()); ?> — Niveau <?= h($monPerso->getNiveau()); ?></h2>

  <div class="stats-grid">
    <div class="stat"><strong>💖 Points de Vie</strong><?= h($monPerso->getPointsVie()); ?></div>
    <div class="stat"><strong>⚔️ Attaque</strong><?= h($monPerso->getAttaque()); ?></div>
    <div class="stat"><strong>🛡️ Défense</strong><?= h($monPerso->getDefense()); ?></div>
    <div class="stat"><strong>🏅 Expérience</strong><?= h($persoData['experience']); ?> XP</div>
  </div>

  <div class="actions">
    <a href="profil.php" class="btn">⬅ Retour au profil</a>
    <a href="arene.php" class="btn">Aller à l'Arène ⚔️</a>
  </div>

  <div class="footer">© <?= date('Y'); ?> QuestArena — Forgez votre légende ⚔️</div>
</div>

<script>
// ===== FOND PARTICULES =====
const canvas=document.getElementById("particles");
const ctx=canvas.getContext("2d");
let particles=[];
function resize(){canvas.width=innerWidth;canvas.height=innerHeight;}
window.addEventListener("resize",resize);resize();
class Particle{
  constructor(){this.x=Math.random()*canvas.width;this.y=Math.random()*canvas.height;this.size=Math.random()*3;this.speedX=(Math.random()-.5);this.speedY=(Math.random()-.5);}
  update(){this.x+=this.speedX;this.y+=this.speedY;if(this.x<0||this.x>canvas.width)this.speedX*=-1;if(this.y<0||this.y>canvas.height)this.speedY*=-1;}
  draw(){ctx.beginPath();ctx.arc(this.x,this.y,this.size,0,Math.PI*2);ctx.fillStyle="#00e0ff";ctx.fill();}
}
for(let i=0;i<100;i++)particles.push(new Particle());
function animate(){ctx.clearRect(0,0,canvas.width,canvas.height);particles.forEach(p=>{p.update();p.draw();});requestAnimationFrame(animate);}
animate();
</script>
</body>
</html>
