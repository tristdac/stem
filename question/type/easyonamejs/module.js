M.qtype_easyonamejs = {}
M.qtype_easyonamejs = {
    showmyresponse: function(Y, moodle_version, slot) {
        var refreshBut = Y.one("#myresponse" + slot, slot);
        refreshBut.on("click", function() {
            var newxmlStr = document.getElementById('my_answer' +
                slot).value;
            MarvinJSUtil.getEditor("#EASYONAMEJS" + slot).then(
                function(sketcherInstance) {
                    marvinController = new MarvinControllerClass(
                        sketcherInstance);
                    var pastePromise = marvinController.sketcherInstance
                        .importStructure("mol", newxmlStr);
                });
            var MarvinControllerClass = (function() {
                function MarvinControllerClass(
                    sketcherInstance) {
                    this.sketcherInstance =
                        sketcherInstance;
                    this.init();
                }
                MarvinControllerClass.prototype.init =
                    function init() {
                        this.sketcherInstance.setDisplaySettings({
                            "cpkColoring": true,
                            "lonePairsVisible": true,
                            "toolbars": "reporting"
                        });
                    };
                return MarvinControllerClass;
            }());
        });
    },
    showcorresponse: function(Y, moodle_version, slot) {
        var refreshBut = Y.one("#corresponse" + slot, slot);
        refreshBut.on("click", function() {
            var newxmlStr = document.getElementById(
                'correct_answer' + slot).value;
            MarvinJSUtil.getEditor("#EASYONAMEJS" + slot).then(
                function(sketcherInstance) {
                    marvinController = new MarvinControllerClass(
                        sketcherInstance);
                    var pastePromise = marvinController.sketcherInstance
                        .importStructure("mol", newxmlStr);
                });
            var MarvinControllerClass = (function() {
                function MarvinControllerClass(
                    sketcherInstance) {
                    this.sketcherInstance =
                        sketcherInstance;
                    this.init();
                }
                MarvinControllerClass.prototype.init =
                    function init() {
                        this.sketcherInstance.setDisplaySettings({
                            "cpkColoring": true,
                            "lonePairsVisible": true,
                            "toolbars": "reporting"
                        });
                    };
                return MarvinControllerClass;
            }());
        });
    },
    insert_easyonamejs_applet: function(Y, toreplaceid, appletid, name,
        topnode, feedback, readonly, stripped_answer_id, moodleurl,
        marvinpath) {
        var javaparams = ['mol', Y.one(topnode + ' input.mol').get(
            'value')];
       //var newxmlStr = document.getElementById('my_answer' +slot).value;
        var easyonamejsoptions = new Array();
        if (!this.show_java(toreplaceid, appletid, name, 600, 460,
            'chemaxon.marvin.applet.JMSketchLaunch', javaparams,
            stripped_answer_id, moodleurl, marvinpath)) {
            this.show_error(Y, topnode);
        } else {
            var marvinController,
                inputController;
            MarvinJSUtil.getEditor("#" + appletid).then(function(
                sketcherInstance) {
                marvinController = new MarvinControllerClass(sketcherInstance);
            });
            var MarvinControllerClass = (function() {
                function MarvinControllerClass(sketcherInstance) {
                    this.sketcherInstance =
                        sketcherInstance;
                    this.init();
                }
                MarvinControllerClass.prototype.init = function init() {};
                return MarvinControllerClass;
            }());

                var inputform = Y.one(topnode).ancestor('form');
                if (inputform != null) {
                var nextbutton = inputform.one('input[type=submit]');
                nextbutton.on(['mousedown', 'touchstart'], function(e) {
		        exportPromise = marvinController.sketcherInstance.exportStructure("mol", null);
		        exportPromise.then(function(source) {
		                source = source.replace("\n", 'MDL MOLFILE INSERTED\n');
				Y.one(topnode + ' input.answer').set('value', source);
		        }, this);

                }, this);
                var previewsubmit = inputform.one('input[name="finish"]');
                }


                if (previewsubmit != null) {
                previewsubmit.on(['mousedown', 'touchstart'], function(e) {
		        exportPromise = marvinController.sketcherInstance.exportStructure("mol", null);
		        exportPromise.then(function(source) {
		                source = source.replace("\n", 'MDL MOLFILE INSERTED\n');
				Y.one(topnode + ' input.answer').set('value', source);
		        }, this);

                }, this);
                }

                var navbuttons = Y.all('a[id^="quiznavbutton"]');
                navbuttons.on(['mousedown', 'touchstart'], function(e) {
		        exportPromise = marvinController.sketcherInstance.exportStructure("mol", null);
		        exportPromise.then(function(source) {
		                source = source.replace("\n", 'MDL MOLFILE INSERTED\n');
				Y.one(topnode + ' input.answer').set('value', source);
		        }, this);
                }, this);

           /*     var inputdiv = Y.one(topnode);
            if (inputdiv.ancestor('form') != null) {
                inputdiv.ancestor('form').on('submit', function(e) {
                exportPromise = marvinController.sketcherInstance.exportStructure("mol", null);
                exportPromise.then(function(source) {
                        source = source.replace("\n", 'MDL MOLFILE INSERTED\n');
			Y.one(topnode + ' input.answer').set('value', source);
                    });
                }, this);
            } */
        }
    },
    show_error: function(Y, topnode) {
        var errormessage = '<span class ="javawarning">' + M.util.get_string(
            'enablejava', 'qtype_easyonamejs') + '</span>';
        Y.one(topnode + ' .ablock').insert(errormessage, 1);
    },
    /**
     * Gets around problem in ie6 using name
     */
    find_java_applet: function(appletname) {
        for (appletno in document.applets) {
            if (document.applets[appletno].name == appletname) {
                return document.applets[appletno];
            }
        }
        return null;
    },
    nextappletid: 1,
    javainstalled: -99,
    doneie6focus: 0,
    doneie6focusapplets: 0,
    show_java: function(id, appletid, name, width, height, appletclass,
        javavars, stripped_answer_id, moodleurl, marvinpath) {
        var warningspan = document.getElementById(id);
        warningspan.innerHTML = '';
        var newIframe = document.createElement("iframe");
        newIframe.src = marvinpath + "/editor.html";
        newIframe.className = "sketcher-frame";
        newIframe.id = appletid;
        //newIframe.width = width;
        //newIframe.height = height;
        newIframe.setAttribute("data-toolbars", "reporting");
        warningspan.appendChild(newIframe);
        var marvinController,
            inputController;
        MarvinJSUtil.getEditor("#" + appletid).then(function(
            sketcherInstance) {
            marvinController = new MarvinControllerClass(
                sketcherInstance);
            var pastePromise = marvinController.sketcherInstance
                .importStructure("mol", document.getElementById(
                    stripped_answer_id).value);
        });
        var MarvinControllerClass = (function() {
            function MarvinControllerClass(sketcherInstance) {
                this.sketcherInstance = sketcherInstance;
                this.sketcherInstance.setDisplaySettings({
                    "cpkColoring": true,
                    "lonePairsVisible": true,
                    "toolbars": "reporting"
                });
                this.init();
            }
            MarvinControllerClass.prototype.init = function init() {};
            return MarvinControllerClass;
        }());
        return true;
    },
    insert_applet: function(Y, moodleurl, marvinpath) {
        var warningspan = document.getElementById('appletdiv');
        warningspan.innerHTML = '';

        var answernumSpan = document.createElement("span");
        answernumSpan.className = ".answernumber";
        answernumSpan.id = "answernumber";
        answernumSpan.innerHTML = M.util.get_string('viewing_answer1', 'qtype_easyonamejs');
        warningspan.appendChild(answernumSpan);

        var newIframe = document.createElement("iframe");
        newIframe.src = marvinpath + "/editor.html";
        newIframe.className = "sketcher-frame";
        newIframe.id = "MSketch";
        newIframe.width = "600";
        newIframe.height = "460";
        warningspan.appendChild(newIframe);
        //import structure
        var marvinController;
        MarvinJSUtil.getEditor("#MSketch").then(function(
            sketcherInstance) {
            marvinController = new MarvinControllerClass(
                sketcherInstance);
            var pastePromise = marvinController.sketcherInstance
                .importStructure("mol", document.getElementById(
                    'id_answer_0').value);
        });
        var MarvinControllerClass = (function() {
            function MarvinControllerClass(sketcherInstance) {
                this.sketcherInstance = sketcherInstance;
                this.init();
            }
            MarvinControllerClass.prototype.init = function init() {
                this.sketcherInstance.setDisplaySettings({
                    "cpkColoring": true,
                    "lonePairsVisible": true,
                    "toolbars": "reporting"
                });
            };
            return MarvinControllerClass;
        }());
    }
}
M.qtype_easyonamejs.init_getanswerstring = function(Y, moodle_version) {
    var handleSuccess = function(o) {};
    var handleFailure = function(o) {
        /*failure handler code*/
    };
    var callback = {
        success: handleSuccess,
        failure: handleFailure
    };
    if (moodle_version >= 2012120300) { //Moodle 2.4 or higher
        YAHOO = Y.YUI2;
    }
    Y.all(".id_insert").each(function(node) {
        node.on("click", function() {
            var marvinController,
                inputController;
            MarvinJSUtil.getEditor("#MSketch").then(
                function(sketcherInstance) {
                    marvinController = new MarvinControllerClass(
                        sketcherInstance);
                    var buttonid = node.getAttribute(
                        'id');
                    var textfieldid = 'id_answer_' +
                        buttonid.substr(buttonid.length -
                            1);

                    exportPromise = marvinController.sketcherInstance.exportStructure("mol", null);
                    exportPromise.then(function(source) {
					source = source.replace("\n", 'MDL MOLFILE INSERTED\n');
				        Y.one('#' + textfieldid).set('value', source);
                    });
                });
            var MarvinControllerClass = (function() {
                function MarvinControllerClass(
                    sketcherInstance) {
                    this.sketcherInstance =
                        sketcherInstance;
                    this.init();
                }
                MarvinControllerClass.prototype.init =
                    function init() {
                        this.sketcherInstance.setDisplaySettings({
                            "cpkColoring": true,
                            "lonePairsVisible": true,
                            "toolbars": "reporting"
                        });
                    };
                return MarvinControllerClass;
            }());
        });
    });
};

