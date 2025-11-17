<?php
require_once './Classes/Database.php';

$db = new Database();
$conn = $db->getConnection();

if ($conn) {
    echo "<h2 style='color:green;'>✅ Connexion réussie à la base QuestArena !</h2>";
} else {
    echo "<h2 style='color:red;'>❌ Échec de la connexion.</h2>";
}
?>
