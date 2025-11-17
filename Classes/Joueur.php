<?php
require_once 'Database.php';

class Joueur {
    private ?int $id;
    private string $pseudo;
    private string $email;
    private string $motDePasse;

    // Constructeur
    public function __construct(string $pseudo, string $email, string $motDePasse) {
        $this->id = null;
        $this->pseudo = $pseudo;
        $this->email = $email;
        $this->motDePasse = password_hash($motDePasse, PASSWORD_DEFAULT);
    }

    // Méthode pour enregistrer un joueur
    public function inscrire(): bool {
    $db = new Database();
    $pdo = $db->getConnection();

    try {
        $stmt = $pdo->prepare("INSERT INTO joueur (pseudo, email, mot_de_passe) VALUES (?, ?, ?)");
        $stmt->execute([$this->pseudo, $this->email, $this->motDePasse]);

        // Récupère l'ID du joueur fraîchement créé
        $joueurId = $pdo->lastInsertId();

        // Crée un personnage de base pour ce joueur
        require_once 'Personnage.php';
        $personnage = new Personnage($joueurId, $this->pseudo);
        $personnage->creer();

        return true;
    } catch (PDOException $e) {
        echo "❌ Erreur lors de l'inscription : " . $e->getMessage();
        return false;
    }
}


    // Méthode pour connecter un joueur
    public static function seConnecter(string $email, string $motDePasse): ?Joueur {
        $db = new Database();
        $pdo = $db->getConnection();

        $stmt = $pdo->prepare("SELECT * FROM joueur WHERE email = ?");
        $stmt->execute([$email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data && password_verify($motDePasse, $data['mot_de_passe'])) {
            // Si le mot de passe est correct, on crée un objet Joueur
            $joueur = new Joueur($data['pseudo'], $data['email'], $motDePasse);
            $joueur->id = $data['id'];
            return $joueur;
        }

        return null; // Échec de connexion
    }

    // ✅ Getters (accès sécurisé aux propriétés privées)
    public function getId(): ?int {
        return $this->id;
    }

    public function getPseudo(): string {
        return $this->pseudo;
    }

    public function getEmail(): string {
        return $this->email;
    }
}
?>
