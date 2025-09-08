<?php
/*
 * File: C:\XAMPP\htdocs\wmark\index.php
 * Version: 3.0.0
 * Date: 2025-01-18
 * Time: 16:45:00
 * Description: Advanced PDF Watermark Application with Mixed Content Support
 * Major Features:
 * - Support for simultaneous text and image watermarks
 * - Basic mode: Simple toggle-based watermark management
 * - Advanced mode: Per-page customization with individual positioning
 * - Modal-based page editor with drag-and-drop functionality
 * - Backward compatibility with existing presets
 * - Enhanced print system supporting mixed watermarks
 */
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แอปพิมพ์ PDF พร้อม Watermark</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .main-content {
            display: grid;
            grid-template-columns: 450px 1fr;
            gap: 30px;
            padding: 30px;
            min-height: 85vh;
        }

        .control-panel {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            height: fit-content;
            overflow-y: auto;
            max-height: 85vh;
        }

        .preview-panel {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            position: relative;
        }

        .upload-section {
            border: 3px dashed #e1e5e9;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            margin-bottom: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .upload-section:hover,
        .upload-section.dragover {
            border-color: #4facfe;
            background: #f0f8ff;
            transform: scale(1.02);
        }

        .upload-icon {
            font-size: 2.5rem;
            color: #4facfe;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #4facfe;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .color-input {
            height: 40px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .range-display {
            font-size: 0.85rem;
            color: #666;
            font-weight: 500;
            text-align: center;
            margin-top: 3px;
            background: #f0f8ff;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .btn {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin: 8px 0;
        }

        .btn:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.3);
        }

        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }

        .section-divider {
            border-top: 2px solid #e1e5e9;
            margin: 25px 0;
            padding-top: 20px;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin: 0;
        }

        .note {
            font-size: 0.8rem;
            color: #0066cc;
            margin-top: 5px;
            padding: 6px 10px;
            background: #e8f4fd;
            border-radius: 5px;
            border-left: 3px solid #4facfe;
        }

        .preview-container {
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            min-height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            position: relative;
            overflow: auto;
            background: white;
        }

        .pdf-page {
            margin: 20px auto;
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            position: relative;
            display: inline-block;
            transition: all 0.3s ease;
            border-radius: 4px;
            overflow: hidden;
        }

        .pdf-canvas {
            display: block;
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }

        .watermark-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            z-index: 10;
        }

        .watermark-text {
            position: absolute;
            white-space: nowrap;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            transform-origin: center center;
            user-select: none;
            cursor: move;
            z-index: 20;
            pointer-events: auto;
            padding: 8px 16px;
            border-radius: 5px;
            transition: all 0.2s;
            min-width: 50px;
            text-align: center;
        }

        .watermark-text:hover {
            outline: 2px dashed #4facfe;
            outline-offset: 2px;
        }

        .watermark-text.dragging {
            cursor: grabbing;
            outline: 2px solid #4facfe !important;
            outline-offset: 4px;
            transform-origin: center center;
        }

        .navigation {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-btn {
            background: #6c757d;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background 0.2s;
        }

        .nav-btn:hover:not(:disabled) {
            background: #5a6268;
        }

        .nav-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .page-info {
            font-size: 0.9rem;
            color: #666;
            font-weight: 500;
        }

        .hidden {
            display: none;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-top: 15px;
            border: 1px solid #c3e6cb;
            font-size: 0.9rem;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-top: 15px;
            border: 1px solid #f5c6cb;
            font-size: 0.9rem;
        }

        /* Presets Styles */
        .presets-list {
            max-height: 280px;
            overflow-y: auto;
            border: 1px solid #e1e5e9;
            border-radius: 8px;
            padding: 8px;
            background: white;
        }

        .preset-item {
            background: #f8f9fa;
            border: 1px solid #e1e5e9;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .preset-item:hover {
            background: #e9ecef;
            border-color: #4facfe;
            transform: translateY(-1px);
        }

        .preset-item.active {
            background: #e3f2fd;
            border-color: #4facfe;
            box-shadow: 0 0 0 2px rgba(79, 172, 254, 0.2);
        }

        .preset-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .preset-name {
            font-weight: bold;
            color: #333;
            font-size: 0.95rem;
        }

        .preset-actions {
            display: flex;
            gap: 6px;
        }

        .preset-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 4px 6px;
            border-radius: 3px;
            transition: background 0.2s;
        }

        .preset-btn:hover {
            background: rgba(0,0,0,0.1);
        }

        .preset-preview {
            font-size: 0.8rem;
            color: #666;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .preset-detail {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .preset-color-box {
            width: 14px;
            height: 14px;
            border-radius: 2px;
            border: 1px solid #ddd;
        }

        .no-presets {
            text-align: center;
            color: #999;
            padding: 20px;
            font-style: italic;
        }

        .page-image-item {
            background: #f8f9fa;
            border: 1px solid #e1e5e9;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 8px;
            transition: all 0.2s ease;
        }

        .page-image-item:hover {
            background: #e9ecef;
            border-color: #4facfe;
        }

        .page-image-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .page-image-info {
            font-weight: bold;
            color: #333;
            font-size: 0.9rem;
        }

        .page-image-actions {
            display: flex;
            gap: 6px;
        }

        .page-image-btn {
            background: #4facfe;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 0.8rem;
            padding: 4px 8px;
            border-radius: 3px;
            transition: background 0.2s;
        }

        .page-image-btn:hover {
            background: #3a9efc;
        }

        .page-image-btn.danger {
            background: #dc3545;
        }

        .page-image-btn.danger:hover {
            background: #c82333;
        }

        .page-image-preview {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            color: #666;
        }

        .page-image-preview img {
            width: 30px;
            height: 30px;
            border-radius: 3px;
            border: 1px solid #ddd;
            object-fit: cover;
        }

        .position-indicator {
            background: #e3f2fd;
            color: #1976d2;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.75rem;
        }

        .page-watermark-item {
            background: #f8f9fa;
            border: 1px solid #e1e5e9;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 8px;
            transition: all 0.2s ease;
        }

        .page-watermark-item:hover {
            background: #e9ecef;
            border-color: #4facfe;
        }

        .page-watermark-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .page-watermark-info {
            font-weight: bold;
            color: #333;
            font-size: 0.9rem;
        }

        .page-watermark-actions {
            display: flex;
            gap: 6px;
        }

        .watermark-type-badges {
            display: flex;
            gap: 4px;
            margin-top: 8px;
        }

        .watermark-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.7rem;
            font-weight: bold;
        }

        .watermark-badge.text {
            background: #e3f2fd;
            color: #1976d2;
        }

        .watermark-badge.image {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .watermark-content-preview {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-top: 8px;
            font-size: 0.8rem;
            color: #666;
        }

        .watermark-content-preview img {
            width: 20px;
            height: 20px;
            border-radius: 2px;
            border: 1px solid #ddd;
            object-fit: cover;
        }

        .orientation-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* Print Styles */
        @media print {
            @page {
                margin: 0;
                size: A4;
            }
            
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            body {
                margin: 0;
                padding: 0;
                background: white;
            }
            
            body * {
                visibility: hidden;
            }
            
            .print-area,
            .print-area * {
                visibility: visible;
            }
            
            .print-area {
                position: absolute;
                top: 0;
                left: 0;
                margin: 0;
                padding: 0;
            }
            
            .print-area .pdf-page {
                margin: 0;
                padding: 0;
                border: none;
                box-shadow: none;
                position: relative;
                page-break-after: always;
                page-break-inside: avoid;
                width: 210mm;
                height: 297mm;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .print-area .pdf-page:last-child {
                page-break-after: avoid;
            }
            
            .print-area .pdf-page.landscape {
                width: 297mm;
                height: 210mm;
            }
            
            .print-area .pdf-canvas {
                max-width: 100%;
                max-height: 100%;
                width: auto;
                height: auto;
                object-fit: contain;
            }
            
            .print-area .watermark-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                pointer-events: none;
            }
            
            .print-area .watermark-text {
                position: absolute !important;
                white-space: nowrap !important;
                font-weight: bold !important;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.3) !important;
                transform-origin: center center !important;
                pointer-events: none !important;
                /* สำคัญ: ให้ inline style transform ทำงานได้ */
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            .orientation-indicator {
                display: none;
            }
        }

        @media (max-width: 1200px) {
            .main-content {
                grid-template-columns: 1fr;
            }
            
            .control-panel {
                max-height: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🖨️ PDF Print with Watermark</h1>
            <p>พิมพ์เอกสาร PDF พร้อม Watermark โดยไม่แก้ไขไฟล์ต้นฉบับ</p>
        </div>
        
        <div class="main-content">
            <div class="control-panel">
                <!-- Upload Section -->
                <div class="upload-section">
                    <div class="upload-icon">📄</div>
                    <h3>อัปโหลดไฟล์</h3>
                    <p>คลิกเพื่อเลือกไฟล์ PDF หรือรูปภาพ</p>
                    <input type="file" id="pdfFile" accept=".pdf,image/*" style="display: none;">
                </div>

                <!-- Settings Section -->
                <div id="configSection" class="hidden">
                    <div class="section-title">🎨 ตั้งค่า Watermark</div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="enableTextWatermark" checked>
                            <label for="enableTextWatermark">เปิดใช้งานข้อความ Watermark</label>
                        </div>
                    </div>

                    <div id="textWatermarkSection">
                        <div class="form-group">
                            <label for="watermarkText">ข้อความ Watermark:</label>
                            <input type="text" id="watermarkText" class="form-control" 
                                   value="เอกสารลับ - CONFIDENTIAL" 
                                   placeholder="พิมพ์ข้อความที่ต้องการ">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="enableImageWatermark">
                            <label for="enableImageWatermark">เปิดใช้งานรูปภาพ Watermark</label>
                        </div>
                    </div>

                    <div id="imageWatermarkSection" style="display: none;">
                        <div class="form-group">
                            <label for="watermarkImage">รูปภาพ Watermark (ใช้ทั้งเอกสาร):</label>
                            <input type="file" id="watermarkImage" class="form-control" 
                                   accept="image/*" placeholder="เลือกรูปภาพ">
                            <div class="range-display" id="imagePreview" style="display: none;">
                                <img id="imagePreviewImg" style="max-width: 100px; max-height: 60px; margin-top: 10px;">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="enableAdvancedMode">
                            <label for="enableAdvancedMode">โหมดขั้นสูง - จัดการแต่ละหน้าแยกกัน</label>
                        </div>
                        <div class="note">
                            💡 เปิดใช้งานเพื่อกำหนดข้อความและรูปภาพแยกกันในแต่ละหน้า PDF
                        </div>
                    </div>
                        
                    <div id="advancedModeSection" class="hidden">
                        <div class="form-group">
                            <label>การจัดการ Watermark แต่ละหน้า:</label>
                            <div id="pageWatermarksList" class="presets-list" style="max-height: 300px;">
                                <div class="no-presets">อัพโหลด PDF ก่อนเพื่อดูรายการหน้า</div>
                            </div>
                        </div>
                    </div>

                    <div id="textControls">
                        <div class="form-group">
                            <label for="fontFamily">ฟอนต์:</label>
                            <select id="fontFamily" class="form-control">
                                <option value="Arial, sans-serif">Arial</option>
                                <option value="Times New Roman, serif">Times New Roman</option>
                                <option value="Helvetica, sans-serif">Helvetica</option>
                                <option value="Georgia, serif">Georgia</option>
                                <option value="Verdana, sans-serif">Verdana</option>
                                <option value="Courier New, monospace">Courier New</option>
                                <option value="Impact, fantasy">Impact</option>
                                <option value="Comic Sans MS, cursive">Comic Sans MS</option>
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="watermarkColor">สีข้อความ:</label>
                                <input type="color" id="watermarkColor" class="form-control color-input" value="#ff4757">
                            </div>
                            <div class="form-group">
                                <label for="watermarkSize">ขนาดตัวอักษร:</label>
                                <input type="range" id="watermarkSize" class="form-control" 
                                       min="20" max="150" value="48" oninput="updateRangeDisplay('watermarkSize', 'px')">
                                <div class="range-display" id="watermarkSizeDisplay">48px</div>
                            </div>
                        </div>
                    </div>

                    <div id="imageControls" style="display: none;">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="imageWidth">ความกว้าง (px):</label>
                                <input type="range" id="imageWidth" class="form-control" 
                                       min="50" max="500" value="200" oninput="updateRangeDisplay('imageWidth', 'px')">
                                <div class="range-display" id="imageWidthDisplay">200px</div>
                            </div>
                            <div class="form-group">
                                <label for="imageHeight">ความสูง (px):</label>
                                <input type="range" id="imageHeight" class="form-control" 
                                       min="50" max="500" value="200" oninput="updateRangeDisplay('imageHeight', 'px')">
                                <div class="range-display" id="imageHeightDisplay">200px</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="rotationAngle">มุมเอียงข้อความ:</label>
                            <input type="range" id="rotationAngle" class="form-control" min="-45" max="45" value="0">
                            <div class="range-display" id="angleValue">0°</div>
                        </div>
                        <div class="form-group">
                            <label for="textOpacity">ความโปร่งใสข้อความ:</label>
                            <input type="range" id="textOpacity" class="form-control" min="0.1" max="1" step="0.1" value="0.7">
                            <div class="range-display" id="textOpacityValue">70%</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pageOrientation">การวางหน้ากระดาษ:</label>
                        <select id="pageOrientation" class="form-control">
                            <option value="portrait">📄 แนวตั้ง (Portrait)</option>
                            <option value="landscape">📑 แนวนอน (Landscape)</option>
                        </select>
                        <div class="note">
                            💡 ตัวอย่างจะแสดงตาม PDF ต้นฉบับ การตั้งค่านี้มีผลตอนพิมพ์เท่านั้น
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="enableBackground">
                            <label for="enableBackground">เปิดใช้งานพื้นหลัง</label>
                        </div>
                    </div>

                    <div id="backgroundControls" class="hidden">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="watermarkBg">สีพื้นหลัง:</label>
                                <input type="color" id="watermarkBg" class="form-control color-input" value="#000000">
                            </div>
                            <div class="form-group">
                                <label for="bgOpacity">ความโปร่งใสพื้นหลัง:</label>
                                <input type="range" id="bgOpacity" class="form-control" min="0.1" max="1" step="0.1" value="0.5">
                                <div class="range-display" id="bgOpacityValue">50%</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="watermarkPosition">ตำแหน่งเริ่มต้น:</label>
                        <select id="watermarkPosition" class="form-control">
                            <option value="center">กลางหน้า</option>
                            <option value="top">บนสุด</option>
                            <option value="bottom">ล่างสุด</option>
                            <option value="top-left">มุมบนซ้าย</option>
                            <option value="top-right">มุมบนขวา</option>
                            <option value="bottom-left">มุมล่างซ้าย</option>
                            <option value="bottom-right">มุมล่างขวา</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label style="color: #4facfe; font-weight: bold;">💡 คลิกและลากข้อความในตัวอย่างเพื่อปรับตำแหน่ง</label>
                    </div>

                    <button class="btn" id="previewBtn">👁️ แสดงตัวอย่าง</button>

                    <!-- Preset Management -->
                    <div class="section-divider">
                        <div class="section-title">💾 จัดการ Watermark Presets</div>
                        
                        <div class="form-group">
                            <label for="presetName">ชื่อ Preset:</label>
                            <input type="text" id="presetName" class="form-control" placeholder="เช่น: เอกสารลับ, ร่าง, สำเนา">
                        </div>

                        <div class="form-row">
                            <button class="btn btn-success" id="savePresetBtn">💾 บันทึก</button>
                            <button class="btn btn-danger" id="clearPresetsBtn">🗑️ ลบทั้งหมด</button>
                        </div>

                        <div class="form-group">
                            <label>Presets ที่บันทึกไว้:</label>
                            <div id="presetsList" class="presets-list">
                                <div class="no-presets">ยังไม่มี Presets ที่บันทึกไว้</div>
                            </div>
                        </div>
                    </div>

                    <!-- Print Buttons -->
                    <div class="section-divider">
                        <button class="btn btn-success" id="testPrintBtn" disabled>🔍 ทดสอบพิมพ์ (หน้าปัจจุบัน)</button>
                        <button class="btn btn-secondary" id="printBtn" disabled>🖨️ พิมพ์ทั้งหมด</button>
                    </div>

                    <div id="messageSection"></div>
                </div>
            </div>

            <div class="preview-panel">
                <div id="previewSection">
                    <div class="navigation hidden" id="navigation">
                        <button class="nav-btn" id="prevBtn">← ก่อนหน้า</button>
                        <span class="page-info" id="pageInfo">หน้า 1 จาก 1</span>
                        <button class="nav-btn" id="nextBtn">ถัดไป →</button>
                        <span style="margin-left: auto;">
                            <select id="zoomLevel" class="nav-btn" style="background: #6c757d;">
                                <option value="0.5">50%</option>
                                <option value="0.75">75%</option>
                                <option value="1" selected>100%</option>
                                <option value="1.25">125%</option>
                                <option value="1.5">150%</option>
                                <option value="2">200%</option>
                            </select>
                        </span>
                    </div>
                    
                    <div class="preview-container" id="previewContainer">
                        <div>📄 อัปโหลดไฟล์ PDF เพื่อดูตัวอย่าง</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Area -->
    <div class="print-area hidden" id="printArea"></div>

    <script>
        // PDF.js setup
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        // Global Variables
        let pdfDocument = null;
        let currentPage = 1;
        let totalPages = 0;
        let currentZoom = 1;
        let watermarkPosition = { x: 50, y: 50 };
        let isDragging = false;
        let dragOffset = { x: 0, y: 0 };
        let savedPresets = JSON.parse(localStorage.getItem('watermarkPresets')) || [];
        let activePresetId = null;
        
        // New watermark management system
        let isAdvancedMode = false;
        let pageWatermarks = {}; // { pageNumber: { text: {content, enabled, position}, image: {src, enabled, position} } }
        
        // Legacy support
        let pageImages = {}; // For backward compatibility

        // Initialize - ย้ายไปท้ายสุดหลังจาก define ฟังก์ชันทั้งหมด
        console.log('🎯 แอป PDF Watermark เริ่มทำงาน');

        function setupEventListeners() {
            // File upload
            setupFileUpload();
            
            // Drag and drop
            setupDragAndDrop();

            // Watermark type switching
            setupWatermarkControls();

            // Preview and controls
            document.getElementById('previewBtn').onclick = generatePreview;
            document.getElementById('watermarkText').oninput = updatePreview;
            document.getElementById('fontFamily').onchange = updatePreview;
            document.getElementById('watermarkColor').onchange = updatePreview;
            
            // Ranges
            document.getElementById('watermarkSize').oninput = () => {
                updateDisplay('watermarkSizeDisplay', document.getElementById('watermarkSize').value + 'px');
                updatePreview();
            };
            document.getElementById('rotationAngle').oninput = () => {
                updateDisplay('angleValue', document.getElementById('rotationAngle').value + '°');
                updatePreview();
            };
            document.getElementById('textOpacity').oninput = () => {
                updateDisplay('textOpacityValue', Math.round(document.getElementById('textOpacity').value * 100) + '%');
                updatePreview();
            };
            document.getElementById('bgOpacity').oninput = () => {
                updateDisplay('bgOpacityValue', Math.round(document.getElementById('bgOpacity').value * 100) + '%');
                updatePreview();
            };

            // Page orientation - อัปเดต indicator เมื่อเปลี่ยน
            document.getElementById('pageOrientation').onchange = () => {
                const orientation = document.getElementById('pageOrientation').value;
                console.log('🔄 เปลี่ยนการวางหน้ากระดาษเป็น:', orientation);
                showMessage(`ตั้งค่าการพิมพ์เป็น: ${orientation === 'portrait' ? 'แนวตั้ง' : 'แนวนอน'} (ตัวอย่างยังคงแสดงตาม PDF ต้นฉบับ)`, 'success');
                if (pdfDocument) generatePreview(); // อัปเดต indicator
            };

            // Background controls
            document.getElementById('enableBackground').onchange = () => {
                const checked = document.getElementById('enableBackground').checked;
                document.getElementById('backgroundControls').classList.toggle('hidden', !checked);
                updatePreview();
            };
            document.getElementById('watermarkBg').onchange = updatePreview;

            // Text watermark toggle
            document.getElementById('enableTextWatermark').onchange = () => {
                const checked = document.getElementById('enableTextWatermark').checked;
                const section = document.getElementById('textWatermarkSection');
                section.style.display = checked ? 'block' : 'none';
                updatePreview();
            };

            // Image watermark toggle  
            document.getElementById('enableImageWatermark').onchange = () => {
                const checked = document.getElementById('enableImageWatermark').checked;
                const section = document.getElementById('imageWatermarkSection');
                section.style.display = checked ? 'block' : 'none';
                updatePreview();
            };

            // Advanced mode toggle
            document.getElementById('enableAdvancedMode').onchange = () => {
                const checked = document.getElementById('enableAdvancedMode').checked;
                isAdvancedMode = checked;
                document.getElementById('advancedModeSection').classList.toggle('hidden', !checked);
                if (checked && pdfDocument) {
                    updatePageWatermarksList();
                }
                updatePreview();
            };

            // Position
            document.getElementById('watermarkPosition').onchange = () => {
                const position = document.getElementById('watermarkPosition').value;
                setPresetPosition(position);
                updatePreview();
            };

            // Navigation
            document.getElementById('prevBtn').onclick = () => changePage(-1);
            document.getElementById('nextBtn').onclick = () => changePage(1);
            document.getElementById('zoomLevel').onchange = () => {
                currentZoom = parseFloat(document.getElementById('zoomLevel').value);
                generatePreview();
            };

            // Print buttons
            document.getElementById('testPrintBtn').onclick = testPrint;
            document.getElementById('printBtn').onclick = printAll;

            // Presets
            document.getElementById('savePresetBtn').onclick = savePreset;
            document.getElementById('clearPresetsBtn').onclick = clearAllPresets;
        }

        function updateDisplay(elementId, value) {
            const element = document.getElementById(elementId);
            if (element) {
                element.textContent = value;
            } else {
                console.warn('⚠️ Element not found:', elementId);
            }
        }

        function setupFileUpload() {
            console.log('🔧 กำลัง setup file upload events');
            const uploadSection = document.querySelector('.upload-section');
            const fileInput = document.getElementById('pdfFile');
            
            if (!uploadSection || !fileInput) {
                console.error('❌ ไม่พบ upload section หรือ file input');
                return;
            }
            
            // Reset onclick event
            uploadSection.onclick = () => {
                console.log('📁 คลิกที่ upload section');
                fileInput.click();
            };
            fileInput.onchange = handleFileSelect;
            console.log('✅ Setup file upload events เรียบร้อย');
        }

        function setupDragAndDrop() {
            console.log('🔧 กำลัง setup drag and drop events');
            const uploadSection = document.querySelector('.upload-section');
            
            if (!uploadSection) {
                console.error('❌ ไม่พบ upload section สำหรับ drag and drop');
                return;
            }
            
            uploadSection.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadSection.classList.add('dragover');
                uploadSection.style.borderColor = '#4facfe';
                uploadSection.style.background = '#f0f8ff';
            });

            uploadSection.addEventListener('dragleave', (e) => {
                e.preventDefault();
                uploadSection.classList.remove('dragover');
                uploadSection.style.borderColor = '#e1e5e9';
                uploadSection.style.background = '';
            });

            uploadSection.addEventListener('drop', (e) => {
                console.log('📥 ไฟล์ถูกลากมาวาง');
                e.preventDefault();
                uploadSection.classList.remove('dragover');
                uploadSection.style.borderColor = '#e1e5e9';
                uploadSection.style.background = '';
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const file = files[0];
                    console.log('📁 ไฟล์ที่ลากมา:', file.name, 'ประเภท:', file.type);
                    
                    const isPDF = file.type === 'application/pdf';
                    const isImage = file.type.startsWith('image/');
                    
                    if (isPDF || isImage) {
                        // จำลอง event สำหรับ handleFileSelect
                        const mockEvent = { target: { files: [file] } };
                        handleFileSelect(mockEvent);
                    } else {
                        showMessage('กรุณาเลือกไฟล์ PDF หรือรูปภาพเท่านั้น', 'error');
                    }
                }
            });
            console.log('✅ Setup drag and drop events เรียบร้อย');
        }

        function setupWatermarkControls() {
            console.log('🔧 กำลัง setup watermark controls');
            
            // No longer needed - watermark type controls removed in new system
            // Controls are now handled by individual checkboxes
            
            // Image upload handler - check if element exists
            const watermarkImage = document.getElementById('watermarkImage');
            if (watermarkImage) {
                watermarkImage.onchange = function(event) {
                    const file = event.target.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.getElementById('imagePreviewImg');
                            img.src = e.target.result;
                            document.getElementById('imagePreview').style.display = 'block';
                            updatePreview();
                        };
                        reader.readAsDataURL(file);
                    } else {
                        document.getElementById('imagePreview').style.display = 'none';
                        showMessage('กรุณาเลือกไฟล์รูปภาพ', 'error');
                    }
                };
            }
            
            // Image size controls - check if elements exist
            const imageWidth = document.getElementById('imageWidth');
            const imageHeight = document.getElementById('imageHeight');
            if (imageWidth) imageWidth.oninput = updatePreview;
            if (imageHeight) imageHeight.oninput = updatePreview;
        }

        function selectFile() {
            document.getElementById('pdfFile').click();
        }

        async function handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            console.log('📁 เลือกไฟล์:', file.name, 'ประเภท:', file.type);

            // ตรวจสอบประเภทไฟล์
            const isPDF = file.type === 'application/pdf';
            const isImage = file.type.startsWith('image/');

            if (!isPDF && !isImage) {
                showMessage('กรุณาเลือกไฟล์ PDF หรือรูปภาพเท่านั้น', 'error');
                return;
            }

            try {
                // Reset สถานะทั้งหมด
                resetApplicationState();

                if (isPDF) {
                    await handlePDFFile(file);
                } else if (isImage) {
                    await handleImageFile(file);
                }
                
                updatePageInfo();
                generatePreview();
                
                if (isPDF) {
                    showMessage(`โหลดไฟล์ PDF สำเร็จ! (${totalPages} หน้า)`, 'success');
                } else {
                    showMessage(`โหลดไฟล์รูปภาพสำเร็จ!`, 'success');
                }

            } catch (error) {
                console.error('❌ Error loading file:', error);
                showMessage('ไม่สามารถโหลดไฟล์ได้: ' + error.message, 'error');
                resetApplicationState();
            }
        }

        async function handlePDFFile(file) {
            document.getElementById('previewContainer').innerHTML = '<div style="padding: 50px; text-align: center;">กำลังโหลดไฟล์ PDF...</div>';

            const arrayBuffer = await file.arrayBuffer();
            pdfDocument = await pdfjsLib.getDocument(arrayBuffer).promise;
            totalPages = pdfDocument.numPages;
            currentPage = 1;

            console.log('✅ โหลด PDF สำเร็จ:', totalPages, 'หน้า');

            // Update upload section
            updateUploadSection(file.name, totalPages);
            
            document.getElementById('configSection').classList.remove('hidden');
            document.getElementById('navigation').classList.remove('hidden');
            
            // Update page watermarks list if advanced mode is enabled
            if (isAdvancedMode) {
                updatePageWatermarksList();
            }
        }

        async function handleImageFile(file) {
            document.getElementById('previewContainer').innerHTML = '<div style="padding: 50px; text-align: center;">กำลังโหลดไฟล์รูปภาพ...</div>';

            // สร้าง "pseudo-PDF" จากรูปภาพ โดยเก็บรูปภาพใน global variable
            const reader = new FileReader();
            
            return new Promise((resolve, reject) => {
                reader.onload = function(e) {
                    // เก็บข้อมูลรูปภาพใน global variable
                    window.imageDocument = {
                        src: e.target.result,
                        name: file.name,
                        type: file.type
                    };
                    
                    // จำลองเป็น PDF 1 หน้า
                    pdfDocument = null; // ไม่ใช้ PDF document
                    totalPages = 1;
                    currentPage = 1;

                    console.log('✅ โหลดรูปภาพสำเร็จ:', file.name);

                    // Update upload section
                    updateUploadSection(file.name, 1);
                    
                    document.getElementById('configSection').classList.remove('hidden');
                    document.getElementById('navigation').style.display = 'none'; // ซ่อน navigation สำหรับรูปภาพ
                    
                    resolve();
                };
                
                reader.onerror = function() {
                    reject(new Error('ไม่สามารถอ่านไฟล์รูปภาพได้'));
                };
                
                reader.readAsDataURL(file);
            });
        }

        function resetApplicationState() {
            // รีเซ็ตตัวแปรทั้งหมด
            pdfDocument = null;
            currentPage = 1;
            totalPages = 0;
            watermarkPosition = { x: 50, y: 50 };
            isDragging = false;
            dragOffset = { x: 0, y: 0 };
            currentZoom = 1;
            
            // รีเซ็ต UI elements
            document.getElementById('zoomLevel').value = '1';
            document.getElementById('testPrintBtn').disabled = true;
            document.getElementById('printBtn').disabled = true;
            document.getElementById('printArea').innerHTML = '';
            
            // ซ่อน sections ที่ไม่ต้องการ
            document.getElementById('configSection').classList.add('hidden');
            document.getElementById('navigation').classList.add('hidden');
            
            // ล้าง messages
            document.getElementById('messageSection').innerHTML = '';
        }

        function updateUploadSection(fileName, pageCount) {
            document.querySelector('.upload-section').innerHTML = `
                <div class="upload-icon">✅</div>
                <h3>ไฟล์: ${fileName}</h3>
                <p>จำนวนหน้า: ${pageCount} หน้า</p>
                <p>🔄 คลิกหรือลากไฟล์ใหม่เพื่อเปลี่ยน</p>
            `;
        }

        async function generatePreview() {
            console.log('🔄 เริ่มสร้างตัวอย่าง...');

            // ตรวจสอบว่ามีไฟล์หรือไม่
            if (!pdfDocument && !window.imageDocument) {
                showMessage('กรุณาอัปโหลดไฟล์ก่อน', 'error');
                return;
            }

            // จัดการรูปภาพ
            if (window.imageDocument && !pdfDocument) {
                generateImagePreview();
                return;
            }

            if (currentPage < 1 || currentPage > totalPages) {
                console.error('❌ หน้าไม่ถูกต้อง:', currentPage);
                return;
            }

            try {
                document.getElementById('previewContainer').innerHTML = '<div style="padding: 50px; text-align: center;">กำลังสร้างตัวอย่าง...</div>';

                const page = await pdfDocument.getPage(currentPage);
                const orientation = document.getElementById('pageOrientation').value;
                
                // ตรวจสอบ rotation ของหน้า PDF ต้นฉบับ
                const pageRotation = page.rotate || 0;
                console.log(`📄 หน้า ${currentPage} rotation ต้นฉบับ: ${pageRotation}°`);
                
                // แสดงตัวอย่างตาม PDF ต้นฉบับโดยไม่เพิ่ม rotation
                // PDF.js จะจัดการ page rotation ให้เองอัตโนมัติ
                const viewport = page.getViewport({ 
                    scale: currentZoom
                    // ไม่ระบุ rotation เพื่อให้ PDF.js ใช้ค่าต้นฉบับ
                });

                console.log(`📖 สร้างหน้า ${currentPage}, จะพิมพ์แบบ: ${orientation}, แสดงตัวอย่าง: ตาม PDF ต้นฉบับ, ขนาด: ${viewport.width}x${viewport.height}`);

                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                await page.render({ 
                    canvasContext: context, 
                    viewport: viewport 
                }).promise;

                const pageContainer = document.createElement('div');
                pageContainer.className = 'pdf-page';

                canvas.className = 'pdf-canvas';
                pageContainer.appendChild(canvas);

                // Add orientation indicator
                const indicator = document.createElement('div');
                indicator.className = 'orientation-indicator';
                indicator.textContent = `พิมพ์: ${orientation === 'portrait' ? '📄 แนวตั้ง' : '📑 แนวนอน'}`;
                pageContainer.appendChild(indicator);

                // Add watermark
                const overlay = createWatermark(viewport.width, viewport.height);
                pageContainer.appendChild(overlay);

                document.getElementById('previewContainer').innerHTML = '';
                document.getElementById('previewContainer').appendChild(pageContainer);

                // Enable print buttons
                document.getElementById('testPrintBtn').disabled = false;
                document.getElementById('printBtn').disabled = false;

                console.log('✅ สร้างตัวอย่างเรียบร้อย');
                showMessage(`สร้างตัวอย่างเรียบร้อย! (พิมพ์จะออกแบบ${orientation === 'portrait' ? 'แนวตั้ง' : 'แนวนอน'})`, 'success');

            } catch (error) {
                console.error('❌ Error generating preview:', error);
                document.getElementById('previewContainer').innerHTML = '<div style="padding: 50px; text-align: center; color: red;">❌ ไม่สามารถสร้างตัวอย่างได้</div>';
                showMessage('เกิดข้อผิดพลาด: ' + error.message, 'error');
            }
        }

        function generateImagePreview() {
            try {
                console.log('🖼️ สร้างตัวอย่างรูปภาพ...');
                
                const imageData = window.imageDocument;
                
                // สร้าง page container
                const pageContainer = document.createElement('div');
                pageContainer.className = 'pdf-page';

                // สร้าง image element
                const img = document.createElement('img');
                img.src = imageData.src;
                img.className = 'pdf-canvas';
                img.style.maxWidth = '100%';
                img.style.height = 'auto';
                
                // รอให้รูปภาพโหลดเสร็จเพื่อได้ขนาด
                img.onload = function() {
                    const orientation = document.getElementById('pageOrientation').value;
                    
                    // แสดง orientation indicator
                    const indicator = document.createElement('div');
                    indicator.className = 'orientation-indicator';
                    indicator.textContent = `พิมพ์: ${orientation === 'portrait' ? '📄 แนวตั้ง' : '📑 แนวนอน'}`;
                    pageContainer.appendChild(indicator);

                    // Add watermark overlay
                    const overlay = createWatermark(img.naturalWidth || 800, img.naturalHeight || 600);
                    pageContainer.appendChild(overlay);

                    document.getElementById('previewContainer').innerHTML = '';
                    document.getElementById('previewContainer').appendChild(pageContainer);

                    // Enable print buttons
                    document.getElementById('testPrintBtn').disabled = false;
                    document.getElementById('printBtn').disabled = false;

                    console.log('✅ สร้างตัวอย่างรูปภาพเรียบร้อย');
                    showMessage(`สร้างตัวอย่างเรียบร้อย! (พิมพ์จะออกแบบ${orientation === 'portrait' ? 'แนวตั้ง' : 'แนวนอน'})`, 'success');
                };

                pageContainer.appendChild(img);
                
            } catch (error) {
                console.error('❌ Error generating image preview:', error);
                document.getElementById('previewContainer').innerHTML = '<div style="padding: 50px; text-align: center; color: red;">❌ ไม่สามารถสร้างตัวอย่างรูปภาพได้</div>';
                showMessage('เกิดข้อผิดพลาด: ' + error.message, 'error');
            }
        }

        function createWatermark(width, height) {
            const overlay = document.createElement('div');
            overlay.className = 'watermark-overlay';
            overlay.style.width = width + 'px';
            overlay.style.height = height + 'px';

            // Check if we're in advanced mode
            if (isAdvancedMode) {
                return createAdvancedWatermark(overlay, currentPage);
            }

            // Default mode - check which watermarks are enabled
            const textEnabled = document.getElementById('enableTextWatermark').checked;
            const imageEnabled = document.getElementById('enableImageWatermark').checked;
            
            if (textEnabled) {
                createTextWatermark(overlay);
            }
            
            if (imageEnabled) {
                createImageWatermark(overlay);
            }

            return overlay;
        }

        function createAdvancedWatermark(overlay, pageNum) {
            const pageData = pageWatermarks[pageNum];
            
            if (!pageData) {
                return overlay;
            }

            // Add text watermark if enabled
            if (pageData.text && pageData.text.enabled) {
                createAdvancedTextWatermark(overlay, pageData.text);
            }

            // Add image watermark if enabled  
            if (pageData.image && pageData.image.enabled) {
                createAdvancedImageWatermark(overlay, pageData.image);
            }

            return overlay;
        }

        function createAdvancedTextWatermark(overlay, textData) {
            const textElement = document.createElement('div');
            textElement.className = 'watermark-text';
            textElement.textContent = textData.content;

            const fontSize = parseInt(document.getElementById('watermarkSize').value);
            const rotation = parseInt(document.getElementById('rotationAngle').value);
            const textOpacity = parseFloat(document.getElementById('textOpacity').value);
            const position = textData.position;

            textElement.style.fontSize = fontSize + 'px';
            textElement.style.color = document.getElementById('watermarkColor').value;
            textElement.style.fontFamily = document.getElementById('fontFamily').value;
            textElement.style.opacity = textOpacity;
            textElement.style.left = position.x + '%';
            textElement.style.top = position.y + '%';
            textElement.style.transform = `translate(-50%, -50%) rotate(${rotation}deg)`;

            // Background
            if (document.getElementById('enableBackground').checked) {
                const bgColor = document.getElementById('watermarkBg').value;
                const bgOpacity = parseFloat(document.getElementById('bgOpacity').value);
                const bgAlpha = Math.round(bgOpacity * 255).toString(16).padStart(2, '0');
                textElement.style.backgroundColor = bgColor + bgAlpha;
            } else {
                textElement.style.backgroundColor = 'transparent';
            }

            // Add drag functionality with page-specific position tracking
            addAdvancedTextDragFunctionality(textElement, overlay, textData);

            overlay.appendChild(textElement);
            return overlay;
        }

        function createAdvancedImageWatermark(overlay, imageData) {
            if (!imageData.src) {
                return overlay;
            }

            const imageElement = document.createElement('img');
            imageElement.className = 'watermark-text'; // Use same class for drag functionality
            imageElement.src = imageData.src;

            const imageWidth = parseInt(document.getElementById('imageWidth').value);
            const imageHeight = parseInt(document.getElementById('imageHeight').value);
            const rotation = parseInt(document.getElementById('rotationAngle').value);
            const imageOpacity = parseFloat(document.getElementById('textOpacity').value);
            const position = imageData.position;

            imageElement.style.width = imageWidth + 'px';
            imageElement.style.height = imageHeight + 'px';
            imageElement.style.opacity = imageOpacity;
            imageElement.style.position = 'absolute';
            imageElement.style.left = position.x + '%';
            imageElement.style.top = position.y + '%';
            imageElement.style.transform = `translate(-50%, -50%) rotate(${rotation}deg)`;
            imageElement.style.pointerEvents = 'auto';
            imageElement.style.cursor = 'grab';

            // Add drag functionality with page-specific position tracking
            addAdvancedImageDragFunctionality(imageElement, overlay, imageData);

            overlay.appendChild(imageElement);
            return overlay;
        }

        function createTextWatermark(overlay) {
            const textElement = document.createElement('div');
            textElement.className = 'watermark-text';
            textElement.textContent = document.getElementById('watermarkText').value;

            const fontSize = parseInt(document.getElementById('watermarkSize').value);
            const rotation = parseInt(document.getElementById('rotationAngle').value);
            const textOpacity = parseFloat(document.getElementById('textOpacity').value);

            textElement.style.fontSize = fontSize + 'px';
            textElement.style.color = document.getElementById('watermarkColor').value;
            textElement.style.fontFamily = document.getElementById('fontFamily').value;
            textElement.style.opacity = textOpacity;
            textElement.style.left = watermarkPosition.x + '%';
            textElement.style.top = watermarkPosition.y + '%';
            textElement.style.transform = `translate(-50%, -50%) rotate(${rotation}deg)`;

            // Background
            if (document.getElementById('enableBackground').checked) {
                const bgColor = document.getElementById('watermarkBg').value;
                const bgOpacity = parseFloat(document.getElementById('bgOpacity').value);
                const bgAlpha = Math.round(bgOpacity * 255).toString(16).padStart(2, '0');
                textElement.style.backgroundColor = bgColor + bgAlpha;
            } else {
                textElement.style.backgroundColor = 'transparent';
            }

            // Add drag functionality
            addDragFunctionality(textElement, overlay);

            overlay.appendChild(textElement);
            return overlay;
        }

        function createImageWatermark(overlay) {
            // Check if we're in advanced mode with page images (legacy support)
            if (isAdvancedMode && pageImages[currentPage] && pageImages[currentPage].src) {
                const pageImage = pageImages[currentPage];
                
                const imageElement = document.createElement('img');
                imageElement.className = 'watermark-text'; // Use same class for drag functionality
                imageElement.src = pageImage.src;

                const imageWidth = pageImage.width || parseInt(document.getElementById('imageWidth').value);
                const imageHeight = pageImage.height || parseInt(document.getElementById('imageHeight').value);
                const rotation = parseInt(document.getElementById('rotationAngle').value);
                const imageOpacity = parseFloat(document.getElementById('textOpacity').value);

                const position = pageImage.position || watermarkPosition;

                imageElement.style.width = imageWidth + 'px';
                imageElement.style.height = imageHeight + 'px';
                imageElement.style.opacity = imageOpacity;
                imageElement.style.position = 'absolute';
                imageElement.style.left = position.x + '%';
                imageElement.style.top = position.y + '%';
                imageElement.style.transform = `translate(-50%, -50%) rotate(${rotation}deg)`;
                imageElement.style.pointerEvents = 'auto';
                imageElement.style.cursor = 'grab';

                // Add drag functionality with page-specific position tracking
                addPageImageDragFunctionality(imageElement, overlay, currentPage);

                overlay.appendChild(imageElement);
                return overlay;
            }

            // Default single image mode
            const imagePreview = document.getElementById('imagePreviewImg');
            if (!imagePreview.src) {
                return overlay; // Return empty overlay if no image
            }

            const imageElement = document.createElement('img');
            imageElement.className = 'watermark-text'; // Use same class for drag functionality
            imageElement.src = imagePreview.src;

            const imageWidth = parseInt(document.getElementById('imageWidth').value);
            const imageHeight = parseInt(document.getElementById('imageHeight').value);
            const rotation = parseInt(document.getElementById('rotationAngle').value);
            const imageOpacity = parseFloat(document.getElementById('textOpacity').value);

            imageElement.style.width = imageWidth + 'px';
            imageElement.style.height = imageHeight + 'px';
            imageElement.style.opacity = imageOpacity;
            imageElement.style.position = 'absolute';
            imageElement.style.left = watermarkPosition.x + '%';
            imageElement.style.top = watermarkPosition.y + '%';
            imageElement.style.transform = `translate(-50%, -50%) rotate(${rotation}deg)`;
            imageElement.style.pointerEvents = 'auto';
            imageElement.style.cursor = 'grab';

            // Add drag functionality
            addDragFunctionality(imageElement, overlay);

            overlay.appendChild(imageElement);
            return overlay;
        }

        function addDragFunctionality(textElement, overlay) {
            textElement.addEventListener('mousedown', (e) => {
                isDragging = true;
                textElement.classList.add('dragging');

                const overlayRect = overlay.getBoundingClientRect();
                const textRect = textElement.getBoundingClientRect();

                dragOffset.x = e.clientX - (textRect.left + textRect.width / 2);
                dragOffset.y = e.clientY - (textRect.top + textRect.height / 2);

                e.preventDefault();
                e.stopPropagation();
            });

            document.addEventListener('mousemove', (e) => {
                if (!isDragging) return;

                const overlayRect = overlay.getBoundingClientRect();
                const x = ((e.clientX - dragOffset.x - overlayRect.left) / overlayRect.width) * 100;
                const y = ((e.clientY - dragOffset.y - overlayRect.top) / overlayRect.height) * 100;

                watermarkPosition.x = Math.max(5, Math.min(95, x));
                watermarkPosition.y = Math.max(5, Math.min(95, y));

                textElement.style.left = watermarkPosition.x + '%';
                textElement.style.top = watermarkPosition.y + '%';

                // Update position select
                const positionSelect = document.getElementById('watermarkPosition');
                if (!Array.from(positionSelect.options).find(opt => opt.value === 'custom')) {
                    const customOption = document.createElement('option');
                    customOption.value = 'custom';
                    customOption.textContent = 'ตำแหน่งที่กำหนดเอง';
                    positionSelect.appendChild(customOption);
                }
                positionSelect.value = 'custom';
            });

            document.addEventListener('mouseup', () => {
                if (isDragging) {
                    isDragging = false;
                    textElement.classList.remove('dragging');
                }
            });
        }

        function addPageImageDragFunctionality(imageElement, overlay, pageNum) {
            let isPageDragging = false;
            let pageDragOffset = { x: 0, y: 0 };

            imageElement.addEventListener('mousedown', (e) => {
                isPageDragging = true;
                imageElement.classList.add('dragging');

                const overlayRect = overlay.getBoundingClientRect();
                const imageRect = imageElement.getBoundingClientRect();

                pageDragOffset.x = e.clientX - (imageRect.left + imageRect.width / 2);
                pageDragOffset.y = e.clientY - (imageRect.top + imageRect.height / 2);

                e.preventDefault();
                e.stopPropagation();
            });

            document.addEventListener('mousemove', (e) => {
                if (!isPageDragging) return;

                const overlayRect = overlay.getBoundingClientRect();
                const x = ((e.clientX - pageDragOffset.x - overlayRect.left) / overlayRect.width) * 100;
                const y = ((e.clientY - pageDragOffset.y - overlayRect.top) / overlayRect.height) * 100;

                const newX = Math.max(5, Math.min(95, x));
                const newY = Math.max(5, Math.min(95, y));

                // Update page-specific position
                if (!pageImages[pageNum]) {
                    pageImages[pageNum] = { position: { x: 50, y: 50 } };
                }
                pageImages[pageNum].position = { x: newX, y: newY };

                imageElement.style.left = newX + '%';
                imageElement.style.top = newY + '%';

                // Update the page images list
                updatePageImagesList();
            });

            document.addEventListener('mouseup', () => {
                if (isPageDragging) {
                    isPageDragging = false;
                    imageElement.classList.remove('dragging');
                }
            });
        }

        function addAdvancedTextDragFunctionality(textElement, overlay, textData) {
            let isAdvancedDragging = false;
            let advancedDragOffset = { x: 0, y: 0 };

            textElement.addEventListener('mousedown', (e) => {
                isAdvancedDragging = true;
                textElement.classList.add('dragging');

                const overlayRect = overlay.getBoundingClientRect();
                const textRect = textElement.getBoundingClientRect();

                advancedDragOffset.x = e.clientX - (textRect.left + textRect.width / 2);
                advancedDragOffset.y = e.clientY - (textRect.top + textRect.height / 2);

                e.preventDefault();
                e.stopPropagation();
            });

            document.addEventListener('mousemove', (e) => {
                if (!isAdvancedDragging) return;

                const overlayRect = overlay.getBoundingClientRect();
                const x = ((e.clientX - advancedDragOffset.x - overlayRect.left) / overlayRect.width) * 100;
                const y = ((e.clientY - advancedDragOffset.y - overlayRect.top) / overlayRect.height) * 100;

                const newX = Math.max(5, Math.min(95, x));
                const newY = Math.max(5, Math.min(95, y));

                // Update position in textData
                textData.position.x = newX;
                textData.position.y = newY;

                textElement.style.left = newX + '%';
                textElement.style.top = newY + '%';

                // Update the page watermarks list
                updatePageWatermarksList();
            });

            document.addEventListener('mouseup', () => {
                if (isAdvancedDragging) {
                    isAdvancedDragging = false;
                    textElement.classList.remove('dragging');
                }
            });
        }

        function addAdvancedImageDragFunctionality(imageElement, overlay, imageData) {
            let isAdvancedDragging = false;
            let advancedDragOffset = { x: 0, y: 0 };

            imageElement.addEventListener('mousedown', (e) => {
                isAdvancedDragging = true;
                imageElement.classList.add('dragging');

                const overlayRect = overlay.getBoundingClientRect();
                const imageRect = imageElement.getBoundingClientRect();

                advancedDragOffset.x = e.clientX - (imageRect.left + imageRect.width / 2);
                advancedDragOffset.y = e.clientY - (imageRect.top + imageRect.height / 2);

                e.preventDefault();
                e.stopPropagation();
            });

            document.addEventListener('mousemove', (e) => {
                if (!isAdvancedDragging) return;

                const overlayRect = overlay.getBoundingClientRect();
                const x = ((e.clientX - advancedDragOffset.x - overlayRect.left) / overlayRect.width) * 100;
                const y = ((e.clientY - advancedDragOffset.y - overlayRect.top) / overlayRect.height) * 100;

                const newX = Math.max(5, Math.min(95, x));
                const newY = Math.max(5, Math.min(95, y));

                // Update position in imageData
                imageData.position.x = newX;
                imageData.position.y = newY;

                imageElement.style.left = newX + '%';
                imageElement.style.top = newY + '%';

                // Update the page watermarks list
                updatePageWatermarksList();
            });

            document.addEventListener('mouseup', () => {
                if (isAdvancedDragging) {
                    isAdvancedDragging = false;
                    imageElement.classList.remove('dragging');
                }
            });
        }

        function updatePreview() {
            const overlay = document.querySelector('.watermark-overlay');
            if (overlay) {
                overlay.remove();
            }
            
            const pageContainer = document.querySelector('.pdf-page');
            if (pageContainer) {
                // ลองหา canvas (PDF) ก่อน
                const canvas = pageContainer.querySelector('canvas.pdf-canvas');
                if (canvas) {
                    console.log('🔄 อัปเดตลายน้ำ PDF:', canvas.width, 'x', canvas.height);
                    const newOverlay = createWatermark(canvas.width, canvas.height);
                    pageContainer.appendChild(newOverlay);
                    return;
                }
                
                // ถ้าไม่มี canvas ลองหา img (รูปภาพ)
                const img = pageContainer.querySelector('img.pdf-canvas');
                if (img) {
                    // รอให้รูปภาพโหลดเสร็จ
                    if (img.complete && img.naturalWidth > 0) {
                        console.log('🔄 อัปเดตลายน้ำรูปภาพ:', img.naturalWidth, 'x', img.naturalHeight);
                        const newOverlay = createWatermark(img.naturalWidth, img.naturalHeight);
                        pageContainer.appendChild(newOverlay);
                    } else {
                        // ถ้ารูปภาพยังไม่โหลดเสร็จ รอซักครู่แล้วลองใหม่
                        setTimeout(() => {
                            if (img.complete && img.naturalWidth > 0) {
                                console.log('🔄 อัปเดตลายน้ำรูปภาพ (หลังรอ):', img.naturalWidth, 'x', img.naturalHeight);
                                const newOverlay = createWatermark(img.naturalWidth, img.naturalHeight);
                                pageContainer.appendChild(newOverlay);
                            } else {
                                console.log('🔄 อัปเดตลายน้ำรูปภาพ (fallback): 800x600');
                                const newOverlay = createWatermark(800, 600);
                                pageContainer.appendChild(newOverlay);
                            }
                        }, 100);
                    }
                }
            }
        }

        function setPresetPosition(position) {
            switch (position) {
                case 'center': watermarkPosition = { x: 50, y: 50 }; break;
                case 'top': watermarkPosition = { x: 50, y: 15 }; break;
                case 'bottom': watermarkPosition = { x: 50, y: 85 }; break;
                case 'top-left': watermarkPosition = { x: 15, y: 15 }; break;
                case 'top-right': watermarkPosition = { x: 85, y: 15 }; break;
                case 'bottom-left': watermarkPosition = { x: 15, y: 85 }; break;
                case 'bottom-right': watermarkPosition = { x: 85, y: 85 }; break;
            }
        }

        function changePage(direction) {
            const newPage = currentPage + direction;
            if (newPage < 1 || newPage > totalPages) return;

            currentPage = newPage;
            updatePageInfo();
            generatePreview();
        }

        function updatePageInfo() {
            document.getElementById('pageInfo').textContent = `หน้า ${currentPage} จาก ${totalPages}`;
            document.getElementById('prevBtn').disabled = currentPage <= 1;
            document.getElementById('nextBtn').disabled = currentPage >= totalPages;
        }

        async function testPrint() {
            if (!pdfDocument) return;

            try {
                document.getElementById('testPrintBtn').disabled = true;
                const orientation = document.getElementById('pageOrientation').value;
                showMessage(`กำลังเตรียมทดสอบการพิมพ์แบบ${orientation === 'portrait' ? 'แนวตั้ง' : 'แนวนอน'}...`, 'success');

                await preparePrintPages([currentPage]);

                setTimeout(() => {
                    document.getElementById('printArea').classList.remove('hidden');
                    console.log('🖨️ กำลังพิมพ์แบบ:', orientation);
                    window.print();
                    document.getElementById('printArea').classList.add('hidden');
                    document.getElementById('testPrintBtn').disabled = false;
                    showMessage('ทดสอบการพิมพ์เรียบร้อย!', 'success');
                }, 500);

            } catch (error) {
                console.error('❌ Error in test print:', error);
                showMessage('เกิดข้อผิดพลาดในการทดสอบพิมพ์', 'error');
                document.getElementById('testPrintBtn').disabled = false;
            }
        }

        async function printAll() {
            if (!pdfDocument) return;

            try {
                document.getElementById('printBtn').disabled = true;
                const orientation = document.getElementById('pageOrientation').value;
                showMessage(`กำลังเตรียมไฟล์สำหรับพิมพ์แบบ${orientation === 'portrait' ? 'แนวตั้ง' : 'แนวนอน'}...`, 'success');

                const pageNumbers = Array.from({length: totalPages}, (_, i) => i + 1);
                await preparePrintPages(pageNumbers);

                setTimeout(() => {
                    document.getElementById('printArea').classList.remove('hidden');
                    console.log('🖨️ กำลังพิมพ์', totalPages, 'หน้า แบบ:', orientation);
                    window.print();
                    document.getElementById('printArea').classList.add('hidden');
                    document.getElementById('printBtn').disabled = false;
                    showMessage('เตรียมพิมพ์เรียบร้อย!', 'success');
                }, 500);

            } catch (error) {
                console.error('❌ Error preparing print:', error);
                showMessage('เกิดข้อผิดพลาดในการเตรียมพิมพ์', 'error');
                document.getElementById('printBtn').disabled = false;
            }
        }

        async function preparePrintPages(pageNumbers) {
            const printArea = document.getElementById('printArea');
            printArea.innerHTML = '';
            // ลดค่า scale ให้เหมาะสมกับการพิมพ์
            const printScale = 1.5;
            const orientation = document.getElementById('pageOrientation').value;

            console.log('🖨️ เตรียมพิมพ์', pageNumbers.length, 'หน้า แบบ', orientation);

            // ตรวจสอบว่าเป็นรูปภาพหรือ PDF
            if (window.imageDocument && !pdfDocument) {
                preparePrintImage(orientation);
                return;
            }

            for (const pageNum of pageNumbers) {
                if (pageNum > totalPages) continue;

                const page = await pdfDocument.getPage(pageNum);
                
                // รวม rotation ของ PDF กับการตั้งค่าผู้ใช้
                const pageNativeRotation = page.rotate || 0;
                let rotation = pageNativeRotation;
                
                // ปรับ rotation ตาม orientation ที่ผู้ใช้เลือก
                if (orientation === 'portrait') {
                    // สำหรับ portrait: ถ้า PDF เป็นแนวนอน (native 270°) ให้หมุนเพิ่ม 90° เป็น 360°/0°
                    if (pageNativeRotation === 270) {
                        rotation = 360; // หรือ 0 - จะทำให้ PDF แสดงในแนวตั้ง
                    }
                } else if (orientation === 'landscape') {
                    // สำหรับ landscape: เพิ่ม 90° จาก native rotation
                    rotation = pageNativeRotation + 90;
                }

                console.log(`🖨️ หน้า ${pageNum}: native=${pageNativeRotation}°, orientation=${orientation}, total=${rotation}°`);

                const viewport = page.getViewport({ 
                    scale: printScale,
                    rotation: rotation
                });

                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                await page.render({ 
                    canvasContext: context, 
                    viewport: viewport 
                }).promise;

                const pageContainer = document.createElement('div');
                pageContainer.className = 'pdf-page';
                
                if (orientation === 'landscape') {
                    pageContainer.classList.add('landscape');
                }

                canvas.className = 'pdf-canvas';
                pageContainer.appendChild(canvas);

                const overlay = createPrintWatermark(viewport.width, viewport.height, printScale, rotation, pageNum);
                pageContainer.appendChild(overlay);

                printArea.appendChild(pageContainer);
            }
        }

        function preparePrintImage(orientation) {
            const printArea = document.getElementById('printArea');
            const imageData = window.imageDocument;
            
            console.log('🖨️ เตรียมพิมพ์รูปภาพ แบบ', orientation);

            // สร้าง page container
            const pageContainer = document.createElement('div');
            pageContainer.className = 'pdf-page';
            
            if (orientation === 'landscape') {
                pageContainer.classList.add('landscape');
            }

            // สร้าง image element
            const img = document.createElement('img');
            img.src = imageData.src;
            img.className = 'pdf-canvas';
            img.style.maxWidth = '100%';
            img.style.height = 'auto';
            
            pageContainer.appendChild(img);

            // เมื่อรูปภาพโหลดเสร็จ ให้เพิ่ม watermark
            img.onload = function() {
                const overlay = createPrintWatermark(
                    img.naturalWidth || 800, 
                    img.naturalHeight || 600, 
                    1.0, // scale สำหรับรูปภาพ
                    0,   // ไม่มี rotation สำหรับรูปภาพ
                    1    // หน้า 1 สำหรับรูปภาพ
                );
                pageContainer.appendChild(overlay);
                
                console.log('🖼️ เตรียมรูปภาพสำหรับพิมพ์เรียบร้อย');
            };

            printArea.appendChild(pageContainer);
        }

        function createPrintWatermark(width, height, scale, pageRotation = 0, pageNum = 1) {
            const overlay = document.createElement('div');
            overlay.className = 'watermark-overlay';
            // ไม่กำหนดขนาดของ overlay เพราะจะใช้ CSS absolute positioning แทน

            // Check if we're in advanced mode
            if (isAdvancedMode) {
                return createAdvancedPrintWatermark(overlay, width, height, scale, pageRotation, pageNum);
            }

            // Default mode - check which watermarks are enabled
            const textEnabled = document.getElementById('enableTextWatermark').checked;
            const imageEnabled = document.getElementById('enableImageWatermark').checked;
            
            if (textEnabled) {
                createPrintTextWatermark(overlay, width, height, scale, pageRotation, pageNum);
            }
            
            if (imageEnabled) {
                createPrintImageWatermark(overlay, width, height, scale, pageRotation, pageNum);
            }

            return overlay;
        }

        function createAdvancedPrintWatermark(overlay, width, height, scale, pageRotation, pageNum) {
            const pageData = pageWatermarks[pageNum];
            
            if (!pageData) {
                return overlay;
            }

            // Add text watermark if enabled
            if (pageData.text && pageData.text.enabled) {
                createAdvancedPrintTextWatermark(overlay, width, height, scale, pageRotation, pageData.text);
            }

            // Add image watermark if enabled  
            if (pageData.image && pageData.image.enabled) {
                createAdvancedPrintImageWatermark(overlay, width, height, scale, pageRotation, pageData.image);
            }

            return overlay;
        }

        function createAdvancedPrintTextWatermark(overlay, width, height, scale, pageRotation, textData) {
            const textElement = document.createElement('div');
            textElement.className = 'watermark-text';
            textElement.textContent = textData.content;

            const fontSize = parseInt(document.getElementById('watermarkSize').value);
            const userRotation = parseInt(document.getElementById('rotationAngle').value);
            const textOpacity = parseFloat(document.getElementById('textOpacity').value);
            const position = textData.position;

            // คำนวณ rotation และตำแหน่งตาม page rotation
            let totalRotation = userRotation;
            let adjustedX = position.x;
            let adjustedY = position.y;

            const normalizedPageRotation = pageRotation % 360;
            
            if (normalizedPageRotation === 360 || normalizedPageRotation === 0) {
                totalRotation = userRotation + 90;
                adjustedX = position.y;
                adjustedY = 100 - position.x;
            } else if (normalizedPageRotation === 90) {
                totalRotation = userRotation - 90;
                adjustedX = 100 - position.y;
                adjustedY = position.x;
            } else if (normalizedPageRotation === 270) {
                totalRotation = userRotation + 90;
                adjustedX = position.y;
                adjustedY = 100 - position.x;
            }

            textElement.style.fontSize = fontSize + 'px';
            textElement.style.color = document.getElementById('watermarkColor').value;
            textElement.style.fontFamily = document.getElementById('fontFamily').value;
            textElement.style.opacity = textOpacity;
            textElement.style.left = adjustedX + '%';
            textElement.style.top = adjustedY + '%';
            textElement.style.transform = `translate(-50%, -50%) rotate(${totalRotation}deg)`;
            textElement.style.pointerEvents = 'none';
            
            // Background
            if (document.getElementById('enableBackground').checked) {
                const bgColor = document.getElementById('watermarkBg').value;
                const bgOpacity = parseFloat(document.getElementById('bgOpacity').value);
                const bgAlpha = Math.round(bgOpacity * 255).toString(16).padStart(2, '0');
                textElement.style.backgroundColor = bgColor + bgAlpha;
            }

            textElement.style.setProperty('transform', `translate(-50%, -50%) rotate(${totalRotation}deg)`, 'important');

            overlay.appendChild(textElement);
            return overlay;
        }

        function createAdvancedPrintImageWatermark(overlay, width, height, scale, pageRotation, imageData) {
            if (!imageData.src) {
                return overlay;
            }

            const imageElement = document.createElement('img');
            imageElement.className = 'watermark-text';
            imageElement.src = imageData.src;

            const imageWidth = parseInt(document.getElementById('imageWidth').value);
            const imageHeight = parseInt(document.getElementById('imageHeight').value);
            const userRotation = parseInt(document.getElementById('rotationAngle').value);
            const imageOpacity = parseFloat(document.getElementById('textOpacity').value);
            const position = imageData.position;

            // คำนวณ rotation และตำแหน่งตาม page rotation
            let totalRotation = userRotation;
            let adjustedX = position.x;
            let adjustedY = position.y;
                
            const normalizedPageRotation = pageRotation % 360;
                
            if (normalizedPageRotation === 360 || normalizedPageRotation === 0) {
                totalRotation = userRotation + 90;
                adjustedX = position.y;
                adjustedY = 100 - position.x;
            } else if (normalizedPageRotation === 90) {
                totalRotation = userRotation - 90;
                adjustedX = 100 - position.y;
                adjustedY = position.x;
            } else if (normalizedPageRotation === 270) {
                totalRotation = userRotation + 90;
                adjustedX = position.y;
                adjustedY = 100 - position.x;
            }

            imageElement.style.width = imageWidth + 'px';
            imageElement.style.height = imageHeight + 'px';
            imageElement.style.opacity = imageOpacity;
            imageElement.style.position = 'absolute';
            imageElement.style.left = adjustedX + '%';
            imageElement.style.top = adjustedY + '%';
            imageElement.style.transform = `translate(-50%, -50%) rotate(${totalRotation}deg)`;
            imageElement.style.pointerEvents = 'none';
            imageElement.style.setProperty('transform', `translate(-50%, -50%) rotate(${totalRotation}deg)`, 'important');

            overlay.appendChild(imageElement);
            return overlay;
        }

        function createPrintTextWatermark(overlay, width, height, scale, pageRotation = 0) {
            const textElement = document.createElement('div');
            textElement.className = 'watermark-text';
            textElement.textContent = document.getElementById('watermarkText').value;

            // ใช้ขนาดฟอนต์เดิมโดยไม่คูณ scale เพื่อให้ตรงกับตัวอย่าง
            const fontSize = parseInt(document.getElementById('watermarkSize').value);
            const userRotation = parseInt(document.getElementById('rotationAngle').value);
            const textOpacity = parseFloat(document.getElementById('textOpacity').value);

            // คำนวณ rotation ของลายน้ำตาม page rotation รวม
            let totalRotation = userRotation;
            
            // ปรับ rotation ของลายน้ำตาม page rotation ที่เปลี่ยนแปลง
            const normalizedPageRotation = pageRotation % 360;
            console.log(`🔄 Page rotation: ${pageRotation}°, normalized: ${normalizedPageRotation}°`);
            
            // คำนวณการหมุนของลายน้ำตาม PDF rotation
            if (normalizedPageRotation === 360 || normalizedPageRotation === 0) {
                // PDF หมุนมาเป็นแนวตั้งปกติ (จาก 270° → 360°/0°)
                totalRotation = userRotation + 90; // ลายน้ำต้องหมุนตาม
            } else if (normalizedPageRotation === 90) {
                // PDF หมุนเป็นแนวนอน (landscape)
                totalRotation = userRotation - 90;
            } else if (normalizedPageRotation === 270) {
                // PDF แนวนอนธรรมชาติ (native 270°)
                totalRotation = userRotation + 90;
            }
            
            // ปรับตำแหน่งลายน้ำเมื่อหน้าหมุน
            let adjustedX = watermarkPosition.x;
            let adjustedY = watermarkPosition.y;
            
            if (normalizedPageRotation === 360 || normalizedPageRotation === 0) {
                // PDF หมุนจาก 270° → 360°/0° (แนวนอน → แนวตั้ง)
                adjustedX = watermarkPosition.y;
                adjustedY = 100 - watermarkPosition.x;
            } else if (normalizedPageRotation === 90) {
                // PDF หมุนเป็นแนวนอน (landscape)
                adjustedX = 100 - watermarkPosition.y;
                adjustedY = watermarkPosition.x;
            } else if (normalizedPageRotation === 270) {
                // PDF แนวนอนธรรมชาติ (native 270°)
                adjustedX = watermarkPosition.y;
                adjustedY = 100 - watermarkPosition.x;
            }

            textElement.style.fontSize = fontSize + 'px';
            textElement.style.color = document.getElementById('watermarkColor').value;
            textElement.style.fontFamily = document.getElementById('fontFamily').value;
            textElement.style.opacity = textOpacity;
            textElement.style.left = adjustedX + '%';
            textElement.style.top = adjustedY + '%';
            textElement.style.transform = `translate(-50%, -50%) rotate(${totalRotation}deg)`;
            textElement.style.pointerEvents = 'none';
            
            // Force important styles for print
            textElement.style.setProperty('transform', `translate(-50%, -50%) rotate(${totalRotation}deg)`, 'important');

            if (document.getElementById('enableBackground').checked) {
                const bgColor = document.getElementById('watermarkBg').value;
                const bgOpacity = parseFloat(document.getElementById('bgOpacity').value);
                const bgAlpha = Math.round(bgOpacity * 255).toString(16).padStart(2, '0');
                textElement.style.backgroundColor = bgColor + bgAlpha;
            } else {
                textElement.style.backgroundColor = 'transparent';
            }

            console.log(`🏷️ ลายน้ำ: user=${userRotation}°, page=${pageRotation}°, normalized=${normalizedPageRotation}°, total=${totalRotation}°, pos=(${adjustedX}%, ${adjustedY}%)`);

            overlay.appendChild(textElement);
            return overlay;
        }

        function createPrintImageWatermark(overlay, width, height, scale, pageRotation = 0, pageNum = 1) {
            // Check if we're in advanced mode with page images (legacy support)  
            if (isAdvancedMode && pageImages[pageNum] && pageImages[pageNum].src) {
                const pageImage = pageImages[pageNum];
                
                const imageElement = document.createElement('img');
                imageElement.className = 'watermark-text';
                imageElement.src = pageImage.src;

                const imageWidth = pageImage.width || parseInt(document.getElementById('imageWidth').value);
                const imageHeight = pageImage.height || parseInt(document.getElementById('imageHeight').value);
                const userRotation = parseInt(document.getElementById('rotationAngle').value);
                const imageOpacity = parseFloat(document.getElementById('textOpacity').value);

                const position = pageImage.position || { x: 50, y: 50 };

                // คำนวณ rotation และตำแหน่งตาม page rotation (same as existing logic)
                let totalRotation = userRotation;
                let adjustedX = position.x;
                let adjustedY = position.y;
                
                const normalizedPageRotation = pageRotation % 360;
                
                if (normalizedPageRotation === 360 || normalizedPageRotation === 0) {
                    totalRotation = userRotation + 90;
                    adjustedX = position.y;
                    adjustedY = 100 - position.x;
                } else if (normalizedPageRotation === 90) {
                    totalRotation = userRotation - 90;
                    adjustedX = 100 - position.y;
                    adjustedY = position.x;
                } else if (normalizedPageRotation === 270) {
                    totalRotation = userRotation + 90;
                    adjustedX = position.y;
                    adjustedY = 100 - position.x;
                }

                imageElement.style.width = imageWidth + 'px';
                imageElement.style.height = imageHeight + 'px';
                imageElement.style.opacity = imageOpacity;
                imageElement.style.position = 'absolute';
                imageElement.style.left = adjustedX + '%';
                imageElement.style.top = adjustedY + '%';
                imageElement.style.transform = `translate(-50%, -50%) rotate(${totalRotation}deg)`;
                imageElement.style.pointerEvents = 'none';
                imageElement.style.setProperty('transform', `translate(-50%, -50%) rotate(${totalRotation}deg)`, 'important');

                console.log(`🖼️ หน้า ${pageNum} รูปภาพลายน้ำ: user=${userRotation}°, page=${pageRotation}°, total=${totalRotation}°, pos=(${adjustedX}%, ${adjustedY}%)`);

                overlay.appendChild(imageElement);
                return overlay;
            }

            // Default single image mode
            const imagePreview = document.getElementById('imagePreviewImg');
            if (!imagePreview.src) {
                return overlay; // Return empty overlay if no image
            }

            const imageElement = document.createElement('img');
            imageElement.className = 'watermark-text';
            imageElement.src = imagePreview.src;

            const imageWidth = parseInt(document.getElementById('imageWidth').value);
            const imageHeight = parseInt(document.getElementById('imageHeight').value);
            const userRotation = parseInt(document.getElementById('rotationAngle').value);
            const imageOpacity = parseFloat(document.getElementById('textOpacity').value);

            // คำนวณ rotation ของลายน้ำตาม page rotation รวม
            let totalRotation = userRotation;
            
            // ปรับ rotation ของลายน้ำตาม page rotation ที่เปลี่ยนแปลง
            const normalizedPageRotation = pageRotation % 360;
            console.log(`🔄 Image rotation: ${pageRotation}°, normalized: ${normalizedPageRotation}°`);
            
            // คำนวณการหมุนของลายน้ำตาม PDF rotation
            if (normalizedPageRotation === 360 || normalizedPageRotation === 0) {
                // PDF หมุนมาเป็นแนวตั้งปกติ (จาก 270° → 360°/0°)
                totalRotation = userRotation + 90; // ลายน้ำต้องหมุนตาม
            } else if (normalizedPageRotation === 90) {
                // PDF หมุนเป็นแนวนอน (landscape)
                totalRotation = userRotation - 90;
            } else if (normalizedPageRotation === 270) {
                // PDF แนวนอนธรรมชาติ (native 270°)
                totalRotation = userRotation + 90;
            }
            
            // ปรับตำแหน่งลายน้ำเมื่อหน้าหมุน
            let adjustedX = watermarkPosition.x;
            let adjustedY = watermarkPosition.y;
            
            if (normalizedPageRotation === 360 || normalizedPageRotation === 0) {
                // PDF หมุนจาก 270° → 360°/0° (แนวนอน → แนวตั้ง)
                adjustedX = watermarkPosition.y;
                adjustedY = 100 - watermarkPosition.x;
            } else if (normalizedPageRotation === 90) {
                // PDF หมุนเป็นแนวนอน (landscape)
                adjustedX = 100 - watermarkPosition.y;
                adjustedY = watermarkPosition.x;
            } else if (normalizedPageRotation === 270) {
                // PDF แนวนอนธรรมชาติ (native 270°)
                adjustedX = watermarkPosition.y;
                adjustedY = 100 - watermarkPosition.x;
            }

            imageElement.style.width = imageWidth + 'px';
            imageElement.style.height = imageHeight + 'px';
            imageElement.style.opacity = imageOpacity;
            imageElement.style.position = 'absolute';
            imageElement.style.left = adjustedX + '%';
            imageElement.style.top = adjustedY + '%';
            imageElement.style.transform = `translate(-50%, -50%) rotate(${totalRotation}deg)`;
            imageElement.style.pointerEvents = 'none';
            
            // Force important styles for print
            imageElement.style.setProperty('transform', `translate(-50%, -50%) rotate(${totalRotation}deg)`, 'important');

            console.log(`🖼️ รูปภาพลายน้ำ: user=${userRotation}°, page=${pageRotation}°, normalized=${normalizedPageRotation}°, total=${totalRotation}°, pos=(${adjustedX}%, ${adjustedY}%)`);

            overlay.appendChild(imageElement);
            return overlay;
        }

        function showMessage(message, type) {
            const className = type === 'error' ? 'error-message' : 'success-message';
            document.getElementById('messageSection').innerHTML = `<div class="${className}">${message}</div>`;
            setTimeout(() => {
                document.getElementById('messageSection').innerHTML = '';
            }, 5000);
        }

        // Preset Management
        function getCurrentSettings() {
            return {
                // Legacy settings for backward compatibility
                text: document.getElementById('watermarkText').value,
                color: document.getElementById('watermarkColor').value,
                size: parseInt(document.getElementById('watermarkSize').value),
                fontFamily: document.getElementById('fontFamily').value,
                rotation: parseInt(document.getElementById('rotationAngle').value),
                textOpacity: parseFloat(document.getElementById('textOpacity').value),
                enableBackground: document.getElementById('enableBackground').checked,
                backgroundColor: document.getElementById('watermarkBg').value,
                bgOpacity: parseFloat(document.getElementById('bgOpacity').value),
                position: { ...watermarkPosition },
                positionType: document.getElementById('watermarkPosition').value,
                pageOrientation: document.getElementById('pageOrientation').value,
                imageWidth: parseInt(document.getElementById('imageWidth').value),
                imageHeight: parseInt(document.getElementById('imageHeight').value),
                
                // New system settings
                enableTextWatermark: document.getElementById('enableTextWatermark').checked,
                enableImageWatermark: document.getElementById('enableImageWatermark').checked,
                enableAdvancedMode: document.getElementById('enableAdvancedMode').checked,
                singleImage: document.getElementById('imagePreviewImg').src || null,
                pageWatermarks: JSON.parse(JSON.stringify(pageWatermarks)),
                
                // Legacy support
                watermarkType: document.getElementById('enableTextWatermark').checked ? 'text' : 'image',
                enableMultipleImages: false, // No longer used
                pageImages: {} // No longer used
            };
        }

        function applySettings(settings) {
            // Apply basic settings
            document.getElementById('watermarkText').value = settings.text || '';
            document.getElementById('watermarkColor').value = settings.color || '#ff0000';
            document.getElementById('watermarkSize').value = settings.size || 24;
            document.getElementById('fontFamily').value = settings.fontFamily || 'Arial';
            document.getElementById('rotationAngle').value = settings.rotation || 0;
            document.getElementById('textOpacity').value = settings.textOpacity || 0.7;
            document.getElementById('enableBackground').checked = settings.enableBackground || false;
            document.getElementById('watermarkBg').value = settings.backgroundColor || '#ffffff';
            document.getElementById('bgOpacity').value = settings.bgOpacity || 0.8;
            watermarkPosition = { ...settings.position } || { x: 50, y: 50 };
            document.getElementById('watermarkPosition').value = settings.positionType || 'center';
            
            if (settings.pageOrientation) {
                document.getElementById('pageOrientation').value = settings.pageOrientation;
            }

            if (settings.imageWidth) {
                document.getElementById('imageWidth').value = settings.imageWidth;
            }
            if (settings.imageHeight) {
                document.getElementById('imageHeight').value = settings.imageHeight;
            }

            // New system settings
            if (settings.enableTextWatermark !== undefined) {
                document.getElementById('enableTextWatermark').checked = settings.enableTextWatermark;
                document.getElementById('textWatermarkSection').style.display = settings.enableTextWatermark ? 'block' : 'none';
            }

            if (settings.enableImageWatermark !== undefined) {
                document.getElementById('enableImageWatermark').checked = settings.enableImageWatermark;
                document.getElementById('imageWatermarkSection').style.display = settings.enableImageWatermark ? 'block' : 'none';
            }

            if (settings.enableAdvancedMode !== undefined) {
                document.getElementById('enableAdvancedMode').checked = settings.enableAdvancedMode;
                isAdvancedMode = settings.enableAdvancedMode;
                document.getElementById('advancedModeSection').classList.toggle('hidden', !settings.enableAdvancedMode);
            }

            // Single image
            if (settings.singleImage) {
                document.getElementById('imagePreviewImg').src = settings.singleImage;
                document.getElementById('imagePreview').style.display = 'block';
            }

            // Page watermarks (new system)
            if (settings.pageWatermarks) {
                pageWatermarks = JSON.parse(JSON.stringify(settings.pageWatermarks));
                if (isAdvancedMode && pdfDocument) {
                    updatePageWatermarksList();
                }
            }

            // Legacy support - convert old watermarkType to new system
            if (settings.watermarkType && !settings.enableTextWatermark && !settings.enableImageWatermark) {
                if (settings.watermarkType === 'text') {
                    document.getElementById('enableTextWatermark').checked = true;
                    document.getElementById('textWatermarkSection').style.display = 'block';
                } else if (settings.watermarkType === 'image') {
                    document.getElementById('enableImageWatermark').checked = true;
                    document.getElementById('imageWatermarkSection').style.display = 'block';
                }
            }

            // Legacy multiple images conversion
            if (settings.pageImages && Object.keys(settings.pageImages).length > 0) {
                // Convert old pageImages to new pageWatermarks format
                for (const [pageNum, pageData] of Object.entries(settings.pageImages)) {
                    if (!pageWatermarks[pageNum]) {
                        pageWatermarks[pageNum] = { text: {enabled: false}, image: {enabled: false} };
                    }
                    pageWatermarks[pageNum].image = {
                        enabled: true,
                        src: pageData.src,
                        position: pageData.position || { x: 50, y: 50 }
                    };
                }
                // Enable advanced mode for legacy presets
                document.getElementById('enableAdvancedMode').checked = true;
                isAdvancedMode = true;
                document.getElementById('advancedModeSection').classList.remove('hidden');
                if (pdfDocument) {
                    updatePageWatermarksList();
                }
            }

            // Update displays
            updateDisplay('watermarkSizeDisplay', (settings.size || 24) + 'px');
            updateDisplay('angleValue', (settings.rotation || 0) + '°');
            updateDisplay('textOpacityValue', Math.round((settings.textOpacity || 0.7) * 100) + '%');
            updateDisplay('bgOpacityValue', Math.round((settings.bgOpacity || 0.8) * 100) + '%');
            if (settings.imageWidth) updateDisplay('imageWidthDisplay', settings.imageWidth + 'px');
            if (settings.imageHeight) updateDisplay('imageHeightDisplay', settings.imageHeight + 'px');

            // Update background controls
            document.getElementById('backgroundControls').classList.toggle('hidden', !settings.enableBackground);

            if (pdfDocument || window.imageDocument) {
                generatePreview(); // สร้างตัวอย่างใหม่เพื่อแสดงการเปลี่ยนแปลง
            }
        }

        function savePreset() {
            const name = document.getElementById('presetName').value.trim();
            if (!name) {
                showMessage('กรุณาใส่ชื่อ Preset', 'error');
                return;
            }

            const settings = getCurrentSettings();
            const preset = {
                id: Date.now().toString(),
                name: name,
                settings: settings,
                createdAt: new Date().toLocaleString('th-TH')
            };

            const existingIndex = savedPresets.findIndex(p => p.name === name);
            if (existingIndex >= 0) {
                if (confirm(`มี Preset ชื่อ "${name}" อยู่แล้ว ต้องการแทนที่หรือไม่?`)) {
                    savedPresets[existingIndex] = preset;
                } else {
                    return;
                }
            } else {
                savedPresets.push(preset);
            }

            localStorage.setItem('watermarkPresets', JSON.stringify(savedPresets));
            loadPresetsList();
            document.getElementById('presetName').value = '';
            showMessage(`บันทึก Preset "${name}" เรียบร้อยแล้ว`, 'success');
        }

        function loadPresetsList() {
            const presetsList = document.getElementById('presetsList');
            
            if (savedPresets.length === 0) {
                presetsList.innerHTML = '<div class="no-presets">ยังไม่มี Presets ที่บันทึกไว้</div>';
                return;
            }

            presetsList.innerHTML = '';
            savedPresets.forEach(preset => {
                const presetElement = createPresetElement(preset);
                presetsList.appendChild(presetElement);
            });
        }

        function createPresetElement(preset) {
            const div = document.createElement('div');
            div.className = 'preset-item';
            if (activePresetId === preset.id) {
                div.classList.add('active');
            }

            const settings = preset.settings;
            
            div.innerHTML = `
                <div class="preset-header">
                    <span class="preset-name">${preset.name}</span>
                    <div class="preset-actions">
                        <button class="preset-btn" title="ใช้งาน Preset นี้" onclick="loadPreset('${preset.id}')">📂</button>
                        <button class="preset-btn" title="ลบ Preset นี้" onclick="deletePreset('${preset.id}')">🗑️</button>
                    </div>
                </div>
                <div class="preset-preview">
                    <div class="preset-detail">
                        <span>📝</span>
                        <span>"${settings.text.substring(0, 12)}${settings.text.length > 12 ? '...' : ''}"</span>
                    </div>
                    <div class="preset-detail">
                        <span>🎨</span>
                        <div class="preset-color-box" style="background-color: ${settings.color}"></div>
                        <span>${settings.size}px</span>
                    </div>
                    <div class="preset-detail">
                        <span>🔤</span>
                        <span>${settings.fontFamily.split(',')[0]}</span>
                    </div>
                    <div class="preset-detail">
                        <span>${settings.pageOrientation === 'landscape' ? '📑' : '📄'}</span>
                        <span>${settings.pageOrientation === 'landscape' ? 'แนวนอน' : 'แนวตั้ง'}</span>
                    </div>
                </div>
                <div style="font-size: 0.75rem; color: #999; margin-top: 6px;">
                    บันทึกเมื่อ: ${preset.createdAt}
                </div>
            `;

            return div;
        }

        function loadPreset(presetId) {
            const preset = savedPresets.find(p => p.id === presetId);
            if (!preset) {
                showMessage('ไม่พบ Preset ที่เลือก', 'error');
                return;
            }

            applySettings(preset.settings);
            activePresetId = presetId;
            loadPresetsList();
            showMessage(`โหลด Preset "${preset.name}" เรียบร้อยแล้ว`, 'success');
        }

        function deletePreset(presetId) {
            const preset = savedPresets.find(p => p.id === presetId);
            if (!preset) return;

            if (confirm(`ต้องการลบ Preset "${preset.name}" หรือไม่?`)) {
                savedPresets = savedPresets.filter(p => p.id !== presetId);
                localStorage.setItem('watermarkPresets', JSON.stringify(savedPresets));
                
                if (activePresetId === presetId) {
                    activePresetId = null;
                }
                
                loadPresetsList();
                showMessage(`ลบ Preset "${preset.name}" เรียบร้อยแล้ว`, 'success');
            }
        }

        function clearAllPresets() {
            if (savedPresets.length === 0) {
                showMessage('ไม่มี Presets ให้ลบ', 'error');
                return;
            }

            if (confirm(`ต้องการลบ Presets ทั้งหมด ${savedPresets.length} รายการหรือไม่?`)) {
                savedPresets = [];
                activePresetId = null;
                localStorage.removeItem('watermarkPresets');
                loadPresetsList();
                showMessage('ลบ Presets ทั้งหมดเรียบร้อยแล้ว', 'success');
            }
        }

        // Page Watermarks Management Functions
        function updatePageWatermarksList() {
            const pageWatermarksList = document.getElementById('pageWatermarksList');
            
            if (!pdfDocument || totalPages === 0) {
                pageWatermarksList.innerHTML = '<div class="no-presets">อัพโหลด PDF ก่อนเพื่อดูรายการหน้า</div>';
                return;
            }

            pageWatermarksList.innerHTML = '';
            for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
                const pageItem = createPageWatermarkItem(pageNum);
                pageWatermarksList.appendChild(pageItem);
            }
        }

        function createPageWatermarkItem(pageNum) {
            const div = document.createElement('div');
            div.className = 'page-watermark-item';
            
            const pageData = pageWatermarks[pageNum] || { text: {enabled: false}, image: {enabled: false} };
            
            let badges = [];
            if (pageData.text && pageData.text.enabled) {
                badges.push('<span class="watermark-badge text">📝 ข้อความ</span>');
            }
            if (pageData.image && pageData.image.enabled) {
                badges.push('<span class="watermark-badge image">🖼️ รูปภาพ</span>');
            }
            
            div.innerHTML = `
                <div class="page-watermark-header">
                    <span class="page-watermark-info">📄 หน้า ${pageNum}</span>
                    <div class="page-watermark-actions">
                        <button class="page-image-btn" onclick="editPageWatermark(${pageNum})" title="แก้ไข">
                            ⚙️ แก้ไข
                        </button>
                        <button class="page-image-btn" onclick="copyWatermarkToAll(${pageNum})" title="คัดลอกไปทุกหน้า">
                            📋 คัดลอก
                        </button>
                        <button class="page-image-btn danger" onclick="clearPageWatermark(${pageNum})" title="ล้าง">
                            🗑️ ล้าง
                        </button>
                    </div>
                </div>
                <div class="watermark-type-badges">
                    ${badges.length > 0 ? badges.join('') : '<span style="color: #999;">ไม่มี watermark</span>'}
                </div>
                ${(pageData.text && pageData.text.enabled) || (pageData.image && pageData.image.enabled) ? `
                <div class="watermark-content-preview">
                    ${pageData.text && pageData.text.enabled ? `<span>📝 "${pageData.text.content?.substring(0, 15)}${pageData.text.content?.length > 15 ? '...' : ''}"</span>` : ''}
                    ${pageData.image && pageData.image.enabled ? `<img src="${pageData.image.src}" alt="รูป">` : ''}
                </div>
                ` : ''}
            `;

            return div;
        }

        function editPageWatermark(pageNum) {
            // Navigate to the page
            if (currentPage !== pageNum) {
                currentPage = pageNum;
                updatePageInfo();
                generatePreview();
            }

            // Show modal or inline editor for this page
            showPageWatermarkEditor(pageNum);
        }

        function showPageWatermarkEditor(pageNum) {
            const pageData = pageWatermarks[pageNum] || { text: {enabled: false}, image: {enabled: false} };
            
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 1000;
            `;
            
            modal.innerHTML = `
                <div style="background: white; padding: 30px; border-radius: 15px; width: 500px; max-width: 90vw; max-height: 80vh; overflow-y: auto;">
                    <h3>แก้ไข Watermark หน้า ${pageNum}</h3>
                    
                    <div style="margin: 20px 0;">
                        <div class="checkbox-group">
                            <input type="checkbox" id="pageTextEnabled" ${pageData.text?.enabled ? 'checked' : ''}>
                            <label for="pageTextEnabled">เปิดใช้งานข้อความ</label>
                        </div>
                        <div id="pageTextSection" style="margin-top: 10px; ${pageData.text?.enabled ? '' : 'display: none;'}">
                            <input type="text" id="pageTextContent" class="form-control" 
                                   value="${pageData.text?.content || ''}" 
                                   placeholder="พิมพ์ข้อความที่ต้องการ">
                        </div>
                    </div>
                    
                    <div style="margin: 20px 0;">
                        <div class="checkbox-group">
                            <input type="checkbox" id="pageImageEnabled" ${pageData.image?.enabled ? 'checked' : ''}>
                            <label for="pageImageEnabled">เปิดใช้งานรูปภาพ</label>
                        </div>
                        <div id="pageImageSection" style="margin-top: 10px; ${pageData.image?.enabled ? '' : 'display: none;'}">
                            <input type="file" id="pageImageFile" class="form-control" accept="image/*">
                            ${pageData.image?.src ? `<img src="${pageData.image.src}" style="max-width: 100px; max-height: 60px; margin-top: 10px;">` : ''}
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <button onclick="closePageWatermarkEditor()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px;">ยกเลิก</button>
                        <button onclick="savePageWatermark(${pageNum})" style="padding: 10px 20px; background: #4facfe; color: white; border: none; border-radius: 5px;">บันทึก</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Add event handlers for the modal
            document.getElementById('pageTextEnabled').onchange = function() {
                document.getElementById('pageTextSection').style.display = this.checked ? 'block' : 'none';
            };
            
            document.getElementById('pageImageEnabled').onchange = function() {
                document.getElementById('pageImageSection').style.display = this.checked ? 'block' : 'none';
            };
            
            modal.onclick = (e) => {
                if (e.target === modal) closePageWatermarkEditor();
            };
        }

        function savePageWatermark(pageNum) {
            const textEnabled = document.getElementById('pageTextEnabled').checked;
            const textContent = document.getElementById('pageTextContent').value;
            const imageEnabled = document.getElementById('pageImageEnabled').checked;
            const imageFile = document.getElementById('pageImageFile').files[0];
            
            if (!pageWatermarks[pageNum]) {
                pageWatermarks[pageNum] = { text: {}, image: {} };
            }
            
            // Save text watermark
            pageWatermarks[pageNum].text = {
                enabled: textEnabled,
                content: textContent,
                position: pageWatermarks[pageNum].text?.position || { x: 50, y: 50 }
            };
            
            // Save image watermark
            const saveImageAndFinish = (imageSrc = null) => {
                pageWatermarks[pageNum].image = {
                    enabled: imageEnabled,
                    src: imageSrc || pageWatermarks[pageNum].image?.src || null,
                    position: pageWatermarks[pageNum].image?.position || { x: 50, y: 50 }
                };
                
                closePageWatermarkEditor();
                updatePageWatermarksList();
                updatePreview();
                showMessage(`บันทึกการตั้งค่าหน้า ${pageNum} เรียบร้อย`, 'success');
            };
            
            if (imageFile && imageEnabled) {
                const reader = new FileReader();
                reader.onload = (e) => saveImageAndFinish(e.target.result);
                reader.readAsDataURL(imageFile);
            } else {
                saveImageAndFinish();
            }
        }

        function closePageWatermarkEditor() {
            const modal = document.querySelector('.modal-overlay');
            if (modal) modal.remove();
        }

        function copyWatermarkToAll(pageNum) {
            const sourceData = pageWatermarks[pageNum];
            if (!sourceData) {
                showMessage('ไม่มีข้อมูل watermark ในหน้านี้', 'error');
                return;
            }
            
            if (confirm(`ต้องการคัดลอกการตั้งค่าจากหน้า ${pageNum} ไปยังทุกหน้าหรือไม่?`)) {
                for (let i = 1; i <= totalPages; i++) {
                    if (i !== pageNum) {
                        pageWatermarks[i] = JSON.parse(JSON.stringify(sourceData));
                    }
                }
                updatePageWatermarksList();
                updatePreview();
                showMessage(`คัดลอกการตั้งค่าไปยัง ${totalPages} หน้าเรียบร้อย`, 'success');
            }
        }

        function clearPageWatermark(pageNum) {
            if (confirm(`ต้องการลบ watermark ทั้งหมดในหน้า ${pageNum} หรือไม่?`)) {
                delete pageWatermarks[pageNum];
                updatePageWatermarksList();
                updatePreview();
                showMessage(`ลบ watermark หน้า ${pageNum} เรียบร้อย`, 'success');
            }
        }

        function createPageImageItem(pageNum) {
            const div = document.createElement('div');
            div.className = 'page-image-item';
            
            const hasImage = pageImages[pageNum] && pageImages[pageNum].src;
            const position = pageImages[pageNum] ? pageImages[pageNum].position : { x: 50, y: 50 };
            
            div.innerHTML = `
                <div class="page-image-header">
                    <span class="page-image-info">📄 หน้า ${pageNum}</span>
                    <div class="page-image-actions">
                        <button class="page-image-btn" onclick="selectImageForPage(${pageNum})" title="เลือกรูปภาพ">
                            ${hasImage ? '🖼️ เปลี่ยน' : '➕ เพิ่ม'}
                        </button>
                        <button class="page-image-btn" onclick="editPageImagePosition(${pageNum})" title="แก้ไขตำแหน่ง" ${!hasImage ? 'disabled' : ''}>
                            📍 ตำแหน่ง
                        </button>
                        ${hasImage ? `<button class="page-image-btn danger" onclick="removePageImage(${pageNum})" title="ลบรูปภาพ">🗑️</button>` : ''}
                    </div>
                </div>
                ${hasImage ? `
                <div class="page-image-preview">
                    <img src="${pageImages[pageNum].src}" alt="รูปภาพหน้า ${pageNum}">
                    <div class="position-indicator">ตำแหน่ง: ${Math.round(position.x)}, ${Math.round(position.y)}</div>
                </div>
                ` : '<div class="page-image-preview" style="color: #999;">ยังไม่มีรูปภาพ</div>'}
            `;

            return div;
        }

        function selectImageForPage(pageNum) {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = function(event) {
                const file = event.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (!pageImages[pageNum]) {
                            pageImages[pageNum] = {
                                position: { x: 50, y: 50 },
                                width: 200,
                                height: 200
                            };
                        }
                        pageImages[pageNum].src = e.target.result;
                        updatePageImagesList();
                        if (currentPage === pageNum) {
                            updatePreview();
                        }
                        showMessage(`เพิ่มรูปภาพสำหรับหน้า ${pageNum} เรียบร้อย`, 'success');
                    };
                    reader.readAsDataURL(file);
                } else {
                    showMessage('กรุณาเลือกไฟล์รูปภาพ', 'error');
                }
            };
            input.click();
        }

        function editPageImagePosition(pageNum) {
            if (!pageImages[pageNum] || !pageImages[pageNum].src) {
                showMessage('กรุณาเลือกรูปภาพก่อน', 'error');
                return;
            }
            
            // Navigate to the page to edit position
            if (currentPage !== pageNum) {
                currentPage = pageNum;
                updatePageInfo();
                generatePreview();
            }
            
            showMessage(`กำลังแสดงหน้า ${pageNum} - ลากรูปภาพเพื่อปรับตำแหน่ง`, 'success');
        }

        function removePageImage(pageNum) {
            if (confirm(`ต้องการลบรูปภาพในหน้า ${pageNum} หรือไม่?`)) {
                delete pageImages[pageNum];
                updatePageImagesList();
                if (currentPage === pageNum) {
                    updatePreview();
                }
                showMessage(`ลบรูปภาพหน้า ${pageNum} เรียบร้อย`, 'success');
            }
        }

        // Make functions global for onclick
        window.loadPreset = loadPreset;
        window.deletePreset = deletePreset;
        window.selectImageForPage = selectImageForPage;
        window.editPageImagePosition = editPageImagePosition;
        window.removePageImage = removePageImage;
        window.editPageWatermark = editPageWatermark;
        window.savePageWatermark = savePageWatermark;
        window.closePageWatermarkEditor = closePageWatermarkEditor;
        window.copyWatermarkToAll = copyWatermarkToAll;
        window.clearPageWatermark = clearPageWatermark;

        // Initialize application - เรียกหลังจาก define ฟังก์ชันทั้งหมดแล้ว
        setupEventListeners();
        loadPresetsList();
    </script>
</body>
</html>