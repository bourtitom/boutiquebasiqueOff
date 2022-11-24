-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 15 nov. 2022 à 17:48
-- Version du serveur : 10.4.21-MariaDB
-- Version de PHP : 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `boutiquebasiqueoff`
--
CREATE DATABASE IF NOT EXISTS `boutiquebasiqueoff` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `boutiquebasiqueoff`;

-- --------------------------------------------------------

--
-- Structure de la table `categoryproduit`
--

CREATE TABLE `categoryproduit` (
                                   `CATEGORY_ID` int(11) NOT NULL,
                                   `CATEGORY_NAME` varchar(100) NOT NULL,
                                   `CATEGORY_LOGO` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `categoryproduit`
--

INSERT INTO `categoryproduit` (`CATEGORY_ID`, `CATEGORY_NAME`, `CATEGORY_LOGO`) VALUES
                                                                                    (1, 'forgeron', 'forgeron.jpg'),
                                                                                    (2, 'marchand ambulant', 'marchandambulant.png'),
                                                                                    (3, 'boulanger', 'boulanger.jpg'),
                                                                                    (4, 'alchimiste', 'alchimiste.png'),
                                                                                    (5, 'chasseur', 'chasseur.jpg'),
                                                                                    (6, 'dompteur', 'dompteur.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE `client` (
                          `CLIENT_ID` int(11) NOT NULL,
                          `CLIENT_PRENOM` varchar(50) NOT NULL,
                          `CLIENT_NOM` varchar(50) NOT NULL,
                          `CLIENT_NAISSANCE` date DEFAULT NULL,
                          `CLIENT_MAIL` varchar(200) NOT NULL,
                          `CLIENT_PASSWORD` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`CLIENT_ID`, `CLIENT_PRENOM`, `CLIENT_NOM`, `CLIENT_NAISSANCE`, `CLIENT_MAIL`, `CLIENT_PASSWORD`) VALUES
                                                                                                                            (0, 'admin', 'root', '1010-01-01', 'admin@root', '$2y$10$LOjUVhGpFMKbFH0J4pb.Ku7tVrh6PLfahcHQZoajNFx7anEeZFDiO'),
                                                                                                                            (1, 'Melchior', 'Descluse', '2002-07-29', 'mdescluse@gmail.com', '$2y$10$EIN8x3w60iVXvw6p2r3pB.10vGyyBSzzSvDGGLp/IN5Qp/5.u.dP.'),
                                                                                                                            (2, 'Hugoo', 'DECRYPT', '2000-07-14', 'hdecrypt@gmail.com', '$2y$10$pPq7A5XwOkUhamX21YOP5OEYt7OOKO5ljMInTzgKczEnZq29tUaiu'),
                                                                                                                            (3, 'Daniel', 'Vieira', '2004-10-13', 'dvieira@gmail.com', '$2y$10$38T5XpEm5Z.AjIBIDQMpO.KrwuQ.fz9GR.zOgIYQzElRQKfDINYZe'),
                                                                                                                            (4, 'Tom', 'Bourti', '2005-10-17', 'tbourti@gmail.com', '$2y$10$QNJJb//2HilizYVYBf3y4egwq8.F/dy2YGLXBkmZzKioz4XCeGMdq');

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

CREATE TABLE `commande` (
                            `COMMANDE_ID` int(11) NOT NULL,
                            `CLIENT_ID` int(11) NOT NULL,
                            `COMMANDE_DATE` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `commande`
--

INSERT INTO `commande` (`COMMANDE_ID`, `CLIENT_ID`, `COMMANDE_DATE`) VALUES
                                                                         (48, 4, '2022-11-15 09:26:26'),
                                                                         (49, 4, '2022-11-15 09:31:45'),
                                                                         (50, 1, '2022-11-15 10:54:20'),
                                                                         (51, 0, '2022-11-15 11:58:56');

-- --------------------------------------------------------

--
-- Structure de la table `lignecommande`
--

CREATE TABLE `lignecommande` (
                                 `COMMANDE_ID` int(11) NOT NULL,
                                 `PRODUIT_ID` int(11) NOT NULL,
                                 `QUANTITE` int(11) NOT NULL,
                                 `PRIX` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `lignecommande`
--

INSERT INTO `lignecommande` (`COMMANDE_ID`, `PRODUIT_ID`, `QUANTITE`, `PRIX`) VALUES
                                                                                  (48, 37, 1, '1'),
                                                                                  (48, 47, 1, '10'),
                                                                                  (49, 29, 1, '200'),
                                                                                  (49, 49, 1, '1200'),
                                                                                  (50, 40, 1, '20'),
                                                                                  (50, 44, 1, '50'),
                                                                                  (51, 33, 2, '1'),
                                                                                  (51, 37, 3, '1');

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

CREATE TABLE `produit` (
                           `PRODUIT_ID` int(11) NOT NULL,
                           `PRODUIT_NOM` varchar(50) NOT NULL,
                           `PRODUIT_PRIX` decimal(10,0) NOT NULL,
                           `PRODUIT_IMAGE` varchar(100) DEFAULT NULL,
                           `CATEGORY_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `produit`
--

INSERT INTO `produit` (`PRODUIT_ID`, `PRODUIT_NOM`, `PRODUIT_PRIX`, `PRODUIT_IMAGE`, `CATEGORY_ID`) VALUES
                                                                                                        (29, 'Sword', '200', 'sword.jpg', 1),
                                                                                                        (30, 'Axe', '250', 'axe.jpg', 1),
                                                                                                        (31, 'Bow', '150', 'bow.jpg', 1),
                                                                                                        (32, 'Arrow', '5', 'arrow.jpg', 1),
                                                                                                        (33, 'Tissu', '1', 'tissu.jpg', 2),
                                                                                                        (34, 'Iron', '10', 'iron.webp', 2),
                                                                                                        (35, 'Wood', '3', 'buche.jpeg', 2),
                                                                                                        (36, 'Salad', '2', 'salad.webp', 2),
                                                                                                        (37, 'Bread', '1', 'bread.jpeg', 3),
                                                                                                        (38, 'Tarte au Potiron', '20', 'tartepotiron.jpg', 3),
                                                                                                        (39, 'Tarte au Pomme', '20', 'tartepomme.webp', 3),
                                                                                                        (40, 'Tarte au Fraise', '20', 'tarteberry.jpg', 3),
                                                                                                        (41, 'Potion de Soins', '50', 'potionsoins.webp', 4),
                                                                                                        (42, 'Potion de Force', '50', 'potionforce.png', 4),
                                                                                                        (43, 'Potion de Poison', '50', 'potionpoison.webp', 4),
                                                                                                        (44, 'Potion de Charme', '50', 'potioncharme.webp', 4),
                                                                                                        (45, 'Peau de Sanglier', '10', 'peaus.webp', 5),
                                                                                                        (46, 'Viande de Sanglier', '15', 'viands.png', 5),
                                                                                                        (47, 'Peau de Lapin', '10', 'peaul.jpeg', 5),
                                                                                                        (48, ' Viande de Lapin', '15', 'viandel.jpg', 5),
                                                                                                        (49, 'Dragon', '1200', 'dragon.jpg', 6),
                                                                                                        (50, 'Loup', '350', 'loup.jpg', 6),
                                                                                                        (51, 'Chien', '100', 'chien.jpg', 6),
                                                                                                        (52, 'Cheval', '300', 'cheval.webp', 6);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categoryproduit`
--
ALTER TABLE `categoryproduit`
    ADD PRIMARY KEY (`CATEGORY_ID`);

--
-- Index pour la table `client`
--
ALTER TABLE `client`
    ADD PRIMARY KEY (`CLIENT_ID`);

--
-- Index pour la table `commande`
--
ALTER TABLE `commande`
    ADD PRIMARY KEY (`COMMANDE_ID`),
    ADD KEY `FK_CLIENT_COMMANDE` (`CLIENT_ID`);

--
-- Index pour la table `lignecommande`
--
ALTER TABLE `lignecommande`
    ADD PRIMARY KEY (`COMMANDE_ID`,`PRODUIT_ID`),
    ADD KEY `FK_LIGNECOMMANDE_PRODUIT` (`PRODUIT_ID`);

--
-- Index pour la table `produit`
--
ALTER TABLE `produit`
    ADD PRIMARY KEY (`PRODUIT_ID`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categoryproduit`
--
ALTER TABLE `categoryproduit`
    MODIFY `CATEGORY_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `client`
--
ALTER TABLE `client`
    MODIFY `CLIENT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `commande`
--
ALTER TABLE `commande`
    MODIFY `COMMANDE_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT pour la table `produit`
--
ALTER TABLE `produit`
    MODIFY `PRODUIT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
    ADD CONSTRAINT `FK_CLIENT_COMMANDE` FOREIGN KEY (`CLIENT_ID`) REFERENCES `client` (`CLIENT_ID`);

--
-- Contraintes pour la table `lignecommande`
--
ALTER TABLE `lignecommande`
    ADD CONSTRAINT `FK_LIGNECOMMANDE_COMMANDE` FOREIGN KEY (`COMMANDE_ID`) REFERENCES `commande` (`COMMANDE_ID`),
    ADD CONSTRAINT `FK_LIGNECOMMANDE_PRODUIT` FOREIGN KEY (`PRODUIT_ID`) REFERENCES `produit` (`PRODUIT_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
