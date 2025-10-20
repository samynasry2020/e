import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'screens/home_screen.dart';
import 'providers/compatibility_provider.dart';
import 'providers/parts_database_provider.dart';

void main() {
  runApp(HardwareCompatibilityGPT());
}

class HardwareCompatibilityGPT extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => CompatibilityProvider()),
        ChangeNotifierProvider(create: (_) => PartsDatabaseProvider()),
      ],
      child: MaterialApp(
        title: 'Hardware Compatibility GPT',
        theme: ThemeData(
          primarySwatch: Colors.blue,
          visualDensity: VisualDensity.adaptivePlatformDensity,
          appBarTheme: AppBarTheme(
            backgroundColor: Colors.blue[800],
            foregroundColor: Colors.white,
            elevation: 2,
          ),
          cardTheme: CardTheme(
            elevation: 4,
            margin: EdgeInsets.all(8),
          ),
        ),
        home: HomeScreen(),
        debugShowCheckedModeBanner: false,
      ),
    );
  }
}
