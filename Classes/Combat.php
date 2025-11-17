<?php
// Classes/Combat.php (extrait utilitaire)
class CombatUtils
{
    public static function lastOpponentId(PDO $pdo, int $playerId): ?int
    {
        $sql = "SELECT id, joueur1_id, joueur2_id
                FROM combat
                WHERE joueur1_id = :id OR joueur2_id = :id
                ORDER BY date_combat DESC, id DESC
                LIMIT 1";
        $st = $pdo->prepare($sql);
        $st->execute([':id' => $playerId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        return ((int)$row['joueur1_id'] === $playerId)
            ? (int)$row['joueur2_id']
            : (int)$row['joueur1_id'];
    }
}
