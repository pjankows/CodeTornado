function hideNewFile() {
    dojo.addClass("newfile", "hidden");
    return( false );
}

function showNewFile() {
    dojo.removeClass("newfile", "hidden");
    return( false );
}

function hideNewDir() {
    dojo.addClass("newdir", "hidden");
    return( false );
}

function showNewDir() {
    dojo.removeClass("newdir", "hidden");
    return( false );
}

function newFileFormSubmit(e) {
    e.preventDefault();
    dojo.xhrPost({
        url: "/ajax/newfile/",
        form: "newFileForm",
        handleAs: "text",
        load: function(data){
            dojo.byId("navigation").innerHTML = data;
            console.log(data);
            hideNewFile();
        },
        error:function(data,args){
            console.warn(data);
        }
    });
};

function newDirFormSubmit(e) {
    e.preventDefault();
    dojo.xhrPost({
        url: "/ajax/newdir/",
        form: "newFileForm",
        handleAs: "text",
        load: function(data){
            dojo.byId("navigation").innerHTML = data;
            console.log(data);
            hideNewDir();
        },
        error:function(data,args){
            console.warn(data);
        }
    });
};

function init() {
    dojo.require("dijit.dijit");
    var viewport = dijit.getViewport();
    var editor = new CodeMirror.fromTextArea('code', {
    height: (viewport.h - 80) + "px",
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

    var $fileForm = dojo.byId("newFileForm");
    dojo.connect($fileForm, "onsubmit", "newFileFormSubmit");
    var $form = dojo.byId("newDirForm");
    dojo.connect($form, "onsubmit", "newDirFormSubmit");
}