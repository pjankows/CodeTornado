function hideNewFile() {
    dojo.addClass("newfile", "hidden");
    return( false );
}

function showNewFile() {
    dojo.removeClass("newfile", "hidden");
    return( false );
}

function newFileFormSubmit(e) {
    e.preventDefault();
    dojo.xhrPost({
        url: "/ajax/newfile/",
        form: "newFileForm",
        handleAs: "text",
        load: function(data){
            console.log(data);
        },
        error:function(data,args){
            console.warn(data);
        }
    });
};

function init() {
    var editor = new CodeMirror.fromTextArea('code', {
    height: "500px",
    lineNumbers: true,
    textWrapping: false,
    tabMode: "shift",
    indentUnit: 4,
    path: "/js/codemirror/",
    parserfile: [
        "parsexml.js",
        "parsecss.js",
        "tokenizejavascript.js",
        "parsejavascript.js",
        "tokenizephp.js",
        "parsephp.js",
        "parsephphtmlmixed.js"
        ],
    stylesheet: [
        "css/codemirror/xmlcolors.css",
        "css/codemirror/jscolors.css",
        "css/codemirror/csscolors.css",
        "css/codemirror/phpcolors.css"
        ]
    });

    var $form = dojo.byId("newFileForm");
    dojo.connect($form, "onsubmit", "newFileFormSubmit");
}