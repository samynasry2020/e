class HardwareComponent {
  final String id;
  final String name;
  final String partNumber;
  final ComponentType type;
  final String manufacturer;
  final Map<String, dynamic> specifications;
  final List<String> compatibleWith;
  final List<String> incompatibleWith;
  final double price;
  final String availability;
  final String description;

  HardwareComponent({
    required this.id,
    required this.name,
    required this.partNumber,
    required this.type,
    required this.manufacturer,
    required this.specifications,
    this.compatibleWith = const [],
    this.incompatibleWith = const [],
    this.price = 0.0,
    this.availability = 'Available',
    this.description = '',
  });

  factory HardwareComponent.fromJson(Map<String, dynamic> json) {
    return HardwareComponent(
      id: json['id'] ?? '',
      name: json['name'] ?? '',
      partNumber: json['partNumber'] ?? '',
      type: ComponentType.values.firstWhere(
        (e) => e.toString().split('.').last == json['type'],
        orElse: () => ComponentType.other,
      ),
      manufacturer: json['manufacturer'] ?? '',
      specifications: json['specifications'] ?? {},
      compatibleWith: List<String>.from(json['compatibleWith'] ?? []),
      incompatibleWith: List<String>.from(json['incompatibleWith'] ?? []),
      price: (json['price'] ?? 0.0).toDouble(),
      availability: json['availability'] ?? 'Available',
      description: json['description'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'partNumber': partNumber,
      'type': type.toString().split('.').last,
      'manufacturer': manufacturer,
      'specifications': specifications,
      'compatibleWith': compatibleWith,
      'incompatibleWith': incompatibleWith,
      'price': price,
      'availability': availability,
      'description': description,
    };
  }
}

enum ComponentType {
  cpu,
  gpu,
  motherboard,
  memory,
  storage,
  powerSupply,
  cooling,
  case,
  networking,
  other,
}

class CompatibilityRule {
  final String id;
  final ComponentType sourceType;
  final ComponentType targetType;
  final String rule;
  final String description;
  final bool isRequired;

  CompatibilityRule({
    required this.id,
    required this.sourceType,
    required this.targetType,
    required this.rule,
    required this.description,
    this.isRequired = false,
  });
}

class SystemConfiguration {
  final String id;
  final String name;
  final String description;
  final List<HardwareComponent> components;
  final List<String> missingComponents;
  final List<CompatibilityIssue> compatibilityIssues;
  final double totalPrice;
  final int totalPower;

  SystemConfiguration({
    required this.id,
    required this.name,
    this.description = '',
    this.components = const [],
    this.missingComponents = const [],
    this.compatibilityIssues = const [],
    this.totalPrice = 0.0,
    this.totalPower = 0,
  });
}

class CompatibilityIssue {
  final String id;
  final String component1;
  final String component2;
  final String issue;
  final String severity;
  final String recommendation;

  CompatibilityIssue({
    required this.id,
    required this.component1,
    required this.component2,
    required this.issue,
    required this.severity,
    required this.recommendation,
  });
}