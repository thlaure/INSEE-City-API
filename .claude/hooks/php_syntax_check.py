#!/usr/bin/env python3
"""PostToolUse Write|Edit — runs php -l on every PHP file written or edited."""
import json
import subprocess
import sys

event = json.load(sys.stdin)
file_path = event.get("tool_input", {}).get("file_path", "")

if not file_path.endswith(".php"):
    sys.exit(0)

result = subprocess.run(["php", "-l", file_path], capture_output=True, text=True)
if result.returncode != 0:
    print(result.stdout, file=sys.stderr)
    print(result.stderr, file=sys.stderr)
    sys.exit(1)
