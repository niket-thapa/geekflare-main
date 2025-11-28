# Header Implementation Guide

## Overview

The new header has been fully implemented with dynamic WordPress functionality, customization options, and full Gutenberg block support.

## Features Implemented

### 1. **Dynamic WordPress Integration**
- ✅ Custom logo support (via WordPress Customizer)
- ✅ Dynamic navigation menu (WordPress menu system)
- ✅ Search functionality (WordPress search)
- ✅ Language menu support (ready for WPML/Polylang)
- ✅ Customizable Products button

### 2. **Customization Options**
All options available in **Appearance > Customize > Header Settings**:

- **Show Search**: Toggle search icon/functionality
- **Show Language Menu**: Toggle language selector
- **Products Button URL**: Set the Products button link
- **Products Button Text**: Customize button text
- **Products Button Icon**: Upload custom icon image

### 3. **Responsive Design**
- ✅ Mobile menu with hamburger icon
- ✅ Desktop dropdown menus
- ✅ Mobile submenu accordion
- ✅ Responsive search form
- ✅ Touch-friendly interactions

### 4. **JavaScript Functionality**
- ✅ Mobile menu toggle
- ✅ Search form toggle
- ✅ Language menu dropdown
- ✅ Submenu accordion (mobile)
- ✅ Click outside to close
- ✅ Keyboard navigation (Escape key)
- ✅ Auto-close on window resize

### 5. **CSS Styling**
- ✅ Custom eva-prime color palette
- ✅ Container-1216 max-width
- ✅ Smooth transitions and animations
- ✅ Hover effects
- ✅ Active states
- ✅ Backdrop blur effects

## File Structure

```
wp-content/themes/main/
├── header.php                    # Main header template
├── inc/
│   └── class-walker-nav-menu.php # Custom menu walker
├── src/
│   ├── css/
│   │   └── style.css            # Header styles
│   └── js/
│       └── main.js              # Header JavaScript
└── functions.php                 # Customizer settings
```

## Setup Instructions

### 1. Create a Menu

1. Go to **Appearance > Menus**
2. Create a new menu or select existing
3. Add menu items
4. For dropdown menus, create parent items and add children underneath
5. Assign menu to "Primary Menu" location

### 2. Set Logo

1. Go to **Appearance > Customize > Site Identity**
2. Click "Select Logo"
3. Upload your logo image
4. Recommended size: 156px width (or use SVG)

### 3. Customize Header

1. Go to **Appearance > Customize > Header Settings**
2. Configure:
   - Show/Hide search
   - Show/Hide language menu
   - Products button URL
   - Products button text
   - Products button icon

### 4. Language Menu (Optional)

If using WPML or Polylang:
- The language menu will automatically populate
- Upload flag images via theme customizer
- Set language flags for each language code

## Customization

### Colors

The header uses the `eva-prime` color palette defined in `tailwind.config.js`:

```javascript
'eva-prime': {
  50: '#FFF5F2',
  100: '#FFE8E0',
  // ... up to 900
  600: '#FF4D1A', // Primary color
}
```

### Container Width

The header uses a custom container width of 1216px:

```css
.container-1216 {
  max-width: 1216px;
  margin: 0 auto;
  padding: 0 1rem;
}
```

### Menu Structure

The menu walker automatically:
- Adds `menu-item-has-children` class for items with submenus
- Creates proper submenu structure
- Handles active/current menu items
- Supports unlimited nesting levels

## Block Editor Support

The header is fully compatible with Gutenberg blocks:

- ✅ Custom HTML blocks can be used in menu items
- ✅ Block patterns can reference header styles
- ✅ Theme.json colors are available
- ✅ Custom CSS classes work in blocks

## Browser Support

- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Mobile browsers

## Troubleshooting

### Menu Not Showing
- Check that a menu is assigned to "Primary Menu" location
- Verify menu has items added
- Clear browser cache

### Search Not Working
- Check "Show Search" is enabled in Customizer
- Verify JavaScript is loaded (check browser console)
- Ensure `dist/script.js` exists

### Styles Not Applied
- Run `npm run build` to compile CSS
- Clear browser cache
- Check that `dist/style.css` exists and has content

### Mobile Menu Not Toggling
- Check JavaScript console for errors
- Verify `mobile_menu_toggle` button exists
- Ensure body class toggle is working

## Future Enhancements

Potential additions:
- [ ] Sticky header option
- [ ] Header transparency option
- [ ] Custom header layouts
- [ ] Mega menu support
- [ ] Breadcrumb integration
- [ ] Notification bar

## Notes

- The header uses Tailwind utility classes throughout
- All custom styles are in `src/css/style.css`
- JavaScript is vanilla JS (no dependencies)
- Fully accessible (ARIA labels, keyboard navigation)
- SEO-friendly (semantic HTML)

