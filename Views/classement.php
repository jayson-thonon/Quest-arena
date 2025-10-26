<?php
session_start();
require_once '../Classes/Database.php';

if (!isset($_SESSION['joueur_id'])) {
    header("Location: connexion.php");
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

$stmt = $pdo->query("
    SELECT j.pseudo, p.niveau, p.experience, p.victoires
    FROM personnage p
    JOIN joueur j ON j.id = p.joueur_id
    ORDER BY p.niveau DESC, p.victoires DESC, p.experience DESC
    LIMIT 20
");
$classement = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>🏆 Classement - QuestArena</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
<style>
/* ===== GLOBAL ===== */
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{
  background:radial-gradient(circle at top left,#0a1f3a,#001a1a);
  color:#fff;
  overflow-x:hidden;
  min-height:100vh;
}
canvas#particles{position:fixed;top:0;left:0;width:100%;height:100%;z-index:0;}

/* ===== NAVIGATION ===== */
header{
  position:sticky;top:0;z-index:5;
  display:flex;justify-content:center;gap:2rem;
  background:rgba(0,0,0,0.5);backdrop-filter:blur(8px);
  padding:1rem;
}
header a{
  text-decoration:none;color:#ffd700;font-weight:600;transition:.3s;
}
header a:hover{
  color:#00e0ff;text-shadow:0 0 10px #00e0ff;
}

/* ===== CONTAINER ===== */
.container{
  position:relative;z-index:2;
  max-width:950px;margin:3rem auto;padding:2rem;
  background:rgba(255,255,255,0.08);
  backdrop-filter:blur(12px);
  border-radius:20px;
  box-shadow:0 0 25px rgba(0,0,0,0.4);
  animation:fadeIn 1s ease;
  text-align:center;
}
@keyframes fadeIn{from{opacity:0;transform:translateY(30px);}to{opacity:1;transform:translateY(0);}}
h1{
  color:#00e0ff;
  text-shadow:0 0 15px #00e0ff;
  margin-bottom:1.5rem;
  font-size:2rem;
}

/* ===== TABLE ===== */
.table-container{
  overflow-x:auto;
  border-radius:12px;
  box-shadow:0 0 20px rgba(0,224,255,0.2);
}
table{
  width:100%;
  border-collapse:collapse;
  background:rgba(255,255,255,0.05);
  border-radius:12px;
}
th,td{
  padding:12px 15px;
  text-align:center;
  border-bottom:1px solid rgba(255,255,255,0.1);
}
th{
  background:rgba(0,224,255,0.15);
  color:#ffd700;
  border-bottom:2px solid #00e0ff;
}
tr:nth-child(even){background:rgba(255,255,255,0.05);}
tr:hover{
  background:rgba(0,224,255,0.15);
  transition:.3s;
  transform:scale(1.01);
}

/* ===== PODIUM COLORS ===== */
.rank{
  font-weight:700;
  text-shadow:0 0 10px #facc15;
  animation:glowRank 2s ease-in-out infinite alternate;
}
.gold{color:#fbbf24;}
.silver{color:#e5e7eb;}
.bronze{color:#f97316;}
@keyframes glowRank{
  from{text-shadow:0 0 10px #facc15;}
  to{text-shadow:0 0 25px #ffe169;}
}

/* ===== FOOTER ===== */
.footer{text-align:center;margin-top:2rem;color:#999;font-size:.9rem;}
.btn{
  display:inline-block;margin-top:1.5rem;text-decoration:none;
  background:linear-gradient(90deg,#007aff,#00e0ff);
  color:white;font-weight:600;padding:.8rem 1.5rem;
  border-radius:12px;transition:all .3s ease;
}
.btn:hover{transform:scale(1.05);box-shadow:0 0 15px #00e0ff;}

/* ===== RESPONSIVE ===== */
@media(max-width:600px){
  th,td{font-size:0.9rem;padding:8px;}
  h1{font-size:1.5rem;}
}
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
  <a href="deconnexion.php">🚪 Déconnexion</a>
</header>

<div class="container">
  <h1>🏆 Classement des héros de QuestArena</h1>

  <?php if (empty($classement)): ?>
    <p>Aucun héros n’a encore prouvé sa valeur...</p>
  <?php else: ?>
    <div class="table-container">
      <table>
        <tr>
          <th>Rang</th>
          <th>Joueur</th>
          <th>Niveau</th>
          <th>XP</th>
          <th>Victoires</th>
        </tr>
        <?php $rang=1; foreach($classement as $c):
          $class='';
          if($rang==1)$class='gold';
          elseif($rang==2)$class='silver';
          elseif($rang==3)$class='bronze';
        ?>
        <tr>
          <td class="rank <?= $class ?>">#<?= $rang ?></td>
          <td><?= htmlspecialchars($c['pseudo']); ?></td>
          <td><?= $c['niveau']; ?></td>
          <td><?= $c['experience']; ?></td>
          <td><?= $c['victoires']; ?></td>
        </tr>
        <?php $rang++; endforeach; ?>
      </table>
    </div>
  <?php endif; ?>

  <a href="profil.php" class="btn">⬅ Retour au profil</a>
  <div class="footer">© <?= date('Y'); ?> QuestArena — Gloire aux champions ⚡</div>
</div>

<script>
// ===== PARTICULES =====
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

// ===== SON ÉPIQUE À L’OUVERTURE =====
window.addEventListener("load",()=>{
  const sound=new Audio("https://cdn.pixabay.com/download/audio/2021/09/30/audio_fad1a1ab7b.mp3?filename=game-award-6106.mp3");
  sound.volume=0.2;
  sound.play();
});
</script>
</body>
</html>
