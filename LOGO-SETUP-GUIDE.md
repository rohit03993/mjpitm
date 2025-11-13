# Logo Setup Guide

## Where to Place Logo Images

Place your logo images in the following directory:
```
crm/public/images/logos/
```

## File Names

- **Tech Institute Logo**: `MJPITM.png` (or `.jpg`, `.svg`)
- **Paramedical Institute Logo**: `MJPIPS.png` (or `.jpg`, `.svg`)

## Supported Formats

- PNG (Recommended for logos with transparency)
- JPG (For logos without transparency)
- SVG (Recommended for scalable vector logos)

## Steps to Add Logos

1. **Prepare your logo images**:
   - Recommended size: 200x200px to 400x400px for PNG/JPG
   - SVG files can be any size (they scale automatically)
   - Ensure good quality and readable at small sizes

2. **Place the images**:
   - Copy `MJPITM.png` (or `.jpg`, `.svg`) to `crm/public/images/logos/`
   - Copy `MJPIPS.png` (or `.jpg`, `.svg`) to `crm/public/images/logos/`

3. **Verify the images**:
   - The logos will automatically appear in the navigation bar and footer
   - If images are not found, the system will fall back to the default Font Awesome icons

## File Structure

```
crm/
└── public/
    └── images/
        └── logos/
            ├── MJPITM.png (or .jpg, .svg)
            └── MJPIPS.png (or .jpg, .svg)
```

## Notes

- The system checks for images in this order: `.png` → `.jpg` → `.svg`
- If no image is found, it will display the default Font Awesome icon
- Logo images are displayed in:
  - Navigation bar (header)
  - Footer section
- Logo height in navigation: `h-12` (48px)
- Logo height in footer: `h-8` (32px)
- Images are automatically scaled to maintain aspect ratio

## Sharing Images

You can share your logo images with me in this chat, and I'll help you:
1. Place them in the correct directory
2. Verify they're working correctly
3. Adjust sizes if needed

Simply attach or share the logo images in your next message!

