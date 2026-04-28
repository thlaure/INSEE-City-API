#!/usr/bin/env python3
"""PreToolUse Bash — blocks git commit on protected branches."""
import json
import subprocess
import sys

PROTECTED = {"main", "master", "develop"}

event = json.load(sys.stdin)
command = event.get("tool_input", {}).get("command", "")

if not command.startswith("git commit"):
    sys.exit(0)

result = subprocess.run(
    ["git", "branch", "--show-current"],
    capture_output=True,
    text=True,
)
branch = result.stdout.strip()

if branch in PROTECTED:
    print(
        f"ERROR: Cannot commit directly on protected branch '{branch}'. "
        "Create a feature branch first.",
        file=sys.stderr,
    )
    sys.exit(1)
