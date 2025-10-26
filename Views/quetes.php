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
$joueurId = $_SESSION['joueur_id'];

// Récupérer le personnage du joueur
$stmt = $pdo->prepare("SELECT * FROM personnage WHERE joueur_id = ?");
$stmt->execute([$joueurId]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);
$personnage = new Personnage(
    $p['joueur_id'],
    $p['nom'],
    $p['niveau'],
    $p['points_vie'],
    $p['attaque'],
    $p['defense'],
    $p['experience'],
    $p['victoires'],
    $p['id']
);

$message = "";

// === Étape 1 : lister les niveaux disponibles ===
$stmt = $pdo->query("SELECT DISTINCT niveau FROM quete ORDER BY niveau ASC");
$niveaux = $stmt->fetchAll(PDO::FETCH_COLUMN);

// === Étape 2 : afficher les questions du niveau choisi ===
if (isset($_POST['niveau']) && !isset($_POST['reponse'])) {
    $niveauChoisi = (int) $_POST['niveau'];
    $stmt = $pdo->prepare("SELECT * FROM quete WHERE niveau = ? ORDER BY id ASC");
    $stmt->execute([$niveauChoisi]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// === Étape 3 : validation des réponses ===
if (isset($_POST['reponse'])) {
    $niveau = (int) $_POST['niveau'];
    $bonnes = 0;
    $totalXP = 0;

    foreach ($_POST['reponse'] as $idQuete => $rep) {
        $stmt = $pdo->prepare("SELECT bonne_reponse, recompense_xp FROM quete WHERE id = ?");
        $stmt->execute([$idQuete]);
        $q = $stmt->fetch(PDO::FETCH_ASSOC);

        if (strcasecmp(trim($q['bonne_reponse']), trim($rep)) === 0) {
            $bonnes++;
            $totalXP += (int)$q['recompense_xp'];
        }
    }

    $check = $pdo->prepare("
        SELECT 1 FROM quete_joueur qj
        JOIN quete q ON q.id = qj.quete_id
        WHERE qj.joueur_id = ? AND q.niveau = ?
    ");
    $check->execute([$joueurId, $niveau]);
    $dejaFait = $check->fetchColumn();

    if (!$dejaFait && $bonnes >= 3) {
        $personnage->gagnerExperience($totalXP);
        $stmt = $pdo->prepare("
            INSERT INTO quete_joueur (joueur_id, quete_id)
            SELECT ?, id FROM quete WHERE niveau = ?
        ");
        $stmt->execute([$joueurId, $niveau]);
        $message = "✅ Quête de niveau $niveau réussie ! Tu gagnes $totalXP XP 🎉";
    } elseif ($dejaFait) {
        $message = "⚠️ Tu as déjà terminé cette quête.";
    } else {
        $message = "❌ Seulement $bonnes/4 bonnes réponses. Réessaie !";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>📜 Quêtes - QuestArena ⚔️</title>
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
  color:#00e0ff;
  text-shadow:0 0 10px #00e0ff;
}

/* ===== PARCHEMIN CONTAINER ===== */
.scroll-container{
  position:relative;
  z-index:2;
  width:90%;
  max-width:950px;
  margin:3rem auto;
  background:url('https://cdn.pixabay.com/photo/2016/11/22/19/14/parchment-1851162_1280.jpg') center/cover no-repeat;
  border-radius:12px;
  box-shadow:0 0 25px rgba(0,0,0,0.4);
  padding:2rem;
  animation:unroll 1.2s ease-out;
}
@keyframes unroll{
  0%{transform:scaleY(0.1);opacity:0;}
  60%{transform:scaleY(1.05);opacity:1;}
  100%{transform:scaleY(1);}
}

/* ===== LUEUR MAGIQUE ===== */
.scroll-container.glow {
  box-shadow:0 0 40px #00e0ff, 0 0 100px #00e0ff inset;
  transition:box-shadow 0.8s ease-in-out;
}

/* ===== TITRES ===== */
h1{text-align:center;color:#00e0ff;text-shadow:0 0 15px #00e0ff;margin-bottom:1.5rem;}
label,strong{color:#ffd700;}

/* ===== FORMULAIRE ===== */
form select,form button{
  padding:10px;font-size:16px;border-radius:8px;border:none;margin:10px;
}
form select{
  background:rgba(255,255,255,0.15);
  color:white;width:60%;
}
form button{
  background:linear-gradient(90deg,#007aff,#00e0ff);
  color:white;font-weight:600;
  cursor:pointer;transition:.3s;
}
form button:hover{
  transform:scale(1.05);
  box-shadow:0 0 15px #00e0ff;
}

/* ===== QUESTIONS ===== */
.question-block{
  text-align:left;
  margin:15px 0;
  background:rgba(0,0,0,0.35);
  padding:15px;
  border-radius:12px;
  border:1px solid rgba(255,255,255,0.2);
  box-shadow:inset 0 0 15px rgba(0,224,255,0.2);
  transition:.3s;
}
.question-block:hover{
  transform:translateY(-3px);
  box-shadow:0 0 20px #00e0ff;
}
.question-block strong{display:block;margin-bottom:5px;color:#ffd700;}
input[type="radio"]{margin-right:8px;}

/* ===== MESSAGES ===== */
.success{
  background:rgba(0,255,153,0.15);
  color:#00ff99;
  padding:12px;
  border:1px solid #00ff99;
  border-radius:8px;
  margin-bottom:15px;
  text-align:center;
  animation:fadeGlow 1s ease-in-out;
}
.error{
  background:rgba(255,99,99,0.15);
  color:#ff6b6b;
  padding:12px;
  border:1px solid #ff6b6b;
  border-radius:8px;
  margin-bottom:15px;
  text-align:center;
}
@keyframes fadeGlow{
  from{box-shadow:0 0 5px #00ff99;}
  to{box-shadow:0 0 20px #00ff99;}
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
  form select{width:90%;}
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

<div class="scroll-container">
  <h1>📜 Quêtes de QuestArena</h1>

  <?php if ($message): ?>
    <div class="<?= strpos($message,'✅')!==false ? 'success':'error' ?>"><?= $message ?></div>
  <?php endif; ?>

  <?php if (!isset($_POST['niveau']) && !isset($_POST['reponse'])): ?>
    <form method="post">
      <label for="niveau">Choisis ton niveau de quête :</label><br>
      <select name="niveau" id="niveau" required>
        <option value="">-- Sélectionner --</option>
        <?php foreach ($niveaux as $niv): ?>
          <option value="<?= $niv ?>">Niveau <?= $niv ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit">Commencer</button>
    </form>

  <?php elseif (isset($questions)): ?>
    <form method="post">
      <input type="hidden" name="niveau" value="<?= htmlspecialchars($niveauChoisi) ?>">
      <?php foreach ($questions as $q): ?>
        <div class="question-block">
          <strong><?= htmlspecialchars($q['titre']) ?></strong>
          <?= htmlspecialchars($q['question']) ?><br><br>
          <?php for ($i=1;$i<=4;$i++): $opt='option'.$i; ?>
            <label>
              <input type="radio" name="reponse[<?= $q['id'] ?>]" value="<?= htmlspecialchars($q[$opt]) ?>" required>
              <?= htmlspecialchars($q[$opt]) ?>
            </label><br>
          <?php endfor; ?>
        </div>
      <?php endforeach; ?>
      <button type="submit">Valider mes réponses ✅</button>
    </form>
  <?php endif; ?>

  <div style="text-align:center;">
    <a href="profil.php" class="btn">⬅ Retour au profil</a>
  </div>
</div>

<div class="footer">
  © <?= date('Y'); ?> QuestArena — Parchemin des héros ⚔️
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

// ===== AURA MAGIQUE À LA RÉUSSITE =====
if (document.querySelector('.success')) {
  const scroll = document.querySelector('.scroll-container');
  scroll.classList.add('glow');

  const xpSound = new Audio("https://cdn.pixabay.com/download/audio/2021/09/06/audio_45b86f2b39.mp3?filename=magic-sound-6081.mp3");
  xpSound.volume = 0.4;
  xpSound.play();

  setTimeout(() => scroll.classList.remove('glow'), 2500);
}
</script>
</body>
</html>