M.qtype_easyonamejs.init_viewanswerstring = function(Y, moodle_version) {
    var handleSuccess = function(o) {};
    var handleFailure = function(o) {
        /*failure handler code*/
    };
    var callback = {
        success: handleSuccess,
        failure: handleFailure
    };
    if (moodle_version >= 2012120300) { //Moodle 2.4 or higher
        YAHOO = Y.YUI2;
    }
    Y.all(".id_view").each(function(node) {
        node.on("click", function() {
            var marvinController,
                inputController;
            MarvinJSUtil.getEditor("#MSketch").then(
                function(sketcherInstance) {
                    marvinController = new MarvinControllerClass(
                        sketcherInstance);
                    var buttonid = node.getAttribute(
                        'id');
                    var textfieldid = 'id_answer_' +
                        buttonid.substr(buttonid.length -
                            1);

                   var newxmlStr = Y.one('#' + textfieldid).get('value');

                   var pastePromise = marvinController.sketcherInstance.importStructure("mol", newxmlStr);
                });
            var MarvinControllerClass = (function() {
                function MarvinControllerClass(
                    sketcherInstance) {
                    this.sketcherInstance =
                        sketcherInstance;
                    this.init();
                }
                MarvinControllerClass.prototype.init =
                    function init() {
                        this.sketcherInstance.setDisplaySettings({
                            "cpkColoring": true,
                            "lonePairsVisible": true,
                            "toolbars": "reporting"
                        });
                    };
                return MarvinControllerClass;
            }());
        });
    });
};

