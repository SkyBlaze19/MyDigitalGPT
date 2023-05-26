import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:mydigitalgpt/connected.dart';
import 'package:mydigitalgpt/main.dart';

class LoginPage extends StatefulWidget {
  String? _username;
  String _authToken = '';
  @override
  _LoginPageState createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final TextEditingController _usernameController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();

  String? _token;

  Future<void> _login() async {
    final String username = _usernameController.text;
    final String password = _passwordController.text;

    // print('username : $username');
    // print('password : $password');

    // Création du corps de la requête en utilisant les données d'authentification
    final String body = json.encode({
      'username': username,
      'password': password,
    });
    print('body : $body');

    // Envoi de la requête POST pour obtenir le token
    final response = await http.post(
      Uri.https('caen0001.mds-caen.yt', '/auth'),
      body: body,
    );

    print('Code de statut : ${response.statusCode}');
    print('Corps de la réponse : ${response.body}');

    if (response.statusCode == 201) {
      // Analyse de la réponse JSON pour extraire le token
      final data = json.decode(response.body);
      final token = data['token'];

      // Stockage du token pour une utilisation ultérieure
      setState(() {
        _token = token;
      });

      // Gestion du succès
      showDialog(
        context: context,
        builder: (context) => AlertDialog(
          title: const Text('Félicitations !'),
          content: const Text('Identifiants corrects. Vous êtes connecté'),
          actions: [
            TextButton(
              child: const Text('Continuer'),
              onPressed: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                      builder: (context) => Connected(
                            username: _usernameController.text,
                            authToken: _token,
                            passwordController: _passwordController,
                          )),
                );
              },
            ),
          ],
        ),
      );
    } else {
      // Gestion des erreurs de connexion
      showDialog(
        context: context,
        builder: (context) => AlertDialog(
          title: const Text('Erreur de connexion'),
          content: const Text('Identifiants invalides. Veuillez réessayer.'),
          actions: [
            TextButton(
                child: const Text('OK'),
                onPressed: () => {
                      _passwordController.clear(),
                      Navigator.pop(context),
                    }),
          ],
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    //debugPrint('Ceci est un message de débogage');
    print('Mon token lorsque j\'arrive sur login(token) = ${_token}');
    //print('Mon authToken = ${authToken}');
    return Scaffold(
      appBar: AppBar(
        title: const Text("Page de Connexion"),
        leading: IconButton(
          icon: Icon(Icons.arrow_back),
          onPressed: () {
            Navigator.pushReplacement(
              context,
              MaterialPageRoute(
                  builder: (context) =>
                        //_token = 'null';
                        //authToken = null;
                        /*setState(() {
                          _token = null;
                          authToken = null;
                          print('On est dans setState');
                          print('Mon token dans setState (token) = ${_token}');
                          print('Mon token dans setState (authToken) = ${authToken}');
                        });*/
                        WelcomePage()), 
            );
          },
        ),
      ),
      body: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            children: [
              TextField(
                  controller: _usernameController,
                  decoration: const InputDecoration(
                    labelText: 'Nom d\'utilisateur',
                  )),
              TextField(
                controller: _passwordController,
                obscureText: true,
                decoration: const InputDecoration(
                  labelText: 'Mot de Passe',
                ),
              ),
              ElevatedButton(
                onPressed: () async {
                  String dialogTitle = _usernameController.text.isEmpty &&
                          _passwordController.text.isEmpty
                      ? 'Les champs sont vides'
                      : 'Un champ est vide';
                  String dialogContent = _usernameController.text.isEmpty &&
                          _passwordController.text.isEmpty
                      ? 'Veuillez remplir tous les champs.'
                      : 'Veuillez remplir le champ vide.';

                  if (_usernameController.text.isEmpty ||
                      _passwordController.text.isEmpty) {
                    showDialog(
                      context: context,
                      builder: (context) => AlertDialog(
                        title: Text(dialogTitle),
                        content: Text(dialogContent),
                        actions: [
                          TextButton(
                            child: const Text('OK'),
                            onPressed: () => Navigator.pop(context),
                          ),
                        ],
                      ),
                    );
                    return;
                  } else {
                    await _login();
                    _passwordController.clear();
                    /*
                    setState(() {
                      _token = null; // Réinitialisation
                    });*/
                    /*
                    final result = await _login();
                    if (result == true) {
                      _passwordController.clear();
                      setState(() {
                        _token = null; // Réinitialisation
                      });
                    } else {
                      context;
                      MaterialPageRoute(
                        builder: (context) => Connected(
                          username: _usernameController.text,
                          authToken: _token,
                        ),
                      );
                    }*/
                  }
                },
                child: const Text('Se connecter'),
              )
            ],
          )),
    );
  }
}
