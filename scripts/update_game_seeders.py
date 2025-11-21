import re
from pathlib import Path


BASE_DIR = Path(__file__).resolve().parents[1]
SEEDERS_DIR = BASE_DIR / "database" / "seeders"

EXCLUDED_FILES = {
    "BaseGameListSeeder.php",
    "GameListApiSeeder.php",
    "advantplaySeeder.php",
    "DatabaseSeeder.php",
    "GameTypeProductTableSeeder.php",
    "GameTypeTableSeeder.php",
    "GscPlusProductTableSeeder.php",
}

DEFAULT_DIRECTORY = "app/Console/Commands/data/"


def build_options(directory: str | None) -> str:
    options = []

    if directory and directory != DEFAULT_DIRECTORY:
        options.append(f"'directory' => '{directory}'")

    if options:
        inner = ",\n            ".join(options)
        return ", [\n            " + inner + "\n        ]"

    return ""


def refactor_seeder(path: Path) -> None:
    content = path.read_text()

    if "base_path(" not in content:
        return

    class_match = re.search(r"class\s+(\w+)\s+extends\s+\w+", content)
    if not class_match:
        return

    class_name = class_match.group(1)

    json_match = re.search(r"base_path\('([^']+)'\)", content)
    if not json_match:
        return

    full_path = json_match.group(1)
    if "/data/" in full_path:
        directory, filename = full_path.split("/data/", 1)
        directory = directory + "/data/"
    elif "/json_data/" in full_path:
        directory, filename = full_path.split("/json_data/", 1)
        directory = directory + "/json_data/"
    else:
        parts = full_path.rsplit("/", 1)
        if len(parts) == 2:
            directory, filename = parts
            directory = directory + "/"
        else:
            directory = DEFAULT_DIRECTORY
            filename = parts[0]

    new_content = """<?php

namespace Database\\Seeders;

class {class_name} extends BaseGameListSeeder
{{
    public function run(): void
    {{
        $this->seedFromJson('{filename}'{options});
    }}
}}
""".format(
        class_name=class_name,
        filename=filename,
        options=build_options(directory),
    )

    path.write_text(new_content)


def main() -> None:
    for seeder_file in SEEDERS_DIR.glob("*Seeder.php"):
        if seeder_file.name in EXCLUDED_FILES:
            continue
        refactor_seeder(seeder_file)


if __name__ == "__main__":
    main()

