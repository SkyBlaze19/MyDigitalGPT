import 'package:flutter/material.dart';
import 'package:mydigitalgpt/create_user.dart' as CreateUserPage;
import 'package:mydigitalgpt/login.dart';
import 'package:mydigitalgpt/showAll_user.dart';

class Connected extends StatefulWidget {
  final String? username;
  final String? authToken;
  final TextEditingController? passwordController;

  Connected({
    this.username,
    this.authToken,
    this.passwordController,
  });

  @override
  _ConnectedState createState() => _ConnectedState();
}

class _ConnectedState extends State<Connected> {
  LoginPage loginPage = LoginPage();
  String? _token;

  IconData _utilisateursIcon = Icons.keyboard_arrow_down;
  bool _utilisateursExpanded = false;

  IconData _universIcon = Icons.keyboard_arrow_down;
  bool _universExpanded = false;

  IconData _personnagesIcon = Icons.keyboard_arrow_down;
  bool _personnagesExpanded = false;

  IconData _conversationsIcon = Icons.keyboard_arrow_down;
  bool _conversationsExpanded = false;

  @override
  void initState() {
    super.initState();
    _token = widget.authToken;
  }

  Widget build(BuildContext context) {
    String? authToken = widget.authToken;
    print('Mon token : ${authToken ?? ''}');
    return Scaffold(
      appBar: AppBar(
        title: Text('Bonjour, ${widget.username} !'),
        leading: Builder(
          builder: (BuildContext context) {
            return IconButton(
              icon: Icon(Icons.menu),
              onPressed: () {
                Scaffold.of(context).openDrawer();
              },
            );
          },
        ),
        actions: [
          Padding(
            padding: EdgeInsets.only(right: 10.0),
            child: IconButton(
              icon: Icon(Icons.logout),
              onPressed: () {
                widget.passwordController!.clear();
                setState(() {
                  _token = null;
                  authToken = null;
                  print('On est dans setState');
                  print('Mon token dans setState (token) = ${_token}');
                  print('Mon token dans setState (authToken) = ${authToken}');
                });
                //Navigator.pop(context, true); // Déconnexion
                Navigator.push(
                  context,
                  MaterialPageRoute(
                      builder: (context) => LoginPage(
                      )
                    ),
                );
              },
            ),
          ),
        ],
      ),
      drawer: Drawer(
        child: ListView(
          children: [
            DrawerHeader(
              decoration: BoxDecoration(
                color: Colors.blue,
              ),
              child: Center(
                child: Text(
                  'Menu',
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 24,
                  ),
                ),
              ),
            ),
            //Utilisateurs
            ExpansionTile(
              title: Text('Utilisateurs'),
              trailing: Icon(_utilisateursIcon),
              onExpansionChanged: (isExpanded) {
                setState(() {
                  _utilisateursExpanded = isExpanded;
                  _utilisateursIcon = isExpanded
                      ? Icons.keyboard_arrow_up
                      : Icons.keyboard_arrow_down;
                });
              },
              children: [
                /*ListTile(
                  title: Text('Créer un utilisateur'),
                  onTap: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => CreateUserPage.CreateUser(authToken: authToken),
                      ),
                    );
                  },
                ),*/
                ListTile(
                  title: Text('Voir les utilisateurs'),
                  onTap: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (context) => ShowAllUser()
                        ),
                    ); // Fermer le menu
                  },
                ),
                ListTile(
                  title: Text('Voir un utilisateur'),
                  onTap: () {
                    Navigator.pop(context); // Fermer le menu
                  },
                ),
              ],
            ),

            // Univers
            ExpansionTile(
              title: Text('Univers'),
              trailing: Icon(_universIcon),
              onExpansionChanged: (isExpanded) {
                setState(() {
                  _universExpanded = isExpanded;
                  _universIcon = isExpanded
                      ? Icons.keyboard_arrow_up
                      : Icons.keyboard_arrow_down;
                });
              },
              children: [
                //Personnages
                ExpansionTile(
                  title: Text('Personnages'),
                  trailing: Icon(_personnagesIcon),
                  onExpansionChanged: (isExpanded) {
                    setState(() {
                      _personnagesExpanded = isExpanded;
                      _personnagesIcon = isExpanded
                          ? Icons.keyboard_arrow_up
                          : Icons.keyboard_arrow_down;
                    });
                  },
                  children: [
                    ListTile(
                      title: Text('Créer un personnage'),
                      onTap: () {
                        Navigator.pop(context); // Fermer le menu
                      },
                    ),
                    ListTile(
                      title: Text('Ajouter une description'),
                      onTap: () {
                        Navigator.pop(context); // Fermer le menu
                      },
                    ),
                    ListTile(
                      title: Text('Voir un personnage'),
                      onTap: () {
                        Navigator.pop(context); // Fermer le menu
                      },
                    ),
                    ListTile(
                      title: Text('Voir tous les personnages d\'un univers'),
                      onTap: () {
                        Navigator.pop(context); // Fermer le menu
                      },
                    ),
                  ],
                ),
                //Fin personnages

                ListTile(
                  title: Text('Créer un univers'),
                  onTap: () {
                    Navigator.pop(context); // Fermer le menu
                  },
                ),
                ListTile(
                  title: Text('Renommer un univers'),
                  onTap: () {
                    Navigator.pop(context); // Fermer le menu
                  },
                ),
                ListTile(
                  title: Text('Voir un univers'),
                  onTap: () {
                    Navigator.pop(context); // Fermer le menu
                  },
                ),
                ListTile(
                  title: Text('Voir tous les univers'),
                  onTap: () {
                    Navigator.pop(context); // Fermer le menu
                  },
                ),
              ],
            ),

            ExpansionTile(
              title: Text('Conversations'),
              trailing: Icon(_personnagesIcon),
              onExpansionChanged: (isExpanded) {
                setState(() {
                  _conversationsExpanded = isExpanded;
                  _conversationsIcon = isExpanded
                      ? Icons.keyboard_arrow_up
                      : Icons.keyboard_arrow_down;
                });
              },
              children: [
                ListTile(
                  title: Text('Voir toutes les conversations'),
                  onTap: () {
                    Navigator.pop(context); // Fermer le menu
                  },
                ),
                ListTile(
                  title: Text('Voir une conversation'),
                  onTap: () {
                    Navigator.pop(context); // Fermer le menu
                  },
                ),
                ListTile(
                  title: Text('Créer une nouvelle conversation'),
                  onTap: () {
                    Navigator.pop(context); // Fermer le menu
                  },
                ),
                ListTile(
                  title: Text('Supprimer une conversation'),
                  onTap: () {
                    Navigator.pop(context); // Fermer le menu
                  },
                ),
              ],
            ),
          ],
        ),
      ),
      body: Container(
        color: Colors.blueGrey,
        child: Center(
          child: Text(
            'Vous êtes connecté !',
            style: TextStyle(fontSize: 20, color: Colors.white),
          ),
        ),
      ),
    );
  }
}
