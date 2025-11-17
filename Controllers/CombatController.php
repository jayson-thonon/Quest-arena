<?php
require_once __DIR__ . '/../Classes/Database.php';
require_once __DIR__ . '/../Classes/Combat.php'; // pour CombatUtils

class CombatController
{
    public function creerCombat(int $joueurId, int $adversaireId): array
    {
        if ($joueurId === $adversaireId) {
            return ['ok' => false, 'message' => "Tu ne peux pas te combattre toi-même."];
        }

        $db  = new Database();
        $pdo = $db->getConnection();

        // 1) Anti-revanche immédiate
        $lastOpp = CombatUtils::lastOpponentId($pdo, $joueurId);
        if ($lastOpp !== null && $lastOpp === $adversaireId) {
            return [
                'ok' => false,
                'message' => "Revanche immédiate interdite : tu viens déjà d’affronter cet adversaire."
            ];
        }

        // 2) Créer le combat (gagnant calculé plus tard ou immédiatement selon ta logique)
        $st = $pdo->prepare(
            "INSERT INTO combat (joueur1_id, joueur2_id, gagnant_id)
             VALUES (:j1, :j2, NULL)"
        );
        $st->execute([':j1' => $joueurId, ':j2' => $adversaireId]);

        return ['ok' => true, 'message' => "Combat créé ! Bonne chance ⚔️"];
    }
}
