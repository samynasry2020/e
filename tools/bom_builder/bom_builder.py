#!/usr/bin/env python3
import json
import re
import sys
from pathlib import Path
from typing import Dict, Any, List, Tuple

DB_PATH = Path(__file__).with_name("parts_db.json")

AR = {
    "bom_title": "قائمة المكونات المقترحة (BOM)",
    "missing_title": "الأجزاء الناقصة/الاختيارية",
    "validation_title": "نتائج التوافق والتحقق",
    "ok": "متوافق",
    "warn": "تحذير",
    "fail": "غير متوافق",
}

def load_db() -> List[Dict[str, Any]]:
    with open(DB_PATH, "r", encoding="utf-8") as f:
        return json.load(f)

KEY_HINTS = [
    (r"up to\s*56\s*cores|56\s*cores", {"cpu_min_cores": 56}),
    (r"60\s*core|up to a 60 core", {"cpu_min_cores": 60}),
    (r"Intel\s*Xeon\s*W", {"cpu_family": "Xeon W"}),
    (r"up to\s*4\s*high[- ]end\s*GPUs|up to\s*4\s*NVIDIA RTX|up to\s*4 GPUs", {"gpu_count": 4}),
    (r"NVIDIA RTX\s*6000\s*Ada", {"gpu_model": "RTX 6000 Ada"}),
    (r"2TB\s*DDR5", {"memory_target_gb": 2048}),
    (r"120TB\s*storage", {"storage_target_tb": 120}),
    (r"2,?250W|2250W", {"psu_target_watts": 2250}),
    (r"8\s*PCIe\s*slots", {"pcie_slots": 8}),
    (r"4\s*front accessible NVMe bays|4\s*front NVMe bays", {"front_nvme_bays": 4}),
    (r"whisper[- ]quiet|stay cool and quiet", {"acoustic_focus": True}),
]


def parse_requirements(text: str) -> Dict[str, Any]:
    text_l = text.lower()
    req: Dict[str, Any] = {}
    for pat, kv in KEY_HINTS:
        if re.search(pat, text, flags=re.IGNORECASE):
            req.update(kv)
    # Heuristics
    if "xeon w" in text_l:
        req.setdefault("cpu_family", "Xeon W")
    if "4 high end gpus" in text_l or "up to 4" in text_l and "gpu" in text_l:
        req.setdefault("gpu_count", 4)
    if "rtx 6000 ada" in text_l:
        req.setdefault("gpu_model", "RTX 6000 Ada")
    return req


