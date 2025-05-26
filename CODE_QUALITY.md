# Code Quality Setup

This project uses ESLint and Prettier to maintain consistent code quality and formatting.

## Available Scripts

### Linting

- `npm run lint` - Run ESLint with auto-fix on the frontend code
- `npm run lint:check` - Check for linting issues without fixing them

### Formatting

- `npm run format` - Format all frontend code using Prettier
- `npm run format:check` - Check if code is properly formatted

## Configuration Files

- `eslint.config.js` - ESLint configuration using the flat config format
- `.prettierrc` - Prettier configuration for consistent formatting
- `.editorconfig` - Editor configuration for consistent settings across IDEs

## Project Structure

### Frontend (`resources/js/`)

```
resources/js/
├── components/          # Reusable Vue components
│   ├── common/         # Common/shared components
│   ├── forms/          # Form-specific components
│   └── ui/             # UI library components
├── composables/        # Vue composables
├── constants/          # Application constants
├── layouts/            # Page layouts
├── pages/              # Page components
├── stores/             # Pinia stores
├── types/              # TypeScript type definitions
└── utils/              # Utility functions
```

### Backend (`app/`)

```
app/
├── Actions/            # Business logic actions
├── Http/
│   ├── Controllers/    # HTTP controllers
│   ├── Middleware/     # HTTP middleware
│   ├── Requests/       # Form request validation
│   └── Resources/      # API resources
├── Models/             # Eloquent models
├── Repositories/       # Data access layer
└── Services/           # Service layer
```

## Code Quality Rules

- Vue 3 Composition API preferred
- TypeScript strict mode enabled
- Consistent formatting with Prettier
- ESLint rules for Vue 3 best practices
- Automatic import organization
- TailwindCSS class sorting
