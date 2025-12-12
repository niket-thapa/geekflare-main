# Hero Banner Block

A customizable Gutenberg block for displaying a hero banner with search functionality.

## Features

- **Editable Heading** - Rich text editor for the main heading
- **Editable Description** - Rich text editor for the description text
- **Hero Image Upload** - Upload and manage hero icon/image
- **Search Form** - Functional search form integrated with WordPress search
- **Search Suggestions** - Add/remove search suggestion buttons (manageable in block settings)
- **Customizable** - All text and settings can be customized via block sidebar

## Usage

1. In the WordPress editor, click the "+" button to add a block
2. Search for "Hero Banner" or find it in the "Main" category
3. Click to add the block
4. Customize the content using the block editor:
   - Edit heading and description directly in the block
   - Upload hero image via the image upload button
   - Add/edit search suggestions in the block sidebar (Inspector Controls)
   - Customize search placeholder and button text in the sidebar

## Block Settings

In the block sidebar (Inspector Controls), you can:

- **Search Settings Panel**:
  - Search Placeholder text
  - Search Button text

- **Search Suggestions Panel**:
  - Add new suggestions
  - Edit existing suggestions
  - Remove suggestions

## Building the Block

If you need to modify the block editor JavaScript:

1. Install dependencies (if not already installed):
   ```bash
   npm install
   ```

2. The block uses WordPress's built-in block registration. The JavaScript will be loaded automatically.

Note: For advanced customization, you may need to set up @wordpress/scripts for building JSX/ES6 code.