M.qtype_easyonamejs.init_viewanswerstring = function(Y, moodle_version) {
    var handleSuccess = function(o) {};
    var handleFailure = function(o) {
        /*failure handler code*/
    };
    var callback = {
        success: handleSuccess,
        failure: handleFailure
    };
    if (moodle_version >= 2012120300) { //Moodle 2.4 or higher
        YAHOO = Y.YUI2;
    }
    Y.all(".id_view").each(function(node) {
        node.on("click", function() {
            var marvinController,
                inputController;
            MarvinJSUtil.getEditor("#MSketch").then(
                function(sketcherInstance) {
                    marvinController = new MarvinControllerClass(
                        sketcherInstance);
                    var buttonid = node.getAttribute(
                        'id');

                    var textfieldid = 'id_answer_' +
                        buttonid.substr(buttonid.length -
                            1);

                   var newxmlStr = Y.one('#' + textfieldid).get('value');

                   var pastePromise = marvinController.sketcherInstance.importStructure("mol", newxmlStr);
                   var answernumber = parseInt(buttonid.substr(buttonid.length - 1), 10) + 1;
                   Y.one('#answernumber').set('innerHTML', M.util.get_string('viewing_answer', 'qtype_easyonamejs')+' ' + answernumber);

                });
            var MarvinControllerClass = (function() {
                function MarvinControllerClass(
                    sketcherInstance) {
                    this.sketcherInstance =
                        sketcherInstance;
                    this.init();
                }
                MarvinControllerClass.prototype.init =
                    function init() {
                        this.sketcherInstance.setDisplaySettings({
                            "cpkColoring": true,
                            "lonePairsVisible": true,
                            "toolbars": "reporting"
                        });
                    };
                return MarvinControllerClass;
            }());
        });
    });
};
