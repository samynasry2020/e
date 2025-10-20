import 'package:flutter/foundation.dart';
import '../models/hardware_component.dart';

class PartsDatabaseProvider extends ChangeNotifier {
  List<HardwareComponent> _components = [];
  List<CompatibilityRule> _compatibilityRules = [];
  bool _isLoading = false;

  List<HardwareComponent> get components => _components;
  List<CompatibilityRule> get compatibilityRules => _compatibilityRules;
  bool get isLoading => _isLoading;

  PartsDatabaseProvider() {
    _initializeDatabase();
  }

  void _initializeDatabase() {
    _isLoading = true;
    notifyListeners();

    // Initialize with Z8 Fury AI workstation components based on the description
    _components = [
      // CPUs - Intel Xeon W processors
      HardwareComponent(
        id: 'cpu_xeon_w9_3495x',
        name: 'Intel Xeon W9-3495X',
        partNumber: 'PK8071305081500',
        type: ComponentType.cpu,
        manufacturer: 'Intel',
        specifications: {
          'cores': 56,
          'threads': 112,
          'baseClock': '1.9 GHz',
          'boostClock': '4.8 GHz',
          'socket': 'LGA4677',
          'tdp': '350W',
          'memorySupport': 'DDR5-4800',
          'maxMemory': '2TB',
          'pcieLanes': 112,
        },
        compatibleWith: ['motherboard_w790', 'ddr5_memory', 'rtx_6000_ada'],
        price: 5889.00,
        description: 'Extreme performance 56-core processor for AI workstations',
      ),
      HardwareComponent(
        id: 'cpu_xeon_w7_2495x',
        name: 'Intel Xeon W7-2495X',
        partNumber: 'PK8071305081400',
        type: ComponentType.cpu,
        manufacturer: 'Intel',
        specifications: {
          'cores': 24,
          'threads': 48,
          'baseClock': '2.5 GHz',
          'boostClock': '4.8 GHz',
          'socket': 'LGA4677',
          'tdp': '225W',
          'memorySupport': 'DDR5-4800',
          'maxMemory': '2TB',
          'pcieLanes': 64,
        },
        compatibleWith: ['motherboard_w790', 'ddr5_memory', 'rtx_6000_ada'],
        price: 2189.00,
        description: '24-core processor for high-performance workstations',
      ),

      // GPUs - NVIDIA RTX 6000 Ada Generation
      HardwareComponent(
        id: 'gpu_rtx_6000_ada',
        name: 'NVIDIA RTX 6000 Ada Generation',
        partNumber: '900-5G133-2550-000',
        type: ComponentType.gpu,
        manufacturer: 'NVIDIA',
        specifications: {
          'memory': '48GB GDDR6',
          'memoryBandwidth': '960 GB/s',
          'cudaCores': 18176,
          'rtCores': 142,
          'tensorCores': 568,
          'aiTops': 1457,
          'maxPower': '300W',
          'displayOutputs': '4x DisplayPort 1.4a',
          'nvlink': 'Yes',
        },
        compatibleWith: ['motherboard_w790', 'psu_2250w'],
        price: 6800.00,
        description: 'Professional GPU for AI, rendering, and simulation workloads',
      ),

      // Motherboards - W790 chipset
      HardwareComponent(
        id: 'motherboard_w790_pro',
        name: 'ASUS Pro WS W790-ACE',
        partNumber: '90MB1D10-M0EAY0',
        type: ComponentType.motherboard,
        manufacturer: 'ASUS',
        specifications: {
          'socket': 'LGA4677',
          'chipset': 'W790',
          'memorySlots': 8,
          'maxMemory': '2TB',
          'memoryType': 'DDR5',
          'pciSlots': '8x PCIe 5.0 x16',
          'storageSlots': '4x M.2 NVMe',
          'networking': '10GbE + 2.5GbE',
          'usb': 'USB 3.2 Gen2x2',
        },
        compatibleWith: ['cpu_xeon_w9_3495x', 'cpu_xeon_w7_2495x', 'ddr5_memory'],
        price: 1299.00,
        description: 'Professional workstation motherboard with W790 chipset',
      ),

      // Memory - DDR5 modules
      HardwareComponent(
        id: 'memory_ddr5_64gb',
        name: 'Kingston Server Premier DDR5-4800 64GB',
        partNumber: 'KSM48E40BD8KM-64HM',
        type: ComponentType.memory,
        manufacturer: 'Kingston',
        specifications: {
          'capacity': '64GB',
          'type': 'DDR5',
          'speed': '4800 MHz',
          'ecc': 'Yes',
          'registered': 'Yes',
          'voltage': '1.1V',
        },
        compatibleWith: ['motherboard_w790_pro', 'cpu_xeon_w9_3495x'],
        price: 899.00,
        description: 'ECC registered DDR5 memory for workstations',
      ),

      // Storage - NVMe SSDs
      HardwareComponent(
        id: 'storage_nvme_8tb',
        name: 'Samsung 980 PRO 8TB NVMe SSD',
        partNumber: 'MZ-V8P8T0BW',
        type: ComponentType.storage,
        manufacturer: 'Samsung',
        specifications: {
          'capacity': '8TB',
          'interface': 'PCIe 4.0 x4',
          'formFactor': 'M.2 2280',
          'readSpeed': '7000 MB/s',
          'writeSpeed': '6900 MB/s',
          'endurance': '4400 TBW',
        },
        compatibleWith: ['motherboard_w790_pro'],
        price: 1199.00,
        description: 'High-performance NVMe SSD for professional workloads',
      ),

      // Power Supply
      HardwareComponent(
        id: 'psu_2250w',
        name: 'Corsair AX2250 Titanium',
        partNumber: 'CP-9020228-NA',
        type: ComponentType.powerSupply,
        manufacturer: 'Corsair',
        specifications: {
          'wattage': '2250W',
          'efficiency': '80+ Titanium',
          'modular': 'Fully Modular',
          'pciConnectors': '8x PCIe 8-pin',
          'rails': '+12V single rail',
        },
        compatibleWith: ['gpu_rtx_6000_ada', 'cpu_xeon_w9_3495x'],
        price: 899.00,
        description: 'High-wattage PSU for multi-GPU workstations',
      ),

      // Cooling
      HardwareComponent(
        id: 'cooling_liquid_360',
        name: 'Noctua NH-U14S DX-4677',
        partNumber: 'NH-U14S-DX-4677',
        type: ComponentType.cooling,
        manufacturer: 'Noctua',
        specifications: {
          'type': 'Air Cooler',
          'socket': 'LGA4677',
          'fanSize': '140mm',
          'tdpRating': '400W',
          'height': '165mm',
        },
        compatibleWith: ['cpu_xeon_w9_3495x', 'cpu_xeon_w7_2495x'],
        price: 149.00,
        description: 'High-performance CPU cooler for Xeon W processors',
      ),
    ];

    // Initialize compatibility rules
    _compatibilityRules = [
      CompatibilityRule(
        id: 'cpu_motherboard_socket',
        sourceType: ComponentType.cpu,
        targetType: ComponentType.motherboard,
        rule: 'socket_match',
        description: 'CPU and motherboard must have matching socket types',
        isRequired: true,
      ),
      CompatibilityRule(
        id: 'memory_motherboard_type',
        sourceType: ComponentType.memory,
        targetType: ComponentType.motherboard,
        rule: 'memory_type_match',
        description: 'Memory type must be supported by motherboard',
        isRequired: true,
      ),
      CompatibilityRule(
        id: 'gpu_power_requirement',
        sourceType: ComponentType.gpu,
        targetType: ComponentType.powerSupply,
        rule: 'power_sufficient',
        description: 'Power supply must provide sufficient wattage for GPU(s)',
        isRequired: true,
      ),
    ];

    _isLoading = false;
    notifyListeners();
  }

  List<HardwareComponent> getComponentsByType(ComponentType type) {
    return _components.where((component) => component.type == type).toList();
  }

  HardwareComponent? getComponentById(String id) {
    try {
      return _components.firstWhere((component) => component.id == id);
    } catch (e) {
      return null;
    }
  }

  List<HardwareComponent> searchComponents(String query) {
    if (query.isEmpty) return _components;
    
    query = query.toLowerCase();
    return _components.where((component) {
      return component.name.toLowerCase().contains(query) ||
             component.partNumber.toLowerCase().contains(query) ||
             component.manufacturer.toLowerCase().contains(query) ||
             component.description.toLowerCase().contains(query);
    }).toList();
  }

  void addComponent(HardwareComponent component) {
    _components.add(component);
    notifyListeners();
  }

  void updateComponent(HardwareComponent component) {
    final index = _components.indexWhere((c) => c.id == component.id);
    if (index != -1) {
      _components[index] = component;
      notifyListeners();
    }
  }

  void removeComponent(String id) {
    _components.removeWhere((component) => component.id == id);
    notifyListeners();
  }
}