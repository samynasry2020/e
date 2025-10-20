#!/usr/bin/env python3
"""
Test script for Hardware Compatibility Validator
Run this to test the validation logic with sample descriptions
"""

import json
from validation_logic import HardwareValidator, CompatibilityStatus

def test_hp_z8_fury_description():
    """Test with HP Z8 Fury workstation description"""
    print("=" * 60)
    print("TEST 1: HP Z8 Fury Workstation Description")
    print("=" * 60)
    
    description = """
    Extreme performance. Infinite possibilities
    Experience a new echelon in high performance. Transformative single socket technology 
    delivers extreme performance with up to 56 cores in a single CPU, to also unleash the 
    power of up to 4 high end GPUs. Now you can breeze through even the most complex deep 
    learning, virtual production, and VFX.
    
    Relentless power. Extraordinary expandability
    Tackle the most complex workflows with up to a 60 core Intel® Xeon® W CPU, up to 4 
    high-end GPUs, 2TB DDR5 RAM, 120TB storage and 2,250W of power.
    """
    
    validator = HardwareValidator('hardware_database.json', 'compatibility_rules.json')
    report = validator.generate_report(description)
    print(report)
    return report

def test_specific_build():
    """Test with specific component list"""
    print("\n" + "=" * 60)
    print("TEST 2: Specific Component Build")
    print("=" * 60)
    
    description = """
    Build with:
    - Intel Xeon W9-3495X processor
    - 4x NVIDIA RTX 6000 Ada Generation
    - 512GB DDR5 RAM
    - 50TB NVMe storage
    - 1600W power supply
    """
    
    validator = HardwareValidator('hardware_database.json', 'compatibility_rules.json')
    report = validator.generate_report(description)
    print(report)
    return report

def test_arabic_input():
    """Test with Arabic input"""
    print("\n" + "=" * 60)
    print("TEST 3: Arabic Input")
    print("=" * 60)
    
    description = """
    عايز ابني workstation فيه:
    60 core Xeon processor
    RTX 6000 graphics
    2TB memory
    50TB storage
    """
    
    validator = HardwareValidator('hardware_database.json', 'compatibility_rules.json')
    report = validator.generate_report(description)
    print(report)
    return report

def test_component_extraction():
    """Test component extraction functionality"""
    print("\n" + "=" * 60)
    print("TEST 4: Component Extraction")
    print("=" * 60)
    
    validator = HardwareValidator('hardware_database.json', 'compatibility_rules.json')
    
    test_cases = [
        "56 core Intel Xeon W CPU",
        "4x NVIDIA RTX 6000 Ada",
        "2TB DDR5 RAM",
        "120TB storage",
        "2250W power supply"
    ]
    
    for test in test_cases:
        components = validator.parse_description(test)
        print(f"\nInput: {test}")
        for comp in components:
            print(f"  Found: {comp.category} - {comp.model}")
            if comp.part_number:
                print(f"    Part Number: {comp.part_number}")

def test_validation_rules():
    """Test validation rules directly"""
    print("\n" + "=" * 60)
    print("TEST 5: Validation Rules")
    print("=" * 60)
    
    validator = HardwareValidator('hardware_database.json', 'compatibility_rules.json')
    
    # Create test components
    from validation_logic import Component
    
    test_components = [
        Component(
            category='CPU',
            model='Intel Xeon W9-3495X',
            part_number='W9-3495X',
            specifications={'cores': 56, 'tdp': 420, 'socket': 'LGA-4677', 'pcie_lanes': 112}
        ),
        Component(
            category='GPU',
            model='RTX 6000 Ada',
            part_number='L40S',
            specifications={'tdp': 300, 'vram': '48GB'}
        ),
        Component(
            category='GPU',
            model='RTX 6000 Ada',
            part_number='L40S',
            specifications={'tdp': 300, 'vram': '48GB'}
        ),
        Component(
            category='Memory',
            model='64GB DDR5 RDIMM',
            part_number='KSM48R40BD4TMM-64HAM',
            specifications={'type': 'DDR5 RDIMM', 'capacity': '512GB', 'modules': 8}
        )
    ]
    
    results = validator.validate_compatibility(test_components)
    
    print("\nValidation Results:")
    for check, result in results.items():
        print(f"\n{check}:")
        print(f"  Status: {result.status.value}")
        print(f"  Message: {result.message}")
        if result.details:
            print(f"  Details: {result.details}")

def test_missing_components():
    """Test missing component detection"""
    print("\n" + "=" * 60)
    print("TEST 6: Missing Component Detection")
    print("=" * 60)
    
    validator = HardwareValidator('hardware_database.json', 'compatibility_rules.json')
    
    # Partial build
    description = """
    Intel Xeon W 56 cores
    4x RTX 6000
    1TB DDR5 memory
    """
    
    components = validator.parse_description(description)
    missing = validator.identify_missing_components(components)
    
    print("Provided Components:")
    for comp in components:
        print(f"  - {comp.category}: {comp.model}")
    
    print("\nMissing Components:")
    for item in missing:
        print(f"  - {item['component']}: {item['recommendation']}")
        print(f"    Part Numbers: {', '.join(item['part_numbers'])}")

def run_all_tests():
    """Run all test cases"""
    print("🚀 Running Hardware Compatibility Validator Tests\n")
    
    try:
        # Run all tests
        test_hp_z8_fury_description()
        test_specific_build()
        test_arabic_input()
        test_component_extraction()
        test_validation_rules()
        test_missing_components()
        
        print("\n" + "=" * 60)
        print("✅ All tests completed successfully!")
        print("=" * 60)
        
    except Exception as e:
        print(f"\n❌ Test failed with error: {e}")
        import traceback
        traceback.print_exc()

if __name__ == "__main__":
    run_all_tests()