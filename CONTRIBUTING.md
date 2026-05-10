# Contributing to Grubeli.ge

We love your input! We want to make contributing to Grubeli.ge as easy and transparent as possible.

## Table of Contents
1. [Code of Conduct](#code-of-conduct)
2. [Development Process](#development-process)
3. [Pull Requests](#pull-requests)
4. [Coding Standards](#coding-standards)
5. [Commit Messages](#commit-messages)
6. [Testing](#testing)
7. [Reporting Issues](#reporting-issues)
8. [Feature Requests](#feature-requests)

---

## Code of Conduct

By participating, you are expected to uphold this code:

- **Be respectful** — Different perspectives make our project stronger
- **Be constructive** — Criticism should be specific and actionable
- **Be collaborative** — Work together to find the best solutions
- **Be inclusive** — We welcome contributors of all backgrounds

---

## Development Process

1. **Fork** the repository
2. **Clone** your fork:
   ```bash
   git clone https://github.com/your-username/grubeli.ge.git
   cd grubeli.ge
   ```
3. **Create a branch**:
   ```bash
   git checkout -b feature/your-feature-name
   ```
4. **Make changes** following our coding standards
5. **Test** your changes (see Testing section)
6. **Commit** with a clear message (see Commit Messages)
7. **Push** to your fork:
   ```bash
   git push origin feature/your-feature-name
   ```
8. **Open a Pull Request**

---

## Pull Requests

### PR Checklist
- [ ] Code follows project coding standards
- [ ] No debug code, commented-out code, or `error_log()` calls
- [ ] New features include documentation in README
- [ ] All existing functionality is preserved
- [ ] API changes are backward-compatible
- [ ] No secrets or API keys committed

### PR Title Format
```
[type]: Brief description (max 72 chars)
```

Types:
- `feat` — New feature
- `fix` — Bug fix
- `refactor` — Code restructuring
- `perf` — Performance improvement
- `docs` — Documentation changes
- `style` — Code style/formatting (no logic change)
- `chore` — Build/config changes
- `security` — Security fix

### Examples
```
feat: add wind direction compass to hourly forecast
fix: resolve Nominatim rate limit by adding 24h cache
docs: update README with new API endpoints
perf: parallelize weather and air quality API calls
```

---

## Coding Standards

### PHP
- **PSR-12** coding style
- Type hints where possible (`int`, `float`, `string`, `array`, `?type`)
- No short tags (`<?` → use `<?php`)
- Functions prefixed with `get_`, `fetch_`, `cache_`, `is_`, `enrich_`
- Constants in `UPPER_CASE` with descriptive names
- Avoid `@` error suppression; use try-catch instead

### HTML
- Semantic HTML5 (`<header>`, `<main>`, `<footer>`, `<section>`)
- Avoid inline styles where possible (use CSS classes)
- All images must have `alt` attributes
- ARIA attributes for dynamic content (`aria-live`, `aria-expanded`)

### CSS
- Use CSS custom properties for theme colors
- Class naming: `kebab-case` (e.g., `.weather-card`, `.hourly-scroll`)
- Mobile-first responsive design
- No `!important` unless absolutely necessary
- Animations should respect `prefers-reduced-motion`

### JavaScript
- Vanilla JS (no jQuery or frameworks)
- `const` / `let` (no `var`)
- Use `async/await` for async operations
- Debounce search inputs (>200ms)
- No inline event handlers in HTML

### Security
- All user input must be sanitized (`htmlspecialchars()`, `intval()`)
- CSRF tokens for POST endpoints
- Session data should not store large objects
- Use prepared statements if adding database queries

---

## Commit Messages

Follow [Conventional Commits](https://www.conventionalcommits.org/):

```
type(scope): description

[optional body]
[optional footer]
```

### Examples
```
feat(ui): add moon phase widget to main weather card

- Show moonrise/moonset times
- Display lunar phase icon
- Cache moon data with weather data

Closes #42
```

```
fix(api): cache Nominatim reverse geocode for 24 hours

Previously, every page load made a request to Nominatim.
Now results are cached for 24 hours, reducing API calls by ~99%.
```

---

## Testing

### Manual Testing Checklist
- [ ] Page loads without PHP errors/warnings
- [ ] Weather data displays correctly for Tbilisi (default)
- [ ] Air quality index updates when changing location
- [ ] All 3 alert types (earthquake, fire, AI) don't break page
- [ ] Search works for all 97 Georgian cities
- [ ] Historical weather page loads Chart.js charts
- [ ] Mobile responsive: 375px, 768px, 1024px widths
- [ ] Dark/light mode transitions work
- [ ] No console errors in browser dev tools

### Running Locally
```bash
# PHP development server
php -S localhost:8000

# Check for PHP syntax errors
php -l index.php
php -l functions.php
```

---

## Reporting Issues

Use the [issue tracker](https://github.com/yourusername/grubeli.ge/issues).

### Bug Reports
Include:
1. **Description** — What happened vs. what was expected
2. **Steps to reproduce** — Be specific
3. **Environment** — Browser, OS, PHP version
4. **Screenshots** — If applicable
5. **Error logs** — Check `logs/` directory or browser console

### Feature Requests
Include:
1. **Use case** — Why this feature is needed
2. **Proposed solution** — How it could work
3. **Alternatives** — What other approaches you considered
4. **Examples** — Similar features in other apps

---

## License

By contributing, you agree that your contributions will be licensed under the **GNU General Public License v3.0** (GPLv3). See [LICENSE](LICENSE).
