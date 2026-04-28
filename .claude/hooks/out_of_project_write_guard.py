#!/usr/bin/env python3
"""PreToolUse Write — blocks writes to paths outside the project root."""
import json
import os
import subprocess
import sys

event = json.load(sys.stdin)
file_path = event.get("tool_input", {}).get("file_path", "")

if not file_path:
    sys.exit(0)

result = subprocess.run(
    ["git", "rev-parse", "--show-toplevel"],
    capture_output=True,
    text=True,
)
if result.returncode != 0:
    sys.exit(0)

project_root = os.path.realpath(result.stdout.strip())
abs_file = os.path.realpath(os.path.abspath(file_path))

if not abs_file.startswith(project_root + os.sep) and abs_file != project_root:
    print(
        f"ERROR: Writing outside the project directory is blocked.",
        file=sys.stderr,
    )
    print(f"Attempted path: {file_path}", file=sys.stderr)
    sys.exit(1)
