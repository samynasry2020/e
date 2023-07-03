import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

void main() {
  runApp(MultiStepsApp());
}

class MultiStepsApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Company Registration App',
      home: Scaffold(
        appBar: AppBar(
          title: Text('Company Registration'),
        ),
        body: Center(
          child: Step1(),
        ),
      ),
    );
  }
}

class Step1 extends StatelessWidget {
  final TextEditingController _searchController = TextEditingController();

  Future<bool> _checkCompanyNameAvailability(String companyName) async {
    final apiKey = '52456da5-f410-4235-b4bf-930dd4bc9c48';
    final url =
        'https://api.companieshouse.gov.uk/search/companies?q=$companyName&items_per_page=1&access_token=$apiKey';

    final response = await http.get(Uri.parse(url));

    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      final totalResults = data['total_results'];
      return totalResults == 0;
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
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (context) => Step2()),
                  );
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
    return Padding(
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
    );
  }
}

class Step2 extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        // Personal Information Form Widget
        // ...
        // Continue button
        ElevatedButton(
          onPressed: () {
            // Move to the next step (Step3)
            Navigator.push(
              context,
              MaterialPageRoute(builder: (context) => Step3()),
            );
          },
          child: Text('Continue'),
        ),
      ],
    );
  }
}

class Step3 extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        // Partner Selection and Information Form Widget
        // ...
        // Continue button
        ElevatedButton(
          onPressed: () {
            // Move to the next step (Step4)
            Navigator.push(
              context,
              MaterialPageRoute(builder: (context) => Step4()),
            );
          },
          child: Text('Continue'),
        ),
      ],
    );
  }
}

class Step4 extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        // Company Activity Selection Widget
        // ...
        // Continue button
        ElevatedButton(
          onPressed: () {
            // Move to the next step (Step5)
            Navigator.push(
              context,
              MaterialPageRoute(builder: (context) => Step5()),
            );
          },
          child: Text('Continue'),
        ),
      ],
    );
  }
}

class Step5 extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        // Payment Method Selection Widget
        // ...
        // Continue button
        ElevatedButton(
          onPressed: () {
            // Move to the next step (Step6)
            Navigator.push(
              context,
              MaterialPageRoute(builder: (context) => Step6()),
            );
          },
          child: Text('Continue'),
        ),
      ],
    );
  }
}

class Step6 extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        // Payment Page Widget
        // ...
        // Payment button
        ElevatedButton(
          onPressed: () {
            // Perform payment
            // ...
          },
          child: Text('Pay'),
        ),
      ],
    );
  }
}
