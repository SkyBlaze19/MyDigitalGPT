import 'package:flutter/material.dart';
import 'package:mydigitalgpt/connected.dart';
import 'package:mydigitalgpt/nav_menu.dart';

class ShowAllUser extends StatefulWidget {
  String? token;
  final String username;

  /*
  @override
  void initState() {
    super.initState();
    username = widget
        .username; // Ajoutez cette ligne pour récupérer le nom d'utilisateur
  }
  */
  ShowAllUser({required this.username});

  @override
  _ShowAllUserState createState() => _ShowAllUserState();
}

class _ShowAllUserState extends State<ShowAllUser> {
  String? token;
  String? username;

  @override
  void initState() {
    super.initState();
    username = widget.username;
    //token = widget.authToken;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Row(
          children: [
            Text('Liste des utilisateurs'),
            Spacer(), // Ajoute un espace flexible
          ],
        ),
        leading: Builder(
          builder: (BuildContext context) {
            return IconButton(
              icon: Icon(Icons.arrow_back),
              onPressed: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                      builder: (context) => Connected(
                            username: username,
                          )),
                );
              },
            );
          },
        ),
        actions: [
          Builder(
            builder: (BuildContext context) {
              return IconButton(
                icon: Icon(Icons.menu),
                onPressed: () {
                  Scaffold.of(context).openDrawer();
                },
              );
            },
          ),
        ],
      ),
      drawer: AppDrawer(
        currentPage: 'showAllUsers',
        username: username!,
      ),
      body: Center(
        child: const Text('Liste des utilisateurs'),
      ),
    );
  }
}
