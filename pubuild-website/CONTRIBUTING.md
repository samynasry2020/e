# Contributing to PU Build Website

## Content Updates (Non-Developers)

### Updating Products

Edit JSON files in `/content/products/`:

1. Open the relevant file (e.g., `servers.json`)
2. Update product information following the existing structure
3. Test locally if possible, or provide to developer for deployment

**Example:**
```json
{
  "id": "server-new",
  "name": "New Server Model",
  "description": "Description here",
  "specs": [
    "Spec 1",
    "Spec 2"
  ],
  "features": ["Feature 1", "Feature 2"]
}
```

### Updating Company Information

Edit `/lib/constants.ts`:

- Contact information
- Business hours
- Certifications (once obtained)
- NAICS codes
- Locations

### Updating FAQs

Edit `/content/faqs.json`:

```json
{
  "question": "Your question?",
  "answer": "Your answer",
  "category": "General"
}
```

## Code Contributions (Developers)

### Getting Started

1. **Fork and clone:**
   ```bash
   git clone https://github.com/yourusername/pubuild-website.git
   cd pubuild-website
   npm install
   ```

2. **Create a branch:**
   ```bash
   git checkout -b feature/your-feature-name
   ```

3. **Make changes and test:**
   ```bash
   npm run dev
   npm run lint
   npm run type-check
   ```

4. **Commit:**
   ```bash
   git add .
   git commit -m "feat: add your feature"
   ```

5. **Push and create PR:**
   ```bash
   git push origin feature/your-feature-name
   ```

### Code Standards

- **TypeScript**: Use strict types, avoid `any`
- **Formatting**: Follow ESLint rules
- **Accessibility**: Maintain WCAG 2.1 AA compliance
- **Performance**: Keep Lighthouse scores ≥ 90
- **Security**: No hardcoded secrets, validate all inputs

### Commit Messages

Follow conventional commits:

- `feat:` New feature
- `fix:` Bug fix
- `docs:` Documentation updates
- `style:` Code style changes (formatting)
- `refactor:` Code refactoring
- `test:` Adding tests
- `chore:` Maintenance tasks

### Testing Checklist

Before submitting:

- [ ] All pages load without errors
- [ ] Forms submit correctly
- [ ] Mobile responsive
- [ ] Lighthouse audit passes
- [ ] No accessibility violations (axe)
- [ ] No console errors
- [ ] No TypeScript errors
- [ ] Code passes linting

### Review Process

1. Submit PR with clear description
2. Wait for automated checks to pass
3. Request review from maintainer
4. Address feedback
5. Merge when approved

---

**Questions?** Open an issue or contact the development team.
