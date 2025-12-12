# Main Theme

A modern WordPress theme built with Gutenberg blocks and Tailwind CSS. This theme is designed for content-rich websites with flexible layouts and modern design patterns.

## Features

- ✅ Full Gutenberg block editor support
- ✅ Tailwind CSS utility-first styling
- ✅ Responsive design
- ✅ Modern, clean UI
- ✅ SEO-friendly structure
- ✅ Customizable header and footer
- ✅ Support for custom menus
- ✅ Widget-ready sidebar
- ✅ Post thumbnails support
- ✅ Custom logo support

## Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher
- Node.js 16.x or higher
- npm or yarn

## Installation

### 1. Install Theme Files

Copy the theme folder to your WordPress themes directory:

```bash
wp-content/themes/main/
```

### 2. Install Dependencies

Navigate to the theme directory and install npm dependencies:

```bash
cd wp-content/themes/main
npm install
```

This will install:
- Tailwind CSS
- PostCSS
- Autoprefixer
- @tailwindcss/typography plugin

### 3. Build Assets

Compile the CSS and JavaScript files:

```bash
npm run build
```

This will:
- Compile Tailwind CSS from `src/css/style.css` to `dist/style.css`
- Copy JavaScript from `src/js/main.js` to `dist/script.js`

### 4. Activate Theme

1. Log in to your WordPress admin panel
2. Navigate to **Appearance > Themes**
3. Find "Main" theme and click **Activate**

## Development

### Watch Mode (Development)

To automatically rebuild CSS when you make changes:

```bash
npm run dev
```

Or watch CSS only:

```bash
npm run watch:css
```

### Production Build

For production, use the build command with minification:

```bash
npm run build
```

## Project Structure

```
main/
├── dist/                    # Compiled assets (generated)
│   ├── style.css           # Compiled Tailwind CSS
│   └── script.js           # Compiled JavaScript
├── src/                    # Source files
│   ├── css/
│   │   └── style.css       # Tailwind CSS source
│   └── js/
│       └── main.js         # JavaScript source
├── template-parts/         # Reusable template parts
│   ├── content.php
│   ├── content-card.php
│   ├── content-none.php
│   └── content-search.php
├── 404.php                 # 404 error page
├── archive.php             # Archive template
├── comments.php            # Comments template
├── footer.php              # Footer template
├── front-page.php          # Homepage template
├── functions.php           # Theme functions
├── header.php              # Header template
├── index.php               # Main template
├── page.php                # Page template
├── search.php              # Search results template
├── searchform.php          # Search form template
├── single.php              # Single post template
├── style.css               # Theme stylesheet (required by WordPress)
├── package.json            # npm dependencies
├── tailwind.config.js      # Tailwind configuration
├── postcss.config.js       # PostCSS configuration
└── README.md               # This file
```

## Using Tailwind CSS

This theme uses Tailwind CSS utility classes throughout. You can use Tailwind classes directly in:

- PHP template files
- Gutenberg block editor (via custom HTML blocks)
- Widget content
- Post/page content (via custom HTML blocks)

### Adding Custom Tailwind Classes

To add custom Tailwind utilities or components, edit `src/css/style.css`:

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

@layer components {
  .custom-button {
    @apply bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700;
  }
}
```

Then rebuild:

```bash
npm run build:css
```

### Tailwind Configuration

Customize Tailwind in `tailwind.config.js`:

```javascript
module.exports = {
  content: [
    './**/*.php',
    './template-parts/**/*.php',
    './src/**/*.{js,jsx,ts,tsx}',
  ],
  theme: {
    extend: {
      colors: {
        // Add custom colors
      },
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
  ],
}
```

## Theme Customization

### Menus

The theme supports two menu locations:

1. **Primary Menu** - Main navigation in header
2. **Footer Menu** - Footer navigation

To set up menus:

1. Go to **Appearance > Menus**
2. Create a new menu or select an existing one
3. Assign it to "Primary Menu" or "Footer Menu" location

### Widgets

The theme includes one widget area:

- **Sidebar** - Appears on archive and single post pages

To add widgets:

1. Go to **Appearance > Widgets**
2. Add widgets to the "Sidebar" area

### Custom Logo

To add a custom logo:

1. Go to **Appearance > Customize > Site Identity**
2. Click "Select Logo"
3. Upload your logo image

### Homepage Settings

The homepage displays:

- **Hero Section** - Heading and description
- **Most Popular** - Sticky posts
- **Latest Guides** - Recent posts
- **Categories** - Category list

To make posts sticky (appear in "Most Popular"):

1. Edit a post
2. In the "Publish" meta box, check "Stick this post to the front page"

## Template Hierarchy

The theme follows WordPress template hierarchy:

- `front-page.php` - Homepage
- `single.php` - Single posts
- `page.php` - Pages
- `archive.php` - Archives (categories, tags, dates)
- `search.php` - Search results
- `404.php` - 404 error page
- `index.php` - Fallback template

## Gutenberg Support

The theme fully supports the Gutenberg block editor with:

- Wide and full-width alignments
- Block styles
- Responsive embeds
- Custom color palette
- Custom font sizes
- Editor styles

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Troubleshooting

### Styles Not Loading

1. Make sure you've run `npm install`
2. Run `npm run build` to compile assets
3. Clear your browser cache
4. Check that `dist/style.css` exists and has content

### JavaScript Not Working

1. Make sure you've run `npm run build`
2. Check browser console for errors
3. Verify `dist/script.js` exists

### Tailwind Classes Not Working

1. Make sure the class is included in `tailwind.config.js` content paths
2. Rebuild CSS: `npm run build:css`
3. Check that the class exists in Tailwind (refer to [Tailwind CSS docs](https://tailwindcss.com/docs))

## Development Workflow

1. Make changes to PHP templates or source files
2. If changing CSS, edit `src/css/style.css`
3. If changing JS, edit `src/js/main.js`
4. Run `npm run build` to compile
5. Test in browser
6. For active development, use `npm run dev` to watch for changes

## Contributing

When contributing to this theme:

1. Follow WordPress coding standards
2. Use Tailwind utility classes instead of custom CSS when possible
3. Test in multiple browsers
4. Ensure responsive design works on mobile devices
5. Update this README if adding new features

## License

This theme is licensed under the GPL v2 or later.

```
Copyright (C) 2024

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
```

## Support

For issues, questions, or contributions, please refer to the project documentation or contact the development team.

## Changelog

### 1.0.0
- Initial release
- Gutenberg block editor support
- Tailwind CSS integration
- Responsive design
- Custom menus and widgets
- Homepage template with sections
- Archive and single post templates

