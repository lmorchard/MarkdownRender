/**
 * Main JS package for MarkdownRender
 */
var Render_Main = (function () {

    return {

        /**
         * Package initialization
         */
        init: function () {
            var $this = this;

            $(document).ready(function () { 
                $this.buildTOC();
            });
        },

        /**
         * Build a TOC list for each section on the page after the first header
         * of each section.
         */
        buildTOC: function () {
            var $this = this;

            // Recurse through the outline, searching for sections. Find the
            // first header for each section, inject and build a table of
            // contents list after it.
            (function (root) {
                var self = arguments.callee;

                // Recursively search for sections.
                if ('section' !== root.element.tagName.toLowerCase()) {
                    for (var i=0,child; child=root.childs[i]; i++) {
                        self(child);
                    }
                    return;
                }

                // Find the first header for the section, ensure it has an ID.
                var section = $(root.element);
                var h1 = section.find('>h1');
                if (!h1.attr('id')) {
                    h1.attr('id', hex_md5(h1.text()));
                }
                var parent_id = h1.attr('id');

                // Inject the table of contents list after the first header.
                var toc = h1.find('~ul.toc');
                if (!toc.length) {
                    toc = h1.after('<ul class="toc"></ul>').next();
                }

                // Recursively build the relevant table of contents for 
                // this section.
                (function (toc, root, parent_id) {
                    var toc_self = arguments.callee;

                    // Iterate through each of the current outline children to
                    // build list items and recursive sub-lists.
                    for (var i=0, child; child=root.childs[i]; i++) {

                        // Get the current outline element and title, and 
                        // ensure the element has an ID
                        var ele = $(child.element);
                        var title = ele.text();
                        if (!ele.attr('id')) {
                            ele.attr('id', 
                                hex_md5(parent_id + ': ' + title));
                        }
                        var id = ele.attr('id');

                        // Create a list item with a link, and get a 
                        // handle on both.
                        var li = toc.append('<li><a></a></li>')
                            .find('li:last');
                        var link = li.find('a:last');

                        // Set the text and href for the link.
                        link.text(title).attr('href', '#'+id);

                        // If there are children of this outline node, create a
                        // sub-list and recurse down to build it
                        if (child.childs.length > 0) {
                            toc_self(
                                li.append('<ul></ul>').find('ul:last'), 
                                child, id
                            );
                        }

                    }

                }(toc, root, parent_id));

            }(createOutline(document)[0]));

            if (!$('ul.toc').children().length) { 
                $('ul.toc').remove(); 
            }

        },

        EOF: null // I hate trailing comma errors
    };
}()).init();
