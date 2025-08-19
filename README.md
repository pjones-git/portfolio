# Portfolio Website

A modern, responsive portfolio website built with the Miniport template by HTML5 UP, featuring a custom admin interface for easy content management.

## 🌐 Live Website

Your portfolio will be hosted at: **https://pjones-git.github.io/portfolio/**

## ✨ Features

- **Responsive Design**: Mobile-friendly layout that looks great on all devices
- **Admin Interface**: Easy-to-use content management system (for local development)
- **Dynamic Content**: Update your portfolio without editing HTML
- **Professional Layout**: Clean, modern design perfect for showcasing work
- **GitHub Pages Hosting**: Automatic deployment with GitHub Actions
- **SEO Optimized**: Meta tags and semantic HTML structure

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

## 🚀 GitHub Pages Hosting Setup

### Enable GitHub Pages (Quick Setup)

1. **Go to your repository**: https://github.com/pjones-git/portfolio
2. **Click "Settings"** tab (in the repository navigation)
3. **Scroll to "Pages"** (in the left sidebar)
4. **Under "Source"**, select:
   - **Source**: Deploy from a branch
   - **Branch**: main
   - **Folder**: / (root)
5. **Click "Save"**

### Your Live Website

After enabling GitHub Pages, your website will be available at:
**https://pjones-git.github.io/portfolio/**

### Updating Your Live Website

**Method 1: Direct Edit (Recommended for GitHub Pages)**
1. Edit `index.html` directly in your repository (via GitHub web interface or locally)
2. Commit and push changes to the main branch
3. GitHub Pages will automatically deploy the updates

**Method 2: Local Development with Admin Interface**
1. Clone the repository locally
2. Use the PHP admin interface (see Local Development section below)
3. Push the updated `index.html` to GitHub

## 🛠️ Local Development Setup

### Prerequisites (For Admin Interface)

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
