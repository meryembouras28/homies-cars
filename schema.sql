
CREATE DATABASE IF NOT EXISTS homies_cars
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE homies_cars;


CREATE TABLE categories (
  id   INT AUTO_INCREMENT PRIMARY KEY,
  nom  VARCHAR(50) NOT NULL,
  UNIQUE KEY uniq_categorie_nom (nom)
) ENGINE=InnoDB;


CREATE TABLE users (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  firstname  VARCHAR(100) NOT NULL,
  lastname   VARCHAR(100) NOT NULL,
  username   VARCHAR(100) NOT NULL,
  email      VARCHAR(190) NOT NULL,
  password   VARCHAR(255) NOT NULL,           
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_users_email (email),
  UNIQUE KEY uniq_users_username (username)
) ENGINE=InnoDB;


CREATE TABLE agences (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  nom       VARCHAR(100) NOT NULL,
  ville     VARCHAR(100) NOT NULL,
  adresse   VARCHAR(190) NOT NULL,
  telephone VARCHAR(30)  NOT NULL
) ENGINE=InnoDB;


CREATE TABLE voitures (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  categorie_id INT NOT NULL,
  marque       VARCHAR(60)  NOT NULL,
  modele       VARCHAR(60)  NOT NULL,
  annee        SMALLINT     NOT NULL,
  prix_jour    DECIMAL(8,2) NOT NULL CHECK (prix_jour > 0),
  transmission ENUM('Manuelle','Automatique') NOT NULL DEFAULT 'Manuelle',
  carburant    ENUM('Essence','Diesel','Hybride','Electrique') NOT NULL DEFAULT 'Essence',
  nb_places    TINYINT NOT NULL DEFAULT 5,
  image        VARCHAR(120) NOT NULL,
  description  VARCHAR(255) NOT NULL,
  statut       ENUM('disponible','maintenance','hors_service') NOT NULL DEFAULT 'disponible',

  CONSTRAINT fk_voiture_categorie
    FOREIGN KEY (categorie_id) REFERENCES categories(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE INDEX idx_voitures_categorie ON voitures(categorie_id);
CREATE INDEX idx_voitures_statut    ON voitures(statut);


CREATE TABLE reservations (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  voiture_id  INT NOT NULL,
  agence_id   INT NOT NULL,
  date_debut  DATE NOT NULL,
  date_fin    DATE NOT NULL,
  prix_total  DECIMAL(9,2) NOT NULL,
  statut      ENUM('en_attente','confirmee','annulee','terminee') NOT NULL DEFAULT 'confirmee',
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_reservation_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE CASCADE,

  CONSTRAINT fk_reservation_voiture
    FOREIGN KEY (voiture_id) REFERENCES voitures(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,

  CONSTRAINT fk_reservation_agence
    FOREIGN KEY (agence_id) REFERENCES agences(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,

  CONSTRAINT chk_dates_valides CHECK (date_fin > date_debut)
) ENGINE=InnoDB;


CREATE INDEX idx_reservations_dispo ON reservations(voiture_id, date_debut, date_fin);
CREATE INDEX idx_reservations_user  ON reservations(user_id);


CREATE TABLE messages_contact (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  nom        VARCHAR(150) NOT NULL,
  email      VARCHAR(190) NOT NULL,
  tel        VARCHAR(30)  NOT NULL,
  probleme   VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;



INSERT INTO categories (nom) VALUES
  ('Citadine'),
  ('Berline'),
  ('SUV'),
  ('Luxe');

INSERT INTO agences (nom, ville, adresse, telephone) VALUES
  ('Homies Cars - Centre Ville', 'Oujda',   '12 Avenue Mohammed V, Oujda',        '+212 642 420 376'),
  ('Homies Cars - Nador',        'Nador',   '5 Boulevard Hassan II, Nador',       '+212 536 874 356'),
  ('Homies Cars - Aéroport',     'Casablanca', 'Aéroport Mohammed V, Nouaceur',   '+212 522 539 100');


INSERT INTO voitures (categorie_id, marque, modele, annee, prix_jour, transmission, carburant, nb_places, image, description, statut) VALUES
  (2, 'Volkswagen', 'Golf 7',        2021, 350.00, 'Manuelle',    'Essence', 5, 'golf.jpg',
   'Une voiture polyvalente et appréciée, reconnue pour son confort et ses performances équilibrées.', 'disponible'),
  (1, 'Peugeot',    '208',           2022, 250.00, 'Manuelle',    'Essence', 5, '208.jpg',
   'Une petite voiture pratique, confortable et abordable, parfaite pour la ville.', 'disponible'),
  (1, 'Renault',    'Clio 4',        2020, 230.00, 'Manuelle',    'Diesel',  5, 'clio.jpg',
   'Compacte populaire, idéale pour la conduite en ville, avec un bon confort.', 'disponible'),
  (4, 'Mercedes',   'Classe C',      2023, 900.00, 'Automatique', 'Diesel',  5, 'mercedes.jpg',
   'Berline haut de gamme, finitions soignées et conduite très confortable.', 'disponible'),
  (1, 'Citroën',    'C3',            2021, 240.00, 'Manuelle',    'Essence', 5, 'citroen.jpg',
   'Look moderne, économique et agréable à conduire au quotidien.', 'disponible'),
  (3, 'Audi',       'Q8',            2023, 1200.00,'Automatique', 'Essence', 5, 'audi.jpg',
   'SUV premium spacieux, technologie de pointe et grand confort de route.', 'disponible'),
  (1, 'Ford',       'Fiesta',        2020, 220.00, 'Manuelle',    'Essence', 5, 'ford.jpg',
   'Citadine nerveuse et économique, facile à garer.', 'disponible'),
  (4, 'Porsche',    '911',           2022, 2000.00,'Automatique', 'Essence', 4, 'porche.jpg',
   'Voiture sportive emblématique pour une expérience de conduite unique.', 'disponible'),
  (2, 'BMW',        'Série 3',       2022, 850.00, 'Automatique', 'Diesel',  5, 'bmw.jpg',
   'Berline dynamique alliant sportivité et confort de conduite.', 'disponible'),
  (3, 'Land Rover', 'Range Rover',   2023, 1800.00,'Automatique', 'Diesel',  5, 'range.jpg',
   'SUV de luxe, robuste et raffiné, pour toutes les routes.', 'disponible');
