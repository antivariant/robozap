
CKEDITOR.plugins.add('ie11selectionfix',
{

    init: function (editor) {
        // This plugin only applies to IE11+
        if (!(CKEDITOR.env.ie && CKEDITOR.env.version > 10)) {
            return;
        }

        (function () {

            var bookmarks;


            editor.on('beforeCommandExec', function (evt) {
	
				var selection = this.getSelection();
                try{
                    if (this.ie11_bookmarks && selection) {
                        selection.selectBookmarks(this.ie11_bookmarks);
                    }
                }catch(ex) {};
            });


            editor.on('insertElement', function (evt) {
                var selection = this.getSelection();
                if (this.ie11_bookmarks && selection)
                    selection.selectBookmarks(this.ie11_bookmarks);
            });

            CKEDITOR.editor.prototype.setBookmarks = function (bookmarks) {
                var selection = this.getSelection();
                if (bookmarks && selection)
                    selection.selectBookmarks(bookmarks);
            }

            editor.on('contentDom', function (evt) {

                bookmarks = null;

                this.document.getWindow().on('blur', function (evt) {
                    editor.ie11_bookmarks = bookmarks;
                });
				
				this.document.getWindow().on('focus', function (evt) {
                    editor.ie11_bookmarks = null;
                });

                this.document.on('selectionchange', function (evt) {
                    if (CKEDITOR.env.ie && CKEDITOR.env.version > 10) {
                        var selection = editor.getSelection();
                        if (selection)
                            bookmarks = selection.createBookmarks2(true);
                    }
                });

            });
        })();
    }
});    