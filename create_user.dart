import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:form_field_validator/form_field_validator.dart';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
import 'package:mydigitalgpt/login.dart';

import 'connected.dart';

class CreateUser extends StatefulWidget {
  //final String? authToken;

  //CreateUser();

  @override
  _CreateUserState createState() => _CreateUserState();
}



class _CreateUserState extends State<CreateUser> {
  final _formKey = GlobalKey<FormState>();
  String _username = '';
  String _password = '';
  String _email = '';
  String _firstname = '';
  String _lastname = '';

  String? _token;

  @override
  void initState() {
    super.initState();
    //_token = widget.authToken;
  }

  
  Future<void> sendFormData() async {
    //DateTime now = DateTime.now(); -> by API
    //String nowFormattedDate = DateFormat('yyyy-MM-dd HH:mm:ss').format(now);

    final String body = json.encode({
      'username': _username,
      'password': _password,
      'email': _email,
      'firstname': _firstname,
      'lastname': _lastname,
      //'created_at': nowFormattedDate, ->Geré par l'API
      //'updated_at': nowFormattedDate, -> Idem
    });

    print(body);
    //print('Mon token CreateUser whileSendIsUsed: ${_token ?? ''}');

    final response = await http.post(
      Uri.https('caen0001.mds-caen.yt', '/users'),
      headers: {
        'Content-Type': 'application/json',
      },
      body: body,
    );

    final data = json.decode(response.body);
    final token = data['token'];
    print('Mon token CreateUser whileSendIsUsed: ${token ?? ''}');

    // Stockage du token pour une utilisation ultérieure
    setState(() {
      _token = token;
    });

    if (response.statusCode == 201) {
      showDialog(
        context: context,
        builder: (BuildContext context) {
          return AlertDialog(
            title: Text('Utilisateur crée'),
            content: Container(
              child: Text(
                'Bravo, vous avez créer un utilisateur avec succès !',
              ),
            ),
            actions: [
              // Boutons d'action
              TextButton(
                child: Text('Continuer'),
                onPressed: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (context) => LoginPage()),
                  );
                },
              ),
            ],
          );
        },
      );
    } 
    else {
      print('Une erreur s\'est produite lors de l\'envoi de la requête !');
      print(response.body);
    }
  }

  @override
  Widget build(BuildContext context) {
    print('Mon token CreateUser ici: ${_token ?? ''}');
    final EmailValidatorCustom = MultiValidator([
      RequiredValidator(errorText: 'Veuillez saisir une adresse mail !'),
      EmailValidator(errorText: 'Cette adresse mail est invalide !')
    ]);
    final passwordValidator = MultiValidator([
      RequiredValidator(errorText: 'Veuillez saisir un mot de passe !'),
      MinLengthValidator(6,
          errorText:
              'Votre mot de passe doit contenir au minimum 6 caractères !')
    ]);
    return Scaffold(
      appBar: AppBar(
        title: Text('Création d\'un utilisateur'),
      ),
      body: Padding(
        padding: EdgeInsets.all(16.0),

        /*Idée 1
         Créer un fichier pour mon form uniquement et juste faire un appel ici dès que possible 

        Idée 1 ++
         (voir même pour faire en sorte que ce soit un form réutilisable)
         Comme j'ai pu faire avec mon menu, qu'il affiche des champs en fonction de la page et qu'il post des infos en fonctions de la page aussi
         A réfléchir
        */

        child: Form( 
          key: _formKey,
          child: Column(
            children: [
              TextFormField(
                decoration: InputDecoration(labelText: 'Nom d\'utilisateur'),
                validator: RequiredValidator(
                    errorText: 'Veuillez saisir un nom d\'utilisateur'),
                onSaved: (value) {
                  _username = value!;
                },
              ),
              TextFormField(
                decoration: InputDecoration(labelText: 'Mot de passe'),
                obscureText: true,
                validator: passwordValidator,
                onSaved: (value) {
                  _password = value!;
                },
              ),
              TextFormField(
                decoration: InputDecoration(labelText: 'Email'),
                validator: EmailValidatorCustom,
                onSaved: (value) {
                  _email = value!;
                },
              ),
              TextFormField(
                decoration: InputDecoration(labelText: 'Prénom'),
                validator:
                    RequiredValidator(errorText: 'Veuillez saisir un prénom'),
                onSaved: (value) {
                  _firstname = value!;
                },
              ),
              TextFormField(
                decoration: InputDecoration(labelText: 'Nom'),
                validator:
                    RequiredValidator(errorText: 'Veuillez saisir un nom'),
                onSaved: (value) {
                  _lastname = value!;
                },
              ),
              SizedBox(height: 16.0),
              ElevatedButton(
                onPressed: () {
                  final form = _formKey.currentState;
                  if (form != null && form.validate()) {
                    form.save();
                    sendFormData();
                  }
                },
                child: Text('Valider'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}