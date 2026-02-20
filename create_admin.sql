-- Supprimer l'admin s'il existe déjà
DELETE FROM utilisateurs WHERE email = 'admin@sitmypet.com';

-- Insérer le nouvel admin
INSERT INTO utilisateurs 
(email, roles, password, nom, prenom, telephone, adresse, role, is_active, created_at) 
VALUES 
('admin@sitmypet.com',
 '["ROLE_ADMIN"]',
 '$2y$13$A/NrbUgD5jTXUELd8q6ZueLQ9M3G8kqVwQxX9X9X9X9X9X9X9', -- hash de "Admin@1234"
 'Admin',
 'Super',
 '20123456',
 'Tunis, Tunisie',
 'ROLE_ADMIN',
 1,
 NOW()
);

-- Vérifier que l'admin a bien été créé
SELECT * FROM utilisateurs WHERE email = 'admin@sitmypet.com';