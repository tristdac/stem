M.qtype_easyomechjs = {}
M.qtype_easyomechjs = {
    showmyresponse: function(Y, moodle_version, slot) {
        var refreshBut = Y.one("#myresponse" + slot, slot);
        refreshBut.on("click", function() {
            var newxmlStr = document.getElementById('my_answer' +
                slot).value;
            MarvinJSUtil.getEditor("#EASYOMECH" + slot).then(
                function(sketcherInstance) {
                    marvinController = new MarvinControllerClass(
                        sketcherInstance);
                    var pastePromise = marvinController.sketcherInstance
                        .importStructure("mrv", newxmlStr);
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
            MarvinJSUtil.getEditor("#EASYOMECH" + slot).then(
                function(sketcherInstance) {
                    marvinController = new MarvinControllerClass(
                        sketcherInstance);
                    var pastePromise = marvinController.sketcherInstance
                        .importStructure("mrv", newxmlStr);
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
    init_showarrowsrev: function(Y, moodle_version, slot) {
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
        var refreshBut = Y.one("#showorderrev" + slot, slot);
        refreshBut.on("click", function() {
            var xmlStr = document.getElementById(
                'correct_answer' + slot).value;
            ///parse xml string
            if (window.DOMParser) {
                parser = new DOMParser();
                xmlDoc = parser.parseFromString(xmlStr,
                    "text/xml");
                // alert('not IE');
            } else // Internet Explorer
            {
                xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
                xmlDoc.async = false;
                xmlDoc.loadXML(xmlStr);
            }
            meflowarrows = xmlDoc.getElementsByTagName("MEFlow");
            var arrowtot = meflowarrows.length;
            var currentarrow = Number(document.getElementById(
                'curarrow' + slot).value);
            currentarrow = currentarrow - 1;
            if (currentarrow < 0) {
                currentarrow = arrowtot;
            }
            document.getElementById('curarrow' + slot).value =
                Number(currentarrow);
            xAll = xmlDoc.getElementsByTagName('*');
            var i = 5,
                j, y, counter = 0,
                newxmlStr;
            for (j = xAll.length - 1; j >= 0; j -= 1) {
                y = xAll[j];
                if (y.nodeName == 'MEFlow') {
                    if (counter == arrowtot - currentarrow) {
                        j = 0;
                    } else {
                        y.parentNode.removeChild(y);
                    }
                    counter = counter + 1;
                }
            }
            newxmlStr = new XMLSerializer().serializeToString(
                xmlDoc);
            MarvinJSUtil.getEditor("#EASYOMECH" + slot).then(
                function(sketcherInstance) {
                    marvinController = new MarvinControllerClass(
                        sketcherInstance);
                    var pastePromise = marvinController.sketcherInstance
                        .importStructure("mrv", newxmlStr);
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
    init_showarrows: function(Y, moodle_version, slot) {
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
        var refreshBut = Y.one("#showorder" + slot, slot);
        refreshBut.on("click", function() {
            var xmlStr = document.getElementById(
                'correct_answer' + slot).value;
            ///parse xml string
            if (window.DOMParser) {
                parser = new DOMParser();
                xmlDoc = parser.parseFromString(xmlStr,
                    "text/xml");
            } else // Internet Explorer
            {
                xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
                xmlDoc.async = false;
                xmlDoc.loadXML(xmlStr);
            }
            meflowarrows = xmlDoc.getElementsByTagName("MEFlow");
            var arrowtot = meflowarrows.length;
            var currentarrow = Number(document.getElementById(
                'curarrow' + slot).value);
            currentarrow = currentarrow + 1;
            if (currentarrow > arrowtot) {
                currentarrow = 0;
            }
            document.getElementById('curarrow' + slot).value =
                Number(currentarrow);
            xAll = xmlDoc.getElementsByTagName('*');
            var i = 5,
                j, y, counter = 0,
                newxmlStr;
            for (j = xAll.length - 1; j >= 0; j -= 1) {
                y = xAll[j];
                if (y.nodeName == 'MEFlow') {
                    if (counter == arrowtot - currentarrow) {
                        j = 0;
                    } else {
                        y.parentNode.removeChild(y);
                    }
                    counter = counter + 1;
                }
            }
            newxmlStr = new XMLSerializer().serializeToString(
                xmlDoc);
            MarvinJSUtil.getEditor("#EASYOMECH" + slot).then(
                function(sketcherInstance) {
                    marvinController = new MarvinControllerClass(
                        sketcherInstance);
                    var pastePromise = marvinController.sketcherInstance
                        .importStructure("mrv", newxmlStr);
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
    insert_easyomechjs_applet: function(Y, toreplaceid, appletid, name,
        topnode, feedback, readonly, stripped_answer_id, moodleurl,
        marvinpath) {
        var javaparams = ['mol', Y.one(topnode + ' input.mol').get(
            'value')];
        var easyomechjsoptions = new Array();
        if (!this.show_java(toreplaceid, appletid, name, 600, 900,
            'chemaxon.marvin.applet.JMSketchLaunch', javaparams,
            stripped_answer_id, moodleurl, marvinpath)) {
            this.show_error(Y, topnode);
        } else {
            var marvinController,
                inputController;
            MarvinJSUtil.getEditor("#" + appletid).then(function(
                sketcherInstance) {
                marvinController = new MarvinControllerClass(
                    sketcherInstance);
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
		        exportPromise = marvinController.sketcherInstance.exportStructure("mrv", null);
		        exportPromise.then(function(source) {
				Y.one(topnode + ' input.answer').set('value', source);
		        }, this);

                }, this);
                var previewsubmit = inputform.one('input[name="finish"]');
                }

                if (previewsubmit != null) {
                previewsubmit.on(['mousedown', 'touchstart'], function(e) {
		        exportPromise = marvinController.sketcherInstance.exportStructure("mrv", null);
		        exportPromise.then(function(source) {
				Y.one(topnode + ' input.answer').set('value', source);
		        }, this);

                }, this);
                }
                var navbuttons = Y.all('a[id^="quiznavbutton"]');
                if (navbuttons != null) {
                navbuttons.on(['mousedown', 'touchstart'], function(e) {
		        exportPromise = marvinController.sketcherInstance.exportStructure("mrv", null);
		        exportPromise.then(function(source) {
				Y.one(topnode + ' input.answer').set('value', source);
		        }, this);
                }, this);
                }



/*
            var inputdiv = Y.one(topnode);
            if (inputdiv.ancestor('form') != null) {
		    inputdiv.ancestor('form').on('submit', function() {
		        exportPromise = marvinController.sketcherInstance
		            .exportStructure("mrv", null)
		        exportPromise.then(function(source) {
		            Y.one(topnode + ' input.answer').set(
		                'value', source);
		        });
		    }, this);
            }  */
        }
    },
    loadXMLString: function(txt) {
        if (window.DOMParser) {
            parser = new DOMParser();
            xmlDoc = parser.parseFromString(txt, "text/xml");
        } else // Internet Explorer
        {
            xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
            xmlDoc.async = false;
            xmlDoc.loadXML(txt);
        }
        return xmlDoc;
    },
    show_error: function(Y, topnode) {
        var errormessage = '<span class ="javawarning">' + M.util.get_string(
            'enablejava', 'qtype_easyomechjs') + '</span>';
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
                .importStructure("mrv", document.getElementById(
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
        answernumSpan.innerHTML = M.util.get_string('viewing_answer1', 'qtype_easyomechjs');
        warningspan.appendChild(answernumSpan);

        var newIframe = document.createElement("iframe");
        newIframe.src = marvinpath + "/editor.html";
        newIframe.className = "sketcher-frame";
        newIframe.id = "MSketch";
        //newIframe.width = "600";
        //newIframe.height = "460";
        warningspan.appendChild(newIframe);



        //import structure
        var marvinController;
        MarvinJSUtil.getEditor("#MSketch").then(function(
            sketcherInstance) {
            marvinController = new MarvinControllerClass(
                sketcherInstance);
            var pastePromise = marvinController.sketcherInstance
                .importStructure("mrv", document.getElementById(
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
M.qtype_easyomechjs.init_getanswerstring = function(Y, moodle_version) {
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
                    exportPromise = marvinController.sketcherInstance
                        .exportStructure("mrv", null)
                    exportPromise.then(function(source) {
                        Y.one('#' + textfieldid)
                            .set('value',
                                source);
                    });
                });
            var MarvinControllerClass = (function() {
                function MarvinControllerClass(
                    sketcherInstance
                ) {
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
M.qtype_easyomechjs.init_viewanswerstring = function(Y, moodle_version) {
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

                   var pastePromise = marvinController.sketcherInstance.importStructure("mrv", newxmlStr);
                   var answernumber = parseInt(buttonid.substr(buttonid.length - 1), 10) + 1;
                   Y.one('#answernumber').set('innerHTML', M.util.get_string('viewing_answer', 'qtype_easyomechjs')+' ' + answernumber);

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
