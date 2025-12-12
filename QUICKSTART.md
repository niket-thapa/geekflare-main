# Quick Start Guide

## Installation Steps

### 1. Install Dependencies

```bash
cd wp-content/themes/main
npm install
```

### 2. Build Assets

```bash
npm run build
```

This compiles Tailwind CSS and copies JavaScript files to the `dist/` folder.

### 3. Activate Theme

1. Go to WordPress Admin → Appearance → Themes
2. Find "Main" theme
3. Click "Activate"

## Development

### Watch Mode (Auto-rebuild on changes)

```bash
npm run dev
```

This will watch for CSS changes and automatically rebuild.

### Production Build

```bash
npm run build
```

## Important Notes

- **Always run `npm run build` after installing dependencies** to generate the CSS file
- The `dist/` folder contains compiled assets - don't edit these directly
- Edit source files in `src/` directory
- Tailwind classes can be used anywhere in PHP templates

## File Structure

- `src/css/style.css` - Edit Tailwind CSS here
- `src/js/main.js` - Edit JavaScript here
- `dist/style.css` - Compiled CSS (auto-generated)
- `dist/script.js` - Compiled JS (auto-generated)

## Troubleshooting

**Styles not showing?**
- Run `npm run build` to compile CSS
- Clear browser cache
- Check that `dist/style.css` exists and has content

**Need help?**
See the full [README.md](README.md) for detailed documentation.

