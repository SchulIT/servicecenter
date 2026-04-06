import {
    ClassicEditor,
    Bold,
    Image,
    ImageUpload,
    Italic,
    Link,
    Paragraph,
    Strikethrough,
    Essentials,
    BlockQuote,
    List,
    Code,
    CodeBlock,
    Heading,
    Markdown,
    HorizontalLine,
    Emoji,
    Mention,
    Table,
    TableToolbar
} from 'ckeditor5';

import deTranslation from 'ckeditor5/translations/de.js';
import 'ckeditor5/dist/ckeditor5.min.css';

class ImageUploader {
    constructor(loader, editor) {
        this.loader = loader;
        this.editor = editor;
    }

    upload() {
        const url = this.editor.sourceElement.getAttribute('data-url');
        const csrfToken = this.editor.sourceElement.getAttribute('data-csrf-token');
        const csrfTokenParameter = this.editor.sourceElement.getAttribute('data-csrf-token-parameter');

        console.log(this.editor);

        return this.loader.file
            .then(file => new Promise((resolve, reject) => {
                this.xhr = new XMLHttpRequest();
                this.xhr.open('POST', url, true);
                this.xhr.responseType = 'json';

                this.xhr.addEventListener('error', () => reject('Fehler beim Upload.'));
                this.xhr.addEventListener('abort', () => reject());
                this.xhr.addEventListener('load', () => {
                    const response = this.xhr.response;

                    if(!response || response.error) {
                        return reject(response && response.error ? response.error : 'Fehler beim Upload.');
                    }

                    resolve({
                        default: response.filename
                    });
                });

                if(this.xhr.upload) {
                    this.xhr.upload.addEventListener('progress', event => {
                        if(event.lengthComputable) {
                            this.loader.uploadTotal = event.total;
                            this.loader.uploaded = event.loaded;
                        }
                    });
                }

                const data = new FormData();
                data.append(csrfTokenParameter, csrfToken);
                data.append('file', file);

                this.xhr.send(data);
            }));
    }

    abort() {
        if(this.xhr) {
            this.xhr.abort();
        }
    }
}

function ImageUploaderPlugin(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
        return new ImageUploader(loader, editor);
    }
}

for(let el of document.querySelectorAll('[data-editor=markdown]')) {
    ClassicEditor.create(
        el,
        {
            licenseKey: 'GPL',
            plugins: [
                Bold,
                Image,
                ImageUpload,
                ImageUploaderPlugin,
                Italic,
                Link,
                Paragraph,
                Strikethrough,
                Essentials,
                BlockQuote,
                List,
                Code,
                CodeBlock,
                Heading,
                Markdown,
                HorizontalLine,
                Emoji,
                Mention,
                Table,
                TableToolbar
            ],
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'strikethrough', '|',
                'bulletedList', 'numberedList', '|',
                'link', 'emoji', '|',
                'blockquote', 'insertTable', 'code', 'codeBlock', 'horizontalLine', '|',
                'undo', 'redo'
            ],
            table: {
                defaultHeadings: { rows: 1 },
                contentToolbar: [ 'tableColumn', 'tableRow' ]
            },
            translations: [
                deTranslation
            ]
        }
    );
}