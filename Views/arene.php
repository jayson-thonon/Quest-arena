<?php
session_start();
date_default_timezone_set('Pacific/Noumea');

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
$monPersoData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$monPersoData) {
    echo "<p>❌ Aucun personnage trouvé pour ce joueur.</p>";
    exit;
}

$monPerso = new Personnage(
    $monPersoData['joueur_id'],
    $monPersoData['nom'],
    $monPersoData['niveau'],
    $monPersoData['points_vie'],
    $monPersoData['attaque'],
    $monPersoData['defense'],
    $monPersoData['experience'],
    $monPersoData['victoires'],
    $monPersoData['id']
);

$stmt = $pdo->prepare("SELECT * FROM personnage WHERE joueur_id != ?");
$stmt->execute([$_SESSION['joueur_id']]);
$adversaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

$combatResult = "";
$messageErreur = "";
$cooldownMinutes = 10;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adversaire_id'])) {
    $adversaireId = (int) $_POST['adversaire_id'];

    $stmtCheck = $pdo->prepare("
        SELECT date_combat
        FROM combat
        WHERE (joueur1_id = :j1 AND joueur2_id = :j2)
           OR (joueur1_id = :j2 AND joueur2_id = :j1)
        ORDER BY date_combat DESC LIMIT 1
    ");
    $stmtCheck->execute([':j1' => $_SESSION['joueur_id'], ':j2' => $adversaireId]);
    $lastFight = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($lastFight) {
        $dateCombat = new DateTime($lastFight['date_combat'], new DateTimeZone('UTC'));
        $dateCombat->setTimezone(new DateTimeZone(date_default_timezone_get()));
        $timestampCombat = $dateCombat->getTimestamp();
        $tempsRestant = ($timestampCombat + ($cooldownMinutes * 60)) - time();
        if ($tempsRestant > 0) {
            $minutes = floor($tempsRestant / 60);
            $messageErreur = "⚠️ Tu dois attendre encore {$minutes} minute(s) avant de défier cet adversaire à nouveau.";
        }
    }

    if (!$messageErreur) {
        $stmt = $pdo->prepare("SELECT * FROM personnage WHERE id = ?");
        $stmt->execute([$adversaireId]);
        $advData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$advData) {
            echo "<p>❌ Adversaire introuvable.</p>";
            exit;
        }

        $adversaire = new Personnage(
            $advData['joueur_id'],
            $advData['nom'],
            $advData['niveau'],
            $advData['points_vie'],
            $advData['attaque'],
            $advData['defense'],
            $advData['experience'],
            $advData['victoires'],
            $advData['id']
        );

        ob_start();
        $vainqueurNom = $monPerso->combattre($adversaire);
        $combatResult = ob_get_clean();

        $vainqueurNom = trim($vainqueurNom);
        $gagnantId = (strtolower($vainqueurNom) === strtolower($monPerso->getNom()))
            ? $_SESSION['joueur_id']
            : $adversaire->getJoueurId();

        if ($gagnantId === $_SESSION['joueur_id']) {
            $monPerso->gagnerVictoire();
        }

        $stmt = $pdo->prepare("INSERT INTO combat (joueur1_id, joueur2_id, gagnant_id) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['joueur_id'], $adversaire->getJoueurId(), $gagnantId]);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Arène - QuestArena ⚔️</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
<style>
/* ===== GLOBAL ===== */
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background:radial-gradient(circle at top left,#0a1f3a,#001a1a);color:#fff;min-height:100vh;overflow-x:hidden;}
canvas#particles{position:fixed;top:0;left:0;width:100%;height:100%;z-index:0;}
header{
  position:sticky;top:0;z-index:5;
  display:flex;justify-content:center;gap:2rem;
  background:rgba(0,0,0,0.5);
  backdrop-filter:blur(8px);
  padding:1rem;
}
header a{text-decoration:none;color:#ffd700;font-weight:600;transition:.3s;}
header a:hover{color:#00e0ff;text-shadow:0 0 10px #00e0ff;}

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

h1{color:#00e0ff;font-size:2rem;text-shadow:0 0 15px #00e0ff;}
h3{color:#ffd700;margin:10px 0 20px;}

/* ===== SELECT ADVERSAIRE ===== */
form select,form button{
  padding:10px;font-size:16px;border-radius:8px;border:none;margin:10px;
}
form select{background:rgba(255,255,255,0.1);color:white;width:60%;}
form button{
  background:linear-gradient(90deg,#007aff,#00e0ff);
  color:white;font-weight:600;
  cursor:pointer;transition:.3s;
}
form button:hover{transform:scale(1.05);box-shadow:0 0 15px #00e0ff;}

/* ===== ERREUR ===== */
.error{
  background:rgba(248,113,113,0.15);
  color:#ff6b6b;
  border:1px solid #ff6b6b;
  border-radius:8px;
  padding:10px;
  width:70%;
  margin:10px auto;
}

/* ===== COMBAT LOG ===== */
.combat-log{
  background:rgba(255,255,255,0.07);
  width:90%;margin:25px auto;
  padding:20px;border-radius:10px;
  box-shadow:0 0 15px rgba(0,224,255,0.3);
  text-align:left;
  max-height:350px;
  overflow-y:auto;
  color:#ddd;
  font-size:0.95rem;
  line-height:1.5;
  border-left:3px solid #00e0ff;
}

/* ===== ENERGY CIRCLE ===== */
.energy-ring{
  position:absolute;
  top:-80px;left:50%;
  transform:translateX(-50%);
  width:160px;height:160px;
  border-radius:50%;
  border:2px solid rgba(0,224,255,0.3);
  box-shadow:0 0 40px #00e0ff;
  animation:spin 6s linear infinite,pulse 2s ease-in-out infinite alternate;
  opacity:.3;
}
@keyframes spin{from{transform:translateX(-50%) rotate(0);}to{transform:translateX(-50%) rotate(360deg);}}
@keyframes pulse{from{box-shadow:0 0 15px #00e0ff;}to{box-shadow:0 0 40px #00e0ff;}}

/* ===== FOOTER ===== */
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
  <a href="deconnexion.php">🚪 Déconnexion</a>
</header>

<div class="container">
  <div class="energy-ring"></div>
  <h1>🏟️ Arène</h1>
  <h3>Bienvenue, <span style="color:#00e0ff;"><?= htmlspecialchars($monPerso->getNom()); ?></span> ! Prépare-toi au combat.</h3>

  <?php if ($messageErreur): ?>
    <div class="error"><?= $messageErreur; ?></div>
  <?php endif; ?>

  <form method="POST">
    <label><strong>Choisis ton adversaire :</strong></label><br>
    <select name="adversaire_id" required>
      <option value="">-- Sélectionne un adversaire --</option>
      <?php
      $cooldownData = [];
      foreach ($adversaires as $adv):
          $stmt = $pdo->prepare("
              SELECT date_combat FROM combat
              WHERE (joueur1_id = :j1 AND joueur2_id = :j2)
                 OR (joueur1_id = :j2 AND joueur2_id = :j1)
              ORDER BY date_combat DESC LIMIT 1
          ");
          $stmt->execute([':j1' => $_SESSION['joueur_id'], ':j2' => $adv['joueur_id']]);
          $last = $stmt->fetch(PDO::FETCH_ASSOC);

          $disabled = false;
          $remainingText = "";
          if ($last) {
              $dateCombat = new DateTime($last['date_combat'], new DateTimeZone('UTC'));
              $dateCombat->setTimezone(new DateTimeZone(date_default_timezone_get()));
              $timestampCombat = $dateCombat->getTimestamp();
              $remainingSeconds = max(0, ($timestampCombat + ($cooldownMinutes * 60)) - time());
              if ($remainingSeconds > 0) {
                  $disabled = true;
                  $remainingText = sprintf("⏳ (dans %02d:%02d)", floor($remainingSeconds / 60), $remainingSeconds % 60);
              }
          }
      ?>
      <option value="<?= $adv['id']; ?>" <?= $disabled ? 'disabled' : ''; ?>>
        <?= htmlspecialchars($adv['nom']); ?> — Niveau <?= $adv['niveau']; ?> <?= $remainingText; ?>
      </option>
      <?php endforeach; ?>
    </select>
    <button type="submit">Lancer le combat ⚔️</button>
  </form>

  <div class="combat-log">
    <?php if ($combatResult): ?>
      <?= $combatResult; ?>
    <?php else: ?>
      <p>🧙 Entre dans l’arène et affronte un adversaire valeureux !</p>
    <?php endif; ?>
  </div>

  <div class="footer">© <?= date('Y'); ?> QuestArena — Que la gloire t'accompagne ⚡</div>
</div>

<script>
// ===== PARTICULES =====
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
</script>

</body>
</html>
