# MERGE (•) ELEMENTS - Usage Guide

Advanced directory and file bundling command with exclusion patterns and integrity verification.

## Quick Start

### Simple Mode (Fastest)
```bash
php artisan merge:elements --simple
```
No complexity, just enter paths and merge. Perfect for quick bundling.

### Single Path
```bash
php artisan merge:elements --unique=platform/Numerimondes
```

### Multiple Paths (Interactive)
```bash
php artisan merge:elements --multiple
```

## Exclusion Patterns

### Using .gitignore
```bash
php artisan merge:elements --use-gitignore
```
Automatically loads exclusion patterns from your `.gitignore` file.

### Custom Exclusion Patterns
```bash
php artisan merge:elements --exclude=vendor,node_modules,*.log
```

### Combined Example
```bash
php artisan merge:elements \
  --unique=platform/Numerimondes \
  --exclude=Filament/Resources/Software/Pages \
  --use-gitignore \
  --checksum \
  --output=numerimondes-bundle.zip
```

This command will:
- Merge `platform/Numerimondes`
- Exclude `platform/Numerimondes/Filament/Resources/Software/Pages`
- Apply `.gitignore` patterns
- Generate checksums
- Create a ZIP archive

## Checksum Verification

### Generate Checksums
```bash
php artisan merge:elements --checksum
```

Creates:
- `CHECKSUMS.txt` inside the archive (for ZIP)
- `merged-data-*.checksum` external file with SHA256 hashes

### Checksum File Format
```
# CHECKSUM INTEGRITY VERIFICATION
# Generated on: 2025-10-26 14:30:00
# Algorithm: SHA256
# Total files: 42

a1b2c3d4...  platform/Numerimondes/Models/User.php
e5f6g7h8...  platform/Numerimondes/Controllers/HomeController.php
```

## Output Formats

### Text File (.txt)
```bash
php artisan merge:elements --output=my-bundle.txt
```
Creates a single text file with:
- Directory tree structure
- Full content of all files
- Optional checksums

### ZIP Archive (.zip)
```bash
php artisan merge:elements --output=my-bundle.zip
```
Creates a ZIP with:
- Original directory structure preserved
- Individual files accessible
- Optional CHECKSUMS.txt

## Advanced Examples

### Module Export with Checksums
```bash
php artisan merge:elements \
  --unique=platform/Numerimondes \
  --exclude=tests,docs,*.md \
  --checksum \
  --output=numerimondes-v1.0.0.zip
```

### Multi-Path Bundle
```bash
php artisan merge:elements --simple
# Then enter:
# platform/Numerimondes,config/numerimondes.php,routes/numerimondes.php
# done
```

### Complete Backup
```bash
php artisan merge:elements \
  --multiple \
  --use-gitignore \
  --checksum \
  --output=project-backup-$(date +%Y%m%d).zip
```

## File Structure

```
Webkernel/Aptitudes/Base/Commands/
├── MergeElementsCommand.php           # Main command
└── MergeElementsCommand/
    ├── ExclusionManager.php           # Handles exclusion patterns
    ├── ChecksumGenerator.php          # Generates and verifies checksums
    └── PathCollector.php              # Collects and validates paths
```

## Exclusion Pattern Syntax

Patterns follow `.gitignore` conventions:

- `vendor/` - Exclude vendor directory
- `*.log` - Exclude all .log files
- `**/tests/` - Exclude tests directories anywhere
- `node_modules/` - Exclude node_modules
- `/build` - Exclude build in root only
- `!important.log` - Include (negation)

## Developer Experience Features

✓ **Interactive prompts** with smart defaults  
✓ **Progress bars** for long operations  
✓ **Color-coded output** for better readability  
✓ **Auto-complete** path suggestions  
✓ **Conflict resolution** for existing files  
✓ **Detailed summaries** after operations  
✓ **Error handling** with clear messages  

## Programmatic Usage

```php
use Webkernel\Aptitudes\Base\Commands\MergeElementsCommand;

$command = new MergeElementsCommand();

// Merge to text file
$command->mergeToTextFile([
    'platform/Numerimondes',
    'config/numerimondes.php'
], 'numerimondes-export.txt');

// Merge to ZIP
$command->mergeToZipArchive([
    'platform/Numerimondes'
], 'numerimondes-bundle.zip');
```

## Tips & Tricks

1. **Use `--simple`** for quick operations without questions
2. **Combine `--use-gitignore` and `--exclude`** for precise control
3. **Always use `--checksum`** for production exports
4. **Name outputs descriptively**: `module-name-v1.0.0.zip`
5. **Test exclusions** with a text output first before ZIP

## Troubleshooting

**Problem**: Path not found  
**Solution**: Use relative paths from project root or absolute paths

**Problem**: Too many files excluded  
**Solution**: Review your exclusion patterns, use `!pattern` for exceptions

**Problem**: Checksum verification fails  
**Solution**: Files were modified after export, regenerate the bundle

## Performance

- **Small projects** (<100 files): Instant
- **Medium projects** (100-1000 files): ~5-10 seconds
- **Large projects** (1000+ files): Progress bar shows real-time status

## Security Notes

- Checksums use SHA256 algorithm
- External checksum files for verification
- Exclusion patterns prevent sensitive data leaks
- Always review `.gitignore` before using `--use-gitignore`

---

**Built for**: Modular architectures, backup workflows, module distribution, automated inspection

**Maintained by**: Webkernel Team
