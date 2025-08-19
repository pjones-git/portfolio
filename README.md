# Website Content Management System

A comprehensive form-based content management system for the HTML5UP Miniport template. This system allows you to easily customize all aspects of your website through a user-friendly web interface.

## 🚀 Features

- **Visual Content Editor**: Easy-to-use web form for editing all website content
- **Dynamic Image Upload**: Upload and manage profile and portfolio images
- **Live Preview**: See your changes before publishing
- **Backup System**: Automatic backups with restore functionality
- **Mobile Responsive**: Admin interface works on all devices
- **Template System**: Clean separation of content and presentation

## 📁 File Structure

```
miniport/
├── admin.html              # Main content editor interface
├── index.html              # Generated website (auto-updated)
├── index-template.html     # Template with placeholders
├── content-config.json     # Website content configuration
├── update-content.php      # Backend processing script
├── backup-content.php      # Backup management script
├── preview.html            # Live preview functionality
├── uploads/                # Uploaded images directory
├── backups/                # Automatic backups directory
├── assets/                 # Original theme assets
│   ├── css/
│   ├── js/
│   └── ...
└── images/                 # Original theme images
```

## 🛠️ Setup Instructions

### Prerequisites

- Web server with PHP support (Apache, Nginx, etc.)
- PHP 7.0 or higher
- PHP GD extension (for image processing)
- Write permissions on the website directory

### Installation

1. **Upload Files**: Place all files in your web server directory
2. **Set Permissions**: Ensure the following directories are writable:
   ```bash
   chmod 755 uploads/
   chmod 755 backups/
   chmod 644 content-config.json
   ```
3. **Access Admin Panel**: Open `admin.html` in your web browser
4. **Initial Setup**: The system will automatically create necessary directories

### First Time Setup

1. Open `admin.html` in your web browser
2. The form will be populated with the current website content
3. Customize the content as needed
4. Click "Save Changes" to apply your modifications
5. Use "Preview Site" to see how it will look
6. The main `index.html` file is automatically updated

## 📝 Usage Guide

### Content Sections

**Site Settings**
- Site title and meta description
- Appears in browser tabs and search results

**Personal Information**
- Your name and personal tagline/bio
- Profile image upload
- Call-to-action button text

**Work/Services Section**
- Section title and subtitle
- Up to 6 services with icons, titles, and descriptions
- Footer text and call-to-action

**Portfolio Section**
- Section title and subtitle
- Multiple portfolio items with images, titles, descriptions, and links
- Image upload for each portfolio item

**Contact Section**
- Section title and subtitle
- Contact form action URL
- Social media links with custom icons

**Navigation**
- Customizable menu items
- Internal links (e.g., #section) or external URLs

**Footer/Copyright**
- Copyright text
- Design credits

### Image Management

**Supported Formats**: JPG, PNG, GIF, WebP
**Maximum Size**: 5MB per image
**Auto-Resize**: Images are automatically resized to optimize loading

**Profile Images**: Recommended 400x400 pixels
**Portfolio Images**: Recommended 600x400 pixels

### Backup System

**Automatic Backups**: Created every time you save changes
**Manual Backups**: Click "Backup Current" to create on-demand backups
**Backup Location**: `backups/` directory
**Backup Types**: 
- Config-only backups (.json)
- Full backups (.zip) - includes images and HTML

### Preview System

- **Live Preview**: Click "Preview Site" to see changes before publishing
- **Auto-Refresh**: Preview updates automatically every 30 seconds
- **Fullscreen Mode**: Use fullscreen toggle for better viewing
- **Keyboard Shortcuts**: 
  - Ctrl/Cmd + R: Refresh preview
  - Ctrl/Cmd + F: Toggle fullscreen

## 🔧 Advanced Configuration

### Custom Icons

Use FontAwesome icon classes for services and social links:
- Examples: `fa-comments`, `fa-camera-retro`, `fa-twitter`
- Add `solid` checkbox for solid style icons
- Full icon list: [FontAwesome Icons](https://fontawesome.com/icons)

### Contact Form Integration

Set the contact form action to:
- **Email**: `mailto:your@email.com`
- **PHP Script**: `contact.php` (create your own handler)
- **Third-party Service**: URL to your form processing service

### Color Customization

Edit `assets/css/main.css` to customize colors:
- Primary color: `#4acaa8`
- Text color: `#555`
- Background colors in `.wrapper.style1`, `.style2`, etc.

## 🛡️ Security Notes

- Keep backup files secure and don't expose them publicly
- The `uploads/` directory should not allow PHP execution
- Consider adding `.htaccess` rules to protect admin files
- Regularly update PHP and server software

## 🐛 Troubleshooting

### Common Issues

**Images not uploading**
- Check file permissions on `uploads/` directory
- Verify PHP upload limits in `php.ini`
- Ensure GD extension is installed

**Changes not saving**
- Check write permissions on all files
- Verify PHP error logs for specific errors
- Ensure all required directories exist

**Preview not loading**
- Check that `content-config.json` is readable
- Verify `index-template.html` exists
- Check browser console for JavaScript errors

### Error Messages

- **"Failed to upload image"**: File size too large or invalid format
- **"Failed to write configuration"**: Permission issues
- **"Invalid JSON"**: Corrupt configuration file (restore from backup)

## 📞 Support

For issues or questions:
1. Check the browser console for error messages
2. Review PHP error logs
3. Verify file permissions and directory structure
4. Test with a minimal content configuration

## 📄 License

This content management system is provided as-is. The original HTML5UP Miniport template is licensed under CCA 3.0.

---

**Happy editing!** 🎉
