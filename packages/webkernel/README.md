# webkernel

<img style="width:100%" src="https://raw.githubusercontent.com/numerimondes/webkernel/refs/heads/main/src/docs/assets/og-webkernel.png">

> **Warning:** This package must be installed on a new Laravel project. The user model will be located in `packages/webkernel/src/Models/User.php` and its namespace will change to `Webkernel\Models\User`.

> **Note:** While the package is available via Packagist using `composer require webkernel/webkernel`, we recommend following the process below as it's clearer and doesn't send files to `/vendor`.

## Installation Steps (from project root)

### Prerequisites

- Ensure Laravel is installed locally
- Never install this package on a server or environment you don't manage

### Step 1: Install the Package

**On Linux and macOS:**
```bash
mkdir -p packages && git clone https://github.com/numerimondes/webkernel.git packages/webkernel
```

**On Windows:**
```powershell
if (-Not (Test-Path "packages")) { New-Item -ItemType Directory -Path "packages" | Out-Null }; git clone https://github.com/numerimondes/webkernel.git packages\webkernel
```

### Step 2: Run the Installation Script

After cloning the repository, execute the installation script to finalize the package integration into your Laravel project. From your Laravel project root, run:

```bash
php packages/webkernel/install.php
```

This script will configure your environment and prepare the user model.

### Step 3: Additional Configuration

If you need to modify the user model or other package elements, you can find the user model in `packages/webkernel/src/Models/User.php`. The user model namespace has been changed to `Webkernel\Models\User`.

### Step 4: Verification

Once installation is complete, you can verify that everything works correctly by:
- Running migrations
- Checking that routes are properly defined
- Testing access to Webkernel package-related functionalities
