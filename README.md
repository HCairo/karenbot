# KarenBot

## Vue d'ensemble

Ce projet est un chatbot conçu pour assister les techniciens IT en fournissant un accès rapide à des documents, liens et ressources comme des modèles Excel, des fichiers Word, et plus encore. Le chatbot est construit à l'aide de Rasa (un framework open-source de machine learning pour créer des agents conversationnels) et est intégré dans un framework PHP MVC avec une interface front-end en JavaScript pour l'interaction utilisateur.

## Fonctionnalités

- Traitement du langage naturel (NLP) avec Rasa pour comprendre les requêtes des utilisateurs.
- Fournit des liens vers les documents demandés (par exemple, Excel, Word).
- Facile à intégrer dans un projet PHP et JavaScript existant.
- Personnalisable avec des intentions et réponses supplémentaires.

## Prérequis

Pour configurer et exécuter ce projet, vous aurez besoin de :

- **Python 3.8+** (pour Rasa)
- **PHP 7+**
- **Composer** (pour les dépendances PHP)
- **cURL** (extension PHP pour les requêtes HTTP)
- **Rasa** (installé via pip)
- **Git** (pour cloner le dépôt)

## Installation et Configuration

### 1. Cloner le dépôt

Clonez ce dépôt en local :

```bash
git clone https://github.com/HCairo/karenbot.git (HTTPS)
git clone git@github.com:HCairo/karenbot.git (SSH)
cd karenbot