def select_parts(db: List[Dict[str, Any]], req: Dict[str, Any]) -> Tuple[List[Dict[str, Any]], List[str]]:
    selected: List[Dict[str, Any]] = []
    missing: List[str] = []

    # CPU
    cpu_candidates = [p for p in db if p["category"] == "cpu" and p["specs"].get("family") == req.get("cpu_family", p["specs"].get("family"))]
    if req.get("cpu_min_cores"):
        cpu_candidates = [p for p in cpu_candidates if p["specs"].get("cores", 0) >= req["cpu_min_cores"]]
        # prefer closest equal or minimal above
        cpu_candidates.sort(key=lambda p: p["specs"]["cores"])  # ascending
    if not cpu_candidates:
        cpu_candidates = [p for p in db if p["category"] == "cpu"]
    if cpu_candidates:
        selected.append(cpu_candidates[-1])  # pick top (max cores)
    else:
        missing.append("CPU Xeon W")

    # Motherboard (match socket and support 4 GPUs if requested)
    mb_candidates = [p for p in db if p["category"] == "motherboard"]
    if selected:
        socket = selected[0]["specs"].get("socket")
        mb_candidates = [p for p in mb_candidates if p["specs"].get("socket") == socket]
    if req.get("gpu_count"):
        mb_candidates = [p for p in mb_candidates if p["specs"].get("max_gpus_double_slot", 0) >= req["gpu_count"]]
    if not mb_candidates:
        missing.append("Motherboard W790 LGA4677 with >=4 GPUs support")
    else:
        # prefer EEB form factor for expandability
        eeb = [p for p in mb_candidates if p["specs"].get("form_factor") == "EEB"]
        selected.append(eeb[0] if eeb else mb_candidates[0])

    # GPU(s)
    if req.get("gpu_model") == "RTX 6000 Ada":
        gpu_candidates = [p for p in db if p["category"] == "gpu" and "rtx6000ada" in p.get("compatibility_tags", [])]
    else:
        gpu_candidates = [p for p in db if p["category"] == "gpu"]
    if req.get("gpu_count"):
        if gpu_candidates:
            # replicate item count times
            for _ in range(req["gpu_count"]):
                selected.append(gpu_candidates[0])
        else:
            missing.append(f"{req['gpu_count']}x GPUs")
    elif gpu_candidates:
        selected.append(gpu_candidates[0])

    # Memory target
    mem_target = req.get("memory_target_gb", 512)
    mem_candidates = [p for p in db if p["category"] == "memory" and "ddr5-ecc-rdimm" in p.get("compatibility_tags", [])]
    if mem_candidates:
        # Choose number of modules to reach target or close below
        mod = mem_candidates[0]
        count = max(1, mem_target // mod["specs"]["capacity_gb"]) if mem_target else 8
        # Avoid exceeding motherboard slots (assume 8)
        count = min(count, 8)
        for _ in range(count):
            selected.append(mod)
        if mem_target and mod["specs"]["capacity_gb"] * count < mem_target:
            missing.append(f"Additional RDIMMs to reach {mem_target}GB")
    else:
        missing.append("DDR5 ECC RDIMMs")

    # Storage target and front bays
    if req.get("storage_target_tb"):
        # Use 30.72TB U.2 NVMe and 4-bay cage
        ssd = next((p for p in db if p["id"] == "nvme-micron-9400-pro-30.72tb-u2"), None)
        cage = next((p for p in db if p["category"] == "nvme_cage" and p["specs"].get("bays") == 4), None)
        if ssd:
            needed = int((req["storage_target_tb"] + ssd["specs"]["capacity_tb"] - 1) // ssd["specs"]["capacity_tb"])  # ceil
            needed = min(needed, 4)  # constrained by cage
            for _ in range(needed):
                selected.append(ssd)
            if req["storage_target_tb"] > ssd["specs"]["capacity_tb"] * 4:
                missing.append("Extra storage beyond 4x U.2 bays or add HBA")
        else:
            missing.append("Enterprise U.2 NVMe SSDs")
        if req.get("front_nvme_bays", 0) >= 4 and cage:
            selected.append(cage)
        elif req.get("front_nvme_bays"):
            missing.append("Front-accessible 4-bay U.2/U.3 cage")

    # PSU sizing
    gpu_power = sum(p["specs"].get("tdp_watts", 0) for p in selected if p["category"] == "gpu")
    cpu_power = next((p for p in selected if p["category"] == "cpu"), {}).get("specs", {}).get("tdp_watts", 0)
    platform_overhead = 150  # chipset, fans, storage, etc.
    total_tdp = gpu_power + cpu_power + platform_overhead
    target_psu = max(req.get("psu_target_watts", 0), int(total_tdp * 1.25))
    psu_candidates = [p for p in db if p["category"] == "psu" and p["specs"].get("wattage", 0) >= target_psu]
    if psu_candidates:
        # prefer higher wattage
        psu_candidates.sort(key=lambda p: p["specs"]["wattage"]) 
        selected.append(psu_candidates[-1])
    else:
        missing.append(f"PSU >= {target_psu}W")

    # Cooler for LGA4677
    cooler = next((p for p in db if p["category"] == "cooler" and p["specs"].get("socket") == selected[0]["specs"].get("socket")), None)
    if cooler:
        selected.append(cooler)
    else:
        missing.append("LGA4677 CPU cooler 350W")

    # Chassis (needs 4x 5.25in or native bays and support EEB)
    chassis_candidates = [p for p in db if p["category"] == "chassis"]
    if req.get("front_nvme_bays"):
        chassis_candidates = [p for p in chassis_candidates if p["specs"].get("front_bays_5_25", 0) >= 2 or p["specs"].get("front_nvme_bays", 0) >= 4]
    mb = next((p for p in selected if p["category"] == "motherboard"), None)
    if mb:
        ff = mb["specs"].get("form_factor")
        chassis_candidates = [p for p in chassis_candidates if ff in p["specs"].get("board_support", [])]
    if chassis_candidates:
        selected.append(chassis_candidates[0])
    else:
        missing.append("Chassis supporting EEB/E-ATX and 4 GPUs")

    return selected, missing


def validate(selected: List[Dict[str, Any]], req: Dict[str, Any]) -> List[Tuple[str, str, str]]:
    results: List[Tuple[str, str, str]] = []
    mb = next((p for p in selected if p["category"] == "motherboard"), None)
    cpu = next((p for p in selected if p["category"] == "cpu"), None)
    gpus = [p for p in selected if p["category"] == "gpu"]
    mems = [p for p in selected if p["category"] == "memory"]
    psu = next((p for p in selected if p["category"] == "psu"), None)

    # CPU-MB socket
    if cpu and mb and cpu["specs"].get("socket") == mb["specs"].get("socket"):
        results.append(("CPU ↔ لوحة الأم (المقبس)", AR["ok"], "مطابق LGA4677"))
    else:
        results.append(("CPU ↔ لوحة الأم (المقبس)", AR["fail"], "عدم تطابق المقابس"))

    # GPU count vs MB capacity
    if mb and gpus:
        max_gpus = mb["specs"].get("max_gpus_double_slot", 0)
        if len(gpus) <= max_gpus:
            results.append(("عدد الـGPU ↔ اللوحة الأم", AR["ok"], f"{len(gpus)} ≤ {max_gpus}"))
        else:
            results.append(("عدد الـGPU ↔ اللوحة الأم", AR["fail"], f"{len(gpus)} > {max_gpus}"))

    # Memory type
    if mb and mems:
        if "DDR5 RDIMM" in mb["specs"].get("memory_type", "") and all("DDR5 RDIMM" in m["specs"]["type"] for m in mems):
            results.append(("الرام ↔ اللوحة الأم", AR["ok"], "DDR5 RDIMM ECC"))
        else:
            results.append(("الرام ↔ اللوحة الأم", AR["fail"], "نوع الذاكرة غير متطابق"))

    # PSU sizing
    if psu:
        gpu_power = sum(p["specs"].get("tdp_watts", 0) for p in gpus)
        cpu_power = cpu["specs"].get("tdp_watts", 0) if cpu else 0
        platform_overhead = 150
        tdp = gpu_power + cpu_power + platform_overhead
        if psu["specs"].get("wattage", 0) >= int(tdp * 1.2):
            results.append(("قدرة مزوّد الطاقة", AR["ok"], f"PSU {psu['specs']['wattage']}W ≥ {int(tdp*1.2)}W"))
        else:
            results.append(("قدرة مزوّد الطاقة", AR["warn"], f"قد تكون غير كافية ({psu['specs']['wattage']}W < {int(tdp*1.2)}W)"))

    return results


def format_output(selected: List[Dict[str, Any]], missing: List[str], validations: List[Tuple[str, str, str]]) -> str:
    lines: List[str] = []
    lines.append(f"## {AR['bom_title']}")
    # Group items and enumerate counts
    counts: Dict[str, int] = {}
    for p in selected:
        key = (p["category"], p["manufacturer"], p["model"], p.get("mpn") or "—")
        counts[str(key)] = counts.get(str(key), 0) + 1
    for key_str, count in counts.items():
        category, mfr, model, mpn = eval(key_str)
        lines.append(f"- **{category.upper()}**: {mfr} {model} x{count} — MPN: {mpn}")

    if missing:
        lines.append(f"\n## {AR['missing_title']}")
        for m in missing:
            lines.append(f"- {m}")

    if validations:
        lines.append(f"\n## {AR['validation_title']}")
        for name, status, detail in validations:
            lines.append(f"- **{name}**: {status} — {detail}")

    return "\n".join(lines)


def main() -> int:
    text = sys.stdin.read() if not sys.argv[1:] else " ".join(sys.argv[1:])
    if not text.strip():
        print("Provide a description via stdin or args.")
        return 2
    db = load_db()
    req = parse_requirements(text)
    selected, missing = select_parts(db, req)
    validations = validate(selected, req)
    print(format_output(selected, missing, validations))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
