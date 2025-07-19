function initTemplateEditor(placeholders) {
    // Initialize TinyMCE for each editor
    tinymce.init({
        selector: '.tinymce',
        height: 300,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist outdent indent | link table | placeholderButton | help',
        menubar: 'file edit view insert format tools table help',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; }',
        branding: false,
        promotion: false,
        setup: function(editor) {
            // Add custom button for placeholders
            editor.ui.registry.addButton('placeholderButton', {
                text: 'Insert Placeholder',
                onAction: function() {
                    // Create menu items for each placeholder
                    const items = Object.entries(placeholders).map(([key, description]) => ({
                        type: 'menuitem',
                        text: description,
                        onAction: function() {
                            editor.insertContent('[' + key + ']');
                        }
                    }));
                    
                    // Show the menu
                    editor.ui.registry.addMenuButton('placeholderButton', {
                        text: 'Insert Placeholder',
                        fetch: function(callback) {
                            callback(items);
                        }
                    });
                }
            });
        },
        // Prevent removing placeholders on paste
        paste_preprocess: function(plugin, args) {
            // Preserve [placeholder] tags
            args.content = args.content.replace(/\[([^\]]+)\]/g, '[$1]');
        },
        // Custom CSS for the editor
        content_css: [
            'https://fonts.googleapis.com/css2?family=Times+New+Roman&display=swap'
        ],
        style_formats: [
            { title: 'Heading 1', format: 'h1' },
            { title: 'Heading 2', format: 'h2' },
            { title: 'Heading 3', format: 'h3' },
            { title: 'Paragraph', format: 'p' },
            { title: 'Official Text', inline: 'span', styles: { 'font-family': 'Times New Roman' } },
            { title: 'Center Text', block: 'div', styles: { 'text-align': 'center' } },
            { title: 'Right Text', block: 'div', styles: { 'text-align': 'right' } },
            { title: 'Indent Text', block: 'div', styles: { 'margin-left': '40px' } }
        ]
    });

    // Add preview button functionality
    document.getElementById('previewButton')?.addEventListener('click', function() {
        // Get the current content from TinyMCE editors
        const headerContent = tinymce.get('header_content').getContent();
        const bodyContent = tinymce.get('body_content').getContent();
        const footerContent = tinymce.get('footer_content').getContent();
        
        // Create preview window
        const previewWindow = window.open('', '_blank');
        previewWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Template Preview</title>
                <style>
                    body {
                        font-family: "Times New Roman", serif;
                        margin: 40px;
                        line-height: 1.6;
                        color: #333;
                    }
                    .header { margin-bottom: 40px; }
                    .content { margin: 30px 0; }
                    .footer { margin-top: 40px; }
                    ${document.getElementById('custom_css').value}
                </style>
            </head>
            <body>
                <div class="header">${headerContent}</div>
                <div class="content">${bodyContent}</div>
                <div class="footer">${footerContent}</div>
            </body>
            </html>
        `);
        previewWindow.document.close();
    });
} 