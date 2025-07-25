# Requirements Document

## Introduction

Ce système permet de générer des examens de certification personnalisés en sélectionnant des chapitres spécifiques et en configurant le nombre de questions par chapitre. Le système utilise une banque de questions organisée par chapitres pour chaque certification et génère des examens avec des questions sélectionnées aléatoirement selon la configuration définie.

## Requirements

### Requirement 1

**User Story:** En tant qu'administrateur, je veux créer des chapitres pour une certification, afin d'organiser les questions par thématiques spécifiques.

#### Acceptance Criteria

1. WHEN l'administrateur accède à une certification THEN le système SHALL afficher la liste des chapitres existants
2. WHEN l'administrateur clique sur "Ajouter un chapitre" THEN le système SHALL afficher un formulaire de création de chapitre
3. WHEN l'administrateur saisit le nom et la description d'un chapitre THEN le système SHALL créer le chapitre et l'associer à la certification
4. WHEN l'administrateur modifie un chapitre existant THEN le système SHALL mettre à jour les informations du chapitre
5. WHEN l'administrateur supprime un chapitre THEN le système SHALL supprimer le chapitre ET toutes ses questions associées après confirmation

### Requirement 2

**User Story:** En tant qu'administrateur, je veux ajouter des questions à un chapitre spécifique, afin de constituer une banque de questions organisée par thématique.

#### Acceptance Criteria

1. WHEN l'administrateur sélectionne un chapitre THEN le système SHALL afficher la liste des questions du chapitre
2. WHEN l'administrateur clique sur "Ajouter une question" THEN le système SHALL afficher un formulaire de création de question
3. WHEN l'administrateur saisit une question avec ses réponses THEN le système SHALL rattacher la question au chapitre sélectionné
4. WHEN l'administrateur définit les réponses THEN le système SHALL permettre de marquer une ou plusieurs réponses comme correctes
5. WHEN l'administrateur assigne une difficulté et des points THEN le système SHALL enregistrer ces métadonnées avec la question

### Requirement 3

**User Story:** En tant qu'administrateur, je veux configurer un examen en sélectionnant les chapitres et le nombre de questions par chapitre, afin de créer des examens personnalisés.

#### Acceptance Criteria

1. WHEN l'administrateur accède à la configuration d'examen d'une certification THEN le système SHALL afficher tous les chapitres disponibles
2. WHEN l'administrateur sélectionne des chapitres THEN le système SHALL permettre de définir le nombre de questions pour chaque chapitre sélectionné
3. WHEN l'administrateur définit un nombre de questions pour un chapitre THEN le système SHALL vérifier que ce nombre ne dépasse pas le nombre de questions disponibles dans ce chapitre
4. WHEN l'administrateur configure le temps total de l'examen THEN le système SHALL enregistrer cette limite de temps
5. WHEN l'administrateur sauvegarde la configuration THEN le système SHALL créer ou mettre à jour la configuration d'examen

### Requirement 4

**User Story:** En tant que système, je veux générer un examen avec des questions aléatoires selon la configuration définie, afin de créer des examens uniques à chaque passage.

#### Acceptance Criteria

1. WHEN un utilisateur démarre un examen THEN le système SHALL sélectionner aléatoirement le nombre de questions configuré pour chaque chapitre
2. WHEN le système sélectionne les questions THEN il SHALL respecter la répartition définie par chapitre dans la configuration
3. WHEN le système génère l'examen THEN il SHALL mélanger l'ordre des questions de tous les chapitres
4. WHEN le système présente les questions THEN il SHALL mélanger l'ordre des réponses pour chaque question
5. WHEN l'examen est généré THEN le système SHALL appliquer la limite de temps configurée

