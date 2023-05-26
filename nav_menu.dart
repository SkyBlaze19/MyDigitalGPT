import 'package:flutter/material.dart';
import 'package:mydigitalgpt/showAll_user.dart';

class AppDrawer extends StatelessWidget {
  final String currentPage;
  final String username;

  const AppDrawer({required this.currentPage, required this.username});

  @override
  Widget build(BuildContext context) {
    //username = widget.username;
    return Drawer(
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
          // Utilisateurs
          ExpansionTile(
            title: Text('Utilisateurs'),
            trailing: Icon(Icons.keyboard_arrow_down),
            children: [
              if (currentPage != 'showAllUsers')
                ListTile(
                  title: Text('Voir les utilisateurs'),
                  onTap: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (context) => ShowAllUser(username: username)),
                    );
                  },
                ),
              ListTile(
                title: Text('Voir un utilisateur'),
                onTap: () {
                  Navigator.pop(context);
                },
              ),
            ],
          ),

          // Univers
          ExpansionTile(
            title: Text('Univers'),
            trailing: Icon(Icons.keyboard_arrow_down),
            children: [
              // Personnages
              ExpansionTile(
                title: Text('Personnages'),
                trailing: Icon(Icons.keyboard_arrow_down),
                children: [
                  ListTile(
                    title: Text('Créer un personnage'),
                    onTap: () {
                      Navigator.pop(context);
                    },
                  ),
                  ListTile(
                    title: Text('Ajouter une description'),
                    onTap: () {
                      Navigator.pop(context);
                    },
                  ),
                  ListTile(
                    title: Text('Voir un personnage'),
                    onTap: () {
                      Navigator.pop(context);
                    },
                  ),
                  ListTile(
                    title: Text('Voir tous les personnages d\'un univers'),
                    onTap: () {
                      Navigator.pop(context);
                    },
                  ),
                ],
              ),
              // Fin personnages

              ListTile(
                title: Text('Créer un univers'),
                onTap: () {
                  Navigator.pop(context);
                },
              ),
              ListTile(
                title: Text('Renommer un univers'),
                onTap: () {
                  Navigator.pop(context);
                },
              ),
              ListTile(
                title: Text('Voir un univers'),
                onTap: () {
                  Navigator.pop(context);
                },
              ),
              ListTile(
                title: Text('Voir tous les univers'),
                onTap: () {
                  Navigator.pop(context);
                },
              ),
            ],
          ),

          ExpansionTile(
            title: Text('Conversations'),
            trailing: Icon(Icons.keyboard_arrow_down),
            children: [
              ListTile(
                title: Text('Voir toutes les conversations'),
                onTap: () {
                  Navigator.pop(context);
                },
              ),
              ListTile(
                title: Text('Voir une conversation'),
                onTap: () {
                  Navigator.pop(context);
                },
              ),
              ListTile(
                title: Text('Créer une nouvelle conversation'),
                onTap: () {
                  Navigator.pop(context);
                },
              ),
              ListTile(
                title: Text('Supprimer une conversation'),
                onTap: () {
                  Navigator.pop(context);
                },
              ),
            ],
          ),
        ],
      ),
    );
  }
}
