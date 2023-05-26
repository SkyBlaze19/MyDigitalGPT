import 'package:flutter/material.dart';

class ShowAllUser extends StatefulWidget {
  

  @override
  _ShowAllUserState createState() => _ShowAllUserState();
}

class _ShowAllUserState extends State<ShowAllUser> {
  String? token;

  @override
  void initState() {
    super.initState();
    //token = widget.authToken;
  }

  @override
  Widget build(BuildContext context) {
    // Utilisez la valeur de _token dans votre interface utilisateur
    // ...

    return Scaffold(
        appBar: AppBar(
          title: Text('Liste des utilisateurs'),
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
          /*actions: [
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
          ],*/
        ),
        body: Center(
            child: const Text('Liste des users à venir !'),
        ),

        
      );
  }
}