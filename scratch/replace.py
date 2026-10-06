import os

def replace_in_file(filepath):
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
    except Exception:
        return

    new_content = content.replace("L'Femma ONE", "Jelita Saung")
    new_content = new_content.replace("L'Femma", "Jelita Saung")
    new_content = new_content.replace("LPP Mataram", "Rutan Perempuan Bandung")
    
    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated {filepath}")

def walk_directory(dirpath):
    for root, _, files in os.walk(dirpath):
        for file in files:
            if file.endswith('.vue') or file.endswith('.php'):
                replace_in_file(os.path.join(root, file))

if __name__ == '__main__':
    walk_directory('/Users/agustokenyjia/Herd/E-Commerce/resources')
