const TIMING = 5000;

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

function hideNewBranch() {
    dojo.addClass("newbranch", "hidden");
    return( false );
}

function showNewBranch() {
    dojo.removeClass("newbranch", "hidden");
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
        form: "newDirForm",
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
}

function newBranchFormSubmit(e) {
    e.preventDefault();
    dojo.xhrPost({
        url: "/ajax/newbranch/",
        form: "newBranchForm",
        handleAs: "text",
        load: function(data){
            dojo.byId("navigation").innerHTML = data;
            console.log(data);
            hideNewBranch();
        },
        error:function(data,args){
            console.warn(data);
        }
    });
}

function saveFile(e) {
    e.preventDefault();
    dojo.xhrPost({
        url: "/ajax/save/",
        form: "editorForm",
        handleAs: "text",
        load: function(data){
            console.log(data);
        },
        error:function(data,args){
            console.warn(data);
        }
    });
}

function updateContent(d) {
    dojo.addClass("loader", "hidden");
}

function update() {
    var d;
    dojo.removeClass("loader", "hidden");
    d = new Date();
    console.log("update " + d.toLocaleString());
    dojo.xhrGet({
        url: "/ajax/update/",
        handleAs: "text",
        timeout: TIMING,
        load: function(data) {
            updateContent(d);
        },
        error: function(error) {
            dojo.addClass("loader", "hidden");
        }
    });
}

// function getFile(e) {
//     e.preventDefault();
//     dojo.xhrGet({
//         url: "/ajax/getfile/",
//         form: "",
//         handleAs "text",
//         load: function(data){
//             console.log(data);
//         },
//         error:function(data,args){
//             console.warn(data);
//         }
//     });
// }

function init() {
    dojo.require("dijit.dijit");
    var viewport = dijit.getViewport();
    editor = new CodeMirror.fromTextArea('code', {
    height: (viewport.h - 110) + "px",
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
    var $dirForm = dojo.byId("newDirForm");
    dojo.connect($dirForm, "onsubmit", "newDirFormSubmit");
    var $branchForm = dojo.byId("newBranchForm");
    dojo.connect($branchForm, "onsubmit", "newBranchFormSubmit");

    timer = setInterval("update()", TIMING);
}