# Frontend Testing Setup

This project uses **Vitest** and **Vue Testing Library** for testing Vue.js components.

## Setup

The testing environment is configured with:

- **Vitest** - Fast unit test framework powered by Vite
- **Vue Testing Library** - Simple and complete testing utilities for Vue
- **Happy DOM** - Lightweight DOM implementation for testing
- **Jest DOM** - Custom matchers for DOM testing

## Configuration

Testing configuration is located in `vite.config.ts`:

```typescript
test: {
  globals: true,
  environment: 'happy-dom',
  include: ['resources/js/**/*.{test,spec}.{js,ts,vue}'],
  setupFiles: ['./tests/frontend/setup.ts'],
}
```

## Running Tests

```bash
# Run tests in watch mode
npm run test

# Run tests once
npm run test:run

# Run tests with UI
npm run test:ui

# Run tests with coverage
npm run test:coverage
```

## Writing Tests

### Basic Component Test

```typescript
import { render, screen } from '@testing-library/vue';
import { describe, it, expect } from 'vitest';
import MyComponent from '../MyComponent.vue';

describe('MyComponent', () => {
  it('should render correctly', () => {
    render(MyComponent, {
      props: {
        title: 'Test Title',
      },
    });

    expect(screen.getByText('Test Title')).toBeInTheDocument();
  });
});
```

### Testing User Interactions

```typescript
import { render, screen } from '@testing-library/vue';
import userEvent from '@testing-library/user-event';
import { describe, it, expect } from 'vitest';

describe('Interactive Component', () => {
  it('should handle click events', async () => {
    const user = userEvent.setup();

    render(MyComponent);

    const button = screen.getByRole('button');
    await user.click(button);

    expect(screen.getByText('Clicked!')).toBeInTheDocument();
  });
});
```

### Testing with Slots

```typescript
render(MyComponent, {
  slots: {
    default: 'Slot content',
    header: '<h1>Header content</h1>',
  },
});
```

### Mocking Dependencies

```typescript
import { vi } from 'vitest';

// Mock external dependencies
vi.mock('@inertiajs/vue3', () => ({
  Link: {
    name: 'Link',
    props: ['href'],
    template: '<a :href="href"><slot /></a>',
  },
}));
```

## Best Practices

1. **Use semantic queries**: Prefer `getByRole`, `getByLabelText`, `getByText` over `getByTestId`
2. **Test behavior, not implementation**: Focus on what the user sees and does
3. **Use async/await**: Always await user interactions and async operations
4. **Mock external dependencies**: Mock API calls, external libraries, and complex dependencies
5. **Keep tests simple**: One assertion per test when possible
6. **Use descriptive test names**: Test names should clearly describe what is being tested

## Available Matchers

Thanks to `@testing-library/jest-dom`, you have access to custom matchers:

- `toBeInTheDocument()`
- `toHaveClass(className)`
- `toHaveAttribute(attr, value)`
- `toHaveTextContent(text)`
- `toBeVisible()`
- `toBeDisabled()`
- And many more...

## File Structure

```
tests/frontend/
├── README.md          # This file
└── setup.ts          # Global test setup

resources/js/components/
└── __tests__/        # Component tests
    ├── Icon.test.ts
    └── TextLink.test.ts
```

## Examples

Check the existing test files for examples:

- `resources/js/components/__tests__/Icon.test.ts` - Basic component testing
- `resources/js/components/__tests__/TextLink.test.ts` - Testing with slots and user interactions
