jQuery.fn.selection = function(){
        var element = (this.jquery) ? this[0] : this;
        var replace = arguments[0] || false;
        var move_to = arguments[1] || false;
        // Need to focus the given element to extract the selection
        element.focus();
        // IE sux!
        if (jQuery.browser.msie) {
                var s = document.selection.createRange();
                // Return or replace
                if (replace == false) {
                        return s.text;
                } else {
                        s.text = replace;
                        if (move_to > 0) {
                                var m = document.selection.createRange();
                                m.moveToBookmark(s.getBookmark());
                                m.collapse(true);
                                m.move('character', move_to);
                                m.select();
                        }
                        return this;
                }
        } else {
                var s = element.selectionStart;
                var e = element.selectionEnd;
                // Return or replace
                if (replace == false) {
                        return element.value.substr(s, (e - s));
                } else {
                        element.value = element.value.substr(0, s) + replace + element.value.substr(e, element.value.length);
                        if (move_to > 0) { element.setSelectionRange(s+move_to, s+move_to); }
                        return this;
                }
        }
};