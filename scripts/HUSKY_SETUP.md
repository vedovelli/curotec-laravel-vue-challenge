# Husky Git Hooks Setup

This project uses [Husky](https://typicode.github.io/husky/) to automatically run quality checks and enforce coding standards through git hooks.

## What is Husky?

Husky is a tool that makes it easy to use git hooks. It's ultra-fast (runs in ~1ms), lightweight (2kB gzipped), and has no dependencies. It automatically runs scripts at specific git events like commit, push, etc.

## Installed Hooks

### 1. Pre-commit Hook (`.husky/pre-commit`)

Runs **before** each commit with minimal checks for fast commits:

- ✅ **Minimal validation** - Basic pre-commit validation
- 🚀 **Fast execution** - No blocking quality checks

This allows for quick, frequent commits during development.

### 2. Pre-push Hook (`.husky/pre-push`)

Runs **before** each push with comprehensive quality validation:

- ✅ **PHPStan static analysis** (`composer quality`)
- ✅ **PHP tests** (`php artisan test`)
- ✅ **ESLint check** (`npm run lint:check`)
- ✅ **Prettier formatting check** (`npm run format:check`)
- ✅ **Frontend build** (`npm run build`)
- ⚠️ **Console.log detection** (warns but doesn't block)

If any check fails, the push is blocked with helpful error messages.

## Available Scripts

### Quality Check Scripts

```bash
# Run all quality checks (backend + frontend)
npm run quality:all

# Run only backend quality checks
npm run quality:backend

# Run only frontend quality checks
npm run quality:frontend

# Auto-fix frontend issues
npm run fix:frontend
```

### Individual Scripts

```bash
# Backend
composer quality          # PHPStan analysis
php artisan test          # PHP tests

# Frontend
npm run lint:check        # ESLint check
npm run format:check      # Prettier check
npm run lint              # ESLint auto-fix
npm run format            # Prettier auto-fix
npm run build             # Build frontend
```

## How It Works

1. **Installation**: Husky was installed with `npm install --save-dev husky`
2. **Initialization**: `npx husky init` created the `.husky` directory
3. **Prepare Script**: Added to `package.json` to ensure hooks are installed
4. **Hook Scripts**: Created executable shell scripts in `.husky/`

## Bypassing Hooks (Emergency Use Only)

If you need to bypass hooks in an emergency:

```bash
# Skip pre-commit hooks
git commit --no-verify -m "emergency fix"

# Skip pre-push hooks
git push --no-verify
```

**⚠️ Warning**: Only use `--no-verify` in genuine emergencies. The hooks exist to maintain code quality.

## Troubleshooting

### Hook Not Running

1. Ensure hooks are executable:

   ```bash
   chmod +x .husky/pre-commit
   chmod +x .husky/pre-push
   ```

2. Reinstall Husky:
   ```bash
   npm run prepare
   ```

### Quality Checks Failing

1. **PHPStan errors**: Fix type issues or update `phpstan.neon`
2. **Test failures**: Fix failing tests before committing
3. **ESLint errors**: Run `npm run lint` to auto-fix
4. **Prettier errors**: Run `npm run format` to auto-fix
5. **Build errors**: Fix TypeScript/Vue compilation issues

### Performance

If hooks are too slow, you can:

1. **Optimize individual checks**: Focus on changed files only
2. **Split checks**: Move some checks to CI/CD pipeline
3. **Parallel execution**: Run independent checks in parallel

## Benefits

- 🚀 **Fast commits**: Minimal pre-commit checks allow quick, frequent commits
- 🛡️ **Quality gate**: Comprehensive checks before sharing code via push
- 🔄 **Automated workflow**: No manual quality check steps
- 🎯 **Flexible development**: Commit often, validate before sharing
- ⚡ **Developer-friendly**: Quality checks only when pushing to remote

## Integration with CI/CD

These hooks complement (don't replace) CI/CD pipelines:

- **Local hooks**: Fast feedback during development
- **CI/CD**: Comprehensive testing, deployment, security scans

Both layers ensure maximum code quality and reliability.
