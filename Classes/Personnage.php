<?php
require_once 'Database.php';

class Personnage {
    private ?int $id;
    private int $joueurId;
    private string $nom;
    private int $niveau;
    private int $pointsVie;
    private int $attaque;
    private int $defense;
    private int $experience;
    private int $victoires;

    public function __construct(
        int $joueurId,
        string $nom,
        int $niveau = 1,
        int $pointsVie = 100,
        int $attaque = 10,
        int $defense = 5,
        int $experience = 0,
        int $victoires = 0,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->joueurId = $joueurId;
        $this->nom = $nom;
        $this->niveau = $niveau;
        $this->pointsVie = $pointsVie;
        $this->attaque = $attaque;
        $this->defense = $defense;
        $this->experience = $experience;
        $this->victoires = $victoires;
    }

    // === CRÉER UN PERSONNAGE EN BASE ===
    public function creer(): bool {
        $db = new Database();
        $pdo = $db->getConnection();

        try {
            $stmt = $pdo->prepare("
                INSERT INTO personnage (joueur_id, nom, niveau, points_vie, attaque, defense, experience, victoires)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([
                $this->joueurId,
                $this->nom,
                $this->niveau,
                $this->pointsVie,
                $this->attaque,
                $this->defense,
                $this->experience,
                $this->victoires
            ]);
        } catch (PDOException $e) {
            echo "❌ Erreur lors de la création du personnage : " . $e->getMessage();
            return false;
        }
    }

    // === COMBAT ENTRE PERSONNAGES ===
    public function combattre(Personnage $adversaire): string {
        $pv1 = $this->pointsVie;
        $pv2 = $adversaire->pointsVie;

        echo "<h3>⚔️ Combat : {$this->nom} VS {$adversaire->nom}</h3>";

        while ($pv1 > 0 && $pv2 > 0) {
            $degats1 = max(0, $this->attaque - $adversaire->defense);
            $pv2 -= $degats1;
            echo "{$this->nom} inflige {$degats1} dégâts à {$adversaire->nom} (PV restants : " . max(0, $pv2) . ")<br>";

            if ($pv2 <= 0) break;

            $degats2 = max(0, $adversaire->attaque - $this->defense);
            $pv1 -= $degats2;
            echo "{$adversaire->nom} inflige {$degats2} dégâts à {$this->nom} (PV restants : " . max(0, $pv1) . ")<br>";
        }

        $vainqueur = ($pv1 > 0) ? $this->nom : $adversaire->nom;
        echo "<h3>🏆 Le vainqueur est {$vainqueur} !</h3>";

        return $vainqueur;
    }

    // === GAGNER UNE VICTOIRE ===
    public function gagnerVictoire(): void {
        $this->victoires++;

        if ($this->victoires >= 3) {
            $this->niveau++;
            $this->victoires = 0;
            $this->pointsVie += 10;
            $this->attaque += 2;
            $this->defense += 1;
            echo "<p style='color:green;'>🎉 {$this->nom} passe au niveau {$this->niveau} après 3 victoires !</p>";
        }

        $db = new Database();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare("
            UPDATE personnage
            SET niveau = ?, points_vie = ?, attaque = ?, defense = ?, victoires = ?
            WHERE joueur_id = ?
        ");
        $stmt->execute([
            $this->niveau,
            $this->pointsVie,
            $this->attaque,
            $this->defense,
            $this->victoires,
            $this->joueurId
        ]);
    }

    // === GAGNER DE L'EXPÉRIENCE ===
    public function gagnerExperience(int $xpGagne): void {
        $this->experience += $xpGagne;
        echo "<p>✨ {$this->nom} gagne {$xpGagne} points d'expérience.</p>";

        // Vérifie si un level-up est nécessaire
        $this->verifierMonteeNiveau();
        $this->sauvegarder();
    }

    // === VÉRIFIE SI LE JOUEUR MONTE DE NIVEAU ===
    private function verifierMonteeNiveau(): void {
        $seuils = [
            1 => 0,
            2 => 200,
            3 => 500,
            4 => 1000,
            5 => 2000
        ];

        foreach ($seuils as $niv => $xpNecessaire) {
            if ($this->experience >= $xpNecessaire && $niv > $this->niveau) {
                $this->niveau = $niv;
                $this->pointsVie += 15;
                $this->attaque += 3;
                $this->defense += 2;
                echo "<p style='color:blue;'>💫 {$this->nom} monte au niveau {$this->niveau} !</p>";
            }
        }
    }

    // === SAUVEGARDE LES MODIFS DANS LA BDD ===
    private function sauvegarder(): void {
        $db = new Database();
        $pdo = $db->getConnection();

        $stmt = $pdo->prepare("
            UPDATE personnage 
            SET niveau = ?, points_vie = ?, attaque = ?, defense = ?, experience = ?, victoires = ?
            WHERE joueur_id = ?
        ");
        $stmt->execute([
            $this->niveau,
            $this->pointsVie,
            $this->attaque,
            $this->defense,
            $this->experience,
            $this->victoires,
            $this->joueurId
        ]);
    }

    // === GETTERS ===
    public function getNom(): string { return $this->nom; }
    public function getJoueurId(): int { return $this->joueurId; }
    public function getNiveau(): int { return $this->niveau; }
    public function getPointsVie(): int { return $this->pointsVie; }
    public function getAttaque(): int { return $this->attaque; }
    public function getDefense(): int { return $this->defense; }
    public function getExperience(): int { return $this->experience; }
    public function getVictoires(): int { return $this->victoires; }
}
?>
