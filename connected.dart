import 'package:flutter/material.dart';
import 'package:mydigitalgpt/create_user.dart' as CreateUserPage;
import 'package:mydigitalgpt/login.dart';
import 'package:mydigitalgpt/nav_menu.dart';
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

  /* A garder pour le moment, était ce utile / utilisé ? 
  (étant donné que je ne sais pas je garde) 

  IconData _utilisateursIcon = Icons.keyboard_arrow_down;
  bool _utilisateursExpanded = false;

  IconData _universIcon = Icons.keyboard_arrow_down;
  bool _universExpanded = false;

  IconData _personnagesIcon = Icons.keyboard_arrow_down;
  bool _personnagesExpanded = false;

  IconData _conversationsIcon = Icons.keyboard_arrow_down;
  bool _conversationsExpanded = false;
*/

  @override
  void initState() {
    super.initState();
    _token = widget.authToken;
  }

  Widget build(BuildContext context) {
    String? authToken = widget.authToken;
    String? username = widget.username;
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
                widget.passwordController?.clear();
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
                  MaterialPageRoute(builder: (context) => LoginPage()),
                );
              },
            ),
          ),
        ],
      ),
      drawer: AppDrawer(
        currentPage: 'connected',
        username: widget.username ?? '',
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
