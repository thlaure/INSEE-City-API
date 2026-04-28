#!/usr/bin/env python3
"""PostToolUse Write|Edit — detects hardcoded credentials in PHP files."""
import json
import re
import sys

PATTERN = re.compile(
    r"""(?:password|secret|api_key|private_key|token|credential)\s*[=:>]+\s*['"][A-Za-z0-9+/!@#%^&*_\-]{8,}['"]""",
    re.IGNORECASE,
)

event = json.load(sys.stdin)
tool_input = event.get("tool_input", {})
file_path = tool_input.get("file_path", "")

if not file_path.endswith(".php"):
    sys.exit(0)

content = tool_input.get("content") or tool_input.get("new_string") or ""
matches = PATTERN.findall(content)

if matches:
    print(f"ERROR: Possible hardcoded credential in {file_path}:", file=sys.stderr)
    for m in matches[:3]:
        print(f"  {m}", file=sys.stderr)
    print("Fix: use environment variables or Symfony secrets instead.", file=sys.stderr)
    sys.exit(1)
