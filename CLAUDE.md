# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a PHP web application for adding watermarks to PDF documents during printing. The application allows users to:
- Upload PDF files
- Add customizable text watermarks with various styling options
- Preview watermarked pages
- Print with watermarks applied (both single page test prints and full document)
- Save and manage watermark presets

## Architecture

**Single-file application**: The entire application is contained in `index.php` which includes:
- HTML structure and UI components
- CSS styling with responsive design and print media queries
- JavaScript functionality using PDF.js for PDF rendering and manipulation

**Key Components**:
- **PDF.js Integration**: Uses PDF.js CDN (v3.11.174) for client-side PDF processing
- **Control Panel**: Form-based interface for watermark configuration
- **Preview Panel**: Real-time preview with draggable watermark positioning
- **Print System**: Dedicated print area with orientation support
- **Preset Management**: LocalStorage-based saving system for watermark configurations

**Core JavaScript Functions**:
- `generatePreview()`: Renders PDF pages with watermarks for preview
- `createWatermark()`: Creates draggable watermark overlays
- `preparePrintPages()`: Generates print-ready pages with scaled watermarks
- `savePreset()`/`loadPreset()`: Manages watermark configuration presets

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

**Watermark Configuration**:
- Text content, font family, size, color, rotation, opacity
- Optional background with color and opacity
- Preset position options plus manual drag-and-drop positioning
- Page orientation (portrait/landscape) affects both preview and printing

**Print System**:
- Test print (current page only)
- Full document printing
- CSS print styles for proper page sizing
- Landscape/portrait orientation support

**Preset System**:
- Save/load watermark configurations
- LocalStorage persistence
- Visual preset preview with key settings displayed
- ## Thai Language Support

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