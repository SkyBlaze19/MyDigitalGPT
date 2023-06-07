<?php

class UserController {
    public function index() {
        // Logique pour afficher la liste des utilisateurs
        // Récupérer les données des utilisateurs depuis le modèle
        // Charger la vue correspondante avec les données récupérées
        
        // Exemple simplifié
        $users = UserModel::getAllUsers();
        include 'views/user/index.php';
    }
    
    public function show($id) {
        // Logique pour afficher les détails d'un utilisateur spécifique
        // Récupérer les données de l'utilisateur depuis le modèle
        // Charger la vue correspondante avec les données récupérées
        
        // Exemple simplifié
        $user = UserModel::getUserById($id);
        include 'views/user/show.php';
    }
    
    public function create() {
        // Logique pour afficher le formulaire de création d'un utilisateur
        // Charger la vue correspondante avec le formulaire
        
        // Exemple simplifié
        include 'views/user/create.php';
    }
    
    public function store() {
        // Logique pour traiter les données soumises lors de la création d'un utilisateur
        // Valider les données, enregistrer l'utilisateur dans le modèle, etc.
        
        // Exemple simplifié
        $username = $_POST['username'];
        $email = $_POST['email'];
        
        // Enregistrer l'utilisateur dans le modèle
        UserModel::createUser($username, $email);
        
        // Rediriger vers une autre page ou afficher un message de succès
        header('Location: /users');
        exit;
    }
    
    public function edit($id) {
        // Logique pour afficher le formulaire d'édition d'un utilisateur
        // Récupérer les données de l'utilisateur depuis le modèle
        // Charger la vue correspondante avec les données récupérées
        
        // Exemple simplifié
        $user = UserModel::getUserById($id);
        include 'views/user/edit.php';
    }
    
    public function update($id) {
        // Logique pour traiter les données soumises lors de la mise à jour d'un utilisateur
        // Valider les données, mettre à jour l'utilisateur dans le modèle, etc.
        
        // Exemple simplifié
        $username = $_POST['username'];
        $email = $_POST['email'];
        
        // Mettre à jour l'utilisateur dans le modèle
        UserModel::updateUser($id, $username, $email);
        
        // Rediriger vers une autre page ou afficher un message de succès
        header('Location: /users');
        exit;
    }
    
    public function delete($id) {
        // Logique pour supprimer un utilisateur spécifique
        // Supprimer l'utilisateur dans le modèle
        
        // Exemple simplifié
        UserModel::deleteUser($id);
        
        // Rediriger vers une autre page ou afficher un message de succès
        header('Location: /users');
        exit;
    }
}
