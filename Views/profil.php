<?php
session_start();
require_once '../Classes/Database.php';

if (!isset($_SESSION['joueur_id'])) {
    header("Location: connexion.php");
    exit;
}

$db = new Database();
$pdo = $db->getConnection();
$joueurId = $_SESSION['joueur_id'];

// === Récupérer les infos du personnage ===
$stmt = $pdo->prepare("SELECT * FROM personnage WHERE joueur_id = ?");
$stmt->execute([$joueurId]);
$personnage = $stmt->fetch(PDO::FETCH_ASSOC);

// === Récupérer les 5 dernières quêtes terminées ===
$stmtQ = $pdo->prepare("
    SELECT q.titre, q.recompense_xp, qj.date_realisation
    FROM quete_joueur qj
    JOIN quete q ON q.id = qj.quete_id
    WHERE qj.joueur_id = ?
    ORDER BY qj.date_realisation DESC
    LIMIT 5
");
$stmtQ->execute([$joueurId]);
$quetes = $stmtQ->fetchAll(PDO::FETCH_ASSOC);

function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Profil - QuestArena ⚔️</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
<style>
/* === BASE === */
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background:radial-gradient(circle at top left,#0a1f3a,#001a1a);color:#fff;overflow-x:hidden;min-height:100vh;}
canvas#particles{position:fixed;top:0;left:0;width:100%;height:100%;z-index:0;}
.container{position:relative;z-index:2;max-width:900px;margin:3rem auto;padding:2rem;background:rgba(255,255,255,0.08);backdrop-filter:blur(12px);border-radius:20px;box-shadow:0 0 25px rgba(0,0,0,0.4);}
h1{text-align:center;font-size:2rem;margin-bottom:1.5rem;color:#00e0ff;text-shadow:0 0 15px rgba(0,224,255,0.4);}
.card{text-align:center;}
.card h2{color:#ffd700;margin-bottom:1rem;}

/* === XP BAR === */
.xp-bar{background:#222;border-radius:10px;overflow:hidden;width:80%;margin:10px auto;height:15px;position:relative;}
.xp-fill{background:linear-gradient(90deg,#007aff,#00e0ff);height:100%;width:0%;transition:width 1s ease-in-out;}
.xp-text{text-align:center;margin-top:0.5rem;color:#bbb;}
.level-up-glow{animation:glowPulse 1.5s ease-in-out infinite alternate;}
@keyframes glowPulse{from{box-shadow:0 0 10px #00e0ff;}to{box-shadow:0 0 30px #00e0ff;}}
.level-up-popup{
  position:absolute;top:-50px;left:50%;transform:translateX(-50%);
  color:#00e0ff;font-size:1.3rem;font-weight:700;
  text-shadow:0 0 15px #00e0ff,0 0 30px #007aff;
  opacity:0;animation:popupFade 2s forwards;
}
@keyframes popupFade{
  0%{opacity:0;transform:translate(-50%,0);}
  20%{opacity:1;transform:translate(-50%,-10px);}
  80%{opacity:1;}
  100%{opacity:0;transform:translate(-50%,-40px);}
}

/* === STATS === */
.stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-top:20px;}
.stat{background:rgba(255,255,255,0.1);border-radius:12px;padding:12px;text-align:center;box-shadow:0 0 10px rgba(0,0,0,0.3);transition:0.3s;}
.stat:hover{transform:translateY(-5px);box-shadow:0 0 15px #00e0ff;}

/* === TABLE === */
.recent{margin-top:40px;}
.recent h2{color:#00e0ff;margin-bottom:10px;text-shadow:0 0 8px rgba(0,224,255,0.3);}
table{width:100%;border-collapse:collapse;background:rgba(255,255,255,0.05);border-radius:10px;overflow:hidden;}
th,td{padding:0.7rem;text-align:left;}
th{color:#ffd700;border-bottom:2px solid #00e0ff;}
tr:hover{background:rgba(0,224,255,0.1);}

/* === BOUTONS === */
.btn{display:inline-block;text-decoration:none;color:white;background:linear-gradient(90deg,#007aff,#00e0ff);padding:0.8rem 1.5rem;border-radius:12px;font-weight:600;margin:0.5rem;transition:all 0.3s ease;}
.btn:hover{transform:scale(1.05);box-shadow:0 0 15px #00e0ff;}
.footer{text-align:center;margin-top:2rem;color:#999;font-size:0.9rem;}
header{display:flex;justify-content:center;gap:2rem;padding:1rem;background:rgba(0,0,0,0.5);backdrop-filter:blur(8px);}
header a{text-decoration:none;color:#ffd700;font-weight:600;transition:0.3s;}
header a:hover{color:#00e0ff;text-shadow:0 0 10px #00e0ff;}
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
  <h1>Bienvenue, <?= h($_SESSION['pseudo']); ?> 🧙</h1>

  <?php if ($personnage): ?>
  <div class="card">
    <h2><?= h($personnage['nom']); ?> — Niveau <?= (int)$personnage['niveau']; ?></h2>
    <?php
      $xpActuelle = (int)$personnage['experience'];
      $xpMax = ($personnage['niveau'] < 5) ? ($personnage['niveau'] * 200) : 2000;
      $progress = min(100, ($xpActuelle / $xpMax) * 100);
      $niveauUp = $xpActuelle >= $xpMax;
    ?>
    <div class="xp-bar <?= $niveauUp ? 'level-up-glow' : '' ?>">
      <div class="xp-fill" style="width: <?= $progress ?>%;"></div>
      <?php if ($niveauUp): ?>
        <div class="level-up-popup">✨ Niveau supérieur ! ✨</div>
      <?php endif; ?>
    </div>
    <p class="xp-text"><?= $xpActuelle ?> / <?= $xpMax ?> XP</p>

    <div class="stat-grid">
      <div class="stat">💖 <strong>PV 80 :</strong><br><?= (int)$personnage['points_vie']; ?></div>
      <div class="stat">⚔️ <strong>Attaque :</strong><br><?= (int)$personnage['attaque']; ?></div>
      <div class="stat">🛡️ <strong>Défense :</strong><br><?= (int)$personnage['defense']; ?></div>
      <div class="stat">🏅 <strong>Victoires :</strong><br><?= (int)$personnage['victoires']; ?></div>
    </div>
  </div>
  <?php else: ?>
    <p>❌ Aucun personnage trouvé pour ce joueur.</p>
  <?php endif; ?>

  <div class="recent">
    <h2>📜 Dernières quêtes réalisées</h2>
    <?php if ($quetes): ?>
      <table>
        <tr><th>Titre</th><th>XP Gagné</th><th>Date</th></tr>
        <?php foreach ($quetes as $q): ?>
          <tr>
            <td><?= h($q['titre']); ?></td>
            <td><?= (int)$q['recompense_xp']; ?> XP</td>
            <td><?= h($q['date_realisation']); ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php else: ?>
      <p>Aucune quête complétée pour le moment… Pars à l’aventure ! ⚔️</p>
    <?php endif; ?>
  </div>

  <div style="text-align:center;margin-top:25px;">
    <a href="arene.php" class="btn">Aller à l'Arène ⚔️</a>
    <a href="quetes.php" class="btn">Faire des Quêtes 📜</a>
  </div>

  <div class="footer">© <?= date('Y'); ?> QuestArena — Forgez votre légende ⚔️</div>
</div>

<script>
// === FOND DE PARTICULES ===
const canvas=document.getElementById("particles");
const ctx=canvas.getContext("2d");
let particles=[];
function resize(){canvas.width=innerWidth;canvas.height=innerHeight;}
window.addEventListener("resize",resize);resize();
class Particle{constructor(){this.x=Math.random()*canvas.width;this.y=Math.random()*canvas.height;this.size=Math.random()*3;this.speedX=(Math.random()-.5);this.speedY=(Math.random()-.5);}
update(){this.x+=this.speedX;this.y+=this.speedY;if(this.x<0||this.x>canvas.width)this.speedX*=-1;if(this.y<0||this.y>canvas.height)this.speedY*=-1;}
draw(){ctx.beginPath();ctx.arc(this.x,this.y,this.size,0,Math.PI*2);ctx.fillStyle="#00e0ff";ctx.fill();}}
for(let i=0;i<100;i++)particles.push(new Particle());
function animate(){ctx.clearRect(0,0,canvas.width,canvas.height);particles.forEach(p=>{p.update();p.draw();});requestAnimationFrame(animate);}
animate();

// === ANIMATION XP ===
document.addEventListener('DOMContentLoaded',()=>{
  document.querySelectorAll('.xp-fill').forEach(bar=>{
    const width=bar.style.width;bar.style.width='0%';
    setTimeout(()=>bar.style.width=width,300);
  });
  // effet sonore (optionnel)
  const lvlUp=document.querySelector('.level-up-popup');
  if(lvlUp){
    const audio=new Audio('https://cdn.pixabay.com/download/audio/2022/03/15/audio_d4f16e2d1f.mp3?filename=level-up-191997.mp3');
    audio.volume=0.3;audio.play();
  }
});
</script>
</body>
</html>
