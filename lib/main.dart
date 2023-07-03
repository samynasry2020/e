import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

void main() {
  runApp(MyApp());
}

class MyApp extends StatelessWidget {
  final TextEditingController _searchController = TextEditingController();
  final apiKey = '52456da5-f410-4235-b4bf-930dd4bc9c48';

  Future<bool> _checkCompanyNameAvailability(String companyName) async {
    final url =
        'https://api.companieshouse.gov.uk/search/companies?q=$companyName&items_per_page=1&access_token=$apiKey';

    final response = await http.get(Uri.parse(url));

    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      final items = data['items'];
      return items.isEmpty;
    } else {
      throw Exception('Failed to check company name availability');
    }
  }

  void _showCompanyNameAvailability(BuildContext context) async {
    final companyName = _searchController.text.trim();

    if (companyName.isNotEmpty) {
      final isAvailable = await _checkCompanyNameAvailability(companyName);

      showDialog(
        context: context,
        builder: (BuildContext context) {
          return AlertDialog(
            title: Text('Company Name Availability'),
            content: Text(
              isAvailable
                  ? 'Congratulations! The company name is available for registration.'
                  : 'Sorry, the company name is not available for registration.',
            ),
            actions: <Widget>[
              TextButton(
                child: Text('OK'),
                onPressed: () {
                  Navigator.of(context).pop();
                },
              ),
            ],
          );
        },
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Company Search',
      home: Scaffold(
        appBar: AppBar(
          title: Text('Company Search'),
        ),
        body: Padding(
          padding: EdgeInsets.all(16.0),
          child: Column(
            children: [
              TextField(
                controller: _searchController,
                decoration: InputDecoration(
                  labelText: 'Enter Company Name',
                ),
              ),
              SizedBox(height: 16.0),
              ElevatedButton(
                child: Text('Check Availability'),
                onPressed: () => _showCompanyNameAvailability(context),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
