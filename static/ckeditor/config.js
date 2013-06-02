/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config )
{
    config.toolbar = [
        {name: 'Bold', items: ['Bold']},
        {name: 'Italic', items: ['Italic']},
        {name: 'Underline', items: ['Underline']},
        {name: 'NumberedList', items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']},        
        {name: 'Outdent', items: ['Outdent']},
        {name: 'Indent', items: ['Indent']},
        {name: 'NumberedList', items: ['NumberedList']},
        {name: 'BulletedList', items: ['BulletedList']},
        {name: 'Blockquote', items: ['Blockquote']},
        {name: 'Image', items: ['Image']},
        {name: 'Smiley', items: ['Smiley']},
        {name: 'Link', items: ['Link']},
        {name: 'TextColor', items: ['TextColor']},
        {name: 'BGColor', items: ['BGColor']},
        {name: 'document', items: ['Source']},
        {name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize']}
    ];

    config.extraPlugins = 'onchange';
};
