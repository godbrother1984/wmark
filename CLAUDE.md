# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a PHP web application for adding multiple types of watermarks to PDF documents during printing. The application allows users to:
- Upload PDF files
- Add customizable text and/or image watermarks simultaneously
- Use basic mode for simple watermarking across entire document
- Use advanced mode for per-page watermark customization
- Preview watermarked pages with real-time updates
- Print with watermarks applied (both single page test prints and full document)
- Save and manage watermark presets with full configuration support

## Architecture

**Single-file application**: The entire application is contained in `index.php` which includes:
- HTML structure and UI components
- CSS styling with responsive design and print media queries
- JavaScript functionality using PDF.js for PDF rendering and manipulation

**Key Components**:
- **PDF.js Integration**: Uses PDF.js CDN (v3.11.174) for client-side PDF processing
- **Control Panel**: Form-based interface for watermark configuration with multiple modes
- **Preview Panel**: Real-time preview with draggable watermark positioning
- **Print System**: Dedicated print area with orientation support
- **Preset Management**: LocalStorage-based saving system for watermark configurations
- **Advanced Page Management**: Per-page watermark configuration system

**Core JavaScript Functions**:
- `generatePreview()`: Renders PDF pages with watermarks for preview (supports mixed watermarks)
- `createWatermark()`: Creates draggable watermark overlays (text and/or images)
- `createAdvancedWatermark()`: Handles per-page watermark rendering
- `preparePrintPages()`: Generates print-ready pages with scaled watermarks
- `savePreset()`/`loadPreset()`: Manages watermark configuration presets
- `updatePageWatermarksList()`: Manages advanced mode page list
- `editPageWatermark()`: Modal editor for individual page settings

## Development Environment

**Web Server**: Designed to run on Apache/XAMPP stack
- Place in XAMPP htdocs directory
- Access via `http://localhost/wmark/`

**Dependencies**:
- PDF.js library (loaded via CDN)
- Modern browser with HTML5 Canvas support
- LocalStorage for preset management

**File Structure**: Single-file architecture means all modifications go into `index.php`

## Key Features

### **Watermark Modes**

**Basic Mode** (Default):
- Toggle text watermarks on/off independently
- Toggle image watermarks on/off independently
- Both types can be used simultaneously
- Same settings apply to entire document
- Simple drag-and-drop positioning

**Advanced Mode**:
- Per-page watermark customization
- Each page can have different combinations:
  - Text only
  - Image only  
  - Both text and image
  - No watermarks
- Individual positioning for each page
- Modal editor for detailed page settings
- Copy settings to all pages functionality

### **Watermark Configuration**

**Text Watermarks**:
- Text content, font family, size, color, rotation, opacity
- Optional background with color and opacity
- Preset position options plus manual drag-and-drop positioning

**Image Watermarks**:
- File upload support (PNG, JPG, etc.)
- Customizable width and height
- Opacity and rotation controls
- Drag-and-drop positioning

**Print System**:
- Test print (current page only)
- Full document printing
- CSS print styles for proper page sizing
- Landscape/portrait orientation support
- Per-page watermark rendering in advanced mode

**Preset System**:
- Save/load complete watermark configurations
- LocalStorage persistence with backward compatibility
- Support for both basic and advanced mode settings
- Visual preset preview with key settings displayed
- Automatic migration from legacy presets

## Usage Instructions

### **Basic Mode Usage**:
1. Upload PDF file
2. Check "เปิดใช้งานข้อความ Watermark" for text watermarks
3. Check "เปิดใช้งานรูปภาพ Watermark" for image watermarks  
4. Configure settings (text, image, styling options)
5. Use drag-and-drop to position watermarks
6. Preview and print

### **Advanced Mode Usage**:
1. Upload PDF file
2. Enable desired basic watermark types (optional)
3. Check "โหมดขั้นสูง - จัดการแต่ละหน้าแยกกัน"
4. Click "⚙️ แก้ไข" for each page to customize
5. Use modal editor to set text/image for each page
6. Drag watermarks to adjust positions per page
7. Use "📋 คัดลอก" to copy settings to all pages
8. Preview and print

## Thai Language Support

- **Primary Language**: All UI text in Thai
- **Font**: Sarabun (Google Fonts)
- **Date Format**: Thai Buddhist calendar support (พ.ศ.)
- **Number Format**: Thai locale formatting
- **Form Labels**: Use Thai with English equivalents in placeholders

## Development Guidelines

- **Professional Design**: Clean, elegant, modern interface - avoid flashy colors, maintain professional appearance
- **Thai Communication**: Use Thai language for all communication
- **File Headers**: Every modified file must include path, version, date, and time
- **No Unnecessary Changes**: Never modify UX/UI or functions unrelated to the current task
- **Documentation-Driven**: Development must follow and align with existing documentation
- **User-Friendly**: Optimize for ease of use by employees
- **Modern Frameworks**: Use contemporary tools and frameworks when appropriate