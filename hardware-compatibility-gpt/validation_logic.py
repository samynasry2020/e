"""
Hardware Compatibility Validation Logic
This module provides functions to validate PC hardware compatibility
"""

import json
from typing import Dict, List, Tuple, Optional
from dataclasses import dataclass
from enum import Enum

class CompatibilityStatus(Enum):
    COMPATIBLE = "✅ Compatible"
    WARNING = "⚠️ Warning"
    INCOMPATIBLE = "❌ Incompatible"
    MISSING = "🔍 Missing"

@dataclass
class Component:
    """Represents a hardware component"""
    category: str
    model: str
    part_number: Optional[str]
    specifications: Dict

@dataclass
class ValidationResult:
    """Result of compatibility validation"""
    status: CompatibilityStatus
    message: str
    details: Optional[str] = None

class HardwareValidator:
    """Main validation class for hardware compatibility"""
    
    def __init__(self, hardware_db_path: str, rules_path: str):
        """Initialize validator with database and rules"""
        with open(hardware_db_path, 'r') as f:
            self.hardware_db = json.load(f)
        with open(rules_path, 'r') as f:
            self.rules = json.load(f)
    
    def parse_description(self, description: str) -> List[Component]:
        """
        Parse hardware description and extract components
        
        Args:
            description: Text description of hardware
            
        Returns:
            List of identified components
        """
        components = []
        
        # Parse CPU
        if "xeon" in description.lower() or "core" in description.lower():
            cpu_info = self._extract_cpu(description)
            if cpu_info:
                components.append(cpu_info)
        
        # Parse GPU
        if "rtx" in description.lower() or "gpu" in description.lower():
            gpu_info = self._extract_gpu(description)
            components.extend(gpu_info)
        
        # Parse Memory
        if "ddr" in description.lower() or "ram" in description.lower():
            memory_info = self._extract_memory(description)
            if memory_info:
                components.append(memory_info)
        
        # Parse Storage
        if "tb" in description.lower() or "storage" in description.lower():
            storage_info = self._extract_storage(description)
            components.extend(storage_info)
        
        # Parse PSU
        if "w " in description.lower() or "watt" in description.lower():
            psu_info = self._extract_psu(description)
            if psu_info:
                components.append(psu_info)
        
        return components
    
    def _extract_cpu(self, description: str) -> Optional[Component]:
        """Extract CPU information from description"""
        import re
        
        # Look for Intel Xeon W processors
        xeon_pattern = r"(\d+)\s*core.*?xeon\s+w|xeon\s+w.*?(\d+)\s*core"
        match = re.search(xeon_pattern, description.lower())
        
        if match:
            cores = int(match.group(1) or match.group(2))
            
            # Find matching CPU in database
            for model, specs in self.hardware_db['processors']['intel_xeon_w'].items():
                if specs['cores'] == cores:
                    return Component(
                        category='CPU',
                        model=f"Intel Xeon {model}",
                        part_number=model,
                        specifications=specs
                    )
                elif abs(specs['cores'] - cores) <= 4:  # Close match
                    return Component(
                        category='CPU',
                        model=f"Intel Xeon {model} (closest match)",
                        part_number=model,
                        specifications=specs
                    )
        
        return None
    
    def _extract_gpu(self, description: str) -> List[Component]:
        """Extract GPU information from description"""
        import re
        
        gpus = []
        
        # Look for NVIDIA RTX cards
        rtx_pattern = r"(\d+)?\s*(?:x\s*)?(?:nvidia\s*)?rtx\s*(\d+)"
        matches = re.findall(rtx_pattern, description.lower())
        
        for match in matches:
            count = int(match[0]) if match[0] else 1
            model_num = match[1]
            
            # Find matching GPU in database
            for gpu_model, specs in self.hardware_db['gpus']['nvidia_rtx'].items():
                if model_num in gpu_model.lower().replace(' ', ''):
                    for _ in range(count):
                        gpus.append(Component(
                            category='GPU',
                            model=gpu_model,
                            part_number=specs['part_number'],
                            specifications=specs
                        ))
                    break
        
        # Special case for "4 high-end GPUs"
        if "4 high" in description.lower() and "gpu" in description.lower():
            # Default to RTX 6000 Ada for high-end
            specs = self.hardware_db['gpus']['nvidia_rtx']['RTX 6000 Ada']
            for _ in range(4):
                gpus.append(Component(
                    category='GPU',
                    model='RTX 6000 Ada',
                    part_number=specs['part_number'],
                    specifications=specs
                ))
        
        return gpus
    
    def _extract_memory(self, description: str) -> Optional[Component]:
        """Extract memory information from description"""
        import re
        
        # Look for memory specifications
        memory_pattern = r"(\d+)\s*(?:tb|gb)\s*(?:ddr\d+)?\s*(?:ram|memory)"
        match = re.search(memory_pattern, description.lower())
        
        if match:
            capacity = match.group(1)
            unit = "TB" if "tb" in match.group(0) else "GB"
            
            # Convert to GB for consistency
            total_gb = int(capacity) * 1024 if unit == "TB" else int(capacity)
            
            # Recommend appropriate memory configuration
            if total_gb >= 2048:  # 2TB
                modules = 32  # 32x 64GB modules
                per_module = 64
            elif total_gb >= 1024:  # 1TB
                modules = 16  # 16x 64GB modules
                per_module = 64
            elif total_gb >= 512:
                modules = 8  # 8x 64GB modules
                per_module = 64
            else:
                modules = total_gb // 64
                per_module = 64
            
            # Get specs from database
            memory_model = list(self.hardware_db['memory']['ddr5_rdimm'].keys())[0]
            specs = self.hardware_db['memory']['ddr5_rdimm'][memory_model]
            
            return Component(
                category='Memory',
                model=f"{modules}x {per_module}GB DDR5 RDIMM",
                part_number=f"{modules}x {memory_model}",
                specifications={
                    **specs,
                    'total_capacity': f"{total_gb}GB",
                    'modules': modules
                }
            )
        
        return None
    
    def _extract_storage(self, description: str) -> List[Component]:
        """Extract storage information from description"""
        import re
        
        storage = []
        
        # Look for storage capacity
        storage_pattern = r"(\d+)\s*tb\s*storage"
        match = re.search(storage_pattern, description.lower())
        
        if match:
            total_tb = int(match.group(1))
            
            # Recommend storage configuration
            if total_tb >= 100:
                # Use enterprise drives for large capacity
                num_drives = total_tb // 30
                drive_model = "Samsung PM1743"
                drive_capacity = "30.72TB"
            elif total_tb >= 50:
                num_drives = total_tb // 15
                drive_model = "Samsung PM1743"
                drive_capacity = "15.36TB"
            else:
                num_drives = total_tb // 4
                drive_model = "Samsung 990 PRO"
                drive_capacity = "4TB"
            
            for i in range(num_drives):
                storage.append(Component(
                    category='Storage',
                    model=f"{drive_model} {drive_capacity}",
                    part_number=f"{drive_model}-{drive_capacity}",
                    specifications=self.hardware_db['storage']['nvme'].get(
                        drive_model, 
                        self.hardware_db['storage']['nvme']['Samsung 990 PRO']
                    )
                ))
        
        # Always add boot drive if not present
        if not storage or all(s.specifications.get('type') == 'Enterprise' for s in storage):
            storage.insert(0, Component(
                category='Storage',
                model='Samsung 990 PRO 2TB (Boot)',
                part_number='MZ-V9P2T0B',
                specifications=self.hardware_db['storage']['nvme']['Samsung 990 PRO']
            ))
        
        return storage
    
    def _extract_psu(self, description: str) -> Optional[Component]:
        """Extract PSU information from description"""
        import re
        
        # Look for power specifications
        power_pattern = r"(\d+)\s*w(?:atts?)?"
        match = re.search(power_pattern, description.lower())
        
        if match:
            wattage = int(match.group(1))
            
            # Find appropriate PSU
            for psu_model, specs in self.hardware_db['power_supplies']['workstation'].items():
                if specs['wattage'] >= wattage:
                    return Component(
                        category='PSU',
                        model=psu_model,
                        part_number=specs.get('part_number', psu_model),
                        specifications=specs
                    )
        
        return None
    
    def validate_compatibility(self, components: List[Component]) -> Dict[str, ValidationResult]:
        """
        Validate compatibility between components
        
        Args:
            components: List of components to validate
            
        Returns:
            Dictionary of validation results
        """
        results = {}
        
        # Group components by category
        component_dict = {}
        for comp in components:
            if comp.category not in component_dict:
                component_dict[comp.category] = []
            component_dict[comp.category].append(comp)
        
        # Check CPU-Motherboard compatibility
        if 'CPU' in component_dict:
            cpu = component_dict['CPU'][0]
            if 'Motherboard' not in component_dict:
                results['CPU-Motherboard'] = ValidationResult(
                    status=CompatibilityStatus.MISSING,
                    message="Motherboard required for CPU",
                    details=f"Recommended: ASUS Pro WS W790-ACE for {cpu.model}"
                )
            else:
                mb = component_dict['Motherboard'][0]
                if cpu.specifications['socket'] == mb.specifications['socket']:
                    results['CPU-Motherboard'] = ValidationResult(
                        status=CompatibilityStatus.COMPATIBLE,
                        message=f"{cpu.model} compatible with {mb.model}"
                    )
                else:
                    results['CPU-Motherboard'] = ValidationResult(
                        status=CompatibilityStatus.INCOMPATIBLE,
                        message=f"Socket mismatch: {cpu.specifications['socket']} vs {mb.specifications['socket']}"
                    )
        
        # Check power requirements
        total_power = self._calculate_power_requirement(component_dict)
        if 'PSU' in component_dict:
            psu = component_dict['PSU'][0]
            if psu.specifications['wattage'] >= total_power * 1.2:
                results['Power'] = ValidationResult(
                    status=CompatibilityStatus.COMPATIBLE,
                    message=f"PSU {psu.specifications['wattage']}W sufficient for {total_power}W system"
                )
            else:
                results['Power'] = ValidationResult(
                    status=CompatibilityStatus.WARNING,
                    message=f"PSU may be insufficient: {psu.specifications['wattage']}W for {total_power}W system",
                    details=f"Recommend at least {int(total_power * 1.2)}W PSU"
                )
        else:
            results['Power'] = ValidationResult(
                status=CompatibilityStatus.MISSING,
                message=f"Power supply required",
                details=f"System requires approximately {int(total_power * 1.2)}W PSU"
            )
        
        # Check memory compatibility
        if 'Memory' in component_dict:
            memory = component_dict['Memory'][0]
            if 'Motherboard' in component_dict:
                mb = component_dict['Motherboard'][0]
                if 'DDR5' in memory.specifications.get('type', '') and 'DDR5' in mb.specifications.get('memory_type', ''):
                    results['Memory'] = ValidationResult(
                        status=CompatibilityStatus.COMPATIBLE,
                        message=f"Memory compatible with motherboard"
                    )
        
        # Check GPU compatibility
        if 'GPU' in component_dict:
            gpu_count = len(component_dict['GPU'])
            if 'Motherboard' in component_dict:
                mb = component_dict['Motherboard'][0]
                available_slots = mb.specifications['pcie_slots'].get('x16', 0)
                if gpu_count <= available_slots:
                    results['GPU-Slots'] = ValidationResult(
                        status=CompatibilityStatus.COMPATIBLE,
                        message=f"{gpu_count} GPUs fit in {available_slots} available PCIe x16 slots"
                    )
                else:
                    results['GPU-Slots'] = ValidationResult(
                        status=CompatibilityStatus.INCOMPATIBLE,
                        message=f"Not enough PCIe slots: {gpu_count} GPUs need {available_slots} available"
                    )
        
        return results
    
    def _calculate_power_requirement(self, component_dict: Dict[str, List[Component]]) -> int:
        """Calculate total system power requirement"""
        total = 150  # Base system power
        
        if 'CPU' in component_dict:
            total += component_dict['CPU'][0].specifications.get('tdp', 0)
        
        if 'GPU' in component_dict:
            for gpu in component_dict['GPU']:
                total += gpu.specifications.get('tdp', 0)
        
        # Add 10% for drives and other components
        total = int(total * 1.1)
        
        return total
    
    def identify_missing_components(self, components: List[Component]) -> List[Dict]:
        """Identify missing essential components"""
        missing = []
        
        component_categories = {comp.category for comp in components}
        required = self.rules['required_components']['essential']
        
        for req in required:
            category = req.split(' ')[0]  # Get first word as category
            if category not in component_categories:
                # Provide recommendations
                if category == 'Motherboard':
                    missing.append({
                        'component': 'Motherboard',
                        'recommendation': 'ASUS Pro WS W790-ACE or HP Z8 Fury G5 motherboard',
                        'part_numbers': ['90MB1CR0-M0EAY0', 'HP-Z8-G5-MB']
                    })
                elif category == 'Case':
                    missing.append({
                        'component': 'Case',
                        'recommendation': 'HP Z8 Fury G5 Chassis or Corsair 7000D Airflow',
                        'part_numbers': ['HP-Z8-CHASSIS', 'CC-9011218-WW']
                    })
                elif category == 'CPU' and category not in component_categories:
                    missing.append({
                        'component': 'CPU Cooling',
                        'recommendation': 'Noctua NH-U14S DX-4677 or Arctic Liquid Freezer II 420',
                        'part_numbers': ['NH-U14S-DX-4677', 'ACFRE00132A']
                    })
        
        return missing
    
    def generate_report(self, description: str) -> str:
        """
        Generate complete compatibility report
        
        Args:
            description: Hardware description text
            
        Returns:
            Formatted compatibility report
        """
        # Parse components
        components = self.parse_description(description)
        
        # Validate compatibility
        validation_results = self.validate_compatibility(components)
        
        # Identify missing components
        missing = self.identify_missing_components(components)
        
        # Calculate system requirements
        component_dict = {}
        for comp in components:
            if comp.category not in component_dict:
                component_dict[comp.category] = []
            component_dict[comp.category].append(comp)
        
        total_power = self._calculate_power_requirement(component_dict)
        
        # Generate report
        report = "# 🖥️ Hardware Compatibility Report\n\n"
        
        # Identified Components
        report += "## 📋 Identified Components\n\n"
        for comp in components:
            report += f"### {comp.category}\n"
            report += f"- **Model:** {comp.model}\n"
            if comp.part_number:
                report += f"- **Part Number:** {comp.part_number}\n"
            report += f"- **Key Specs:** {self._format_specs(comp.specifications)}\n\n"
        
        # Compatibility Status
        report += "## ✅ Compatibility Validation\n\n"
        for check, result in validation_results.items():
            report += f"### {check}\n"
            report += f"- **Status:** {result.status.value}\n"
            report += f"- **Message:** {result.message}\n"
            if result.details:
                report += f"- **Details:** {result.details}\n"
            report += "\n"
        
        # Missing Components
        if missing:
            report += "## 🔍 Missing Components\n\n"
            for item in missing:
                report += f"### {item['component']}\n"
                report += f"- **Recommendation:** {item['recommendation']}\n"
                report += f"- **Part Numbers:** {', '.join(item['part_numbers'])}\n\n"
        
        # System Requirements
        report += "## ⚡ System Requirements\n\n"
        report += f"- **Estimated Power Draw:** {total_power}W\n"
        report += f"- **Recommended PSU:** {int(total_power * 1.2)}W minimum\n"
        report += f"- **Cooling Requirements:** High-performance cooling required for Xeon W\n"
        report += f"- **Case Requirements:** Full tower or workstation chassis recommended\n\n"
        
        # Recommendations
        report += "## 💡 Optimization Recommendations\n\n"
        report += "1. **Memory Configuration:** Use all 8 memory channels for optimal performance\n"
        report += "2. **Storage:** Consider RAID configuration for data redundancy\n"
        report += "3. **Cooling:** Liquid cooling recommended for sustained workloads\n"
        report += "4. **Power:** Consider UPS for system protection\n"
        report += "5. **Networking:** 10GbE recommended for large data transfers\n"
        
        return report
    
    def _format_specs(self, specs: Dict) -> str:
        """Format specifications for display"""
        important_specs = []
        
        if 'cores' in specs:
            important_specs.append(f"{specs['cores']} cores")
        if 'tdp' in specs:
            important_specs.append(f"{specs['tdp']}W TDP")
        if 'vram' in specs:
            important_specs.append(f"{specs['vram']} VRAM")
        if 'capacity' in specs:
            important_specs.append(specs['capacity'])
        if 'speed' in specs:
            important_specs.append(specs['speed'])
        if 'wattage' in specs:
            important_specs.append(f"{specs['wattage']}W")
        
        return ', '.join(important_specs)


# Example usage
if __name__ == "__main__":
    # Initialize validator
    validator = HardwareValidator('hardware_database.json', 'compatibility_rules.json')
    
    # Example description
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
    
    # Generate report
    report = validator.generate_report(description)
    print(report)