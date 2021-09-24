/* PNotify modules included in this custom build file:
animate
buttons
callbacks
confirm
history
mobile
*/
/*
PNotify 3.0.0 sciactive.com/pnotify/
(C) 2015 Hunter Perrin; Google, Inc.
license Apache-2.0
*/
/*
 * ====== PNotify ======
 *
 * http://sciactive.com/pnotify/
 *
 * Copyright 2009-2015 Hunter Perrin
 * Copyright 2015 Google, Inc.
 *
 * Licensed under Apache License, Version 2.0.
 * 	http://www.apache.org/licenses/LICENSE-2.0
 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as a module.
        define('pnotify', ['jquery'], function($){
            return factory($, root);
        });
    } else if (typeof exports === 'object' && typeof module !== 'undefined') {
        // CommonJS
        module.exports = factory(require('jquery'), global || root);
    } else {
        // Browser globals
        root.PNotify = factory(root.jQuery, root);
    }
}(this, function($, root){
    var init = function(root){
        var default_stack = {
            dir1: "down",
            dir2: "left",
            push: "bottom",
            spacing1: 36,
            spacing2: 36,
            context: $("body"),
            modal: false
        };
        var posTimer, // Position all timer.
            body,
            jwindow = $(root);
        // Set global variables.
        var do_when_ready = function(){
            body = $("body");
            PNotify.prototype.options.stack.context = body;
            jwindow = $(root);
            // Reposition the notices when the window resizes.
            jwindow.bind('resize', function(){
                if (posTimer) {
                    clearTimeout(posTimer);
                }
                posTimer = setTimeout(function(){
                    PNotify.positionAll(true);
                }, 10);
            });
        };
        var createStackOverlay = function(stack) {
            var overlay = $("<div />", {"class": "ui-pnotify-modal-overlay"});
            overlay.prependTo(stack.context);
            if (stack.overlay_close) {
                // Close the notices on overlay click.
                overlay.click(function(){
                    PNotify.removeStack(stack);
                });
            }
            return overlay;
        };
        var PNotify = function(options){
            this.parseOptions(options);
            this.init();
        };
        $.extend(PNotify.prototype, {
            // The current version of PNotify.
            version: "3.0.0",

            // === Options ===

            // Options defaults.
            options: {
                // The notice's title.
                title: false,
                // Whether to escape the content of the title. (Not allow HTML.)
                title_escape: false,
                // The notice's text.
                text: false,
                // Whether to escape the content of the text. (Not allow HTML.)
                text_escape: false,
                // What styling classes to use. (Can be either "brighttheme", "jqueryui", "bootstrap2", "bootstrap3", or "fontawesome".)
                styling: "brighttheme",
                // Additional classes to be added to the notice. (For custom styling.)
                addclass: "",
                // Class to be added to the notice for corner styling.
                cornerclass: "",
                // Display the notice when it is created.
                auto_display: true,
                // Width of the notice.
                width: "300px",
                // Minimum height of the notice. It will expand to fit content.
                min_height: "16px",
                // Type of the notice. "notice", "info", "success", or "error".
                type: "notice",
                // Set icon to true to use the default icon for the selected
                // style/type, false for no icon, or a string for your own icon class.
                icon: true,
                // The animation to use when displaying and hiding the notice. "none"
                // and "fade" are supported through CSS. Others are supported
                // through the Animate module and Animate.css.
                animation: "fade",
                // Speed at which the notice animates in and out. "slow", "normal",
                // or "fast". Respectively, 600ms, 400ms, 200ms.
                animate_speed: "normal",
                // Display a drop shadow.
                shadow: true,
                // After a delay, remove the notice.
                hide: true,
                // Delay in milliseconds before the notice is removed.
                delay: 8000,
                // Reset the hide timer if the mouse moves over the notice.
                mouse_reset: true,
                // Remove the notice's elements from the DOM after it is removed.
                remove: true,
                // Change new lines to br tags.
                insert_brs: true,
                // Whether to remove notices from the global array.
                destroy: true,
                // The stack on which the notices will be placed. Also controls the
                // direction the notices stack.
                stack: default_stack
            },

            // === Modules ===

            // This object holds all the PNotify modules. They are used to provide
            // additional functionality.
            modules: {},
            // This runs an event on all the modules.
            runModules: function(event, arg){
                var curArg;
                for (var module in this.modules) {
                    curArg = ((typeof arg === "object" && module in arg) ? arg[module] : arg);
                    if (typeof this.modules[module][event] === 'function') {
                        this.modules[module].notice = this;
                        this.modules[module].options = typeof this.options[module] === 'object' ? this.options[module] : {};
                        this.modules[module][event](this, typeof this.options[module] === 'object' ? this.options[module] : {}, curArg);
                    }
                }
            },

            // === Class Variables ===

            state: "initializing", // The state can be "initializing", "opening", "open", "closing", and "closed".
            timer: null, // Auto close timer.
            animTimer: null, // Animation timer.
            styles: null,
            elem: null,
            container: null,
            title_container: null,
            text_container: null,
            animating: false, // Stores what is currently being animated (in or out).
            timerHide: false, // Stores whether the notice was hidden by a timer.

            // === Events ===

            init: function(){
                var that = this;

                // First and foremost, we don't want our module objects all referencing the prototype.
                this.modules = {};
                $.extend(true, this.modules, PNotify.prototype.modules);

                // Get our styling object.
                if (typeof this.options.styling === "object") {
                    this.styles = this.options.styling;
                } else {
                    this.styles = PNotify.styling[this.options.styling];
                }

                // Create our widget.
                // Stop animation, reset the removal timer when the user mouses over.
                this.elem = $("<div />", {
                    "class": "ui-pnotify "+this.options.addclass,
                    "css": {"display": "none"},
                    "aria-live": "assertive",
                    "aria-role": "alertdialog",
                    "mouseenter": function(e){
                        if (that.options.mouse_reset && that.animating === "out") {
                            if (!that.timerHide) {
                                return;
                            }
                            that.cancelRemove();
                        }
                        // Stop the close timer.
                        if (that.options.hide && that.options.mouse_reset) {
                            that.cancelRemove();
                        }
                    },
                    "mouseleave": function(e){
                        // Start the close timer.
                        if (that.options.hide && that.options.mouse_reset && that.animating !== "out") {
                            that.queueRemove();
                        }
                        PNotify.positionAll();
                    }
                });
                // Maybe we need to fade in/out.
                if (this.options.animation === "fade") {
                    this.elem.addClass("ui-pnotify-fade-"+this.options.animate_speed);
                }
                // Create a container for the notice contents.
                this.container = $("<div />", {
                    "class": this.styles.container+" ui-pnotify-container "+(this.options.type === "error" ? this.styles.error : (this.options.type === "info" ? this.styles.info : (this.options.type === "success" ? this.styles.success : this.styles.notice))),
                    "role": "alert"
                }).appendTo(this.elem);
                if (this.options.cornerclass !== "") {
                    this.container.removeClass("ui-corner-all").addClass(this.options.cornerclass);
                }
                // Create a drop shadow.
                if (this.options.shadow) {
                    this.container.addClass("ui-pnotify-shadow");
                }


                // Add the appropriate icon.
                if (this.options.icon !== false) {
                    $("<div />", {"class": "ui-pnotify-icon"})
                        .append($("<span />", {"class": this.options.icon === true ? (this.options.type === "error" ? this.styles.error_icon : (this.options.type === "info" ? this.styles.info_icon : (this.options.type === "success" ? this.styles.success_icon : this.styles.notice_icon))) : this.options.icon}))
                        .prependTo(this.container);
                }

                // Add a title.
                this.title_container = $("<h4 />", {
                    "class": "ui-pnotify-title"
                })
                    .appendTo(this.container);
                if (this.options.title === false) {
                    this.title_container.hide();
                } else if (this.options.title_escape) {
                    this.title_container.text(this.options.title);
                } else {
                    this.title_container.html(this.options.title);
                }

                // Add text.
                this.text_container = $("<div />", {
                    "class": "ui-pnotify-text",
                    "aria-role": "alert"
                })
                    .appendTo(this.container);
                if (this.options.text === false) {
                    this.text_container.hide();
                } else if (this.options.text_escape) {
                    this.text_container.text(this.options.text);
                } else {
                    this.text_container.html(this.options.insert_brs ? String(this.options.text).replace(/\n/g, "<br />") : this.options.text);
                }

                // Set width and min height.
                if (typeof this.options.width === "string") {
                    this.elem.css("width", this.options.width);
                }
                if (typeof this.options.min_height === "string") {
                    this.container.css("min-height", this.options.min_height);
                }


                // Add the notice to the notice array.
                if (this.options.stack.push === "top") {
                    PNotify.notices = $.merge([this], PNotify.notices);
                } else {
                    PNotify.notices = $.merge(PNotify.notices, [this]);
                }
                // Now position all the notices if they are to push to the top.
                if (this.options.stack.push === "top") {
                    this.queuePosition(false, 1);
                }




                // Mark the stack so it won't animate the new notice.
                this.options.stack.animation = false;

                // Run the modules.
                this.runModules('init');

                // Display the notice.
                if (this.options.auto_display) {
                    this.open();
                }
                return this;
            },

            // This function is for updating the notice.
            update: function(options){
                // Save old options.
                var oldOpts = this.options;
                // Then update to the new options.
                this.parseOptions(oldOpts, options);
                // Maybe we need to fade in/out.
                this.elem.removeClass("ui-pnotify-fade-slow ui-pnotify-fade-normal ui-pnotify-fade-fast");
                if (this.options.animation === "fade") {
                    this.elem.addClass("ui-pnotify-fade-"+this.options.animate_speed);
                }
                // Update the corner class.
                if (this.options.cornerclass !== oldOpts.cornerclass) {
                    this.container.removeClass("ui-corner-all "+oldOpts.cornerclass).addClass(this.options.cornerclass);
                }
                // Update the shadow.
                if (this.options.shadow !== oldOpts.shadow) {
                    if (this.options.shadow) {
                        this.container.addClass("ui-pnotify-shadow");
                    } else {
                        this.container.removeClass("ui-pnotify-shadow");
                    }
                }
                // Update the additional classes.
                if (this.options.addclass === false) {
                    this.elem.removeClass(oldOpts.addclass);
                } else if (this.options.addclass !== oldOpts.addclass) {
                    this.elem.removeClass(oldOpts.addclass).addClass(this.options.addclass);
                }
                // Update the title.
                if (this.options.title === false) {
                    this.title_container.slideUp("fast");
                } else if (this.options.title !== oldOpts.title) {
                    if (this.options.title_escape) {
                        this.title_container.text(this.options.title);
                    } else {
                        this.title_container.html(this.options.title);
                    }
                    if (oldOpts.title === false) {
                        this.title_container.slideDown(200);
                    }
                }
                // Update the text.
                if (this.options.text === false) {
                    this.text_container.slideUp("fast");
                } else if (this.options.text !== oldOpts.text) {
                    if (this.options.text_escape) {
                        this.text_container.text(this.options.text);
                    } else {
                        this.text_container.html(this.options.insert_brs ? String(this.options.text).replace(/\n/g, "<br />") : this.options.text);
                    }
                    if (oldOpts.text === false) {
                        this.text_container.slideDown(200);
                    }
                }
                // Change the notice type.
                if (this.options.type !== oldOpts.type)
                    this.container.removeClass(
                        this.styles.error+" "+this.styles.notice+" "+this.styles.success+" "+this.styles.info
                    ).addClass(this.options.type === "error" ?
                        this.styles.error :
                        (this.options.type === "info" ?
                                this.styles.info :
                                (this.options.type === "success" ?
                                        this.styles.success :
                                        this.styles.notice
                                )
                        )
                    );
                if (this.options.icon !== oldOpts.icon || (this.options.icon === true && this.options.type !== oldOpts.type)) {
                    // Remove any old icon.
                    this.container.find("div.ui-pnotify-icon").remove();
                    if (this.options.icon !== false) {
                        // Build the new icon.
                        $("<div />", {"class": "ui-pnotify-icon"})
                            .append($("<span />", {"class": this.options.icon === true ? (this.options.type === "error" ? this.styles.error_icon : (this.options.type === "info" ? this.styles.info_icon : (this.options.type === "success" ? this.styles.success_icon : this.styles.notice_icon))) : this.options.icon}))
                            .prependTo(this.container);
                    }
                }
                // Update the width.
                if (this.options.width !== oldOpts.width) {
                    this.elem.animate({width: this.options.width});
                }
                // Update the minimum height.
                if (this.options.min_height !== oldOpts.min_height) {
                    this.container.animate({minHeight: this.options.min_height});
                }
                // Update the timed hiding.
                if (!this.options.hide) {
                    this.cancelRemove();
                } else if (!oldOpts.hide) {
                    this.queueRemove();
                }
                this.queuePosition(true);

                // Run the modules.
                this.runModules('update', oldOpts);
                return this;
            },

            // Display the notice.
            open: function(){
                this.state = "opening";
                // Run the modules.
                this.runModules('beforeOpen');

                var that = this;
                // If the notice is not in the DOM, append it.
                if (!this.elem.parent().length) {
                    this.elem.appendTo(this.options.stack.context ? this.options.stack.context : body);
                }
                // Try to put it in the right position.
                if (this.options.stack.push !== "top") {
                    this.position(true);
                }
                this.animateIn(function(){
                    that.queuePosition(true);

                    // Now set it to hide.
                    if (that.options.hide) {
                        that.queueRemove();
                    }

                    that.state = "open";

                    // Run the modules.
                    that.runModules('afterOpen');
                });

                return this;
            },

            // Remove the notice.
            remove: function(timer_hide) {
                this.state = "closing";
                this.timerHide = !!timer_hide; // Make sure it's a boolean.
                // Run the modules.
                this.runModules('beforeClose');

                var that = this;
                if (this.timer) {
                    root.clearTimeout(this.timer);
                    this.timer = null;
                }
                this.animateOut(function(){
                    that.state = "closed";
                    // Run the modules.
                    that.runModules('afterClose');
                    that.queuePosition(true);
                    // If we're supposed to remove the notice from the DOM, do it.
                    if (that.options.remove) {
                        that.elem.detach();
                    }
                    // Run the modules.
                    that.runModules('beforeDestroy');
                    // Remove object from PNotify.notices to prevent memory leak (issue #49)
                    // unless destroy is off
                    if (that.options.destroy) {
                        if (PNotify.notices !== null) {
                            var idx = $.inArray(that,PNotify.notices);
                            if (idx !== -1) {
                                PNotify.notices.splice(idx,1);
                            }
                        }
                    }
                    // Run the modules.
                    that.runModules('afterDestroy');
                });

                return this;
            },

            // === Class Methods ===

            // Get the DOM element.
            get: function(){
                return this.elem;
            },

            // Put all the options in the right places.
            parseOptions: function(options, moreOptions){
                this.options = $.extend(true, {}, PNotify.prototype.options);
                // This is the only thing that *should* be copied by reference.
                this.options.stack = PNotify.prototype.options.stack;
                var optArray = [options, moreOptions], curOpts;
                for (var curIndex=0; curIndex < optArray.length; curIndex++) {
                    curOpts = optArray[curIndex];
                    if (typeof curOpts === "undefined") {
                        break;
                    }
                    if (typeof curOpts !== 'object') {
                        this.options.text = curOpts;
                    } else {
                        for (var option in curOpts) {
                            if (this.modules[option]) {
                                // Avoid overwriting module defaults.
                                $.extend(true, this.options[option], curOpts[option]);
                            } else {
                                this.options[option] = curOpts[option];
                            }
                        }
                    }
                }
            },

            // Animate the notice in.
            animateIn: function(callback){
                // Declare that the notice is animating in.
                this.animating = "in";
                var that = this;
                callback = (function(){
                    if (that.animTimer) {
                        clearTimeout(that.animTimer);
                    }
                    if (that.animating !== "in") {
                        return;
                    }
                    if (that.elem.is(":visible")) {
                        if (this) {
                            this.call();
                        }
                        // Declare that the notice has completed animating.
                        that.animating = false;
                    } else {
                        that.animTimer = setTimeout(callback, 40);
                    }
                }).bind(callback);

                if (this.options.animation === "fade") {
                    this.elem.one('webkitTransitionEnd mozTransitionEnd MSTransitionEnd oTransitionEnd transitionend', callback).addClass("ui-pnotify-in");
                    this.elem.css("opacity"); // This line is necessary for some reason. Some notices don't fade without it.
                    this.elem.addClass("ui-pnotify-fade-in");
                    // Just in case the event doesn't fire, call it after 650 ms.
                    this.animTimer = setTimeout(callback, 650);
                } else {
                    this.elem.addClass("ui-pnotify-in");
                    callback();
                }
            },

            // Animate the notice out.
            animateOut: function(callback){
                // Declare that the notice is animating out.
                this.animating = "out";
                var that = this;
                callback = (function(){
                    if (that.animTimer) {
                        clearTimeout(that.animTimer);
                    }
                    if (that.animating !== "out") {
                        return;
                    }
                    if (that.elem.css("opacity") == "0" || !that.elem.is(":visible")) {
                        that.elem.removeClass("ui-pnotify-in");
                        if (this) {
                            this.call();
                        }
                        // Declare that the notice has completed animating.
                        that.animating = false;
                    } else {
                        // In case this was called before the notice finished animating.
                        that.animTimer = setTimeout(callback, 40);
                    }
                }).bind(callback);

                if (this.options.animation === "fade") {
                    this.elem.one('webkitTransitionEnd mozTransitionEnd MSTransitionEnd oTransitionEnd transitionend', callback).removeClass("ui-pnotify-fade-in");
                    // Just in case the event doesn't fire, call it after 650 ms.
                    this.animTimer = setTimeout(callback, 650);
                } else {
                    this.elem.removeClass("ui-pnotify-in");
                    callback();
                }
            },

            // Position the notice. dont_skip_hidden causes the notice to
            // position even if it's not visible.
            position: function(dontSkipHidden){
                // Get the notice's stack.
                var stack = this.options.stack,
                    elem = this.elem;
                if (typeof stack.context === "undefined") {
                    stack.context = body;
                }
                if (!stack) {
                    return;
                }
                if (typeof stack.nextpos1 !== "number") {
                    stack.nextpos1 = stack.firstpos1;
                }
                if (typeof stack.nextpos2 !== "number") {
                    stack.nextpos2 = stack.firstpos2;
                }
                if (typeof stack.addpos2 !== "number") {
                    stack.addpos2 = 0;
                }
                var hidden = !elem.hasClass("ui-pnotify-in");
                // Skip this notice if it's not shown.
                if (!hidden || dontSkipHidden) {
                    if (stack.modal) {
                        if (stack.overlay) {
                            stack.overlay.show();
                        } else {
                            stack.overlay = createStackOverlay(stack);
                        }
                    }
                    // Add animate class by default.
                    elem.addClass("ui-pnotify-move");
                    var curpos1, curpos2;
                    // Calculate the current pos1 value.
                    var csspos1;
                    switch (stack.dir1) {
                        case "down":
                            csspos1 = "top";
                            break;
                        case "up":
                            csspos1 = "bottom";
                            break;
                        case "left":
                            csspos1 = "right";
                            break;
                        case "right":
                            csspos1 = "left";
                            break;
                    }
                    curpos1 = parseInt(elem.css(csspos1).replace(/(?:\..*|[^0-9.])/g, ''));
                    if (isNaN(curpos1)) {
                        curpos1 = 0;
                    }
                    // Remember the first pos1, so the first visible notice goes there.
                    if (typeof stack.firstpos1 === "undefined" && !hidden) {
                        stack.firstpos1 = curpos1;
                        stack.nextpos1 = stack.firstpos1;
                    }
                    // Calculate the current pos2 value.
                    var csspos2;
                    switch (stack.dir2) {
                        case "down":
                            csspos2 = "top";
                            break;
                        case "up":
                            csspos2 = "bottom";
                            break;
                        case "left":
                            csspos2 = "right";
                            break;
                        case "right":
                            csspos2 = "left";
                            break;
                    }
                    curpos2 = parseInt(elem.css(csspos2).replace(/(?:\..*|[^0-9.])/g, ''));
                    if (isNaN(curpos2)) {
                        curpos2 = 0;
                    }
                    // Remember the first pos2, so the first visible notice goes there.
                    if (typeof stack.firstpos2 === "undefined" && !hidden) {
                        stack.firstpos2 = curpos2;
                        stack.nextpos2 = stack.firstpos2;
                    }
                    // Check that it's not beyond the viewport edge.
                    if (
                        (stack.dir1 === "down" && stack.nextpos1 + elem.height() > (stack.context.is(body) ? jwindow.height() : stack.context.prop('scrollHeight')) ) ||
                        (stack.dir1 === "up" && stack.nextpos1 + elem.height() > (stack.context.is(body) ? jwindow.height() : stack.context.prop('scrollHeight')) ) ||
                        (stack.dir1 === "left" && stack.nextpos1 + elem.width() > (stack.context.is(body) ? jwindow.width() : stack.context.prop('scrollWidth')) ) ||
                        (stack.dir1 === "right" && stack.nextpos1 + elem.width() > (stack.context.is(body) ? jwindow.width() : stack.context.prop('scrollWidth')) )
                    ) {
                        // If it is, it needs to go back to the first pos1, and over on pos2.
                        stack.nextpos1 = stack.firstpos1;
                        stack.nextpos2 += stack.addpos2 + (typeof stack.spacing2 === "undefined" ? 25 : stack.spacing2);
                        stack.addpos2 = 0;
                    }
                    if (typeof stack.nextpos2 === "number") {
                        if (!stack.animation) {
                            elem.removeClass("ui-pnotify-move");
                            elem.css(csspos2, stack.nextpos2+"px");
                            elem.css(csspos2);
                            elem.addClass("ui-pnotify-move");
                        } else {
                            elem.css(csspos2, stack.nextpos2+"px");
                        }
                    }
                    // Keep track of the widest/tallest notice in the column/row, so we can push the next column/row.
                    switch (stack.dir2) {
                        case "down":
                        case "up":
                            if (elem.outerHeight(true) > stack.addpos2) {
                                stack.addpos2 = elem.height();
                            }
                            break;
                        case "left":
                        case "right":
                            if (elem.outerWidth(true) > stack.addpos2) {
                                stack.addpos2 = elem.width();
                            }
                            break;
                    }
                    // Move the notice on dir1.
                    if (typeof stack.nextpos1 === "number") {
                        if (!stack.animation) {
                            elem.removeClass("ui-pnotify-move");
                            elem.css(csspos1, stack.nextpos1+"px");
                            elem.css(csspos1);
                            elem.addClass("ui-pnotify-move");
                        } else {
                            elem.css(csspos1, stack.nextpos1+"px");
                        }
                    }
                    // Calculate the next dir1 position.
                    switch (stack.dir1) {
                        case "down":
                        case "up":
                            stack.nextpos1 += elem.height() + (typeof stack.spacing1 === "undefined" ? 25 : stack.spacing1);
                            break;
                        case "left":
                        case "right":
                            stack.nextpos1 += elem.width() + (typeof stack.spacing1 === "undefined" ? 25 : stack.spacing1);
                            break;
                    }
                }
                return this;
            },
            // Queue the position all function so it doesn't run repeatedly and
            // use up resources.
            queuePosition: function(animate, milliseconds){
                if (posTimer) {
                    clearTimeout(posTimer);
                }
                if (!milliseconds) {
                    milliseconds = 10;
                }
                posTimer = setTimeout(function(){
                    PNotify.positionAll(animate);
                }, milliseconds);
                return this;
            },


            // Cancel any pending removal timer.
            cancelRemove: function(){
                if (this.timer) {
                    root.clearTimeout(this.timer);
                }
                if (this.animTimer) {
                    root.clearTimeout(this.animTimer);
                }
                if (this.state === "closing") {
                    // If it's animating out, stop it.
                    this.state = "open";
                    this.animating = false;
                    this.elem.addClass("ui-pnotify-in");
                    if (this.options.animation === "fade") {
                        this.elem.addClass("ui-pnotify-fade-in");
                    }
                }
                return this;
            },
            // Queue a removal timer.
            queueRemove: function(){
                var that = this;
                // Cancel any current removal timer.
                this.cancelRemove();
                this.timer = root.setTimeout(function(){
                    that.remove(true);
                }, (isNaN(this.options.delay) ? 0 : this.options.delay));
                return this;
            }
        });
        // These functions affect all notices.
        $.extend(PNotify, {
            // This holds all the notices.
            notices: [],
            reload: init,
            removeAll: function(){
                $.each(PNotify.notices, function(){
                    if (this.remove) {
                        this.remove(false);
                    }
                });
            },
            removeStack: function(stack){
                $.each(PNotify.notices, function(){
                    if (this.remove && this.options.stack === stack) {
                        this.remove(false);
                    }
                });
            },
            positionAll: function(animate){
                // This timer is used for queueing this function so it doesn't run
                // repeatedly.
                if (posTimer) {
                    clearTimeout(posTimer);
                }
                posTimer = null;
                // Reset the next position data.
                if (PNotify.notices && PNotify.notices.length) {
                    $.each(PNotify.notices, function(){
                        var s = this.options.stack;
                        if (!s) {
                            return;
                        }
                        if (s.overlay) {
                            s.overlay.hide();
                        }
                        s.nextpos1 = s.firstpos1;
                        s.nextpos2 = s.firstpos2;
                        s.addpos2 = 0;
                        s.animation = animate;
                    });
                    $.each(PNotify.notices, function(){
                        this.position();
                    });
                } else {
                    var s = PNotify.prototype.options.stack;
                    if (s) {
                        delete s.nextpos1;
                        delete s.nextpos2;
                    }
                }
            },
            styling: {
                brighttheme: {
                    // Bright Theme doesn't require any UI libraries.
                    container: "brighttheme",
                    notice: "brighttheme-notice",
                    notice_icon: "brighttheme-icon-notice",
                    info: "brighttheme-info",
                    info_icon: "brighttheme-icon-info",
                    success: "brighttheme-success",
                    success_icon: "brighttheme-icon-success",
                    error: "brighttheme-error",
                    error_icon: "brighttheme-icon-error"
                },
                jqueryui: {
                    container: "ui-widget ui-widget-content ui-corner-all",
                    notice: "ui-state-highlight",
                    // (The actual jQUI notice icon looks terrible.)
                    notice_icon: "ui-icon ui-icon-info",
                    info: "",
                    info_icon: "ui-icon ui-icon-info",
                    success: "ui-state-default",
                    success_icon: "ui-icon ui-icon-circle-check",
                    error: "ui-state-error",
                    error_icon: "ui-icon ui-icon-alert"
                },
                bootstrap3: {
                    container: "alert",
                    notice: "alert-warning",
                    notice_icon: "glyphicon glyphicon-exclamation-sign",
                    info: "alert-info",
                    info_icon: "glyphicon glyphicon-info-sign",
                    success: "alert-success",
                    success_icon: "glyphicon glyphicon-ok-sign",
                    error: "alert-danger",
                    error_icon: "glyphicon glyphicon-warning-sign"
                }
            }
        });
        /*
     * uses icons from http://fontawesome.io/
     * version 4.0.3
     */
        PNotify.styling.fontawesome = $.extend({}, PNotify.styling.bootstrap3);
        $.extend(PNotify.styling.fontawesome, {
            notice_icon: "fa fa-exclamation-circle",
            info_icon: "fa fa-info",
            success_icon: "fa fa-check",
            error_icon: "fa fa-warning"
        });

        if (root.document.body) {
            do_when_ready();
        } else {
            $(do_when_ready);
        }
        return PNotify;
    };
    return init(root);
}));
// Animate
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as a module.
        define('pnotify.animate', ['jquery', 'pnotify'], factory);
    } else if (typeof exports === 'object' && typeof module !== 'undefined') {
        // CommonJS
        module.exports = factory(require('jquery'), require('./pnotify'));
    } else {
        // Browser globals
        factory(root.jQuery, root.PNotify);
    }
}(this, function($, PNotify){
    PNotify.prototype.options.animate = {
        // Use animate.css to animate the notice.
        animate: false,
        // The class to use to animate the notice in.
        in_class: "",
        // The class to use to animate the notice out.
        out_class: ""
    };
    PNotify.prototype.modules.animate = {
        init: function(notice, options){
            this.setUpAnimations(notice, options);

            notice.attention = function(aniClass, callback){
                notice.elem.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
                    notice.elem.removeClass(aniClass);
                    if (callback) {
                        callback.call(notice);
                    }
                }).addClass("animated "+aniClass);
            };
        },

        update: function(notice, options, oldOpts){
            if (options.animate != oldOpts.animate) {
                this.setUpAnimations(notice, options)
            }
        },

        setUpAnimations: function(notice, options){
            if (options.animate) {
                notice.options.animation = "none";
                notice.elem.removeClass("ui-pnotify-fade-slow ui-pnotify-fade-normal ui-pnotify-fade-fast");
                if (!notice._animateIn) {
                    notice._animateIn = notice.animateIn;
                }
                if (!notice._animateOut) {
                    notice._animateOut = notice.animateOut;
                }
                notice.animateIn = this.animateIn.bind(this);
                notice.animateOut = this.animateOut.bind(this);
                var animSpeed = 400;
                if (notice.options.animate_speed === "slow") {
                    animSpeed = 600;
                } else if (notice.options.animate_speed === "fast") {
                    animSpeed = 200;
                } else if (notice.options.animate_speed > 0) {
                    animSpeed = notice.options.animate_speed;
                }
                animSpeed = animSpeed / 1000;
                notice.elem.addClass("animated").css({
                    "-webkit-animation-duration": animSpeed+"s",
                    "-moz-animation-duration": animSpeed+"s",
                    "animation-duration": animSpeed+"s"
                });
            } else if (notice._animateIn && notice._animateOut) {
                notice.animateIn = notice._animateIn;
                delete notice._animateIn;
                notice.animateOut = notice._animateOut;
                delete notice._animateOut;
                notice.elem.addClass("animated");
            }
        },

        animateIn: function(callback){
            // Declare that the notice is animating in.
            this.notice.animating = "in";
            var that = this;
            callback = (function(){
                if (this) {
                    this.call();
                }
                // Declare that the notice has completed animating.
                that.notice.animating = false;
            }).bind(callback);

            this.notice.elem.show().one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', callback).removeClass(this.options.out_class).addClass("ui-pnotify-in").addClass(this.options.in_class);
        },

        animateOut: function(callback){
            // Declare that the notice is animating out.
            this.notice.animating = "out";
            var that = this;
            callback = (function(){
                that.notice.elem.removeClass("ui-pnotify-in");
                if (this) {
                    this.call();
                }
                // Declare that the notice has completed animating.
                that.notice.animating = false;
            }).bind(callback);

            this.notice.elem.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', callback).removeClass(this.options.in_class).addClass(this.options.out_class);
        }
    };
}));
// Buttons
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as a module.
        define('pnotify.buttons', ['jquery', 'pnotify'], factory);
    } else if (typeof exports === 'object' && typeof module !== 'undefined') {
        // CommonJS
        module.exports = factory(require('jquery'), require('./pnotify'));
    } else {
        // Browser globals
        factory(root.jQuery, root.PNotify);
    }
}(this, function($, PNotify){
    PNotify.prototype.options.buttons = {
        // Provide a button for the user to manually close the notice.
        closer: true,
        // Only show the closer button on hover.
        closer_hover: true,
        // Provide a button for the user to manually stick the notice.
        sticker: true,
        // Only show the sticker button on hover.
        sticker_hover: true,
        // Show the buttons even when the nonblock module is in use.
        show_on_nonblock: false,
        // The various displayed text, helps facilitating internationalization.
        labels: {
            close: "Close",
            stick: "Stick",
            unstick: "Unstick"
        },
        // The classes to use for button icons. Leave them null to use the classes from the styling you're using.
        classes: {
            closer: null,
            pin_up: null,
            pin_down: null
        }
    };
    PNotify.prototype.modules.buttons = {
        closer: null,
        sticker: null,

        init: function(notice, options){
            var that = this;
            notice.elem.on({
                "mouseenter": function(e){
                    // Show the buttons.
                    if (that.options.sticker && (!(notice.options.nonblock && notice.options.nonblock.nonblock) || that.options.show_on_nonblock)) {
                        that.sticker.trigger("pnotify:buttons:toggleStick").css("visibility", "visible");
                    }
                    if (that.options.closer && (!(notice.options.nonblock && notice.options.nonblock.nonblock) || that.options.show_on_nonblock)) {
                        that.closer.css("visibility", "visible");
                    }
                },
                "mouseleave": function(e){
                    // Hide the buttons.
                    if (that.options.sticker_hover) {
                        that.sticker.css("visibility", "hidden");
                    }
                    if (that.options.closer_hover) {
                        that.closer.css("visibility", "hidden");
                    }
                }
            });

            // Provide a button to stick the notice.
            this.sticker = $("<div />", {
                "class": "ui-pnotify-sticker",
                "aria-role": "button",
                "aria-pressed": notice.options.hide ? "false" : "true",
                "tabindex": "0",
                "title": notice.options.hide ? options.labels.stick : options.labels.unstick,
                "css": {
                    "cursor": "pointer",
                    "visibility": options.sticker_hover ? "hidden" : "visible"
                },
                "click": function(){
                    notice.options.hide = !notice.options.hide;
                    if (notice.options.hide) {
                        notice.queueRemove();
                    } else {
                        notice.cancelRemove();
                    }
                    $(this).trigger("pnotify:buttons:toggleStick");
                }
            })
                .bind("pnotify:buttons:toggleStick", function(){
                    var pin_up = that.options.classes.pin_up === null ? notice.styles.pin_up : that.options.classes.pin_up;
                    var pin_down = that.options.classes.pin_down === null ? notice.styles.pin_down : that.options.classes.pin_down;
                    $(this)
                        .attr("title", notice.options.hide ? that.options.labels.stick : that.options.labels.unstick)
                        .children()
                        .attr("class", "")
                        .addClass(notice.options.hide ? pin_up : pin_down)
                        .attr("aria-pressed", notice.options.hide ? "false" : "true");
                })
                .append("<span />")
                .trigger("pnotify:buttons:toggleStick")
                .prependTo(notice.container);
            if (!options.sticker || (notice.options.nonblock && notice.options.nonblock.nonblock && !options.show_on_nonblock)) {
                this.sticker.css("display", "none");
            }

            // Provide a button to close the notice.
            this.closer = $("<div />", {
                "class": "ui-pnotify-closer",
                "aria-role": "button",
                "tabindex": "0",
                "title": options.labels.close,
                "css": {"cursor": "pointer", "visibility": options.closer_hover ? "hidden" : "visible"},
                "click": function(){
                    notice.remove(false);
                    that.sticker.css("visibility", "hidden");
                    that.closer.css("visibility", "hidden");
                }
            })
                .append($("<span />", {"class": options.classes.closer === null ? notice.styles.closer : options.classes.closer}))
                .prependTo(notice.container);
            if (!options.closer || (notice.options.nonblock && notice.options.nonblock.nonblock && !options.show_on_nonblock)) {
                this.closer.css("display", "none");
            }
        },
        update: function(notice, options){
            // Update the sticker and closer buttons.
            if (!options.closer || (notice.options.nonblock && notice.options.nonblock.nonblock && !options.show_on_nonblock)) {
                this.closer.css("display", "none");
            } else if (options.closer) {
                this.closer.css("display", "block");
            }
            if (!options.sticker || (notice.options.nonblock && notice.options.nonblock.nonblock && !options.show_on_nonblock)) {
                this.sticker.css("display", "none");
            } else if (options.sticker) {
                this.sticker.css("display", "block");
            }
            // Update the sticker icon.
            this.sticker.trigger("pnotify:buttons:toggleStick");
            // Update the close icon.
            this.closer.find("span").attr("class", "").addClass(options.classes.closer === null ? notice.styles.closer : options.classes.closer);
            // Update the hover status of the buttons.
            if (options.sticker_hover) {
                this.sticker.css("visibility", "hidden");
            } else if (!(notice.options.nonblock && notice.options.nonblock.nonblock && !options.show_on_nonblock)) {
                this.sticker.css("visibility", "visible");
            }
            if (options.closer_hover) {
                this.closer.css("visibility", "hidden");
            } else if (!(notice.options.nonblock && notice.options.nonblock.nonblock && !options.show_on_nonblock)) {
                this.closer.css("visibility", "visible");
            }
        }
    };
    $.extend(PNotify.styling.brighttheme, {
        closer: "brighttheme-icon-closer",
        pin_up: "brighttheme-icon-sticker",
        pin_down: "brighttheme-icon-sticker brighttheme-icon-stuck"
    });
    $.extend(PNotify.styling.jqueryui, {
        closer: "ui-icon ui-icon-close",
        pin_up: "ui-icon ui-icon-pin-w",
        pin_down: "ui-icon ui-icon-pin-s"
    });
    $.extend(PNotify.styling.bootstrap2, {
        closer: "icon-remove",
        pin_up: "icon-pause",
        pin_down: "icon-play"
    });
    $.extend(PNotify.styling.bootstrap3, {
        closer: "glyphicon glyphicon-remove",
        pin_up: "glyphicon glyphicon-pause",
        pin_down: "glyphicon glyphicon-play"
    });
    $.extend(PNotify.styling.fontawesome, {
        closer: "fa fa-times",
        pin_up: "fa fa-pause",
        pin_down: "fa fa-play"
    });
}));
// Callbacks
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as a module.
        define('pnotify.callbacks', ['jquery', 'pnotify'], factory);
    } else if (typeof exports === 'object' && typeof module !== 'undefined') {
        // CommonJS
        module.exports = factory(require('jquery'), require('./pnotify'));
    } else {
        // Browser globals
        factory(root.jQuery, root.PNotify);
    }
}(this, function($, PNotify){
    var _init   = PNotify.prototype.init,
        _open   = PNotify.prototype.open,
        _remove = PNotify.prototype.remove;
    PNotify.prototype.init = function(){
        if (this.options.before_init) {
            this.options.before_init(this.options);
        }
        _init.apply(this, arguments);
        if (this.options.after_init) {
            this.options.after_init(this);
        }
    };
    PNotify.prototype.open = function(){
        var ret;
        if (this.options.before_open) {
            ret = this.options.before_open(this);
        }
        if (ret !== false) {
            _open.apply(this, arguments);
            if (this.options.after_open) {
                this.options.after_open(this);
            }
        }
    };
    PNotify.prototype.remove = function(timer_hide){
        var ret;
        if (this.options.before_close) {
            ret = this.options.before_close(this, timer_hide);
        }
        if (ret !== false) {
            _remove.apply(this, arguments);
            if (this.options.after_close) {
                this.options.after_close(this, timer_hide);
            }
        }
    };
}));
// Confirm
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as a module.
        define('pnotify.confirm', ['jquery', 'pnotify'], factory);
    } else if (typeof exports === 'object' && typeof module !== 'undefined') {
        // CommonJS
        module.exports = factory(require('jquery'), require('./pnotify'));
    } else {
        // Browser globals
        factory(root.jQuery, root.PNotify);
    }
}(this, function($, PNotify){
    PNotify.prototype.options.confirm = {
        // Make a confirmation box.
        confirm: false,
        // Make a prompt.
        prompt: false,
        // Classes to add to the input element of the prompt.
        prompt_class: "",
        // The default value of the prompt.
        prompt_default: "",
        // Whether the prompt should accept multiple lines of text.
        prompt_multi_line: false,
        // Where to align the buttons. (right, center, left, justify)
        align: "right",
        // The buttons to display, and their callbacks.
        buttons: [
            {
                text: "Ok",
                addClass: "",
                // Whether to trigger this button when the user hits enter in a single line prompt.
                promptTrigger: true,
                click: function(notice, value){
                    notice.remove();
                    notice.get().trigger("pnotify.confirm", [notice, value]);
                }
            },
            {
                text: "Cancel",
                addClass: "",
                click: function(notice){
                    notice.remove();
                    notice.get().trigger("pnotify.cancel", notice);
                }
            }
        ]
    };
    PNotify.prototype.modules.confirm = {
        // The div that contains the buttons.
        container: null,
        // The input element of a prompt.
        prompt: null,

        init: function(notice, options){
            this.container = $('<div class="ui-pnotify-action-bar" style="margin-top:5px;clear:both;" />').css('text-align', options.align).appendTo(notice.container);

            if (options.confirm || options.prompt)
                this.makeDialog(notice, options);
            else
                this.container.hide();
        },

        update: function(notice, options){
            if (options.confirm) {
                this.makeDialog(notice, options);
                this.container.show();
            } else {
                this.container.hide().empty();
            }
        },

        afterOpen: function(notice, options){
            if (options.prompt)
                this.prompt.focus();
        },

        makeDialog: function(notice, options) {
            var already = false, that = this, btn, elem;
            this.container.empty();
            if (options.prompt) {
                this.prompt = $('<'+(options.prompt_multi_line ? 'textarea rows="5"' : 'input type="text"')+' style="margin-bottom:5px;clear:both;" />')
                    .addClass((typeof notice.styles.input === "undefined" ? "" : notice.styles.input)+" "+(typeof options.prompt_class === "undefined" ? "" : options.prompt_class))
                    .val(options.prompt_default)
                    .appendTo(this.container);
            }
            var customButtons = (options.buttons[0] && options.buttons[0] !== PNotify.prototype.options.confirm.buttons[0]);
            for (var i = 0; i < options.buttons.length; i++) {
                if (options.buttons[i] === null || (customButtons && PNotify.prototype.options.confirm.buttons[i] && PNotify.prototype.options.confirm.buttons[i] === options.buttons[i])) {
                    continue;
                }
                btn = options.buttons[i];
                if (already)
                    this.container.append(' ');
                else
                    already = true;
                elem = $('<button type="button" class="ui-pnotify-action-button" />')
                    .addClass((typeof notice.styles.btn === "undefined" ? "" : notice.styles.btn)+" "+(typeof btn.addClass === "undefined" ? "" : btn.addClass))
                    .text(btn.text)
                    .appendTo(this.container)
                    .on("click", (function(btn){ return function(){
                        if (typeof btn.click == "function") {
                            btn.click(notice, options.prompt ? that.prompt.val() : null);
                        }
                    }})(btn));
                if (options.prompt && !options.prompt_multi_line && btn.promptTrigger)
                    this.prompt.keypress((function(elem){ return function(e){
                        if (e.keyCode == 13)
                            elem.click();
                    }})(elem));
                if (notice.styles.text) {
                    elem.wrapInner('<span class="'+notice.styles.text+'"></span>');
                }
                if (notice.styles.btnhover) {
                    elem.hover((function(elem){ return function(){
                        elem.addClass(notice.styles.btnhover);
                    }})(elem), (function(elem){ return function(){
                        elem.removeClass(notice.styles.btnhover);
                    }})(elem));
                }
                if (notice.styles.btnactive) {
                    elem.on("mousedown", (function(elem){ return function(){
                        elem.addClass(notice.styles.btnactive);
                    }})(elem)).on("mouseup", (function(elem){ return function(){
                        elem.removeClass(notice.styles.btnactive);
                    }})(elem));
                }
                if (notice.styles.btnfocus) {
                    elem.on("focus", (function(elem){ return function(){
                        elem.addClass(notice.styles.btnfocus);
                    }})(elem)).on("blur", (function(elem){ return function(){
                        elem.removeClass(notice.styles.btnfocus);
                    }})(elem));
                }
            }
        }
    };
    $.extend(PNotify.styling.jqueryui, {
        btn: "ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only",
        btnhover: "ui-state-hover",
        btnactive: "ui-state-active",
        btnfocus: "ui-state-focus",
        input: "",
        text: "ui-button-text"
    });
    $.extend(PNotify.styling.bootstrap2, {
        btn: "btn",
        input: ""
    });
    $.extend(PNotify.styling.bootstrap3, {
        btn: "btn btn-default",
        input: "form-control"
    });
    $.extend(PNotify.styling.fontawesome, {
        btn: "btn btn-default",
        input: "form-control"
    });
}));
// History
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as a module.
        define('pnotify.history', ['jquery', 'pnotify'], factory);
    } else if (typeof exports === 'object' && typeof module !== 'undefined') {
        // CommonJS
        module.exports = factory(require('jquery'), require('./pnotify'));
    } else {
        // Browser globals
        factory(root.jQuery, root.PNotify);
    }
}(this, function($, PNotify){
    var history_menu,
        history_handle_top;
    $(function(){
        $("body").on("pnotify.history-all", function(){
            // Display all notices. (Disregarding non-history notices.)
            $.each(PNotify.notices, function(){
                if (this.modules.history.inHistory) {
                    if (this.elem.is(":visible")) {
                        // The hide variable controls whether the history pull down should
                        // queue a removal timer.
                        if (this.options.hide)
                            this.queueRemove();
                    } else if (this.open)
                        this.open();
                }
            });
        }).on("pnotify.history-last", function(){
            var pushTop = (PNotify.prototype.options.stack.push === "top");

            // Look up the last history notice, and display it.
            var i = (pushTop ? 0 : -1);

            var notice;
            do {
                if (i === -1)
                    notice = PNotify.notices.slice(i);
                else
                    notice = PNotify.notices.slice(i, i+1);
                if (!notice[0])
                    return false;

                i = (pushTop ? i + 1 : i - 1);
            } while (!notice[0].modules.history.inHistory || notice[0].elem.is(":visible"));
            if (notice[0].open)
                notice[0].open();
        });
    });
    PNotify.prototype.options.history = {
        // Place the notice in the history.
        history: true,
        // Display a pull down menu to redisplay previous notices.
        menu: false,
        // Make the pull down menu fixed to the top of the viewport.
        fixed: true,
        // Maximum number of notifications to have onscreen.
        maxonscreen: Infinity,
        // The various displayed text, helps facilitating internationalization.
        labels: {
            redisplay: "Redisplay",
            all: "All",
            last: "Last"
        }
    };
    PNotify.prototype.modules.history = {
        // The history variable controls whether the notice gets redisplayed
        // by the history pull down.
        inHistory: false,

        init: function(notice, options){
            // Make sure that no notices get destroyed.
            notice.options.destroy = false;

            this.inHistory = options.history;

            if (options.menu) {
                // If there isn't a history pull down, create one.
                if (typeof history_menu === "undefined") {
                    history_menu = $("<div />", {
                        "class": "ui-pnotify-history-container "+notice.styles.hi_menu,
                        "mouseleave": function(){
                            history_menu.animate({top: "-"+history_handle_top+"px"}, {duration: 100, queue: false});
                        }
                    })
                        .append($("<div />", {"class": "ui-pnotify-history-header", "text": options.labels.redisplay}))
                        .append($("<button />", {
                            "class": "ui-pnotify-history-all "+notice.styles.hi_btn,
                            "text": options.labels.all,
                            "mouseenter": function(){
                                $(this).addClass(notice.styles.hi_btnhov);
                            },
                            "mouseleave": function(){
                                $(this).removeClass(notice.styles.hi_btnhov);
                            },
                            "click": function(){
                                $(this).trigger("pnotify.history-all");
                                return false;
                            }
                        }))
                        .append($("<button />", {
                            "class": "ui-pnotify-history-last "+notice.styles.hi_btn,
                            "text": options.labels.last,
                            "mouseenter": function(){
                                $(this).addClass(notice.styles.hi_btnhov);
                            },
                            "mouseleave": function(){
                                $(this).removeClass(notice.styles.hi_btnhov);
                            },
                            "click": function(){
                                $(this).trigger("pnotify.history-last");
                                return false;
                            }
                        }))
                        .appendTo("body");

                    // Make a handle so the user can pull down the history tab.
                    var handle = $("<span />", {
                        "class": "ui-pnotify-history-pulldown "+notice.styles.hi_hnd,
                        "mouseenter": function(){
                            history_menu.animate({top: "0"}, {duration: 100, queue: false});
                        }
                    })
                        .appendTo(history_menu);

                    // Get the top of the handle.
                    history_handle_top = handle.offset().top + 2;
                    // Hide the history pull down up to the top of the handle.
                    history_menu.css({top: "-"+history_handle_top+"px"});

                    // Apply the fixed styling.
                    if (options.fixed) {
                        history_menu.addClass('ui-pnotify-history-fixed');
                    }
                }
            }
        },
        update: function(notice, options){
            // Update values for history menu access.
            this.inHistory = options.history;
            if (options.fixed && history_menu) {
                history_menu.addClass('ui-pnotify-history-fixed');
            } else if (history_menu) {
                history_menu.removeClass('ui-pnotify-history-fixed');
            }
        },
        beforeOpen: function(notice, options){
            // Remove oldest notifications leaving only options.maxonscreen on screen
            if (PNotify.notices && (PNotify.notices.length > options.maxonscreen)) {
                // Oldest are normally in front of array, or if stack.push=="top" then
                // they are at the end of the array! (issue #98)
                var el;
                if (notice.options.stack.push !== "top")
                    el = PNotify.notices.slice(0, PNotify.notices.length - options.maxonscreen);
                else
                    el = PNotify.notices.slice(options.maxonscreen, PNotify.notices.length);

                $.each(el, function(){
                    if (this.remove)
                        this.remove();
                });
            }
        }
    };
    $.extend(PNotify.styling.jqueryui, {
        hi_menu: "ui-state-default ui-corner-bottom",
        hi_btn: "ui-state-default ui-corner-all",
        hi_btnhov: "ui-state-hover",
        hi_hnd: "ui-icon ui-icon-grip-dotted-horizontal"
    });
    $.extend(PNotify.styling.bootstrap2, {
        hi_menu: "well",
        hi_btn: "btn",
        hi_btnhov: "",
        hi_hnd: "icon-chevron-down"
    });
    $.extend(PNotify.styling.bootstrap3, {
        hi_menu: "well",
        hi_btn: "btn btn-default",
        hi_btnhov: "",
        hi_hnd: "glyphicon glyphicon-chevron-down"
    });
    $.extend(PNotify.styling.fontawesome, {
        hi_menu: "well",
        hi_btn: "btn btn-default",
        hi_btnhov: "",
        hi_hnd: "fa fa-chevron-down"
    });
}));
// Mobile
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as a module.
        define('pnotify.mobile', ['jquery', 'pnotify'], factory);
    } else if (typeof exports === 'object' && typeof module !== 'undefined') {
        // CommonJS
        module.exports = factory(require('jquery'), require('./pnotify'));
    } else {
        // Browser globals
        factory(root.jQuery, root.PNotify);
    }
}(this, function($, PNotify){
    PNotify.prototype.options.mobile = {
        // Let the user swipe the notice away.
        swipe_dismiss: true,
        // Styles the notice to look good on mobile.
        styling: true
    };
    PNotify.prototype.modules.mobile = {
        swipe_dismiss: true,

        init: function(notice, options){
            var that = this,
                origX = null,
                diffX = null,
                noticeWidth = null;

            this.swipe_dismiss = options.swipe_dismiss;
            this.doMobileStyling(notice, options);

            notice.elem.on({
                "touchstart": function(e){
                    if (!that.swipe_dismiss) {
                        return;
                    }

                    origX = e.originalEvent.touches[0].screenX;
                    noticeWidth = notice.elem.width();
                    notice.container.css("left", "0");
                },
                "touchmove": function(e){
                    if (!origX || !that.swipe_dismiss) {
                        return;
                    }

                    var curX = e.originalEvent.touches[0].screenX;

                    diffX = curX - origX;
                    var opacity = (1 - (Math.abs(diffX) / noticeWidth)) * notice.options.opacity;

                    notice.elem.css("opacity", opacity);
                    notice.container.css("left", diffX);
                },
                "touchend": function() {
                    if (!origX || !that.swipe_dismiss) {
                        return;
                    }

                    if (Math.abs(diffX) > 40) {
                        var goLeft = (diffX < 0) ? noticeWidth * -2 : noticeWidth * 2;
                        notice.elem.animate({"opacity": 0}, 100);
                        notice.container.animate({"left": goLeft}, 100);
                        notice.remove();
                    } else {
                        notice.elem.animate({"opacity": notice.options.opacity}, 100);
                        notice.container.animate({"left": 0}, 100);
                    }
                    origX = null;
                    diffX = null;
                    noticeWidth = null;
                },
                "touchcancel": function(){
                    if (!origX || !that.swipe_dismiss) {
                        return;
                    }

                    notice.elem.animate({"opacity": notice.options.opacity}, 100);
                    notice.container.animate({"left": 0}, 100);
                    origX = null;
                    diffX = null;
                    noticeWidth = null;
                }
            });
        },
        update: function(notice, options){
            this.swipe_dismiss = options.swipe_dismiss;
            this.doMobileStyling(notice, options);
        },
        doMobileStyling: function(notice, options){
            if (options.styling) {
                notice.elem.addClass("ui-pnotify-mobile-able");

                if ($(window).width() <= 480) {
                    if (!notice.options.stack.mobileOrigSpacing1) {
                        notice.options.stack.mobileOrigSpacing1 = notice.options.stack.spacing1;
                        notice.options.stack.mobileOrigSpacing2 = notice.options.stack.spacing2;
                    }
                    notice.options.stack.spacing1 = 0;
                    notice.options.stack.spacing2 = 0;
                } else if (notice.options.stack.mobileOrigSpacing1 || notice.options.stack.mobileOrigSpacing2) {
                    notice.options.stack.spacing1 = notice.options.stack.mobileOrigSpacing1;
                    delete notice.options.stack.mobileOrigSpacing1;
                    notice.options.stack.spacing2 = notice.options.stack.mobileOrigSpacing2;
                    delete notice.options.stack.mobileOrigSpacing2;
                }
            } else {
                notice.elem.removeClass("ui-pnotify-mobile-able");

                if (notice.options.stack.mobileOrigSpacing1) {
                    notice.options.stack.spacing1 = notice.options.stack.mobileOrigSpacing1;
                    delete notice.options.stack.mobileOrigSpacing1;
                }
                if (notice.options.stack.mobileOrigSpacing2) {
                    notice.options.stack.spacing2 = notice.options.stack.mobileOrigSpacing2;
                    delete notice.options.stack.mobileOrigSpacing2;
                }
            }
        }
    };
}));
/*! Magnific Popup - v1.0.0 - 2015-01-03
* http://dimsemenov.com/plugins/magnific-popup/
* Copyright (c) 2015 Dmitry Semenov; */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):a("object"==typeof exports?require("jquery"):window.jQuery||window.Zepto)}(function(a){var b,c,d,e,f,g,h="Close",i="BeforeClose",j="AfterClose",k="BeforeAppend",l="MarkupParse",m="Open",n="Change",o="mfp",p="."+o,q="mfp-ready",r="mfp-removing",s="mfp-prevent-close",t=function(){},u=!!window.jQuery,v=a(window),w=function(a,c){b.ev.on(o+a+p,c)},x=function(b,c,d,e){var f=document.createElement("div");return f.className="mfp-"+b,d&&(f.innerHTML=d),e?c&&c.appendChild(f):(f=a(f),c&&f.appendTo(c)),f},y=function(c,d){b.ev.triggerHandler(o+c,d),b.st.callbacks&&(c=c.charAt(0).toLowerCase()+c.slice(1),b.st.callbacks[c]&&b.st.callbacks[c].apply(b,a.isArray(d)?d:[d]))},z=function(c){return c===g&&b.currTemplate.closeBtn||(b.currTemplate.closeBtn=a(b.st.closeMarkup.replace("%title%",b.st.tClose)),g=c),b.currTemplate.closeBtn},A=function(){a.magnificPopup.instance||(b=new t,b.init(),a.magnificPopup.instance=b)},B=function(){var a=document.createElement("p").style,b=["ms","O","Moz","Webkit"];if(void 0!==a.transition)return!0;for(;b.length;)if(b.pop()+"Transition"in a)return!0;return!1};t.prototype={constructor:t,init:function(){var c=navigator.appVersion;b.isIE7=-1!==c.indexOf("MSIE 7."),b.isIE8=-1!==c.indexOf("MSIE 8."),b.isLowIE=b.isIE7||b.isIE8,b.isAndroid=/android/gi.test(c),b.isIOS=/iphone|ipad|ipod/gi.test(c),b.supportsTransition=B(),b.probablyMobile=b.isAndroid||b.isIOS||/(Opera Mini)|Kindle|webOS|BlackBerry|(Opera Mobi)|(Windows Phone)|IEMobile/i.test(navigator.userAgent),d=a(document),b.popupsCache={}},open:function(c){var e;if(c.isObj===!1){b.items=c.items.toArray(),b.index=0;var g,h=c.items;for(e=0;e<h.length;e++)if(g=h[e],g.parsed&&(g=g.el[0]),g===c.el[0]){b.index=e;break}}else b.items=a.isArray(c.items)?c.items:[c.items],b.index=c.index||0;if(b.isOpen)return void b.updateItemHTML();b.types=[],f="",b.ev=c.mainEl&&c.mainEl.length?c.mainEl.eq(0):d,c.key?(b.popupsCache[c.key]||(b.popupsCache[c.key]={}),b.currTemplate=b.popupsCache[c.key]):b.currTemplate={},b.st=a.extend(!0,{},a.magnificPopup.defaults,c),b.fixedContentPos="auto"===b.st.fixedContentPos?!b.probablyMobile:b.st.fixedContentPos,b.st.modal&&(b.st.closeOnContentClick=!1,b.st.closeOnBgClick=!1,b.st.showCloseBtn=!1,b.st.enableEscapeKey=!1),b.bgOverlay||(b.bgOverlay=x("bg").on("click"+p,function(){b.close()}),b.wrap=x("wrap").attr("tabindex",-1).on("click"+p,function(a){b._checkIfClose(a.target)&&b.close()}),b.container=x("container",b.wrap)),b.contentContainer=x("content"),b.st.preloader&&(b.preloader=x("preloader",b.container,b.st.tLoading));var i=a.magnificPopup.modules;for(e=0;e<i.length;e++){var j=i[e];j=j.charAt(0).toUpperCase()+j.slice(1),b["init"+j].call(b)}y("BeforeOpen"),b.st.showCloseBtn&&(b.st.closeBtnInside?(w(l,function(a,b,c,d){c.close_replaceWith=z(d.type)}),f+=" mfp-close-btn-in"):b.wrap.append(z())),b.st.alignTop&&(f+=" mfp-align-top"),b.wrap.css(b.fixedContentPos?{overflow:b.st.overflowY,overflowX:"hidden",overflowY:b.st.overflowY}:{top:v.scrollTop(),position:"absolute"}),(b.st.fixedBgPos===!1||"auto"===b.st.fixedBgPos&&!b.fixedContentPos)&&b.bgOverlay.css({height:d.height(),position:"absolute"}),b.st.enableEscapeKey&&d.on("keyup"+p,function(a){27===a.keyCode&&b.close()}),v.on("resize"+p,function(){b.updateSize()}),b.st.closeOnContentClick||(f+=" mfp-auto-cursor"),f&&b.wrap.addClass(f);var k=b.wH=v.height(),n={};if(b.fixedContentPos&&b._hasScrollBar(k)){var o=b._getScrollbarSize();o&&(n.marginRight=o)}b.fixedContentPos&&(b.isIE7?a("body, html").css("overflow","hidden"):n.overflow="hidden");var r=b.st.mainClass;return b.isIE7&&(r+=" mfp-ie7"),r&&b._addClassToMFP(r),b.updateItemHTML(),y("BuildControls"),a("html").css(n),b.bgOverlay.add(b.wrap).prependTo(b.st.prependTo||a(document.body)),b._lastFocusedEl=document.activeElement,setTimeout(function(){b.content?(b._addClassToMFP(q),b._setFocus()):b.bgOverlay.addClass(q),d.on("focusin"+p,b._onFocusIn)},16),b.isOpen=!0,b.updateSize(k),y(m),c},close:function(){b.isOpen&&(y(i),b.isOpen=!1,b.st.removalDelay&&!b.isLowIE&&b.supportsTransition?(b._addClassToMFP(r),setTimeout(function(){b._close()},b.st.removalDelay)):b._close())},_close:function(){y(h);var c=r+" "+q+" ";if(b.bgOverlay.detach(),b.wrap.detach(),b.container.empty(),b.st.mainClass&&(c+=b.st.mainClass+" "),b._removeClassFromMFP(c),b.fixedContentPos){var e={marginRight:""};b.isIE7?a("body, html").css("overflow",""):e.overflow="",a("html").css(e)}d.off("keyup"+p+" focusin"+p),b.ev.off(p),b.wrap.attr("class","mfp-wrap").removeAttr("style"),b.bgOverlay.attr("class","mfp-bg"),b.container.attr("class","mfp-container"),!b.st.showCloseBtn||b.st.closeBtnInside&&b.currTemplate[b.currItem.type]!==!0||b.currTemplate.closeBtn&&b.currTemplate.closeBtn.detach(),b._lastFocusedEl&&a(b._lastFocusedEl).focus(),b.currItem=null,b.content=null,b.currTemplate=null,b.prevHeight=0,y(j)},updateSize:function(a){if(b.isIOS){var c=document.documentElement.clientWidth/window.innerWidth,d=window.innerHeight*c;b.wrap.css("height",d),b.wH=d}else b.wH=a||v.height();b.fixedContentPos||b.wrap.css("height",b.wH),y("Resize")},updateItemHTML:function(){var c=b.items[b.index];b.contentContainer.detach(),b.content&&b.content.detach(),c.parsed||(c=b.parseEl(b.index));var d=c.type;if(y("BeforeChange",[b.currItem?b.currItem.type:"",d]),b.currItem=c,!b.currTemplate[d]){var f=b.st[d]?b.st[d].markup:!1;y("FirstMarkupParse",f),b.currTemplate[d]=f?a(f):!0}e&&e!==c.type&&b.container.removeClass("mfp-"+e+"-holder");var g=b["get"+d.charAt(0).toUpperCase()+d.slice(1)](c,b.currTemplate[d]);b.appendContent(g,d),c.preloaded=!0,y(n,c),e=c.type,b.container.prepend(b.contentContainer),y("AfterChange")},appendContent:function(a,c){b.content=a,a?b.st.showCloseBtn&&b.st.closeBtnInside&&b.currTemplate[c]===!0?b.content.find(".mfp-close").length||b.content.append(z()):b.content=a:b.content="",y(k),b.container.addClass("mfp-"+c+"-holder"),b.contentContainer.append(b.content)},parseEl:function(c){var d,e=b.items[c];if(e.tagName?e={el:a(e)}:(d=e.type,e={data:e,src:e.src}),e.el){for(var f=b.types,g=0;g<f.length;g++)if(e.el.hasClass("mfp-"+f[g])){d=f[g];break}e.src=e.el.attr("data-mfp-src"),e.src||(e.src=e.el.attr("href"))}return e.type=d||b.st.type||"inline",e.index=c,e.parsed=!0,b.items[c]=e,y("ElementParse",e),b.items[c]},addGroup:function(a,c){var d=function(d){d.mfpEl=this,b._openClick(d,a,c)};c||(c={});var e="click.magnificPopup";c.mainEl=a,c.items?(c.isObj=!0,a.off(e).on(e,d)):(c.isObj=!1,c.delegate?a.off(e).on(e,c.delegate,d):(c.items=a,a.off(e).on(e,d)))},_openClick:function(c,d,e){var f=void 0!==e.midClick?e.midClick:a.magnificPopup.defaults.midClick;if(f||2!==c.which&&!c.ctrlKey&&!c.metaKey){var g=void 0!==e.disableOn?e.disableOn:a.magnificPopup.defaults.disableOn;if(g)if(a.isFunction(g)){if(!g.call(b))return!0}else if(v.width()<g)return!0;c.type&&(c.preventDefault(),b.isOpen&&c.stopPropagation()),e.el=a(c.mfpEl),e.delegate&&(e.items=d.find(e.delegate)),b.open(e)}},updateStatus:function(a,d){if(b.preloader){c!==a&&b.container.removeClass("mfp-s-"+c),d||"loading"!==a||(d=b.st.tLoading);var e={status:a,text:d};y("UpdateStatus",e),a=e.status,d=e.text,b.preloader.html(d),b.preloader.find("a").on("click",function(a){a.stopImmediatePropagation()}),b.container.addClass("mfp-s-"+a),c=a}},_checkIfClose:function(c){if(!a(c).hasClass(s)){var d=b.st.closeOnContentClick,e=b.st.closeOnBgClick;if(d&&e)return!0;if(!b.content||a(c).hasClass("mfp-close")||b.preloader&&c===b.preloader[0])return!0;if(c===b.content[0]||a.contains(b.content[0],c)){if(d)return!0}else if(e&&a.contains(document,c))return!0;return!1}},_addClassToMFP:function(a){b.bgOverlay.addClass(a),b.wrap.addClass(a)},_removeClassFromMFP:function(a){this.bgOverlay.removeClass(a),b.wrap.removeClass(a)},_hasScrollBar:function(a){return(b.isIE7?d.height():document.body.scrollHeight)>(a||v.height())},_setFocus:function(){(b.st.focus?b.content.find(b.st.focus).eq(0):b.wrap).focus()},_onFocusIn:function(c){return c.target===b.wrap[0]||a.contains(b.wrap[0],c.target)?void 0:(b._setFocus(),!1)},_parseMarkup:function(b,c,d){var e;d.data&&(c=a.extend(d.data,c)),y(l,[b,c,d]),a.each(c,function(a,c){if(void 0===c||c===!1)return!0;if(e=a.split("_"),e.length>1){var d=b.find(p+"-"+e[0]);if(d.length>0){var f=e[1];"replaceWith"===f?d[0]!==c[0]&&d.replaceWith(c):"img"===f?d.is("img")?d.attr("src",c):d.replaceWith('<img src="'+c+'" class="'+d.attr("class")+'" />'):d.attr(e[1],c)}}else b.find(p+"-"+a).html(c)})},_getScrollbarSize:function(){if(void 0===b.scrollbarSize){var a=document.createElement("div");a.style.cssText="width: 99px; height: 99px; overflow: scroll; position: absolute; top: -9999px;",document.body.appendChild(a),b.scrollbarSize=a.offsetWidth-a.clientWidth,document.body.removeChild(a)}return b.scrollbarSize}},a.magnificPopup={instance:null,proto:t.prototype,modules:[],open:function(b,c){return A(),b=b?a.extend(!0,{},b):{},b.isObj=!0,b.index=c||0,this.instance.open(b)},close:function(){return a.magnificPopup.instance&&a.magnificPopup.instance.close()},registerModule:function(b,c){c.options&&(a.magnificPopup.defaults[b]=c.options),a.extend(this.proto,c.proto),this.modules.push(b)},defaults:{disableOn:0,key:null,midClick:!1,mainClass:"",preloader:!0,focus:"",closeOnContentClick:!1,closeOnBgClick:!0,closeBtnInside:!0,showCloseBtn:!0,enableEscapeKey:!0,modal:!1,alignTop:!1,removalDelay:0,prependTo:null,fixedContentPos:"auto",fixedBgPos:"auto",overflowY:"auto",closeMarkup:'<button title="%title%" type="button" class="mfp-close">&times;</button>',tClose:"Close (Esc)",tLoading:"Loading..."}},a.fn.magnificPopup=function(c){A();var d=a(this);if("string"==typeof c)if("open"===c){var e,f=u?d.data("magnificPopup"):d[0].magnificPopup,g=parseInt(arguments[1],10)||0;f.items?e=f.items[g]:(e=d,f.delegate&&(e=e.find(f.delegate)),e=e.eq(g)),b._openClick({mfpEl:e},d,f)}else b.isOpen&&b[c].apply(b,Array.prototype.slice.call(arguments,1));else c=a.extend(!0,{},c),u?d.data("magnificPopup",c):d[0].magnificPopup=c,b.addGroup(d,c);return d};var C,D,E,F="inline",G=function(){E&&(D.after(E.addClass(C)).detach(),E=null)};a.magnificPopup.registerModule(F,{options:{hiddenClass:"hide",markup:"",tNotFound:"Content not found"},proto:{initInline:function(){b.types.push(F),w(h+"."+F,function(){G()})},getInline:function(c,d){if(G(),c.src){var e=b.st.inline,f=a(c.src);if(f.length){var g=f[0].parentNode;g&&g.tagName&&(D||(C=e.hiddenClass,D=x(C),C="mfp-"+C),E=f.after(D).detach().removeClass(C)),b.updateStatus("ready")}else b.updateStatus("error",e.tNotFound),f=a("<div>");return c.inlineElement=f,f}return b.updateStatus("ready"),b._parseMarkup(d,{},c),d}}});var H,I="ajax",J=function(){H&&a(document.body).removeClass(H)},K=function(){J(),b.req&&b.req.abort()};a.magnificPopup.registerModule(I,{options:{settings:null,cursor:"mfp-ajax-cur",tError:'<a href="%url%">The content</a> could not be loaded.'},proto:{initAjax:function(){b.types.push(I),H=b.st.ajax.cursor,w(h+"."+I,K),w("BeforeChange."+I,K)},getAjax:function(c){H&&a(document.body).addClass(H),b.updateStatus("loading");var d=a.extend({url:c.src,success:function(d,e,f){var g={data:d,xhr:f};y("ParseAjax",g),b.appendContent(a(g.data),I),c.finished=!0,J(),b._setFocus(),setTimeout(function(){b.wrap.addClass(q)},16),b.updateStatus("ready"),y("AjaxContentAdded")},error:function(){J(),c.finished=c.loadError=!0,b.updateStatus("error",b.st.ajax.tError.replace("%url%",c.src))}},b.st.ajax.settings);return b.req=a.ajax(d),""}}});var L,M=function(c){if(c.data&&void 0!==c.data.title)return c.data.title;var d=b.st.image.titleSrc;if(d){if(a.isFunction(d))return d.call(b,c);if(c.el)return c.el.attr(d)||""}return""};a.magnificPopup.registerModule("image",{options:{markup:'<div class="mfp-figure"><div class="mfp-close"></div><figure><div class="mfp-img"></div><figcaption><div class="mfp-bottom-bar"><div class="mfp-title"></div><div class="mfp-counter"></div></div></figcaption></figure></div>',cursor:"mfp-zoom-out-cur",titleSrc:"title",verticalFit:!0,tError:'<a href="%url%">The image</a> could not be loaded.'},proto:{initImage:function(){var c=b.st.image,d=".image";b.types.push("image"),w(m+d,function(){"image"===b.currItem.type&&c.cursor&&a(document.body).addClass(c.cursor)}),w(h+d,function(){c.cursor&&a(document.body).removeClass(c.cursor),v.off("resize"+p)}),w("Resize"+d,b.resizeImage),b.isLowIE&&w("AfterChange",b.resizeImage)},resizeImage:function(){var a=b.currItem;if(a&&a.img&&b.st.image.verticalFit){var c=0;b.isLowIE&&(c=parseInt(a.img.css("padding-top"),10)+parseInt(a.img.css("padding-bottom"),10)),a.img.css("max-height",b.wH-c)}},_onImageHasSize:function(a){a.img&&(a.hasSize=!0,L&&clearInterval(L),a.isCheckingImgSize=!1,y("ImageHasSize",a),a.imgHidden&&(b.content&&b.content.removeClass("mfp-loading"),a.imgHidden=!1))},findImageSize:function(a){var c=0,d=a.img[0],e=function(f){L&&clearInterval(L),L=setInterval(function(){return d.naturalWidth>0?void b._onImageHasSize(a):(c>200&&clearInterval(L),c++,void(3===c?e(10):40===c?e(50):100===c&&e(500)))},f)};e(1)},getImage:function(c,d){var e=0,f=function(){c&&(c.img[0].complete?(c.img.off(".mfploader"),c===b.currItem&&(b._onImageHasSize(c),b.updateStatus("ready")),c.hasSize=!0,c.loaded=!0,y("ImageLoadComplete")):(e++,200>e?setTimeout(f,100):g()))},g=function(){c&&(c.img.off(".mfploader"),c===b.currItem&&(b._onImageHasSize(c),b.updateStatus("error",h.tError.replace("%url%",c.src))),c.hasSize=!0,c.loaded=!0,c.loadError=!0)},h=b.st.image,i=d.find(".mfp-img");if(i.length){var j=document.createElement("img");j.className="mfp-img",c.el&&c.el.find("img").length&&(j.alt=c.el.find("img").attr("alt")),c.img=a(j).on("load.mfploader",f).on("error.mfploader",g),j.src=c.src,i.is("img")&&(c.img=c.img.clone()),j=c.img[0],j.naturalWidth>0?c.hasSize=!0:j.width||(c.hasSize=!1)}return b._parseMarkup(d,{title:M(c),img_replaceWith:c.img},c),b.resizeImage(),c.hasSize?(L&&clearInterval(L),c.loadError?(d.addClass("mfp-loading"),b.updateStatus("error",h.tError.replace("%url%",c.src))):(d.removeClass("mfp-loading"),b.updateStatus("ready")),d):(b.updateStatus("loading"),c.loading=!0,c.hasSize||(c.imgHidden=!0,d.addClass("mfp-loading"),b.findImageSize(c)),d)}}});var N,O=function(){return void 0===N&&(N=void 0!==document.createElement("p").style.MozTransform),N};a.magnificPopup.registerModule("zoom",{options:{enabled:!1,easing:"ease-in-out",duration:300,opener:function(a){return a.is("img")?a:a.find("img")}},proto:{initZoom:function(){var a,c=b.st.zoom,d=".zoom";if(c.enabled&&b.supportsTransition){var e,f,g=c.duration,j=function(a){var b=a.clone().removeAttr("style").removeAttr("class").addClass("mfp-animated-image"),d="all "+c.duration/1e3+"s "+c.easing,e={position:"fixed",zIndex:9999,left:0,top:0,"-webkit-backface-visibility":"hidden"},f="transition";return e["-webkit-"+f]=e["-moz-"+f]=e["-o-"+f]=e[f]=d,b.css(e),b},k=function(){b.content.css("visibility","visible")};w("BuildControls"+d,function(){if(b._allowZoom()){if(clearTimeout(e),b.content.css("visibility","hidden"),a=b._getItemToZoom(),!a)return void k();f=j(a),f.css(b._getOffset()),b.wrap.append(f),e=setTimeout(function(){f.css(b._getOffset(!0)),e=setTimeout(function(){k(),setTimeout(function(){f.remove(),a=f=null,y("ZoomAnimationEnded")},16)},g)},16)}}),w(i+d,function(){if(b._allowZoom()){if(clearTimeout(e),b.st.removalDelay=g,!a){if(a=b._getItemToZoom(),!a)return;f=j(a)}f.css(b._getOffset(!0)),b.wrap.append(f),b.content.css("visibility","hidden"),setTimeout(function(){f.css(b._getOffset())},16)}}),w(h+d,function(){b._allowZoom()&&(k(),f&&f.remove(),a=null)})}},_allowZoom:function(){return"image"===b.currItem.type},_getItemToZoom:function(){return b.currItem.hasSize?b.currItem.img:!1},_getOffset:function(c){var d;d=c?b.currItem.img:b.st.zoom.opener(b.currItem.el||b.currItem);var e=d.offset(),f=parseInt(d.css("padding-top"),10),g=parseInt(d.css("padding-bottom"),10);e.top-=a(window).scrollTop()-f;var h={width:d.width(),height:(u?d.innerHeight():d[0].offsetHeight)-g-f};return O()?h["-moz-transform"]=h.transform="translate("+e.left+"px,"+e.top+"px)":(h.left=e.left,h.top=e.top),h}}});var P="iframe",Q="//about:blank",R=function(a){if(b.currTemplate[P]){var c=b.currTemplate[P].find("iframe");c.length&&(a||(c[0].src=Q),b.isIE8&&c.css("display",a?"block":"none"))}};a.magnificPopup.registerModule(P,{options:{markup:'<div class="mfp-iframe-scaler"><div class="mfp-close"></div><iframe class="mfp-iframe" src="//about:blank" frameborder="0" allowfullscreen></iframe></div>',srcAction:"iframe_src",patterns:{youtube:{index:"youtube.com",id:"v=",src:"//www.youtube.com/embed/%id%?autoplay=1"},vimeo:{index:"vimeo.com/",id:"/",src:"//player.vimeo.com/video/%id%?autoplay=1"},gmaps:{index:"//maps.google.",src:"%id%&output=embed"}}},proto:{initIframe:function(){b.types.push(P),w("BeforeChange",function(a,b,c){b!==c&&(b===P?R():c===P&&R(!0))}),w(h+"."+P,function(){R()})},getIframe:function(c,d){var e=c.src,f=b.st.iframe;a.each(f.patterns,function(){return e.indexOf(this.index)>-1?(this.id&&(e="string"==typeof this.id?e.substr(e.lastIndexOf(this.id)+this.id.length,e.length):this.id.call(this,e)),e=this.src.replace("%id%",e),!1):void 0});var g={};return f.srcAction&&(g[f.srcAction]=e),b._parseMarkup(d,g,c),b.updateStatus("ready"),d}}});var S=function(a){var c=b.items.length;return a>c-1?a-c:0>a?c+a:a},T=function(a,b,c){return a.replace(/%curr%/gi,b+1).replace(/%total%/gi,c)};a.magnificPopup.registerModule("gallery",{options:{enabled:!1,arrowMarkup:'<button title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir%"></button>',preload:[0,2],navigateByImgClick:!0,arrows:!0,tPrev:"Previous (Left arrow key)",tNext:"Next (Right arrow key)",tCounter:"%curr% of %total%"},proto:{initGallery:function(){var c=b.st.gallery,e=".mfp-gallery",g=Boolean(a.fn.mfpFastClick);return b.direction=!0,c&&c.enabled?(f+=" mfp-gallery",w(m+e,function(){c.navigateByImgClick&&b.wrap.on("click"+e,".mfp-img",function(){return b.items.length>1?(b.next(),!1):void 0}),d.on("keydown"+e,function(a){37===a.keyCode?b.prev():39===a.keyCode&&b.next()})}),w("UpdateStatus"+e,function(a,c){c.text&&(c.text=T(c.text,b.currItem.index,b.items.length))}),w(l+e,function(a,d,e,f){var g=b.items.length;e.counter=g>1?T(c.tCounter,f.index,g):""}),w("BuildControls"+e,function(){if(b.items.length>1&&c.arrows&&!b.arrowLeft){var d=c.arrowMarkup,e=b.arrowLeft=a(d.replace(/%title%/gi,c.tPrev).replace(/%dir%/gi,"left")).addClass(s),f=b.arrowRight=a(d.replace(/%title%/gi,c.tNext).replace(/%dir%/gi,"right")).addClass(s),h=g?"mfpFastClick":"click";e[h](function(){b.prev()}),f[h](function(){b.next()}),b.isIE7&&(x("b",e[0],!1,!0),x("a",e[0],!1,!0),x("b",f[0],!1,!0),x("a",f[0],!1,!0)),b.container.append(e.add(f))}}),w(n+e,function(){b._preloadTimeout&&clearTimeout(b._preloadTimeout),b._preloadTimeout=setTimeout(function(){b.preloadNearbyImages(),b._preloadTimeout=null},16)}),void w(h+e,function(){d.off(e),b.wrap.off("click"+e),b.arrowLeft&&g&&b.arrowLeft.add(b.arrowRight).destroyMfpFastClick(),b.arrowRight=b.arrowLeft=null})):!1},next:function(){b.direction=!0,b.index=S(b.index+1),b.updateItemHTML()},prev:function(){b.direction=!1,b.index=S(b.index-1),b.updateItemHTML()},goTo:function(a){b.direction=a>=b.index,b.index=a,b.updateItemHTML()},preloadNearbyImages:function(){var a,c=b.st.gallery.preload,d=Math.min(c[0],b.items.length),e=Math.min(c[1],b.items.length);for(a=1;a<=(b.direction?e:d);a++)b._preloadItem(b.index+a);for(a=1;a<=(b.direction?d:e);a++)b._preloadItem(b.index-a)},_preloadItem:function(c){if(c=S(c),!b.items[c].preloaded){var d=b.items[c];d.parsed||(d=b.parseEl(c)),y("LazyLoad",d),"image"===d.type&&(d.img=a('<img class="mfp-img" />').on("load.mfploader",function(){d.hasSize=!0}).on("error.mfploader",function(){d.hasSize=!0,d.loadError=!0,y("LazyLoadError",d)}).attr("src",d.src)),d.preloaded=!0}}}});var U="retina";a.magnificPopup.registerModule(U,{options:{replaceSrc:function(a){return a.src.replace(/\.\w+$/,function(a){return"@2x"+a})},ratio:1},proto:{initRetina:function(){if(window.devicePixelRatio>1){var a=b.st.retina,c=a.ratio;c=isNaN(c)?c():c,c>1&&(w("ImageHasSize."+U,function(a,b){b.img.css({"max-width":b.img[0].naturalWidth/c,width:"100%"})}),w("ElementParse."+U,function(b,d){d.src=a.replaceSrc(d,c)}))}}}}),function(){var b=1e3,c="ontouchstart"in window,d=function(){v.off("touchmove"+f+" touchend"+f)},e="mfpFastClick",f="."+e;a.fn.mfpFastClick=function(e){return a(this).each(function(){var g,h=a(this);if(c){var i,j,k,l,m,n;h.on("touchstart"+f,function(a){l=!1,n=1,m=a.originalEvent?a.originalEvent.touches[0]:a.touches[0],j=m.clientX,k=m.clientY,v.on("touchmove"+f,function(a){m=a.originalEvent?a.originalEvent.touches:a.touches,n=m.length,m=m[0],(Math.abs(m.clientX-j)>10||Math.abs(m.clientY-k)>10)&&(l=!0,d())}).on("touchend"+f,function(a){d(),l||n>1||(g=!0,a.preventDefault(),clearTimeout(i),i=setTimeout(function(){g=!1},b),e())})})}h.on("click"+f,function(){g||e()})})},a.fn.destroyMfpFastClick=function(){a(this).off("touchstart"+f+" click"+f),c&&v.off("touchmove"+f+" touchend"+f)}}(),A()});/*
    Thumbelina Content Slider
    V1.0 Rev 1302190900

    A lightweight horizontal and vertical content slider designed for image thumbnails.
    http://www.starplugins.com/thumbelina

    Developed by Star Plugins
    http://www.starplugins.com

    Copyright 2013, Star Plugins, www.starplugins.com
    License: GNU General Public License, version 3 (GPL-3.0)
    http://www.opensource.org/licenses/gpl-3.0.html
*/
;(function($) {
    $.fn.Thumbelina = function(settings) {
        var $container = this,      // Handy reference to container.
            $list = $('ul',this),   // Handy reference to the list element.
            moveDir = 0,            // Current direction of movement.
            pos = 0,                // Current actual position.
            destPos = 0,            // Current destination position.
            listDimension = 0,      // Size (width or height depending on orientation) of list element.
            idle = 0,
            outerFunc,
            orientData;              // Stores function calls and CSS attribute for horiz or vert mode.

        // Add thumblina CSS class, and create an inner wrapping container, within which the list will slide with overflow hidden.
        $list.addClass('thumbelina').wrap('<div style="position:absolute;overflow:hidden;width:100%;height:100%;">');
        // Create settings by merging user settings into defaults.
        settings = $.extend({}, $.fn.Thumbelina.defaults, settings);

        // Depending on vertical or horizontal, get functions to call and CSS attribute to change.
        if(settings.orientation === 'vertical')
            orientData = {outerSizeFunc:  'outerHeight', cssAttr: 'top', display: 'block'};
        else
            orientData = {outerSizeFunc:  'outerWidth', cssAttr: 'left', display: 'inline-block'};

        // Apply display type of list items.
        $('li',$list).css({display: orientData.display});

        // Function to bind events to buttons.
        var bindButEvents = function($elem,dir) {
            $elem.bind('mousedown mouseup touchend touchstart',function(evt) {
                if (evt.type==='mouseup' || evt.type==='touchend') moveDir = 0;
                else moveDir = dir;
                return false;
            });
        };

        // Bind the events.
        bindButEvents(settings.$bwdBut,1);
        bindButEvents(settings.$fwdBut,-1);

        // Store ref to outerWidth() or outerHeight() function.
        outerFunc = orientData.outerSizeFunc;

        // Function to animate. Moves the list element inside the container.
        // Does various bounds checks.
        var animate = function() {
            var minPos;

            // If no movement or resize for 100 cycles, then go into 'idle' mode to save CPU.
            if (!moveDir && pos === destPos && listDimension === $container[outerFunc]() ) {
                idle++;
                if (idle>100) return;
            }else {
                // Make a note of current size for idle comparison next cycle.
                listDimension = $container[outerFunc]();
                idle = 0;
            }

            // Update destination pos.
            destPos += settings.maxSpeed * moveDir;

            // Work out minimum scroll position.
            // This will also cause the thumbs to drag back out again when increasing container size.
            minPos = listDimension - $list[outerFunc]();


            // Minimum pos should always be <= 0;
            if (minPos > 0) minPos = 0;
            // Bounds check (maximum advance i.e list moving left/up)
            if (destPos < minPos) destPos = minPos;
            // Bounds check (maximum retreat i.e list moving right/down)
            if (destPos>0) destPos = 0;

            // Disable/enable buttons depending min/max extents.
            if (destPos === minPos) settings.$fwdBut.addClass('disabled');
            else settings.$fwdBut.removeClass('disabled');
            if (destPos === 0) settings.$bwdBut.addClass('disabled');
            else settings.$bwdBut.removeClass('disabled');

            // Animate towards destination with a simple easing calculation.
            pos += (destPos - pos) / settings.easing;

            // If within 1000th of a pixel to dest, then just 'snap' to exact value.
            // Do this so pos will end up exactly == destPos (deals with rounding errors).
            if (Math.abs(destPos-pos)<0.001) pos = destPos;

            $list.css(orientData.cssAttr, Math.floor(pos));
        };

        setInterval(function(){
            animate();
        },1000/60);
    };

    $.fn.Thumbelina.defaults = {
        orientation:    "horizontal",   // Orientation mode, horizontal or vertical.
        easing:         8,              // Amount of easing (min 1) larger = more drift.
        maxSpeed:       5,              // Max speed of movement (pixels per cycle).
        $bwdBut:   null,                // jQuery element used as backward button.
        $fwdBut:    null                // jQuery element used as forward button.
    };

})(jQuery);

/*
 * jQuery mmenu v5.0.4
 * @requires jQuery 1.7.0 or later
 *
 * mmenu.frebsite.nl
 *
 * Copyright (c) Fred Heusschen
 * www.frebsite.nl
 *
 * Licensed under the MIT license:
 * http://en.wikipedia.org/wiki/MIT_License
 */
!function(t){function n(){t[s].glbl||(d={$wndw:t(window),$html:t("html"),$body:t("body")},a={},l={},r={},t.each([a,l,r],function(e,t){t.add=function(e){e=e.split(" ");for(var n in e)t[e[n]]=t.mm(e[n])}}),a.mm=function(e){return"mm-"+e},a.add("wrapper menu vertical panel nopanel current highest opened subopened header hasheader title btn prev next first last listview nolistview selected divider spacer hidden fullsubopen"),a.umm=function(e){return"mm-"==e.slice(0,3)&&(e=e.slice(3)),e},l.mm=function(e){return"mm-"+e},l.add("parent sub"),r.mm=function(e){return e+".mm"},r.add("transitionend webkitTransitionEnd mousedown mouseup touchstart touchmove touchend click keydown"),t[s]._c=a,t[s]._d=l,t[s]._e=r,t[s].glbl=d)}var s="mmenu",i="5.0.4";if(!t[s]){t[s]=function(e,t,n){this.$menu=e,this._api=["bind","init","update","setSelected","getInstance","openPanel","closePanel","closeAllPanels"],this.opts=t,this.conf=n,this.vars={},this.cbck={},"function"==typeof this.___deprecated&&this.___deprecated(),this._initMenu(),this._initAnchors();var s=this.$menu.children(this.conf.panelNodetype);return this._initAddons(),this.init(s),"function"==typeof this.___debug&&this.___debug(),this},t[s].version=i,t[s].addons={},t[s].uniqueId=0,t[s].defaults={extensions:[],onClick:{setSelected:!0},slidingSubmenus:!0},t[s].configuration={classNames:{panel:"Panel",vertical:"Vertical",selected:"Selected",divider:"Divider",spacer:"Spacer"},clone:!1,openingInterval:25,panelNodetype:"ul, ol, div",transitionDuration:400},t[s].prototype={init:function(e){e=e.not("."+a.nopanel),e=this._initPanels(e),this.trigger("init",e),this.trigger("update")},update:function(){this.trigger("update")},setSelected:function(e){this.$menu.find("."+a.listview).children().removeClass(a.selected),e.addClass(a.selected),this.trigger("setSelected",e)},openPanel:function(e){var n=e.parent();if(n.hasClass(a.vertical)){var s=n.parents("."+a.subopened);if(s.length)return this.openPanel(s.first());n.addClass(a.opened)}else{if(e.hasClass(a.current))return;var i=t(this.$menu).children("."+a.panel),l=i.filter("."+a.current);i.removeClass(a.highest).removeClass(a.current).not(e).not(l).not("."+a.vertical).addClass(a.hidden),e.hasClass(a.opened)?l.addClass(a.highest).removeClass(a.opened).removeClass(a.subopened):(e.addClass(a.highest),l.addClass(a.subopened)),e.removeClass(a.hidden).addClass(a.current),setTimeout(function(){e.removeClass(a.subopened).addClass(a.opened)},this.conf.openingInterval)}this.trigger("openPanel",e)},closePanel:function(e){var t=e.parent();t.hasClass(a.vertical)&&(t.removeClass(a.opened),this.trigger("closePanel",e))},closeAllPanels:function(){this.$menu.find("."+a.listview).children().removeClass(a.selected).filter("."+a.vertical).removeClass(a.opened);var e=this.$menu.children("."+a.panel),t=e.first();this.$menu.children("."+a.panel).not(t).removeClass(a.subopened).removeClass(a.opened).removeClass(a.current).removeClass(a.highest).addClass(a.hidden),this.openPanel(t)},togglePanel:function(e){var t=e.parent();t.hasClass(a.vertical)&&this[t.hasClass(a.opened)?"closePanel":"openPanel"](e)},getInstance:function(){return this},bind:function(e,t){this.cbck[e]=this.cbck[e]||[],this.cbck[e].push(t)},trigger:function(){var t=this,n=Array.prototype.slice.call(arguments),s=n.shift();if(this.cbck[s])for(e in this.cbck[s])this.cbck[s][e].apply(t,n)},_initMenu:function(){this.opts.offCanvas&&this.conf.clone&&(this.$menu=this.$menu.clone(!0),this.$menu.add(this.$menu.find("*")).filter("[id]").each(function(){t(this).attr("id",a.mm(t(this).attr("id")))})),this.$menu.contents().each(function(){3==t(this)[0].nodeType&&t(this).remove()}),this.$menu.parent().addClass(a.wrapper);var e=[a.menu];this.opts.slidingSubmenus||e.push(a.vertical),this.opts.extensions=this.opts.extensions.length?"mm-"+this.opts.extensions.join(" mm-"):"",this.opts.extensions&&e.push(this.opts.extensions),this.$menu.addClass(e.join(" "))},_initPanels:function(e){var n=this;this.__findAddBack(e,"ul, ol").not("."+a.nolistview).addClass(a.listview);var s=this.__findAddBack(e,"."+a.listview).children();this.__refactorClass(s,this.conf.classNames.selected,"selected"),this.__refactorClass(s,this.conf.classNames.divider,"divider"),this.__refactorClass(s,this.conf.classNames.spacer,"spacer"),this.__refactorClass(this.__findAddBack(e,"."+this.conf.classNames.panel),this.conf.classNames.panel,"panel");var i=t(),r=e.add(this.__findAddBack(e,"."+a.listview).children().children(this.conf.panelNodetype)).not("."+a.nopanel);this.__refactorClass(r,this.conf.classNames.vertical,"vertical"),this.opts.slidingSubmenus||r.addClass(a.vertical),r.each(function(){var e=t(this),s=e;e.is("ul, ol")?(e.wrap('<div class="'+a.panel+'" />'),s=e.parent()):s.addClass(a.panel);var l=e.attr("id");e.removeAttr("id"),s.attr("id",l||n.__getUniqueId()),e.hasClass(a.vertical)&&(e.removeClass(n.conf.classNames.vertical),s.add(s.parent()).addClass(a.vertical)),i=i.add(s);var r=s.children().first(),d=s.children().last();r.is("."+a.listview)&&r.addClass(a.first),d.is("."+a.listview)&&d.addClass(a.last)});var d=t("."+a.panel,this.$menu);i.each(function(){var e=t(this),n=e.parent(),s=n.children("a, span");if(!n.is("."+a.menu)&&!e.data(l.parent)){if(n.data(l.sub,e),e.data(l.parent,n),n.parent().is("."+a.listview)){var i=e.attr("id"),r=t('<a class="'+a.next+'" href="#'+i+'" data-target="#'+i+'" />').insertBefore(s);s.is("a")||r.addClass(a.fullsubopen)}if(!n.hasClass(a.vertical)){var d=n.closest("."+a.panel);if(d.length){var i=d.attr("id");e.prepend('<div class="'+a.header+'"><a class="'+a.btn+" "+a.prev+'" href="#'+i+'" data-target="#'+i+'"></a><a class="'+a.title+'">'+s.text()+"</a></div>"),e.addClass(a.hasheader)}}}});var o=this.__findAddBack(e,"."+a.listview).children("."+a.selected).removeClass(a.selected).last().addClass(a.selected);o.add(o.parentsUntil("."+a.menu,"li")).filter("."+a.vertical).addClass(a.opened).end().not("."+a.vertical).each(function(){t(this).parentsUntil("."+a.menu,"."+a.panel).not("."+a.vertical).first().addClass(a.opened).parentsUntil("."+a.menu,"."+a.panel).not("."+a.vertical).first().addClass(a.opened).addClass(a.subopened)}),o.children("."+a.panel).not("."+a.vertical).addClass(a.opened).parentsUntil("."+a.menu,"."+a.panel).not("."+a.vertical).first().addClass(a.opened).addClass(a.subopened);var c=d.filter("."+a.opened);return c.length||(c=i.first()),c.addClass(a.opened).last().addClass(a.current),i.not("."+a.vertical).not(c.last()).addClass(a.hidden).end().appendTo(this.$menu),i},_initAnchors:function(){var e=this;d.$body.on(r.click+"-oncanvas","a[href]",function(n){var i=t(this),l=!1,r=e.$menu.find(i).length;for(var o in t[s].addons)if(l=t[s].addons[o].clickAnchor.call(e,i,r))break;if(!l&&r){var c=i.attr("href");if(c.length>1&&"#"==c.slice(0,1)){var h=t(c,e.$menu);h.is("."+a.panel)&&(l=!0,e[i.parent().hasClass(a.vertical)?"togglePanel":"openPanel"](h))}}if(l&&n.preventDefault(),!l&&r&&i.is("."+a.listview+" > li > a")&&!i.is('[rel="external"]')&&!i.is('[target="_blank"]')){e.__valueOrFn(e.opts.onClick.setSelected,i)&&e.setSelected(t(n.target).parent());var u=e.__valueOrFn(e.opts.onClick.preventDefault,i,"#"==c.slice(0,1));u&&n.preventDefault(),e.__valueOrFn(e.opts.onClick.blockUI,i,!u)&&d.$html.addClass(a.blocking),e.__valueOrFn(e.opts.onClick.close,i,u)&&e.close()}})},_initAddons:function(){for(var e in t[s].addons)t[s].addons[e].add.call(this),t[s].addons[e].add=function(){};for(var e in t[s].addons)t[s].addons[e].setup.call(this)},__api:function(){var e=this,n={};return t.each(this._api,function(){var t=this;n[t]=function(){var s=e[t].apply(e,arguments);return"undefined"==typeof s?n:s}}),n},__valueOrFn:function(e,t,n){return"function"==typeof e?e.call(t[0]):"undefined"==typeof e&&"undefined"!=typeof n?n:e},__refactorClass:function(e,t,n){return e.filter("."+t).removeClass(t).addClass(a[n])},__findAddBack:function(e,t){return e.find(t).add(e.filter(t))},__filterListItems:function(e){return e.not("."+a.divider).not("."+a.hidden)},__transitionend:function(e,t,n){var s=!1,i=function(){s||t.call(e[0]),s=!0};e.one(r.transitionend,i),e.one(r.webkitTransitionEnd,i),setTimeout(i,1.1*n)},__getUniqueId:function(){return a.mm(t[s].uniqueId++)}},t.fn[s]=function(e,i){return n(),e=t.extend(!0,{},t[s].defaults,e),i=t.extend(!0,{},t[s].configuration,i),this.each(function(){var n=t(this);if(!n.data(s)){var a=new t[s](n,e,i);n.data(s,a.__api())}})},t[s].support={touch:"ontouchstart"in window||navigator.msMaxTouchPoints};var a,l,r,d}}(jQuery);
/*
 * jQuery mmenu offCanvas addon
 * mmenu.frebsite.nl
 *
 * Copyright (c) Fred Heusschen
 */
!function(e){var t="mmenu",o="offCanvas";e[t].addons[o]={setup:function(){if(this.opts[o]){var n=this.opts[o],i=this.conf[o];a=e[t].glbl,this._api=e.merge(this._api,["open","close","setPage"]),("top"==n.position||"bottom"==n.position)&&(n.zposition="front"),"string"!=typeof i.pageSelector&&(i.pageSelector="> "+i.pageNodetype),a.$allMenus=(a.$allMenus||e()).add(this.$menu),this.vars.opened=!1;var r=[s.offcanvas];"left"!=n.position&&r.push(s.mm(n.position)),"back"!=n.zposition&&r.push(s.mm(n.zposition)),this.$menu.addClass(r.join(" ")).parent().removeClass(s.wrapper),this.setPage(a.$page),this._initBlocker(),this["_initWindow_"+o](),this.$menu[i.menuInjectMethod+"To"](i.menuWrapperSelector)}},add:function(){s=e[t]._c,n=e[t]._d,i=e[t]._e,s.add("offcanvas slideout modal background opening blocker page"),n.add("style"),i.add("resize")},clickAnchor:function(e){if(!this.opts[o])return!1;var t=this.$menu.attr("id");if(t&&t.length&&(this.conf.clone&&(t=s.umm(t)),e.is('[href="#'+t+'"]')))return this.open(),!0;if(a.$page){var t=a.$page.attr("id");return t&&t.length&&e.is('[href="#'+t+'"]')?(this.close(),!0):!1}}},e[t].defaults[o]={position:"left",zposition:"back",modal:!1,moveBackground:!0},e[t].configuration[o]={pageNodetype:"div",pageSelector:null,menuWrapperSelector:"body",menuInjectMethod:"prepend"},e[t].prototype.open=function(){if(!this.vars.opened){var e=this;this._openSetup(),setTimeout(function(){e._openFinish()},this.conf.openingInterval),this.trigger("open")}},e[t].prototype._openSetup=function(){var e=this;this.closeAllOthers(),a.$page.data(n.style,a.$page.attr("style")||""),a.$wndw.trigger(i.resize+"-offcanvas",[!0]);var t=[s.opened];this.opts[o].modal&&t.push(s.modal),this.opts[o].moveBackground&&t.push(s.background),"left"!=this.opts[o].position&&t.push(s.mm(this.opts[o].position)),"back"!=this.opts[o].zposition&&t.push(s.mm(this.opts[o].zposition)),this.opts.extensions&&t.push(this.opts.extensions),a.$html.addClass(t.join(" ")),setTimeout(function(){e.vars.opened=!0},this.conf.openingInterval),this.$menu.addClass(s.current+" "+s.opened)},e[t].prototype._openFinish=function(){var e=this;this.__transitionend(a.$page,function(){e.trigger("opened")},this.conf.transitionDuration),a.$html.addClass(s.opening),this.trigger("opening")},e[t].prototype.close=function(){if(this.vars.opened){var e=this;this.__transitionend(a.$page,function(){e.$menu.removeClass(s.current).removeClass(s.opened),a.$html.removeClass(s.opened).removeClass(s.modal).removeClass(s.background).removeClass(s.mm(e.opts[o].position)).removeClass(s.mm(e.opts[o].zposition)),e.opts.extensions&&a.$html.removeClass(e.opts.extensions),a.$page.attr("style",a.$page.data(n.style)),e.vars.opened=!1,e.trigger("closed")},this.conf.transitionDuration),a.$html.removeClass(s.opening),this.trigger("close"),this.trigger("closing")}},e[t].prototype.closeAllOthers=function(){a.$allMenus.not(this.$menu).each(function(){var o=e(this).data(t);o&&o.close&&o.close()})},e[t].prototype.setPage=function(t){t&&t.length||(t=e(this.conf[o].pageSelector,a.$body),t.length>1&&(t=t.wrapAll("<"+this.conf[o].pageNodetype+" />").parent())),t.attr("id",t.attr("id")||this.__getUniqueId()),t.addClass(s.page+" "+s.slideout),a.$page=t,this.trigger("setPage",t)},e[t].prototype["_initWindow_"+o]=function(){a.$wndw.off(i.keydown+"-offcanvas").on(i.keydown+"-offcanvas",function(e){return a.$html.hasClass(s.opened)&&9==e.keyCode?(e.preventDefault(),!1):void 0});var e=0;a.$wndw.off(i.resize+"-offcanvas").on(i.resize+"-offcanvas",function(t,o){if(o||a.$html.hasClass(s.opened)){var n=a.$wndw.height();(o||n!=e)&&(e=n,a.$page.css("minHeight",n))}})},e[t].prototype._initBlocker=function(){var t=this;a.$blck||(a.$blck=e('<div id="'+s.blocker+'" class="'+s.slideout+'" />')),a.$blck.appendTo(a.$body).off(i.touchstart+"-offcanvas "+i.touchmove+"-offcanvas").on(i.touchstart+"-offcanvas "+i.touchmove+"-offcanvas",function(e){e.preventDefault(),e.stopPropagation(),a.$blck.trigger(i.mousedown+"-offcanvas")}).off(i.mousedown+"-offcanvas").on(i.mousedown+"-offcanvas",function(e){e.preventDefault(),a.$html.hasClass(s.modal)||(t.closeAllOthers(),t.close())})};var s,n,i,a}(jQuery);
/*
 * jQuery mmenu autoHeight addon
 * mmenu.frebsite.nl
 *
 * Copyright (c) Fred Heusschen
 */
!function(t){var e="mmenu",i="autoHeight";t[e].addons[i]={setup:function(){if(this.opts.offCanvas){switch(this.opts.offCanvas.position){case"left":case"right":return}var n=this,o=this.opts[i];if(this.conf[i],h=t[e].glbl,"boolean"==typeof o&&o&&(o={height:"auto"}),"object"!=typeof o&&(o={}),o=this.opts[i]=t.extend(!0,{},t[e].defaults[i],o),"auto"==o.height){this.$menu.addClass(s.autoheight);var u=function(t){var e=this.$menu.children("."+s.current);_top=parseInt(e.css("top"),10)||0,_bot=parseInt(e.css("bottom"),10)||0,this.$menu.addClass(s.measureheight),t=t||this.$menu.children("."+s.current),t.is("."+s.vertical)&&(t=t.parents("."+s.panel).not("."+s.vertical).first()),this.$menu.height(t.outerHeight()+_top+_bot).removeClass(s.measureheight)};this.bind("update",u),this.bind("openPanel",u),this.bind("closePanel",u),this.bind("open",u),h.$wndw.off(a.resize+"-autoheight").on(a.resize+"-autoheight",function(){u.call(n)})}}},add:function(){s=t[e]._c,n=t[e]._d,a=t[e]._e,s.add("autoheight measureheight"),a.add("resize")},clickAnchor:function(){}},t[e].defaults[i]={height:"default"};var s,n,a,h}(jQuery);
/*
 * jQuery mmenu backButton addon
 * mmenu.frebsite.nl
 *
 * Copyright (c) Fred Heusschen
 */
!function(o){var t="mmenu",n="backButton";o[t].addons[n]={setup:function(){if(this.opts.offCanvas){var i=this,e=this.opts[n];if(this.conf[n],a=o[t].glbl,"boolean"==typeof e&&(e={close:e}),"object"!=typeof e&&(e={}),e=o.extend(!0,{},o[t].defaults[n],e),e.close){var c="#"+i.$menu.attr("id");this.bind("opened",function(){location.hash!=c&&history.pushState(null,document.title,c)}),o(window).on("popstate",function(o){a.$html.hasClass(s.opened)?(o.stopPropagation(),i.close()):location.hash==c&&(o.stopPropagation(),i.open())})}}},add:function(){return window.history&&window.history.pushState?(s=o[t]._c,i=o[t]._d,e=o[t]._e,void 0):(o[t].addons[n].setup=function(){},void 0)},clickAnchor:function(){}},o[t].defaults[n]={close:!1};var s,i,e,a}(jQuery);
/*
 * jQuery mmenu buttonbars addon
 * mmenu.frebsite.nl
 *
 * Copyright (c) Fred Heusschen
 */
!function(t){var n="mmenu",i="buttonbars";t[n].addons[i]={setup:function(){this.opts[i],this.conf[i],s=t[n].glbl,this.bind("init",function(n){this.__refactorClass(t("div",n),this.conf.classNames[i].buttonbar,"buttonbar"),t("."+a.buttonbar,n).each(function(){var n=t(this),i=n.children().not("input"),o=n.children().filter("input");n.addClass(a.buttonbar+"-"+i.length),o.each(function(){var n=t(this),a=i.filter('label[for="'+n.attr("id")+'"]');a.length&&n.insertBefore(a)})})})},add:function(){a=t[n]._c,o=t[n]._d,r=t[n]._e,a.add("buttonbar")},clickAnchor:function(){}},t[n].configuration.classNames[i]={buttonbar:"Buttonbar"};var a,o,r,s}(jQuery);
/*
 * jQuery mmenu counters addon
 * mmenu.frebsite.nl
 *
 * Copyright (c) Fred Heusschen
 */
!function(t){var n="mmenu",e="counters";t[n].addons[e]={setup:function(){var c=this,o=this.opts[e];this.conf[e],s=t[n].glbl,"boolean"==typeof o&&(o={add:o,update:o}),"object"!=typeof o&&(o={}),o=this.opts[e]=t.extend(!0,{},t[n].defaults[e],o),this.bind("init",function(n){this.__refactorClass(t("em",n),this.conf.classNames[e].counter,"counter")}),o.add&&this.bind("init",function(n){n.each(function(){var n=t(this).data(a.parent);n&&(n.children("em."+i.counter).length||n.prepend(t('<em class="'+i.counter+'" />')))})}),o.update&&this.bind("update",function(){this.$menu.find("."+i.panel).each(function(){var n=t(this),e=n.data(a.parent);if(e){var s=e.children("em."+i.counter);s.length&&(n=n.children("."+i.listview),n.length&&s.html(c.__filterListItems(n.children()).length))}})})},add:function(){i=t[n]._c,a=t[n]._d,c=t[n]._e,i.add("counter search noresultsmsg")},clickAnchor:function(){}},t[n].defaults[e]={add:!1,update:!1},t[n].configuration.classNames[e]={counter:"Counter"};var i,a,c,s}(jQuery);
/*
 * jQuery mmenu dividers addon
 * mmenu.frebsite.nl
 *
 * Copyright (c) Fred Heusschen
 */
!function(i){var e="mmenu",s="dividers";i[e].addons[s]={setup:function(){var n=this,a=this.opts[s];if(this.conf[s],l=i[e].glbl,"boolean"==typeof a&&(a={add:a,fixed:a}),"object"!=typeof a&&(a={}),a=this.opts[s]=i.extend(!0,{},i[e].defaults[s],a),this.bind("init",function(){this.__refactorClass(i("li",this.$menu),this.conf.classNames[s].collapsed,"collapsed")}),a.add&&this.bind("init",function(e){switch(a.addTo){case"panels":var s=e;break;default:var s=i(a.addTo,this.$menu).filter("."+d.panel)}i("."+d.divider,s).remove(),s.find("."+d.listview).not("."+d.vertical).each(function(){var e="";n.__filterListItems(i(this).children()).each(function(){var s=i.trim(i(this).children("a, span").text()).slice(0,1).toLowerCase();s!=e&&s.length&&(e=s,i('<li class="'+d.divider+'">'+s+"</li>").insertBefore(this))})})}),a.collapse&&this.bind("init",function(e){i("."+d.divider,e).each(function(){var e=i(this),s=e.nextUntil("."+d.divider,"."+d.collapsed);s.length&&(e.children("."+d.subopen).length||(e.wrapInner("<span />"),e.prepend('<a href="#" class="'+d.subopen+" "+d.fullsubopen+'" />')))})}),a.fixed){var o=function(e){e=e||this.$menu.children("."+d.current);var s=e.find("."+d.divider).not("."+d.hidden);if(s.length){this.$menu.addClass(d.hasdividers);var n=e.scrollTop()||0,t="";e.is(":visible")&&e.find("."+d.divider).not("."+d.hidden).each(function(){i(this).position().top+n<n+1&&(t=i(this).text())}),this.$fixeddivider.text(t)}else this.$menu.removeClass(d.hasdividers)};this.$fixeddivider=i('<ul class="'+d.listview+" "+d.fixeddivider+'"><li class="'+d.divider+'"></li></ul>').prependTo(this.$menu).children(),this.bind("openPanel",o),this.bind("init",function(e){e.off(t.scroll+"-dividers "+t.touchmove+"-dividers").on(t.scroll+"-dividers "+t.touchmove+"-dividers",function(){o.call(n,i(this))})})}},add:function(){d=i[e]._c,n=i[e]._d,t=i[e]._e,d.add("collapsed uncollapsed fixeddivider hasdividers"),t.add("scroll")},clickAnchor:function(i,e){if(this.opts[s].collapse&&e){var n=i.parent();if(n.is("."+d.divider)){var t=n.nextUntil("."+d.divider,"."+d.collapsed);return n.toggleClass(d.opened),t[n.hasClass(d.opened)?"addClass":"removeClass"](d.uncollapsed),!0}}return!1}},i[e].defaults[s]={add:!1,addTo:"panels",fixed:!1,collapse:!1},i[e].configuration.classNames[s]={collapsed:"Collapsed"};var d,n,t,l}(jQuery);
/*
 * jQuery mmenu dragOpen addon
 * mmenu.frebsite.nl
 *
 * Copyright (c) Fred Heusschen
 */
!function(e){function t(e,t,n){return t>e&&(e=t),e>n&&(e=n),e}var n="mmenu",o="dragOpen";e[n].addons[o]={setup:function(){if(this.opts.offCanvas){var i=this,a=this.opts[o],p=this.conf[o];if(r=e[n].glbl,"boolean"==typeof a&&(a={open:a}),"object"!=typeof a&&(a={}),a=this.opts[o]=e.extend(!0,{},e[n].defaults[o],a),a.open){var d,f,c,u,h,l={},m=0,g=!1,v=!1,w=0,_=0;switch(this.opts.offCanvas.position){case"left":case"right":l.events="panleft panright",l.typeLower="x",l.typeUpper="X",v="width";break;case"top":case"bottom":l.events="panup pandown",l.typeLower="y",l.typeUpper="Y",v="height"}switch(this.opts.offCanvas.position){case"right":case"bottom":l.negative=!0,u=function(e){e>=r.$wndw[v]()-a.maxStartPos&&(m=1)};break;default:l.negative=!1,u=function(e){e<=a.maxStartPos&&(m=1)}}switch(this.opts.offCanvas.position){case"left":l.open_dir="right",l.close_dir="left";break;case"right":l.open_dir="left",l.close_dir="right";break;case"top":l.open_dir="down",l.close_dir="up";break;case"bottom":l.open_dir="up",l.close_dir="down"}switch(this.opts.offCanvas.zposition){case"front":h=function(){return this.$menu};break;default:h=function(){return e("."+s.slideout)}}var b=this.__valueOrFn(a.pageNode,this.$menu,r.$page);"string"==typeof b&&(b=e(b));var y=new Hammer(b[0],a.vendors.hammer);y.on("panstart",function(e){u(e.center[l.typeLower]),r.$slideOutNodes=h(),g=l.open_dir}).on(l.events+" panend",function(e){m>0&&e.preventDefault()}).on(l.events,function(e){if(d=e["delta"+l.typeUpper],l.negative&&(d=-d),d!=w&&(g=d>=w?l.open_dir:l.close_dir),w=d,w>a.threshold&&1==m){if(r.$html.hasClass(s.opened))return;m=2,i._openSetup(),i.trigger("opening"),r.$html.addClass(s.dragging),_=t(r.$wndw[v]()*p[v].perc,p[v].min,p[v].max)}2==m&&(f=t(w,10,_)-("front"==i.opts.offCanvas.zposition?_:0),l.negative&&(f=-f),c="translate"+l.typeUpper+"("+f+"px )",r.$slideOutNodes.css({"-webkit-transform":"-webkit-"+c,transform:c}))}).on("panend",function(){2==m&&(r.$html.removeClass(s.dragging),r.$slideOutNodes.css("transform",""),i[g==l.open_dir?"_openFinish":"close"]()),m=0})}}},add:function(){return"function"!=typeof Hammer||Hammer.VERSION<2?(e[n].addons[o].setup=function(){},void 0):(s=e[n]._c,i=e[n]._d,a=e[n]._e,s.add("dragging"),void 0)},clickAnchor:function(){}},e[n].defaults[o]={open:!1,maxStartPos:100,threshold:50,vendors:{hammer:{}}},e[n].configuration[o]={width:{perc:.8,min:140,max:440},height:{perc:.8,min:140,max:880}};var s,i,a,r}(jQuery);
/*
 * jQuery mmenu fixedElements addon
 * mmenu.frebsite.nl
 *
 * Copyright (c) Fred Heusschen
 */
!function(i){var s="mmenu",a="fixedElements";i[s].addons[a]={setup:function(){if(this.opts.offCanvas){this.opts[a],this.conf[a],t=i[s].glbl;var d=function(i){var s=this.conf.classNames[a].fixed;this.__refactorClass(i.find("."+s),s,"fixed").appendTo(t.$body).addClass(n.slideout)};d.call(this,t.$page),this.bind("setPage",d)}},add:function(){n=i[s]._c,d=i[s]._d,e=i[s]._e,n.add("fixed")},clickAnchor:function(){}},i[s].configuration.classNames[a]={fixed:"Fixed"};var n,d,e,t}(jQuery);
/*
 * jQuery mmenu footer addon
 * mmenu.frebsite.nl
 *
 * Copyright (c) Fred Heusschen
 */
!function(t){var e="mmenu",o="footer";t[e].addons[o]={setup:function(){var i=this.opts[o];if(this.conf[o],a=t[e].glbl,"boolean"==typeof i&&(i={add:i,update:i}),"object"!=typeof i&&(i={}),i=this.opts[o]=t.extend(!0,{},t[e].defaults[o],i),i.add){var s=i.content?i.content:i.title;t('<div class="'+n.footer+'" />').appendTo(this.$menu).append(s),this.$menu.addClass(n.hasfooter)}if(this.$footer=this.$menu.children("."+n.footer),i.update&&this.$footer&&this.$footer.length){var d=function(e){e=e||this.$menu.children("."+n.current);var s=t("."+this.conf.classNames[o].panelFooter,e).html()||i.title;this.$footer[s?"removeClass":"addClass"](n.hidden),this.$footer.html(s)};this.bind("openPanel",d),this.bind("init",function(){d.call(this,this.$menu.children("."+n.current))})}},add:function(){n=t[e]._c,i=t[e]._d,s=t[e]._e,n.add("footer hasfooter")},clickAnchor:function(){}},t[e].defaults[o]={add:!1,content:!1,title:"",update:!1},t[e].configuration.classNames[o]={panelFooter:"Footer"};var n,i,s,a}(jQuery);
/*
 * jQuery mmenu header addon
 * mmenu.frebsite.nl
 *
 * Copyright (c) Fred Heusschen
 */
!function(e){var t="mmenu",a="header";e[t].addons[a]={setup:function(){var i=this.opts[a];if(this.conf[a],s=e[t].glbl,"boolean"==typeof i&&(i={add:i,update:i}),"object"!=typeof i&&(i={}),"undefined"==typeof i.content&&(i.content=["prev","title","next"]),i=this.opts[a]=e.extend(!0,{},e[t].defaults[a],i),i.add){if(i.content instanceof Array){for(var d=e("<div />"),h=0,l=i.content.length;l>h;h++)switch(i.content[h]){case"prev":case"next":case"close":d.append('<a class="'+n[i.content[h]]+" "+n.btn+'" href="#"></a>');break;case"title":d.append('<a class="'+n.title+'"></a>');break;default:d.append(i.content[h])}d=d.html()}else var d=i.content;e('<div class="'+n.header+'" />').prependTo(this.$menu).append(d),this.$menu.addClass(n.hasheader),this.bind("init",function(e){e.removeClass(n.hasheader)})}if(this.$header=this.$menu.children("."+n.header),i.update&&this.$header&&this.$header.length){var c=this.$header.find("."+n.title),o=this.$header.find("."+n.prev),f=this.$header.find("."+n.next),p=this.$header.find("."+n.close),u=function(e){e=e||this.$menu.children("."+n.current);var t=e.find("."+this.conf.classNames[a].panelHeader),s=e.find("."+this.conf.classNames[a].panelPrev),d=e.find("."+this.conf.classNames[a].panelNext),h=e.data(r.parent),l=t.html(),p=s.attr("href"),u=d.attr("href"),v=!1,m=s.html(),$=d.html();switch(l||(l=e.children("."+n.header).children("."+n.title).html()),l||(l=i.title),p||(p=e.children("."+n.header).children("."+n.prev).attr("href")),i.titleLink){case"anchor":var v=h?h.children("a").not("."+n.next).attr("href"):!1;break;case"panel":var v=p}c[v?"attr":"removeAttr"]("href",v),c[l?"removeClass":"addClass"](n.hidden),c.html(l),o[p?"attr":"removeAttr"]("href",p),o[p||m?"removeClass":"addClass"](n.hidden),o.html(m),f[u?"attr":"removeAttr"]("href",u),f[u||$?"removeClass":"addClass"](n.hidden),f.html($)};if(this.bind("openPanel",u),this.bind("init",function(){u.call(this,this.$menu.children("."+n.current))}),this.opts.offCanvas){var v=function(e){p.attr("href","#"+e.attr("id"))};v.call(this,s.$page),this.bind("setPage",v)}}},add:function(){n=e[t]._c,r=e[t]._d,i=e[t]._e,n.add("close")},clickAnchor:function(){}},e[t].defaults[a]={add:!1,title:"Menu",titleLink:"panel",update:!1},e[t].configuration.classNames[a]={panelHeader:"Header",panelNext:"Next",panelPrev:"Prev"};var n,r,i,s}(jQuery);
/*
 * jQuery mmenu searchfield addon
 * mmenu.frebsite.nl
 *
 * Copyright (c) Fred Heusschen
 */
!function(e){function s(e){switch(e){case 9:case 16:case 17:case 18:case 37:case 38:case 39:case 40:return!0}return!1}var a="mmenu",n="searchfield";e[a].addons[n]={setup:function(){var o=this,d=this.opts[n],c=this.conf[n];r=e[a].glbl,"boolean"==typeof d&&(d={add:d,search:d}),"object"!=typeof d&&(d={}),d=this.opts[n]=e.extend(!0,{},e[a].defaults[n],d),this.bind("init",function(a){if(d.add){switch(d.addTo){case"menu":var n=this.$menu;break;case"panels":var n=a;break;default:var n=e(d.addTo,this.$menu).filter("."+t.panel)}n.each(function(){var s=e(this);if(!s.is("."+t.panel)||!s.is("."+t.vertical)){if(!s.children("."+t.search).length){var a=c.form?"form":"div",n=e("<"+a+' class="'+t.search+'" />');if(c.form&&"object"==typeof c.form)for(var l in c.form)n.attr(l,c.form[l]);n.append('<input placeholder="'+d.placeholder+'" type="text" autocomplete="off" />').prependTo(s),s.addClass(t.hassearch)}if(d.noResults&&(s.is("."+t.menu)&&(s=s.children("."+t.panel).first()),!s.children("."+t.noresultsmsg).length)){var i=s.children("."+t.listview);e('<div class="'+t.noresultsmsg+'" />').append(d.noResults)[i.length?"insertBefore":"prependTo"](i.length?i:s)}}})}d.search&&e("."+t.search,this.$menu).each(function(){var a=e(this);if("menu"==d.addTo)var n=e("."+t.panel,o.$menu),r=o.$menu;else var n=a.closest("."+t.panel),r=n;var c=a.children("input"),h=o.__findAddBack(n,"."+t.listview).children("li"),u=h.filter("."+t.divider),f=o.__filterListItems(h),p="> a",v=p+", > span",m=function(){var s=c.val().toLowerCase();n.scrollTop(0),f.add(u).addClass(t.hidden).find("."+t.fullsubopensearch).removeClass(t.fullsubopen).removeClass(t.fullsubopensearch),f.each(function(){var a=e(this),n=p;(d.showTextItems||d.showSubPanels&&a.find("."+t.next))&&(n=v),e(n,a).text().toLowerCase().indexOf(s)>-1&&a.add(a.prevAll("."+t.divider).first()).removeClass(t.hidden)}),d.showSubPanels&&n.each(function(){var s=e(this);o.__filterListItems(s.find("."+t.listview).children()).each(function(){var s=e(this),a=s.data(l.sub);s.removeClass(t.nosubresults),a&&a.find("."+t.listview).children().removeClass(t.hidden)})}),e(n.get().reverse()).each(function(s){var a=e(this),n=a.data(l.parent);n&&(o.__filterListItems(a.find("."+t.listview).children()).length?(n.hasClass(t.hidden)&&n.children("."+t.next).not("."+t.fullsubopen).addClass(t.fullsubopen).addClass(t.fullsubopensearch),n.removeClass(t.hidden).removeClass(t.nosubresults).prevAll("."+t.divider).first().removeClass(t.hidden)):"menu"==d.addTo&&(a.hasClass(t.opened)&&setTimeout(function(){o.openPanel(n.closest("."+t.panel))},1.5*(s+1)*o.conf.openingInterval),n.addClass(t.nosubresults)))}),r[f.not("."+t.hidden).length?"removeClass":"addClass"](t.noresults),this.update()};c.off(i.keyup+"-searchfield "+i.change+"-searchfield").on(i.keyup+"-searchfield",function(e){s(e.keyCode)||m.call(o)}).on(i.change+"-searchfield",function(){m.call(o)})})})},add:function(){t=e[a]._c,l=e[a]._d,i=e[a]._e,t.add("search hassearch noresultsmsg noresults nosubresults fullsubopensearch"),i.add("change keyup")},clickAnchor:function(){}},e[a].defaults[n]={add:!1,addTo:"menu",search:!1,placeholder:"Search",noResults:"No results found.",showTextItems:!1,showSubPanels:!0},e[a].configuration[n]={form:!1};var t,l,i,r}(jQuery);
/*
 * jQuery mmenu sectionIndexer addon
 * mmenu.frebsite.nl
 *
 * Copyright (c) Fred Heusschen
 */
!function(e){var a="mmenu",r="sectionIndexer";e[a].addons[r]={setup:function(){var n=this,s=this.opts[r];this.conf[r],d=e[a].glbl,"boolean"==typeof s&&(s={add:s}),"object"!=typeof s&&(s={}),s=this.opts[r]=e.extend(!0,{},e[a].defaults[r],s),this.bind("init",function(a){if(s.add){switch(s.addTo){case"panels":var r=a;break;default:var r=e(s.addTo,this.$menu).filter("."+i.panel)}r.find("."+i.divider).closest("."+i.panel).addClass(i.hasindexer)}if(!this.$indexer&&this.$menu.children("."+i.hasindexer).length){this.$indexer=e('<div class="'+i.indexer+'" />').prependTo(this.$menu).append('<a href="#a">a</a><a href="#b">b</a><a href="#c">c</a><a href="#d">d</a><a href="#e">e</a><a href="#f">f</a><a href="#g">g</a><a href="#h">h</a><a href="#i">i</a><a href="#j">j</a><a href="#k">k</a><a href="#l">l</a><a href="#m">m</a><a href="#n">n</a><a href="#o">o</a><a href="#p">p</a><a href="#q">q</a><a href="#r">r</a><a href="#s">s</a><a href="#t">t</a><a href="#u">u</a><a href="#v">v</a><a href="#w">w</a><a href="#x">x</a><a href="#y">y</a><a href="#z">z</a><a href="##">#</a>'),this.$indexer.children().on(h.mouseover+"-searchfield "+i.touchmove+"-searchfield",function(){var a=e(this).attr("href").slice(1),r=n.$menu.children("."+i.current),h=r.find("."+i.listview),d=!1,s=r.scrollTop(),t=h.position().top+parseInt(h.css("margin-top"),10)+parseInt(h.css("padding-top"),10)+s;r.scrollTop(0),h.children("."+i.divider).not("."+i.hidden).each(function(){d===!1&&a==e(this).text().slice(0,1).toLowerCase()&&(d=e(this).position().top+t)}),r.scrollTop(d!==!1?d:s)});var d=function(e){n.$menu[(e.hasClass(i.hasindexer)?"add":"remove")+"Class"](i.hasindexer)};this.bind("openPanel",d),d.call(this,this.$menu.children("."+i.current))}})},add:function(){i=e[a]._c,n=e[a]._d,h=e[a]._e,i.add("indexer hasindexer"),h.add("mouseover touchmove")},clickAnchor:function(e){return e.parent().is("."+i.indexer)?!0:void 0}},e[a].defaults[r]={add:!1,addTo:"panels"};var i,n,h,d}(jQuery);
/*
 * jQuery mmenu toggles addon
 * mmenu.frebsite.nl
 *
 * Copyright (c) Fred Heusschen
 */
!function(t){var e="mmenu",c="toggles";t[e].addons[c]={setup:function(){var n=this;this.opts[c],this.conf[c],l=t[e].glbl,this.bind("init",function(e){this.__refactorClass(t("input",e),this.conf.classNames[c].toggle,"toggle"),this.__refactorClass(t("input",e),this.conf.classNames[c].check,"check"),t("input."+s.toggle+", input."+s.check,e).each(function(){var e=t(this),c=e.closest("li"),i=e.hasClass(s.toggle)?"toggle":"check",l=e.attr("id")||n.__getUniqueId();c.children('label[for="'+l+'"]').length||(e.attr("id",l),c.prepend(e),t('<label for="'+l+'" class="'+s[i]+'"></label>').insertBefore(c.children("a, span").last()))})})},add:function(){s=t[e]._c,n=t[e]._d,i=t[e]._e,s.add("toggle check")},clickAnchor:function(){}},t[e].configuration.classNames[c]={toggle:"Toggle",check:"Check"};var s,n,i,l}(jQuery);/*
 * Swiper 2.7.6
 * Mobile touch slider and framework with hardware accelerated transitions
 *
 * http://www.idangero.us/sliders/swiper/
 *
 * Copyright 2010-2015, Vladimir Kharlampidi
 * The iDangero.us
 * http://www.idangero.us/
 *
 * Licensed under GPL & MIT
 *
 * Released on: February 11, 2015
 */
var Swiper = function (selector, params) {
    'use strict';

    /*=========================
     A little bit dirty but required part for IE8 and old FF support
     ===========================*/
    if (!document.body.outerHTML && document.body.__defineGetter__) {
        if (HTMLElement) {
            var element = HTMLElement.prototype;
            if (element.__defineGetter__) {
                element.__defineGetter__('outerHTML', function () { return new XMLSerializer().serializeToString(this); });
            }
        }
    }

    if (!window.getComputedStyle) {
        window.getComputedStyle = function (el, pseudo) {
            this.el = el;
            this.getPropertyValue = function (prop) {
                var re = /(\-([a-z]){1})/g;
                if (prop === 'float') prop = 'styleFloat';
                if (re.test(prop)) {
                    prop = prop.replace(re, function () {
                        return arguments[2].toUpperCase();
                    });
                }
                return el.currentStyle[prop] ? el.currentStyle[prop] : null;
            };
            return this;
        };
    }
    if (!Array.prototype.indexOf) {
        Array.prototype.indexOf = function (obj, start) {
            for (var i = (start || 0), j = this.length; i < j; i++) {
                if (this[i] === obj) { return i; }
            }
            return -1;
        };
    }
    if (!document.querySelectorAll) {
        if (!window.jQuery) return;
    }
    function $$(selector, context) {
        if (document.querySelectorAll)
            return (context || document).querySelectorAll(selector);
        else
            return jQuery(selector, context);
    }

    /*=========================
     Check for correct selector
     ===========================*/
    if (typeof selector === 'undefined') return;

    if (!(selector.nodeType)) {
        if ($$(selector).length === 0) return;
    }

    /*=========================
     _this
     ===========================*/
    var _this = this;

    /*=========================
     Default Flags and vars
     ===========================*/
    _this.touches = {
        start: 0,
        startX: 0,
        startY: 0,
        current: 0,
        currentX: 0,
        currentY: 0,
        diff: 0,
        abs: 0
    };
    _this.positions = {
        start: 0,
        abs: 0,
        diff: 0,
        current: 0
    };
    _this.times = {
        start: 0,
        end: 0
    };

    _this.id = (new Date()).getTime();
    _this.container = (selector.nodeType) ? selector : $$(selector)[0];
    _this.isTouched = false;
    _this.isMoved = false;
    _this.activeIndex = 0;
    _this.centerIndex = 0;
    _this.activeLoaderIndex = 0;
    _this.activeLoopIndex = 0;
    _this.previousIndex = null;
    _this.velocity = 0;
    _this.snapGrid = [];
    _this.slidesGrid = [];
    _this.imagesToLoad = [];
    _this.imagesLoaded = 0;
    _this.wrapperLeft = 0;
    _this.wrapperRight = 0;
    _this.wrapperTop = 0;
    _this.wrapperBottom = 0;
    _this.isAndroid = navigator.userAgent.toLowerCase().indexOf('android') >= 0;
    var wrapper, slideSize, wrapperSize, direction, isScrolling, containerSize;

    /*=========================
     Default Parameters
     ===========================*/
    var defaults = {
        eventTarget: 'wrapper', // or 'container'
        mode : 'horizontal', // or 'vertical'
        touchRatio : 1,
        speed : 300,
        freeMode : false,
        freeModeFluid : false,
        momentumRatio: 1,
        momentumBounce: true,
        momentumBounceRatio: 1,
        slidesPerView : 1,
        slidesPerGroup : 1,
        slidesPerViewFit: true, //Fit to slide when spv "auto" and slides larger than container
        simulateTouch : true,
        followFinger : true,
        shortSwipes : true,
        longSwipesRatio: 0.5,
        moveStartThreshold: false,
        onlyExternal : false,
        createPagination : true,
        pagination : false,
        paginationElement: 'span',
        paginationClickable: false,
        paginationAsRange: true,
        resistance : true, // or false or 100%
        scrollContainer : false,
        preventLinks : true,
        preventLinksPropagation: false,
        noSwiping : false, // or class
        noSwipingClass : 'swiper-no-swiping', //:)
        initialSlide: 0,
        keyboardControl: false,
        mousewheelControl : false,
        mousewheelControlForceToAxis : false,
        useCSS3Transforms : true,
        // Autoplay
        autoplay: false,
        autoplayDisableOnInteraction: true,
        autoplayStopOnLast: false,
        //Loop mode
        loop: false,
        loopAdditionalSlides: 0,
        // Round length values
        roundLengths: false,
        //Auto Height
        calculateHeight: false,
        //Apply CSS for width and/or height
        cssWidthAndHeight: false, // or true or 'width' or 'height'
        //Images Preloader
        updateOnImagesReady : true,
        //Form elements
        releaseFormElements : true,
        //Watch for active slide, useful when use effects on different slide states
        watchActiveIndex: false,
        //Slides Visibility Fit
        visibilityFullFit : false,
        //Slides Offset
        offsetPxBefore : 0,
        offsetPxAfter : 0,
        offsetSlidesBefore : 0,
        offsetSlidesAfter : 0,
        centeredSlides: false,
        //Queue callbacks
        queueStartCallbacks : false,
        queueEndCallbacks : false,
        //Auto Resize
        autoResize : true,
        resizeReInit : false,
        //DOMAnimation
        DOMAnimation : true,
        //Slides Loader
        loader: {
            slides: [], //array with slides
            slidesHTMLType: 'inner', // or 'outer'
            surroundGroups: 1, //keep preloaded slides groups around view
            logic: 'reload', //or 'change'
            loadAllSlides: false
        },
        // One way swipes
        swipeToPrev: true,
        swipeToNext: true,
        //Namespace
        slideElement: 'div',
        slideClass: 'swiper-slide',
        slideActiveClass: 'swiper-slide-active',
        slideVisibleClass: 'swiper-slide-visible',
        slideDuplicateClass: 'swiper-slide-duplicate',
        wrapperClass: 'swiper-wrapper',
        paginationElementClass: 'swiper-pagination-switch',
        paginationActiveClass: 'swiper-active-switch',
        paginationVisibleClass: 'swiper-visible-switch'
    };
    params = params || {};
    for (var prop in defaults) {
        if (prop in params && typeof params[prop] === 'object') {
            for (var subProp in defaults[prop]) {
                if (! (subProp in params[prop])) {
                    params[prop][subProp] = defaults[prop][subProp];
                }
            }
        }
        else if (! (prop in params)) {
            params[prop] = defaults[prop];
        }
    }
    _this.params = params;
    if (params.scrollContainer) {
        params.freeMode = true;
        params.freeModeFluid = true;
    }
    if (params.loop) {
        params.resistance = '100%';
    }
    var isH = params.mode === 'horizontal';

    /*=========================
     Define Touch Events
     ===========================*/
    var desktopEvents = ['mousedown', 'mousemove', 'mouseup'];
    if (_this.browser.ie10) desktopEvents = ['MSPointerDown', 'MSPointerMove', 'MSPointerUp'];
    if (_this.browser.ie11) desktopEvents = ['pointerdown', 'pointermove', 'pointerup'];

    _this.touchEvents = {
        touchStart : _this.support.touch || !params.simulateTouch  ? 'touchstart' : desktopEvents[0],
        touchMove : _this.support.touch || !params.simulateTouch ? 'touchmove' : desktopEvents[1],
        touchEnd : _this.support.touch || !params.simulateTouch ? 'touchend' : desktopEvents[2]
    };

    /*=========================
     Wrapper
     ===========================*/
    for (var i = _this.container.childNodes.length - 1; i >= 0; i--) {
        if (_this.container.childNodes[i].className) {
            var _wrapperClasses = _this.container.childNodes[i].className.split(/\s+/);
            for (var j = 0; j < _wrapperClasses.length; j++) {
                if (_wrapperClasses[j] === params.wrapperClass) {
                    wrapper = _this.container.childNodes[i];
                }
            }
        }
    }

    _this.wrapper = wrapper;
    /*=========================
     Slide API
     ===========================*/
    _this._extendSwiperSlide = function  (el) {
        el.append = function () {
            if (params.loop) {
                el.insertAfter(_this.slides.length - _this.loopedSlides);
            }
            else {
                _this.wrapper.appendChild(el);
                _this.reInit();
            }

            return el;
        };
        el.prepend = function () {
            if (params.loop) {
                _this.wrapper.insertBefore(el, _this.slides[_this.loopedSlides]);
                _this.removeLoopedSlides();
                _this.calcSlides();
                _this.createLoop();
            }
            else {
                _this.wrapper.insertBefore(el, _this.wrapper.firstChild);
            }
            _this.reInit();
            return el;
        };
        el.insertAfter = function (index) {
            if (typeof index === 'undefined') return false;
            var beforeSlide;

            if (params.loop) {
                beforeSlide = _this.slides[index + 1 + _this.loopedSlides];
                if (beforeSlide) {
                    _this.wrapper.insertBefore(el, beforeSlide);
                }
                else {
                    _this.wrapper.appendChild(el);
                }
                _this.removeLoopedSlides();
                _this.calcSlides();
                _this.createLoop();
            }
            else {
                beforeSlide = _this.slides[index + 1];
                _this.wrapper.insertBefore(el, beforeSlide);
            }
            _this.reInit();
            return el;
        };
        el.clone = function () {
            return _this._extendSwiperSlide(el.cloneNode(true));
        };
        el.remove = function () {
            _this.wrapper.removeChild(el);
            _this.reInit();
        };
        el.html = function (html) {
            if (typeof html === 'undefined') {
                return el.innerHTML;
            }
            else {
                el.innerHTML = html;
                return el;
            }
        };
        el.index = function () {
            var index;
            for (var i = _this.slides.length - 1; i >= 0; i--) {
                if (el === _this.slides[i]) index = i;
            }
            return index;
        };
        el.isActive = function () {
            if (el.index() === _this.activeIndex) return true;
            else return false;
        };
        if (!el.swiperSlideDataStorage) el.swiperSlideDataStorage = {};
        el.getData = function (name) {
            return el.swiperSlideDataStorage[name];
        };
        el.setData = function (name, value) {
            el.swiperSlideDataStorage[name] = value;
            return el;
        };
        el.data = function (name, value) {
            if (typeof value === 'undefined') {
                return el.getAttribute('data-' + name);
            }
            else {
                el.setAttribute('data-' + name, value);
                return el;
            }
        };
        el.getWidth = function (outer, round) {
            return _this.h.getWidth(el, outer, round);
        };
        el.getHeight = function (outer, round) {
            return _this.h.getHeight(el, outer, round);
        };
        el.getOffset = function () {
            return _this.h.getOffset(el);
        };
        return el;
    };

    //Calculate information about number of slides
    _this.calcSlides = function (forceCalcSlides) {
        var oldNumber = _this.slides ? _this.slides.length : false;
        _this.slides = [];
        _this.displaySlides = [];
        for (var i = 0; i < _this.wrapper.childNodes.length; i++) {
            if (_this.wrapper.childNodes[i].className) {
                var _className = _this.wrapper.childNodes[i].className;
                var _slideClasses = _className.split(/\s+/);
                for (var j = 0; j < _slideClasses.length; j++) {
                    if (_slideClasses[j] === params.slideClass) {
                        _this.slides.push(_this.wrapper.childNodes[i]);
                    }
                }
            }
        }
        for (i = _this.slides.length - 1; i >= 0; i--) {
            _this._extendSwiperSlide(_this.slides[i]);
        }
        if (oldNumber === false) return;
        if (oldNumber !== _this.slides.length || forceCalcSlides) {

            // Number of slides has been changed
            removeSlideEvents();
            addSlideEvents();
            _this.updateActiveSlide();
            if (_this.params.pagination) _this.createPagination();
            _this.callPlugins('numberOfSlidesChanged');
        }
    };

    //Create Slide
    _this.createSlide = function (html, slideClassList, el) {
        slideClassList = slideClassList || _this.params.slideClass;
        el = el || params.slideElement;
        var newSlide = document.createElement(el);
        newSlide.innerHTML = html || '';
        newSlide.className = slideClassList;
        return _this._extendSwiperSlide(newSlide);
    };

    //Append Slide
    _this.appendSlide = function (html, slideClassList, el) {
        if (!html) return;
        if (html.nodeType) {
            return _this._extendSwiperSlide(html).append();
        }
        else {
            return _this.createSlide(html, slideClassList, el).append();
        }
    };
    _this.prependSlide = function (html, slideClassList, el) {
        if (!html) return;
        if (html.nodeType) {
            return _this._extendSwiperSlide(html).prepend();
        }
        else {
            return _this.createSlide(html, slideClassList, el).prepend();
        }
    };
    _this.insertSlideAfter = function (index, html, slideClassList, el) {
        if (typeof index === 'undefined') return false;
        if (html.nodeType) {
            return _this._extendSwiperSlide(html).insertAfter(index);
        }
        else {
            return _this.createSlide(html, slideClassList, el).insertAfter(index);
        }
    };
    _this.removeSlide = function (index) {
        if (_this.slides[index]) {
            if (params.loop) {
                if (!_this.slides[index + _this.loopedSlides]) return false;
                _this.slides[index + _this.loopedSlides].remove();
                _this.removeLoopedSlides();
                _this.calcSlides();
                _this.createLoop();
            }
            else _this.slides[index].remove();
            return true;
        }
        else return false;
    };
    _this.removeLastSlide = function () {
        if (_this.slides.length > 0) {
            if (params.loop) {
                _this.slides[_this.slides.length - 1 - _this.loopedSlides].remove();
                _this.removeLoopedSlides();
                _this.calcSlides();
                _this.createLoop();
            }
            else _this.slides[_this.slides.length - 1].remove();
            return true;
        }
        else {
            return false;
        }
    };
    _this.removeAllSlides = function () {
        var num = _this.slides.length;
        for (var i = _this.slides.length - 1; i >= 0; i--) {
            _this.slides[i].remove();
            if (i === num - 1) {
                _this.setWrapperTranslate(0);
            }
        }
    };
    _this.getSlide = function (index) {
        return _this.slides[index];
    };
    _this.getLastSlide = function () {
        return _this.slides[_this.slides.length - 1];
    };
    _this.getFirstSlide = function () {
        return _this.slides[0];
    };

    //Currently Active Slide
    _this.activeSlide = function () {
        return _this.slides[_this.activeIndex];
    };

    /*=========================
     Wrapper for Callbacks : Allows additive callbacks via function arrays
     ===========================*/
    _this.fireCallback = function () {
        var callback = arguments[0];
        if (Object.prototype.toString.call(callback) === '[object Array]') {
            for (var i = 0; i < callback.length; i++) {
                if (typeof callback[i] === 'function') {
                    callback[i](arguments[1], arguments[2], arguments[3], arguments[4], arguments[5]);
                }
            }
        } else if (Object.prototype.toString.call(callback) === '[object String]') {
            if (params['on' + callback]) _this.fireCallback(params['on' + callback], arguments[1], arguments[2], arguments[3], arguments[4], arguments[5]);
        } else {
            callback(arguments[1], arguments[2], arguments[3], arguments[4], arguments[5]);
        }
    };
    function isArray(obj) {
        if (Object.prototype.toString.apply(obj) === '[object Array]') return true;
        return false;
    }

    /**
     * Allows user to add callbacks, rather than replace them
     * @param callback
     * @param func
     * @return {*}
     */
    _this.addCallback = function (callback, func) {
        var _this = this, tempFunc;
        if (_this.params['on' + callback]) {
            if (isArray(this.params['on' + callback])) {
                return this.params['on' + callback].push(func);
            } else if (typeof this.params['on' + callback] === 'function') {
                tempFunc = this.params['on' + callback];
                this.params['on' + callback] = [];
                this.params['on' + callback].push(tempFunc);
                return this.params['on' + callback].push(func);
            }
        } else {
            this.params['on' + callback] = [];
            return this.params['on' + callback].push(func);
        }
    };
    _this.removeCallbacks = function (callback) {
        if (_this.params['on' + callback]) {
            _this.params['on' + callback] = null;
        }
    };

    /*=========================
     Plugins API
     ===========================*/
    var _plugins = [];
    for (var plugin in _this.plugins) {
        if (params[plugin]) {
            var p = _this.plugins[plugin](_this, params[plugin]);
            if (p) _plugins.push(p);
        }
    }
    _this.callPlugins = function (method, args) {
        if (!args) args = {};
        for (var i = 0; i < _plugins.length; i++) {
            if (method in _plugins[i]) {
                _plugins[i][method](args);
            }
        }
    };

    /*=========================
     Windows Phone 8 Fix
     ===========================*/
    if ((_this.browser.ie10 || _this.browser.ie11) && !params.onlyExternal) {
        _this.wrapper.classList.add('swiper-wp8-' + (isH ? 'horizontal' : 'vertical'));
    }

    /*=========================
     Free Mode Class
     ===========================*/
    if (params.freeMode) {
        _this.container.className += ' swiper-free-mode';
    }

    /*==================================================
     Init/Re-init/Resize Fix
     ====================================================*/
    _this.initialized = false;
    _this.init = function (force, forceCalcSlides) {
        var _width = _this.h.getWidth(_this.container, false, params.roundLengths);
        var _height = _this.h.getHeight(_this.container, false, params.roundLengths);
        if (_width === _this.width && _height === _this.height && !force) return;

        _this.width = _width;
        _this.height = _height;

        var slideWidth, slideHeight, slideMaxHeight, wrapperWidth, wrapperHeight, slideLeft;
        var i; // loop index variable to avoid JSHint W004 / W038
        containerSize = isH ? _width : _height;
        var wrapper = _this.wrapper;

        if (force) {
            _this.calcSlides(forceCalcSlides);
        }

        if (params.slidesPerView === 'auto') {
            //Auto mode
            var slidesWidth = 0;
            var slidesHeight = 0;

            //Unset Styles
            if (params.slidesOffset > 0) {
                wrapper.style.paddingLeft = '';
                wrapper.style.paddingRight = '';
                wrapper.style.paddingTop = '';
                wrapper.style.paddingBottom = '';
            }
            wrapper.style.width = '';
            wrapper.style.height = '';
            if (params.offsetPxBefore > 0) {
                if (isH) _this.wrapperLeft = params.offsetPxBefore;
                else _this.wrapperTop = params.offsetPxBefore;
            }
            if (params.offsetPxAfter > 0) {
                if (isH) _this.wrapperRight = params.offsetPxAfter;
                else _this.wrapperBottom = params.offsetPxAfter;
            }

            if (params.centeredSlides) {
                if (isH) {
                    _this.wrapperLeft = (containerSize - this.slides[0].getWidth(true, params.roundLengths)) / 2;
                    _this.wrapperRight = (containerSize - _this.slides[_this.slides.length - 1].getWidth(true, params.roundLengths)) / 2;
                }
                else {
                    _this.wrapperTop = (containerSize - _this.slides[0].getHeight(true, params.roundLengths)) / 2;
                    _this.wrapperBottom = (containerSize - _this.slides[_this.slides.length - 1].getHeight(true, params.roundLengths)) / 2;
                }
            }

            if (isH) {
                if (_this.wrapperLeft >= 0) wrapper.style.paddingLeft = _this.wrapperLeft + 'px';
                if (_this.wrapperRight >= 0) wrapper.style.paddingRight = _this.wrapperRight + 'px';
            }
            else {
                if (_this.wrapperTop >= 0) wrapper.style.paddingTop = _this.wrapperTop + 'px';
                if (_this.wrapperBottom >= 0) wrapper.style.paddingBottom = _this.wrapperBottom + 'px';
            }
            slideLeft = 0;
            var centeredSlideLeft = 0;
            _this.snapGrid = [];
            _this.slidesGrid = [];

            slideMaxHeight = 0;
            for (i = 0; i < _this.slides.length; i++) {
                slideWidth = _this.slides[i].getWidth(true, params.roundLengths);
                slideHeight = _this.slides[i].getHeight(true, params.roundLengths);
                if (params.calculateHeight) {
                    slideMaxHeight = Math.max(slideMaxHeight, slideHeight);
                }
                var _slideSize = isH ? slideWidth : slideHeight;
                if (params.centeredSlides) {
                    var nextSlideWidth = i === _this.slides.length - 1 ? 0 : _this.slides[i + 1].getWidth(true, params.roundLengths);
                    var nextSlideHeight = i === _this.slides.length - 1 ? 0 : _this.slides[i + 1].getHeight(true, params.roundLengths);
                    var nextSlideSize = isH ? nextSlideWidth : nextSlideHeight;
                    if (_slideSize > containerSize) {
                        if (params.slidesPerViewFit) {
                            _this.snapGrid.push(slideLeft + _this.wrapperLeft);
                            _this.snapGrid.push(slideLeft + _slideSize - containerSize + _this.wrapperLeft);
                        }
                        else {
                            for (var j = 0; j <= Math.floor(_slideSize / (containerSize + _this.wrapperLeft)); j++) {
                                if (j === 0) _this.snapGrid.push(slideLeft + _this.wrapperLeft);
                                else _this.snapGrid.push(slideLeft + _this.wrapperLeft + containerSize * j);
                            }
                        }
                        _this.slidesGrid.push(slideLeft + _this.wrapperLeft);
                    }
                    else {
                        _this.snapGrid.push(centeredSlideLeft);
                        _this.slidesGrid.push(centeredSlideLeft);
                    }
                    centeredSlideLeft += _slideSize / 2 + nextSlideSize / 2;
                }
                else {
                    if (_slideSize > containerSize) {
                        if (params.slidesPerViewFit) {
                            _this.snapGrid.push(slideLeft);
                            _this.snapGrid.push(slideLeft + _slideSize - containerSize);
                        }
                        else {
                            if (containerSize !== 0) {
                                for (var k = 0; k <= Math.floor(_slideSize / containerSize); k++) {
                                    _this.snapGrid.push(slideLeft + containerSize * k);
                                }
                            }
                            else {
                                _this.snapGrid.push(slideLeft);
                            }
                        }

                    }
                    else {
                        _this.snapGrid.push(slideLeft);
                    }
                    _this.slidesGrid.push(slideLeft);
                }

                slideLeft += _slideSize;

                slidesWidth += slideWidth;
                slidesHeight += slideHeight;
            }
            if (params.calculateHeight) _this.height = slideMaxHeight;
            if (isH) {
                wrapperSize = slidesWidth + _this.wrapperRight + _this.wrapperLeft;
                if (!params.cssWidthAndHeight || params.cssWidthAndHeight === 'height') {
                    wrapper.style.width = (slidesWidth) + 'px';
                }
                if (!params.cssWidthAndHeight || params.cssWidthAndHeight === 'width') {
                    wrapper.style.height = (_this.height) + 'px';
                }
            }
            else {
                if (!params.cssWidthAndHeight || params.cssWidthAndHeight === 'height') {
                    wrapper.style.width = (_this.width) + 'px';
                }
                if (!params.cssWidthAndHeight || params.cssWidthAndHeight === 'width') {
                    wrapper.style.height = (slidesHeight) + 'px';
                }
                wrapperSize = slidesHeight + _this.wrapperTop + _this.wrapperBottom;
            }

        }
        else if (params.scrollContainer) {
            //Scroll Container
            wrapper.style.width = '';
            wrapper.style.height = '';
            wrapperWidth = _this.slides[0].getWidth(true, params.roundLengths);
            wrapperHeight = _this.slides[0].getHeight(true, params.roundLengths);
            wrapperSize = isH ? wrapperWidth : wrapperHeight;
            wrapper.style.width = wrapperWidth + 'px';
            wrapper.style.height = wrapperHeight + 'px';
            slideSize = isH ? wrapperWidth : wrapperHeight;

        }
        else {
            //For usual slides
            if (params.calculateHeight) {
                slideMaxHeight = 0;
                wrapperHeight = 0;
                //ResetWrapperSize
                if (!isH) _this.container.style.height = '';
                wrapper.style.height = '';

                for (i = 0; i < _this.slides.length; i++) {
                    //ResetSlideSize
                    _this.slides[i].style.height = '';
                    slideMaxHeight = Math.max(_this.slides[i].getHeight(true), slideMaxHeight);
                    if (!isH) wrapperHeight += _this.slides[i].getHeight(true);
                }
                slideHeight = slideMaxHeight;
                _this.height = slideHeight;

                if (isH) wrapperHeight = slideHeight;
                else {
                    containerSize = slideHeight;
                    _this.container.style.height = containerSize + 'px';
                }
            }
            else {
                slideHeight = isH ? _this.height : _this.height / params.slidesPerView;
                if (params.roundLengths) slideHeight = Math.ceil(slideHeight);
                wrapperHeight = isH ? _this.height : _this.slides.length * slideHeight;
            }
            slideWidth = isH ? _this.width / params.slidesPerView : _this.width;
            if (params.roundLengths) slideWidth = Math.ceil(slideWidth);
            wrapperWidth = isH ? _this.slides.length * slideWidth : _this.width;
            slideSize = isH ? slideWidth : slideHeight;

            if (params.offsetSlidesBefore > 0) {
                if (isH) _this.wrapperLeft = slideSize * params.offsetSlidesBefore;
                else _this.wrapperTop = slideSize * params.offsetSlidesBefore;
            }
            if (params.offsetSlidesAfter > 0) {
                if (isH) _this.wrapperRight = slideSize * params.offsetSlidesAfter;
                else _this.wrapperBottom = slideSize * params.offsetSlidesAfter;
            }
            if (params.offsetPxBefore > 0) {
                if (isH) _this.wrapperLeft = params.offsetPxBefore;
                else _this.wrapperTop = params.offsetPxBefore;
            }
            if (params.offsetPxAfter > 0) {
                if (isH) _this.wrapperRight = params.offsetPxAfter;
                else _this.wrapperBottom = params.offsetPxAfter;
            }
            if (params.centeredSlides) {
                if (isH) {
                    _this.wrapperLeft = (containerSize - slideSize) / 2;
                    _this.wrapperRight = (containerSize - slideSize) / 2;
                }
                else {
                    _this.wrapperTop = (containerSize - slideSize) / 2;
                    _this.wrapperBottom = (containerSize - slideSize) / 2;
                }
            }
            if (isH) {
                if (_this.wrapperLeft > 0) wrapper.style.paddingLeft = _this.wrapperLeft + 'px';
                if (_this.wrapperRight > 0) wrapper.style.paddingRight = _this.wrapperRight + 'px';
            }
            else {
                if (_this.wrapperTop > 0) wrapper.style.paddingTop = _this.wrapperTop + 'px';
                if (_this.wrapperBottom > 0) wrapper.style.paddingBottom = _this.wrapperBottom + 'px';
            }

            wrapperSize = isH ? wrapperWidth + _this.wrapperRight + _this.wrapperLeft : wrapperHeight + _this.wrapperTop + _this.wrapperBottom;
            if (parseFloat(wrapperWidth) > 0 && (!params.cssWidthAndHeight || params.cssWidthAndHeight === 'height')) {
                wrapper.style.width = wrapperWidth + 'px';
            }
            if (parseFloat(wrapperHeight) > 0 && (!params.cssWidthAndHeight || params.cssWidthAndHeight === 'width')) {
                wrapper.style.height = wrapperHeight + 'px';
            }
            slideLeft = 0;
            _this.snapGrid = [];
            _this.slidesGrid = [];
            for (i = 0; i < _this.slides.length; i++) {
                _this.snapGrid.push(slideLeft);
                _this.slidesGrid.push(slideLeft);
                slideLeft += slideSize;
                if (parseFloat(slideWidth) > 0 && (!params.cssWidthAndHeight || params.cssWidthAndHeight === 'height')) {
                    _this.slides[i].style.width = slideWidth + 'px';
                }
                if (parseFloat(slideHeight) > 0 && (!params.cssWidthAndHeight || params.cssWidthAndHeight === 'width')) {
                    _this.slides[i].style.height = slideHeight + 'px';
                }
            }

        }

        if (!_this.initialized) {
            _this.callPlugins('onFirstInit');
            if (params.onFirstInit) _this.fireCallback(params.onFirstInit, _this);
        }
        else {
            _this.callPlugins('onInit');
            if (params.onInit) _this.fireCallback(params.onInit, _this);
        }
        _this.initialized = true;
    };

    _this.reInit = function (forceCalcSlides) {
        _this.init(true, forceCalcSlides);
    };

    _this.resizeFix = function (reInit) {
        _this.callPlugins('beforeResizeFix');

        _this.init(params.resizeReInit || reInit);

        // swipe to active slide in fixed mode
        if (!params.freeMode) {
            _this.swipeTo((params.loop ? _this.activeLoopIndex : _this.activeIndex), 0, false);
            // Fix autoplay
            if (params.autoplay) {
                if (_this.support.transitions && typeof autoplayTimeoutId !== 'undefined') {
                    if (typeof autoplayTimeoutId !== 'undefined') {
                        clearTimeout(autoplayTimeoutId);
                        autoplayTimeoutId = undefined;
                        _this.startAutoplay();
                    }
                }
                else {
                    if (typeof autoplayIntervalId !== 'undefined') {
                        clearInterval(autoplayIntervalId);
                        autoplayIntervalId = undefined;
                        _this.startAutoplay();
                    }
                }
            }
        }
        // move wrapper to the beginning in free mode
        else if (_this.getWrapperTranslate() < -maxWrapperPosition()) {
            _this.setWrapperTransition(0);
            _this.setWrapperTranslate(-maxWrapperPosition());
        }

        _this.callPlugins('afterResizeFix');
    };

    /*==========================================
     Max and Min Positions
     ============================================*/
    function maxWrapperPosition() {
        var a = (wrapperSize - containerSize);
        if (params.freeMode) {
            a = wrapperSize - containerSize;
        }
        // if (params.loop) a -= containerSize;
        if (params.slidesPerView > _this.slides.length && !params.centeredSlides) {
            a = 0;
        }
        if (a < 0) a = 0;
        return a;
    }

    /*==========================================
     Event Listeners
     ============================================*/
    function initEvents() {
        var bind = _this.h.addEventListener;
        var eventTarget = params.eventTarget === 'wrapper' ? _this.wrapper : _this.container;
        //Touch Events
        if (! (_this.browser.ie10 || _this.browser.ie11)) {
            if (_this.support.touch) {
                bind(eventTarget, 'touchstart', onTouchStart);
                bind(eventTarget, 'touchmove', onTouchMove);
                bind(eventTarget, 'touchend', onTouchEnd);
            }
            if (params.simulateTouch) {
                bind(eventTarget, 'mousedown', onTouchStart);
                bind(document, 'mousemove', onTouchMove);
                bind(document, 'mouseup', onTouchEnd);
            }
        }
        else {
            bind(eventTarget, _this.touchEvents.touchStart, onTouchStart);
            bind(document, _this.touchEvents.touchMove, onTouchMove);
            bind(document, _this.touchEvents.touchEnd, onTouchEnd);
        }

        //Resize Event
        if (params.autoResize) {
            bind(window, 'resize', _this.resizeFix);
        }
        //Slide Events
        addSlideEvents();
        //Mousewheel
        _this._wheelEvent = false;
        if (params.mousewheelControl) {
            if (document.onmousewheel !== undefined) {
                _this._wheelEvent = 'mousewheel';
            }
            if (!_this._wheelEvent) {
                try {
                    new WheelEvent('wheel');
                    _this._wheelEvent = 'wheel';
                } catch (e) {}
            }
            if (!_this._wheelEvent) {
                _this._wheelEvent = 'DOMMouseScroll';
            }
            if (_this._wheelEvent) {
                bind(_this.container, _this._wheelEvent, handleMousewheel);
            }
        }

        //Keyboard
        function _loadImage(img) {
            var image, src;
            var onReady = function () {
                if (typeof _this === 'undefined' || _this === null) return;
                if (_this.imagesLoaded !== undefined) _this.imagesLoaded++;
                if (_this.imagesLoaded === _this.imagesToLoad.length) {
                    _this.reInit();
                    if (params.onImagesReady) _this.fireCallback(params.onImagesReady, _this);
                }
            };

            if (!img.complete) {
                src = (img.currentSrc || img.getAttribute('src'));
                if (src) {
                    image = new Image();
                    image.onload = onReady;
                    image.onerror = onReady;
                    image.src = src;
                } else {
                    onReady();
                }

            } else {//image already loaded...
                onReady();
            }
        }

        if (params.keyboardControl) {
            bind(document, 'keydown', handleKeyboardKeys);
        }
        if (params.updateOnImagesReady) {
            _this.imagesToLoad = $$('img', _this.container);

            for (var i = 0; i < _this.imagesToLoad.length; i++) {
                _loadImage(_this.imagesToLoad[i]);
            }
        }
    }

    //Remove Event Listeners
    _this.destroy = function (removeStyles) {
        var unbind = _this.h.removeEventListener;
        var eventTarget = params.eventTarget === 'wrapper' ? _this.wrapper : _this.container;
        //Touch Events
        if (! (_this.browser.ie10 || _this.browser.ie11)) {
            if (_this.support.touch) {
                unbind(eventTarget, 'touchstart', onTouchStart);
                unbind(eventTarget, 'touchmove', onTouchMove);
                unbind(eventTarget, 'touchend', onTouchEnd);
            }
            if (params.simulateTouch) {
                unbind(eventTarget, 'mousedown', onTouchStart);
                unbind(document, 'mousemove', onTouchMove);
                unbind(document, 'mouseup', onTouchEnd);
            }
        }
        else {
            unbind(eventTarget, _this.touchEvents.touchStart, onTouchStart);
            unbind(document, _this.touchEvents.touchMove, onTouchMove);
            unbind(document, _this.touchEvents.touchEnd, onTouchEnd);
        }

        //Resize Event
        if (params.autoResize) {
            unbind(window, 'resize', _this.resizeFix);
        }

        //Init Slide Events
        removeSlideEvents();

        //Pagination
        if (params.paginationClickable) {
            removePaginationEvents();
        }

        //Mousewheel
        if (params.mousewheelControl && _this._wheelEvent) {
            unbind(_this.container, _this._wheelEvent, handleMousewheel);
        }

        //Keyboard
        if (params.keyboardControl) {
            unbind(document, 'keydown', handleKeyboardKeys);
        }

        //Stop autoplay
        if (params.autoplay) {
            _this.stopAutoplay();
        }
        // Remove styles
        if (removeStyles) {
            _this.wrapper.removeAttribute('style');
            for (var i = 0; i < _this.slides.length; i++) {
                _this.slides[i].removeAttribute('style');
            }
        }
        // Plugins
        _this.callPlugins('onDestroy');

        // Check jQuery/Zepto data
        if (window.jQuery && window.jQuery(_this.container).data('swiper')) {
            window.jQuery(_this.container).removeData('swiper');
        }
        if (window.Zepto && window.Zepto(_this.container).data('swiper')) {
            window.Zepto(_this.container).removeData('swiper');
        }

        //Destroy variable
        _this = null;
    };

    function addSlideEvents() {
        var bind = _this.h.addEventListener,
            i;

        //Prevent Links Events
        if (params.preventLinks) {
            var links = $$('a', _this.container);
            for (i = 0; i < links.length; i++) {
                bind(links[i], 'click', preventClick);
            }
        }
        //Release Form Elements
        if (params.releaseFormElements) {
            var formElements = $$('input, textarea, select', _this.container);
            for (i = 0; i < formElements.length; i++) {
                bind(formElements[i], _this.touchEvents.touchStart, releaseForms, true);
                if (_this.support.touch && params.simulateTouch) {
                    bind(formElements[i], 'mousedown', releaseForms, true);
                }
            }
        }

        //Slide Clicks & Touches
        if (params.onSlideClick) {
            for (i = 0; i < _this.slides.length; i++) {
                bind(_this.slides[i], 'click', slideClick);
            }
        }
        if (params.onSlideTouch) {
            for (i = 0; i < _this.slides.length; i++) {
                bind(_this.slides[i], _this.touchEvents.touchStart, slideTouch);
            }
        }
    }
    function removeSlideEvents() {
        var unbind = _this.h.removeEventListener,
            i;

        //Slide Clicks & Touches
        if (params.onSlideClick) {
            for (i = 0; i < _this.slides.length; i++) {
                unbind(_this.slides[i], 'click', slideClick);
            }
        }
        if (params.onSlideTouch) {
            for (i = 0; i < _this.slides.length; i++) {
                unbind(_this.slides[i], _this.touchEvents.touchStart, slideTouch);
            }
        }
        //Release Form Elements
        if (params.releaseFormElements) {
            var formElements = $$('input, textarea, select', _this.container);
            for (i = 0; i < formElements.length; i++) {
                unbind(formElements[i], _this.touchEvents.touchStart, releaseForms, true);
                if (_this.support.touch && params.simulateTouch) {
                    unbind(formElements[i], 'mousedown', releaseForms, true);
                }
            }
        }
        //Prevent Links Events
        if (params.preventLinks) {
            var links = $$('a', _this.container);
            for (i = 0; i < links.length; i++) {
                unbind(links[i], 'click', preventClick);
            }
        }
    }
    /*==========================================
     Keyboard Control
     ============================================*/
    function handleKeyboardKeys(e) {
        var kc = e.keyCode || e.charCode;
        if (e.shiftKey || e.altKey || e.ctrlKey || e.metaKey) return;
        if (kc === 37 || kc === 39 || kc === 38 || kc === 40) {
            var inView = false;
            //Check that swiper should be inside of visible area of window
            var swiperOffset = _this.h.getOffset(_this.container);
            var scrollLeft = _this.h.windowScroll().left;
            var scrollTop = _this.h.windowScroll().top;
            var windowWidth = _this.h.windowWidth();
            var windowHeight = _this.h.windowHeight();
            var swiperCoord = [
                [swiperOffset.left, swiperOffset.top],
                [swiperOffset.left + _this.width, swiperOffset.top],
                [swiperOffset.left, swiperOffset.top + _this.height],
                [swiperOffset.left + _this.width, swiperOffset.top + _this.height]
            ];
            for (var i = 0; i < swiperCoord.length; i++) {
                var point = swiperCoord[i];
                if (
                    point[0] >= scrollLeft && point[0] <= scrollLeft + windowWidth &&
                    point[1] >= scrollTop && point[1] <= scrollTop + windowHeight
                ) {
                    inView = true;
                }

            }
            if (!inView) return;
        }
        if (isH) {
            if (kc === 37 || kc === 39) {
                if (e.preventDefault) e.preventDefault();
                else e.returnValue = false;
            }
            if (kc === 39) _this.swipeNext();
            if (kc === 37) _this.swipePrev();
        }
        else {
            if (kc === 38 || kc === 40) {
                if (e.preventDefault) e.preventDefault();
                else e.returnValue = false;
            }
            if (kc === 40) _this.swipeNext();
            if (kc === 38) _this.swipePrev();
        }
    }

    _this.disableKeyboardControl = function () {
        params.keyboardControl = false;
        _this.h.removeEventListener(document, 'keydown', handleKeyboardKeys);
    };

    _this.enableKeyboardControl = function () {
        params.keyboardControl = true;
        _this.h.addEventListener(document, 'keydown', handleKeyboardKeys);
    };

    /*==========================================
     Mousewheel Control
     ============================================*/
    var lastScrollTime = (new Date()).getTime();
    function handleMousewheel(e) {
        var we = _this._wheelEvent;
        var delta = 0;

        //Opera & IE
        if (e.detail) delta = -e.detail;
        //WebKits
        else if (we === 'mousewheel') {
            if (params.mousewheelControlForceToAxis) {
                if (isH) {
                    if (Math.abs(e.wheelDeltaX) > Math.abs(e.wheelDeltaY)) delta = e.wheelDeltaX;
                    else return;
                }
                else {
                    if (Math.abs(e.wheelDeltaY) > Math.abs(e.wheelDeltaX)) delta = e.wheelDeltaY;
                    else return;
                }
            }
            else {
                delta = e.wheelDelta;
            }
        }
        //Old FireFox
        else if (we === 'DOMMouseScroll') delta = -e.detail;
        //New FireFox
        else if (we === 'wheel') {
            if (params.mousewheelControlForceToAxis) {
                if (isH) {
                    if (Math.abs(e.deltaX) > Math.abs(e.deltaY)) delta = -e.deltaX;
                    else return;
                }
                else {
                    if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) delta = -e.deltaY;
                    else return;
                }
            }
            else {
                delta = Math.abs(e.deltaX) > Math.abs(e.deltaY) ? - e.deltaX : - e.deltaY;
            }
        }

        if (!params.freeMode) {
            if ((new Date()).getTime() - lastScrollTime > 60) {
                if (delta < 0) _this.swipeNext();
                else _this.swipePrev();
            }
            lastScrollTime = (new Date()).getTime();

        }
        else {
            //Freemode or scrollContainer:
            var position = _this.getWrapperTranslate() + delta;

            if (position > 0) position = 0;
            if (position < -maxWrapperPosition()) position = -maxWrapperPosition();

            _this.setWrapperTransition(0);
            _this.setWrapperTranslate(position);
            _this.updateActiveSlide(position);

            // Return page scroll on edge positions
            if (position === 0 || position === -maxWrapperPosition()) return;
        }
        if (params.autoplay) _this.stopAutoplay(true);

        if (e.preventDefault) e.preventDefault();
        else e.returnValue = false;
        return false;
    }
    _this.disableMousewheelControl = function () {
        if (!_this._wheelEvent) return false;
        params.mousewheelControl = false;
        _this.h.removeEventListener(_this.container, _this._wheelEvent, handleMousewheel);
        return true;
    };

    _this.enableMousewheelControl = function () {
        if (!_this._wheelEvent) return false;
        params.mousewheelControl = true;
        _this.h.addEventListener(_this.container, _this._wheelEvent, handleMousewheel);
        return true;
    };

    /*=========================
     Grab Cursor
     ===========================*/
    if (params.grabCursor) {
        var containerStyle = _this.container.style;
        containerStyle.cursor = 'move';
        containerStyle.cursor = 'grab';
        containerStyle.cursor = '-moz-grab';
        containerStyle.cursor = '-webkit-grab';
    }

    /*=========================
     Slides Events Handlers
     ===========================*/

    _this.allowSlideClick = true;
    function slideClick(event) {
        if (_this.allowSlideClick) {
            setClickedSlide(event);
            _this.fireCallback(params.onSlideClick, _this, event);
        }
    }

    function slideTouch(event) {
        setClickedSlide(event);
        _this.fireCallback(params.onSlideTouch, _this, event);
    }

    function setClickedSlide(event) {

        // IE 6-8 support
        if (!event.currentTarget) {
            var element = event.srcElement;
            do {
                if (element.className.indexOf(params.slideClass) > -1) {
                    break;
                }
                element = element.parentNode;
            } while (element);
            _this.clickedSlide = element;
        }
        else {
            _this.clickedSlide = event.currentTarget;
        }

        _this.clickedSlideIndex     = _this.slides.indexOf(_this.clickedSlide);
        _this.clickedSlideLoopIndex = _this.clickedSlideIndex - (_this.loopedSlides || 0);
    }

    _this.allowLinks = true;
    function preventClick(e) {
        if (!_this.allowLinks) {
            if (e.preventDefault) e.preventDefault();
            else e.returnValue = false;
            if (params.preventLinksPropagation && 'stopPropagation' in e) {
                e.stopPropagation();
            }
            return false;
        }
    }
    function releaseForms(e) {
        if (e.stopPropagation) e.stopPropagation();
        else e.returnValue = false;
        return false;

    }

    /*==================================================
     Event Handlers
     ====================================================*/
    var isTouchEvent = false;
    var allowThresholdMove;
    var allowMomentumBounce = true;
    function onTouchStart(event) {
        if (params.preventLinks) _this.allowLinks = true;
        //Exit if slider is already was touched
        if (_this.isTouched || params.onlyExternal) {
            return false;
        }

        // Blur active elements
        var eventTarget = event.target || event.srcElement;
        if (document.activeElement && document.activeElement !== document.body) {
            if (document.activeElement !== eventTarget) document.activeElement.blur();
        }

        // Form tag names
        var formTagNames = ('input select textarea').split(' ');

        // Check for no swiping
        if (params.noSwiping && (eventTarget) && noSwipingSlide(eventTarget)) return false;
        allowMomentumBounce = false;
        //Check For Nested Swipers
        _this.isTouched = true;
        isTouchEvent = event.type === 'touchstart';

        // prevent user enter with right and the swiper move (needs isTouchEvent)
        if (!isTouchEvent && 'which' in event && event.which === 3) {
            _this.isTouched = false;
            return false;
        }

        if (!isTouchEvent || event.targetTouches.length === 1) {
            _this.callPlugins('onTouchStartBegin');
            if (!isTouchEvent && !_this.isAndroid && formTagNames.indexOf(eventTarget.tagName.toLowerCase()) < 0) {

                if (event.preventDefault) event.preventDefault();
                else event.returnValue = false;
            }

            var pageX = isTouchEvent ? event.targetTouches[0].pageX : (event.pageX || event.clientX);
            var pageY = isTouchEvent ? event.targetTouches[0].pageY : (event.pageY || event.clientY);

            //Start Touches to check the scrolling
            _this.touches.startX = _this.touches.currentX = pageX;
            _this.touches.startY = _this.touches.currentY = pageY;

            _this.touches.start = _this.touches.current = isH ? pageX : pageY;

            //Set Transition Time to 0
            _this.setWrapperTransition(0);

            //Get Start Translate Position
            _this.positions.start = _this.positions.current = _this.getWrapperTranslate();

            //Set Transform
            _this.setWrapperTranslate(_this.positions.start);

            //TouchStartTime
            _this.times.start = (new Date()).getTime();

            //Unset Scrolling
            isScrolling = undefined;

            //Set Treshold
            if (params.moveStartThreshold > 0) {
                allowThresholdMove = false;
            }

            //CallBack
            if (params.onTouchStart) _this.fireCallback(params.onTouchStart, _this, event);
            _this.callPlugins('onTouchStartEnd');

        }
    }
    var velocityPrevPosition, velocityPrevTime;
    function onTouchMove(event) {
        // If slider is not touched - exit
        if (!_this.isTouched || params.onlyExternal) return;
        if (isTouchEvent && event.type === 'mousemove') return;

        var pageX = isTouchEvent ? event.targetTouches[0].pageX : (event.pageX || event.clientX);
        var pageY = isTouchEvent ? event.targetTouches[0].pageY : (event.pageY || event.clientY);

        //check for scrolling
        if (typeof isScrolling === 'undefined' && isH) {
            isScrolling = !!(isScrolling || Math.abs(pageY - _this.touches.startY) > Math.abs(pageX - _this.touches.startX));
        }
        if (typeof isScrolling === 'undefined' && !isH) {
            isScrolling = !!(isScrolling || Math.abs(pageY - _this.touches.startY) < Math.abs(pageX - _this.touches.startX));
        }
        if (isScrolling) {
            _this.isTouched = false;
            return;
        }

        // One way swipes
        if (isH) {
            if ((!params.swipeToNext && pageX < _this.touches.startX) || ((!params.swipeToPrev && pageX > _this.touches.startX))) {
                return;
            }
        }
        else {
            if ((!params.swipeToNext && pageY < _this.touches.startY) || ((!params.swipeToPrev && pageY > _this.touches.startY))) {
                return;
            }
        }

        //Check For Nested Swipers
        if (event.assignedToSwiper) {
            _this.isTouched = false;
            return;
        }
        event.assignedToSwiper = true;

        //Block inner links
        if (params.preventLinks) {
            _this.allowLinks = false;
        }
        if (params.onSlideClick) {
            _this.allowSlideClick = false;
        }

        //Stop AutoPlay if exist
        if (params.autoplay) {
            _this.stopAutoplay(true);
        }
        if (!isTouchEvent || event.touches.length === 1) {

            //Moved Flag
            if (!_this.isMoved) {
                _this.callPlugins('onTouchMoveStart');

                if (params.loop) {
                    _this.fixLoop();
                    _this.positions.start = _this.getWrapperTranslate();
                }
                if (params.onTouchMoveStart) _this.fireCallback(params.onTouchMoveStart, _this);
            }
            _this.isMoved = true;

            // cancel event
            if (event.preventDefault) event.preventDefault();
            else event.returnValue = false;

            _this.touches.current = isH ? pageX : pageY;

            _this.positions.current = (_this.touches.current - _this.touches.start) * params.touchRatio + _this.positions.start;

            //Resistance Callbacks
            if (_this.positions.current > 0 && params.onResistanceBefore) {
                _this.fireCallback(params.onResistanceBefore, _this, _this.positions.current);
            }
            if (_this.positions.current < -maxWrapperPosition() && params.onResistanceAfter) {
                _this.fireCallback(params.onResistanceAfter, _this, Math.abs(_this.positions.current + maxWrapperPosition()));
            }
            //Resistance
            if (params.resistance && params.resistance !== '100%') {
                var resistance;
                //Resistance for Negative-Back sliding
                if (_this.positions.current > 0) {
                    resistance = 1 - _this.positions.current / containerSize / 2;
                    if (resistance < 0.5)
                        _this.positions.current = (containerSize / 2);
                    else
                        _this.positions.current = _this.positions.current * resistance;
                }
                //Resistance for After-End Sliding
                if (_this.positions.current < -maxWrapperPosition()) {

                    var diff = (_this.touches.current - _this.touches.start) * params.touchRatio + (maxWrapperPosition() + _this.positions.start);
                    resistance = (containerSize + diff) / (containerSize);
                    var newPos = _this.positions.current - diff * (1 - resistance) / 2;
                    var stopPos = -maxWrapperPosition() - containerSize / 2;

                    if (newPos < stopPos || resistance <= 0)
                        _this.positions.current = stopPos;
                    else
                        _this.positions.current = newPos;
                }
            }
            if (params.resistance && params.resistance === '100%') {
                //Resistance for Negative-Back sliding
                if (_this.positions.current > 0 && !(params.freeMode && !params.freeModeFluid)) {
                    _this.positions.current = 0;
                }
                //Resistance for After-End Sliding
                if (_this.positions.current < -maxWrapperPosition() && !(params.freeMode && !params.freeModeFluid)) {
                    _this.positions.current = -maxWrapperPosition();
                }
            }
            //Move Slides
            if (!params.followFinger) return;

            if (!params.moveStartThreshold) {
                _this.setWrapperTranslate(_this.positions.current);
            }
            else {
                if (Math.abs(_this.touches.current - _this.touches.start) > params.moveStartThreshold || allowThresholdMove) {
                    if (!allowThresholdMove) {
                        allowThresholdMove = true;
                        _this.touches.start = _this.touches.current;
                        return;
                    }
                    _this.setWrapperTranslate(_this.positions.current);
                }
                else {
                    _this.positions.current = _this.positions.start;
                }
            }

            if (params.freeMode || params.watchActiveIndex) {
                _this.updateActiveSlide(_this.positions.current);
            }

            //Grab Cursor
            if (params.grabCursor) {
                _this.container.style.cursor = 'move';
                _this.container.style.cursor = 'grabbing';
                _this.container.style.cursor = '-moz-grabbin';
                _this.container.style.cursor = '-webkit-grabbing';
            }
            //Velocity
            if (!velocityPrevPosition) velocityPrevPosition = _this.touches.current;
            if (!velocityPrevTime) velocityPrevTime = (new Date()).getTime();
            _this.velocity = (_this.touches.current - velocityPrevPosition) / ((new Date()).getTime() - velocityPrevTime) / 2;
            if (Math.abs(_this.touches.current - velocityPrevPosition) < 2) _this.velocity = 0;
            velocityPrevPosition = _this.touches.current;
            velocityPrevTime = (new Date()).getTime();
            //Callbacks
            _this.callPlugins('onTouchMoveEnd');
            if (params.onTouchMove) _this.fireCallback(params.onTouchMove, _this, event);

            return false;
        }
    }
    function onTouchEnd(event) {
        //Check For scrolling
        if (isScrolling) {
            _this.swipeReset();
        }
        // If slider is not touched exit
        if (params.onlyExternal || !_this.isTouched) return;
        _this.isTouched = false;

        //Return Grab Cursor
        if (params.grabCursor) {
            _this.container.style.cursor = 'move';
            _this.container.style.cursor = 'grab';
            _this.container.style.cursor = '-moz-grab';
            _this.container.style.cursor = '-webkit-grab';
        }

        //Check for Current Position
        if (!_this.positions.current && _this.positions.current !== 0) {
            _this.positions.current = _this.positions.start;
        }

        //For case if slider touched but not moved
        if (params.followFinger) {
            _this.setWrapperTranslate(_this.positions.current);
        }

        // TouchEndTime
        _this.times.end = (new Date()).getTime();

        //Difference
        _this.touches.diff = _this.touches.current - _this.touches.start;
        _this.touches.abs = Math.abs(_this.touches.diff);

        _this.positions.diff = _this.positions.current - _this.positions.start;
        _this.positions.abs = Math.abs(_this.positions.diff);

        var diff = _this.positions.diff;
        var diffAbs = _this.positions.abs;
        var timeDiff = _this.times.end - _this.times.start;

        if (diffAbs < 5 && (timeDiff) < 300 && _this.allowLinks === false) {
            if (!params.freeMode && diffAbs !== 0) _this.swipeReset();
            //Release inner links
            if (params.preventLinks) {
                _this.allowLinks = true;
            }
            if (params.onSlideClick) {
                _this.allowSlideClick = true;
            }
        }

        setTimeout(function () {
            //Release inner links
            if (typeof _this === 'undefined' || _this === null) return;
            if (params.preventLinks) {
                _this.allowLinks = true;
            }
            if (params.onSlideClick) {
                _this.allowSlideClick = true;
            }
        }, 100);

        var maxPosition = maxWrapperPosition();

        //Not moved or Prevent Negative Back Sliding/After-End Sliding
        if (!_this.isMoved && params.freeMode) {
            _this.isMoved = false;
            if (params.onTouchEnd) _this.fireCallback(params.onTouchEnd, _this, event);
            _this.callPlugins('onTouchEnd');
            return;
        }
        if (!_this.isMoved || _this.positions.current > 0 || _this.positions.current < -maxPosition) {
            _this.swipeReset();
            if (params.onTouchEnd) _this.fireCallback(params.onTouchEnd, _this, event);
            _this.callPlugins('onTouchEnd');
            return;
        }

        _this.isMoved = false;

        //Free Mode
        if (params.freeMode) {
            if (params.freeModeFluid) {
                var momentumDuration = 1000 * params.momentumRatio;
                var momentumDistance = _this.velocity * momentumDuration;
                var newPosition = _this.positions.current + momentumDistance;
                var doBounce = false;
                var afterBouncePosition;
                var bounceAmount = Math.abs(_this.velocity) * 20 * params.momentumBounceRatio;
                if (newPosition < -maxPosition) {
                    if (params.momentumBounce && _this.support.transitions) {
                        if (newPosition + maxPosition < -bounceAmount) newPosition = -maxPosition - bounceAmount;
                        afterBouncePosition = -maxPosition;
                        doBounce = true;
                        allowMomentumBounce = true;
                    }
                    else newPosition = -maxPosition;
                }
                if (newPosition > 0) {
                    if (params.momentumBounce && _this.support.transitions) {
                        if (newPosition > bounceAmount) newPosition = bounceAmount;
                        afterBouncePosition = 0;
                        doBounce = true;
                        allowMomentumBounce = true;
                    }
                    else newPosition = 0;
                }
                //Fix duration
                if (_this.velocity !== 0) momentumDuration = Math.abs((newPosition - _this.positions.current) / _this.velocity);

                _this.setWrapperTranslate(newPosition);

                _this.setWrapperTransition(momentumDuration);

                if (params.momentumBounce && doBounce) {
                    _this.wrapperTransitionEnd(function () {
                        if (!allowMomentumBounce) return;
                        if (params.onMomentumBounce) _this.fireCallback(params.onMomentumBounce, _this);
                        _this.callPlugins('onMomentumBounce');

                        _this.setWrapperTranslate(afterBouncePosition);
                        _this.setWrapperTransition(300);
                    });
                }

                _this.updateActiveSlide(newPosition);
            }
            if (!params.freeModeFluid || timeDiff >= 300) _this.updateActiveSlide(_this.positions.current);

            if (params.onTouchEnd) _this.fireCallback(params.onTouchEnd, _this, event);
            _this.callPlugins('onTouchEnd');
            return;
        }

        //Direction
        direction = diff < 0 ? 'toNext' : 'toPrev';

        //Short Touches
        if (direction === 'toNext' && (timeDiff <= 300)) {
            if (diffAbs < 30 || !params.shortSwipes) _this.swipeReset();
            else _this.swipeNext(true, true);
        }

        if (direction === 'toPrev' && (timeDiff <= 300)) {
            if (diffAbs < 30 || !params.shortSwipes) _this.swipeReset();
            else _this.swipePrev(true, true);
        }

        //Long Touches
        var targetSlideSize = 0;
        if (params.slidesPerView === 'auto') {
            //Define current slide's width
            var currentPosition = Math.abs(_this.getWrapperTranslate());
            var slidesOffset = 0;
            var _slideSize;
            for (var i = 0; i < _this.slides.length; i++) {
                _slideSize = isH ? _this.slides[i].getWidth(true, params.roundLengths) : _this.slides[i].getHeight(true, params.roundLengths);
                slidesOffset += _slideSize;
                if (slidesOffset > currentPosition) {
                    targetSlideSize = _slideSize;
                    break;
                }
            }
            if (targetSlideSize > containerSize) targetSlideSize = containerSize;
        }
        else {
            targetSlideSize = slideSize * params.slidesPerView;
        }
        if (direction === 'toNext' && (timeDiff > 300)) {
            if (diffAbs >= targetSlideSize * params.longSwipesRatio) {
                _this.swipeNext(true, true);
            }
            else {
                _this.swipeReset();
            }
        }
        if (direction === 'toPrev' && (timeDiff > 300)) {
            if (diffAbs >= targetSlideSize * params.longSwipesRatio) {
                _this.swipePrev(true, true);
            }
            else {
                _this.swipeReset();
            }
        }
        if (params.onTouchEnd) _this.fireCallback(params.onTouchEnd, _this, event);
        _this.callPlugins('onTouchEnd');
    }


    /*==================================================
     noSwiping Bubble Check by Isaac Strack
     ====================================================*/
    function hasClass(el, classname) {
        return el && el.getAttribute('class') && el.getAttribute('class').indexOf(classname) > -1;
    }
    function noSwipingSlide(el) {
        /*This function is specifically designed to check the parent elements for the noSwiping class, up to the wrapper.
         We need to check parents because while onTouchStart bubbles, _this.isTouched is checked in onTouchStart, which stops the bubbling.
         So, if a text box, for example, is the initial target, and the parent slide container has the noSwiping class, the _this.isTouched
         check will never find it, and what was supposed to be noSwiping is able to be swiped.
         This function will iterate up and check for the noSwiping class in parents, up through the wrapperClass.*/

        // First we create a truthy variable, which is that swiping is allowd (noSwiping = false)
        var noSwiping = false;

        // Now we iterate up (parentElements) until we reach the node with the wrapperClass.
        do {

            // Each time, we check to see if there's a 'swiper-no-swiping' class (noSwipingClass).
            if (hasClass(el, params.noSwipingClass))
            {
                noSwiping = true; // If there is, we set noSwiping = true;
            }

            el = el.parentElement;  // now we iterate up (parent node)

        } while (!noSwiping && el.parentElement && !hasClass(el, params.wrapperClass)); // also include el.parentElement truthy, just in case.

        // because we didn't check the wrapper itself, we do so now, if noSwiping is false:
        if (!noSwiping && hasClass(el, params.wrapperClass) && hasClass(el, params.noSwipingClass))
            noSwiping = true; // if the wrapper has the noSwipingClass, we set noSwiping = true;

        return noSwiping;
    }

    function addClassToHtmlString(klass, outerHtml) {
        var par = document.createElement('div');
        var child;

        par.innerHTML = outerHtml;
        child = par.firstChild;
        child.className += ' ' + klass;

        return child.outerHTML;
    }


    /*==================================================
     Swipe Functions
     ====================================================*/
    _this.swipeNext = function (runCallbacks, internal) {
        if (typeof runCallbacks === 'undefined') runCallbacks = true;
        if (!internal && params.loop) _this.fixLoop();
        if (!internal && params.autoplay) _this.stopAutoplay(true);
        _this.callPlugins('onSwipeNext');
        var currentPosition = _this.getWrapperTranslate().toFixed(2);
        var newPosition = currentPosition;
        if (params.slidesPerView === 'auto') {
            for (var i = 0; i < _this.snapGrid.length; i++) {
                if (-currentPosition >= _this.snapGrid[i].toFixed(2) && -currentPosition < _this.snapGrid[i + 1].toFixed(2)) {
                    newPosition = -_this.snapGrid[i + 1];
                    break;
                }
            }
        }
        else {
            var groupSize = slideSize * params.slidesPerGroup;
            newPosition = -(Math.floor(Math.abs(currentPosition) / Math.floor(groupSize)) * groupSize + groupSize);
        }
        if (newPosition < -maxWrapperPosition()) {
            newPosition = -maxWrapperPosition();
        }
        if (newPosition === currentPosition) return false;
        swipeToPosition(newPosition, 'next', {runCallbacks: runCallbacks});
        return true;
    };
    _this.swipePrev = function (runCallbacks, internal) {
        if (typeof runCallbacks === 'undefined') runCallbacks = true;
        if (!internal && params.loop) _this.fixLoop();
        if (!internal && params.autoplay) _this.stopAutoplay(true);
        _this.callPlugins('onSwipePrev');

        var currentPosition = Math.ceil(_this.getWrapperTranslate());
        var newPosition;
        if (params.slidesPerView === 'auto') {
            newPosition = 0;
            for (var i = 1; i < _this.snapGrid.length; i++) {
                if (-currentPosition === _this.snapGrid[i]) {
                    newPosition = -_this.snapGrid[i - 1];
                    break;
                }
                if (-currentPosition > _this.snapGrid[i] && -currentPosition < _this.snapGrid[i + 1]) {
                    newPosition = -_this.snapGrid[i];
                    break;
                }
            }
        }
        else {
            var groupSize = slideSize * params.slidesPerGroup;
            newPosition = -(Math.ceil(-currentPosition / groupSize) - 1) * groupSize;
        }

        if (newPosition > 0) newPosition = 0;

        if (newPosition === currentPosition) return false;
        swipeToPosition(newPosition, 'prev', {runCallbacks: runCallbacks});
        return true;

    };
    _this.swipeReset = function (runCallbacks) {
        if (typeof runCallbacks === 'undefined') runCallbacks = true;
        _this.callPlugins('onSwipeReset');
        var currentPosition = _this.getWrapperTranslate();
        var groupSize = slideSize * params.slidesPerGroup;
        var newPosition;
        var maxPosition = -maxWrapperPosition();
        if (params.slidesPerView === 'auto') {
            newPosition = 0;
            for (var i = 0; i < _this.snapGrid.length; i++) {
                if (-currentPosition === _this.snapGrid[i]) return;
                if (-currentPosition >= _this.snapGrid[i] && -currentPosition < _this.snapGrid[i + 1]) {
                    if (_this.positions.diff > 0) newPosition = -_this.snapGrid[i + 1];
                    else newPosition = -_this.snapGrid[i];
                    break;
                }
            }
            if (-currentPosition >= _this.snapGrid[_this.snapGrid.length - 1]) newPosition = -_this.snapGrid[_this.snapGrid.length - 1];
            if (currentPosition <= -maxWrapperPosition()) newPosition = -maxWrapperPosition();
        }
        else {
            newPosition = currentPosition < 0 ? Math.round(currentPosition / groupSize) * groupSize : 0;
            if (currentPosition <= -maxWrapperPosition()) newPosition = -maxWrapperPosition();
        }
        if (params.scrollContainer)  {
            newPosition = currentPosition < 0 ? currentPosition : 0;
        }
        if (newPosition < -maxWrapperPosition()) {
            newPosition = -maxWrapperPosition();
        }
        if (params.scrollContainer && (containerSize > slideSize)) {
            newPosition = 0;
        }

        if (newPosition === currentPosition) return false;

        swipeToPosition(newPosition, 'reset', {runCallbacks: runCallbacks});
        return true;
    };

    _this.swipeTo = function (index, speed, runCallbacks) {
        index = parseInt(index, 10);
        _this.callPlugins('onSwipeTo', {index: index, speed: speed});
        if (params.loop) index = index + _this.loopedSlides;
        var currentPosition = _this.getWrapperTranslate();
        if (!isFinite(index) || index > (_this.slides.length - 1) || index < 0) return;
        var newPosition;
        if (params.slidesPerView === 'auto') {
            newPosition = -_this.slidesGrid[index];
        }
        else {
            newPosition = -index * slideSize;
        }
        if (newPosition < - maxWrapperPosition()) {
            newPosition = - maxWrapperPosition();
        }

        if (newPosition === currentPosition) return false;

        if (typeof runCallbacks === 'undefined') runCallbacks = true;
        swipeToPosition(newPosition, 'to', {index: index, speed: speed, runCallbacks: runCallbacks});
        return true;
    };

    function swipeToPosition(newPosition, action, toOptions) {
        var speed = (action === 'to' && toOptions.speed >= 0) ? toOptions.speed : params.speed;
        var timeOld = + new Date();

        function anim() {
            var timeNew = + new Date();
            var time = timeNew - timeOld;
            currentPosition += animationStep * time / (1000 / 60);
            condition = direction === 'toNext' ? currentPosition > newPosition : currentPosition < newPosition;
            if (condition) {
                _this.setWrapperTranslate(Math.ceil(currentPosition));
                _this._DOMAnimating = true;
                window.setTimeout(function () {
                    anim();
                }, 1000 / 60);
            }
            else {
                if (params.onSlideChangeEnd) {
                    if (action === 'to') {
                        if (toOptions.runCallbacks === true) _this.fireCallback(params.onSlideChangeEnd, _this, direction);
                    }
                    else {
                        _this.fireCallback(params.onSlideChangeEnd, _this, direction);
                    }

                }
                _this.setWrapperTranslate(newPosition);
                _this._DOMAnimating = false;
            }
        }

        if (_this.support.transitions || !params.DOMAnimation) {
            _this.setWrapperTranslate(newPosition);
            _this.setWrapperTransition(speed);
        }
        else {
            //Try the DOM animation
            var currentPosition = _this.getWrapperTranslate();
            var animationStep = Math.ceil((newPosition - currentPosition) / speed * (1000 / 60));
            var direction = currentPosition > newPosition ? 'toNext' : 'toPrev';
            var condition = direction === 'toNext' ? currentPosition > newPosition : currentPosition < newPosition;
            if (_this._DOMAnimating) return;

            anim();
        }

        //Update Active Slide Index
        _this.updateActiveSlide(newPosition);

        //Callbacks
        if (params.onSlideNext && action === 'next' && toOptions.runCallbacks === true) {
            _this.fireCallback(params.onSlideNext, _this, newPosition);
        }
        if (params.onSlidePrev && action === 'prev' && toOptions.runCallbacks === true) {
            _this.fireCallback(params.onSlidePrev, _this, newPosition);
        }
        //'Reset' Callback
        if (params.onSlideReset && action === 'reset' && toOptions.runCallbacks === true) {
            _this.fireCallback(params.onSlideReset, _this, newPosition);
        }

        //'Next', 'Prev' and 'To' Callbacks
        if ((action === 'next' || action === 'prev' || action === 'to') && toOptions.runCallbacks === true)
            slideChangeCallbacks(action);
    }
    /*==================================================
     Transition Callbacks
     ====================================================*/
    //Prevent Multiple Callbacks
    _this._queueStartCallbacks = false;
    _this._queueEndCallbacks = false;
    function slideChangeCallbacks(direction) {
        //Transition Start Callback
        _this.callPlugins('onSlideChangeStart');
        if (params.onSlideChangeStart) {
            if (params.queueStartCallbacks && _this.support.transitions) {
                if (_this._queueStartCallbacks) return;
                _this._queueStartCallbacks = true;
                _this.fireCallback(params.onSlideChangeStart, _this, direction);
                _this.wrapperTransitionEnd(function () {
                    _this._queueStartCallbacks = false;
                });
            }
            else _this.fireCallback(params.onSlideChangeStart, _this, direction);
        }
        //Transition End Callback
        if (params.onSlideChangeEnd) {
            if (_this.support.transitions) {
                if (params.queueEndCallbacks) {
                    if (_this._queueEndCallbacks) return;
                    _this._queueEndCallbacks = true;
                    _this.wrapperTransitionEnd(function (swiper) {
                        _this.fireCallback(params.onSlideChangeEnd, swiper, direction);
                    });
                }
                else {
                    _this.wrapperTransitionEnd(function (swiper) {
                        _this.fireCallback(params.onSlideChangeEnd, swiper, direction);
                    });
                }
            }
            else {
                if (!params.DOMAnimation) {
                    setTimeout(function () {
                        _this.fireCallback(params.onSlideChangeEnd, _this, direction);
                    }, 10);
                }
            }
        }
    }

    /*==================================================
     Update Active Slide Index
     ====================================================*/
    _this.updateActiveSlide = function (position) {
        if (!_this.initialized) return;
        if (_this.slides.length === 0) return;
        _this.previousIndex = _this.activeIndex;
        if (typeof position === 'undefined') position = _this.getWrapperTranslate();
        if (position > 0) position = 0;
        var i;
        if (params.slidesPerView === 'auto') {
            var slidesOffset = 0;
            _this.activeIndex = _this.slidesGrid.indexOf(-position);
            if (_this.activeIndex < 0) {
                for (i = 0; i < _this.slidesGrid.length - 1; i++) {
                    if (-position > _this.slidesGrid[i] && -position < _this.slidesGrid[i + 1]) {
                        break;
                    }
                }
                var leftDistance = Math.abs(_this.slidesGrid[i] + position);
                var rightDistance = Math.abs(_this.slidesGrid[i + 1] + position);
                if (leftDistance <= rightDistance) _this.activeIndex = i;
                else _this.activeIndex = i + 1;
            }
        }
        else {
            _this.activeIndex = Math[params.visibilityFullFit ? 'ceil' : 'round'](-position / slideSize);
        }

        if (_this.activeIndex === _this.slides.length) _this.activeIndex = _this.slides.length - 1;
        if (_this.activeIndex < 0) _this.activeIndex = 0;

        // Check for slide
        if (!_this.slides[_this.activeIndex]) return;

        // Calc Visible slides
        _this.calcVisibleSlides(position);

        // Mark visible and active slides with additonal classes
        if (_this.support.classList) {
            var slide;
            for (i = 0; i < _this.slides.length; i++) {
                slide = _this.slides[i];
                slide.classList.remove(params.slideActiveClass);
                if (_this.visibleSlides.indexOf(slide) >= 0) {
                    slide.classList.add(params.slideVisibleClass);
                } else {
                    slide.classList.remove(params.slideVisibleClass);
                }
            }
            _this.slides[_this.activeIndex].classList.add(params.slideActiveClass);
        } else {
            var activeClassRegexp = new RegExp('\\s*' + params.slideActiveClass);
            var inViewClassRegexp = new RegExp('\\s*' + params.slideVisibleClass);

            for (i = 0; i < _this.slides.length; i++) {
                _this.slides[i].className = _this.slides[i].className.replace(activeClassRegexp, '').replace(inViewClassRegexp, '');
                if (_this.visibleSlides.indexOf(_this.slides[i]) >= 0) {
                    _this.slides[i].className += ' ' + params.slideVisibleClass;
                }
            }
            _this.slides[_this.activeIndex].className += ' ' + params.slideActiveClass;
        }

        //Update loop index
        if (params.loop) {
            var ls = _this.loopedSlides;
            _this.activeLoopIndex = _this.activeIndex - ls;
            if (_this.activeLoopIndex >= _this.slides.length - ls * 2) {
                _this.activeLoopIndex = _this.slides.length - ls * 2 - _this.activeLoopIndex;
            }
            if (_this.activeLoopIndex < 0) {
                _this.activeLoopIndex = _this.slides.length - ls * 2 + _this.activeLoopIndex;
            }
            if (_this.activeLoopIndex < 0) _this.activeLoopIndex = 0;
        }
        else {
            _this.activeLoopIndex = _this.activeIndex;
        }
        //Update Pagination
        if (params.pagination) {
            _this.updatePagination(position);
        }
    };
    /*==================================================
     Pagination
     ====================================================*/
    _this.createPagination = function (firstInit) {
        if (params.paginationClickable && _this.paginationButtons) {
            removePaginationEvents();
        }
        _this.paginationContainer = params.pagination.nodeType ? params.pagination : $$(params.pagination)[0];
        if (params.createPagination) {
            var paginationHTML = '';
            var numOfSlides = _this.slides.length;
            var numOfButtons = numOfSlides;
            if (params.loop) numOfButtons -= _this.loopedSlides * 2;
            for (var i = 0; i < numOfButtons; i++) {
                paginationHTML += '<' + params.paginationElement + ' class="' + params.paginationElementClass + '"></' + params.paginationElement + '>';
            }
            _this.paginationContainer.innerHTML = paginationHTML;
        }
        _this.paginationButtons = $$('.' + params.paginationElementClass, _this.paginationContainer);
        if (!firstInit) _this.updatePagination();
        _this.callPlugins('onCreatePagination');
        if (params.paginationClickable) {
            addPaginationEvents();
        }
    };
    function removePaginationEvents() {
        var pagers = _this.paginationButtons;
        if (pagers) {
            for (var i = 0; i < pagers.length; i++) {
                _this.h.removeEventListener(pagers[i], 'click', paginationClick);
            }
        }
    }
    function addPaginationEvents() {
        var pagers = _this.paginationButtons;
        if (pagers) {
            for (var i = 0; i < pagers.length; i++) {
                _this.h.addEventListener(pagers[i], 'click', paginationClick);
            }
        }
    }
    function paginationClick(e) {
        var index;
        var target = e.target || e.srcElement;
        var pagers = _this.paginationButtons;
        for (var i = 0; i < pagers.length; i++) {
            if (target === pagers[i]) index = i;
        }
        if (params.autoplay) _this.stopAutoplay(true);
        _this.swipeTo(index);
    }
    _this.updatePagination = function (position) {
        if (!params.pagination) return;
        if (_this.slides.length < 1) return;
        var activePagers = $$('.' + params.paginationActiveClass, _this.paginationContainer);
        if (!activePagers) return;

        //Reset all Buttons' class to not active
        var pagers = _this.paginationButtons;
        if (pagers.length === 0) return;
        for (var i = 0; i < pagers.length; i++) {
            pagers[i].className = params.paginationElementClass;
        }

        var indexOffset = params.loop ? _this.loopedSlides : 0;
        if (params.paginationAsRange) {
            if (!_this.visibleSlides) _this.calcVisibleSlides(position);
            //Get Visible Indexes
            var visibleIndexes = [];
            var j; // lopp index - avoid JSHint W004 / W038
            for (j = 0; j < _this.visibleSlides.length; j++) {
                var visIndex = _this.slides.indexOf(_this.visibleSlides[j]) - indexOffset;

                if (params.loop && visIndex < 0) {
                    visIndex = _this.slides.length - _this.loopedSlides * 2 + visIndex;
                }
                if (params.loop && visIndex >= _this.slides.length - _this.loopedSlides * 2) {
                    visIndex = _this.slides.length - _this.loopedSlides * 2 - visIndex;
                    visIndex = Math.abs(visIndex);
                }
                visibleIndexes.push(visIndex);
            }

            for (j = 0; j < visibleIndexes.length; j++) {
                if (pagers[visibleIndexes[j]]) pagers[visibleIndexes[j]].className += ' ' + params.paginationVisibleClass;
            }

            if (params.loop) {
                if (pagers[_this.activeLoopIndex] !== undefined) {
                    pagers[_this.activeLoopIndex].className += ' ' + params.paginationActiveClass;
                }
            }
            else {
                if (pagers[_this.activeIndex]) pagers[_this.activeIndex].className += ' ' + params.paginationActiveClass;
            }
        }
        else {
            if (params.loop) {
                if (pagers[_this.activeLoopIndex]) pagers[_this.activeLoopIndex].className += ' ' + params.paginationActiveClass + ' ' + params.paginationVisibleClass;
            }
            else {
                if (pagers[_this.activeIndex]) pagers[_this.activeIndex].className += ' ' + params.paginationActiveClass + ' ' + params.paginationVisibleClass;
            }
        }
    };
    _this.calcVisibleSlides = function (position) {
        var visibleSlides = [];
        var _slideLeft = 0, _slideSize = 0, _slideRight = 0;
        if (isH && _this.wrapperLeft > 0) position = position + _this.wrapperLeft;
        if (!isH && _this.wrapperTop > 0) position = position + _this.wrapperTop;

        for (var i = 0; i < _this.slides.length; i++) {
            _slideLeft += _slideSize;
            if (params.slidesPerView === 'auto')
                _slideSize  = isH ? _this.h.getWidth(_this.slides[i], true, params.roundLengths) : _this.h.getHeight(_this.slides[i], true, params.roundLengths);
            else _slideSize = slideSize;

            _slideRight = _slideLeft + _slideSize;
            var isVisibile = false;
            if (params.visibilityFullFit) {
                if (_slideLeft >= -position && _slideRight <= -position + containerSize) isVisibile = true;
                if (_slideLeft <= -position && _slideRight >= -position + containerSize) isVisibile = true;
            }
            else {
                if (_slideRight > -position && _slideRight <= ((-position + containerSize))) isVisibile = true;
                if (_slideLeft >= -position && _slideLeft < ((-position + containerSize))) isVisibile = true;
                if (_slideLeft < -position && _slideRight > ((-position + containerSize))) isVisibile = true;
            }

            if (isVisibile) visibleSlides.push(_this.slides[i]);

        }
        if (visibleSlides.length === 0) visibleSlides = [_this.slides[_this.activeIndex]];

        _this.visibleSlides = visibleSlides;
    };

    /*==========================================
     Autoplay
     ============================================*/
    var autoplayTimeoutId, autoplayIntervalId;
    _this.startAutoplay = function () {
        if (_this.support.transitions) {
            if (typeof autoplayTimeoutId !== 'undefined') return false;
            if (!params.autoplay) return;
            _this.callPlugins('onAutoplayStart');
            if (params.onAutoplayStart) _this.fireCallback(params.onAutoplayStart, _this);
            autoplay();
        }
        else {
            if (typeof autoplayIntervalId !== 'undefined') return false;
            if (!params.autoplay) return;
            _this.callPlugins('onAutoplayStart');
            if (params.onAutoplayStart) _this.fireCallback(params.onAutoplayStart, _this);
            autoplayIntervalId = setInterval(function () {
                if (params.loop) {
                    _this.fixLoop();
                    _this.swipeNext(true, true);
                }
                else if (!_this.swipeNext(true, true)) {
                    if (!params.autoplayStopOnLast) _this.swipeTo(0);
                    else {
                        clearInterval(autoplayIntervalId);
                        autoplayIntervalId = undefined;
                    }
                }
            }, params.autoplay);
        }
    };
    _this.stopAutoplay = function (internal) {
        if (_this.support.transitions) {
            if (!autoplayTimeoutId) return;
            if (autoplayTimeoutId) clearTimeout(autoplayTimeoutId);
            autoplayTimeoutId = undefined;
            if (internal && !params.autoplayDisableOnInteraction) {
                _this.wrapperTransitionEnd(function () {
                    autoplay();
                });
            }
            _this.callPlugins('onAutoplayStop');
            if (params.onAutoplayStop) _this.fireCallback(params.onAutoplayStop, _this);
        }
        else {
            if (autoplayIntervalId) clearInterval(autoplayIntervalId);
            autoplayIntervalId = undefined;
            _this.callPlugins('onAutoplayStop');
            if (params.onAutoplayStop) _this.fireCallback(params.onAutoplayStop, _this);
        }
    };
    function autoplay() {
        autoplayTimeoutId = setTimeout(function () {
            if (params.loop) {
                _this.fixLoop();
                _this.swipeNext(true, true);
            }
            else if (!_this.swipeNext(true, true)) {
                if (!params.autoplayStopOnLast) _this.swipeTo(0);
                else {
                    clearTimeout(autoplayTimeoutId);
                    autoplayTimeoutId = undefined;
                }
            }
            _this.wrapperTransitionEnd(function () {
                if (typeof autoplayTimeoutId !== 'undefined') autoplay();
            });
        }, params.autoplay);
    }
    /*==================================================
     Loop
     ====================================================*/
    _this.loopCreated = false;
    _this.removeLoopedSlides = function () {
        if (_this.loopCreated) {
            for (var i = 0; i < _this.slides.length; i++) {
                if (_this.slides[i].getData('looped') === true) _this.wrapper.removeChild(_this.slides[i]);
            }
        }
    };

    _this.createLoop = function () {
        if (_this.slides.length === 0) return;
        if (params.slidesPerView === 'auto') {
            _this.loopedSlides = params.loopedSlides || 1;
        }
        else {
            _this.loopedSlides = Math.floor(params.slidesPerView) + params.loopAdditionalSlides;
        }

        if (_this.loopedSlides > _this.slides.length) {
            _this.loopedSlides = _this.slides.length;
        }

        var slideFirstHTML = '',
            slideLastHTML = '',
            i;
        var slidesSetFullHTML = '';
        /**
         loopedSlides is too large if loopAdditionalSlides are set.
         Need to divide the slides by maximum number of slides existing.

         @author        Tomaz Lovrec <tomaz.lovrec@blanc-noir.at>
         */
        var numSlides = _this.slides.length;
        var fullSlideSets = Math.floor(_this.loopedSlides / numSlides);
        var remainderSlides = _this.loopedSlides % numSlides;
        // assemble full sets of slides
        for (i = 0; i < (fullSlideSets * numSlides); i++) {
            var j = i;
            if (i >= numSlides) {
                var over = Math.floor(i / numSlides);
                j = i - (numSlides * over);
            }
            slidesSetFullHTML += _this.slides[j].outerHTML;
        }
        // assemble remainder slides
        // assemble remainder appended to existing slides
        for (i = 0; i < remainderSlides;i++) {
            slideLastHTML += addClassToHtmlString(params.slideDuplicateClass, _this.slides[i].outerHTML);
        }
        // assemble slides that get preppended to existing slides
        for (i = numSlides - remainderSlides; i < numSlides;i++) {
            slideFirstHTML += addClassToHtmlString(params.slideDuplicateClass, _this.slides[i].outerHTML);
        }
        // assemble all slides
        var slides = slideFirstHTML + slidesSetFullHTML + wrapper.innerHTML + slidesSetFullHTML + slideLastHTML;
        // set the slides
        wrapper.innerHTML = slides;

        _this.loopCreated = true;
        _this.calcSlides();

        //Update Looped Slides with special class
        for (i = 0; i < _this.slides.length; i++) {
            if (i < _this.loopedSlides || i >= _this.slides.length - _this.loopedSlides) _this.slides[i].setData('looped', true);
        }
        _this.callPlugins('onCreateLoop');

    };

    _this.fixLoop = function () {
        var newIndex;
        //Fix For Negative Oversliding
        if (_this.activeIndex < _this.loopedSlides) {
            newIndex = _this.slides.length - _this.loopedSlides * 3 + _this.activeIndex;
            _this.swipeTo(newIndex, 0, false);
        }
        //Fix For Positive Oversliding
        else if ((params.slidesPerView === 'auto' && _this.activeIndex >= _this.loopedSlides * 2) || (_this.activeIndex > _this.slides.length - params.slidesPerView * 2)) {
            newIndex = -_this.slides.length + _this.activeIndex + _this.loopedSlides;
            _this.swipeTo(newIndex, 0, false);
        }
    };

    /*==================================================
     Slides Loader
     ====================================================*/
    _this.loadSlides = function () {
        var slidesHTML = '';
        _this.activeLoaderIndex = 0;
        var slides = params.loader.slides;
        var slidesToLoad = params.loader.loadAllSlides ? slides.length : params.slidesPerView * (1 + params.loader.surroundGroups);
        for (var i = 0; i < slidesToLoad; i++) {
            if (params.loader.slidesHTMLType === 'outer') slidesHTML += slides[i];
            else {
                slidesHTML += '<' + params.slideElement + ' class="' + params.slideClass + '" data-swiperindex="' + i + '">' + slides[i] + '</' + params.slideElement + '>';
            }
        }
        _this.wrapper.innerHTML = slidesHTML;
        _this.calcSlides(true);
        //Add permanent transitionEnd callback
        if (!params.loader.loadAllSlides) {
            _this.wrapperTransitionEnd(_this.reloadSlides, true);
        }
    };

    _this.reloadSlides = function () {
        var slides = params.loader.slides;
        var newActiveIndex = parseInt(_this.activeSlide().data('swiperindex'), 10);
        if (newActiveIndex < 0 || newActiveIndex > slides.length - 1) return; //<-- Exit
        _this.activeLoaderIndex = newActiveIndex;
        var firstIndex = Math.max(0, newActiveIndex - params.slidesPerView * params.loader.surroundGroups);
        var lastIndex = Math.min(newActiveIndex + params.slidesPerView * (1 + params.loader.surroundGroups) - 1, slides.length - 1);
        //Update Transforms
        if (newActiveIndex > 0) {
            var newTransform = -slideSize * (newActiveIndex - firstIndex);
            _this.setWrapperTranslate(newTransform);
            _this.setWrapperTransition(0);
        }
        var i; // loop index
        //New Slides
        if (params.loader.logic === 'reload') {
            _this.wrapper.innerHTML = '';
            var slidesHTML = '';
            for (i = firstIndex; i <= lastIndex; i++) {
                slidesHTML += params.loader.slidesHTMLType === 'outer' ? slides[i] : '<' + params.slideElement + ' class="' + params.slideClass + '" data-swiperindex="' + i + '">' + slides[i] + '</' + params.slideElement + '>';
            }
            _this.wrapper.innerHTML = slidesHTML;
        }
        else {
            var minExistIndex = 1000;
            var maxExistIndex = 0;

            for (i = 0; i < _this.slides.length; i++) {
                var index = _this.slides[i].data('swiperindex');
                if (index < firstIndex || index > lastIndex) {
                    _this.wrapper.removeChild(_this.slides[i]);
                }
                else {
                    minExistIndex = Math.min(index, minExistIndex);
                    maxExistIndex = Math.max(index, maxExistIndex);
                }
            }
            for (i = firstIndex; i <= lastIndex; i++) {
                var newSlide;
                if (i < minExistIndex) {
                    newSlide = document.createElement(params.slideElement);
                    newSlide.className = params.slideClass;
                    newSlide.setAttribute('data-swiperindex', i);
                    newSlide.innerHTML = slides[i];
                    _this.wrapper.insertBefore(newSlide, _this.wrapper.firstChild);
                }
                if (i > maxExistIndex) {
                    newSlide = document.createElement(params.slideElement);
                    newSlide.className = params.slideClass;
                    newSlide.setAttribute('data-swiperindex', i);
                    newSlide.innerHTML = slides[i];
                    _this.wrapper.appendChild(newSlide);
                }
            }
        }
        //reInit
        _this.reInit(true);
    };

    /*==================================================
     Make Swiper
     ====================================================*/
    function makeSwiper() {
        _this.calcSlides();
        if (params.loader.slides.length > 0 && _this.slides.length === 0) {
            _this.loadSlides();
        }
        if (params.loop) {
            _this.createLoop();
        }
        _this.init();
        initEvents();
        if (params.pagination) {
            _this.createPagination(true);
        }

        if (params.loop || params.initialSlide > 0) {
            _this.swipeTo(params.initialSlide, 0, false);
        }
        else {
            _this.updateActiveSlide(0);
        }
        if (params.autoplay) {
            _this.startAutoplay();
        }
        /**
         * Set center slide index.
         *
         * @author        Tomaz Lovrec <tomaz.lovrec@gmail.com>
         */
        _this.centerIndex = _this.activeIndex;

        // Callbacks
        if (params.onSwiperCreated) _this.fireCallback(params.onSwiperCreated, _this);
        _this.callPlugins('onSwiperCreated');
    }

    makeSwiper();
};

Swiper.prototype = {
    plugins : {},

    /*==================================================
     Wrapper Operations
     ====================================================*/
    wrapperTransitionEnd : function (callback, permanent) {
        'use strict';
        var a = this,
            el = a.wrapper,
            events = ['webkitTransitionEnd', 'transitionend', 'oTransitionEnd', 'MSTransitionEnd', 'msTransitionEnd'],
            i;

        function fireCallBack(e) {
            if (e.target !== el) return;
            callback(a);
            if (a.params.queueEndCallbacks) a._queueEndCallbacks = false;
            if (!permanent) {
                for (i = 0; i < events.length; i++) {
                    a.h.removeEventListener(el, events[i], fireCallBack);
                }
            }
        }

        if (callback) {
            for (i = 0; i < events.length; i++) {
                a.h.addEventListener(el, events[i], fireCallBack);
            }
        }
    },

    getWrapperTranslate : function (axis) {
        'use strict';
        var el = this.wrapper,
            matrix, curTransform, curStyle, transformMatrix;

        // automatic axis detection
        if (typeof axis === 'undefined') {
            axis = this.params.mode === 'horizontal' ? 'x' : 'y';
        }

        if (this.support.transforms && this.params.useCSS3Transforms) {
            curStyle = window.getComputedStyle(el, null);
            if (window.WebKitCSSMatrix) {
                // Some old versions of Webkit choke when 'none' is passed; pass
                // empty string instead in this case
                transformMatrix = new WebKitCSSMatrix(curStyle.webkitTransform === 'none' ? '' : curStyle.webkitTransform);
            }
            else {
                transformMatrix = curStyle.MozTransform || curStyle.OTransform || curStyle.MsTransform || curStyle.msTransform  || curStyle.transform || curStyle.getPropertyValue('transform').replace('translate(', 'matrix(1, 0, 0, 1,');
                matrix = transformMatrix.toString().split(',');
            }

            if (axis === 'x') {
                //Latest Chrome and webkits Fix
                if (window.WebKitCSSMatrix)
                    curTransform = transformMatrix.m41;
                //Crazy IE10 Matrix
                else if (matrix.length === 16)
                    curTransform = parseFloat(matrix[12]);
                //Normal Browsers
                else
                    curTransform = parseFloat(matrix[4]);
            }
            if (axis === 'y') {
                //Latest Chrome and webkits Fix
                if (window.WebKitCSSMatrix)
                    curTransform = transformMatrix.m42;
                //Crazy IE10 Matrix
                else if (matrix.length === 16)
                    curTransform = parseFloat(matrix[13]);
                //Normal Browsers
                else
                    curTransform = parseFloat(matrix[5]);
            }
        }
        else {
            if (axis === 'x') curTransform = parseFloat(el.style.left, 10) || 0;
            if (axis === 'y') curTransform = parseFloat(el.style.top, 10) || 0;
        }
        return curTransform || 0;
    },

    setWrapperTranslate : function (x, y, z) {
        'use strict';
        var es = this.wrapper.style,
            coords = {x: 0, y: 0, z: 0},
            translate;

        // passed all coordinates
        if (arguments.length === 3) {
            coords.x = x;
            coords.y = y;
            coords.z = z;
        }

        // passed one coordinate and optional axis
        else {
            if (typeof y === 'undefined') {
                y = this.params.mode === 'horizontal' ? 'x' : 'y';
            }
            coords[y] = x;
        }

        if (this.support.transforms && this.params.useCSS3Transforms) {
            translate = this.support.transforms3d ? 'translate3d(' + coords.x + 'px, ' + coords.y + 'px, ' + coords.z + 'px)' : 'translate(' + coords.x + 'px, ' + coords.y + 'px)';
            es.webkitTransform = es.MsTransform = es.msTransform = es.MozTransform = es.OTransform = es.transform = translate;
        }
        else {
            es.left = coords.x + 'px';
            es.top  = coords.y + 'px';
        }
        this.callPlugins('onSetWrapperTransform', coords);
        if (this.params.onSetWrapperTransform) this.fireCallback(this.params.onSetWrapperTransform, this, coords);
    },

    setWrapperTransition : function (duration) {
        'use strict';
        var es = this.wrapper.style;
        es.webkitTransitionDuration = es.MsTransitionDuration = es.msTransitionDuration = es.MozTransitionDuration = es.OTransitionDuration = es.transitionDuration = (duration / 1000) + 's';
        this.callPlugins('onSetWrapperTransition', {duration: duration});
        if (this.params.onSetWrapperTransition) this.fireCallback(this.params.onSetWrapperTransition, this, duration);

    },

    /*==================================================
     Helpers
     ====================================================*/
    h : {
        getWidth: function (el, outer, round) {
            'use strict';
            var width = window.getComputedStyle(el, null).getPropertyValue('width');
            var returnWidth = parseFloat(width);
            //IE Fixes
            if (isNaN(returnWidth) || width.indexOf('%') > 0 || returnWidth < 0) {
                returnWidth = el.offsetWidth - parseFloat(window.getComputedStyle(el, null).getPropertyValue('padding-left')) - parseFloat(window.getComputedStyle(el, null).getPropertyValue('padding-right'));
            }
            if (outer) returnWidth += parseFloat(window.getComputedStyle(el, null).getPropertyValue('padding-left')) + parseFloat(window.getComputedStyle(el, null).getPropertyValue('padding-right'));
            if (round) return Math.ceil(returnWidth);
            else return returnWidth;
        },
        getHeight: function (el, outer, round) {
            'use strict';
            if (outer) return el.offsetHeight;

            var height = window.getComputedStyle(el, null).getPropertyValue('height');
            var returnHeight = parseFloat(height);
            //IE Fixes
            if (isNaN(returnHeight) || height.indexOf('%') > 0 || returnHeight < 0) {
                returnHeight = el.offsetHeight - parseFloat(window.getComputedStyle(el, null).getPropertyValue('padding-top')) - parseFloat(window.getComputedStyle(el, null).getPropertyValue('padding-bottom'));
            }
            if (outer) returnHeight += parseFloat(window.getComputedStyle(el, null).getPropertyValue('padding-top')) + parseFloat(window.getComputedStyle(el, null).getPropertyValue('padding-bottom'));
            if (round) return Math.ceil(returnHeight);
            else return returnHeight;
        },
        getOffset: function (el) {
            'use strict';
            var box = el.getBoundingClientRect();
            var body = document.body;
            var clientTop  = el.clientTop  || body.clientTop  || 0;
            var clientLeft = el.clientLeft || body.clientLeft || 0;
            var scrollTop  = window.pageYOffset || el.scrollTop;
            var scrollLeft = window.pageXOffset || el.scrollLeft;
            if (document.documentElement && !window.pageYOffset) {
                //IE7-8
                scrollTop  = document.documentElement.scrollTop;
                scrollLeft = document.documentElement.scrollLeft;
            }
            return {
                top: box.top  + scrollTop  - clientTop,
                left: box.left + scrollLeft - clientLeft
            };
        },
        windowWidth : function () {
            'use strict';
            if (window.innerWidth) return window.innerWidth;
            else if (document.documentElement && document.documentElement.clientWidth) return document.documentElement.clientWidth;
        },
        windowHeight : function () {
            'use strict';
            if (window.innerHeight) return window.innerHeight;
            else if (document.documentElement && document.documentElement.clientHeight) return document.documentElement.clientHeight;
        },
        windowScroll : function () {
            'use strict';
            if (typeof pageYOffset !== 'undefined') {
                return {
                    left: window.pageXOffset,
                    top: window.pageYOffset
                };
            }
            else if (document.documentElement) {
                return {
                    left: document.documentElement.scrollLeft,
                    top: document.documentElement.scrollTop
                };
            }
        },

        addEventListener : function (el, event, listener, useCapture) {
            'use strict';
            if (typeof useCapture === 'undefined') {
                useCapture = false;
            }

            if (el.addEventListener) {
                el.addEventListener(event, listener, useCapture);
            }
            else if (el.attachEvent) {
                el.attachEvent('on' + event, listener);
            }
        },

        removeEventListener : function (el, event, listener, useCapture) {
            'use strict';
            if (typeof useCapture === 'undefined') {
                useCapture = false;
            }

            if (el.removeEventListener) {
                el.removeEventListener(event, listener, useCapture);
            }
            else if (el.detachEvent) {
                el.detachEvent('on' + event, listener);
            }
        }
    },
    setTransform : function (el, transform) {
        'use strict';
        var es = el.style;
        es.webkitTransform = es.MsTransform = es.msTransform = es.MozTransform = es.OTransform = es.transform = transform;
    },
    setTranslate : function (el, translate) {
        'use strict';
        var es = el.style;
        var pos = {
            x : translate.x || 0,
            y : translate.y || 0,
            z : translate.z || 0
        };
        var transformString = this.support.transforms3d ? 'translate3d(' + (pos.x) + 'px,' + (pos.y) + 'px,' + (pos.z) + 'px)' : 'translate(' + (pos.x) + 'px,' + (pos.y) + 'px)';
        es.webkitTransform = es.MsTransform = es.msTransform = es.MozTransform = es.OTransform = es.transform = transformString;
        if (!this.support.transforms) {
            es.left = pos.x + 'px';
            es.top = pos.y + 'px';
        }
    },
    setTransition : function (el, duration) {
        'use strict';
        var es = el.style;
        es.webkitTransitionDuration = es.MsTransitionDuration = es.msTransitionDuration = es.MozTransitionDuration = es.OTransitionDuration = es.transitionDuration = duration + 'ms';
    },
    /*==================================================
     Feature Detection
     ====================================================*/
    support: {

        touch : (window.Modernizr && Modernizr.touch === true) || (function () {
            'use strict';
            return !!(('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch);
        })(),

        transforms3d : (window.Modernizr && Modernizr.csstransforms3d === true) || (function () {
            'use strict';
            var div = document.createElement('div').style;
            return ('webkitPerspective' in div || 'MozPerspective' in div || 'OPerspective' in div || 'MsPerspective' in div || 'perspective' in div);
        })(),

        transforms : (window.Modernizr && Modernizr.csstransforms === true) || (function () {
            'use strict';
            var div = document.createElement('div').style;
            return ('transform' in div || 'WebkitTransform' in div || 'MozTransform' in div || 'msTransform' in div || 'MsTransform' in div || 'OTransform' in div);
        })(),

        transitions : (window.Modernizr && Modernizr.csstransitions === true) || (function () {
            'use strict';
            var div = document.createElement('div').style;
            return ('transition' in div || 'WebkitTransition' in div || 'MozTransition' in div || 'msTransition' in div || 'MsTransition' in div || 'OTransition' in div);
        })(),

        classList : (function () {
            'use strict';
            var div = document.createElement('div');
            return 'classList' in div;
        })()
    },

    browser : {

        ie8 : (function () {
            'use strict';
            var rv = -1; // Return value assumes failure.
            if (navigator.appName === 'Microsoft Internet Explorer') {
                var ua = navigator.userAgent;
                var re = new RegExp(/MSIE ([0-9]{1,}[\.0-9]{0,})/);
                if (re.exec(ua) !== null)
                    rv = parseFloat(RegExp.$1);
            }
            return rv !== -1 && rv < 9;
        })(),

        ie10 : window.navigator.msPointerEnabled,
        ie11 : window.navigator.pointerEnabled
    }
};

/*=========================
 jQuery & Zepto Plugins
 ===========================*/
if (window.jQuery || window.Zepto) {
    (function ($) {
        'use strict';
        $.fn.swiper = function (params) {
            var firstInstance;
            this.each(function (i) {
                var that = $(this);
                var s = new Swiper(that[0], params);
                if (!i) firstInstance = s;
                that.data('swiper', s);
            });
            return firstInstance;
        };
    })(window.jQuery || window.Zepto);
}

// CommonJS support
if (typeof(module) !== 'undefined') {
    module.exports = Swiper;

// requirejs support
} else if (typeof define === 'function' && define.amd) {
    define([], function () {
        'use strict';
        return Swiper;
    });
}
/*!
 * jQuery Cookie Plugin v1.4.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2006, 2014 Klaus Hartl
 * Released under the MIT license
 */
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // CommonJS
        factory(require('jquery'));
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {

    var pluses = /\+/g;

    function encode(s) {
        return config.raw ? s : encodeURIComponent(s);
    }

    function decode(s) {
        return config.raw ? s : decodeURIComponent(s);
    }

    function stringifyCookieValue(value) {
        return encode(config.json ? JSON.stringify(value) : String(value));
    }

    function parseCookieValue(s) {
        if (s.indexOf('"') === 0) {
            // This is a quoted cookie as according to RFC2068, unescape...
            s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
        }

        try {
            // Replace server-side written pluses with spaces.
            // If we can't decode the cookie, ignore it, it's unusable.
            // If we can't parse the cookie, ignore it, it's unusable.
            s = decodeURIComponent(s.replace(pluses, ' '));
            return config.json ? JSON.parse(s) : s;
        } catch(e) {}
    }

    function read(s, converter) {
        var value = config.raw ? s : parseCookieValue(s);
        return $.isFunction(converter) ? converter(value) : value;
    }

    var config = $.cookie = function (key, value, options) {

        // Write

        if (arguments.length > 1 && !$.isFunction(value)) {
            options = $.extend({}, config.defaults, options);

            if (typeof options.expires === 'number') {
                var days = options.expires, t = options.expires = new Date();
                t.setTime(+t + days * 864e+5);
            }

            return (document.cookie = [
                encode(key), '=', stringifyCookieValue(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path    ? '; path=' + options.path : '',
                options.domain  ? '; domain=' + options.domain : '',
                options.secure  ? '; secure' : ''
            ].join(''));
        }

        // Read

        var result = key ? undefined : {};

        // To prevent the for loop in the first place assign an empty array
        // in case there are no cookies at all. Also prevents odd result when
        // calling $.cookie().
        var cookies = document.cookie ? document.cookie.split('; ') : [];

        for (var i = 0, l = cookies.length; i < l; i++) {
            var parts = cookies[i].split('=');
            var name = decode(parts.shift());
            var cookie = parts.join('=');

            if (key && key === name) {
                // If second argument (value) is a function it's a converter...
                result = read(cookie, value);
                break;
            }

            // Prevent storing a cookie that we couldn't decode.
            if (!key && (cookie = read(cookie)) !== undefined) {
                result[name] = cookie;
            }
        }

        return result;
    };

    config.defaults = {};

    $.removeCookie = function (key, options) {
        if ($.cookie(key) === undefined) {
            return false;
        }

        // Must not alter options, thus extending a fresh object...
        $.cookie(key, '', $.extend({}, options, { expires: -1 }));
        return !$.cookie(key);
    };

}));
/*! jQuery Mosaic Flow v0.2.5 by Artem Sapegin - http://sapegin.github.io/jquery.mosaicflow/ - Licensed MIT */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):a(jQuery)}(function(a){"use strict";function b(a,b){this.container=a,this.options=b,this.container.trigger("start"),this.init(),this.container.trigger("ready")}function c(a){function b(a,b){return b.toUpper()}var c={},d=a.data();for(var e in d)c[e.replace(/-(\w)/g,b)]=d[e];return c}function d(a){var b={};if(b.height=parseInt(a.attr("height"),10),b.width=parseInt(a.attr("width"),10),0===b.height||0===b.width){var c=new Image;c.src=a.attr("src"),b.width=c.width,b.height=c.height}return b}var e=0;a.fn.mosaicflow=function(d){var e=Array.prototype.slice.call(arguments,0);return this.each(function(){var f=a(this),g=f.data("mosaicflow");g?"string"==typeof d&&g[d](e[1]):(d=a.extend({},a.fn.mosaicflow.defaults,d,c(f)),g=new b(f,d),f.data("mosaicflow",g))})},a.fn.mosaicflow.defaults={itemSelector:"> *",columnClass:"mosaicflow__column",minItemWidth:240,itemHeightCalculation:"auto"},b.prototype={init:function(){this.__uid=e++,this.__uidItemCounter=0,this.items=this.container.find(this.options.itemSelector),this.columns=a([]),this.columnsHeights=[],this.itemsHeights={},this.tempContainer=a("<div>").css({visibility:"hidden",width:"100%"}),this.workOnTemp=!1,this.autoCalculation="auto"===this.options.itemHeightCalculation,this.container.append(this.tempContainer);var b=this;this.items.each(function(){var c=a(this),d=c.attr("id");d||(d=b.generateUniqueId(),c.attr("id",d))}),this.container.css("visibility","hidden"),this.autoCalculation?a(window).load(a.proxy(this.refill,this)):this.refill(),a(window).resize(a.proxy(this.refill,this))},refill:function(){this.container.trigger("fill"),this.numberOfColumns=Math.floor(this.container.width()/this.options.minItemWidth),this.numberOfColumns<1&&(this.numberOfColumns=1);var a=this.ensureColumns();a&&(this.fillColumns(),this.columns.filter(":hidden").remove()),this.container.css("visibility","visible"),this.container.trigger("filled")},ensureColumns:function(){var b=this.columns.length,c=this.numberOfColumns;if(this.workingContainer=0===b?this.tempContainer:this.container,c>b)for(var d=c-b,e=0;d>e;e++){var f=a("<div>",{"class":this.options.columnClass});this.workingContainer.append(f)}else if(b>c){for(var g=b;g>=c;)this.columns.eq(g).hide(),g--;var h=b-c;this.columnsHeights.splice(this.columnsHeights.length-h,h)}return c!==b?(this.columns=this.workingContainer.find("."+this.options.columnClass),this.columns.css("width",100/c+"%"),!0):!1},fillColumns:function(){for(var a=this.numberOfColumns,b=this.items.length,c=0;a>c;c++){var d=this.columns.eq(c);this.columnsHeights[c]=0;for(var e=c;b>e;e+=a){var f=this.items.eq(e),g=0;d.append(f),g=this.autoCalculation?f.outerHeight():parseInt(f.find("img").attr("height"),10),this.itemsHeights[f.attr("id")]=g,this.columnsHeights[c]+=g}}this.levelBottomEdge(this.itemsHeights,this.columnsHeights),this.workingContainer===this.tempContainer&&this.container.append(this.tempContainer.children()),this.container.trigger("mosaicflow-layout")},levelBottomEdge:function(b,c){for(;;){var d=a.inArray(Math.min.apply(null,c),c),e=a.inArray(Math.max.apply(null,c),c);if(d===e)return;var f=this.columns.eq(e).children().last(),g=b[f.attr("id")],h=c[d],i=c[e],j=h+g;if(j>=i)return;this.columns.eq(d).append(f),c[e]-=g,c[d]+=g}},add:function(b){this.container.trigger("add");var c=a.inArray(Math.min.apply(null,this.columnsHeights),this.columnsHeights),e=0;if(this.autoCalculation){b.css({position:"static",visibility:"hidden",display:"block"}).appendTo(this.columns.eq(c)),e=b.outerHeight();var f=b.find("img");0!==f.length&&f.each(function(){var b=a(this),c=d(b),f=b.width()*c.height/c.width;e+=f}),b.detach().css({position:"static",visibility:"visible"})}else e=parseInt(b.find("img").attr("height"),10);b.attr("id")||b.attr("id",this.generateUniqueId());var g=this.items.toArray();g.push(b),this.items=a(g),this.itemsHeights[b.attr("id")]=e,this.columnsHeights[c]+=e,this.columns.eq(c).append(b),this.levelBottomEdge(this.itemsHeights,this.columnsHeights),this.container.trigger("mosaicflow-layout"),this.container.trigger("added")},remove:function(a){this.container.trigger("remove");var b=a.parents("."+this.options.columnClass);this.columnsHeights[b.index()-1]-=this.itemsHeights[a.attr("id")],a.detach(),this.items=this.items.not(a),this.levelBottomEdge(this.itemsHeights,this.columnsHeights),this.container.trigger("mosaicflow-layout"),this.container.trigger("removed")},empty:function(){var b=this.numberOfColumns;this.items=a([]),this.itemsHeights={};for(var c=0;b>c;c++){var d=this.columns.eq(c);this.columnsHeights[c]=0,d.empty()}this.container.trigger("mosaicflow-layout")},recomputeHeights:function(){function b(b,d){d=a(d);var f=0;f=c.autoCalculation?d.outerHeight():parseInt(d.find("img").attr("height"),10),c.itemsHeights[d.attr("id")]=f,c.columnsHeights[e]+=f}for(var c=this,d=this.numberOfColumns,e=0;d>e;e++){var f=this.columns.eq(e);this.columnsHeights[e]=0,f.children().each(b)}},generateUniqueId:function(){return this.__uidItemCounter++,"mosaic-"+this.__uid+"-itemid-"+this.__uidItemCounter}},a(function(){a(".mosaicflow").mosaicflow()})});
/*
 *  jQuery OwlCarousel v1.3.3
 *
 *  Copyright (c) 2013 Bartosz Wojciechowski
 *  http://www.owlgraphic.com/owlcarousel/
 *
 *  Licensed under MIT
 *
 */

/*JS Lint helpers: */
/*global dragMove: false, dragEnd: false, $, jQuery, alert, window, document */
/*jslint nomen: true, continue:true */

if (typeof Object.create !== "function") {
    Object.create = function (obj) {
        function F() {}
        F.prototype = obj;
        return new F();
    };
}
(function ($, window, document) {

    var Carousel = {
        init : function (options, el) {
            var base = this;

            base.$elem = $(el);
            base.options = $.extend({}, $.fn.owlCarousel.options, base.$elem.data(), options);

            base.userOptions = options;
            base.loadContent();
        },

        loadContent : function () {
            var base = this, url;

            function getData(data) {
                var i, content = "";
                if (typeof base.options.jsonSuccess === "function") {
                    base.options.jsonSuccess.apply(this, [data]);
                } else {
                    for (i in data.owl) {
                        if (data.owl.hasOwnProperty(i)) {
                            content += data.owl[i].item;
                        }
                    }
                    base.$elem.html(content);
                }
                base.logIn();
            }

            if (typeof base.options.beforeInit === "function") {
                base.options.beforeInit.apply(this, [base.$elem]);
            }

            if (typeof base.options.jsonPath === "string") {
                url = base.options.jsonPath;
                $.getJSON(url, getData);
            } else {
                base.logIn();
            }
        },

        logIn : function () {
            var base = this;

            base.$elem.data({
                "owl-originalStyles": base.$elem.attr("style"),
                "owl-originalClasses": base.$elem.attr("class")
            });

            base.$elem.css({opacity: 0});
            base.orignalItems = base.options.items;
            base.checkBrowser();
            base.wrapperWidth = 0;
            base.checkVisible = null;
            base.setVars();
        },

        setVars : function () {
            var base = this;
            if (base.$elem.children().length === 0) {return false; }
            base.baseClass();
            base.eventTypes();
            base.$userItems = base.$elem.children();
            base.itemsAmount = base.$userItems.length;
            base.wrapItems();
            base.$owlItems = base.$elem.find(".owl-item");
            base.$owlWrapper = base.$elem.find(".owl-wrapper");
            base.playDirection = "next";
            base.prevItem = 0;
            base.prevArr = [0];
            base.currentItem = 0;
            base.customEvents();
            base.onStartup();
        },

        onStartup : function () {
            var base = this;
            base.updateItems();
            base.calculateAll();
            base.buildControls();
            base.updateControls();
            base.response();
            base.moveEvents();
            base.stopOnHover();
            base.owlStatus();

            if (base.options.transitionStyle !== false) {
                base.transitionTypes(base.options.transitionStyle);
            }
            if (base.options.autoPlay === true) {
                base.options.autoPlay = 5000;
            }
            base.play();

            base.$elem.find(".owl-wrapper").css("display", "block");

            if (!base.$elem.is(":visible")) {
                base.watchVisibility();
            } else {
                base.$elem.css("opacity", 1);
            }
            base.onstartup = false;
            base.eachMoveUpdate();
            if (typeof base.options.afterInit === "function") {
                base.options.afterInit.apply(this, [base.$elem]);
            }
        },

        eachMoveUpdate : function () {
            var base = this;

            if (base.options.lazyLoad === true) {
                base.lazyLoad();
            }
            if (base.options.autoHeight === true) {
                base.autoHeight();
            }
            base.onVisibleItems();

            if (typeof base.options.afterAction === "function") {
                base.options.afterAction.apply(this, [base.$elem]);
            }
        },

        updateVars : function () {
            var base = this;
            if (typeof base.options.beforeUpdate === "function") {
                base.options.beforeUpdate.apply(this, [base.$elem]);
            }
            base.watchVisibility();
            base.updateItems();
            base.calculateAll();
            base.updatePosition();
            base.updateControls();
            base.eachMoveUpdate();
            if (typeof base.options.afterUpdate === "function") {
                base.options.afterUpdate.apply(this, [base.$elem]);
            }
        },

        reload : function () {
            var base = this;
            window.setTimeout(function () {
                base.updateVars();
            }, 0);
        },

        watchVisibility : function () {
            var base = this;

            if (base.$elem.is(":visible") === false) {
                base.$elem.css({opacity: 0});
                window.clearInterval(base.autoPlayInterval);
                window.clearInterval(base.checkVisible);
            } else {
                return false;
            }
            base.checkVisible = window.setInterval(function () {
                if (base.$elem.is(":visible")) {
                    base.reload();
                    base.$elem.animate({opacity: 1}, 200);
                    window.clearInterval(base.checkVisible);
                }
            }, 500);
        },

        wrapItems : function () {
            var base = this;
            base.$userItems.wrapAll("<div class=\"owl-wrapper\">").wrap("<div class=\"owl-item\"></div>");
            base.$elem.find(".owl-wrapper").wrap("<div class=\"owl-wrapper-outer\">");
            base.wrapperOuter = base.$elem.find(".owl-wrapper-outer");
            base.$elem.css("display", "block");
        },

        baseClass : function () {
            var base = this,
                hasBaseClass = base.$elem.hasClass(base.options.baseClass),
                hasThemeClass = base.$elem.hasClass(base.options.theme);

            if (!hasBaseClass) {
                base.$elem.addClass(base.options.baseClass);
            }

            if (!hasThemeClass) {
                base.$elem.addClass(base.options.theme);
            }
        },

        updateItems : function () {
            var base = this, width, i;

            if (base.options.responsive === false) {
                return false;
            }
            if (base.options.singleItem === true) {
                base.options.items = base.orignalItems = 1;
                base.options.itemsCustom = false;
                base.options.itemsDesktop = false;
                base.options.itemsDesktopSmall = false;
                base.options.itemsTablet = false;
                base.options.itemsTabletSmall = false;
                base.options.itemsMobile = false;
                return false;
            }

            width = $(base.options.responsiveBaseWidth).width();

            if (width > (base.options.itemsDesktop[0] || base.orignalItems)) {
                base.options.items = base.orignalItems;
            }
            if (base.options.itemsCustom !== false) {
                //Reorder array by screen size
                base.options.itemsCustom.sort(function (a, b) {return a[0] - b[0]; });

                for (i = 0; i < base.options.itemsCustom.length; i += 1) {
                    if (base.options.itemsCustom[i][0] <= width) {
                        base.options.items = base.options.itemsCustom[i][1];
                    }
                }

            } else {

                if (width <= base.options.itemsDesktop[0] && base.options.itemsDesktop !== false) {
                    base.options.items = base.options.itemsDesktop[1];
                }

                if (width <= base.options.itemsDesktopSmall[0] && base.options.itemsDesktopSmall !== false) {
                    base.options.items = base.options.itemsDesktopSmall[1];
                }

                if (width <= base.options.itemsTablet[0] && base.options.itemsTablet !== false) {
                    base.options.items = base.options.itemsTablet[1];
                }

                if (width <= base.options.itemsTabletSmall[0] && base.options.itemsTabletSmall !== false) {
                    base.options.items = base.options.itemsTabletSmall[1];
                }

                if (width <= base.options.itemsMobile[0] && base.options.itemsMobile !== false) {
                    base.options.items = base.options.itemsMobile[1];
                }
            }

            //if number of items is less than declared
            if (base.options.items > base.itemsAmount && base.options.itemsScaleUp === true) {
                base.options.items = base.itemsAmount;
            }
        },

        response : function () {
            var base = this,
                smallDelay,
                lastWindowWidth;

            if (base.options.responsive !== true) {
                return false;
            }
            lastWindowWidth = $(window).width();

            base.resizer = function () {
                if ($(window).width() !== lastWindowWidth) {
                    if (base.options.autoPlay !== false) {
                        window.clearInterval(base.autoPlayInterval);
                    }
                    window.clearTimeout(smallDelay);
                    smallDelay = window.setTimeout(function () {
                        lastWindowWidth = $(window).width();
                        base.updateVars();
                    }, base.options.responsiveRefreshRate);
                }
            };
            $(window).resize(base.resizer);
        },

        updatePosition : function () {
            var base = this;
            base.jumpTo(base.currentItem);
            if (base.options.autoPlay !== false) {
                base.checkAp();
            }
        },

        appendItemsSizes : function () {
            var base = this,
                roundPages = 0,
                lastItem = base.itemsAmount - base.options.items;

            base.$owlItems.each(function (index) {
                var $this = $(this);
                $this
                    .css({"width": base.itemWidth})
                    .data("owl-item", Number(index));

                if (index % base.options.items === 0 || index === lastItem) {
                    if (!(index > lastItem)) {
                        roundPages += 1;
                    }
                }
                $this.data("owl-roundPages", roundPages);
            });
        },

        appendWrapperSizes : function () {
            var base = this,
                width = base.$owlItems.length * base.itemWidth;

            base.$owlWrapper.css({
                "width": width * 2,
                "left": 0
            });
            base.appendItemsSizes();
        },

        calculateAll : function () {
            var base = this;
            base.calculateWidth();
            base.appendWrapperSizes();
            base.loops();
            base.max();
        },

        calculateWidth : function () {
            var base = this;
            base.itemWidth = Math.round(base.$elem.width() / base.options.items);
        },

        max : function () {
            var base = this,
                maximum = ((base.itemsAmount * base.itemWidth) - base.options.items * base.itemWidth) * -1;
            if (base.options.items > base.itemsAmount) {
                base.maximumItem = 0;
                maximum = 0;
                base.maximumPixels = 0;
            } else {
                base.maximumItem = base.itemsAmount - base.options.items;
                base.maximumPixels = maximum;
            }
            return maximum;
        },

        min : function () {
            return 0;
        },

        loops : function () {
            var base = this,
                prev = 0,
                elWidth = 0,
                i,
                item,
                roundPageNum;

            base.positionsInArray = [0];
            base.pagesInArray = [];

            for (i = 0; i < base.itemsAmount; i += 1) {
                elWidth += base.itemWidth;
                base.positionsInArray.push(-elWidth);

                if (base.options.scrollPerPage === true) {
                    item = $(base.$owlItems[i]);
                    roundPageNum = item.data("owl-roundPages");
                    if (roundPageNum !== prev) {
                        base.pagesInArray[prev] = base.positionsInArray[i];
                        prev = roundPageNum;
                    }
                }
            }
        },

        buildControls : function () {
            var base = this;
            if (base.options.navigation === true || base.options.pagination === true) {
                base.owlControls = $("<div class=\"owl-controls\"/>").toggleClass("clickable", !base.browser.isTouch).appendTo(base.$elem);
            }
            if (base.options.pagination === true) {
                base.buildPagination();
            }
            if (base.options.navigation === true) {
                base.buildButtons();
            }
        },

        buildButtons : function () {
            var base = this,
                buttonsWrapper = $("<div class=\"owl-buttons\"/>");
            base.owlControls.append(buttonsWrapper);

            base.buttonPrev = $("<div/>", {
                "class" : "owl-prev",
                "html" : base.options.navigationText[0] || ""
            });

            base.buttonNext = $("<div/>", {
                "class" : "owl-next",
                "html" : base.options.navigationText[1] || ""
            });

            buttonsWrapper
                .append(base.buttonPrev)
                .append(base.buttonNext);

            buttonsWrapper.on("touchstart.owlControls mousedown.owlControls", "div[class^=\"owl\"]", function (event) {
                event.preventDefault();
            });

            buttonsWrapper.on("touchend.owlControls mouseup.owlControls", "div[class^=\"owl\"]", function (event) {
                event.preventDefault();
                if ($(this).hasClass("owl-next")) {
                    base.next();
                } else {
                    base.prev();
                }
            });
        },

        buildPagination : function () {
            var base = this;

            base.paginationWrapper = $("<div class=\"owl-pagination\"/>");
            base.owlControls.append(base.paginationWrapper);

            base.paginationWrapper.on("touchend.owlControls mouseup.owlControls", ".owl-page", function (event) {
                event.preventDefault();
                if (Number($(this).data("owl-page")) !== base.currentItem) {
                    base.goTo(Number($(this).data("owl-page")), true);
                }
            });
        },

        updatePagination : function () {
            var base = this,
                counter,
                lastPage,
                lastItem,
                i,
                paginationButton,
                paginationButtonInner;

            if (base.options.pagination === false) {
                return false;
            }

            base.paginationWrapper.html("");

            counter = 0;
            lastPage = base.itemsAmount - base.itemsAmount % base.options.items;

            for (i = 0; i < base.itemsAmount; i += 1) {
                if (i % base.options.items === 0) {
                    counter += 1;
                    if (lastPage === i) {
                        lastItem = base.itemsAmount - base.options.items;
                    }
                    paginationButton = $("<div/>", {
                        "class" : "owl-page"
                    });
                    paginationButtonInner = $("<span></span>", {
                        "text": base.options.paginationNumbers === true ? counter : "",
                        "class": base.options.paginationNumbers === true ? "owl-numbers" : ""
                    });
                    paginationButton.append(paginationButtonInner);

                    paginationButton.data("owl-page", lastPage === i ? lastItem : i);
                    paginationButton.data("owl-roundPages", counter);

                    base.paginationWrapper.append(paginationButton);
                }
            }
            base.checkPagination();
        },
        checkPagination : function () {
            var base = this;
            if (base.options.pagination === false) {
                return false;
            }
            base.paginationWrapper.find(".owl-page").each(function () {
                if ($(this).data("owl-roundPages") === $(base.$owlItems[base.currentItem]).data("owl-roundPages")) {
                    base.paginationWrapper
                        .find(".owl-page")
                        .removeClass("active");
                    $(this).addClass("active");
                }
            });
        },

        checkNavigation : function () {
            var base = this;

            if (base.options.navigation === false) {
                return false;
            }
            if (base.options.rewindNav === false) {
                if (base.currentItem === 0 && base.maximumItem === 0) {
                    base.buttonPrev.addClass("disabled");
                    base.buttonNext.addClass("disabled");
                } else if (base.currentItem === 0 && base.maximumItem !== 0) {
                    base.buttonPrev.addClass("disabled");
                    base.buttonNext.removeClass("disabled");
                } else if (base.currentItem === base.maximumItem) {
                    base.buttonPrev.removeClass("disabled");
                    base.buttonNext.addClass("disabled");
                } else if (base.currentItem !== 0 && base.currentItem !== base.maximumItem) {
                    base.buttonPrev.removeClass("disabled");
                    base.buttonNext.removeClass("disabled");
                }
            }
        },

        updateControls : function () {
            var base = this;
            base.updatePagination();
            base.checkNavigation();
            if (base.owlControls) {
                if (base.options.items >= base.itemsAmount) {
                    base.owlControls.hide();
                } else {
                    base.owlControls.show();
                }
            }
        },

        destroyControls : function () {
            var base = this;
            if (base.owlControls) {
                base.owlControls.remove();
            }
        },

        next : function (speed) {
            var base = this;

            if (base.isTransition) {
                return false;
            }

            base.currentItem += base.options.scrollPerPage === true ? base.options.items : 1;
            if (base.currentItem > base.maximumItem + (base.options.scrollPerPage === true ? (base.options.items - 1) : 0)) {
                if (base.options.rewindNav === true) {
                    base.currentItem = 0;
                    speed = "rewind";
                } else {
                    base.currentItem = base.maximumItem;
                    return false;
                }
            }
            base.goTo(base.currentItem, speed);
        },

        prev : function (speed) {
            var base = this;

            if (base.isTransition) {
                return false;
            }

            if (base.options.scrollPerPage === true && base.currentItem > 0 && base.currentItem < base.options.items) {
                base.currentItem = 0;
            } else {
                base.currentItem -= base.options.scrollPerPage === true ? base.options.items : 1;
            }
            if (base.currentItem < 0) {
                if (base.options.rewindNav === true) {
                    base.currentItem = base.maximumItem;
                    speed = "rewind";
                } else {
                    base.currentItem = 0;
                    return false;
                }
            }
            base.goTo(base.currentItem, speed);
        },

        goTo : function (position, speed, drag) {
            var base = this,
                goToPixel;

            if (base.isTransition) {
                return false;
            }
            if (typeof base.options.beforeMove === "function") {
                base.options.beforeMove.apply(this, [base.$elem]);
            }
            if (position >= base.maximumItem) {
                position = base.maximumItem;
            } else if (position <= 0) {
                position = 0;
            }

            base.currentItem = base.owl.currentItem = position;
            if (base.options.transitionStyle !== false && drag !== "drag" && base.options.items === 1 && base.browser.support3d === true) {
                base.swapSpeed(0);
                if (base.browser.support3d === true) {
                    base.transition3d(base.positionsInArray[position]);
                } else {
                    base.css2slide(base.positionsInArray[position], 1);
                }
                base.afterGo();
                base.singleItemTransition();
                return false;
            }
            goToPixel = base.positionsInArray[position];

            if (base.browser.support3d === true) {
                base.isCss3Finish = false;

                if (speed === true) {
                    base.swapSpeed("paginationSpeed");
                    window.setTimeout(function () {
                        base.isCss3Finish = true;
                    }, base.options.paginationSpeed);

                } else if (speed === "rewind") {
                    base.swapSpeed(base.options.rewindSpeed);
                    window.setTimeout(function () {
                        base.isCss3Finish = true;
                    }, base.options.rewindSpeed);

                } else {
                    base.swapSpeed("slideSpeed");
                    window.setTimeout(function () {
                        base.isCss3Finish = true;
                    }, base.options.slideSpeed);
                }
                base.transition3d(goToPixel);
            } else {
                if (speed === true) {
                    base.css2slide(goToPixel, base.options.paginationSpeed);
                } else if (speed === "rewind") {
                    base.css2slide(goToPixel, base.options.rewindSpeed);
                } else {
                    base.css2slide(goToPixel, base.options.slideSpeed);
                }
            }
            base.afterGo();
        },

        jumpTo : function (position) {
            var base = this;
            if (typeof base.options.beforeMove === "function") {
                base.options.beforeMove.apply(this, [base.$elem]);
            }
            if (position >= base.maximumItem || position === -1) {
                position = base.maximumItem;
            } else if (position <= 0) {
                position = 0;
            }
            base.swapSpeed(0);
            if (base.browser.support3d === true) {
                base.transition3d(base.positionsInArray[position]);
            } else {
                base.css2slide(base.positionsInArray[position], 1);
            }
            base.currentItem = base.owl.currentItem = position;
            base.afterGo();
        },

        afterGo : function () {
            var base = this;

            base.prevArr.push(base.currentItem);
            base.prevItem = base.owl.prevItem = base.prevArr[base.prevArr.length - 2];
            base.prevArr.shift(0);

            if (base.prevItem !== base.currentItem) {
                base.checkPagination();
                base.checkNavigation();
                base.eachMoveUpdate();

                if (base.options.autoPlay !== false) {
                    base.checkAp();
                }
            }
            if (typeof base.options.afterMove === "function" && base.prevItem !== base.currentItem) {
                base.options.afterMove.apply(this, [base.$elem]);
            }
        },

        stop : function () {
            var base = this;
            base.apStatus = "stop";
            window.clearInterval(base.autoPlayInterval);
        },

        checkAp : function () {
            var base = this;
            if (base.apStatus !== "stop") {
                base.play();
            }
        },

        play : function () {
            var base = this;
            base.apStatus = "play";
            if (base.options.autoPlay === false) {
                return false;
            }
            window.clearInterval(base.autoPlayInterval);
            base.autoPlayInterval = window.setInterval(function () {
                base.next(true);
            }, base.options.autoPlay);
        },

        swapSpeed : function (action) {
            var base = this;
            if (action === "slideSpeed") {
                base.$owlWrapper.css(base.addCssSpeed(base.options.slideSpeed));
            } else if (action === "paginationSpeed") {
                base.$owlWrapper.css(base.addCssSpeed(base.options.paginationSpeed));
            } else if (typeof action !== "string") {
                base.$owlWrapper.css(base.addCssSpeed(action));
            }
        },

        addCssSpeed : function (speed) {
            return {
                "-webkit-transition": "all " + speed + "ms ease",
                "-moz-transition": "all " + speed + "ms ease",
                "-o-transition": "all " + speed + "ms ease",
                "transition": "all " + speed + "ms ease"
            };
        },

        removeTransition : function () {
            return {
                "-webkit-transition": "",
                "-moz-transition": "",
                "-o-transition": "",
                "transition": ""
            };
        },

        doTranslate : function (pixels) {
            return {
                "-webkit-transform": "translate3d(" + pixels + "px, 0px, 0px)",
                "-moz-transform": "translate3d(" + pixels + "px, 0px, 0px)",
                "-o-transform": "translate3d(" + pixels + "px, 0px, 0px)",
                "-ms-transform": "translate3d(" + pixels + "px, 0px, 0px)",
                "transform": "translate3d(" + pixels + "px, 0px,0px)"
            };
        },

        transition3d : function (value) {
            var base = this;
            base.$owlWrapper.css(base.doTranslate(value));
        },

        css2move : function (value) {
            var base = this;
            base.$owlWrapper.css({"left" : value});
        },

        css2slide : function (value, speed) {
            var base = this;

            base.isCssFinish = false;
            base.$owlWrapper.stop(true, true).animate({
                "left" : value
            }, {
                duration : speed || base.options.slideSpeed,
                complete : function () {
                    base.isCssFinish = true;
                }
            });
        },

        checkBrowser : function () {
            var base = this,
                translate3D = "translate3d(0px, 0px, 0px)",
                tempElem = document.createElement("div"),
                regex,
                asSupport,
                support3d,
                isTouch;

            tempElem.style.cssText = "  -moz-transform:" + translate3D +
                "; -ms-transform:"     + translate3D +
                "; -o-transform:"      + translate3D +
                "; -webkit-transform:" + translate3D +
                "; transform:"         + translate3D;
            regex = /translate3d\(0px, 0px, 0px\)/g;
            asSupport = tempElem.style.cssText.match(regex);
            support3d = (asSupport !== null && asSupport.length === 1);

            isTouch = "ontouchstart" in window || window.navigator.msMaxTouchPoints;

            base.browser = {
                "support3d" : support3d,
                "isTouch" : isTouch
            };
        },

        moveEvents : function () {
            var base = this;
            if (base.options.mouseDrag !== false || base.options.touchDrag !== false) {
                base.gestures();
                base.disabledEvents();
            }
        },

        eventTypes : function () {
            var base = this,
                types = ["s", "e", "x"];

            base.ev_types = {};

            if (base.options.mouseDrag === true && base.options.touchDrag === true) {
                types = [
                    "touchstart.owl mousedown.owl",
                    "touchmove.owl mousemove.owl",
                    "touchend.owl touchcancel.owl mouseup.owl"
                ];
            } else if (base.options.mouseDrag === false && base.options.touchDrag === true) {
                types = [
                    "touchstart.owl",
                    "touchmove.owl",
                    "touchend.owl touchcancel.owl"
                ];
            } else if (base.options.mouseDrag === true && base.options.touchDrag === false) {
                types = [
                    "mousedown.owl",
                    "mousemove.owl",
                    "mouseup.owl"
                ];
            }

            base.ev_types.start = types[0];
            base.ev_types.move = types[1];
            base.ev_types.end = types[2];
        },

        disabledEvents :  function () {
            var base = this;
            base.$elem.on("dragstart.owl", function (event) { event.preventDefault(); });
            base.$elem.on("mousedown.disableTextSelect", function (e) {
                return $(e.target).is('input, textarea, select, option');
            });
        },

        gestures : function () {
            /*jslint unparam: true*/
            var base = this,
                locals = {
                    offsetX : 0,
                    offsetY : 0,
                    baseElWidth : 0,
                    relativePos : 0,
                    position: null,
                    minSwipe : null,
                    maxSwipe: null,
                    sliding : null,
                    dargging: null,
                    targetElement : null
                };

            base.isCssFinish = true;

            function getTouches(event) {
                if (event.touches !== undefined) {
                    return {
                        x : event.touches[0].pageX,
                        y : event.touches[0].pageY
                    };
                }

                if (event.touches === undefined) {
                    if (event.pageX !== undefined) {
                        return {
                            x : event.pageX,
                            y : event.pageY
                        };
                    }
                    if (event.pageX === undefined) {
                        return {
                            x : event.clientX,
                            y : event.clientY
                        };
                    }
                }
            }

            function swapEvents(type) {
                if (type === "on") {
                    $(document).on(base.ev_types.move, dragMove);
                    $(document).on(base.ev_types.end, dragEnd);
                } else if (type === "off") {
                    $(document).off(base.ev_types.move);
                    $(document).off(base.ev_types.end);
                }
            }

            function dragStart(event) {
                var ev = event.originalEvent || event || window.event,
                    position;

                if (ev.which === 3) {
                    return false;
                }
                if (base.itemsAmount <= base.options.items) {
                    return;
                }
                if (base.isCssFinish === false && !base.options.dragBeforeAnimFinish) {
                    return false;
                }
                if (base.isCss3Finish === false && !base.options.dragBeforeAnimFinish) {
                    return false;
                }

                if (base.options.autoPlay !== false) {
                    window.clearInterval(base.autoPlayInterval);
                }

                if (base.browser.isTouch !== true && !base.$owlWrapper.hasClass("grabbing")) {
                    base.$owlWrapper.addClass("grabbing");
                }

                base.newPosX = 0;
                base.newRelativeX = 0;

                $(this).css(base.removeTransition());

                position = $(this).position();
                locals.relativePos = position.left;

                locals.offsetX = getTouches(ev).x - position.left;
                locals.offsetY = getTouches(ev).y - position.top;

                swapEvents("on");

                locals.sliding = false;
                locals.targetElement = ev.target || ev.srcElement;
            }

            function dragMove(event) {
                var ev = event.originalEvent || event || window.event,
                    minSwipe,
                    maxSwipe;

                base.newPosX = getTouches(ev).x - locals.offsetX;
                base.newPosY = getTouches(ev).y - locals.offsetY;
                base.newRelativeX = base.newPosX - locals.relativePos;

                if (typeof base.options.startDragging === "function" && locals.dragging !== true && base.newRelativeX !== 0) {
                    locals.dragging = true;
                    base.options.startDragging.apply(base, [base.$elem]);
                }

                if ((base.newRelativeX > 8 || base.newRelativeX < -8) && (base.browser.isTouch === true)) {
                    if (ev.preventDefault !== undefined) {
                        ev.preventDefault();
                    } else {
                        ev.returnValue = false;
                    }
                    locals.sliding = true;
                }

                if ((base.newPosY > 10 || base.newPosY < -10) && locals.sliding === false) {
                    $(document).off("touchmove.owl");
                }

                minSwipe = function () {
                    return base.newRelativeX / 5;
                };

                maxSwipe = function () {
                    return base.maximumPixels + base.newRelativeX / 5;
                };

                base.newPosX = Math.max(Math.min(base.newPosX, minSwipe()), maxSwipe());
                if (base.browser.support3d === true) {
                    base.transition3d(base.newPosX);
                } else {
                    base.css2move(base.newPosX);
                }
            }

            function dragEnd(event) {
                var ev = event.originalEvent || event || window.event,
                    newPosition,
                    handlers,
                    owlStopEvent;

                ev.target = ev.target || ev.srcElement;

                locals.dragging = false;

                if (base.browser.isTouch !== true) {
                    base.$owlWrapper.removeClass("grabbing");
                }

                if (base.newRelativeX < 0) {
                    base.dragDirection = base.owl.dragDirection = "left";
                } else {
                    base.dragDirection = base.owl.dragDirection = "right";
                }

                if (base.newRelativeX !== 0) {
                    newPosition = base.getNewPosition();
                    base.goTo(newPosition, false, "drag");
                    if (locals.targetElement === ev.target && base.browser.isTouch !== true) {
                        $(ev.target).on("click.disable", function (ev) {
                            ev.stopImmediatePropagation();
                            ev.stopPropagation();
                            ev.preventDefault();
                            $(ev.target).off("click.disable");
                        });
                        handlers = $._data(ev.target, "events").click;
                        owlStopEvent = handlers.pop();
                        handlers.splice(0, 0, owlStopEvent);
                    }
                }
                swapEvents("off");
            }
            base.$elem.on(base.ev_types.start, ".owl-wrapper", dragStart);
        },

        getNewPosition : function () {
            var base = this,
                newPosition = base.closestItem();

            if (newPosition > base.maximumItem) {
                base.currentItem = base.maximumItem;
                newPosition  = base.maximumItem;
            } else if (base.newPosX >= 0) {
                newPosition = 0;
                base.currentItem = 0;
            }
            return newPosition;
        },
        closestItem : function () {
            var base = this,
                array = base.options.scrollPerPage === true ? base.pagesInArray : base.positionsInArray,
                goal = base.newPosX,
                closest = null;

            $.each(array, function (i, v) {
                if (goal - (base.itemWidth / 20) > array[i + 1] && goal - (base.itemWidth / 20) < v && base.moveDirection() === "left") {
                    closest = v;
                    if (base.options.scrollPerPage === true) {
                        base.currentItem = $.inArray(closest, base.positionsInArray);
                    } else {
                        base.currentItem = i;
                    }
                } else if (goal + (base.itemWidth / 20) < v && goal + (base.itemWidth / 20) > (array[i + 1] || array[i] - base.itemWidth) && base.moveDirection() === "right") {
                    if (base.options.scrollPerPage === true) {
                        closest = array[i + 1] || array[array.length - 1];
                        base.currentItem = $.inArray(closest, base.positionsInArray);
                    } else {
                        closest = array[i + 1];
                        base.currentItem = i + 1;
                    }
                }
            });
            return base.currentItem;
        },

        moveDirection : function () {
            var base = this,
                direction;
            if (base.newRelativeX < 0) {
                direction = "right";
                base.playDirection = "next";
            } else {
                direction = "left";
                base.playDirection = "prev";
            }
            return direction;
        },

        customEvents : function () {
            /*jslint unparam: true*/
            var base = this;
            base.$elem.on("owl.next", function () {
                base.next();
            });
            base.$elem.on("owl.prev", function () {
                base.prev();
            });
            base.$elem.on("owl.play", function (event, speed) {
                base.options.autoPlay = speed;
                base.play();
                base.hoverStatus = "play";
            });
            base.$elem.on("owl.stop", function () {
                base.stop();
                base.hoverStatus = "stop";
            });
            base.$elem.on("owl.goTo", function (event, item) {
                base.goTo(item);
            });
            base.$elem.on("owl.jumpTo", function (event, item) {
                base.jumpTo(item);
            });
        },

        stopOnHover : function () {
            var base = this;
            if (base.options.stopOnHover === true && base.browser.isTouch !== true && base.options.autoPlay !== false) {
                base.$elem.on("mouseover", function () {
                    base.stop();
                });
                base.$elem.on("mouseout", function () {
                    if (base.hoverStatus !== "stop") {
                        base.play();
                    }
                });
            }
        },

        lazyLoad : function () {
            var base = this,
                i,
                $item,
                itemNumber,
                $lazyImg,
                follow;

            if (base.options.lazyLoad === false) {
                return false;
            }
            for (i = 0; i < base.itemsAmount; i += 1) {
                $item = $(base.$owlItems[i]);

                if ($item.data("owl-loaded") === "loaded") {
                    continue;
                }

                itemNumber = $item.data("owl-item");
                $lazyImg = $item.find(".lazyOwl");

                if (typeof $lazyImg.data("src") !== "string") {
                    $item.data("owl-loaded", "loaded");
                    continue;
                }
                if ($item.data("owl-loaded") === undefined) {
                    $lazyImg.hide();
                    $item.addClass("loading").data("owl-loaded", "checked");
                }
                if (base.options.lazyFollow === true) {
                    follow = itemNumber >= base.currentItem;
                } else {
                    follow = true;
                }
                if (follow && itemNumber < base.currentItem + base.options.items && $lazyImg.length) {
                    $lazyImg.each(function() {
                        base.lazyPreload($item, $(this));
                    });
                }
            }
        },

        lazyPreload : function ($item, $lazyImg) {
            var base = this,
                iterations = 0,
                isBackgroundImg;

            if ($lazyImg.prop("tagName") === "DIV") {
                $lazyImg.css("background-image", "url(" + $lazyImg.data("src") + ")");
                isBackgroundImg = true;
            } else {
                $lazyImg[0].src = $lazyImg.data("src");
            }

            function showImage() {
                $item.data("owl-loaded", "loaded").removeClass("loading");
                $lazyImg.removeAttr("data-src");
                if (base.options.lazyEffect === "fade") {
                    $lazyImg.fadeIn(400);
                } else {
                    $lazyImg.show();
                }
                if (typeof base.options.afterLazyLoad === "function") {
                    base.options.afterLazyLoad.apply(this, [base.$elem]);
                }
            }

            function checkLazyImage() {
                iterations += 1;
                if (base.completeImg($lazyImg.get(0)) || isBackgroundImg === true) {
                    showImage();
                } else if (iterations <= 100) {//if image loads in less than 10 seconds
                    window.setTimeout(checkLazyImage, 100);
                } else {
                    showImage();
                }
            }

            checkLazyImage();
        },

        autoHeight : function () {
            var base = this,
                $currentimg = $(base.$owlItems[base.currentItem]).find("img"),
                iterations;

            function addHeight() {
                var $currentItem = $(base.$owlItems[base.currentItem]).height();
                base.wrapperOuter.css("height", $currentItem + "px");
                if (!base.wrapperOuter.hasClass("autoHeight")) {
                    window.setTimeout(function () {
                        base.wrapperOuter.addClass("autoHeight");
                    }, 0);
                }
            }

            function checkImage() {
                iterations += 1;
                if (base.completeImg($currentimg.get(0))) {
                    addHeight();
                } else if (iterations <= 100) { //if image loads in less than 10 seconds
                    window.setTimeout(checkImage, 100);
                } else {
                    base.wrapperOuter.css("height", ""); //Else remove height attribute
                }
            }

            if ($currentimg.get(0) !== undefined) {
                iterations = 0;
                checkImage();
            } else {
                addHeight();
            }
        },

        completeImg : function (img) {
            var naturalWidthType;

            if (!img.complete) {
                return false;
            }
            naturalWidthType = typeof img.naturalWidth;
            if (naturalWidthType !== "undefined" && img.naturalWidth === 0) {
                return false;
            }
            return true;
        },

        onVisibleItems : function () {
            var base = this,
                i;

            if (base.options.addClassActive === true) {
                base.$owlItems.removeClass("active");
            }
            base.visibleItems = [];
            for (i = base.currentItem; i < base.currentItem + base.options.items; i += 1) {
                base.visibleItems.push(i);

                if (base.options.addClassActive === true) {
                    $(base.$owlItems[i]).addClass("active");
                }
            }
            base.owl.visibleItems = base.visibleItems;
        },

        transitionTypes : function (className) {
            var base = this;
            //Currently available: "fade", "backSlide", "goDown", "fadeUp"
            base.outClass = "owl-" + className + "-out";
            base.inClass = "owl-" + className + "-in";
        },

        singleItemTransition : function () {
            var base = this,
                outClass = base.outClass,
                inClass = base.inClass,
                $currentItem = base.$owlItems.eq(base.currentItem),
                $prevItem = base.$owlItems.eq(base.prevItem),
                prevPos = Math.abs(base.positionsInArray[base.currentItem]) + base.positionsInArray[base.prevItem],
                origin = Math.abs(base.positionsInArray[base.currentItem]) + base.itemWidth / 2,
                animEnd = 'webkitAnimationEnd oAnimationEnd MSAnimationEnd animationend';

            base.isTransition = true;

            base.$owlWrapper
                .addClass('owl-origin')
                .css({
                    "-webkit-transform-origin" : origin + "px",
                    "-moz-perspective-origin" : origin + "px",
                    "perspective-origin" : origin + "px"
                });
            function transStyles(prevPos) {
                return {
                    "position" : "relative",
                    "left" : prevPos + "px"
                };
            }

            $prevItem
                .css(transStyles(prevPos, 10))
                .addClass(outClass)
                .on(animEnd, function () {
                    base.endPrev = true;
                    $prevItem.off(animEnd);
                    base.clearTransStyle($prevItem, outClass);
                });

            $currentItem
                .addClass(inClass)
                .on(animEnd, function () {
                    base.endCurrent = true;
                    $currentItem.off(animEnd);
                    base.clearTransStyle($currentItem, inClass);
                });
        },

        clearTransStyle : function (item, classToRemove) {
            var base = this;
            item.css({
                "position" : "",
                "left" : ""
            }).removeClass(classToRemove);

            if (base.endPrev && base.endCurrent) {
                base.$owlWrapper.removeClass('owl-origin');
                base.endPrev = false;
                base.endCurrent = false;
                base.isTransition = false;
            }
        },

        owlStatus : function () {
            var base = this;
            base.owl = {
                "userOptions"   : base.userOptions,
                "baseElement"   : base.$elem,
                "userItems"     : base.$userItems,
                "owlItems"      : base.$owlItems,
                "currentItem"   : base.currentItem,
                "prevItem"      : base.prevItem,
                "visibleItems"  : base.visibleItems,
                "isTouch"       : base.browser.isTouch,
                "browser"       : base.browser,
                "dragDirection" : base.dragDirection
            };
        },

        clearEvents : function () {
            var base = this;
            base.$elem.off(".owl owl mousedown.disableTextSelect");
            $(document).off(".owl owl");
            $(window).off("resize", base.resizer);
        },

        unWrap : function () {
            var base = this;
            if (base.$elem.children().length !== 0) {
                base.$owlWrapper.unwrap();
                base.$userItems.unwrap().unwrap();
                if (base.owlControls) {
                    base.owlControls.remove();
                }
            }
            base.clearEvents();
            base.$elem.attr({
                style: base.$elem.data("owl-originalStyles") || "",
                class: base.$elem.data("owl-originalClasses")
            });
        },

        destroy : function () {
            var base = this;
            base.stop();
            window.clearInterval(base.checkVisible);
            base.unWrap();
            base.$elem.removeData();
        },

        reinit : function (newOptions) {
            var base = this,
                options = $.extend({}, base.userOptions, newOptions);
            base.unWrap();
            base.init(options, base.$elem);
        },

        addItem : function (htmlString, targetPosition) {
            var base = this,
                position;

            if (!htmlString) {return false; }

            if (base.$elem.children().length === 0) {
                base.$elem.append(htmlString);
                base.setVars();
                return false;
            }
            base.unWrap();
            if (targetPosition === undefined || targetPosition === -1) {
                position = -1;
            } else {
                position = targetPosition;
            }
            if (position >= base.$userItems.length || position === -1) {
                base.$userItems.eq(-1).after(htmlString);
            } else {
                base.$userItems.eq(position).before(htmlString);
            }

            base.setVars();
        },

        removeItem : function (targetPosition) {
            var base = this,
                position;

            if (base.$elem.children().length === 0) {
                return false;
            }
            if (targetPosition === undefined || targetPosition === -1) {
                position = -1;
            } else {
                position = targetPosition;
            }

            base.unWrap();
            base.$userItems.eq(position).remove();
            base.setVars();
        }

    };

    $.fn.owlCarousel = function (options) {
        return this.each(function () {
            if ($(this).data("owl-init") === true) {
                return false;
            }
            $(this).data("owl-init", true);
            var carousel = Object.create(Carousel);
            carousel.init(options, this);
            $.data(this, "owlCarousel", carousel);
        });
    };

    $.fn.owlCarousel.options = {

        items : 5,
        itemsCustom : false,
        itemsDesktop : [1199, 4],
        itemsDesktopSmall : [979, 3],
        itemsTablet : [768, 2],
        itemsTabletSmall : false,
        itemsMobile : [479, 1],
        singleItem : false,
        itemsScaleUp : false,

        slideSpeed : 200,
        paginationSpeed : 800,
        rewindSpeed : 1000,

        autoPlay : false,
        stopOnHover : false,

        navigation : false,
        navigationText : ["prev", "next"],
        rewindNav : true,
        scrollPerPage : false,

        pagination : true,
        paginationNumbers : false,

        responsive : true,
        responsiveRefreshRate : 200,
        responsiveBaseWidth : window,

        baseClass : "owl-carousel",
        theme : "owl-theme",

        lazyLoad : false,
        lazyFollow : true,
        lazyEffect : "fade",

        autoHeight : false,

        jsonPath : false,
        jsonSuccess : false,

        dragBeforeAnimFinish : true,
        mouseDrag : true,
        touchDrag : true,

        addClassActive : false,
        transitionStyle : false,

        beforeUpdate : false,
        afterUpdate : false,
        beforeInit : false,
        afterInit : false,
        beforeMove : false,
        afterMove : false,
        afterAction : false,
        startDragging : false,
        afterLazyLoad: false
    };
}(jQuery, window, document));(function($) {
    'use strict';

    var _currentSpinnerId = 0;

    function _scopedEventName(name, id) {
        return name + '.touchspin_' + id;
    }

    function _scopeEventNames(names, id) {
        return $.map(names, function(name) {
            return _scopedEventName(name, id);
        });
    }

    $.fn.TouchSpin = function(options) {

        if (options === 'destroy') {
            this.each(function() {
                var originalinput = $(this),
                    originalinput_data = originalinput.data();
                $(document).off(_scopeEventNames([
                    'mouseup',
                    'touchend',
                    'touchcancel',
                    'mousemove',
                    'touchmove',
                    'scroll',
                    'scrollstart'], originalinput_data.spinnerid).join(' '));
            });
            return;
        }

        var defaults = {
            min: 0,
            max: 100,
            initval: '',
            replacementval: '',
            step: 1,
            decimals: 0,
            stepinterval: 100,
            forcestepdivisibility: 'round', // none | floor | round | ceil
            stepintervaldelay: 500,
            verticalbuttons: false,
            verticalupclass: 'glyphicon glyphicon-chevron-up',
            verticaldownclass: 'glyphicon glyphicon-chevron-down',
            prefix: '',
            postfix: '',
            prefix_extraclass: '',
            postfix_extraclass: '',
            booster: true,
            boostat: 10,
            maxboostedstep: false,
            mousewheel: true,
            buttondown_class: 'btn btn-default',
            buttonup_class: 'btn btn-default',
            buttondown_txt: '-',
            buttonup_txt: '+'
        };

        var attributeMap = {
            min: 'min',
            max: 'max',
            initval: 'init-val',
            replacementval: 'replacement-val',
            step: 'step',
            decimals: 'decimals',
            stepinterval: 'step-interval',
            verticalbuttons: 'vertical-buttons',
            verticalupclass: 'vertical-up-class',
            verticaldownclass: 'vertical-down-class',
            forcestepdivisibility: 'force-step-divisibility',
            stepintervaldelay: 'step-interval-delay',
            prefix: 'prefix',
            postfix: 'postfix',
            prefix_extraclass: 'prefix-extra-class',
            postfix_extraclass: 'postfix-extra-class',
            booster: 'booster',
            boostat: 'boostat',
            maxboostedstep: 'max-boosted-step',
            mousewheel: 'mouse-wheel',
            buttondown_class: 'button-down-class',
            buttonup_class: 'button-up-class',
            buttondown_txt: 'button-down-txt',
            buttonup_txt: 'button-up-txt'
        };

        return this.each(function() {

            var settings,
                originalinput = $(this),
                originalinput_data = originalinput.data(),
                container,
                elements,
                value,
                downSpinTimer,
                upSpinTimer,
                downDelayTimeout,
                upDelayTimeout,
                spincount = 0,
                spinning = false;

            init();


            function init() {
                if (originalinput.data('alreadyinitialized')) {
                    return;
                }

                originalinput.data('alreadyinitialized', true);
                _currentSpinnerId += 1;
                originalinput.data('spinnerid', _currentSpinnerId);


                if (!originalinput.is('input')) {
                    console.log('Must be an input.');
                    return;
                }

                _initSettings();
                _setInitval();
                _checkValue();
                _buildHtml();
                _initElements();
                _hideEmptyPrefixPostfix();
                _bindEvents();
                _bindEventsInterface();
                elements.input.css('display', 'block');
            }

            function _setInitval() {
                if (settings.initval !== '' && originalinput.val() === '') {
                    originalinput.val(settings.initval);
                }
            }

            function changeSettings(newsettings) {
                _updateSettings(newsettings);
                _checkValue();

                var value = elements.input.val();

                if (value !== '') {
                    value = Number(elements.input.val());
                    elements.input.val(value.toFixed(settings.decimals));
                }
            }

            function _initSettings() {
                settings = $.extend({}, defaults, originalinput_data, _parseAttributes(), options);
            }

            function _parseAttributes() {
                var data = {};
                $.each(attributeMap, function(key, value) {
                    var attrName = 'bts-' + value + '';
                    if (originalinput.is('[data-' + attrName + ']')) {
                        data[key] = originalinput.data(attrName);
                    }
                });
                return data;
            }

            function _updateSettings(newsettings) {
                settings = $.extend({}, settings, newsettings);
            }

            function _buildHtml() {
                var initval = originalinput.val(),
                    parentelement = originalinput.parent();

                if (initval !== '') {
                    initval = Number(initval).toFixed(settings.decimals);
                }

                originalinput.data('initvalue', initval).val(initval);
                originalinput.addClass('form-control');

                if (parentelement.hasClass('input-group')) {
                    _advanceInputGroup(parentelement);
                }
                else {
                    _buildInputGroup();
                }
            }

            function _advanceInputGroup(parentelement) {
                parentelement.addClass('bootstrap-touchspin');

                var prev = originalinput.prev(),
                    next = originalinput.next();

                var downhtml,
                    uphtml,
                    prefixhtml = '<span class="input-group-addon bootstrap-touchspin-prefix">' + settings.prefix + '</span>',
                    postfixhtml = '<span class="input-group-addon bootstrap-touchspin-postfix">' + settings.postfix + '</span>';

                if (prev.hasClass('input-group-btn')) {
                    downhtml = '<button class="' + settings.buttondown_class + ' bootstrap-touchspin-down" type="button">' + settings.buttondown_txt + '</button>';
                    prev.append(downhtml);
                }
                else {
                    downhtml = '<span class="input-group-btn"><button class="' + settings.buttondown_class + ' bootstrap-touchspin-down" type="button">' + settings.buttondown_txt + '</button></span>';
                    $(downhtml).insertBefore(originalinput);
                }

                if (next.hasClass('input-group-btn')) {
                    uphtml = '<button class="' + settings.buttonup_class + ' bootstrap-touchspin-up" type="button">' + settings.buttonup_txt + '</button>';
                    next.prepend(uphtml);
                }
                else {
                    uphtml = '<span class="input-group-btn"><button class="' + settings.buttonup_class + ' bootstrap-touchspin-up" type="button">' + settings.buttonup_txt + '</button></span>';
                    $(uphtml).insertAfter(originalinput);
                }

                $(prefixhtml).insertBefore(originalinput);
                $(postfixhtml).insertAfter(originalinput);

                container = parentelement;
            }

            function _buildInputGroup() {
                var html;

                if (settings.verticalbuttons) {
                    html = '<div class="input-group bootstrap-touchspin"><span class="input-group-addon bootstrap-touchspin-prefix">' + settings.prefix + '</span><span class="input-group-addon bootstrap-touchspin-postfix">' + settings.postfix + '</span><span class="input-group-btn-vertical"><button class="' + settings.buttondown_class + ' bootstrap-touchspin-up" type="button"><i class="' + settings.verticalupclass + '"></i></button><button class="' + settings.buttonup_class + ' bootstrap-touchspin-down" type="button"><i class="' + settings.verticaldownclass + '"></i></button></span></div>';
                }
                else {
                    html = '<div class="input-group bootstrap-touchspin"><span class="input-group-btn"><button class="' + settings.buttondown_class + ' bootstrap-touchspin-down" type="button">' + settings.buttondown_txt + '</button></span><span class="input-group-addon bootstrap-touchspin-prefix">' + settings.prefix + '</span><span class="input-group-addon bootstrap-touchspin-postfix">' + settings.postfix + '</span><span class="input-group-btn"><button class="' + settings.buttonup_class + ' bootstrap-touchspin-up" type="button">' + settings.buttonup_txt + '</button></span></div>';
                }

                container = $(html).insertBefore(originalinput);

                $('.bootstrap-touchspin-prefix', container).after(originalinput);

                if (originalinput.hasClass('input-sm')) {
                    container.addClass('input-group-sm');
                }
                else if (originalinput.hasClass('input-lg')) {
                    container.addClass('input-group-lg');
                }
            }

            function _initElements() {
                elements = {
                    down: $('.bootstrap-touchspin-down', container),
                    up: $('.bootstrap-touchspin-up', container),
                    input: $('input', container),
                    prefix: $('.bootstrap-touchspin-prefix', container).addClass(settings.prefix_extraclass),
                    postfix: $('.bootstrap-touchspin-postfix', container).addClass(settings.postfix_extraclass)
                };
            }

            function _hideEmptyPrefixPostfix() {
                if (settings.prefix === '') {
                    elements.prefix.hide();
                }

                if (settings.postfix === '') {
                    elements.postfix.hide();
                }
            }

            function _bindEvents() {
                originalinput.on('keydown', function(ev) {
                    var code = ev.keyCode || ev.which;

                    if (code === 38) {
                        if (spinning !== 'up') {
                            upOnce();
                            startUpSpin();
                        }
                        ev.preventDefault();
                    }
                    else if (code === 40) {
                        if (spinning !== 'down') {
                            downOnce();
                            startDownSpin();
                        }
                        ev.preventDefault();
                    }
                });

                originalinput.on('keyup', function(ev) {
                    var code = ev.keyCode || ev.which;

                    if (code === 38) {
                        stopSpin();
                    }
                    else if (code === 40) {
                        stopSpin();
                    }
                });

                originalinput.on('blur', function() {
                    _checkValue();
                });

                elements.down.on('keydown', function(ev) {
                    var code = ev.keyCode || ev.which;

                    if (code === 32 || code === 13) {
                        if (spinning !== 'down') {
                            downOnce();
                            startDownSpin();
                        }
                        ev.preventDefault();
                    }
                });

                elements.down.on('keyup', function(ev) {
                    var code = ev.keyCode || ev.which;

                    if (code === 32 || code === 13) {
                        stopSpin();
                    }
                });

                elements.up.on('keydown', function(ev) {
                    var code = ev.keyCode || ev.which;

                    if (code === 32 || code === 13) {
                        if (spinning !== 'up') {
                            upOnce();
                            startUpSpin();
                        }
                        ev.preventDefault();
                    }
                });

                elements.up.on('keyup', function(ev) {
                    var code = ev.keyCode || ev.which;

                    if (code === 32 || code === 13) {
                        stopSpin();
                    }
                });

                elements.down.on('mousedown.touchspin', function(ev) {
                    elements.down.off('touchstart.touchspin');  // android 4 workaround

                    if (originalinput.is(':disabled')) {
                        return;
                    }

                    downOnce();
                    startDownSpin();

                    ev.preventDefault();
                    ev.stopPropagation();
                });

                elements.down.on('touchstart.touchspin', function(ev) {
                    elements.down.off('mousedown.touchspin');  // android 4 workaround

                    if (originalinput.is(':disabled')) {
                        return;
                    }

                    downOnce();
                    startDownSpin();

                    ev.preventDefault();
                    ev.stopPropagation();
                });

                elements.up.on('mousedown.touchspin', function(ev) {
                    elements.up.off('touchstart.touchspin');  // android 4 workaround

                    if (originalinput.is(':disabled')) {
                        return;
                    }

                    upOnce();
                    startUpSpin();

                    ev.preventDefault();
                    ev.stopPropagation();
                });

                elements.up.on('touchstart.touchspin', function(ev) {
                    elements.up.off('mousedown.touchspin');  // android 4 workaround

                    if (originalinput.is(':disabled')) {
                        return;
                    }

                    upOnce();
                    startUpSpin();

                    ev.preventDefault();
                    ev.stopPropagation();
                });

                elements.up.on('mouseout touchleave touchend touchcancel', function(ev) {
                    if (!spinning) {
                        return;
                    }

                    ev.stopPropagation();
                    stopSpin();
                });

                elements.down.on('mouseout touchleave touchend touchcancel', function(ev) {
                    if (!spinning) {
                        return;
                    }

                    ev.stopPropagation();
                    stopSpin();
                });

                elements.down.on('mousemove touchmove', function(ev) {
                    if (!spinning) {
                        return;
                    }

                    ev.stopPropagation();
                    ev.preventDefault();
                });

                elements.up.on('mousemove touchmove', function(ev) {
                    if (!spinning) {
                        return;
                    }

                    ev.stopPropagation();
                    ev.preventDefault();
                });

                $(document).on(_scopeEventNames(['mouseup', 'touchend', 'touchcancel'], _currentSpinnerId).join(' '), function(ev) {
                    if (!spinning) {
                        return;
                    }

                    ev.preventDefault();
                    stopSpin();
                });

                $(document).on(_scopeEventNames(['mousemove', 'touchmove', 'scroll', 'scrollstart'], _currentSpinnerId).join(' '), function(ev) {
                    if (!spinning) {
                        return;
                    }

                    ev.preventDefault();
                    stopSpin();
                });

                originalinput.on('mousewheel DOMMouseScroll', function(ev) {
                    if (!settings.mousewheel || !originalinput.is(':focus')) {
                        return;
                    }

                    var delta = ev.originalEvent.wheelDelta || -ev.originalEvent.deltaY || -ev.originalEvent.detail;

                    ev.stopPropagation();
                    ev.preventDefault();

                    if (delta < 0) {
                        downOnce();
                    }
                    else {
                        upOnce();
                    }
                });
            }

            function _bindEventsInterface() {
                originalinput.on('touchspin.uponce', function() {
                    stopSpin();
                    upOnce();
                });

                originalinput.on('touchspin.downonce', function() {
                    stopSpin();
                    downOnce();
                });

                originalinput.on('touchspin.startupspin', function() {
                    startUpSpin();
                });

                originalinput.on('touchspin.startdownspin', function() {
                    startDownSpin();
                });

                originalinput.on('touchspin.stopspin', function() {
                    stopSpin();
                });

                originalinput.on('touchspin.updatesettings', function(e, newsettings) {
                    changeSettings(newsettings);
                });
            }

            function _forcestepdivisibility(value) {
                switch (settings.forcestepdivisibility) {
                    case 'round':
                        return (Math.round(value / settings.step) * settings.step).toFixed(settings.decimals);
                    case 'floor':
                        return (Math.floor(value / settings.step) * settings.step).toFixed(settings.decimals);
                    case 'ceil':
                        return (Math.ceil(value / settings.step) * settings.step).toFixed(settings.decimals);
                    default:
                        return value;
                }
            }

            function _checkValue() {
                var val, parsedval, returnval;

                val = originalinput.val();

                if (val === '') {
                    if (settings.replacementval !== '') {
                        originalinput.val(settings.replacementval);
                        originalinput.trigger('change');
                    }
                    return;
                }

                if (settings.decimals > 0 && val === '.') {
                    return;
                }

                parsedval = parseFloat(val);

                if (isNaN(parsedval)) {
                    if (settings.replacementval !== '') {
                        parsedval = settings.replacementval;
                    }
                    else {
                        parsedval = 0;
                    }
                }

                returnval = parsedval;

                if (parsedval.toString() !== val) {
                    returnval = parsedval;
                }

                if (parsedval < settings.min) {
                    returnval = settings.min;
                }

                if (parsedval > settings.max) {
                    returnval = settings.max;
                }

                returnval = _forcestepdivisibility(returnval);

                if (Number(val).toString() !== returnval.toString()) {
                    originalinput.val(returnval);
                    originalinput.trigger('change');
                }
            }

            function _getBoostedStep() {
                if (!settings.booster) {
                    return settings.step;
                }
                else {
                    var boosted = Math.pow(2, Math.floor(spincount / settings.boostat)) * settings.step;

                    if (settings.maxboostedstep) {
                        if (boosted > settings.maxboostedstep) {
                            boosted = settings.maxboostedstep;
                            value = Math.round((value / boosted)) * boosted;
                        }
                    }

                    return Math.max(settings.step, boosted);
                }
            }

            function upOnce() {
                _checkValue();

                value = parseFloat(elements.input.val());
                if (isNaN(value)) {
                    value = 0;
                }

                var initvalue = value,
                    boostedstep = _getBoostedStep();

                value = value + boostedstep;

                if (value > settings.max) {
                    value = settings.max;
                    originalinput.trigger('touchspin.on.max');
                    stopSpin();
                }

                elements.input.val(Number(value).toFixed(settings.decimals));

                if (initvalue !== value) {
                    originalinput.trigger('change');
                }
            }

            function downOnce() {
                _checkValue();

                value = parseFloat(elements.input.val());
                if (isNaN(value)) {
                    value = 0;
                }

                var initvalue = value,
                    boostedstep = _getBoostedStep();

                value = value - boostedstep;

                if (value < settings.min) {
                    value = settings.min;
                    originalinput.trigger('touchspin.on.min');
                    stopSpin();
                }

                elements.input.val(value.toFixed(settings.decimals));

                if (initvalue !== value) {
                    originalinput.trigger('change');
                }
            }

            function startDownSpin() {
                stopSpin();

                spincount = 0;
                spinning = 'down';

                originalinput.trigger('touchspin.on.startspin');
                originalinput.trigger('touchspin.on.startdownspin');

                downDelayTimeout = setTimeout(function() {
                    downSpinTimer = setInterval(function() {
                        spincount++;
                        downOnce();
                    }, settings.stepinterval);
                }, settings.stepintervaldelay);
            }

            function startUpSpin() {
                stopSpin();

                spincount = 0;
                spinning = 'up';

                originalinput.trigger('touchspin.on.startspin');
                originalinput.trigger('touchspin.on.startupspin');

                upDelayTimeout = setTimeout(function() {
                    upSpinTimer = setInterval(function() {
                        spincount++;
                        upOnce();
                    }, settings.stepinterval);
                }, settings.stepintervaldelay);
            }

            function stopSpin() {
                clearTimeout(downDelayTimeout);
                clearTimeout(upDelayTimeout);
                clearInterval(downSpinTimer);
                clearInterval(upSpinTimer);

                switch (spinning) {
                    case 'up':
                        originalinput.trigger('touchspin.on.stopupspin');
                        originalinput.trigger('touchspin.on.stopspin');
                        break;
                    case 'down':
                        originalinput.trigger('touchspin.on.stopdownspin');
                        originalinput.trigger('touchspin.on.stopspin');
                        break;
                }

                spincount = 0;
                spinning = false;
            }

        });

    };

})(jQuery);
/*!
 * @copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @version 3.2.0
 *
 * A simple yet powerful JQuery star rating plugin that allows rendering
 * fractional star ratings and supports Right to Left (RTL) input.
 *
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */!function(e){var t=0,a=5,n=.5,r="ontouchstart"in window||window.DocumentTouch&&document instanceof window.DocumentTouch,l=function(t,a){return"undefined"==typeof t||null===t||void 0===t||t==[]||""===t||a&&""===e.trim(t)},i=function(e,t,a){var n=l(e.data(t))?e.attr(t):e.data(t);return n?n:a[t]},s=function(e){var t=(""+e).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);return t?Math.max(0,(t[1]?t[1].length:0)-(t[2]?+t[2]:0)):0},o=function(e,t){return parseFloat(e.toFixed(t))},c=function(t,a){this.$element=e(t),this.init(a)};c.prototype={constructor:c,_parseAttr:function(e,r){var s=this,o=s.$element;if("range"===o.attr("type")||"number"===o.attr("type")){var c=i(o,e,r),u=n;"min"===e?u=t:"max"===e?u=a:"step"===e&&(u=n);var p=l(c)?u:c;return parseFloat(p)}return parseFloat(r[e])},listen:function(){var t=this;t.initTouch(),t.$rating.on("click",function(e){if(!t.inactive){var a=e.pageX-t.$rating.offset().left;t.setStars(a),t.$element.trigger("change"),t.$element.trigger("rating.change",[t.$element.val(),t.$caption.html()]),t.starClicked=!0}}),t.$rating.on("mousemove",function(e){if(t.hoverEnabled&&!t.inactive){t.starClicked=!1;var a=e.pageX-t.$rating.offset().left,n=t.calculate(a);t.toggleHover(n),t.$element.trigger("rating.hover",[n.val,n.caption,"stars"])}}),t.$rating.on("mouseleave",function(){if(t.hoverEnabled&&!t.inactive&&!t.starClicked){var e=t.cache;t.toggleHover(e),t.$element.trigger("rating.hoverleave",["stars"])}}),t.$clear.on("mousemove",function(){if(t.hoverEnabled&&!t.inactive&&t.hoverOnClear){t.clearClicked=!1;var e,a='<span class="'+t.clearCaptionClass+'">'+t.clearCaption+"</span>",n=t.clearValue,r=t.getWidthFromValue(n);e={caption:a,width:r,val:n},t.toggleHover(e),t.$element.trigger("rating.hover",[n,a,"clear"])}}),t.$clear.on("mouseleave",function(){if(t.hoverEnabled&&!t.inactive&&!t.clearClicked&&t.hoverOnClear){var e=t.cache;t.toggleHover(e),t.$element.trigger("rating.hoverleave",["clear"])}}),t.$clear.on("click",function(){t.inactive||(t.clear(),t.clearClicked=!0)}),e(t.$element[0].form).on("reset",function(){t.inactive||t.reset()})},setTouch:function(e,t){var a=this;if(r&&!a.inactive){var n=e.originalEvent.touches[0].pageX-a.$rating.offset().left;if(t===!0)a.setStars(n),a.$element.trigger("change"),a.$element.trigger("rating.change",[a.$element.val(),a.$caption.html()]),a.starClicked=!0;else{var l=a.calculate(n),i=l.val<=a.clearValue?a.fetchCaption(a.clearValue):l.caption,s=a.getWidthFromValue(a.clearValue),o=l.val<=a.clearValue?a.rtl?100-s+"%":s+"%":l.width;a.$caption.html(i),a.$stars.css("width",o)}}},initTouch:function(){var e=this;e.$rating.on("touchstart",function(t){e.setTouch(t,!1)}),e.$rating.on("touchmove",function(t){e.setTouch(t,!1)}),e.$rating.on("touchend",function(t){e.setTouch(t,!0)})},initSlider:function(e){var r=this;l(r.$element.val())&&r.$element.val(0),r.initialValue=r.$element.val(),r.min="undefined"!=typeof e.min?e.min:r._parseAttr("min",e),r.max="undefined"!=typeof e.max?e.max:r._parseAttr("max",e),r.step="undefined"!=typeof e.step?e.step:r._parseAttr("step",e),(isNaN(r.min)||l(r.min))&&(r.min=t),(isNaN(r.max)||l(r.max))&&(r.max=a),(isNaN(r.step)||l(r.step)||0==r.step)&&(r.step=n),r.diff=r.max-r.min},init:function(t){var a=this;a.options=t,a.hoverEnabled=t.hoverEnabled,a.hoverChangeCaption=t.hoverChangeCaption,a.hoverChangeStars=t.hoverChangeStars,a.hoverOnClear=t.hoverOnClear,a.starClicked=!1,a.clearClicked=!1,a.initSlider(t),a.checkDisabled(),$element=a.$element,a.containerClass=t.containerClass,a.glyphicon=t.glyphicon;var n=a.glyphicon?"î€†":"â˜…";a.symbol=l(t.symbol)?n:t.symbol,a.rtl=t.rtl||a.$element.attr("dir"),a.rtl&&a.$element.attr("dir","rtl"),a.showClear=t.showClear,a.showCaption=t.showCaption,a.size=t.size,a.stars=t.stars,a.defaultCaption=t.defaultCaption,a.starCaptions=t.starCaptions,a.starCaptionClasses=t.starCaptionClasses,a.clearButton=t.clearButton,a.clearButtonTitle=t.clearButtonTitle,a.clearButtonBaseClass=l(t.clearButtonBaseClass)?"clear-rating":t.clearButtonBaseClass,a.clearButtonActiveClass=l(t.clearButtonActiveClass)?"clear-rating-active":t.clearButtonActiveClass,a.clearCaption=t.clearCaption,a.clearCaptionClass=t.clearCaptionClass,a.clearValue=l(t.clearValue)?a.min:t.clearValue,a.$element.removeClass("form-control").addClass("form-control"),a.$clearElement=l(t.clearElement)?null:e(t.clearElement),a.$captionElement=l(t.captionElement)?null:e(t.captionElement),"undefined"==typeof a.$rating&&"undefined"==typeof a.$container&&(a.$rating=e(document.createElement("div")).html('<div class="rating-stars"></div>'),a.$container=e(document.createElement("div")),a.$container.before(a.$rating),a.$container.append(a.$rating),a.$element.before(a.$container).appendTo(a.$rating)),a.$stars=a.$rating.find(".rating-stars"),a.generateRating(),a.$clear=l(a.$clearElement)?a.$container.find("."+a.clearButtonBaseClass):a.$clearElement,a.$caption=l(a.$captionElement)?a.$container.find(".caption"):a.$captionElement,a.setStars(),a.$element.hide(),a.listen(),a.showClear&&a.$clear.attr({"class":a.getClearClass()}),a.cache={caption:a.$caption.html(),width:a.$stars.width(),val:a.$element.val()},a.$element.removeClass("rating-loading")},checkDisabled:function(){var e=this;e.disabled=i(e.$element,"disabled",e.options),e.readonly=i(e.$element,"readonly",e.options),e.inactive=e.disabled||e.readonly},getClearClass:function(){return this.clearButtonBaseClass+" "+(this.inactive?"":this.clearButtonActiveClass)},generateRating:function(){var e=this,t=e.renderClear(),a=e.renderCaption(),n=e.rtl?"rating-container-rtl":"rating-container",r=e.getStars();n+=e.glyphicon?"î€†"==e.symbol?" rating-gly-star":" rating-gly":" rating-uni",e.$rating.attr("class",n),e.$rating.attr("data-content",r),e.$stars.attr("data-content",r);var n=e.rtl?"star-rating-rtl":"star-rating";e.$container.attr("class",n+" rating-"+e.size),e.inactive?e.$container.removeClass("rating-active").addClass("rating-disabled"):e.$container.removeClass("rating-disabled").addClass("rating-active"),"undefined"==typeof e.$caption&&"undefined"==typeof e.$clear&&(e.rtl?e.$container.prepend(a).append(t):e.$container.prepend(t).append(a)),l(e.containerClass)||e.$container.removeClass(e.containerClass).addClass(e.containerClass)},getStars:function(){for(var e=this,t=e.stars,a="",n=1;t>=n;n++)a+=e.symbol;return a},renderClear:function(){var e=this;if(!e.showClear)return"";var t=e.getClearClass();return l(e.$clearElement)?'<div class="'+t+'" title="'+e.clearButtonTitle+'">'+e.clearButton+"</div>":(e.$clearElement.removeClass(t).addClass(t).attr({title:e.clearButtonTitle}),e.$clearElement.html(e.clearButton),"")},renderCaption:function(){var e=this,t=e.$element.val();if(!e.showCaption)return"";var a=e.fetchCaption(t);return l(e.$captionElement)?'<div class="caption">'+a+"</div>":(e.$captionElement.removeClass("caption").addClass("caption").attr({title:e.clearCaption}),e.$captionElement.html(a),"")},fetchCaption:function(e){var t,a,n=this,r=parseFloat(e);if(t="function"==typeof n.starCaptionClasses?l(n.starCaptionClasses(r))?n.clearCaptionClass:n.starCaptionClasses(r):l(n.starCaptionClasses[r])?n.clearCaptionClass:n.starCaptionClasses[r],"function"==typeof n.starCaptions)var a=l(n.starCaptions(r))?n.defaultCaption.replace(/\{rating\}/g,r):n.starCaptions(r);else var a=l(n.starCaptions[r])?n.defaultCaption.replace(/\{rating\}/g,r):n.starCaptions[r];var i=r==n.clearValue?n.clearCaption:a;return'<span class="'+t+'">'+i+"</span>"},getWidthFromValue:function(e){{var t=this,a=t.min,n=t.max;t.step}return a>=e||a==n?0:e>=n?100:100*(e-a)/(n-a)},getValueFromPosition:function(e){var t,a,n=this,r=s(n.step),l=n.$rating.width();return t=e/l,a=n.rtl?n.min+Math.floor(n.diff*t/n.step)*n.step:n.min+Math.ceil(n.diff*t/n.step)*n.step,a<n.min?a=n.min:a>n.max&&(a=n.max),a=o(parseFloat(a),r),n.rtl&&(a=n.max-a),a},toggleHover:function(e){var t=this;if(t.hoverChangeCaption){var a=e.val<=t.clearValue?t.fetchCaption(t.clearValue):e.caption;t.$caption.html(a)}if(t.hoverChangeStars){var n=t.getWidthFromValue(t.clearValue),r=e.val<=t.clearValue?t.rtl?100-n+"%":n+"%":e.width;t.$stars.css("width",r)}},calculate:function(e){var t=this,a=l(t.$element.val())?0:t.$element.val(),n=arguments.length?t.getValueFromPosition(e):a,r=t.fetchCaption(n),i=t.getWidthFromValue(n);return t.rtl&&(i=100-i),i+="%",{caption:r,width:i,val:n}},setStars:function(e){var t=this,a=arguments.length?t.calculate(e):t.calculate();t.$element.val(a.val),t.$stars.css("width",a.width),t.$caption.html(a.caption),t.cache=a},clear:function(){var e=this,t='<span class="'+e.clearCaptionClass+'">'+e.clearCaption+"</span>";e.$stars.removeClass("rated"),e.inactive||e.$caption.html(t),e.$element.val(e.clearValue),e.setStars(),e.$element.trigger("rating.clear")},reset:function(){var e=this;e.$element.val(e.initialValue),e.setStars(),e.$element.trigger("rating.reset")},update:function(e){if(arguments.length>0){var t=this;t.$element.val(e),t.setStars()}},refresh:function(t){var a=this;arguments.length&&(a.$rating.off(),a.$clear.off(),a.init(e.extend(a.options,t)),a.showClear?a.$clear.show():a.$clear.hide(),a.showCaption?a.$caption.show():a.$caption.hide(),a.$element.trigger("rating.refresh"))}},e.fn.rating=function(t){var a=Array.apply(null,arguments);return a.shift(),this.each(function(){var n=e(this),r=n.data("rating"),l="object"==typeof t&&t;r||n.data("rating",r=new c(this,e.extend({},e.fn.rating.defaults,l,e(this).data()))),"string"==typeof t&&r[t].apply(r,a)})},e.fn.rating.defaults={stars:5,glyphicon:!0,symbol:null,disabled:!1,readonly:!1,rtl:!1,size:"md",showClear:!0,showCaption:!0,defaultCaption:"{rating} Stars",starCaptions:{.5:"Half Star",1:"One Star",1.5:"One & Half Star",2:"Two Stars",2.5:"Two & Half Stars",3:"Three Stars",3.5:"Three & Half Stars",4:"Four Stars",4.5:"Four & Half Stars",5:"Five Stars"},starCaptionClasses:{.5:"label label-danger",1:"label label-danger",1.5:"label label-warning",2:"label label-warning",2.5:"label label-info",3:"label label-info",3.5:"label label-primary",4:"label label-primary",4.5:"label label-success",5:"label label-success"},clearButton:'<i class="glyphicon glyphicon-minus-sign"></i>',clearButtonTitle:"Clear",clearButtonBaseClass:"clear-rating",clearButtonActiveClass:"clear-rating-active",clearCaption:"Not Rated",clearCaptionClass:"label label-default",clearValue:null,captionElement:null,clearElement:null,containerClass:null,hoverEnabled:!0,hoverChangeCaption:!0,hoverChangeStars:!0,hoverOnClear:!0},e("input.rating").addClass("rating-loading"),e(document).ready(function(){var t=e("input.rating"),a=Object.keys(t).length;a>0&&t.rating()})}(jQuery);/*! http://mths.be/placeholder v2.0.8 by @mathias */
;(function(window, document, $) {

    // Opera Mini v7 doesnâ€™t support placeholder although its DOM seems to indicate so
    var isOperaMini = Object.prototype.toString.call(window.operamini) == '[object OperaMini]';
    var isInputSupported = 'placeholder' in document.createElement('input') && !isOperaMini;
    var isTextareaSupported = 'placeholder' in document.createElement('textarea') && !isOperaMini;
    var prototype = $.fn;
    var valHooks = $.valHooks;
    var propHooks = $.propHooks;
    var hooks;
    var placeholder;

    if (isInputSupported && isTextareaSupported) {

        placeholder = prototype.placeholder = function() {
            return this;
        };

        placeholder.input = placeholder.textarea = true;

    } else {

        placeholder = prototype.placeholder = function() {
            var $this = this;
            $this
                .filter((isInputSupported ? 'textarea' : ':input') + '[placeholder]')
                .not('.placeholder')
                .bind({
                    'focus.placeholder': clearPlaceholder,
                    'blur.placeholder': setPlaceholder
                })
                .data('placeholder-enabled', true)
                .trigger('blur.placeholder');
            return $this;
        };

        placeholder.input = isInputSupported;
        placeholder.textarea = isTextareaSupported;

        hooks = {
            'get': function(element) {
                var $element = $(element);

                var $passwordInput = $element.data('placeholder-password');
                if ($passwordInput) {
                    return $passwordInput[0].value;
                }

                return $element.data('placeholder-enabled') && $element.hasClass('placeholder') ? '' : element.value;
            },
            'set': function(element, value) {
                var $element = $(element);

                var $passwordInput = $element.data('placeholder-password');
                if ($passwordInput) {
                    return $passwordInput[0].value = value;
                }

                if (!$element.data('placeholder-enabled')) {
                    return element.value = value;
                }
                if (value == '') {
                    element.value = value;
                    // Issue #56: Setting the placeholder causes problems if the element continues to have focus.
                    if (element != safeActiveElement()) {
                        // We can't use `triggerHandler` here because of dummy text/password inputs :(
                        setPlaceholder.call(element);
                    }
                } else if ($element.hasClass('placeholder')) {
                    clearPlaceholder.call(element, true, value) || (element.value = value);
                } else {
                    element.value = value;
                }
                // `set` can not return `undefined`; see http://jsapi.info/jquery/1.7.1/val#L2363
                return $element;
            }
        };

        if (!isInputSupported) {
            valHooks.input = hooks;
            propHooks.value = hooks;
        }
        if (!isTextareaSupported) {
            valHooks.textarea = hooks;
            propHooks.value = hooks;
        }

        $(function() {
            // Look for forms
            $(document).delegate('form', 'submit.placeholder', function() {
                // Clear the placeholder values so they don't get submitted
                var $inputs = $('.placeholder', this).each(clearPlaceholder);
                setTimeout(function() {
                    $inputs.each(setPlaceholder);
                }, 10);
            });
        });

        // Clear placeholder values upon page reload
        $(window).bind('beforeunload.placeholder', function() {
            $('.placeholder').each(function() {
                this.value = '';
            });
        });

    }

    function args(elem) {
        // Return an object of element attributes
        var newAttrs = {};
        var rinlinejQuery = /^jQuery\d+$/;
        $.each(elem.attributes, function(i, attr) {
            if (attr.specified && !rinlinejQuery.test(attr.name)) {
                newAttrs[attr.name] = attr.value;
            }
        });
        return newAttrs;
    }

    function clearPlaceholder(event, value) {
        var input = this;
        var $input = $(input);
        if (input.value == $input.attr('placeholder') && $input.hasClass('placeholder')) {
            if ($input.data('placeholder-password')) {
                $input = $input.hide().next().show().attr('id', $input.removeAttr('id').data('placeholder-id'));
                // If `clearPlaceholder` was called from `$.valHooks.input.set`
                if (event === true) {
                    return $input[0].value = value;
                }
                $input.focus();
            } else {
                input.value = '';
                $input.removeClass('placeholder');
                input == safeActiveElement() && input.select();
            }
        }
    }

    function setPlaceholder() {
        var $replacement;
        var input = this;
        var $input = $(input);
        var id = this.id;
        if (input.value == '') {
            if (input.type == 'password') {
                if (!$input.data('placeholder-textinput')) {
                    try {
                        $replacement = $input.clone().attr({ 'type': 'text' });
                    } catch(e) {
                        $replacement = $('<input>').attr($.extend(args(this), { 'type': 'text' }));
                    }
                    $replacement
                        .removeAttr('name')
                        .data({
                            'placeholder-password': $input,
                            'placeholder-id': id
                        })
                        .bind('focus.placeholder', clearPlaceholder);
                    $input
                        .data({
                            'placeholder-textinput': $replacement,
                            'placeholder-id': id
                        })
                        .before($replacement);
                }
                $input = $input.removeAttr('id').hide().prev().attr('id', id).show();
                // Note: `$input[0] != input` now!
            }
            $input.addClass('placeholder');
            $input[0].value = $input.attr('placeholder');
        } else {
            $input.removeClass('placeholder');
        }
    }

    function safeActiveElement() {
        // Avoid IE9 `document.activeElement` of death
        // https://github.com/mathiasbynens/jquery-placeholder/pull/99
        try {
            return document.activeElement;
        } catch (exception) {}
    }

}(this, document, jQuery));
/*!
	Colorbox 1.5.14
	license: MIT
	http://www.jacklmoore.com/colorbox
*/
(function(t,e,i){function n(i,n,o){var r=e.createElement(i);return n&&(r.id=Z+n),o&&(r.style.cssText=o),t(r)}function o(){return i.innerHeight?i.innerHeight:t(i).height()}function r(e,i){i!==Object(i)&&(i={}),this.cache={},this.el=e,this.value=function(e){var n;return void 0===this.cache[e]&&(n=t(this.el).attr("data-cbox-"+e),void 0!==n?this.cache[e]=n:void 0!==i[e]?this.cache[e]=i[e]:void 0!==X[e]&&(this.cache[e]=X[e])),this.cache[e]},this.get=function(e){var i=this.value(e);return t.isFunction(i)?i.call(this.el,this):i}}function h(t){var e=W.length,i=(z+t)%e;return 0>i?e+i:i}function a(t,e){return Math.round((/%/.test(t)?("x"===e?E.width():o())/100:1)*parseInt(t,10))}function s(t,e){return t.get("photo")||t.get("photoRegex").test(e)}function l(t,e){return t.get("retinaUrl")&&i.devicePixelRatio>1?e.replace(t.get("photoRegex"),t.get("retinaSuffix")):e}function d(t){"contains"in y[0]&&!y[0].contains(t.target)&&t.target!==v[0]&&(t.stopPropagation(),y.focus())}function c(t){c.str!==t&&(y.add(v).removeClass(c.str).addClass(t),c.str=t)}function g(e){z=0,e&&e!==!1&&"nofollow"!==e?(W=t("."+te).filter(function(){var i=t.data(this,Y),n=new r(this,i);return n.get("rel")===e}),z=W.index(_.el),-1===z&&(W=W.add(_.el),z=W.length-1)):W=t(_.el)}function u(i){t(e).trigger(i),ae.triggerHandler(i)}function f(i){var o;if(!G){if(o=t(i).data(Y),_=new r(i,o),g(_.get("rel")),!$){$=q=!0,c(_.get("className")),y.css({visibility:"hidden",display:"block",opacity:""}),L=n(se,"LoadedContent","width:0; height:0; overflow:hidden; visibility:hidden"),b.css({width:"",height:""}).append(L),D=T.height()+k.height()+b.outerHeight(!0)-b.height(),j=C.width()+H.width()+b.outerWidth(!0)-b.width(),A=L.outerHeight(!0),N=L.outerWidth(!0);var h=a(_.get("initialWidth"),"x"),s=a(_.get("initialHeight"),"y"),l=_.get("maxWidth"),f=_.get("maxHeight");_.w=(l!==!1?Math.min(h,a(l,"x")):h)-N-j,_.h=(f!==!1?Math.min(s,a(f,"y")):s)-A-D,L.css({width:"",height:_.h}),J.position(),u(ee),_.get("onOpen"),O.add(F).hide(),y.focus(),_.get("trapFocus")&&e.addEventListener&&(e.addEventListener("focus",d,!0),ae.one(re,function(){e.removeEventListener("focus",d,!0)})),_.get("returnFocus")&&ae.one(re,function(){t(_.el).focus()})}var p=parseFloat(_.get("opacity"));v.css({opacity:p===p?p:"",cursor:_.get("overlayClose")?"pointer":"",visibility:"visible"}).show(),_.get("closeButton")?B.html(_.get("close")).appendTo(b):B.appendTo("<div/>"),w()}}function p(){y||(V=!1,E=t(i),y=n(se).attr({id:Y,"class":t.support.opacity===!1?Z+"IE":"",role:"dialog",tabindex:"-1"}).hide(),v=n(se,"Overlay").hide(),S=t([n(se,"LoadingOverlay")[0],n(se,"LoadingGraphic")[0]]),x=n(se,"Wrapper"),b=n(se,"Content").append(F=n(se,"Title"),I=n(se,"Current"),P=t('<button type="button"/>').attr({id:Z+"Previous"}),K=t('<button type="button"/>').attr({id:Z+"Next"}),R=n("button","Slideshow"),S),B=t('<button type="button"/>').attr({id:Z+"Close"}),x.append(n(se).append(n(se,"TopLeft"),T=n(se,"TopCenter"),n(se,"TopRight")),n(se,!1,"clear:left").append(C=n(se,"MiddleLeft"),b,H=n(se,"MiddleRight")),n(se,!1,"clear:left").append(n(se,"BottomLeft"),k=n(se,"BottomCenter"),n(se,"BottomRight"))).find("div div").css({"float":"left"}),M=n(se,!1,"position:absolute; width:9999px; visibility:hidden; display:none; max-width:none;"),O=K.add(P).add(I).add(R)),e.body&&!y.parent().length&&t(e.body).append(v,y.append(x,M))}function m(){function i(t){t.which>1||t.shiftKey||t.altKey||t.metaKey||t.ctrlKey||(t.preventDefault(),f(this))}return y?(V||(V=!0,K.click(function(){J.next()}),P.click(function(){J.prev()}),B.click(function(){J.close()}),v.click(function(){_.get("overlayClose")&&J.close()}),t(e).bind("keydown."+Z,function(t){var e=t.keyCode;$&&_.get("escKey")&&27===e&&(t.preventDefault(),J.close()),$&&_.get("arrowKey")&&W[1]&&!t.altKey&&(37===e?(t.preventDefault(),P.click()):39===e&&(t.preventDefault(),K.click()))}),t.isFunction(t.fn.on)?t(e).on("click."+Z,"."+te,i):t("."+te).live("click."+Z,i)),!0):!1}function w(){var e,o,r,h=J.prep,d=++le;if(q=!0,U=!1,u(he),u(ie),_.get("onLoad"),_.h=_.get("height")?a(_.get("height"),"y")-A-D:_.get("innerHeight")&&a(_.get("innerHeight"),"y"),_.w=_.get("width")?a(_.get("width"),"x")-N-j:_.get("innerWidth")&&a(_.get("innerWidth"),"x"),_.mw=_.w,_.mh=_.h,_.get("maxWidth")&&(_.mw=a(_.get("maxWidth"),"x")-N-j,_.mw=_.w&&_.w<_.mw?_.w:_.mw),_.get("maxHeight")&&(_.mh=a(_.get("maxHeight"),"y")-A-D,_.mh=_.h&&_.h<_.mh?_.h:_.mh),e=_.get("href"),Q=setTimeout(function(){S.show()},100),_.get("inline")){var c=t(e);r=t("<div>").hide().insertBefore(c),ae.one(he,function(){r.replaceWith(c)}),h(c)}else _.get("iframe")?h(" "):_.get("html")?h(_.get("html")):s(_,e)?(e=l(_,e),U=new Image,t(U).addClass(Z+"Photo").bind("error",function(){h(n(se,"Error").html(_.get("imgError")))}).one("load",function(){d===le&&setTimeout(function(){var e;t.each(["alt","longdesc","aria-describedby"],function(e,i){var n=t(_.el).attr(i)||t(_.el).attr("data-"+i);n&&U.setAttribute(i,n)}),_.get("retinaImage")&&i.devicePixelRatio>1&&(U.height=U.height/i.devicePixelRatio,U.width=U.width/i.devicePixelRatio),_.get("scalePhotos")&&(o=function(){U.height-=U.height*e,U.width-=U.width*e},_.mw&&U.width>_.mw&&(e=(U.width-_.mw)/U.width,o()),_.mh&&U.height>_.mh&&(e=(U.height-_.mh)/U.height,o())),_.h&&(U.style.marginTop=Math.max(_.mh-U.height,0)/2+"px"),W[1]&&(_.get("loop")||W[z+1])&&(U.style.cursor="pointer",U.onclick=function(){J.next()}),U.style.width=U.width+"px",U.style.height=U.height+"px",h(U)},1)}),U.src=e):e&&M.load(e,_.get("data"),function(e,i){d===le&&h("error"===i?n(se,"Error").html(_.get("xhrError")):t(this).contents())})}var v,y,x,b,T,C,H,k,W,E,L,M,S,F,I,R,K,P,B,O,_,D,j,A,N,z,U,$,q,G,Q,J,V,X={html:!1,photo:!1,iframe:!1,inline:!1,transition:"elastic",speed:300,fadeOut:300,width:!1,initialWidth:"600",innerWidth:!1,maxWidth:!1,height:!1,initialHeight:"450",innerHeight:!1,maxHeight:!1,scalePhotos:!0,scrolling:!0,opacity:.9,preloading:!0,className:!1,overlayClose:!0,escKey:!0,arrowKey:!0,top:!1,bottom:!1,left:!1,right:!1,fixed:!1,data:void 0,closeButton:!0,fastIframe:!0,open:!1,reposition:!0,loop:!0,slideshow:!1,slideshowAuto:!0,slideshowSpeed:2500,slideshowStart:"start slideshow",slideshowStop:"stop slideshow",photoRegex:/\.(gif|png|jp(e|g|eg)|bmp|ico|webp|jxr|svg)((#|\?).*)?$/i,retinaImage:!1,retinaUrl:!1,retinaSuffix:"@2x.$1",current:"image {current} of {total}",previous:"previous",next:"next",close:"close",xhrError:"This content failed to load.",imgError:"This image failed to load.",returnFocus:!0,trapFocus:!0,onOpen:!1,onLoad:!1,onComplete:!1,onCleanup:!1,onClosed:!1,rel:function(){return this.rel},href:function(){return t(this).attr("href")},title:function(){return this.title}},Y="colorbox",Z="cbox",te=Z+"Element",ee=Z+"_open",ie=Z+"_load",ne=Z+"_complete",oe=Z+"_cleanup",re=Z+"_closed",he=Z+"_purge",ae=t("<a/>"),se="div",le=0,de={},ce=function(){function t(){clearTimeout(h)}function e(){(_.get("loop")||W[z+1])&&(t(),h=setTimeout(J.next,_.get("slideshowSpeed")))}function i(){R.html(_.get("slideshowStop")).unbind(s).one(s,n),ae.bind(ne,e).bind(ie,t),y.removeClass(a+"off").addClass(a+"on")}function n(){t(),ae.unbind(ne,e).unbind(ie,t),R.html(_.get("slideshowStart")).unbind(s).one(s,function(){J.next(),i()}),y.removeClass(a+"on").addClass(a+"off")}function o(){r=!1,R.hide(),t(),ae.unbind(ne,e).unbind(ie,t),y.removeClass(a+"off "+a+"on")}var r,h,a=Z+"Slideshow_",s="click."+Z;return function(){r?_.get("slideshow")||(ae.unbind(oe,o),o()):_.get("slideshow")&&W[1]&&(r=!0,ae.one(oe,o),_.get("slideshowAuto")?i():n(),R.show())}}();t[Y]||(t(p),J=t.fn[Y]=t[Y]=function(e,i){var n,o=this;if(e=e||{},t.isFunction(o))o=t("<a/>"),e.open=!0;else if(!o[0])return o;return o[0]?(p(),m()&&(i&&(e.onComplete=i),o.each(function(){var i=t.data(this,Y)||{};t.data(this,Y,t.extend(i,e))}).addClass(te),n=new r(o[0],e),n.get("open")&&f(o[0])),o):o},J.position=function(e,i){function n(){T[0].style.width=k[0].style.width=b[0].style.width=parseInt(y[0].style.width,10)-j+"px",b[0].style.height=C[0].style.height=H[0].style.height=parseInt(y[0].style.height,10)-D+"px"}var r,h,s,l=0,d=0,c=y.offset();if(E.unbind("resize."+Z),y.css({top:-9e4,left:-9e4}),h=E.scrollTop(),s=E.scrollLeft(),_.get("fixed")?(c.top-=h,c.left-=s,y.css({position:"fixed"})):(l=h,d=s,y.css({position:"absolute"})),d+=_.get("right")!==!1?Math.max(E.width()-_.w-N-j-a(_.get("right"),"x"),0):_.get("left")!==!1?a(_.get("left"),"x"):Math.round(Math.max(E.width()-_.w-N-j,0)/2),l+=_.get("bottom")!==!1?Math.max(o()-_.h-A-D-a(_.get("bottom"),"y"),0):_.get("top")!==!1?a(_.get("top"),"y"):Math.round(Math.max(o()-_.h-A-D,0)/2),y.css({top:c.top,left:c.left,visibility:"visible"}),x[0].style.width=x[0].style.height="9999px",r={width:_.w+N+j,height:_.h+A+D,top:l,left:d},e){var g=0;t.each(r,function(t){return r[t]!==de[t]?(g=e,void 0):void 0}),e=g}de=r,e||y.css(r),y.dequeue().animate(r,{duration:e||0,complete:function(){n(),q=!1,x[0].style.width=_.w+N+j+"px",x[0].style.height=_.h+A+D+"px",_.get("reposition")&&setTimeout(function(){E.bind("resize."+Z,J.position)},1),t.isFunction(i)&&i()},step:n})},J.resize=function(t){var e;$&&(t=t||{},t.width&&(_.w=a(t.width,"x")-N-j),t.innerWidth&&(_.w=a(t.innerWidth,"x")),L.css({width:_.w}),t.height&&(_.h=a(t.height,"y")-A-D),t.innerHeight&&(_.h=a(t.innerHeight,"y")),t.innerHeight||t.height||(e=L.scrollTop(),L.css({height:"auto"}),_.h=L.height()),L.css({height:_.h}),e&&L.scrollTop(e),J.position("none"===_.get("transition")?0:_.get("speed")))},J.prep=function(i){function o(){return _.w=_.w||L.width(),_.w=_.mw&&_.mw<_.w?_.mw:_.w,_.w}function a(){return _.h=_.h||L.height(),_.h=_.mh&&_.mh<_.h?_.mh:_.h,_.h}if($){var d,g="none"===_.get("transition")?0:_.get("speed");L.remove(),L=n(se,"LoadedContent").append(i),L.hide().appendTo(M.show()).css({width:o(),overflow:_.get("scrolling")?"auto":"hidden"}).css({height:a()}).prependTo(b),M.hide(),t(U).css({"float":"none"}),c(_.get("className")),d=function(){function i(){t.support.opacity===!1&&y[0].style.removeAttribute("filter")}var n,o,a=W.length;$&&(o=function(){clearTimeout(Q),S.hide(),u(ne),_.get("onComplete")},F.html(_.get("title")).show(),L.show(),a>1?("string"==typeof _.get("current")&&I.html(_.get("current").replace("{current}",z+1).replace("{total}",a)).show(),K[_.get("loop")||a-1>z?"show":"hide"]().html(_.get("next")),P[_.get("loop")||z?"show":"hide"]().html(_.get("previous")),ce(),_.get("preloading")&&t.each([h(-1),h(1)],function(){var i,n=W[this],o=new r(n,t.data(n,Y)),h=o.get("href");h&&s(o,h)&&(h=l(o,h),i=e.createElement("img"),i.src=h)})):O.hide(),_.get("iframe")?(n=e.createElement("iframe"),"frameBorder"in n&&(n.frameBorder=0),"allowTransparency"in n&&(n.allowTransparency="true"),_.get("scrolling")||(n.scrolling="no"),t(n).attr({src:_.get("href"),name:(new Date).getTime(),"class":Z+"Iframe",allowFullScreen:!0}).one("load",o).appendTo(L),ae.one(he,function(){n.src="//about:blank"}),_.get("fastIframe")&&t(n).trigger("load")):o(),"fade"===_.get("transition")?y.fadeTo(g,1,i):i())},"fade"===_.get("transition")?y.fadeTo(g,0,function(){J.position(0,d)}):J.position(g,d)}},J.next=function(){!q&&W[1]&&(_.get("loop")||W[z+1])&&(z=h(1),f(W[z]))},J.prev=function(){!q&&W[1]&&(_.get("loop")||z)&&(z=h(-1),f(W[z]))},J.close=function(){$&&!G&&(G=!0,$=!1,u(oe),_.get("onCleanup"),E.unbind("."+Z),v.fadeTo(_.get("fadeOut")||0,0),y.stop().fadeTo(_.get("fadeOut")||0,0,function(){y.hide(),v.hide(),u(he),L.remove(),setTimeout(function(){G=!1,u(re),_.get("onClosed")},1)}))},J.remove=function(){y&&(y.stop(),t[Y].close(),y.stop(!1,!0).remove(),v.remove(),G=!1,y=null,t("."+te).removeData(Y).removeClass(te),t(e).unbind("click."+Z).unbind("keydown."+Z))},J.element=function(){return t(_.el)},J.settings=X)})(jQuery,document,window);/**
 * fastLiveFilter jQuery plugin 1.0.3
 *
 * Copyright (c) 2011, Anthony Bush
 * License: <http://www.opensource.org/licenses/bsd-license.php>
 * Project Website: http://anthonybush.com/projects/jquery_fast_live_filter/
 **/

jQuery.fn.fastLiveFilter = function(list, options) {
    // Options: input, list, timeout, callback
    options = options || {};
    list = jQuery(list);
    var input = this;
    var lastFilter = '';
    var timeout = options.timeout || 0;
    var callback = options.callback || function() {};

    var keyTimeout;

    // NOTE: because we cache lis & len here, users would need to re-init the plugin
    // if they modify the list in the DOM later.  This doesn't give us that much speed
    // boost, so perhaps it's not worth putting it here.
    var lis = list.children();
    var len = lis.length;
    var oldDisplay = len > 0 ? lis[0].style.display : "block";
    callback(len); // do a one-time callback on initialization to make sure everything's in sync

    input.change(function() {
        // var startTime = new Date().getTime();
        var filter = input.val().toLowerCase();
        var li, innerText;
        var numShown = 0;
        for (var i = 0; i < len; i++) {
            li = lis[i];
            innerText = !options.selector ?
                (li.textContent || li.innerText || "") :
                $(li).find(options.selector).text();

            if (innerText.toLowerCase().indexOf(filter) >= 0) {
                if (li.style.display == "none") {
                    li.style.display = oldDisplay;
                }
                numShown++;
            } else {
                if (li.style.display != "none") {
                    li.style.display = "none";
                }
            }
        }
        callback(numShown);
        // var endTime = new Date().getTime();
        // console.log('Search for ' + filter + ' took: ' + (endTime - startTime) + ' (' + numShown + ' results)');
        return false;
    }).keydown(function() {
        clearTimeout(keyTimeout);
        keyTimeout = setTimeout(function() {
            if( input.val() === lastFilter ) return;
            lastFilter = input.val();
            input.change();
        }, timeout);
    });
    return this; // maintain jQuery chainability
}
;eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}(';5 V=(8(){"1D 1B";5 j={l:\'l\',E:\'1y\',m:\'m\',p:\'1x\',q:\'1v\',v:\'v\'},19={"1u":1t,"1q":1n,"1m":11,"1k":18,"1j":11,"1i":18},S=8(a,b){5 d=1g,O=d.1f(\'a\'),b=b||d.17.G,H=b.r(/\\/\\/(.*?)(?::(.*?))?@/)||[];O.G=b;w(5 i R j){a[i]=O[j[i]]||\'\'}a.l=a.l.o(/:$/,\'\');a.q=a.q.o(/^\\?/,\'\');a.v=a.v.o(/^#/,\'\');a.F=H[1]||\'\';a.x=H[2]||\'\';a.m=(19[a.l]==a.m||a.m==0)?\'\':a.m;9(!a.l&&!/^([a-z]+:)?\\/\\//.1d(b)){5 c=T V(d.17.G.r(/(.*\\/)/)[0]),A=c.p.X(\'/\'),B=a.p.X(\'/\');A.W();w(5 i=0,C=[\'l\',\'F\',\'x\',\'E\',\'m\'],s=C.Z;i<s;i++){a[C[i]]=c[C[i]]}10(B[0]==\'..\'){A.W();B.1c()}a.p=(b.1p(0,1)!=\'/\'?A.13(\'/\'):\'\')+\'/\'+B.13(\'/\')}D{a.p=a.p.o(/^\\/?/,\'/\')}14(a)},15=8(s){s=s.o(/\\+/g,\' \');s=s.o(/%([1b][0-t-f])%([P][0-t-f])%([P][0-t-f])/N,8(a,b,c,d){5 e=u(b,16)-1e,Q=u(c,16)-M;9(e==0&&Q<1h){k a}5 f=u(d,16)-M,n=(e<<12)+(Q<<6)+f;9(n>1l){k a}k K.J(n)});s=s.o(/%([1o][0-t-f])%([P][0-t-f])/N,8(a,b,c){5 d=u(b,16)-1a;9(d<2){k a}5 e=u(c,16)-M;k K.J((d<<6)+e)});s=s.o(/%([0-7][0-t-f])/N,8(a,b){k K.J(u(b,16))});k s},14=8(g){5 h=g.q;g.q=T(8(c){5 d=/([^=&]+)(=([^&]*))?/g,r;10((r=d.1r(c))){5 f=1s(r[1].o(/\\+/g,\' \')),I=r[3]?15(r[3]):\'\';9(4[f]!=1w){9(!(4[f]y Y)){4[f]=[4[f]]}4[f].1z(I)}D{4[f]=I}}4.1A=8(){w(f R 4){9(!(4[f]y U)){1C 4[f]}}};4.L=8(){5 s=\'\',e=1E;w(5 i R 4){9(4[i]y U){1F}9(4[i]y Y){5 a=4[i].Z;9(a){w(5 b=0;b<a;b++){s+=s?\'&\':\'\';s+=e(i)+\'=\'+e(4[i][b])}}D{s+=(s?\'&\':\'\')+e(i)+\'=\'}}D{s+=s?\'&\':\'\';s+=e(i)+\'=\'+e(4[i])}}k s}})(h)};k 8(a){4.L=8(){k((4.l&&(4.l+\'://\'))+(4.F&&(4.F+(4.x&&(\':\'+4.x))+\'@\'))+(4.E&&4.E)+(4.m&&(\':\'+4.m))+(4.p&&4.p)+(4.q.L()&&(\'?\'+4.q))+(4.v&&(\'#\'+4.v)))};S(4,a)}}());',62,104,'||||this|var|||function|if|||||||||||return|protocol|port||replace|path|query|match||9a|parseInt|hash|for|pass|instanceof||basePath|selfPath|props|else|host|user|href|auth|value|fromCharCode|String|toString|0x80|gi|link|89ab|n2|in|parse|new|Function|Url|pop|split|Array|length|while|80||join|parseQs|decode||location|443|defaultPorts|0xC0|ef|shift|test|0xE0|createElement|document|32|wss|ws|https|0xFFFF|http|70|cd|substring|gopher|exec|decodeURIComponent|21|ftp|search|null|pathname|hostname|push|clear|strict|delete|use|encodeURIComponent|continue'.split('|'),0,{}));// jQuery Mask Plugin v1.6.4
// github.com/igorescobar/jQuery-Mask-Plugin
(function(g){"function"===typeof define&&define.amd?define(["jquery"],g):g(window.jQuery||window.Zepto)})(function(g){var y=function(a,f,d){var k=this,x;a=g(a);f="function"===typeof f?f(a.val(),void 0,a,d):f;k.init=function(){d=d||{};k.byPassKeys=[9,16,17,18,36,37,38,39,40,91];k.translation={0:{pattern:/\d/},9:{pattern:/\d/,optional:!0},"#":{pattern:/\d/,recursive:!0},A:{pattern:/[a-zA-Z0-9]/},S:{pattern:/[a-zA-Z]/}};k.translation=g.extend({},k.translation,d.translation);k=g.extend(!0,{},k,d);a.each(function(){!1!==
d.maxlength&&a.attr("maxlength",f.length);d.placeholder&&a.attr("placeholder",d.placeholder);a.attr("autocomplete","off");c.destroyEvents();c.events();var b=c.getCaret();c.val(c.getMasked());c.setCaret(b+c.getMaskCharactersBeforeCount(b,!0))})};var c={getCaret:function(){var b;b=0;var e=a.get(0),c=document.selection,e=e.selectionStart;if(c&&!~navigator.appVersion.indexOf("MSIE 10"))b=c.createRange(),b.moveStart("character",a.is("input")?-a.val().length:-a.text().length),b=b.text.length;else if(e||
        "0"===e)b=e;return b},setCaret:function(b){if(a.is(":focus")){var e;e=a.get(0);e.setSelectionRange?e.setSelectionRange(b,b):e.createTextRange&&(e=e.createTextRange(),e.collapse(!0),e.moveEnd("character",b),e.moveStart("character",b),e.select())}},events:function(){a.on("keydown.mask",function(){x=c.val()});a.on("keyup.mask",c.behaviour);a.on("paste.mask drop.mask",function(){setTimeout(function(){a.keydown().keyup()},100)});a.on("change.mask",function(){a.data("changeCalled",!0)});a.on("blur.mask",
        function(b){b=g(b.target);b.prop("defaultValue")!==b.val()&&(b.prop("defaultValue",b.val()),b.data("changeCalled")||b.trigger("change"));b.data("changeCalled",!1)});a.on("focusout.mask",function(){d.clearIfNotMatch&&c.val().length<f.length&&c.val("")})},destroyEvents:function(){a.off("keydown.mask keyup.mask paste.mask drop.mask change.mask blur.mask focusout.mask").removeData("changeCalled")},val:function(b){var e=a.is("input");return 0<arguments.length?e?a.val(b):a.text(b):e?a.val():a.text()},getMaskCharactersBeforeCount:function(b,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  e){for(var a=0,c=0,d=f.length;c<d&&c<b;c++)k.translation[f.charAt(c)]||(b=e?b+1:b,a++);return a},determineCaretPos:function(b,a,d,h){return k.translation[f.charAt(Math.min(b-1,f.length-1))]?Math.min(b+d-a-h,d):c.determineCaretPos(b+1,a,d,h)},behaviour:function(b){b=b||window.event;var a=b.keyCode||b.which;if(-1===g.inArray(a,k.byPassKeys)){var d=c.getCaret(),f=c.val(),n=f.length,l=d<n,p=c.getMasked(),m=p.length,q=c.getMaskCharactersBeforeCount(m-1)-c.getMaskCharactersBeforeCount(n-1);p!==f&&c.val(p);
        !l||65===a&&b.ctrlKey||(8!==a&&46!==a&&(d=c.determineCaretPos(d,n,m,q)),c.setCaret(d));return c.callbacks(b)}},getMasked:function(b){var a=[],g=c.val(),h=0,n=f.length,l=0,p=g.length,m=1,q="push",s=-1,r,u;d.reverse?(q="unshift",m=-1,r=0,h=n-1,l=p-1,u=function(){return-1<h&&-1<l}):(r=n-1,u=function(){return h<n&&l<p});for(;u();){var v=f.charAt(h),w=g.charAt(l),t=k.translation[v];if(t)w.match(t.pattern)?(a[q](w),t.recursive&&(-1===s?s=h:h===r&&(h=s-m),r===s&&(h-=m)),h+=m):t.optional&&(h+=m,l-=m),l+=
        m;else{if(!b)a[q](v);w===v&&(l+=m);h+=m}}b=f.charAt(r);n!==p+1||k.translation[b]||a.push(b);return a.join("")},callbacks:function(b){var e=c.val(),g=c.val()!==x;if(!0===g&&"function"===typeof d.onChange)d.onChange(e,b,a,d);if(!0===g&&"function"===typeof d.onKeyPress)d.onKeyPress(e,b,a,d);if("function"===typeof d.onComplete&&e.length===f.length)d.onComplete(e,b,a,d)}};k.remove=function(){var a=c.getCaret(),d=c.getMaskCharactersBeforeCount(a);c.destroyEvents();c.val(k.getCleanVal()).removeAttr("maxlength");
    c.setCaret(a-d)};k.getCleanVal=function(){return c.getMasked(!0)};k.init()};g.fn.mask=function(a,f){this.unmask();return this.each(function(){g(this).data("mask",new y(this,a,f))})};g.fn.unmask=function(){return this.each(function(){try{g(this).data("mask").remove()}catch(a){}})};g.fn.cleanVal=function(){return g(this).data("mask").getCleanVal()};g("*[data-mask]").each(function(){var a=g(this),f={};"true"===a.attr("data-mask-reverse")&&(f.reverse=!0);"false"===a.attr("data-mask-maxlength")&&(f.maxlength=
    !1);"true"===a.attr("data-mask-clearifnotmatch")&&(f.clearIfNotMatch=!0);a.mask(a.attr("data-mask"),f)})});
// Generated by CoffeeScript 1.4.0

/*
 jQuery Credit Card Validator

 Copyright 2012 Pawel Decowski

 This work is licensed under the Creative Commons Attribution-ShareAlike 3.0
 Unported License. To view a copy of this license, visit:

 http://creativecommons.org/licenses/by-sa/3.0/

 or send a letter to:

 Creative Commons, 444 Castro Street, Suite 900,
 Mountain View, California, 94041, USA.
 */


(function() {
    var $,
        __indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

    $ = jQuery;

    $.fn.validateCreditCard = function(callback, options) {
        var card, card_type, card_types, get_card_type, is_valid_length, is_valid_luhn, normalize, validate, validate_number, _i, _len, _ref, _ref1;
        card_types = [
            {
                name: 'amex',
                pattern: /^3[47]/,
                valid_length: 15,
                mask_number: '0000 000000 00000',
                mask_security: '0009'
            },
            {
                name: 'diners',
                pattern: /^3(6|0[0-5])/,
                valid_length: 14,
                mask_number: '0000 000000 0000',
                mask_security: '000'
            },
            /*{
             name: 'diners',
             pattern: /^36/,
             valid_length: 15,
             mask_number: '0000 000000 0000',
             mask_security: '000'
             },*/
            {
                name: 'jcb',
                pattern: /^35(2[89]|[3-8][0-9])/,
                valid_length: 16
            },
            {
                name: 'elo',
                pattern: /^((((636368)|(438935)|(504175)|(451416)|(636297))\d{0,10})|((5067)|(4576)|(4011))\d{0,12})$/,
                valid_length: 16,
                mask_number: '0000 0000 0000 0000',
                mask_security: '000'
            },
            {
                name: 'laser',
                pattern: /^(6304|670[69]|6771)/,
                valid_length: [16, 17, 18, 19]
            }, {
                name: 'visa_electron',
                pattern: /^(4026|417500|4508|4844|491(3|7))/,
                valid_length: 16,
                mask_number: '0000 0000 0000 0000',
                mask_security: '000'
            },
            {
                name: 'visa',
                pattern: /^4/,
                valid_length: 16,
                mask_number: '0000 0000 0000 0000',
                mask_security: '000'
            },
            {
                name: 'mastercard',
                pattern: /^5[1-5]/,
                valid_length: 16,
                mask_number: '0000 0000 0000 0000',
                mask_security: '000'
            },
            {
                name: 'maestro',
                pattern: /^(5018|5020|5038|6304|6759|676[1-3])/,
                valid_length: [12, 13, 14, 15, 16, 17, 18, 19]
            },
            {
                name: 'discover',
                pattern: /^(6011|622(12[6-9]|1[3-9][0-9]|[2-8][0-9]{2}|9[0-1][0-9]|92[0-5]|64[4-9])|65)/,
                valid_length: 16,
                mask_number: '0000 0000 0000 0000',
                mask_security: '0009'
            }
        ];
        if (options == null) {
            options = {};
        }
        if ((_ref = options.accept) == null) {
            options.accept = (function() {
                var _i, _len, _results;
                _results = [];
                for (_i = 0, _len = card_types.length; _i < _len; _i++) {
                    card = card_types[_i];
                    _results.push(card.name);
                }
                return _results;
            })();
        }
        _ref1 = options.accept;
        for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
            card_type = _ref1[_i];
            if (__indexOf.call((function() {
                var _j, _len1, _results;
                _results = [];
                for (_j = 0, _len1 = card_types.length; _j < _len1; _j++) {
                    card = card_types[_j];
                    _results.push(card.name);
                }
                return _results;
            })(), card_type) < 0) {
                throw "Credit card type '" + card_type + "' is not supported";
            }
        }
        get_card_type = function(number) {
            var _j, _len1, _ref2;
            _ref2 = (function() {
                var _k, _len1, _ref2, _results;
                _results = [];
                for (_k = 0, _len1 = card_types.length; _k < _len1; _k++) {
                    card = card_types[_k];
                    if (_ref2 = card.name, __indexOf.call(options.accept, _ref2) >= 0) {
                        _results.push(card);
                    }
                }
                return _results;
            })();
            for (_j = 0, _len1 = _ref2.length; _j < _len1; _j++) {
                card_type = _ref2[_j];
                if (number.match(card_type.pattern)) {
                    return card_type;
                }
            }
            return null;
        };
        is_valid_luhn = function(number) {
            var digit, n, sum, _j, _len1, _ref2;
            sum = 0;
            _ref2 = number.split('').reverse();
            for (n = _j = 0, _len1 = _ref2.length; _j < _len1; n = ++_j) {
                digit = _ref2[n];
                digit = +digit;
                if (n % 2) {
                    digit *= 2;
                    if (digit < 10) {
                        sum += digit;
                    } else {
                        sum += digit - 9;
                    }
                } else {
                    sum += digit;
                }
            }
            return sum % 10 === 0;
        };
        is_valid_length = function(number, card_type) {
            var _ref2;
            return _ref2 = number.length, __indexOf.call(card_type.valid_length, _ref2) >= 0;
        };
        validate_number = function(number) {
            var length_valid, luhn_valid;
            card_type = get_card_type(number);
            luhn_valid = false;
            length_valid = false;
            if (card_type != null) {
                luhn_valid = is_valid_luhn(number);
                length_valid = is_valid_length(number, card_type);
            }
            return callback({
                card_type: card_type,
                luhn_valid: luhn_valid,
                length_valid: length_valid
            });
        };
        validate = function() {
            var number;
            number = normalize($(this).val());
            return validate_number(number);
        };
        normalize = function(number) {
            return number.replace(/[ -]/g, '');
        };
        this.bind('input', function() {
            $(this).unbind('keyup');
            return validate.call(this);
        });
        this.bind('keyup', function() {
            return validate.call(this);
        });
        if (this.length !== 0) {
            validate.call(this);
        }
        return this;
    };

}).call(this);
/*
 * jQuery Highlight plugin
 *
 * Based on highlight v3 by Johann Burkard
 * http://johannburkard.de/blog/programming/javascript/highlight-javascript-text-higlighting-jquery-plugin.html
 *
 * Code a little bit refactored and cleaned (in my humble opinion).
 * Most important changes:
 *  - has an option to highlight only entire words (wordsOnly - false by default),
 *  - has an option to be case sensitive (caseSensitive - false by default)
 *  - highlight element tag and class names can be specified in options
 *
 * Usage:
 *   // wrap every occurrance of text 'lorem' in content
 *   // with <span class='highlight'> (default options)
 *   $('#content').highlight('lorem');
 *
 *   // search for and highlight more terms at once
 *   // so you can save some time on traversing DOM
 *   $('#content').highlight(['lorem', 'ipsum']);
 *   $('#content').highlight('lorem ipsum');
 *
 *   // search only for entire word 'lorem'
 *   $('#content').highlight('lorem', { wordsOnly: true });
 *
 *   // don't ignore case during search of term 'lorem'
 *   $('#content').highlight('lorem', { caseSensitive: true });
 *
 *   // wrap every occurrance of term 'ipsum' in content
 *   // with <em class='important'>
 *   $('#content').highlight('ipsum', { element: 'em', className: 'important' });
 *
 *   // remove default highlight
 *   $('#content').unhighlight();
 *
 *   // remove custom highlight
 *   $('#content').unhighlight({ element: 'em', className: 'important' });
 *
 *
 * Copyright (c) 2009 Bartek Szopka
 *
 * Licensed under MIT license.
 *
 */

jQuery.extend({
    highlight: function (node, re, nodeName, className) {
        if (node.nodeType === 3) {
            var match = node.data.match(re);
            if (match) {
                var highlight = document.createElement(nodeName || 'span');
                highlight.className = className || 'highlight';
                var wordNode = node.splitText(match.index);
                wordNode.splitText(match[0].length);
                var wordClone = wordNode.cloneNode(true);
                highlight.appendChild(wordClone);
                wordNode.parentNode.replaceChild(highlight, wordNode);
                return 1; //skip added node in parent
            }
        } else if ((node.nodeType === 1 && node.childNodes) && // only element nodes that have children
            !/(script|style)/i.test(node.tagName) && // ignore script and style nodes
            !(node.tagName === nodeName.toUpperCase() && node.className === className)) { // skip if already highlighted
            for (var i = 0; i < node.childNodes.length; i++) {
                i += jQuery.highlight(node.childNodes[i], re, nodeName, className);
            }
        }
        return 0;
    }
});

jQuery.fn.unhighlight = function (options) {
    var settings = { className: 'highlight', element: 'span' };
    jQuery.extend(settings, options);

    return this.find(settings.element + "." + settings.className).each(function () {
        var parent = this.parentNode;
        parent.replaceChild(this.firstChild, this);
        parent.normalize();
    }).end();
};

jQuery.fn.highlight = function (words, options) {
    var settings = { className: 'highlight', element: 'span', caseSensitive: false, wordsOnly: false };
    jQuery.extend(settings, options);

    if (words.constructor === String) {
        words = [words];
    }
    words = jQuery.grep(words, function(word, i){
        return word != '';
    });
    words = jQuery.map(words, function(word, i) {
        return word.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
    });
    if (words.length == 0) { return this; };

    var flag = settings.caseSensitive ? "" : "i";
    var pattern = "(" + words.join("|") + ")";
    if (settings.wordsOnly) {
        pattern = "\\b" + pattern + "\\b";
    }
    var re = new RegExp(pattern, flag);

    return this.each(function () {
        jQuery.highlight(this, re, settings.element, settings.className);
    });
};

/*! jQuery UI - v1.9.2 - 2014-05-13
* http://jqueryui.com
* Includes: jquery.ui.core.js, jquery.ui.widget.js, jquery.ui.position.js, jquery.ui.autocomplete.js, jquery.ui.menu.js
* Copyright 2014 jQuery Foundation and other contributors; Licensed MIT */

(function(e,t){function i(t,i){var s,n,r,o=t.nodeName.toLowerCase();return"area"===o?(s=t.parentNode,n=s.name,t.href&&n&&"map"===s.nodeName.toLowerCase()?(r=e("img[usemap=#"+n+"]")[0],!!r&&a(r)):!1):(/input|select|textarea|button|object/.test(o)?!t.disabled:"a"===o?t.href||i:i)&&a(t)}function a(t){return e.expr.filters.visible(t)&&!e(t).parents().andSelf().filter(function(){return"hidden"===e.css(this,"visibility")}).length}var s=0,n=/^ui-id-\d+$/;e.ui=e.ui||{},e.ui.version||(e.extend(e.ui,{version:"1.9.2",keyCode:{BACKSPACE:8,COMMA:188,DELETE:46,DOWN:40,END:35,ENTER:13,ESCAPE:27,HOME:36,LEFT:37,NUMPAD_ADD:107,NUMPAD_DECIMAL:110,NUMPAD_DIVIDE:111,NUMPAD_ENTER:108,NUMPAD_MULTIPLY:106,NUMPAD_SUBTRACT:109,PAGE_DOWN:34,PAGE_UP:33,PERIOD:190,RIGHT:39,SPACE:32,TAB:9,UP:38}}),e.fn.extend({_focus:e.fn.focus,focus:function(t,i){return"number"==typeof t?this.each(function(){var a=this;setTimeout(function(){e(a).focus(),i&&i.call(a)},t)}):this._focus.apply(this,arguments)},scrollParent:function(){var t;return t=e.ui.ie&&/(static|relative)/.test(this.css("position"))||/absolute/.test(this.css("position"))?this.parents().filter(function(){return/(relative|absolute|fixed)/.test(e.css(this,"position"))&&/(auto|scroll)/.test(e.css(this,"overflow")+e.css(this,"overflow-y")+e.css(this,"overflow-x"))}).eq(0):this.parents().filter(function(){return/(auto|scroll)/.test(e.css(this,"overflow")+e.css(this,"overflow-y")+e.css(this,"overflow-x"))}).eq(0),/fixed/.test(this.css("position"))||!t.length?e(document):t},zIndex:function(i){if(i!==t)return this.css("zIndex",i);if(this.length)for(var a,s,n=e(this[0]);n.length&&n[0]!==document;){if(a=n.css("position"),("absolute"===a||"relative"===a||"fixed"===a)&&(s=parseInt(n.css("zIndex"),10),!isNaN(s)&&0!==s))return s;n=n.parent()}return 0},uniqueId:function(){return this.each(function(){this.id||(this.id="ui-id-"+ ++s)})},removeUniqueId:function(){return this.each(function(){n.test(this.id)&&e(this).removeAttr("id")})}}),e.extend(e.expr[":"],{data:e.expr.createPseudo?e.expr.createPseudo(function(t){return function(i){return!!e.data(i,t)}}):function(t,i,a){return!!e.data(t,a[3])},focusable:function(t){return i(t,!isNaN(e.attr(t,"tabindex")))},tabbable:function(t){var a=e.attr(t,"tabindex"),s=isNaN(a);return(s||a>=0)&&i(t,!s)}}),e(function(){var t=document.body,i=t.appendChild(i=document.createElement("div"));i.offsetHeight,e.extend(i.style,{minHeight:"100px",height:"auto",padding:0,borderWidth:0}),e.support.minHeight=100===i.offsetHeight,e.support.selectstart="onselectstart"in i,t.removeChild(i).style.display="none"}),e("<a>").outerWidth(1).jquery||e.each(["Width","Height"],function(i,a){function s(t,i,a,s){return e.each(n,function(){i-=parseFloat(e.css(t,"padding"+this))||0,a&&(i-=parseFloat(e.css(t,"border"+this+"Width"))||0),s&&(i-=parseFloat(e.css(t,"margin"+this))||0)}),i}var n="Width"===a?["Left","Right"]:["Top","Bottom"],r=a.toLowerCase(),o={innerWidth:e.fn.innerWidth,innerHeight:e.fn.innerHeight,outerWidth:e.fn.outerWidth,outerHeight:e.fn.outerHeight};e.fn["inner"+a]=function(i){return i===t?o["inner"+a].call(this):this.each(function(){e(this).css(r,s(this,i)+"px")})},e.fn["outer"+a]=function(t,i){return"number"!=typeof t?o["outer"+a].call(this,t):this.each(function(){e(this).css(r,s(this,t,!0,i)+"px")})}}),e("<a>").data("a-b","a").removeData("a-b").data("a-b")&&(e.fn.removeData=function(t){return function(i){return arguments.length?t.call(this,e.camelCase(i)):t.call(this)}}(e.fn.removeData)),function(){var t=/msie ([\w.]+)/.exec(navigator.userAgent.toLowerCase())||[];e.ui.ie=t.length?!0:!1,e.ui.ie6=6===parseFloat(t[1],10)}(),e.fn.extend({disableSelection:function(){return this.bind((e.support.selectstart?"selectstart":"mousedown")+".ui-disableSelection",function(e){e.preventDefault()})},enableSelection:function(){return this.unbind(".ui-disableSelection")}}),e.extend(e.ui,{plugin:{add:function(t,i,a){var s,n=e.ui[t].prototype;for(s in a)n.plugins[s]=n.plugins[s]||[],n.plugins[s].push([i,a[s]])},call:function(e,t,i){var a,s=e.plugins[t];if(s&&e.element[0].parentNode&&11!==e.element[0].parentNode.nodeType)for(a=0;s.length>a;a++)e.options[s[a][0]]&&s[a][1].apply(e.element,i)}},contains:e.contains,hasScroll:function(t,i){if("hidden"===e(t).css("overflow"))return!1;var a=i&&"left"===i?"scrollLeft":"scrollTop",s=!1;return t[a]>0?!0:(t[a]=1,s=t[a]>0,t[a]=0,s)},isOverAxis:function(e,t,i){return e>t&&t+i>e},isOver:function(t,i,a,s,n,r){return e.ui.isOverAxis(t,a,n)&&e.ui.isOverAxis(i,s,r)}}))})(jQuery);(function(e,t){var i=0,s=Array.prototype.slice,a=e.cleanData;e.cleanData=function(t){for(var i,s=0;null!=(i=t[s]);s++)try{e(i).triggerHandler("remove")}catch(n){}a(t)},e.widget=function(i,s,a){var n,r,o,h,l=i.split(".")[0];i=i.split(".")[1],n=l+"-"+i,a||(a=s,s=e.Widget),e.expr[":"][n.toLowerCase()]=function(t){return!!e.data(t,n)},e[l]=e[l]||{},r=e[l][i],o=e[l][i]=function(e,i){return this._createWidget?(arguments.length&&this._createWidget(e,i),t):new o(e,i)},e.extend(o,r,{version:a.version,_proto:e.extend({},a),_childConstructors:[]}),h=new s,h.options=e.widget.extend({},h.options),e.each(a,function(t,i){e.isFunction(i)&&(a[t]=function(){var e=function(){return s.prototype[t].apply(this,arguments)},a=function(e){return s.prototype[t].apply(this,e)};return function(){var t,s=this._super,n=this._superApply;return this._super=e,this._superApply=a,t=i.apply(this,arguments),this._super=s,this._superApply=n,t}}())}),o.prototype=e.widget.extend(h,{widgetEventPrefix:r?h.widgetEventPrefix:i},a,{constructor:o,namespace:l,widgetName:i,widgetBaseClass:n,widgetFullName:n}),r?(e.each(r._childConstructors,function(t,i){var s=i.prototype;e.widget(s.namespace+"."+s.widgetName,o,i._proto)}),delete r._childConstructors):s._childConstructors.push(o),e.widget.bridge(i,o)},e.widget.extend=function(i){for(var a,n,r=s.call(arguments,1),o=0,h=r.length;h>o;o++)for(a in r[o])n=r[o][a],r[o].hasOwnProperty(a)&&n!==t&&(i[a]=e.isPlainObject(n)?e.isPlainObject(i[a])?e.widget.extend({},i[a],n):e.widget.extend({},n):n);return i},e.widget.bridge=function(i,a){var n=a.prototype.widgetFullName||i;e.fn[i]=function(r){var o="string"==typeof r,h=s.call(arguments,1),l=this;return r=!o&&h.length?e.widget.extend.apply(null,[r].concat(h)):r,o?this.each(function(){var s,a=e.data(this,n);return a?e.isFunction(a[r])&&"_"!==r.charAt(0)?(s=a[r].apply(a,h),s!==a&&s!==t?(l=s&&s.jquery?l.pushStack(s.get()):s,!1):t):e.error("no such method '"+r+"' for "+i+" widget instance"):e.error("cannot call methods on "+i+" prior to initialization; "+"attempted to call method '"+r+"'")}):this.each(function(){var t=e.data(this,n);t?t.option(r||{})._init():e.data(this,n,new a(r,this))}),l}},e.Widget=function(){},e.Widget._childConstructors=[],e.Widget.prototype={widgetName:"widget",widgetEventPrefix:"",defaultElement:"<div>",options:{disabled:!1,create:null},_createWidget:function(t,s){s=e(s||this.defaultElement||this)[0],this.element=e(s),this.uuid=i++,this.eventNamespace="."+this.widgetName+this.uuid,this.options=e.widget.extend({},this.options,this._getCreateOptions(),t),this.bindings=e(),this.hoverable=e(),this.focusable=e(),s!==this&&(e.data(s,this.widgetName,this),e.data(s,this.widgetFullName,this),this._on(!0,this.element,{remove:function(e){e.target===s&&this.destroy()}}),this.document=e(s.style?s.ownerDocument:s.document||s),this.window=e(this.document[0].defaultView||this.document[0].parentWindow)),this._create(),this._trigger("create",null,this._getCreateEventData()),this._init()},_getCreateOptions:e.noop,_getCreateEventData:e.noop,_create:e.noop,_init:e.noop,destroy:function(){this._destroy(),this.element.unbind(this.eventNamespace).removeData(this.widgetName).removeData(this.widgetFullName).removeData(e.camelCase(this.widgetFullName)),this.widget().unbind(this.eventNamespace).removeAttr("aria-disabled").removeClass(this.widgetFullName+"-disabled "+"ui-state-disabled"),this.bindings.unbind(this.eventNamespace),this.hoverable.removeClass("ui-state-hover"),this.focusable.removeClass("ui-state-focus")},_destroy:e.noop,widget:function(){return this.element},option:function(i,s){var a,n,r,o=i;if(0===arguments.length)return e.widget.extend({},this.options);if("string"==typeof i)if(o={},a=i.split("."),i=a.shift(),a.length){for(n=o[i]=e.widget.extend({},this.options[i]),r=0;a.length-1>r;r++)n[a[r]]=n[a[r]]||{},n=n[a[r]];if(i=a.pop(),s===t)return n[i]===t?null:n[i];n[i]=s}else{if(s===t)return this.options[i]===t?null:this.options[i];o[i]=s}return this._setOptions(o),this},_setOptions:function(e){var t;for(t in e)this._setOption(t,e[t]);return this},_setOption:function(e,t){return this.options[e]=t,"disabled"===e&&(this.widget().toggleClass(this.widgetFullName+"-disabled ui-state-disabled",!!t).attr("aria-disabled",t),this.hoverable.removeClass("ui-state-hover"),this.focusable.removeClass("ui-state-focus")),this},enable:function(){return this._setOption("disabled",!1)},disable:function(){return this._setOption("disabled",!0)},_on:function(i,s,a){var n,r=this;"boolean"!=typeof i&&(a=s,s=i,i=!1),a?(s=n=e(s),this.bindings=this.bindings.add(s)):(a=s,s=this.element,n=this.widget()),e.each(a,function(a,o){function h(){return i||r.options.disabled!==!0&&!e(this).hasClass("ui-state-disabled")?("string"==typeof o?r[o]:o).apply(r,arguments):t}"string"!=typeof o&&(h.guid=o.guid=o.guid||h.guid||e.guid++);var l=a.match(/^(\w+)\s*(.*)$/),u=l[1]+r.eventNamespace,d=l[2];d?n.delegate(d,u,h):s.bind(u,h)})},_off:function(e,t){t=(t||"").split(" ").join(this.eventNamespace+" ")+this.eventNamespace,e.unbind(t).undelegate(t)},_delay:function(e,t){function i(){return("string"==typeof e?s[e]:e).apply(s,arguments)}var s=this;return setTimeout(i,t||0)},_hoverable:function(t){this.hoverable=this.hoverable.add(t),this._on(t,{mouseenter:function(t){e(t.currentTarget).addClass("ui-state-hover")},mouseleave:function(t){e(t.currentTarget).removeClass("ui-state-hover")}})},_focusable:function(t){this.focusable=this.focusable.add(t),this._on(t,{focusin:function(t){e(t.currentTarget).addClass("ui-state-focus")},focusout:function(t){e(t.currentTarget).removeClass("ui-state-focus")}})},_trigger:function(t,i,s){var a,n,r=this.options[t];if(s=s||{},i=e.Event(i),i.type=(t===this.widgetEventPrefix?t:this.widgetEventPrefix+t).toLowerCase(),i.target=this.element[0],n=i.originalEvent)for(a in n)a in i||(i[a]=n[a]);return this.element.trigger(i,s),!(e.isFunction(r)&&r.apply(this.element[0],[i].concat(s))===!1||i.isDefaultPrevented())}},e.each({show:"fadeIn",hide:"fadeOut"},function(t,i){e.Widget.prototype["_"+t]=function(s,a,n){"string"==typeof a&&(a={effect:a});var r,o=a?a===!0||"number"==typeof a?i:a.effect||i:t;a=a||{},"number"==typeof a&&(a={duration:a}),r=!e.isEmptyObject(a),a.complete=n,a.delay&&s.delay(a.delay),r&&e.effects&&(e.effects.effect[o]||e.uiBackCompat!==!1&&e.effects[o])?s[t](a):o!==t&&s[o]?s[o](a.duration,a.easing,n):s.queue(function(i){e(this)[t](),n&&n.call(s[0]),i()})}}),e.uiBackCompat!==!1&&(e.Widget.prototype._getCreateOptions=function(){return e.metadata&&e.metadata.get(this.element[0])[this.widgetName]})})(jQuery);(function(e,t){function i(e,t,i){return[parseInt(e[0],10)*(c.test(e[0])?t/100:1),parseInt(e[1],10)*(c.test(e[1])?i/100:1)]}function a(t,i){return parseInt(e.css(t,i),10)||0}e.ui=e.ui||{};var s,n=Math.max,r=Math.abs,o=Math.round,h=/left|center|right/,l=/top|center|bottom/,u=/[\+\-]\d+%?/,d=/^\w+/,c=/%$/,p=e.fn.position;e.position={scrollbarWidth:function(){if(s!==t)return s;var i,a,n=e("<div style='display:block;width:50px;height:50px;overflow:hidden;'><div style='height:100px;width:auto;'></div></div>"),r=n.children()[0];return e("body").append(n),i=r.offsetWidth,n.css("overflow","scroll"),a=r.offsetWidth,i===a&&(a=n[0].clientWidth),n.remove(),s=i-a},getScrollInfo:function(t){var i=t.isWindow?"":t.element.css("overflow-x"),a=t.isWindow?"":t.element.css("overflow-y"),s="scroll"===i||"auto"===i&&t.width<t.element[0].scrollWidth,n="scroll"===a||"auto"===a&&t.height<t.element[0].scrollHeight;return{width:s?e.position.scrollbarWidth():0,height:n?e.position.scrollbarWidth():0}},getWithinInfo:function(t){var i=e(t||window),a=e.isWindow(i[0]);return{element:i,isWindow:a,offset:i.offset()||{left:0,top:0},scrollLeft:i.scrollLeft(),scrollTop:i.scrollTop(),width:a?i.width():i.outerWidth(),height:a?i.height():i.outerHeight()}}},e.fn.position=function(t){if(!t||!t.of)return p.apply(this,arguments);t=e.extend({},t);var s,c,m,f,g,v=e(t.of),y=e.position.getWithinInfo(t.within),b=e.position.getScrollInfo(y),x=v[0],_=(t.collision||"flip").split(" "),k={};return 9===x.nodeType?(c=v.width(),m=v.height(),f={top:0,left:0}):e.isWindow(x)?(c=v.width(),m=v.height(),f={top:v.scrollTop(),left:v.scrollLeft()}):x.preventDefault?(t.at="left top",c=m=0,f={top:x.pageY,left:x.pageX}):(c=v.outerWidth(),m=v.outerHeight(),f=v.offset()),g=e.extend({},f),e.each(["my","at"],function(){var e,i,a=(t[this]||"").split(" ");1===a.length&&(a=h.test(a[0])?a.concat(["center"]):l.test(a[0])?["center"].concat(a):["center","center"]),a[0]=h.test(a[0])?a[0]:"center",a[1]=l.test(a[1])?a[1]:"center",e=u.exec(a[0]),i=u.exec(a[1]),k[this]=[e?e[0]:0,i?i[0]:0],t[this]=[d.exec(a[0])[0],d.exec(a[1])[0]]}),1===_.length&&(_[1]=_[0]),"right"===t.at[0]?g.left+=c:"center"===t.at[0]&&(g.left+=c/2),"bottom"===t.at[1]?g.top+=m:"center"===t.at[1]&&(g.top+=m/2),s=i(k.at,c,m),g.left+=s[0],g.top+=s[1],this.each(function(){var h,l,u=e(this),d=u.outerWidth(),p=u.outerHeight(),x=a(this,"marginLeft"),T=a(this,"marginTop"),w=d+x+a(this,"marginRight")+b.width,S=p+T+a(this,"marginBottom")+b.height,N=e.extend({},g),M=i(k.my,u.outerWidth(),u.outerHeight());"right"===t.my[0]?N.left-=d:"center"===t.my[0]&&(N.left-=d/2),"bottom"===t.my[1]?N.top-=p:"center"===t.my[1]&&(N.top-=p/2),N.left+=M[0],N.top+=M[1],e.support.offsetFractions||(N.left=o(N.left),N.top=o(N.top)),h={marginLeft:x,marginTop:T},e.each(["left","top"],function(i,a){e.ui.position[_[i]]&&e.ui.position[_[i]][a](N,{targetWidth:c,targetHeight:m,elemWidth:d,elemHeight:p,collisionPosition:h,collisionWidth:w,collisionHeight:S,offset:[s[0]+M[0],s[1]+M[1]],my:t.my,at:t.at,within:y,elem:u})}),e.fn.bgiframe&&u.bgiframe(),t.using&&(l=function(e){var i=f.left-N.left,a=i+c-d,s=f.top-N.top,o=s+m-p,h={target:{element:v,left:f.left,top:f.top,width:c,height:m},element:{element:u,left:N.left,top:N.top,width:d,height:p},horizontal:0>a?"left":i>0?"right":"center",vertical:0>o?"top":s>0?"bottom":"middle"};d>c&&c>r(i+a)&&(h.horizontal="center"),p>m&&m>r(s+o)&&(h.vertical="middle"),h.important=n(r(i),r(a))>n(r(s),r(o))?"horizontal":"vertical",t.using.call(this,e,h)}),u.offset(e.extend(N,{using:l}))})},e.ui.position={fit:{left:function(e,t){var i,a=t.within,s=a.isWindow?a.scrollLeft:a.offset.left,r=a.width,o=e.left-t.collisionPosition.marginLeft,h=s-o,l=o+t.collisionWidth-r-s;t.collisionWidth>r?h>0&&0>=l?(i=e.left+h+t.collisionWidth-r-s,e.left+=h-i):e.left=l>0&&0>=h?s:h>l?s+r-t.collisionWidth:s:h>0?e.left+=h:l>0?e.left-=l:e.left=n(e.left-o,e.left)},top:function(e,t){var i,a=t.within,s=a.isWindow?a.scrollTop:a.offset.top,r=t.within.height,o=e.top-t.collisionPosition.marginTop,h=s-o,l=o+t.collisionHeight-r-s;t.collisionHeight>r?h>0&&0>=l?(i=e.top+h+t.collisionHeight-r-s,e.top+=h-i):e.top=l>0&&0>=h?s:h>l?s+r-t.collisionHeight:s:h>0?e.top+=h:l>0?e.top-=l:e.top=n(e.top-o,e.top)}},flip:{left:function(e,t){var i,a,s=t.within,n=s.offset.left+s.scrollLeft,o=s.width,h=s.isWindow?s.scrollLeft:s.offset.left,l=e.left-t.collisionPosition.marginLeft,u=l-h,d=l+t.collisionWidth-o-h,c="left"===t.my[0]?-t.elemWidth:"right"===t.my[0]?t.elemWidth:0,p="left"===t.at[0]?t.targetWidth:"right"===t.at[0]?-t.targetWidth:0,m=-2*t.offset[0];0>u?(i=e.left+c+p+m+t.collisionWidth-o-n,(0>i||r(u)>i)&&(e.left+=c+p+m)):d>0&&(a=e.left-t.collisionPosition.marginLeft+c+p+m-h,(a>0||d>r(a))&&(e.left+=c+p+m))},top:function(e,t){var i,a,s=t.within,n=s.offset.top+s.scrollTop,o=s.height,h=s.isWindow?s.scrollTop:s.offset.top,l=e.top-t.collisionPosition.marginTop,u=l-h,d=l+t.collisionHeight-o-h,c="top"===t.my[1],p=c?-t.elemHeight:"bottom"===t.my[1]?t.elemHeight:0,m="top"===t.at[1]?t.targetHeight:"bottom"===t.at[1]?-t.targetHeight:0,f=-2*t.offset[1];0>u?(a=e.top+p+m+f+t.collisionHeight-o-n,e.top+p+m+f>u&&(0>a||r(u)>a)&&(e.top+=p+m+f)):d>0&&(i=e.top-t.collisionPosition.marginTop+p+m+f-h,e.top+p+m+f>d&&(i>0||d>r(i))&&(e.top+=p+m+f))}},flipfit:{left:function(){e.ui.position.flip.left.apply(this,arguments),e.ui.position.fit.left.apply(this,arguments)},top:function(){e.ui.position.flip.top.apply(this,arguments),e.ui.position.fit.top.apply(this,arguments)}}},function(){var t,i,a,s,n,r=document.getElementsByTagName("body")[0],o=document.createElement("div");t=document.createElement(r?"div":"body"),a={visibility:"hidden",width:0,height:0,border:0,margin:0,background:"none"},r&&e.extend(a,{position:"absolute",left:"-1000px",top:"-1000px"});for(n in a)t.style[n]=a[n];t.appendChild(o),i=r||document.documentElement,i.insertBefore(t,i.firstChild),o.style.cssText="position: absolute; left: 10.7432222px;",s=e(o).offset().left,e.support.offsetFractions=s>10&&11>s,t.innerHTML="",i.removeChild(t)}(),e.uiBackCompat!==!1&&function(e){var i=e.fn.position;e.fn.position=function(a){if(!a||!a.offset)return i.call(this,a);var s=a.offset.split(" "),n=a.at.split(" ");return 1===s.length&&(s[1]=s[0]),/^\d/.test(s[0])&&(s[0]="+"+s[0]),/^\d/.test(s[1])&&(s[1]="+"+s[1]),1===n.length&&(/left|center|right/.test(n[0])?n[1]="center":(n[1]=n[0],n[0]="center")),i.call(this,e.extend(a,{at:n[0]+s[0]+" "+n[1]+s[1],offset:t}))}}(jQuery)})(jQuery);(function(e){var t=0;e.widget("ui.autocomplete",{version:"1.9.2",defaultElement:"<input>",options:{appendTo:"body",autoFocus:!1,delay:300,minLength:1,position:{my:"left top",at:"left bottom",collision:"none"},source:null,change:null,close:null,focus:null,open:null,response:null,search:null,select:null},pending:0,_create:function(){var t,i,a;this.isMultiLine=this._isMultiLine(),this.valueMethod=this.element[this.element.is("input,textarea")?"val":"text"],this.isNewMenu=!0,this.element.addClass("ui-autocomplete-input").attr("autocomplete","off"),this._on(this.element,{keydown:function(s){if(this.element.prop("readOnly"))return t=!0,a=!0,i=!0,undefined;t=!1,a=!1,i=!1;var n=e.ui.keyCode;switch(s.keyCode){case n.PAGE_UP:t=!0,this._move("previousPage",s);break;case n.PAGE_DOWN:t=!0,this._move("nextPage",s);break;case n.UP:t=!0,this._keyEvent("previous",s);break;case n.DOWN:t=!0,this._keyEvent("next",s);break;case n.ENTER:case n.NUMPAD_ENTER:this.menu.active&&(t=!0,s.preventDefault(),this.menu.select(s));break;case n.TAB:this.menu.active&&this.menu.select(s);break;case n.ESCAPE:this.menu.element.is(":visible")&&(this._value(this.term),this.close(s),s.preventDefault());break;default:i=!0,this._searchTimeout(s)}},keypress:function(a){if(t)return t=!1,a.preventDefault(),undefined;if(!i){var s=e.ui.keyCode;switch(a.keyCode){case s.PAGE_UP:this._move("previousPage",a);break;case s.PAGE_DOWN:this._move("nextPage",a);break;case s.UP:this._keyEvent("previous",a);break;case s.DOWN:this._keyEvent("next",a)}}},input:function(e){return a?(a=!1,e.preventDefault(),undefined):(this._searchTimeout(e),undefined)},focus:function(){this.selectedItem=null,this.previous=this._value()},blur:function(e){return this.cancelBlur?(delete this.cancelBlur,undefined):(clearTimeout(this.searching),this.close(e),this._change(e),undefined)}}),this._initSource(),this.menu=e("<ul>").addClass("ui-autocomplete").appendTo(this.document.find(this.options.appendTo||"body")[0]).menu({input:e(),role:null}).zIndex(this.element.zIndex()+1).hide().data("menu"),this._on(this.menu.element,{mousedown:function(t){t.preventDefault(),this.cancelBlur=!0,this._delay(function(){delete this.cancelBlur});var i=this.menu.element[0];e(t.target).closest(".ui-menu-item").length||this._delay(function(){var t=this;this.document.one("mousedown",function(a){a.target===t.element[0]||a.target===i||e.contains(i,a.target)||t.close()})})},menufocus:function(t,i){if(this.isNewMenu&&(this.isNewMenu=!1,t.originalEvent&&/^mouse/.test(t.originalEvent.type)))return this.menu.blur(),this.document.one("mousemove",function(){e(t.target).trigger(t.originalEvent)}),undefined;var a=i.item.data("ui-autocomplete-item")||i.item.data("item.autocomplete");!1!==this._trigger("focus",t,{item:a})?t.originalEvent&&/^key/.test(t.originalEvent.type)&&this._value(a.value):this.liveRegion.text(a.value)},menuselect:function(e,t){var i=t.item.data("ui-autocomplete-item")||t.item.data("item.autocomplete"),a=this.previous;this.element[0]!==this.document[0].activeElement&&(this.element.focus(),this.previous=a,this._delay(function(){this.previous=a,this.selectedItem=i})),!1!==this._trigger("select",e,{item:i})&&this._value(i.value),this.term=this._value(),this.close(e),this.selectedItem=i}}),this.liveRegion=e("<span>",{role:"status","aria-live":"polite"}).addClass("ui-helper-hidden-accessible").insertAfter(this.element),e.fn.bgiframe&&this.menu.element.bgiframe(),this._on(this.window,{beforeunload:function(){this.element.removeAttr("autocomplete")}})},_destroy:function(){clearTimeout(this.searching),this.element.removeClass("ui-autocomplete-input").removeAttr("autocomplete"),this.menu.element.remove(),this.liveRegion.remove()},_setOption:function(e,t){this._super(e,t),"source"===e&&this._initSource(),"appendTo"===e&&this.menu.element.appendTo(this.document.find(t||"body")[0]),"disabled"===e&&t&&this.xhr&&this.xhr.abort()},_isMultiLine:function(){return this.element.is("textarea")?!0:this.element.is("input")?!1:this.element.prop("isContentEditable")},_initSource:function(){var t,i,a=this;e.isArray(this.options.source)?(t=this.options.source,this.source=function(i,a){a(e.ui.autocomplete.filter(t,i.term))}):"string"==typeof this.options.source?(i=this.options.source,this.source=function(t,s){a.xhr&&a.xhr.abort(),a.xhr=e.ajax({url:i,data:t,dataType:"json",success:function(e){s(e)},error:function(){s([])}})}):this.source=this.options.source},_searchTimeout:function(e){clearTimeout(this.searching),this.searching=this._delay(function(){this.term!==this._value()&&(this.selectedItem=null,this.search(null,e))},this.options.delay)},search:function(e,t){return e=null!=e?e:this._value(),this.term=this._value(),e.length<this.options.minLength?this.close(t):this._trigger("search",t)!==!1?this._search(e):undefined},_search:function(e){this.pending++,this.element.addClass("ui-autocomplete-loading"),this.cancelSearch=!1,this.source({term:e},this._response())},_response:function(){var e=this,i=++t;return function(a){i===t&&e.__response(a),e.pending--,e.pending||e.element.removeClass("ui-autocomplete-loading")}},__response:function(e){e&&(e=this._normalize(e)),this._trigger("response",null,{content:e}),!this.options.disabled&&e&&e.length&&!this.cancelSearch?(this._suggest(e),this._trigger("open")):this._close()},close:function(e){this.cancelSearch=!0,this._close(e)},_close:function(e){this.menu.element.is(":visible")&&(this.menu.element.hide(),this.menu.blur(),this.isNewMenu=!0,this._trigger("close",e))},_change:function(e){this.previous!==this._value()&&this._trigger("change",e,{item:this.selectedItem})},_normalize:function(t){return t.length&&t[0].label&&t[0].value?t:e.map(t,function(t){return"string"==typeof t?{label:t,value:t}:e.extend({label:t.label||t.value,value:t.value||t.label},t)})},_suggest:function(t){var i=this.menu.element.empty().zIndex(this.element.zIndex()+1);this._renderMenu(i,t),this.menu.refresh(),i.show(),this._resizeMenu(),i.position(e.extend({of:this.element},this.options.position)),this.options.autoFocus&&this.menu.next()},_resizeMenu:function(){var e=this.menu.element;e.outerWidth(Math.max(e.width("").outerWidth()+1,this.element.outerWidth()))},_renderMenu:function(t,i){var a=this;e.each(i,function(e,i){a._renderItemData(t,i)})},_renderItemData:function(e,t){return this._renderItem(e,t).data("ui-autocomplete-item",t)},_renderItem:function(t,i){return e("<li>").append(e("<a>").text(i.label)).appendTo(t)},_move:function(e,t){return this.menu.element.is(":visible")?this.menu.isFirstItem()&&/^previous/.test(e)||this.menu.isLastItem()&&/^next/.test(e)?(this._value(this.term),this.menu.blur(),undefined):(this.menu[e](t),undefined):(this.search(null,t),undefined)},widget:function(){return this.menu.element},_value:function(){return this.valueMethod.apply(this.element,arguments)},_keyEvent:function(e,t){(!this.isMultiLine||this.menu.element.is(":visible"))&&(this._move(e,t),t.preventDefault())}}),e.extend(e.ui.autocomplete,{escapeRegex:function(e){return e.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g,"\\$&")},filter:function(t,i){var a=RegExp(e.ui.autocomplete.escapeRegex(i),"i");return e.grep(t,function(e){return a.test(e.label||e.value||e)})}}),e.widget("ui.autocomplete",e.ui.autocomplete,{options:{messages:{noResults:"No search results.",results:function(e){return e+(e>1?" results are":" result is")+" available, use up and down arrow keys to navigate."}}},__response:function(e){var t;this._superApply(arguments),this.options.disabled||this.cancelSearch||(t=e&&e.length?this.options.messages.results(e.length):this.options.messages.noResults,this.liveRegion.text(t))}})})(jQuery);(function(e){var t=!1;e.widget("ui.menu",{version:"1.9.2",defaultElement:"<ul>",delay:300,options:{icons:{submenu:"ui-icon-carat-1-e"},menus:"ul",position:{my:"left top",at:"right top"},role:"menu",blur:null,focus:null,select:null},_create:function(){this.activeMenu=this.element,this.element.uniqueId().addClass("ui-menu ui-widget ui-widget-content ui-corner-all").toggleClass("ui-menu-icons",!!this.element.find(".ui-icon").length).attr({role:this.options.role,tabIndex:0}).bind("click"+this.eventNamespace,e.proxy(function(e){this.options.disabled&&e.preventDefault()},this)),this.options.disabled&&this.element.addClass("ui-state-disabled").attr("aria-disabled","true"),this._on({"mousedown .ui-menu-item > a":function(e){e.preventDefault()},"click .ui-state-disabled > a":function(e){e.preventDefault()},"click .ui-menu-item:has(a)":function(i){var a=e(i.target).closest(".ui-menu-item");!t&&a.not(".ui-state-disabled").length&&(t=!0,this.select(i),a.has(".ui-menu").length?this.expand(i):this.element.is(":focus")||(this.element.trigger("focus",[!0]),this.active&&1===this.active.parents(".ui-menu").length&&clearTimeout(this.timer)))},"mouseenter .ui-menu-item":function(t){var i=e(t.currentTarget);i.siblings().children(".ui-state-active").removeClass("ui-state-active"),this.focus(t,i)},mouseleave:"collapseAll","mouseleave .ui-menu":"collapseAll",focus:function(e,t){var i=this.active||this.element.children(".ui-menu-item").eq(0);t||this.focus(e,i)},blur:function(t){this._delay(function(){e.contains(this.element[0],this.document[0].activeElement)||this.collapseAll(t)})},keydown:"_keydown"}),this.refresh(),this._on(this.document,{click:function(i){e(i.target).closest(".ui-menu").length||this.collapseAll(i),t=!1}})},_destroy:function(){this.element.removeAttr("aria-activedescendant").find(".ui-menu").andSelf().removeClass("ui-menu ui-widget ui-widget-content ui-corner-all ui-menu-icons").removeAttr("role").removeAttr("tabIndex").removeAttr("aria-labelledby").removeAttr("aria-expanded").removeAttr("aria-hidden").removeAttr("aria-disabled").removeUniqueId().show(),this.element.find(".ui-menu-item").removeClass("ui-menu-item").removeAttr("role").removeAttr("aria-disabled").children("a").removeUniqueId().removeClass("ui-corner-all ui-state-hover").removeAttr("tabIndex").removeAttr("role").removeAttr("aria-haspopup").children().each(function(){var t=e(this);t.data("ui-menu-submenu-carat")&&t.remove()}),this.element.find(".ui-menu-divider").removeClass("ui-menu-divider ui-widget-content")},_keydown:function(t){function i(e){return e.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g,"\\$&")}var a,s,n,r,o,h=!0;switch(t.keyCode){case e.ui.keyCode.PAGE_UP:this.previousPage(t);break;case e.ui.keyCode.PAGE_DOWN:this.nextPage(t);break;case e.ui.keyCode.HOME:this._move("first","first",t);break;case e.ui.keyCode.END:this._move("last","last",t);break;case e.ui.keyCode.UP:this.previous(t);break;case e.ui.keyCode.DOWN:this.next(t);break;case e.ui.keyCode.LEFT:this.collapse(t);break;case e.ui.keyCode.RIGHT:this.active&&!this.active.is(".ui-state-disabled")&&this.expand(t);break;case e.ui.keyCode.ENTER:case e.ui.keyCode.SPACE:this._activate(t);break;case e.ui.keyCode.ESCAPE:this.collapse(t);break;default:h=!1,s=this.previousFilter||"",n=String.fromCharCode(t.keyCode),r=!1,clearTimeout(this.filterTimer),n===s?r=!0:n=s+n,o=RegExp("^"+i(n),"i"),a=this.activeMenu.children(".ui-menu-item").filter(function(){return o.test(e(this).children("a").text())}),a=r&&-1!==a.index(this.active.next())?this.active.nextAll(".ui-menu-item"):a,a.length||(n=String.fromCharCode(t.keyCode),o=RegExp("^"+i(n),"i"),a=this.activeMenu.children(".ui-menu-item").filter(function(){return o.test(e(this).children("a").text())})),a.length?(this.focus(t,a),a.length>1?(this.previousFilter=n,this.filterTimer=this._delay(function(){delete this.previousFilter},1e3)):delete this.previousFilter):delete this.previousFilter}h&&t.preventDefault()},_activate:function(e){this.active.is(".ui-state-disabled")||(this.active.children("a[aria-haspopup='true']").length?this.expand(e):this.select(e))},refresh:function(){var t,i=this.options.icons.submenu,a=this.element.find(this.options.menus);a.filter(":not(.ui-menu)").addClass("ui-menu ui-widget ui-widget-content ui-corner-all").hide().attr({role:this.options.role,"aria-hidden":"true","aria-expanded":"false"}).each(function(){var t=e(this),a=t.prev("a"),s=e("<span>").addClass("ui-menu-icon ui-icon "+i).data("ui-menu-submenu-carat",!0);a.attr("aria-haspopup","true").prepend(s),t.attr("aria-labelledby",a.attr("id"))}),t=a.add(this.element),t.children(":not(.ui-menu-item):has(a)").addClass("ui-menu-item").attr("role","presentation").children("a").uniqueId().addClass("ui-corner-all").attr({tabIndex:-1,role:this._itemRole()}),t.children(":not(.ui-menu-item)").each(function(){var t=e(this);/[^\-â€”â€“\s]/.test(t.text())||t.addClass("ui-widget-content ui-menu-divider")}),t.children(".ui-state-disabled").attr("aria-disabled","true"),this.active&&!e.contains(this.element[0],this.active[0])&&this.blur()},_itemRole:function(){return{menu:"menuitem",listbox:"option"}[this.options.role]},focus:function(e,t){var i,a;this.blur(e,e&&"focus"===e.type),this._scrollIntoView(t),this.active=t.first(),a=this.active.children("a").addClass("ui-state-focus"),this.options.role&&this.element.attr("aria-activedescendant",a.attr("id")),this.active.parent().closest(".ui-menu-item").children("a:first").addClass("ui-state-active"),e&&"keydown"===e.type?this._close():this.timer=this._delay(function(){this._close()},this.delay),i=t.children(".ui-menu"),i.length&&/^mouse/.test(e.type)&&this._startOpening(i),this.activeMenu=t.parent(),this._trigger("focus",e,{item:t})},_scrollIntoView:function(t){var i,a,s,n,r,o;this._hasScroll()&&(i=parseFloat(e.css(this.activeMenu[0],"borderTopWidth"))||0,a=parseFloat(e.css(this.activeMenu[0],"paddingTop"))||0,s=t.offset().top-this.activeMenu.offset().top-i-a,n=this.activeMenu.scrollTop(),r=this.activeMenu.height(),o=t.height(),0>s?this.activeMenu.scrollTop(n+s):s+o>r&&this.activeMenu.scrollTop(n+s-r+o))},blur:function(e,t){t||clearTimeout(this.timer),this.active&&(this.active.children("a").removeClass("ui-state-focus"),this.active=null,this._trigger("blur",e,{item:this.active}))},_startOpening:function(e){clearTimeout(this.timer),"true"===e.attr("aria-hidden")&&(this.timer=this._delay(function(){this._close(),this._open(e)},this.delay))},_open:function(t){var i=e.extend({of:this.active},this.options.position);clearTimeout(this.timer),this.element.find(".ui-menu").not(t.parents(".ui-menu")).hide().attr("aria-hidden","true"),t.show().removeAttr("aria-hidden").attr("aria-expanded","true").position(i)},collapseAll:function(t,i){clearTimeout(this.timer),this.timer=this._delay(function(){var a=i?this.element:e(t&&t.target).closest(this.element.find(".ui-menu"));a.length||(a=this.element),this._close(a),this.blur(t),this.activeMenu=a},this.delay)},_close:function(e){e||(e=this.active?this.active.parent():this.element),e.find(".ui-menu").hide().attr("aria-hidden","true").attr("aria-expanded","false").end().find("a.ui-state-active").removeClass("ui-state-active")},collapse:function(e){var t=this.active&&this.active.parent().closest(".ui-menu-item",this.element);t&&t.length&&(this._close(),this.focus(e,t))},expand:function(e){var t=this.active&&this.active.children(".ui-menu ").children(".ui-menu-item").first();t&&t.length&&(this._open(t.parent()),this._delay(function(){this.focus(e,t)}))},next:function(e){this._move("next","first",e)},previous:function(e){this._move("prev","last",e)},isFirstItem:function(){return this.active&&!this.active.prevAll(".ui-menu-item").length},isLastItem:function(){return this.active&&!this.active.nextAll(".ui-menu-item").length},_move:function(e,t,i){var a;this.active&&(a="first"===e||"last"===e?this.active["first"===e?"prevAll":"nextAll"](".ui-menu-item").eq(-1):this.active[e+"All"](".ui-menu-item").eq(0)),a&&a.length&&this.active||(a=this.activeMenu.children(".ui-menu-item")[t]()),this.focus(i,a)},nextPage:function(t){var i,a,s;return this.active?(this.isLastItem()||(this._hasScroll()?(a=this.active.offset().top,s=this.element.height(),this.active.nextAll(".ui-menu-item").each(function(){return i=e(this),0>i.offset().top-a-s}),this.focus(t,i)):this.focus(t,this.activeMenu.children(".ui-menu-item")[this.active?"last":"first"]())),undefined):(this.next(t),undefined)},previousPage:function(t){var i,a,s;return this.active?(this.isFirstItem()||(this._hasScroll()?(a=this.active.offset().top,s=this.element.height(),this.active.prevAll(".ui-menu-item").each(function(){return i=e(this),i.offset().top-a+s>0}),this.focus(t,i)):this.focus(t,this.activeMenu.children(".ui-menu-item").first())),undefined):(this.next(t),undefined)},_hasScroll:function(){return this.element.outerHeight()<this.element.prop("scrollHeight")},select:function(t){this.active=this.active||e(t.target).closest(".ui-menu-item");var i={item:this.active};this.active.has(".ui-menu").length||this.collapseAll(t,!0),this._trigger("select",t,i)}})})(jQuery);/* ========================================================================
 * Bootstrap: affix.js v3.3.1
 * http://getbootstrap.com/javascript/#affix
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
    'use strict';

    // AFFIX CLASS DEFINITION
    // ======================

    var Affix = function (element, options) {
        this.options = $.extend({}, Affix.DEFAULTS, options)

        this.$target = $(this.options.target)
            .on('scroll.bs.affix.data-api', $.proxy(this.checkPosition, this))
            .on('click.bs.affix.data-api',  $.proxy(this.checkPositionWithEventLoop, this))

        this.$element     = $(element)
        this.affixed      =
            this.unpin        =
                this.pinnedOffset = null

        this.checkPosition()
    }

    Affix.VERSION  = '3.3.1'

    Affix.RESET    = 'affix affix-top affix-bottom'

    Affix.DEFAULTS = {
        offset: 0,
        target: window
    }

    Affix.prototype.getState = function (scrollHeight, height, offsetTop, offsetBottom) {
        var scrollTop    = this.$target.scrollTop()
        var position     = this.$element.offset()
        var targetHeight = this.$target.height()

        if (offsetTop != null && this.affixed == 'top') return scrollTop < offsetTop ? 'top' : false

        if (this.affixed == 'bottom') {
            if (offsetTop != null) return (scrollTop + this.unpin <= position.top) ? false : 'bottom'
            return (scrollTop + targetHeight <= scrollHeight - offsetBottom) ? false : 'bottom'
        }

        var initializing   = this.affixed == null
        var colliderTop    = initializing ? scrollTop : position.top
        var colliderHeight = initializing ? targetHeight : height

        if (offsetTop != null && colliderTop <= offsetTop) return 'top'
        if (offsetBottom != null && (colliderTop + colliderHeight >= scrollHeight - offsetBottom)) return 'bottom'

        return false
    }

    Affix.prototype.getPinnedOffset = function () {
        if (this.pinnedOffset) return this.pinnedOffset
        this.$element.removeClass(Affix.RESET).addClass('affix')
        var scrollTop = this.$target.scrollTop()
        var position  = this.$element.offset()
        return (this.pinnedOffset = position.top - scrollTop)
    }

    Affix.prototype.checkPositionWithEventLoop = function () {
        setTimeout($.proxy(this.checkPosition, this), 1)
    }

    Affix.prototype.checkPosition = function () {
        if (!this.$element.is(':visible')) return

        var height       = this.$element.height()
        var offset       = this.options.offset
        var offsetTop    = offset.top
        var offsetBottom = offset.bottom
        var scrollHeight = $('body').height()

        if (typeof offset != 'object')         offsetBottom = offsetTop = offset
        if (typeof offsetTop == 'function')    offsetTop    = offset.top(this.$element)
        if (typeof offsetBottom == 'function') offsetBottom = offset.bottom(this.$element)

        var affix = this.getState(scrollHeight, height, offsetTop, offsetBottom)

        if (this.affixed != affix) {
            if (this.unpin != null) this.$element.css('top', '')

            var affixType = 'affix' + (affix ? '-' + affix : '')
            var e         = $.Event(affixType + '.bs.affix')

            this.$element.trigger(e)

            if (e.isDefaultPrevented()) return

            this.affixed = affix
            this.unpin = affix == 'bottom' ? this.getPinnedOffset() : null

            this.$element
                .removeClass(Affix.RESET)
                .addClass(affixType)
                .trigger(affixType.replace('affix', 'affixed') + '.bs.affix')
        }

        if (affix == 'bottom') {
            this.$element.offset({
                top: scrollHeight - height - offsetBottom
            })
        }
    }


    // AFFIX PLUGIN DEFINITION
    // =======================

    function Plugin(option) {
        return this.each(function () {
            var $this   = $(this)
            var data    = $this.data('bs.affix')
            var options = typeof option == 'object' && option

            if (!data) $this.data('bs.affix', (data = new Affix(this, options)))
            if (typeof option == 'string') data[option]()
        })
    }

    var old = $.fn.affix

    $.fn.affix             = Plugin
    $.fn.affix.Constructor = Affix


    // AFFIX NO CONFLICT
    // =================

    $.fn.affix.noConflict = function () {
        $.fn.affix = old
        return this
    }


    // AFFIX DATA-API
    // ==============

    $(window).on('load', function () {
        $('[data-spy="affix"]').each(function () {
            var $spy = $(this)
            var data = $spy.data()

            data.offset = data.offset || {}

            if (data.offsetBottom != null) data.offset.bottom = data.offsetBottom
            if (data.offsetTop    != null) data.offset.top    = data.offsetTop

            Plugin.call($spy, data)
        })
    })

}(jQuery);
/* ========================================================================
 * Bootstrap: alert.js v3.3.1
 * http://getbootstrap.com/javascript/#alerts
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
    'use strict';

    // ALERT CLASS DEFINITION
    // ======================

    var dismiss = '[data-dismiss="alert"]'
    var Alert   = function (el) {
        $(el).on('click', dismiss, this.close)
    }

    Alert.VERSION = '3.3.1'

    Alert.TRANSITION_DURATION = 150

    Alert.prototype.close = function (e) {
        var $this    = $(this)
        var selector = $this.attr('data-target')

        if (!selector) {
            selector = $this.attr('href')
            selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
        }

        var $parent = $(selector)

        if (e) e.preventDefault()

        if (!$parent.length) {
            $parent = $this.closest('.alert')
        }

        $parent.trigger(e = $.Event('close.bs.alert'))

        if (e.isDefaultPrevented()) return

        $parent.removeClass('in')

        function removeElement() {
            // detach from parent, fire event then clean up data
            $parent.detach().trigger('closed.bs.alert').remove()
        }

        $.support.transition && $parent.hasClass('fade') ?
            $parent
                .one('bsTransitionEnd', removeElement)
                .emulateTransitionEnd(Alert.TRANSITION_DURATION) :
            removeElement()
    }


    // ALERT PLUGIN DEFINITION
    // =======================

    function Plugin(option) {
        return this.each(function () {
            var $this = $(this)
            var data  = $this.data('bs.alert')

            if (!data) $this.data('bs.alert', (data = new Alert(this)))
            if (typeof option == 'string') data[option].call($this)
        })
    }

    var old = $.fn.alert

    $.fn.alert             = Plugin
    $.fn.alert.Constructor = Alert


    // ALERT NO CONFLICT
    // =================

    $.fn.alert.noConflict = function () {
        $.fn.alert = old
        return this
    }


    // ALERT DATA-API
    // ==============

    $(document).on('click.bs.alert.data-api', dismiss, Alert.prototype.close)

}(jQuery);
/* ========================================================================
 * Bootstrap: collapse.js v3.3.1
 * http://getbootstrap.com/javascript/#collapse
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
    'use strict';

    // COLLAPSE PUBLIC CLASS DEFINITION
    // ================================

    var Collapse = function (element, options) {
        this.$element      = $(element)
        this.options       = $.extend({}, Collapse.DEFAULTS, options)
        this.$trigger      = $(this.options.trigger).filter('[href="#' + element.id + '"], [data-target="#' + element.id + '"]')
        this.transitioning = null

        if (this.options.parent) {
            this.$parent = this.getParent()
        } else {
            this.addAriaAndCollapsedClass(this.$element, this.$trigger)
        }

        if (this.options.toggle) this.toggle()
    }

    Collapse.VERSION  = '3.3.1'

    Collapse.TRANSITION_DURATION = 350

    Collapse.DEFAULTS = {
        toggle: true,
        trigger: '[data-toggle="collapse"]'
    }

    Collapse.prototype.dimension = function () {
        var hasWidth = this.$element.hasClass('width')
        return hasWidth ? 'width' : 'height'
    }

    Collapse.prototype.show = function () {
        if (this.transitioning || this.$element.hasClass('in')) return

        var activesData
        var actives = this.$parent && this.$parent.find('> .panel').children('.in, .collapsing')

        if (actives && actives.length) {
            activesData = actives.data('bs.collapse')
            if (activesData && activesData.transitioning) return
        }

        var startEvent = $.Event('show.bs.collapse')
        this.$element.trigger(startEvent)
        if (startEvent.isDefaultPrevented()) return

        if (actives && actives.length) {
            Plugin.call(actives, 'hide')
            activesData || actives.data('bs.collapse', null)
        }

        var dimension = this.dimension()

        this.$element
            .removeClass('collapse')
            .addClass('collapsing')[dimension](0)
            .attr('aria-expanded', true)

        this.$trigger
            .removeClass('collapsed')
            .attr('aria-expanded', true)

        this.transitioning = 1

        var complete = function () {
            this.$element
                .removeClass('collapsing')
                .addClass('collapse in')[dimension]('')
            this.transitioning = 0
            this.$element
                .trigger('shown.bs.collapse')
        }

        if (!$.support.transition) return complete.call(this)

        var scrollSize = $.camelCase(['scroll', dimension].join('-'))

        this.$element
            .one('bsTransitionEnd', $.proxy(complete, this))
            .emulateTransitionEnd(Collapse.TRANSITION_DURATION)[dimension](this.$element[0][scrollSize])
    }

    Collapse.prototype.hide = function () {
        if (this.transitioning || !this.$element.hasClass('in')) return

        var startEvent = $.Event('hide.bs.collapse')
        this.$element.trigger(startEvent)
        if (startEvent.isDefaultPrevented()) return

        var dimension = this.dimension()

        this.$element[dimension](this.$element[dimension]())[0].offsetHeight

        this.$element
            .addClass('collapsing')
            .removeClass('collapse in')
            .attr('aria-expanded', false)

        this.$trigger
            .addClass('collapsed')
            .attr('aria-expanded', false)

        this.transitioning = 1

        var complete = function () {
            this.transitioning = 0
            this.$element
                .removeClass('collapsing')
                .addClass('collapse')
                .trigger('hidden.bs.collapse')
        }

        if (!$.support.transition) return complete.call(this)

        this.$element
            [dimension](0)
            .one('bsTransitionEnd', $.proxy(complete, this))
            .emulateTransitionEnd(Collapse.TRANSITION_DURATION)
    }

    Collapse.prototype.toggle = function () {
        this[this.$element.hasClass('in') ? 'hide' : 'show']()
    }

    Collapse.prototype.getParent = function () {
        return $(this.options.parent)
            .find('[data-toggle="collapse"][data-parent="' + this.options.parent + '"]')
            .each($.proxy(function (i, element) {
                var $element = $(element)
                this.addAriaAndCollapsedClass(getTargetFromTrigger($element), $element)
            }, this))
            .end()
    }

    Collapse.prototype.addAriaAndCollapsedClass = function ($element, $trigger) {
        var isOpen = $element.hasClass('in')

        $element.attr('aria-expanded', isOpen)
        $trigger
            .toggleClass('collapsed', !isOpen)
            .attr('aria-expanded', isOpen)
    }

    function getTargetFromTrigger($trigger) {
        var href
        var target = $trigger.attr('data-target')
            || (href = $trigger.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '') // strip for ie7

        return $(target)
    }


    // COLLAPSE PLUGIN DEFINITION
    // ==========================

    function Plugin(option) {
        return this.each(function () {
            var $this   = $(this)
            var data    = $this.data('bs.collapse')
            var options = $.extend({}, Collapse.DEFAULTS, $this.data(), typeof option == 'object' && option)

            if (!data && options.toggle && option == 'show') options.toggle = false
            if (!data) $this.data('bs.collapse', (data = new Collapse(this, options)))
            if (typeof option == 'string') data[option]()
        })
    }

    var old = $.fn.collapse

    $.fn.collapse             = Plugin
    $.fn.collapse.Constructor = Collapse


    // COLLAPSE NO CONFLICT
    // ====================

    $.fn.collapse.noConflict = function () {
        $.fn.collapse = old
        return this
    }


    // COLLAPSE DATA-API
    // =================

    $(document).on('click.bs.collapse.data-api', '[data-toggle="collapse"]', function (e) {
        var $this   = $(this)

        if (!$this.attr('data-target')) e.preventDefault()

        var $target = getTargetFromTrigger($this)
        var data    = $target.data('bs.collapse')
        var option  = data ? 'toggle' : $.extend({}, $this.data(), { trigger: this })

        Plugin.call($target, option)
    })

}(jQuery);
/* ========================================================================
 * Bootstrap: dropdown.js v3.3.1
 * http://getbootstrap.com/javascript/#dropdowns
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
    'use strict';

    // DROPDOWN CLASS DEFINITION
    // =========================

    var backdrop = '.dropdown-backdrop'
    var toggle   = '[data-toggle="dropdown"]'
    var Dropdown = function (element) {
        $(element).on('click.bs.dropdown', this.toggle)
    }

    Dropdown.VERSION = '3.3.1'

    Dropdown.prototype.toggle = function (e) {
        var $this = $(this)

        if ($this.is('.disabled, :disabled')) return

        var $parent  = getParent($this)
        var isActive = $parent.hasClass('open')

        clearMenus()

        if (!isActive) {
            if ('ontouchstart' in document.documentElement && !$parent.closest('.navbar-nav').length) {
                // if mobile we use a backdrop because click events don't delegate
                $('<div class="dropdown-backdrop"/>').insertAfter($(this)).on('click', clearMenus)
            }

            var relatedTarget = { relatedTarget: this }
            $parent.trigger(e = $.Event('show.bs.dropdown', relatedTarget))

            if (e.isDefaultPrevented()) return

            $this
                .trigger('focus')
                .attr('aria-expanded', 'true')

            $parent
                .toggleClass('open')
                .trigger('shown.bs.dropdown', relatedTarget)
        }

        return false
    }

    Dropdown.prototype.keydown = function (e) {
        if (!/(38|40|27|32)/.test(e.which) || /input|textarea/i.test(e.target.tagName)) return

        var $this = $(this)

        e.preventDefault()
        e.stopPropagation()

        if ($this.is('.disabled, :disabled')) return

        var $parent  = getParent($this)
        var isActive = $parent.hasClass('open')

        if ((!isActive && e.which != 27) || (isActive && e.which == 27)) {
            if (e.which == 27) $parent.find(toggle).trigger('focus')
            return $this.trigger('click')
        }

        var desc = ' li:not(.divider):visible a'
        var $items = $parent.find('[role="menu"]' + desc + ', [role="listbox"]' + desc)

        if (!$items.length) return

        var index = $items.index(e.target)

        if (e.which == 38 && index > 0)                 index--                        // up
        if (e.which == 40 && index < $items.length - 1) index++                        // down
        if (!~index)                                      index = 0

        $items.eq(index).trigger('focus')
    }

    function clearMenus(e) {
        if (e && e.which === 3) return
        $(backdrop).remove()
        $(toggle).each(function () {
            var $this         = $(this)
            var $parent       = getParent($this)
            var relatedTarget = { relatedTarget: this }

            if (!$parent.hasClass('open')) return

            $parent.trigger(e = $.Event('hide.bs.dropdown', relatedTarget))

            if (e.isDefaultPrevented()) return

            $this.attr('aria-expanded', 'false')
            $parent.removeClass('open').trigger('hidden.bs.dropdown', relatedTarget)
        })
    }

    function getParent($this) {
        var selector = $this.attr('data-target')

        if (!selector) {
            selector = $this.attr('href')
            selector = selector && /#[A-Za-z]/.test(selector) && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
        }

        var $parent = selector && $(selector)

        return $parent && $parent.length ? $parent : $this.parent()
    }


    // DROPDOWN PLUGIN DEFINITION
    // ==========================

    function Plugin(option) {
        return this.each(function () {
            var $this = $(this)
            var data  = $this.data('bs.dropdown')

            if (!data) $this.data('bs.dropdown', (data = new Dropdown(this)))
            if (typeof option == 'string') data[option].call($this)
        })
    }

    var old = $.fn.dropdown

    $.fn.dropdown             = Plugin
    $.fn.dropdown.Constructor = Dropdown


    // DROPDOWN NO CONFLICT
    // ====================

    $.fn.dropdown.noConflict = function () {
        $.fn.dropdown = old
        return this
    }


    // APPLY TO STANDARD DROPDOWN ELEMENTS
    // ===================================

    $(document)
        .on('click.bs.dropdown.data-api', clearMenus)
        .on('click.bs.dropdown.data-api', '.dropdown form', function (e) { e.stopPropagation() })
        .on('click.bs.dropdown.data-api', toggle, Dropdown.prototype.toggle)
        .on('keydown.bs.dropdown.data-api', toggle, Dropdown.prototype.keydown)
        .on('keydown.bs.dropdown.data-api', '[role="menu"]', Dropdown.prototype.keydown)
        .on('keydown.bs.dropdown.data-api', '[role="listbox"]', Dropdown.prototype.keydown)

}(jQuery);
/* ========================================================================
 * Bootstrap: modal.js v3.3.1
 * http://getbootstrap.com/javascript/#modals
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
    'use strict';

    // MODAL CLASS DEFINITION
    // ======================

    var Modal = function (element, options) {
        this.options        = options
        this.$body          = $(document.body)
        this.$element       = $(element)
        this.$backdrop      =
            this.isShown        = null
        this.scrollbarWidth = 0

        if (this.options.remote) {
            this.$element
                .find('.modal-content')
                .load(this.options.remote, $.proxy(function () {
                    this.$element.trigger('loaded.bs.modal')
                }, this))
        }
    }

    Modal.VERSION  = '3.3.1'

    Modal.TRANSITION_DURATION = 300
    Modal.BACKDROP_TRANSITION_DURATION = 150

    Modal.DEFAULTS = {
        backdrop: true,
        keyboard: true,
        show: true
    }

    Modal.prototype.toggle = function (_relatedTarget) {
        return this.isShown ? this.hide() : this.show(_relatedTarget)
    }

    Modal.prototype.show = function (_relatedTarget) {
        var that = this
        var e    = $.Event('show.bs.modal', { relatedTarget: _relatedTarget })

        this.$element.trigger(e)

        if (this.isShown || e.isDefaultPrevented()) return

        this.isShown = true

        this.checkScrollbar()
        this.setScrollbar()
        this.$body.addClass('modal-open')

        this.escape()
        this.resize()

        this.$element.on('click.dismiss.bs.modal', '[data-dismiss="modal"]', $.proxy(this.hide, this))

        this.backdrop(function () {
            var transition = $.support.transition && that.$element.hasClass('fade')

            if (!that.$element.parent().length) {
                that.$element.appendTo(that.$body) // don't move modals dom position
            }

            that.$element
                .show()
                .scrollTop(0)

            if (that.options.backdrop) that.adjustBackdrop()
            that.adjustDialog()

            if (transition) {
                that.$element[0].offsetWidth // force reflow
            }

            that.$element
                .addClass('in')
                .attr('aria-hidden', false)

            that.enforceFocus()

            var e = $.Event('shown.bs.modal', { relatedTarget: _relatedTarget })

            transition ?
                that.$element.find('.modal-dialog') // wait for modal to slide in
                    .one('bsTransitionEnd', function () {
                        that.$element.trigger('focus').trigger(e)
                    })
                    .emulateTransitionEnd(Modal.TRANSITION_DURATION) :
                that.$element.trigger('focus').trigger(e)
        })
    }

    Modal.prototype.hide = function (e) {
        if (e) e.preventDefault()

        e = $.Event('hide.bs.modal')

        this.$element.trigger(e)

        if (!this.isShown || e.isDefaultPrevented()) return

        this.isShown = false

        this.escape()
        this.resize()

        $(document).off('focusin.bs.modal')

        this.$element
            .removeClass('in')
            .attr('aria-hidden', true)
            .off('click.dismiss.bs.modal')

        $.support.transition && this.$element.hasClass('fade') ?
            this.$element
                .one('bsTransitionEnd', $.proxy(this.hideModal, this))
                .emulateTransitionEnd(Modal.TRANSITION_DURATION) :
            this.hideModal()
    }

    Modal.prototype.enforceFocus = function () {
        $(document)
            .off('focusin.bs.modal') // guard against infinite focus loop
            .on('focusin.bs.modal', $.proxy(function (e) {
                if (this.$element[0] !== e.target && !this.$element.has(e.target).length) {
                    this.$element.trigger('focus')
                }
            }, this))
    }

    Modal.prototype.escape = function () {
        if (this.isShown && this.options.keyboard) {
            this.$element.on('keydown.dismiss.bs.modal', $.proxy(function (e) {
                e.which == 27 && this.hide()
            }, this))
        } else if (!this.isShown) {
            this.$element.off('keydown.dismiss.bs.modal')
        }
    }

    Modal.prototype.resize = function () {
        if (this.isShown) {
            $(window).on('resize.bs.modal', $.proxy(this.handleUpdate, this))
        } else {
            $(window).off('resize.bs.modal')
        }
    }

    Modal.prototype.hideModal = function () {
        var that = this
        this.$element.hide()
        this.backdrop(function () {
            that.$body.removeClass('modal-open')
            that.resetAdjustments()
            that.resetScrollbar()
            that.$element.trigger('hidden.bs.modal')
        })
    }

    Modal.prototype.removeBackdrop = function () {
        this.$backdrop && this.$backdrop.remove()
        this.$backdrop = null
    }

    Modal.prototype.backdrop = function (callback) {
        var that = this
        var animate = this.$element.hasClass('fade') ? 'fade' : ''

        if (this.isShown && this.options.backdrop) {
            var doAnimate = $.support.transition && animate

            this.$backdrop = $('<div class="modal-backdrop ' + animate + '" />')
                .prependTo(this.$element)
                .on('click.dismiss.bs.modal', $.proxy(function (e) {
                    if (e.target !== e.currentTarget) return
                    this.options.backdrop == 'static'
                        ? this.$element[0].focus.call(this.$element[0])
                        : this.hide.call(this)
                }, this))

            if (doAnimate) this.$backdrop[0].offsetWidth // force reflow

            this.$backdrop.addClass('in')

            if (!callback) return

            doAnimate ?
                this.$backdrop
                    .one('bsTransitionEnd', callback)
                    .emulateTransitionEnd(Modal.BACKDROP_TRANSITION_DURATION) :
                callback()

        } else if (!this.isShown && this.$backdrop) {
            this.$backdrop.removeClass('in')

            var callbackRemove = function () {
                that.removeBackdrop()
                callback && callback()
            }
            $.support.transition && this.$element.hasClass('fade') ?
                this.$backdrop
                    .one('bsTransitionEnd', callbackRemove)
                    .emulateTransitionEnd(Modal.BACKDROP_TRANSITION_DURATION) :
                callbackRemove()

        } else if (callback) {
            callback()
        }
    }

    // these following methods are used to handle overflowing modals

    Modal.prototype.handleUpdate = function () {
        if (this.options.backdrop) this.adjustBackdrop()
        this.adjustDialog()
    }

    Modal.prototype.adjustBackdrop = function () {
        this.$backdrop
            .css('height', 0)
            .css('height', this.$element[0].scrollHeight)
    }

    Modal.prototype.adjustDialog = function () {
        var modalIsOverflowing = this.$element[0].scrollHeight > document.documentElement.clientHeight

        this.$element.css({
            paddingLeft:  !this.bodyIsOverflowing && modalIsOverflowing ? this.scrollbarWidth : '',
            paddingRight: this.bodyIsOverflowing && !modalIsOverflowing ? this.scrollbarWidth : ''
        })
    }

    Modal.prototype.resetAdjustments = function () {
        this.$element.css({
            paddingLeft: '',
            paddingRight: ''
        })
    }

    Modal.prototype.checkScrollbar = function () {
        this.bodyIsOverflowing = document.body.scrollHeight > document.documentElement.clientHeight
        this.scrollbarWidth = this.measureScrollbar()
    }

    Modal.prototype.setScrollbar = function () {
        var bodyPad = parseInt((this.$body.css('padding-right') || 0), 10)
        if (this.bodyIsOverflowing) this.$body.css('padding-right', bodyPad + this.scrollbarWidth)
    }

    Modal.prototype.resetScrollbar = function () {
        this.$body.css('padding-right', '')
    }

    Modal.prototype.measureScrollbar = function () { // thx walsh
        var scrollDiv = document.createElement('div')
        scrollDiv.className = 'modal-scrollbar-measure'
        this.$body.append(scrollDiv)
        var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth
        this.$body[0].removeChild(scrollDiv)
        return scrollbarWidth
    }


    // MODAL PLUGIN DEFINITION
    // =======================

    function Plugin(option, _relatedTarget) {
        return this.each(function () {
            var $this   = $(this)
            var data    = $this.data('bs.modal')
            var options = $.extend({}, Modal.DEFAULTS, $this.data(), typeof option == 'object' && option)

            if (!data) $this.data('bs.modal', (data = new Modal(this, options)))
            if (typeof option == 'string') data[option](_relatedTarget)
            else if (options.show) data.show(_relatedTarget)
        })
    }

    var old = $.fn.modal

    $.fn.modal             = Plugin
    $.fn.modal.Constructor = Modal


    // MODAL NO CONFLICT
    // =================

    $.fn.modal.noConflict = function () {
        $.fn.modal = old
        return this
    }


    // MODAL DATA-API
    // ==============

    $(document).on('click.bs.modal.data-api', '[data-toggle="modal"]', function (e) {
        var $this   = $(this)
        var href    = $this.attr('href')
        var $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))) // strip for ie7
        var option  = $target.data('bs.modal') ? 'toggle' : $.extend({ remote: !/#/.test(href) && href }, $target.data(), $this.data())

        if ($this.is('a')) e.preventDefault()

        $target.one('show.bs.modal', function (showEvent) {
            if (showEvent.isDefaultPrevented()) return // only register focus restorer if modal will actually get shown
            $target.one('hidden.bs.modal', function () {
                $this.is(':visible') && $this.trigger('focus')
            })
        })
        Plugin.call($target, option, this)
    })

}(jQuery);
/* ========================================================================
 * Bootstrap: tab.js v3.3.1
 * http://getbootstrap.com/javascript/#tabs
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
    'use strict';

    // TAB CLASS DEFINITION
    // ====================

    var Tab = function (element) {
        this.element = $(element)
    }

    Tab.VERSION = '3.3.1'

    Tab.TRANSITION_DURATION = 150

    Tab.prototype.show = function () {
        var $this    = this.element
        var $ul      = $this.closest('ul:not(.dropdown-menu)')
        var selector = $this.data('target')

        if (!selector) {
            selector = $this.attr('href')
            selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
        }

        if ($this.parent('li').hasClass('active')) return

        var $previous = $ul.find('.active:last a')
        var hideEvent = $.Event('hide.bs.tab', {
            relatedTarget: $this[0]
        })
        var showEvent = $.Event('show.bs.tab', {
            relatedTarget: $previous[0]
        })

        $previous.trigger(hideEvent)
        $this.trigger(showEvent)

        if (showEvent.isDefaultPrevented() || hideEvent.isDefaultPrevented()) return

        var $target = $(selector)

        this.activate($this.closest('li'), $ul)
        this.activate($target, $target.parent(), function () {
            $previous.trigger({
                type: 'hidden.bs.tab',
                relatedTarget: $this[0]
            })
            $this.trigger({
                type: 'shown.bs.tab',
                relatedTarget: $previous[0]
            })
        })
    }

    Tab.prototype.activate = function (element, container, callback) {
        var $active    = container.find('> .active')
        var transition = callback
            && $.support.transition
            && (($active.length && $active.hasClass('fade')) || !!container.find('> .fade').length)

        function next() {
            $active
                .removeClass('active')
                .find('> .dropdown-menu > .active')
                .removeClass('active')
                .end()
                .find('[data-toggle="tab"]')
                .attr('aria-expanded', false)

            element
                .addClass('active')
                .find('[data-toggle="tab"]')
                .attr('aria-expanded', true)

            if (transition) {
                element[0].offsetWidth // reflow for transition
                element.addClass('in')
            } else {
                element.removeClass('fade')
            }

            if (element.parent('.dropdown-menu')) {
                element
                    .closest('li.dropdown')
                    .addClass('active')
                    .end()
                    .find('[data-toggle="tab"]')
                    .attr('aria-expanded', true)
            }

            callback && callback()
        }

        $active.length && transition ?
            $active
                .one('bsTransitionEnd', next)
                .emulateTransitionEnd(Tab.TRANSITION_DURATION) :
            next()

        $active.removeClass('in')
    }


    // TAB PLUGIN DEFINITION
    // =====================

    function Plugin(option) {
        return this.each(function () {
            var $this = $(this)
            var data  = $this.data('bs.tab')

            if (!data) $this.data('bs.tab', (data = new Tab(this)))
            if (typeof option == 'string') data[option]()
        })
    }

    var old = $.fn.tab

    $.fn.tab             = Plugin
    $.fn.tab.Constructor = Tab


    // TAB NO CONFLICT
    // ===============

    $.fn.tab.noConflict = function () {
        $.fn.tab = old
        return this
    }


    // TAB DATA-API
    // ============

    var clickHandler = function (e) {
        e.preventDefault()
        Plugin.call($(this), 'show')
    }

    $(document)
        .on('click.bs.tab.data-api', '[data-toggle="tab"]', clickHandler)
        .on('click.bs.tab.data-api', '[data-toggle="pill"]', clickHandler)

}(jQuery);
/* ========================================================================
 * Bootstrap: transition.js v3.3.1
 * http://getbootstrap.com/javascript/#transitions
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
    'use strict';

    // CSS TRANSITION SUPPORT (Shoutout: http://www.modernizr.com/)
    // ============================================================

    function transitionEnd() {
        var el = document.createElement('bootstrap')

        var transEndEventNames = {
            WebkitTransition : 'webkitTransitionEnd',
            MozTransition    : 'transitionend',
            OTransition      : 'oTransitionEnd otransitionend',
            transition       : 'transitionend'
        }

        for (var name in transEndEventNames) {
            if (el.style[name] !== undefined) {
                return { end: transEndEventNames[name] }
            }
        }

        return false // explicit for ie8 (  ._.)
    }

    // http://blog.alexmaccaw.com/css-transitions
    $.fn.emulateTransitionEnd = function (duration) {
        var called = false
        var $el = this
        $(this).one('bsTransitionEnd', function () { called = true })
        var callback = function () { if (!called) $($el).trigger($.support.transition.end) }
        setTimeout(callback, duration)
        return this
    }

    $(function () {
        $.support.transition = transitionEnd()

        if (!$.support.transition) return

        $.event.special.bsTransitionEnd = {
            bindType: $.support.transition.end,
            delegateType: $.support.transition.end,
            handle: function (e) {
                if ($(e.target).is(this)) return e.handleObj.handler.apply(this, arguments)
            }
        }
    })

}(jQuery);
// SweetAlert
// 2014 (c) - Tristan Edwards
// github.com/t4t5/sweetalert
(function(window, document) {

    var modalClass   = '.sweet-alert',
        overlayClass = '.sweet-overlay',
        alertTypes   = ['error', 'warning', 'info', 'success'],
        defaultParams = {
            title: '',
            text: '',
            type: null,
            allowOutsideClick: false,
            showCancelButton: false,
            closeOnConfirm: true,
            closeOnCancel: true,
            confirmButtonText: 'OK',
            confirmButtonClass: 'btn-primary',
            cancelButtonText: 'Cancel',
            imageUrl: null,
            imageSize: null,
            timer: null
        };


    /*
   * Manipulate DOM
   */

    var getModal = function() {
            return document.querySelector(modalClass);
        },
        getOverlay = function() {
            return document.querySelector(overlayClass);
        },
        hasClass = function(elem, className) {
            return new RegExp(' ' + className + ' ').test(' ' + elem.className + ' ');
        },
        addClass = function(elem, className) {
            if (!hasClass(elem, className)) {
                elem.className += ' ' + className;
            }
        },
        removeClass = function(elem, className) {
            var newClass = ' ' + elem.className.replace(/[\t\r\n]/g, ' ') + ' ';
            if (hasClass(elem, className)) {
                while (newClass.indexOf(' ' + className + ' ') >= 0) {
                    newClass = newClass.replace(' ' + className + ' ', ' ');
                }
                elem.className = newClass.replace(/^\s+|\s+$/g, '');
            }
        },
        escapeHtml = function(str) {
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(str));
            return div.innerHTML;
        },
        _show = function(elem) {
            elem.style.opacity = '';
            elem.style.display = 'block';
        },
        show = function(elems) {
            if (elems && !elems.length) {
                return _show(elems);
            }
            for (var i = 0; i < elems.length; ++i) {
                _show(elems[i]);
            }
        },
        _hide = function(elem) {
            elem.style.opacity = '';
            elem.style.display = 'none';
        },
        hide = function(elems) {
            if (elems && !elems.length) {
                return _hide(elems);
            }
            for (var i = 0; i < elems.length; ++i) {
                _hide(elems[i]);
            }
        },
        isDescendant = function(parent, child) {
            var node = child.parentNode;
            while (node !== null) {
                if (node === parent) {
                    return true;
                }
                node = node.parentNode;
            }
            return false;
        },
        getTopMargin = function(elem) {
            elem.style.left = '-9999px';
            elem.style.display = 'block';

            var height = elem.clientHeight;
            var padding = parseInt(getComputedStyle(elem).getPropertyValue('padding'), 10);

            elem.style.left = '';
            elem.style.display = 'none';
            return ('-' + parseInt(height / 2 + padding) + 'px');
        },
        fadeIn = function(elem, interval) {
            if(+elem.style.opacity < 1) {
                interval = interval || 16;
                elem.style.opacity = 0;
                elem.style.display = 'block';
                var last = +new Date();
                var tick = function() {
                    elem.style.opacity = +elem.style.opacity + (new Date() - last) / 100;
                    last = +new Date();

                    if (+elem.style.opacity < 1) {
                        setTimeout(tick, interval);
                    }
                };
                tick();
            }
        },
        fadeOut = function(elem, interval) {
            interval = interval || 16;
            elem.style.opacity = 1;
            var last = +new Date();
            var tick = function() {
                elem.style.opacity = +elem.style.opacity - (new Date() - last) / 100;
                last = +new Date();

                if (+elem.style.opacity > 0) {
                    setTimeout(tick, interval);
                } else {
                    elem.style.display = 'none';
                }
            };
            tick();
        },
        fireClick = function(node) {
            // Taken from http://www.nonobtrusive.com/2011/11/29/programatically-fire-crossbrowser-click-event-with-javascript/
            // Then fixed for today's Chrome browser.
            if (MouseEvent) {
                // Up-to-date approach
                var mevt = new MouseEvent('click', {
                    view: window,
                    bubbles: false,
                    cancelable: true
                });
                node.dispatchEvent(mevt);
            } else if ( document.createEvent ) {
                // Fallback
                var evt = document.createEvent('MouseEvents');
                evt.initEvent('click', false, false);
                node.dispatchEvent(evt);
            } else if( document.createEventObject ) {
                node.fireEvent('onclick') ;
            } else if (typeof node.onclick === 'function' ) {
                node.onclick();
            }
        },
        stopEventPropagation = function(e) {
            // In particular, make sure the space bar doesn't scroll the main window.
            if (typeof e.stopPropagation === 'function') {
                e.stopPropagation();
                e.preventDefault();
            } else if (window.event && window.event.hasOwnProperty('cancelBubble')) {
                window.event.cancelBubble = true;
            }
        };

    // Remember state in cases where opening and handling a modal will fiddle with it.
    var previousActiveElement,
        previousDocumentClick,
        previousWindowKeyDown,
        lastFocusedButton;

    /*
   * Add modal + overlay to DOM
   */

    window.sweetAlertInitialize = function() {
        var sweetHTML = '<div class="sweet-overlay" tabIndex="-1"></div><div class="sweet-alert" tabIndex="-1"><div class="icon error"><span class="x-mark"><span class="line left"></span><span class="line right"></span></span></div><div class="icon warning"> <span class="body"></span> <span class="dot"></span> </div> <div class="icon info"></div> <div class="icon success"> <span class="line tip"></span> <span class="line long"></span> <div class="placeholder"></div> <div class="fix"></div> </div> <div class="icon custom"></div> <h2>Title</h2><p class="lead text-muted">Text</p><p><button class="cancel btn btn-default btn-lg" tabIndex="2">Cancel</button> <button class="confirm btn btn-lg" tabIndex="1">OK</button></p></div>',
            sweetWrap = document.createElement('div');

        sweetWrap.innerHTML = sweetHTML;

        // For readability: check sweet-alert.html
        document.body.appendChild(sweetWrap);

        // For development use only!
        /*jQuery.ajax({
      url: '../lib/sweet-alert.html', // Change path depending on file location
      dataType: 'html'
    })
    .done(function(html) {
      jQuery('body').append(html);
    });*/
    }

    /*
   * Global sweetAlert function
   */

    window.sweetAlert = window.swal = function() {
        if (arguments[0] === undefined) {
            window.console.error('sweetAlert expects at least 1 attribute!');
            return false;
        }

        var params = extend({}, defaultParams);

        switch (typeof arguments[0]) {

            case 'string':
                params.title = arguments[0];
                params.text  = arguments[1] || '';
                params.type  = arguments[2] || '';

                break;

            case 'object':
                if (arguments[0].title === undefined) {
                    window.console.error('Missing "title" argument!');
                    return false;
                }

                params.title              = arguments[0].title;
                params.text               = arguments[0].text || defaultParams.text;
                params.type               = arguments[0].type || defaultParams.type;
                params.allowOutsideClick  = arguments[0].allowOutsideClick || defaultParams.allowOutsideClick;
                params.showCancelButton   = arguments[0].showCancelButton !== undefined ? arguments[0].showCancelButton : defaultParams.showCancelButton;
                params.closeOnConfirm     = arguments[0].closeOnConfirm !== undefined ? arguments[0].closeOnConfirm : defaultParams.closeOnConfirm;
                params.closeOnCancel      = arguments[0].closeOnCancel !== undefined ? arguments[0].closeOnCancel : defaultParams.closeOnCancel;
                params.timer              = arguments[0].timer || defaultParams.timer;

                // Show "Confirm" instead of "OK" if cancel button is visible
                params.confirmButtonText  = (defaultParams.showCancelButton) ? 'Confirm' : defaultParams.confirmButtonText;
                params.confirmButtonText  = arguments[0].confirmButtonText || defaultParams.confirmButtonText;
                params.confirmButtonClass = arguments[0].confirmButtonClass || defaultParams.confirmButtonClass;
                params.cancelButtonText   = arguments[0].cancelButtonText || defaultParams.cancelButtonText;
                params.imageUrl           = arguments[0].imageUrl || defaultParams.imageUrl;
                params.imageSize          = arguments[0].imageSize || defaultParams.imageSize;
                params.doneFunction       = arguments[1] || null;

                break;

            default:
                window.console.error('Unexpected type of argument! Expected "string" or "object", got ' + typeof arguments[0]);
                return false;

        }

        setParameters(params);
        fixVerticalPosition();
        openModal();


        // Modal interactions
        var modal = getModal();

        // Mouse interactions
        var onButtonEvent = function(e) {

            var target = e.target || e.srcElement,
                targetedConfirm    = (target.className.indexOf('confirm') > -1),
                modalIsVisible     = hasClass(modal, 'visible'),
                doneFunctionExists = (params.doneFunction && modal.getAttribute('data-has-done-function') === 'true');

            switch (e.type) {
                case ("click"):
                    if (targetedConfirm && doneFunctionExists && modalIsVisible) { // Clicked "confirm"

                        params.doneFunction(true);

                        if (params.closeOnConfirm) {
                            closeModal();
                        }
                    } else if (doneFunctionExists && modalIsVisible) { // Clicked "cancel"

                        // Check if callback function expects a parameter (to track cancel actions)
                        var functionAsStr          = String(params.doneFunction).replace(/\s/g, '');
                        var functionHandlesCancel  = functionAsStr.substring(0, 9) === "function(" && functionAsStr.substring(9, 10) !== ")";

                        if (functionHandlesCancel) {
                            params.doneFunction(false);
                        }

                        if (params.closeOnCancel) {
                            closeModal();
                        }
                    } else {
                        closeModal();
                    }

                    break;
            }
        };

        var $buttons = modal.querySelectorAll('button');
        for (var i = 0; i < $buttons.length; i++) {
            $buttons[i].onclick     = onButtonEvent;
        }

        // Remember the current document.onclick event.
        previousDocumentClick = document.onclick;
        document.onclick = function(e) {
            var target = e.target || e.srcElement;

            var clickedOnModal = (modal === target),
                clickedOnModalChild = isDescendant(modal, e.target),
                modalIsVisible = hasClass(modal, 'visible'),
                outsideClickIsAllowed = modal.getAttribute('data-allow-ouside-click') === 'true';

            if (!clickedOnModal && !clickedOnModalChild && modalIsVisible && outsideClickIsAllowed) {
                closeModal();
            }
        };


        // Keyboard interactions
        var $okButton = modal.querySelector('button.confirm'),
            $cancelButton = modal.querySelector('button.cancel'),
            $modalButtons = modal.querySelectorAll('button:not([type=hidden])');


        function handleKeyDown(e) {
            var keyCode = e.keyCode || e.which;

            if ([9,13,32,27].indexOf(keyCode) === -1) {
                // Don't do work on keys we don't care about.
                return;
            }

            var $targetElement = e.target || e.srcElement;

            var btnIndex = -1; // Find the button - note, this is a nodelist, not an array.
            for (var i = 0; i < $modalButtons.length; i++) {
                if ($targetElement === $modalButtons[i]) {
                    btnIndex = i;
                    break;
                }
            }

            if (keyCode === 9) {
                // TAB
                if (btnIndex === -1) {
                    // No button focused. Jump to the confirm button.
                    $targetElement = $okButton;
                } else {
                    // Cycle to the next button
                    if (btnIndex === $modalButtons.length - 1) {
                        $targetElement = $modalButtons[0];
                    } else {
                        $targetElement = $modalButtons[btnIndex + 1];
                    }
                }

                stopEventPropagation(e);
                $targetElement.focus();

            } else {
                if (keyCode === 13 || keyCode === 32) {
                    if (btnIndex === -1) {
                        // ENTER/SPACE clicked outside of a button.
                        $targetElement = $okButton;
                    } else {
                        // Do nothing - let the browser handle it.
                        $targetElement = undefined;
                    }
                } else if (keyCode === 27 && !($cancelButton.hidden || $cancelButton.style.display === 'none')) {
                    // ESC to cancel only if there's a cancel button displayed (like the alert() window).
                    $targetElement = $cancelButton;
                } else {
                    // Fallback - let the browser handle it.
                    $targetElement = undefined;
                }

                if ($targetElement !== undefined) {
                    fireClick($targetElement, e);
                }
            }
        }

        previousWindowKeyDown = window.onkeydown;
        window.onkeydown = handleKeyDown;

        function handleOnBlur(e) {
            var $targetElement = e.target || e.srcElement,
                $focusElement = e.relatedTarget,
                modalIsVisible = hasClass(modal, 'visible');

            if (modalIsVisible) {
                var btnIndex = -1; // Find the button - note, this is a nodelist, not an array.

                if ($focusElement !== null) {
                    // If we picked something in the DOM to focus to, let's see if it was a button.
                    for (var i = 0; i < $modalButtons.length; i++) {
                        if ($focusElement === $modalButtons[i]) {
                            btnIndex = i;
                            break;
                        }
                    }

                    if (btnIndex === -1) {
                        // Something in the dom, but not a visible button. Focus back on the button.
                        $targetElement.focus();
                    }
                } else {
                    // Exiting the DOM (e.g. clicked in the URL bar);
                    lastFocusedButton = $targetElement;
                }
            }
        }

        $okButton.onblur = handleOnBlur;
        $cancelButton.onblur = handleOnBlur;

        window.onfocus = function() {
            // When the user has focused away and focused back from the whole window.
            window.setTimeout(function() {
                // Put in a timeout to jump out of the event sequence. Calling focus() in the event
                // sequence confuses things.
                if (lastFocusedButton !== undefined) {
                    lastFocusedButton.focus();
                    lastFocusedButton = undefined;
                }
            }, 0);
        };
    };

    /**
     * Set default params for each popup
     * @param {Object} userParams
     */
    window.swal.setDefaults = function(userParams) {
        if (!userParams) {
            throw new Error('userParams is required');
        }
        if (typeof userParams !== 'object') {
            throw new Error('userParams has to be a object');
        }

        extend(defaultParams, userParams);
    };

    /*
   * Set type, text and actions on modal
   */

    function setParameters(params) {
        var modal = getModal();

        var $title = modal.querySelector('h2'),
            $text = modal.querySelector('p'),
            $cancelBtn = modal.querySelector('button.cancel'),
            $confirmBtn = modal.querySelector('button.confirm');

        // Title
        $title.innerHTML = escapeHtml(params.title).split("\n").join("<br>");

        // Text
        $text.innerHTML = escapeHtml(params.text || '').split("\n").join("<br>");
        if (params.text) {
            show($text);
        }

        // Icon
        hide(modal.querySelectorAll('.icon'));
        if (params.type) {
            var validType = false;
            for (var i = 0; i < alertTypes.length; i++) {
                if (params.type === alertTypes[i]) {
                    validType = true;
                    break;
                }
            }
            if (!validType) {
                window.console.error('Unknown alert type: ' + params.type);
                return false;
            }
            var $icon = modal.querySelector('.icon.' + params.type);
            show($icon);

            // Animate icon
            switch (params.type) {
                case "success":
                    addClass($icon, 'animate');
                    addClass($icon.querySelector('.tip'), 'animateSuccessTip');
                    addClass($icon.querySelector('.long'), 'animateSuccessLong');
                    break;
                case "error":
                    addClass($icon, 'animateErrorIcon');
                    addClass($icon.querySelector('.x-mark'), 'animateXMark');
                    break;
                case "warning":
                    addClass($icon, 'pulseWarning');
                    addClass($icon.querySelector('.body'), 'pulseWarningIns');
                    addClass($icon.querySelector('.dot'), 'pulseWarningIns');
                    break;
            }

        }

        // Custom image
        if (params.imageUrl) {
            var $customIcon = modal.querySelector('.icon.custom');

            $customIcon.style.backgroundImage = 'url(' + params.imageUrl + ')';
            show($customIcon);

            var _imgWidth  = 80,
                _imgHeight = 80;

            if (params.imageSize) {
                var imgWidth  = params.imageSize.split('x')[0];
                var imgHeight = params.imageSize.split('x')[1];

                if (!imgWidth || !imgHeight) {
                    window.console.error("Parameter imageSize expects value with format WIDTHxHEIGHT, got " + params.imageSize);
                } else {
                    _imgWidth  = imgWidth;
                    _imgHeight = imgHeight;

                    $customIcon.css({
                        'width': imgWidth + 'px',
                        'height': imgHeight + 'px'
                    });
                }
            }
            $customIcon.setAttribute('style', $customIcon.getAttribute('style') + 'width:' + _imgWidth + 'px; height:' + _imgHeight + 'px');
        }

        // Cancel button
        modal.setAttribute('data-has-cancel-button', params.showCancelButton);
        if (params.showCancelButton) {
            $cancelBtn.style.display = 'inline-block';
        } else {
            hide($cancelBtn);
        }

        // Edit text on cancel and confirm buttons
        if (params.cancelButtonText) {
            $cancelBtn.innerHTML = escapeHtml(params.cancelButtonText);
        }
        if (params.confirmButtonText) {
            $confirmBtn.innerHTML = escapeHtml(params.confirmButtonText);
        }

        // Reset confirm buttons to default class (Ugly fix)
        $confirmBtn.className = 'confirm btn btn-lg'

        // Set confirm button to selected class
        addClass($confirmBtn, params.confirmButtonClass);

        // Allow outside click?
        modal.setAttribute('data-allow-ouside-click', params.allowOutsideClick);

        // Done-function
        var hasDoneFunction = (params.doneFunction) ? true : false;
        modal.setAttribute('data-has-done-function', hasDoneFunction);

        // Close timer
        modal.setAttribute('data-timer', params.timer);
    }


    /*
   * Set hover, active and focus-states for buttons (source: http://www.sitepoint.com/javascript-generate-lighter-darker-color)
   */

    function colorLuminance(hex, lum) {
        // Validate hex string
        hex = String(hex).replace(/[^0-9a-f]/gi, '');
        if (hex.length < 6) {
            hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
        }
        lum = lum || 0;

        // Convert to decimal and change luminosity
        var rgb = "#", c, i;
        for (i = 0; i < 3; i++) {
            c = parseInt(hex.substr(i*2,2), 16);
            c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
            rgb += ("00"+c).substr(c.length);
        }

        return rgb;
    }

    function extend(a, b){
        for (var key in b) {
            if (b.hasOwnProperty(key)) {
                a[key] = b[key];
            }
        }

        return a;
    }

    function hexToRgb(hex) {
        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? parseInt(result[1], 16) + ', ' + parseInt(result[2], 16) + ', ' + parseInt(result[3], 16) : null;
    }

    // Add box-shadow style to button (depending on its chosen bg-color)
    function setFocusStyle($button, bgColor) {
        var rgbColor = hexToRgb(bgColor);
        $button.style.boxShadow = '0 0 2px rgba(' + rgbColor +', 0.8), inset 0 0 0 1px rgba(0, 0, 0, 0.05)';
    }


    /*
   * Animations
   */

    function openModal() {
        var modal = getModal();
        fadeIn(getOverlay(), 10);
        show(modal);
        addClass(modal, 'showSweetAlert');
        removeClass(modal, 'hideSweetAlert');

        previousActiveElement = document.activeElement;
        var $okButton = modal.querySelector('button.confirm');
        $okButton.focus();

        setTimeout(function() {
            addClass(modal, 'visible');
        }, 500);

        var timer = modal.getAttribute('data-timer');
        if (timer !== "null" && timer !== "") {
            setTimeout(function() {
                closeModal();
            }, timer);
        }
    }

    function closeModal() {
        var modal = getModal();
        fadeOut(getOverlay(), 5);
        fadeOut(modal, 5);
        removeClass(modal, 'showSweetAlert');
        addClass(modal, 'hideSweetAlert');
        removeClass(modal, 'visible');


        // Reset icon animations

        var $successIcon = modal.querySelector('.icon.success');
        removeClass($successIcon, 'animate');
        removeClass($successIcon.querySelector('.tip'), 'animateSuccessTip');
        removeClass($successIcon.querySelector('.long'), 'animateSuccessLong');

        var $errorIcon = modal.querySelector('.icon.error');
        removeClass($errorIcon, 'animateErrorIcon');
        removeClass($errorIcon.querySelector('.x-mark'), 'animateXMark');

        var $warningIcon = modal.querySelector('.icon.warning');
        removeClass($warningIcon, 'pulseWarning');
        removeClass($warningIcon.querySelector('.body'), 'pulseWarningIns');
        removeClass($warningIcon.querySelector('.dot'), 'pulseWarningIns');


        // Reset the page to its previous state
        window.onkeydown = previousWindowKeyDown;
        document.onclick = previousDocumentClick;
        if (previousActiveElement) {
            previousActiveElement.focus();
        }
        lastFocusedButton = undefined;
    }


    /*
   * Set "margin-top"-property on modal based on its computed height
   */

    function fixVerticalPosition() {
        var modal = getModal();
        modal.style.marginTop = getTopMargin(getModal());
    }


    /*
   * If library is injected after page has loaded
   */

    (function () {
        if (document.readyState === "complete" || document.readyState === "interactive" && document.body) {
            sweetAlertInitialize();
        } else {
            if (document.addEventListener) {
                document.addEventListener('DOMContentLoaded', function factorial() {
                    document.removeEventListener('DOMContentLoaded', arguments.callee, false);
                    sweetAlertInitialize();
                }, false);
            } else if (document.attachEvent) {
                document.attachEvent('onreadystatechange', function() {
                    if (document.readyState === 'complete') {
                        document.detachEvent('onreadystatechange', arguments.callee);
                        sweetAlertInitialize();
                    }
                });
            }
        }
    })();

})(window, document);
(function ($) {

    'use strict';

    var dw, dh, rw, rh, lx, ly;

    var defaults = {

        // The text to display within the notice box while loading the zoom image.
        loadingNotice: 'Loading image',

        // The text to display within the notice box if an error occurs when loading the zoom image.
        errorNotice: 'The image could not be loaded',

        // The time (in milliseconds) to display the error notice.
        errorDuration: 2500,

        // Prevent clicks on the zoom image link.
        preventClicks: true,

        // Callback function to execute when the flyout is displayed.
        onShow: $.noop,

        // Callback function to execute when the flyout is removed.
        onHide: $.noop,

        // Callback function to execute when the cursor is moved while over the image.
        onMove: $.noop

    };

    /**
     * EasyZoom
     * @constructor
     * @param {Object} target
     * @param {Object} options (Optional)
     */
    function EasyZoom(target, options) {
        this.$target = $(target);
        this.opts = $.extend({}, defaults, options, this.$target.data());

        this.isOpen === undefined && this._init();
    }

    /**
     * Init
     * @private
     */
    EasyZoom.prototype._init = function() {
        this.$link   = this.$target.find('a');
        this.$image  = this.$target.find('img');

        this.$flyout = $('<div class="easyzoom-flyout" />');
        this.$notice = $('<div class="easyzoom-notice" />');

        this.$target.on({
            'mousemove.easyzoom touchmove.easyzoom': $.proxy(this._onMove, this),
            'mouseleave.easyzoom touchend.easyzoom': $.proxy(this._onLeave, this),
            'mouseenter.easyzoom touchstart.easyzoom': $.proxy(this._onEnter, this)
        });

        this.opts.preventClicks && this.$target.on('click.easyzoom', function(e) {
            e.preventDefault();
        });
    };

    /**
     * Show
     * @param {MouseEvent|TouchEvent} e
     * @param {Boolean} testMouseOver (Optional)
     */
    EasyZoom.prototype.show = function(e, testMouseOver) {
        var w1, h1, w2, h2;
        var self = this;

        if (!this.isReady) {
            return this._loadImage(this.$link.attr('href'), function() {
                if (self.isMouseOver || !testMouseOver) {
                    self.show(e);
                }
            });
        }

        this.$target.append(this.$flyout);

        w1 = this.$target.width();
        h1 = this.$target.height();

        w2 = this.$flyout.width();
        h2 = this.$flyout.height();

        dw = this.$zoom.width() - w2;
        dh = this.$zoom.height() - h2;

        rw = dw / w1;
        rh = dh / h1;

        this.isOpen = true;

        this.opts.onShow.call(this);

        e && this._move(e);
    };

    /**
     * On enter
     * @private
     * @param {Event} e
     */
    EasyZoom.prototype._onEnter = function(e) {
        var touches = e.originalEvent.touches;

        this.isMouseOver = true;

        if (!touches || touches.length == 1) {
            e.preventDefault();
            this.show(e, true);
        }
    };

    /**
     * On move
     * @private
     * @param {Event} e
     */
    EasyZoom.prototype._onMove = function(e) {
        if (!this.isOpen) return;

        e.preventDefault();
        this._move(e);
    };

    /**
     * On leave
     * @private
     */
    EasyZoom.prototype._onLeave = function() {
        this.isMouseOver = false;
        this.isOpen && this.hide();
    };

    /**
     * On load
     * @private
     * @param {Event} e
     */
    EasyZoom.prototype._onLoad = function(e) {
        // IE may fire a load event even on error so test the image dimensions
        if (!e.currentTarget.width) return;

        this.isReady = true;

        this.$notice.detach();
        this.$flyout.html(this.$zoom);
        this.$target.removeClass('is-loading').addClass('is-ready');

        e.data.call && e.data();
    };

    /**
     * On error
     * @private
     */
    EasyZoom.prototype._onError = function() {
        var self = this;

        this.$notice.text(this.opts.errorNotice);
        this.$target.removeClass('is-loading').addClass('is-error');

        this.detachNotice = setTimeout(function() {
            self.$notice.detach();
            self.detachNotice = null;
        }, this.opts.errorDuration);
    };

    /**
     * Load image
     * @private
     * @param {String} href
     * @param {Function} callback
     */
    EasyZoom.prototype._loadImage = function(href, callback) {
        var zoom = new Image;

        this.$target
            .addClass('is-loading')
            .append(this.$notice.text(this.opts.loadingNotice));

        this.$zoom = $(zoom)
            .on('error', $.proxy(this._onError, this))
            .on('load', callback, $.proxy(this._onLoad, this));

        zoom.style.position = 'absolute';
        zoom.src = href;
    };

    /**
     * Move
     * @private
     * @param {Event} e
     */
    EasyZoom.prototype._move = function(e) {

        if (e.type.indexOf('touch') === 0) {
            var touchlist = e.touches || e.originalEvent.touches;
            lx = touchlist[0].pageX;
            ly = touchlist[0].pageY;
        } else {
            lx = e.pageX || lx;
            ly = e.pageY || ly;
        }

        var offset  = this.$target.offset();
        var pt = ly - offset.top;
        var pl = lx - offset.left;
        var xt = Math.ceil(pt * rh);
        var xl = Math.ceil(pl * rw);

        // Close if outside
        if (xl < 0 || xt < 0 || xl > dw || xt > dh) {
            this.hide();
        } else {
            var top = xt * -1;
            var left = xl * -1;

            this.$zoom.css({
                top: top,
                left: left
            });

            this.opts.onMove.call(this, top, left);
        }

    };

    /**
     * Hide
     */
    EasyZoom.prototype.hide = function() {
        if (!this.isOpen) return;

        this.$flyout.detach();
        this.isOpen = false;

        this.opts.onHide.call(this);
    };

    /**
     * Swap
     * @param {String} standardSrc
     * @param {String} zoomHref
     * @param {String|Array} srcset (Optional)
     */
    EasyZoom.prototype.swap = function(standardSrc, zoomHref, srcset) {
        this.hide();
        this.isReady = false;

        this.detachNotice && clearTimeout(this.detachNotice);

        this.$notice.parent().length && this.$notice.detach();

        this.$target.removeClass('is-loading is-ready is-error');

        this.$image.attr({
            src: standardSrc,
            srcset: $.isArray(srcset) ? srcset.join() : srcset
        });

        this.$link.attr('href', zoomHref);
    };

    /**
     * Teardown
     */
    EasyZoom.prototype.teardown = function() {
        this.hide();

        this.$target
            .off('.easyzoom')
            .removeClass('is-loading is-ready is-error');

        this.detachNotice && clearTimeout(this.detachNotice);

        delete this.$link;
        delete this.$zoom;
        delete this.$image;
        delete this.$notice;
        delete this.$flyout;

        delete this.isOpen;
        delete this.isReady;
    };

    // jQuery plugin wrapper
    $.fn.easyZoom = function(options) {
        return this.each(function() {
            var api = $.data(this, 'easyZoom');

            if (!api) {
                $.data(this, 'easyZoom', new EasyZoom(this, options));
            } else if (api.isOpen === undefined) {
                api._init();
            }
        });
    };

    // AMD and CommonJS module compatibility
    if (typeof define === 'function' && define.amd){
        define(function() {
            return EasyZoom;
        });
    } else if (typeof module !== 'undefined' && module.exports) {
        module.exports = EasyZoom;
    }

})(jQuery);/*! PhotoSwipe - v4.1.1 - 2015-12-24
* http://photoswipe.com
* Copyright (c) 2015 Dmitry Semenov; */
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define(factory);
    } else if (typeof exports === 'object') {
        module.exports = factory();
    } else {
        root.PhotoSwipe = factory();
    }
})(this, function () {

    'use strict';
    var PhotoSwipe = function(template, UiClass, items, options){

        /*>>framework-bridge*/
        /**
         *
         * Set of generic functions used by gallery.
         *
         * You're free to modify anything here as long as functionality is kept.
         *
         */
        var framework = {
            features: null,
            bind: function(target, type, listener, unbind) {
                var methodName = (unbind ? 'remove' : 'add') + 'EventListener';
                type = type.split(' ');
                for(var i = 0; i < type.length; i++) {
                    if(type[i]) {
                        target[methodName]( type[i], listener, false);
                    }
                }
            },
            isArray: function(obj) {
                return (obj instanceof Array);
            },
            createEl: function(classes, tag) {
                var el = document.createElement(tag || 'div');
                if(classes) {
                    el.className = classes;
                }
                return el;
            },
            getScrollY: function() {
                var yOffset = window.pageYOffset;
                return yOffset !== undefined ? yOffset : document.documentElement.scrollTop;
            },
            unbind: function(target, type, listener) {
                framework.bind(target,type,listener,true);
            },
            removeClass: function(el, className) {
                var reg = new RegExp('(\\s|^)' + className + '(\\s|$)');
                el.className = el.className.replace(reg, ' ').replace(/^\s\s*/, '').replace(/\s\s*$/, '');
            },
            addClass: function(el, className) {
                if( !framework.hasClass(el,className) ) {
                    el.className += (el.className ? ' ' : '') + className;
                }
            },
            hasClass: function(el, className) {
                return el.className && new RegExp('(^|\\s)' + className + '(\\s|$)').test(el.className);
            },
            getChildByClass: function(parentEl, childClassName) {
                var node = parentEl.firstChild;
                while(node) {
                    if( framework.hasClass(node, childClassName) ) {
                        return node;
                    }
                    node = node.nextSibling;
                }
            },
            arraySearch: function(array, value, key) {
                var i = array.length;
                while(i--) {
                    if(array[i][key] === value) {
                        return i;
                    }
                }
                return -1;
            },
            extend: function(o1, o2, preventOverwrite) {
                for (var prop in o2) {
                    if (o2.hasOwnProperty(prop)) {
                        if(preventOverwrite && o1.hasOwnProperty(prop)) {
                            continue;
                        }
                        o1[prop] = o2[prop];
                    }
                }
            },
            easing: {
                sine: {
                    out: function(k) {
                        return Math.sin(k * (Math.PI / 2));
                    },
                    inOut: function(k) {
                        return - (Math.cos(Math.PI * k) - 1) / 2;
                    }
                },
                cubic: {
                    out: function(k) {
                        return --k * k * k + 1;
                    }
                }
                /*
			elastic: {
				out: function ( k ) {

					var s, a = 0.1, p = 0.4;
					if ( k === 0 ) return 0;
					if ( k === 1 ) return 1;
					if ( !a || a < 1 ) { a = 1; s = p / 4; }
					else s = p * Math.asin( 1 / a ) / ( 2 * Math.PI );
					return ( a * Math.pow( 2, - 10 * k) * Math.sin( ( k - s ) * ( 2 * Math.PI ) / p ) + 1 );

				},
			},
			back: {
				out: function ( k ) {
					var s = 1.70158;
					return --k * k * ( ( s + 1 ) * k + s ) + 1;
				}
			}
		*/
            },

            /**
             *
             * @return {object}
             *
             * {
             *  raf : request animation frame function
             *  caf : cancel animation frame function
             *  transfrom : transform property key (with vendor), or null if not supported
             *  oldIE : IE8 or below
             * }
             *
             */
            detectFeatures: function() {
                if(framework.features) {
                    return framework.features;
                }
                var helperEl = framework.createEl(),
                    helperStyle = helperEl.style,
                    vendor = '',
                    features = {};

                // IE8 and below
                features.oldIE = document.all && !document.addEventListener;

                features.touch = 'ontouchstart' in window;

                if(window.requestAnimationFrame) {
                    features.raf = window.requestAnimationFrame;
                    features.caf = window.cancelAnimationFrame;
                }

                features.pointerEvent = navigator.pointerEnabled || navigator.msPointerEnabled;

                // fix false-positive detection of old Android in new IE
                // (IE11 ua string contains "Android 4.0")

                if(!features.pointerEvent) {

                    var ua = navigator.userAgent;

                    // Detect if device is iPhone or iPod and if it's older than iOS 8
                    // http://stackoverflow.com/a/14223920
                    //
                    // This detection is made because of buggy top/bottom toolbars
                    // that don't trigger window.resize event.
                    // For more info refer to _isFixedPosition variable in core.js

                    if (/iP(hone|od)/.test(navigator.platform)) {
                        var v = (navigator.appVersion).match(/OS (\d+)_(\d+)_?(\d+)?/);
                        if(v && v.length > 0) {
                            v = parseInt(v[1], 10);
                            if(v >= 1 && v < 8 ) {
                                features.isOldIOSPhone = true;
                            }
                        }
                    }

                    // Detect old Android (before KitKat)
                    // due to bugs related to position:fixed
                    // http://stackoverflow.com/questions/7184573/pick-up-the-android-version-in-the-browser-by-javascript

                    var match = ua.match(/Android\s([0-9\.]*)/);
                    var androidversion =  match ? match[1] : 0;
                    androidversion = parseFloat(androidversion);
                    if(androidversion >= 1 ) {
                        if(androidversion < 4.4) {
                            features.isOldAndroid = true; // for fixed position bug & performance
                        }
                        features.androidVersion = androidversion; // for touchend bug
                    }
                    features.isMobileOpera = /opera mini|opera mobi/i.test(ua);

                    // p.s. yes, yes, UA sniffing is bad, propose your solution for above bugs.
                }

                var styleChecks = ['transform', 'perspective', 'animationName'],
                    vendors = ['', 'webkit','Moz','ms','O'],
                    styleCheckItem,
                    styleName;

                for(var i = 0; i < 4; i++) {
                    vendor = vendors[i];

                    for(var a = 0; a < 3; a++) {
                        styleCheckItem = styleChecks[a];

                        // uppercase first letter of property name, if vendor is present
                        styleName = vendor + (vendor ?
                            styleCheckItem.charAt(0).toUpperCase() + styleCheckItem.slice(1) :
                            styleCheckItem);

                        if(!features[styleCheckItem] && styleName in helperStyle ) {
                            features[styleCheckItem] = styleName;
                        }
                    }

                    if(vendor && !features.raf) {
                        vendor = vendor.toLowerCase();
                        features.raf = window[vendor+'RequestAnimationFrame'];
                        if(features.raf) {
                            features.caf = window[vendor+'CancelAnimationFrame'] ||
                                window[vendor+'CancelRequestAnimationFrame'];
                        }
                    }
                }

                if(!features.raf) {
                    var lastTime = 0;
                    features.raf = function(fn) {
                        var currTime = new Date().getTime();
                        var timeToCall = Math.max(0, 16 - (currTime - lastTime));
                        var id = window.setTimeout(function() { fn(currTime + timeToCall); }, timeToCall);
                        lastTime = currTime + timeToCall;
                        return id;
                    };
                    features.caf = function(id) { clearTimeout(id); };
                }

                // Detect SVG support
                features.svg = !!document.createElementNS &&
                    !!document.createElementNS('http://www.w3.org/2000/svg', 'svg').createSVGRect;

                framework.features = features;

                return features;
            }
        };

        framework.detectFeatures();

// Override addEventListener for old versions of IE
        if(framework.features.oldIE) {

            framework.bind = function(target, type, listener, unbind) {

                type = type.split(' ');

                var methodName = (unbind ? 'detach' : 'attach') + 'Event',
                    evName,
                    _handleEv = function() {
                        listener.handleEvent.call(listener);
                    };

                for(var i = 0; i < type.length; i++) {
                    evName = type[i];
                    if(evName) {

                        if(typeof listener === 'object' && listener.handleEvent) {
                            if(!unbind) {
                                listener['oldIE' + evName] = _handleEv;
                            } else {
                                if(!listener['oldIE' + evName]) {
                                    return false;
                                }
                            }

                            target[methodName]( 'on' + evName, listener['oldIE' + evName]);
                        } else {
                            target[methodName]( 'on' + evName, listener);
                        }

                    }
                }
            };

        }

        /*>>framework-bridge*/

        /*>>core*/
//function(template, UiClass, items, options)

        var self = this;

        /**
         * Static vars, don't change unless you know what you're doing.
         */
        var DOUBLE_TAP_RADIUS = 25,
            NUM_HOLDERS = 3;

        /**
         * Options
         */
        var _options = {
            allowPanToNext:true,
            spacing: 0.12,
            bgOpacity: 1,
            mouseUsed: false,
            loop: true,
            pinchToClose: true,
            closeOnScroll: true,
            closeOnVerticalDrag: true,
            verticalDragRange: 0.75,
            hideAnimationDuration: 333,
            showAnimationDuration: 333,
            showHideOpacity: false,
            focus: true,
            escKey: true,
            arrowKeys: true,
            mainScrollEndFriction: 0.35,
            panEndFriction: 0.35,
            isClickableElement: function(el) {
                return el.tagName === 'A';
            },
            getDoubleTapZoom: function(isMouseClick, item) {
                if(isMouseClick) {
                    return 1;
                } else {
                    return item.initialZoomLevel < 0.7 ? 1 : 1.33;
                }
            },
            maxSpreadZoom: 1.33,
            modal: true,

            // not fully implemented yet
            scaleMode: 'fit' // TODO
        };
        framework.extend(_options, options);


        /**
         * Private helper variables & functions
         */

        var _getEmptyPoint = function() {
            return {x:0,y:0};
        };

        var _isOpen,
            _isDestroying,
            _closedByScroll,
            _currentItemIndex,
            _containerStyle,
            _containerShiftIndex,
            _currPanDist = _getEmptyPoint(),
            _startPanOffset = _getEmptyPoint(),
            _panOffset = _getEmptyPoint(),
            _upMoveEvents, // drag move, drag end & drag cancel events array
            _downEvents, // drag start events array
            _globalEventHandlers,
            _viewportSize = {},
            _currZoomLevel,
            _startZoomLevel,
            _translatePrefix,
            _translateSufix,
            _updateSizeInterval,
            _itemsNeedUpdate,
            _currPositionIndex = 0,
            _offset = {},
            _slideSize = _getEmptyPoint(), // size of slide area, including spacing
            _itemHolders,
            _prevItemIndex,
            _indexDiff = 0, // difference of indexes since last content update
            _dragStartEvent,
            _dragMoveEvent,
            _dragEndEvent,
            _dragCancelEvent,
            _transformKey,
            _pointerEventEnabled,
            _isFixedPosition = true,
            _likelyTouchDevice,
            _modules = [],
            _requestAF,
            _cancelAF,
            _initalClassName,
            _initalWindowScrollY,
            _oldIE,
            _currentWindowScrollY,
            _features,
            _windowVisibleSize = {},
            _renderMaxResolution = false,

            // Registers PhotoSWipe module (History, Controller ...)
            _registerModule = function(name, module) {
                framework.extend(self, module.publicMethods);
                _modules.push(name);
            },

            _getLoopedId = function(index) {
                var numSlides = _getNumItems();
                if(index > numSlides - 1) {
                    return index - numSlides;
                } else  if(index < 0) {
                    return numSlides + index;
                }
                return index;
            },

            // Micro bind/trigger
            _listeners = {},
            _listen = function(name, fn) {
                if(!_listeners[name]) {
                    _listeners[name] = [];
                }
                return _listeners[name].push(fn);
            },
            _shout = function(name) {
                var listeners = _listeners[name];

                if(listeners) {
                    var args = Array.prototype.slice.call(arguments);
                    args.shift();

                    for(var i = 0; i < listeners.length; i++) {
                        listeners[i].apply(self, args);
                    }
                }
            },

            _getCurrentTime = function() {
                return new Date().getTime();
            },
            _applyBgOpacity = function(opacity) {
                _bgOpacity = opacity;
                self.bg.style.opacity = opacity * _options.bgOpacity;
            },

            _applyZoomTransform = function(styleObj,x,y,zoom,item) {
                if(!_renderMaxResolution || (item && item !== self.currItem) ) {
                    zoom = zoom / (item ? item.fitRatio : self.currItem.fitRatio);
                }

                styleObj[_transformKey] = _translatePrefix + x + 'px, ' + y + 'px' + _translateSufix + ' scale(' + zoom + ')';
            },
            _applyCurrentZoomPan = function( allowRenderResolution ) {
                if(_currZoomElementStyle) {

                    if(allowRenderResolution) {
                        if(_currZoomLevel > self.currItem.fitRatio) {
                            if(!_renderMaxResolution) {
                                _setImageSize(self.currItem, false, true);
                                _renderMaxResolution = true;
                            }
                        } else {
                            if(_renderMaxResolution) {
                                _setImageSize(self.currItem);
                                _renderMaxResolution = false;
                            }
                        }
                    }


                    _applyZoomTransform(_currZoomElementStyle, _panOffset.x, _panOffset.y, _currZoomLevel);
                }
            },
            _applyZoomPanToItem = function(item) {
                if(item.container) {

                    _applyZoomTransform(item.container.style,
                        item.initialPosition.x,
                        item.initialPosition.y,
                        item.initialZoomLevel,
                        item);
                }
            },
            _setTranslateX = function(x, elStyle) {
                elStyle[_transformKey] = _translatePrefix + x + 'px, 0px' + _translateSufix;
            },
            _moveMainScroll = function(x, dragging) {

                if(!_options.loop && dragging) {
                    var newSlideIndexOffset = _currentItemIndex + (_slideSize.x * _currPositionIndex - x) / _slideSize.x,
                        delta = Math.round(x - _mainScrollPos.x);

                    if( (newSlideIndexOffset < 0 && delta > 0) ||
                        (newSlideIndexOffset >= _getNumItems() - 1 && delta < 0) ) {
                        x = _mainScrollPos.x + delta * _options.mainScrollEndFriction;
                    }
                }

                _mainScrollPos.x = x;
                _setTranslateX(x, _containerStyle);
            },
            _calculatePanOffset = function(axis, zoomLevel) {
                var m = _midZoomPoint[axis] - _offset[axis];
                return _startPanOffset[axis] + _currPanDist[axis] + m - m * ( zoomLevel / _startZoomLevel );
            },

            _equalizePoints = function(p1, p2) {
                p1.x = p2.x;
                p1.y = p2.y;
                if(p2.id) {
                    p1.id = p2.id;
                }
            },
            _roundPoint = function(p) {
                p.x = Math.round(p.x);
                p.y = Math.round(p.y);
            },

            _mouseMoveTimeout = null,
            _onFirstMouseMove = function() {
                // Wait until mouse move event is fired at least twice during 100ms
                // We do this, because some mobile browsers trigger it on touchstart
                if(_mouseMoveTimeout ) {
                    framework.unbind(document, 'mousemove', _onFirstMouseMove);
                    framework.addClass(template, 'pswp--has_mouse');
                    _options.mouseUsed = true;
                    _shout('mouseUsed');
                }
                _mouseMoveTimeout = setTimeout(function() {
                    _mouseMoveTimeout = null;
                }, 100);
            },

            _bindEvents = function() {
                framework.bind(document, 'keydown', self);

                if(_features.transform) {
                    // don't bind click event in browsers that don't support transform (mostly IE8)
                    framework.bind(self.scrollWrap, 'click', self);
                }


                if(!_options.mouseUsed) {
                    framework.bind(document, 'mousemove', _onFirstMouseMove);
                }

                framework.bind(window, 'resize scroll', self);

                _shout('bindEvents');
            },

            _unbindEvents = function() {
                framework.unbind(window, 'resize', self);
                framework.unbind(window, 'scroll', _globalEventHandlers.scroll);
                framework.unbind(document, 'keydown', self);
                framework.unbind(document, 'mousemove', _onFirstMouseMove);

                if(_features.transform) {
                    framework.unbind(self.scrollWrap, 'click', self);
                }

                if(_isDragging) {
                    framework.unbind(window, _upMoveEvents, self);
                }

                _shout('unbindEvents');
            },

            _calculatePanBounds = function(zoomLevel, update) {
                var bounds = _calculateItemSize( self.currItem, _viewportSize, zoomLevel );
                if(update) {
                    _currPanBounds = bounds;
                }
                return bounds;
            },

            _getMinZoomLevel = function(item) {
                if(!item) {
                    item = self.currItem;
                }
                return item.initialZoomLevel;
            },
            _getMaxZoomLevel = function(item) {
                if(!item) {
                    item = self.currItem;
                }
                return item.w > 0 ? _options.maxSpreadZoom : 1;
            },

            // Return true if offset is out of the bounds
            _modifyDestPanOffset = function(axis, destPanBounds, destPanOffset, destZoomLevel) {
                if(destZoomLevel === self.currItem.initialZoomLevel) {
                    destPanOffset[axis] = self.currItem.initialPosition[axis];
                    return true;
                } else {
                    destPanOffset[axis] = _calculatePanOffset(axis, destZoomLevel);

                    if(destPanOffset[axis] > destPanBounds.min[axis]) {
                        destPanOffset[axis] = destPanBounds.min[axis];
                        return true;
                    } else if(destPanOffset[axis] < destPanBounds.max[axis] ) {
                        destPanOffset[axis] = destPanBounds.max[axis];
                        return true;
                    }
                }
                return false;
            },

            _setupTransforms = function() {

                if(_transformKey) {
                    // setup 3d transforms
                    var allow3dTransform = _features.perspective && !_likelyTouchDevice;
                    _translatePrefix = 'translate' + (allow3dTransform ? '3d(' : '(');
                    _translateSufix = _features.perspective ? ', 0px)' : ')';
                    return;
                }

                // Override zoom/pan/move functions in case old browser is used (most likely IE)
                // (so they use left/top/width/height, instead of CSS transform)

                _transformKey = 'left';
                framework.addClass(template, 'pswp--ie');

                _setTranslateX = function(x, elStyle) {
                    elStyle.left = x + 'px';
                };
                _applyZoomPanToItem = function(item) {

                    var zoomRatio = item.fitRatio > 1 ? 1 : item.fitRatio,
                        s = item.container.style,
                        w = zoomRatio * item.w,
                        h = zoomRatio * item.h;

                    s.width = w + 'px';
                    s.height = h + 'px';
                    s.left = item.initialPosition.x + 'px';
                    s.top = item.initialPosition.y + 'px';

                };
                _applyCurrentZoomPan = function() {
                    if(_currZoomElementStyle) {

                        var s = _currZoomElementStyle,
                            item = self.currItem,
                            zoomRatio = item.fitRatio > 1 ? 1 : item.fitRatio,
                            w = zoomRatio * item.w,
                            h = zoomRatio * item.h;

                        s.width = w + 'px';
                        s.height = h + 'px';


                        s.left = _panOffset.x + 'px';
                        s.top = _panOffset.y + 'px';
                    }

                };
            },

            _onKeyDown = function(e) {
                var keydownAction = '';
                if(_options.escKey && e.keyCode === 27) {
                    keydownAction = 'close';
                } else if(_options.arrowKeys) {
                    if(e.keyCode === 37) {
                        keydownAction = 'prev';
                    } else if(e.keyCode === 39) {
                        keydownAction = 'next';
                    }
                }

                if(keydownAction) {
                    // don't do anything if special key pressed to prevent from overriding default browser actions
                    // e.g. in Chrome on Mac cmd+arrow-left returns to previous page
                    if( !e.ctrlKey && !e.altKey && !e.shiftKey && !e.metaKey ) {
                        if(e.preventDefault) {
                            e.preventDefault();
                        } else {
                            e.returnValue = false;
                        }
                        self[keydownAction]();
                    }
                }
            },

            _onGlobalClick = function(e) {
                if(!e) {
                    return;
                }

                // don't allow click event to pass through when triggering after drag or some other gesture
                if(_moved || _zoomStarted || _mainScrollAnimating || _verticalDragInitiated) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            },

            _updatePageScrollOffset = function() {
                self.setScrollOffset(0, framework.getScrollY());
            };







// Micro animation engine
        var _animations = {},
            _numAnimations = 0,
            _stopAnimation = function(name) {
                if(_animations[name]) {
                    if(_animations[name].raf) {
                        _cancelAF( _animations[name].raf );
                    }
                    _numAnimations--;
                    delete _animations[name];
                }
            },
            _registerStartAnimation = function(name) {
                if(_animations[name]) {
                    _stopAnimation(name);
                }
                if(!_animations[name]) {
                    _numAnimations++;
                    _animations[name] = {};
                }
            },
            _stopAllAnimations = function() {
                for (var prop in _animations) {

                    if( _animations.hasOwnProperty( prop ) ) {
                        _stopAnimation(prop);
                    }

                }
            },
            _animateProp = function(name, b, endProp, d, easingFn, onUpdate, onComplete) {
                var startAnimTime = _getCurrentTime(), t;
                _registerStartAnimation(name);

                var animloop = function(){
                    if ( _animations[name] ) {

                        t = _getCurrentTime() - startAnimTime; // time diff
                        //b - beginning (start prop)
                        //d - anim duration

                        if ( t >= d ) {
                            _stopAnimation(name);
                            onUpdate(endProp);
                            if(onComplete) {
                                onComplete();
                            }
                            return;
                        }
                        onUpdate( (endProp - b) * easingFn(t/d) + b );

                        _animations[name].raf = _requestAF(animloop);
                    }
                };
                animloop();
            };



        var publicMethods = {

            // make a few local variables and functions public
            shout: _shout,
            listen: _listen,
            viewportSize: _viewportSize,
            options: _options,

            isMainScrollAnimating: function() {
                return _mainScrollAnimating;
            },
            getZoomLevel: function() {
                return _currZoomLevel;
            },
            getCurrentIndex: function() {
                return _currentItemIndex;
            },
            isDragging: function() {
                return _isDragging;
            },
            isZooming: function() {
                return _isZooming;
            },
            setScrollOffset: function(x,y) {
                _offset.x = x;
                _currentWindowScrollY = _offset.y = y;
                _shout('updateScrollOffset', _offset);
            },
            applyZoomPan: function(zoomLevel,panX,panY,allowRenderResolution) {
                _panOffset.x = panX;
                _panOffset.y = panY;
                _currZoomLevel = zoomLevel;
                _applyCurrentZoomPan( allowRenderResolution );
            },

            init: function() {

                if(_isOpen || _isDestroying) {
                    return;
                }

                var i;

                self.framework = framework; // basic functionality
                self.template = template; // root DOM element of PhotoSwipe
                self.bg = framework.getChildByClass(template, 'pswp__bg');

                _initalClassName = template.className;
                _isOpen = true;

                _features = framework.detectFeatures();
                _requestAF = _features.raf;
                _cancelAF = _features.caf;
                _transformKey = _features.transform;
                _oldIE = _features.oldIE;

                self.scrollWrap = framework.getChildByClass(template, 'pswp__scroll-wrap');
                self.container = framework.getChildByClass(self.scrollWrap, 'pswp__container');

                _containerStyle = self.container.style; // for fast access

                // Objects that hold slides (there are only 3 in DOM)
                self.itemHolders = _itemHolders = [
                    {el:self.container.children[0] , wrap:0, index: -1},
                    {el:self.container.children[1] , wrap:0, index: -1},
                    {el:self.container.children[2] , wrap:0, index: -1}
                ];

                // hide nearby item holders until initial zoom animation finishes (to avoid extra Paints)
                _itemHolders[0].el.style.display = _itemHolders[2].el.style.display = 'none';

                _setupTransforms();

                // Setup global events
                _globalEventHandlers = {
                    resize: self.updateSize,
                    scroll: _updatePageScrollOffset,
                    keydown: _onKeyDown,
                    click: _onGlobalClick
                };

                // disable show/hide effects on old browsers that don't support CSS animations or transforms,
                // old IOS, Android and Opera mobile. Blackberry seems to work fine, even older models.
                var oldPhone = _features.isOldIOSPhone || _features.isOldAndroid || _features.isMobileOpera;
                if(!_features.animationName || !_features.transform || oldPhone) {
                    _options.showAnimationDuration = _options.hideAnimationDuration = 0;
                }

                // init modules
                for(i = 0; i < _modules.length; i++) {
                    self['init' + _modules[i]]();
                }

                // init
                if(UiClass) {
                    var ui = self.ui = new UiClass(self, framework);
                    ui.init();
                }

                _shout('firstUpdate');
                _currentItemIndex = _currentItemIndex || _options.index || 0;
                // validate index
                if( isNaN(_currentItemIndex) || _currentItemIndex < 0 || _currentItemIndex >= _getNumItems() ) {
                    _currentItemIndex = 0;
                }
                self.currItem = _getItemAt( _currentItemIndex );


                if(_features.isOldIOSPhone || _features.isOldAndroid) {
                    _isFixedPosition = false;
                }

                template.setAttribute('aria-hidden', 'false');
                if(_options.modal) {
                    if(!_isFixedPosition) {
                        template.style.position = 'absolute';
                        template.style.top = framework.getScrollY() + 'px';
                    } else {
                        template.style.position = 'fixed';
                    }
                }

                if(_currentWindowScrollY === undefined) {
                    _shout('initialLayout');
                    _currentWindowScrollY = _initalWindowScrollY = framework.getScrollY();
                }

                // add classes to root element of PhotoSwipe
                var rootClasses = 'pswp--open ';
                if(_options.mainClass) {
                    rootClasses += _options.mainClass + ' ';
                }
                if(_options.showHideOpacity) {
                    rootClasses += 'pswp--animate_opacity ';
                }
                rootClasses += _likelyTouchDevice ? 'pswp--touch' : 'pswp--notouch';
                rootClasses += _features.animationName ? ' pswp--css_animation' : '';
                rootClasses += _features.svg ? ' pswp--svg' : '';
                framework.addClass(template, rootClasses);

                self.updateSize();

                // initial update
                _containerShiftIndex = -1;
                _indexDiff = null;
                for(i = 0; i < NUM_HOLDERS; i++) {
                    _setTranslateX( (i+_containerShiftIndex) * _slideSize.x, _itemHolders[i].el.style);
                }

                if(!_oldIE) {
                    framework.bind(self.scrollWrap, _downEvents, self); // no dragging for old IE
                }

                _listen('initialZoomInEnd', function() {
                    self.setContent(_itemHolders[0], _currentItemIndex-1);
                    self.setContent(_itemHolders[2], _currentItemIndex+1);

                    _itemHolders[0].el.style.display = _itemHolders[2].el.style.display = 'block';

                    if(_options.focus) {
                        // focus causes layout,
                        // which causes lag during the animation,
                        // that's why we delay it untill the initial zoom transition ends
                        template.focus();
                    }


                    _bindEvents();
                });

                // set content for center slide (first time)
                self.setContent(_itemHolders[1], _currentItemIndex);

                self.updateCurrItem();

                _shout('afterInit');

                if(!_isFixedPosition) {

                    // On all versions of iOS lower than 8.0, we check size of viewport every second.
                    //
                    // This is done to detect when Safari top & bottom bars appear,
                    // as this action doesn't trigger any events (like resize).
                    //
                    // On iOS8 they fixed this.
                    //
                    // 10 Nov 2014: iOS 7 usage ~40%. iOS 8 usage 56%.

                    _updateSizeInterval = setInterval(function() {
                        if(!_numAnimations && !_isDragging && !_isZooming && (_currZoomLevel === self.currItem.initialZoomLevel)  ) {
                            self.updateSize();
                        }
                    }, 1000);
                }

                framework.addClass(template, 'pswp--visible');
            },

            // Close the gallery, then destroy it
            close: function() {
                if(!_isOpen) {
                    return;
                }

                _isOpen = false;
                _isDestroying = true;
                _shout('close');
                _unbindEvents();

                _showOrHide(self.currItem, null, true, self.destroy);
            },

            // destroys the gallery (unbinds events, cleans up intervals and timeouts to avoid memory leaks)
            destroy: function() {
                _shout('destroy');

                if(_showOrHideTimeout) {
                    clearTimeout(_showOrHideTimeout);
                }

                template.setAttribute('aria-hidden', 'true');
                template.className = _initalClassName;

                if(_updateSizeInterval) {
                    clearInterval(_updateSizeInterval);
                }

                framework.unbind(self.scrollWrap, _downEvents, self);

                // we unbind scroll event at the end, as closing animation may depend on it
                framework.unbind(window, 'scroll', self);

                _stopDragUpdateLoop();

                _stopAllAnimations();

                _listeners = null;
            },

            /**
             * Pan image to position
             * @param {Number} x
             * @param {Number} y
             * @param {Boolean} force Will ignore bounds if set to true.
             */
            panTo: function(x,y,force) {
                if(!force) {
                    if(x > _currPanBounds.min.x) {
                        x = _currPanBounds.min.x;
                    } else if(x < _currPanBounds.max.x) {
                        x = _currPanBounds.max.x;
                    }

                    if(y > _currPanBounds.min.y) {
                        y = _currPanBounds.min.y;
                    } else if(y < _currPanBounds.max.y) {
                        y = _currPanBounds.max.y;
                    }
                }

                _panOffset.x = x;
                _panOffset.y = y;
                _applyCurrentZoomPan();
            },

            handleEvent: function (e) {
                e = e || window.event;
                if(_globalEventHandlers[e.type]) {
                    _globalEventHandlers[e.type](e);
                }
            },


            goTo: function(index) {

                index = _getLoopedId(index);

                var diff = index - _currentItemIndex;
                _indexDiff = diff;

                _currentItemIndex = index;
                self.currItem = _getItemAt( _currentItemIndex );
                _currPositionIndex -= diff;

                _moveMainScroll(_slideSize.x * _currPositionIndex);


                _stopAllAnimations();
                _mainScrollAnimating = false;

                self.updateCurrItem();
            },
            next: function() {
                self.goTo( _currentItemIndex + 1);
            },
            prev: function() {
                self.goTo( _currentItemIndex - 1);
            },

            // update current zoom/pan objects
            updateCurrZoomItem: function(emulateSetContent) {
                if(emulateSetContent) {
                    _shout('beforeChange', 0);
                }

                // itemHolder[1] is middle (current) item
                if(_itemHolders[1].el.children.length) {
                    var zoomElement = _itemHolders[1].el.children[0];
                    if( framework.hasClass(zoomElement, 'pswp__zoom-wrap') ) {
                        _currZoomElementStyle = zoomElement.style;
                    } else {
                        _currZoomElementStyle = null;
                    }
                } else {
                    _currZoomElementStyle = null;
                }

                _currPanBounds = self.currItem.bounds;
                _startZoomLevel = _currZoomLevel = self.currItem.initialZoomLevel;

                _panOffset.x = _currPanBounds.center.x;
                _panOffset.y = _currPanBounds.center.y;

                if(emulateSetContent) {
                    _shout('afterChange');
                }
            },


            invalidateCurrItems: function() {
                _itemsNeedUpdate = true;
                for(var i = 0; i < NUM_HOLDERS; i++) {
                    if( _itemHolders[i].item ) {
                        _itemHolders[i].item.needsUpdate = true;
                    }
                }
            },

            updateCurrItem: function(beforeAnimation) {

                if(_indexDiff === 0) {
                    return;
                }

                var diffAbs = Math.abs(_indexDiff),
                    tempHolder;

                if(beforeAnimation && diffAbs < 2) {
                    return;
                }


                self.currItem = _getItemAt( _currentItemIndex );
                _renderMaxResolution = false;

                _shout('beforeChange', _indexDiff);

                if(diffAbs >= NUM_HOLDERS) {
                    _containerShiftIndex += _indexDiff + (_indexDiff > 0 ? -NUM_HOLDERS : NUM_HOLDERS);
                    diffAbs = NUM_HOLDERS;
                }
                for(var i = 0; i < diffAbs; i++) {
                    if(_indexDiff > 0) {
                        tempHolder = _itemHolders.shift();
                        _itemHolders[NUM_HOLDERS-1] = tempHolder; // move first to last

                        _containerShiftIndex++;
                        _setTranslateX( (_containerShiftIndex+2) * _slideSize.x, tempHolder.el.style);
                        self.setContent(tempHolder, _currentItemIndex - diffAbs + i + 1 + 1);
                    } else {
                        tempHolder = _itemHolders.pop();
                        _itemHolders.unshift( tempHolder ); // move last to first

                        _containerShiftIndex--;
                        _setTranslateX( _containerShiftIndex * _slideSize.x, tempHolder.el.style);
                        self.setContent(tempHolder, _currentItemIndex + diffAbs - i - 1 - 1);
                    }

                }

                // reset zoom/pan on previous item
                if(_currZoomElementStyle && Math.abs(_indexDiff) === 1) {

                    var prevItem = _getItemAt(_prevItemIndex);
                    if(prevItem.initialZoomLevel !== _currZoomLevel) {
                        _calculateItemSize(prevItem , _viewportSize );
                        _setImageSize(prevItem);
                        _applyZoomPanToItem( prevItem );
                    }

                }

                // reset diff after update
                _indexDiff = 0;

                self.updateCurrZoomItem();

                _prevItemIndex = _currentItemIndex;

                _shout('afterChange');

            },



            updateSize: function(force) {

                if(!_isFixedPosition && _options.modal) {
                    var windowScrollY = framework.getScrollY();
                    if(_currentWindowScrollY !== windowScrollY) {
                        template.style.top = windowScrollY + 'px';
                        _currentWindowScrollY = windowScrollY;
                    }
                    if(!force && _windowVisibleSize.x === window.innerWidth && _windowVisibleSize.y === window.innerHeight) {
                        return;
                    }
                    _windowVisibleSize.x = window.innerWidth;
                    _windowVisibleSize.y = window.innerHeight;

                    //template.style.width = _windowVisibleSize.x + 'px';
                    template.style.height = _windowVisibleSize.y + 'px';
                }



                _viewportSize.x = self.scrollWrap.clientWidth;
                _viewportSize.y = self.scrollWrap.clientHeight;

                _updatePageScrollOffset();

                _slideSize.x = _viewportSize.x + Math.round(_viewportSize.x * _options.spacing);
                _slideSize.y = _viewportSize.y;

                _moveMainScroll(_slideSize.x * _currPositionIndex);

                _shout('beforeResize'); // even may be used for example to switch image sources


                // don't re-calculate size on inital size update
                if(_containerShiftIndex !== undefined) {

                    var holder,
                        item,
                        hIndex;

                    for(var i = 0; i < NUM_HOLDERS; i++) {
                        holder = _itemHolders[i];
                        _setTranslateX( (i+_containerShiftIndex) * _slideSize.x, holder.el.style);

                        hIndex = _currentItemIndex+i-1;

                        if(_options.loop && _getNumItems() > 2) {
                            hIndex = _getLoopedId(hIndex);
                        }

                        // update zoom level on items and refresh source (if needsUpdate)
                        item = _getItemAt( hIndex );

                        // re-render gallery item if `needsUpdate`,
                        // or doesn't have `bounds` (entirely new slide object)
                        if( item && (_itemsNeedUpdate || item.needsUpdate || !item.bounds) ) {

                            self.cleanSlide( item );

                            self.setContent( holder, hIndex );

                            // if "center" slide
                            if(i === 1) {
                                self.currItem = item;
                                self.updateCurrZoomItem(true);
                            }

                            item.needsUpdate = false;

                        } else if(holder.index === -1 && hIndex >= 0) {
                            // add content first time
                            self.setContent( holder, hIndex );
                        }
                        if(item && item.container) {
                            _calculateItemSize(item, _viewportSize);
                            _setImageSize(item);
                            _applyZoomPanToItem( item );
                        }

                    }
                    _itemsNeedUpdate = false;
                }

                _startZoomLevel = _currZoomLevel = self.currItem.initialZoomLevel;
                _currPanBounds = self.currItem.bounds;

                if(_currPanBounds) {
                    _panOffset.x = _currPanBounds.center.x;
                    _panOffset.y = _currPanBounds.center.y;
                    _applyCurrentZoomPan( true );
                }

                _shout('resize');
            },

            // Zoom current item to
            zoomTo: function(destZoomLevel, centerPoint, speed, easingFn, updateFn) {
                /*
			if(destZoomLevel === 'fit') {
				destZoomLevel = self.currItem.fitRatio;
			} else if(destZoomLevel === 'fill') {
				destZoomLevel = self.currItem.fillRatio;
			}
		*/

                if(centerPoint) {
                    _startZoomLevel = _currZoomLevel;
                    _midZoomPoint.x = Math.abs(centerPoint.x) - _panOffset.x ;
                    _midZoomPoint.y = Math.abs(centerPoint.y) - _panOffset.y ;
                    _equalizePoints(_startPanOffset, _panOffset);
                }

                var destPanBounds = _calculatePanBounds(destZoomLevel, false),
                    destPanOffset = {};

                _modifyDestPanOffset('x', destPanBounds, destPanOffset, destZoomLevel);
                _modifyDestPanOffset('y', destPanBounds, destPanOffset, destZoomLevel);

                var initialZoomLevel = _currZoomLevel;
                var initialPanOffset = {
                    x: _panOffset.x,
                    y: _panOffset.y
                };

                _roundPoint(destPanOffset);

                var onUpdate = function(now) {
                    if(now === 1) {
                        _currZoomLevel = destZoomLevel;
                        _panOffset.x = destPanOffset.x;
                        _panOffset.y = destPanOffset.y;
                    } else {
                        _currZoomLevel = (destZoomLevel - initialZoomLevel) * now + initialZoomLevel;
                        _panOffset.x = (destPanOffset.x - initialPanOffset.x) * now + initialPanOffset.x;
                        _panOffset.y = (destPanOffset.y - initialPanOffset.y) * now + initialPanOffset.y;
                    }

                    if(updateFn) {
                        updateFn(now);
                    }

                    _applyCurrentZoomPan( now === 1 );
                };

                if(speed) {
                    _animateProp('customZoomTo', 0, 1, speed, easingFn || framework.easing.sine.inOut, onUpdate);
                } else {
                    onUpdate(1);
                }
            }


        };


        /*>>core*/

        /*>>gestures*/
        /**
         * Mouse/touch/pointer event handlers.
         *
         * separated from @core.js for readability
         */

        var MIN_SWIPE_DISTANCE = 30,
            DIRECTION_CHECK_OFFSET = 10; // amount of pixels to drag to determine direction of swipe

        var _gestureStartTime,
            _gestureCheckSpeedTime,

            // pool of objects that are used during dragging of zooming
            p = {}, // first point
            p2 = {}, // second point (for zoom gesture)
            delta = {},
            _currPoint = {},
            _startPoint = {},
            _currPointers = [],
            _startMainScrollPos = {},
            _releaseAnimData,
            _posPoints = [], // array of points during dragging, used to determine type of gesture
            _tempPoint = {},

            _isZoomingIn,
            _verticalDragInitiated,
            _oldAndroidTouchEndTimeout,
            _currZoomedItemIndex = 0,
            _centerPoint = _getEmptyPoint(),
            _lastReleaseTime = 0,
            _isDragging, // at least one pointer is down
            _isMultitouch, // at least two _pointers are down
            _zoomStarted, // zoom level changed during zoom gesture
            _moved,
            _dragAnimFrame,
            _mainScrollShifted,
            _currentPoints, // array of current touch points
            _isZooming,
            _currPointsDistance,
            _startPointsDistance,
            _currPanBounds,
            _mainScrollPos = _getEmptyPoint(),
            _currZoomElementStyle,
            _mainScrollAnimating, // true, if animation after swipe gesture is running
            _midZoomPoint = _getEmptyPoint(),
            _currCenterPoint = _getEmptyPoint(),
            _direction,
            _isFirstMove,
            _opacityChanged,
            _bgOpacity,
            _wasOverInitialZoom,

            _isEqualPoints = function(p1, p2) {
                return p1.x === p2.x && p1.y === p2.y;
            },
            _isNearbyPoints = function(touch0, touch1) {
                return Math.abs(touch0.x - touch1.x) < DOUBLE_TAP_RADIUS && Math.abs(touch0.y - touch1.y) < DOUBLE_TAP_RADIUS;
            },
            _calculatePointsDistance = function(p1, p2) {
                _tempPoint.x = Math.abs( p1.x - p2.x );
                _tempPoint.y = Math.abs( p1.y - p2.y );
                return Math.sqrt(_tempPoint.x * _tempPoint.x + _tempPoint.y * _tempPoint.y);
            },
            _stopDragUpdateLoop = function() {
                if(_dragAnimFrame) {
                    _cancelAF(_dragAnimFrame);
                    _dragAnimFrame = null;
                }
            },
            _dragUpdateLoop = function() {
                if(_isDragging) {
                    _dragAnimFrame = _requestAF(_dragUpdateLoop);
                    _renderMovement();
                }
            },
            _canPan = function() {
                return !(_options.scaleMode === 'fit' && _currZoomLevel ===  self.currItem.initialZoomLevel);
            },

            // find the closest parent DOM element
            _closestElement = function(el, fn) {
                if(!el || el === document) {
                    return false;
                }

                // don't search elements above pswp__scroll-wrap
                if(el.getAttribute('class') && el.getAttribute('class').indexOf('pswp__scroll-wrap') > -1 ) {
                    return false;
                }

                if( fn(el) ) {
                    return el;
                }

                return _closestElement(el.parentNode, fn);
            },

            _preventObj = {},
            _preventDefaultEventBehaviour = function(e, isDown) {
                _preventObj.prevent = !_closestElement(e.target, _options.isClickableElement);

                _shout('preventDragEvent', e, isDown, _preventObj);
                return _preventObj.prevent;

            },
            _convertTouchToPoint = function(touch, p) {
                p.x = touch.pageX;
                p.y = touch.pageY;
                p.id = touch.identifier;
                return p;
            },
            _findCenterOfPoints = function(p1, p2, pCenter) {
                pCenter.x = (p1.x + p2.x) * 0.5;
                pCenter.y = (p1.y + p2.y) * 0.5;
            },
            _pushPosPoint = function(time, x, y) {
                if(time - _gestureCheckSpeedTime > 50) {
                    var o = _posPoints.length > 2 ? _posPoints.shift() : {};
                    o.x = x;
                    o.y = y;
                    _posPoints.push(o);
                    _gestureCheckSpeedTime = time;
                }
            },

            _calculateVerticalDragOpacityRatio = function() {
                var yOffset = _panOffset.y - self.currItem.initialPosition.y; // difference between initial and current position
                return 1 -  Math.abs( yOffset / (_viewportSize.y / 2)  );
            },


            // points pool, reused during touch events
            _ePoint1 = {},
            _ePoint2 = {},
            _tempPointsArr = [],
            _tempCounter,
            _getTouchPoints = function(e) {
                // clean up previous points, without recreating array
                while(_tempPointsArr.length > 0) {
                    _tempPointsArr.pop();
                }

                if(!_pointerEventEnabled) {
                    if(e.type.indexOf('touch') > -1) {

                        if(e.touches && e.touches.length > 0) {
                            _tempPointsArr[0] = _convertTouchToPoint(e.touches[0], _ePoint1);
                            if(e.touches.length > 1) {
                                _tempPointsArr[1] = _convertTouchToPoint(e.touches[1], _ePoint2);
                            }
                        }

                    } else {
                        _ePoint1.x = e.pageX;
                        _ePoint1.y = e.pageY;
                        _ePoint1.id = '';
                        _tempPointsArr[0] = _ePoint1;//_ePoint1;
                    }
                } else {
                    _tempCounter = 0;
                    // we can use forEach, as pointer events are supported only in modern browsers
                    _currPointers.forEach(function(p) {
                        if(_tempCounter === 0) {
                            _tempPointsArr[0] = p;
                        } else if(_tempCounter === 1) {
                            _tempPointsArr[1] = p;
                        }
                        _tempCounter++;

                    });
                }
                return _tempPointsArr;
            },

            _panOrMoveMainScroll = function(axis, delta) {

                var panFriction,
                    overDiff = 0,
                    newOffset = _panOffset[axis] + delta[axis],
                    startOverDiff,
                    dir = delta[axis] > 0,
                    newMainScrollPosition = _mainScrollPos.x + delta.x,
                    mainScrollDiff = _mainScrollPos.x - _startMainScrollPos.x,
                    newPanPos,
                    newMainScrollPos;

                // calculate fdistance over the bounds and friction
                if(newOffset > _currPanBounds.min[axis] || newOffset < _currPanBounds.max[axis]) {
                    panFriction = _options.panEndFriction;
                    // Linear increasing of friction, so at 1/4 of viewport it's at max value.
                    // Looks not as nice as was expected. Left for history.
                    // panFriction = (1 - (_panOffset[axis] + delta[axis] + panBounds.min[axis]) / (_viewportSize[axis] / 4) );
                } else {
                    panFriction = 1;
                }

                newOffset = _panOffset[axis] + delta[axis] * panFriction;

                // move main scroll or start panning
                if(_options.allowPanToNext || _currZoomLevel === self.currItem.initialZoomLevel) {


                    if(!_currZoomElementStyle) {

                        newMainScrollPos = newMainScrollPosition;

                    } else if(_direction === 'h' && axis === 'x' && !_zoomStarted ) {

                        if(dir) {
                            if(newOffset > _currPanBounds.min[axis]) {
                                panFriction = _options.panEndFriction;
                                overDiff = _currPanBounds.min[axis] - newOffset;
                                startOverDiff = _currPanBounds.min[axis] - _startPanOffset[axis];
                            }

                            // drag right
                            if( (startOverDiff <= 0 || mainScrollDiff < 0) && _getNumItems() > 1 ) {
                                newMainScrollPos = newMainScrollPosition;
                                if(mainScrollDiff < 0 && newMainScrollPosition > _startMainScrollPos.x) {
                                    newMainScrollPos = _startMainScrollPos.x;
                                }
                            } else {
                                if(_currPanBounds.min.x !== _currPanBounds.max.x) {
                                    newPanPos = newOffset;
                                }

                            }

                        } else {

                            if(newOffset < _currPanBounds.max[axis] ) {
                                panFriction =_options.panEndFriction;
                                overDiff = newOffset - _currPanBounds.max[axis];
                                startOverDiff = _startPanOffset[axis] - _currPanBounds.max[axis];
                            }

                            if( (startOverDiff <= 0 || mainScrollDiff > 0) && _getNumItems() > 1 ) {
                                newMainScrollPos = newMainScrollPosition;

                                if(mainScrollDiff > 0 && newMainScrollPosition < _startMainScrollPos.x) {
                                    newMainScrollPos = _startMainScrollPos.x;
                                }

                            } else {
                                if(_currPanBounds.min.x !== _currPanBounds.max.x) {
                                    newPanPos = newOffset;
                                }
                            }

                        }


                        //
                    }

                    if(axis === 'x') {

                        if(newMainScrollPos !== undefined) {
                            _moveMainScroll(newMainScrollPos, true);
                            if(newMainScrollPos === _startMainScrollPos.x) {
                                _mainScrollShifted = false;
                            } else {
                                _mainScrollShifted = true;
                            }
                        }

                        if(_currPanBounds.min.x !== _currPanBounds.max.x) {
                            if(newPanPos !== undefined) {
                                _panOffset.x = newPanPos;
                            } else if(!_mainScrollShifted) {
                                _panOffset.x += delta.x * panFriction;
                            }
                        }

                        return newMainScrollPos !== undefined;
                    }

                }

                if(!_mainScrollAnimating) {

                    if(!_mainScrollShifted) {
                        if(_currZoomLevel > self.currItem.fitRatio) {
                            _panOffset[axis] += delta[axis] * panFriction;

                        }
                    }


                }

            },

            // Pointerdown/touchstart/mousedown handler
            _onDragStart = function(e) {

                // Allow dragging only via left mouse button.
                // As this handler is not added in IE8 - we ignore e.which
                //
                // http://www.quirksmode.org/js/events_properties.html
                // https://developer.mozilla.org/en-US/docs/Web/API/event.button
                if(e.type === 'mousedown' && e.button > 0  ) {
                    return;
                }

                if(_initialZoomRunning) {
                    e.preventDefault();
                    return;
                }

                if(_oldAndroidTouchEndTimeout && e.type === 'mousedown') {
                    return;
                }

                if(_preventDefaultEventBehaviour(e, true)) {
                    e.preventDefault();
                }



                _shout('pointerDown');

                if(_pointerEventEnabled) {
                    var pointerIndex = framework.arraySearch(_currPointers, e.pointerId, 'id');
                    if(pointerIndex < 0) {
                        pointerIndex = _currPointers.length;
                    }
                    _currPointers[pointerIndex] = {x:e.pageX, y:e.pageY, id: e.pointerId};
                }



                var startPointsList = _getTouchPoints(e),
                    numPoints = startPointsList.length;

                _currentPoints = null;

                _stopAllAnimations();

                // init drag
                if(!_isDragging || numPoints === 1) {



                    _isDragging = _isFirstMove = true;
                    framework.bind(window, _upMoveEvents, self);

                    _isZoomingIn =
                        _wasOverInitialZoom =
                            _opacityChanged =
                                _verticalDragInitiated =
                                    _mainScrollShifted =
                                        _moved =
                                            _isMultitouch =
                                                _zoomStarted = false;

                    _direction = null;

                    _shout('firstTouchStart', startPointsList);

                    _equalizePoints(_startPanOffset, _panOffset);

                    _currPanDist.x = _currPanDist.y = 0;
                    _equalizePoints(_currPoint, startPointsList[0]);
                    _equalizePoints(_startPoint, _currPoint);

                    //_equalizePoints(_startMainScrollPos, _mainScrollPos);
                    _startMainScrollPos.x = _slideSize.x * _currPositionIndex;

                    _posPoints = [{
                        x: _currPoint.x,
                        y: _currPoint.y
                    }];

                    _gestureCheckSpeedTime = _gestureStartTime = _getCurrentTime();

                    //_mainScrollAnimationEnd(true);
                    _calculatePanBounds( _currZoomLevel, true );

                    // Start rendering
                    _stopDragUpdateLoop();
                    _dragUpdateLoop();

                }

                // init zoom
                if(!_isZooming && numPoints > 1 && !_mainScrollAnimating && !_mainScrollShifted) {
                    _startZoomLevel = _currZoomLevel;
                    _zoomStarted = false; // true if zoom changed at least once

                    _isZooming = _isMultitouch = true;
                    _currPanDist.y = _currPanDist.x = 0;

                    _equalizePoints(_startPanOffset, _panOffset);

                    _equalizePoints(p, startPointsList[0]);
                    _equalizePoints(p2, startPointsList[1]);

                    _findCenterOfPoints(p, p2, _currCenterPoint);

                    _midZoomPoint.x = Math.abs(_currCenterPoint.x) - _panOffset.x;
                    _midZoomPoint.y = Math.abs(_currCenterPoint.y) - _panOffset.y;
                    _currPointsDistance = _startPointsDistance = _calculatePointsDistance(p, p2);
                }


            },

            // Pointermove/touchmove/mousemove handler
            _onDragMove = function(e) {

                e.preventDefault();

                if(_pointerEventEnabled) {
                    var pointerIndex = framework.arraySearch(_currPointers, e.pointerId, 'id');
                    if(pointerIndex > -1) {
                        var p = _currPointers[pointerIndex];
                        p.x = e.pageX;
                        p.y = e.pageY;
                    }
                }

                if(_isDragging) {
                    var touchesList = _getTouchPoints(e);
                    if(!_direction && !_moved && !_isZooming) {

                        if(_mainScrollPos.x !== _slideSize.x * _currPositionIndex) {
                            // if main scroll position is shifted â€“ direction is always horizontal
                            _direction = 'h';
                        } else {
                            var diff = Math.abs(touchesList[0].x - _currPoint.x) - Math.abs(touchesList[0].y - _currPoint.y);
                            // check the direction of movement
                            if(Math.abs(diff) >= DIRECTION_CHECK_OFFSET) {
                                _direction = diff > 0 ? 'h' : 'v';
                                _currentPoints = touchesList;
                            }
                        }

                    } else {
                        _currentPoints = touchesList;
                    }
                }
            },
            //
            _renderMovement =  function() {

                if(!_currentPoints) {
                    return;
                }

                var numPoints = _currentPoints.length;

                if(numPoints === 0) {
                    return;
                }

                _equalizePoints(p, _currentPoints[0]);

                delta.x = p.x - _currPoint.x;
                delta.y = p.y - _currPoint.y;

                if(_isZooming && numPoints > 1) {
                    // Handle behaviour for more than 1 point

                    _currPoint.x = p.x;
                    _currPoint.y = p.y;

                    // check if one of two points changed
                    if( !delta.x && !delta.y && _isEqualPoints(_currentPoints[1], p2) ) {
                        return;
                    }

                    _equalizePoints(p2, _currentPoints[1]);


                    if(!_zoomStarted) {
                        _zoomStarted = true;
                        _shout('zoomGestureStarted');
                    }

                    // Distance between two points
                    var pointsDistance = _calculatePointsDistance(p,p2);

                    var zoomLevel = _calculateZoomLevel(pointsDistance);

                    // slightly over the of initial zoom level
                    if(zoomLevel > self.currItem.initialZoomLevel + self.currItem.initialZoomLevel / 15) {
                        _wasOverInitialZoom = true;
                    }

                    // Apply the friction if zoom level is out of the bounds
                    var zoomFriction = 1,
                        minZoomLevel = _getMinZoomLevel(),
                        maxZoomLevel = _getMaxZoomLevel();

                    if ( zoomLevel < minZoomLevel ) {

                        if(_options.pinchToClose && !_wasOverInitialZoom && _startZoomLevel <= self.currItem.initialZoomLevel) {
                            // fade out background if zooming out
                            var minusDiff = minZoomLevel - zoomLevel;
                            var percent = 1 - minusDiff / (minZoomLevel / 1.2);

                            _applyBgOpacity(percent);
                            _shout('onPinchClose', percent);
                            _opacityChanged = true;
                        } else {
                            zoomFriction = (minZoomLevel - zoomLevel) / minZoomLevel;
                            if(zoomFriction > 1) {
                                zoomFriction = 1;
                            }
                            zoomLevel = minZoomLevel - zoomFriction * (minZoomLevel / 3);
                        }

                    } else if ( zoomLevel > maxZoomLevel ) {
                        // 1.5 - extra zoom level above the max. E.g. if max is x6, real max 6 + 1.5 = 7.5
                        zoomFriction = (zoomLevel - maxZoomLevel) / ( minZoomLevel * 6 );
                        if(zoomFriction > 1) {
                            zoomFriction = 1;
                        }
                        zoomLevel = maxZoomLevel + zoomFriction * minZoomLevel;
                    }

                    if(zoomFriction < 0) {
                        zoomFriction = 0;
                    }

                    // distance between touch points after friction is applied
                    _currPointsDistance = pointsDistance;

                    // _centerPoint - The point in the middle of two pointers
                    _findCenterOfPoints(p, p2, _centerPoint);

                    // paning with two pointers pressed
                    _currPanDist.x += _centerPoint.x - _currCenterPoint.x;
                    _currPanDist.y += _centerPoint.y - _currCenterPoint.y;
                    _equalizePoints(_currCenterPoint, _centerPoint);

                    _panOffset.x = _calculatePanOffset('x', zoomLevel);
                    _panOffset.y = _calculatePanOffset('y', zoomLevel);

                    _isZoomingIn = zoomLevel > _currZoomLevel;
                    _currZoomLevel = zoomLevel;
                    _applyCurrentZoomPan();

                } else {

                    // handle behaviour for one point (dragging or panning)

                    if(!_direction) {
                        return;
                    }

                    if(_isFirstMove) {
                        _isFirstMove = false;

                        // subtract drag distance that was used during the detection direction

                        if( Math.abs(delta.x) >= DIRECTION_CHECK_OFFSET) {
                            delta.x -= _currentPoints[0].x - _startPoint.x;
                        }

                        if( Math.abs(delta.y) >= DIRECTION_CHECK_OFFSET) {
                            delta.y -= _currentPoints[0].y - _startPoint.y;
                        }
                    }

                    _currPoint.x = p.x;
                    _currPoint.y = p.y;

                    // do nothing if pointers position hasn't changed
                    if(delta.x === 0 && delta.y === 0) {
                        return;
                    }

                    if(_direction === 'v' && _options.closeOnVerticalDrag) {
                        if(!_canPan()) {
                            _currPanDist.y += delta.y;
                            _panOffset.y += delta.y;

                            var opacityRatio = _calculateVerticalDragOpacityRatio();

                            _verticalDragInitiated = true;
                            _shout('onVerticalDrag', opacityRatio);

                            _applyBgOpacity(opacityRatio);
                            _applyCurrentZoomPan();
                            return ;
                        }
                    }

                    _pushPosPoint(_getCurrentTime(), p.x, p.y);

                    _moved = true;
                    _currPanBounds = self.currItem.bounds;

                    var mainScrollChanged = _panOrMoveMainScroll('x', delta);
                    if(!mainScrollChanged) {
                        _panOrMoveMainScroll('y', delta);

                        _roundPoint(_panOffset);
                        _applyCurrentZoomPan();
                    }

                }

            },

            // Pointerup/pointercancel/touchend/touchcancel/mouseup event handler
            _onDragRelease = function(e) {

                if(_features.isOldAndroid ) {

                    if(_oldAndroidTouchEndTimeout && e.type === 'mouseup') {
                        return;
                    }

                    // on Android (v4.1, 4.2, 4.3 & possibly older)
                    // ghost mousedown/up event isn't preventable via e.preventDefault,
                    // which causes fake mousedown event
                    // so we block mousedown/up for 600ms
                    if( e.type.indexOf('touch') > -1 ) {
                        clearTimeout(_oldAndroidTouchEndTimeout);
                        _oldAndroidTouchEndTimeout = setTimeout(function() {
                            _oldAndroidTouchEndTimeout = 0;
                        }, 600);
                    }

                }

                _shout('pointerUp');

                if(_preventDefaultEventBehaviour(e, false)) {
                    e.preventDefault();
                }

                var releasePoint;

                if(_pointerEventEnabled) {
                    var pointerIndex = framework.arraySearch(_currPointers, e.pointerId, 'id');

                    if(pointerIndex > -1) {
                        releasePoint = _currPointers.splice(pointerIndex, 1)[0];

                        if(navigator.pointerEnabled) {
                            releasePoint.type = e.pointerType || 'mouse';
                        } else {
                            var MSPOINTER_TYPES = {
                                4: 'mouse', // event.MSPOINTER_TYPE_MOUSE
                                2: 'touch', // event.MSPOINTER_TYPE_TOUCH
                                3: 'pen' // event.MSPOINTER_TYPE_PEN
                            };
                            releasePoint.type = MSPOINTER_TYPES[e.pointerType];

                            if(!releasePoint.type) {
                                releasePoint.type = e.pointerType || 'mouse';
                            }
                        }

                    }
                }

                var touchList = _getTouchPoints(e),
                    gestureType,
                    numPoints = touchList.length;

                if(e.type === 'mouseup') {
                    numPoints = 0;
                }

                // Do nothing if there were 3 touch points or more
                if(numPoints === 2) {
                    _currentPoints = null;
                    return true;
                }

                // if second pointer released
                if(numPoints === 1) {
                    _equalizePoints(_startPoint, touchList[0]);
                }


                // pointer hasn't moved, send "tap release" point
                if(numPoints === 0 && !_direction && !_mainScrollAnimating) {
                    if(!releasePoint) {
                        if(e.type === 'mouseup') {
                            releasePoint = {x: e.pageX, y: e.pageY, type:'mouse'};
                        } else if(e.changedTouches && e.changedTouches[0]) {
                            releasePoint = {x: e.changedTouches[0].pageX, y: e.changedTouches[0].pageY, type:'touch'};
                        }
                    }

                    _shout('touchRelease', e, releasePoint);
                }

                // Difference in time between releasing of two last touch points (zoom gesture)
                var releaseTimeDiff = -1;

                // Gesture completed, no pointers left
                if(numPoints === 0) {
                    _isDragging = false;
                    framework.unbind(window, _upMoveEvents, self);

                    _stopDragUpdateLoop();

                    if(_isZooming) {
                        // Two points released at the same time
                        releaseTimeDiff = 0;
                    } else if(_lastReleaseTime !== -1) {
                        releaseTimeDiff = _getCurrentTime() - _lastReleaseTime;
                    }
                }
                _lastReleaseTime = numPoints === 1 ? _getCurrentTime() : -1;

                if(releaseTimeDiff !== -1 && releaseTimeDiff < 150) {
                    gestureType = 'zoom';
                } else {
                    gestureType = 'swipe';
                }

                if(_isZooming && numPoints < 2) {
                    _isZooming = false;

                    // Only second point released
                    if(numPoints === 1) {
                        gestureType = 'zoomPointerUp';
                    }
                    _shout('zoomGestureEnded');
                }

                _currentPoints = null;
                if(!_moved && !_zoomStarted && !_mainScrollAnimating && !_verticalDragInitiated) {
                    // nothing to animate
                    return;
                }

                _stopAllAnimations();


                if(!_releaseAnimData) {
                    _releaseAnimData = _initDragReleaseAnimationData();
                }

                _releaseAnimData.calculateSwipeSpeed('x');


                if(_verticalDragInitiated) {

                    var opacityRatio = _calculateVerticalDragOpacityRatio();

                    if(opacityRatio < _options.verticalDragRange) {
                        self.close();
                    } else {
                        var initalPanY = _panOffset.y,
                            initialBgOpacity = _bgOpacity;

                        _animateProp('verticalDrag', 0, 1, 300, framework.easing.cubic.out, function(now) {

                            _panOffset.y = (self.currItem.initialPosition.y - initalPanY) * now + initalPanY;

                            _applyBgOpacity(  (1 - initialBgOpacity) * now + initialBgOpacity );
                            _applyCurrentZoomPan();
                        });

                        _shout('onVerticalDrag', 1);
                    }

                    return;
                }


                // main scroll
                if(  (_mainScrollShifted || _mainScrollAnimating) && numPoints === 0) {
                    var itemChanged = _finishSwipeMainScrollGesture(gestureType, _releaseAnimData);
                    if(itemChanged) {
                        return;
                    }
                    gestureType = 'zoomPointerUp';
                }

                // prevent zoom/pan animation when main scroll animation runs
                if(_mainScrollAnimating) {
                    return;
                }

                // Complete simple zoom gesture (reset zoom level if it's out of the bounds)
                if(gestureType !== 'swipe') {
                    _completeZoomGesture();
                    return;
                }

                // Complete pan gesture if main scroll is not shifted, and it's possible to pan current image
                if(!_mainScrollShifted && _currZoomLevel > self.currItem.fitRatio) {
                    _completePanGesture(_releaseAnimData);
                }
            },


            // Returns object with data about gesture
            // It's created only once and then reused
            _initDragReleaseAnimationData  = function() {
                // temp local vars
                var lastFlickDuration,
                    tempReleasePos;

                // s = this
                var s = {
                    lastFlickOffset: {},
                    lastFlickDist: {},
                    lastFlickSpeed: {},
                    slowDownRatio:  {},
                    slowDownRatioReverse:  {},
                    speedDecelerationRatio:  {},
                    speedDecelerationRatioAbs:  {},
                    distanceOffset:  {},
                    backAnimDestination: {},
                    backAnimStarted: {},
                    calculateSwipeSpeed: function(axis) {


                        if( _posPoints.length > 1) {
                            lastFlickDuration = _getCurrentTime() - _gestureCheckSpeedTime + 50;
                            tempReleasePos = _posPoints[_posPoints.length-2][axis];
                        } else {
                            lastFlickDuration = _getCurrentTime() - _gestureStartTime; // total gesture duration
                            tempReleasePos = _startPoint[axis];
                        }
                        s.lastFlickOffset[axis] = _currPoint[axis] - tempReleasePos;
                        s.lastFlickDist[axis] = Math.abs(s.lastFlickOffset[axis]);
                        if(s.lastFlickDist[axis] > 20) {
                            s.lastFlickSpeed[axis] = s.lastFlickOffset[axis] / lastFlickDuration;
                        } else {
                            s.lastFlickSpeed[axis] = 0;
                        }
                        if( Math.abs(s.lastFlickSpeed[axis]) < 0.1 ) {
                            s.lastFlickSpeed[axis] = 0;
                        }

                        s.slowDownRatio[axis] = 0.95;
                        s.slowDownRatioReverse[axis] = 1 - s.slowDownRatio[axis];
                        s.speedDecelerationRatio[axis] = 1;
                    },

                    calculateOverBoundsAnimOffset: function(axis, speed) {
                        if(!s.backAnimStarted[axis]) {

                            if(_panOffset[axis] > _currPanBounds.min[axis]) {
                                s.backAnimDestination[axis] = _currPanBounds.min[axis];

                            } else if(_panOffset[axis] < _currPanBounds.max[axis]) {
                                s.backAnimDestination[axis] = _currPanBounds.max[axis];
                            }

                            if(s.backAnimDestination[axis] !== undefined) {
                                s.slowDownRatio[axis] = 0.7;
                                s.slowDownRatioReverse[axis] = 1 - s.slowDownRatio[axis];
                                if(s.speedDecelerationRatioAbs[axis] < 0.05) {

                                    s.lastFlickSpeed[axis] = 0;
                                    s.backAnimStarted[axis] = true;

                                    _animateProp('bounceZoomPan'+axis,_panOffset[axis],
                                        s.backAnimDestination[axis],
                                        speed || 300,
                                        framework.easing.sine.out,
                                        function(pos) {
                                            _panOffset[axis] = pos;
                                            _applyCurrentZoomPan();
                                        }
                                    );

                                }
                            }
                        }
                    },

                    // Reduces the speed by slowDownRatio (per 10ms)
                    calculateAnimOffset: function(axis) {
                        if(!s.backAnimStarted[axis]) {
                            s.speedDecelerationRatio[axis] = s.speedDecelerationRatio[axis] * (s.slowDownRatio[axis] +
                                s.slowDownRatioReverse[axis] -
                                s.slowDownRatioReverse[axis] * s.timeDiff / 10);

                            s.speedDecelerationRatioAbs[axis] = Math.abs(s.lastFlickSpeed[axis] * s.speedDecelerationRatio[axis]);
                            s.distanceOffset[axis] = s.lastFlickSpeed[axis] * s.speedDecelerationRatio[axis] * s.timeDiff;
                            _panOffset[axis] += s.distanceOffset[axis];

                        }
                    },

                    panAnimLoop: function() {
                        if ( _animations.zoomPan ) {
                            _animations.zoomPan.raf = _requestAF(s.panAnimLoop);

                            s.now = _getCurrentTime();
                            s.timeDiff = s.now - s.lastNow;
                            s.lastNow = s.now;

                            s.calculateAnimOffset('x');
                            s.calculateAnimOffset('y');

                            _applyCurrentZoomPan();

                            s.calculateOverBoundsAnimOffset('x');
                            s.calculateOverBoundsAnimOffset('y');


                            if (s.speedDecelerationRatioAbs.x < 0.05 && s.speedDecelerationRatioAbs.y < 0.05) {

                                // round pan position
                                _panOffset.x = Math.round(_panOffset.x);
                                _panOffset.y = Math.round(_panOffset.y);
                                _applyCurrentZoomPan();

                                _stopAnimation('zoomPan');
                                return;
                            }
                        }

                    }
                };
                return s;
            },

            _completePanGesture = function(animData) {
                // calculate swipe speed for Y axis (paanning)
                animData.calculateSwipeSpeed('y');

                _currPanBounds = self.currItem.bounds;

                animData.backAnimDestination = {};
                animData.backAnimStarted = {};

                // Avoid acceleration animation if speed is too low
                if(Math.abs(animData.lastFlickSpeed.x) <= 0.05 && Math.abs(animData.lastFlickSpeed.y) <= 0.05 ) {
                    animData.speedDecelerationRatioAbs.x = animData.speedDecelerationRatioAbs.y = 0;

                    // Run pan drag release animation. E.g. if you drag image and release finger without momentum.
                    animData.calculateOverBoundsAnimOffset('x');
                    animData.calculateOverBoundsAnimOffset('y');
                    return true;
                }

                // Animation loop that controls the acceleration after pan gesture ends
                _registerStartAnimation('zoomPan');
                animData.lastNow = _getCurrentTime();
                animData.panAnimLoop();
            },


            _finishSwipeMainScrollGesture = function(gestureType, _releaseAnimData) {
                var itemChanged;
                if(!_mainScrollAnimating) {
                    _currZoomedItemIndex = _currentItemIndex;
                }



                var itemsDiff;

                if(gestureType === 'swipe') {
                    var totalShiftDist = _currPoint.x - _startPoint.x,
                        isFastLastFlick = _releaseAnimData.lastFlickDist.x < 10;

                    // if container is shifted for more than MIN_SWIPE_DISTANCE,
                    // and last flick gesture was in right direction
                    if(totalShiftDist > MIN_SWIPE_DISTANCE &&
                        (isFastLastFlick || _releaseAnimData.lastFlickOffset.x > 20) ) {
                        // go to prev item
                        itemsDiff = -1;
                    } else if(totalShiftDist < -MIN_SWIPE_DISTANCE &&
                        (isFastLastFlick || _releaseAnimData.lastFlickOffset.x < -20) ) {
                        // go to next item
                        itemsDiff = 1;
                    }
                }

                var nextCircle;

                if(itemsDiff) {

                    _currentItemIndex += itemsDiff;

                    if(_currentItemIndex < 0) {
                        _currentItemIndex = _options.loop ? _getNumItems()-1 : 0;
                        nextCircle = true;
                    } else if(_currentItemIndex >= _getNumItems()) {
                        _currentItemIndex = _options.loop ? 0 : _getNumItems()-1;
                        nextCircle = true;
                    }

                    if(!nextCircle || _options.loop) {
                        _indexDiff += itemsDiff;
                        _currPositionIndex -= itemsDiff;
                        itemChanged = true;
                    }



                }

                var animateToX = _slideSize.x * _currPositionIndex;
                var animateToDist = Math.abs( animateToX - _mainScrollPos.x );
                var finishAnimDuration;


                if(!itemChanged && animateToX > _mainScrollPos.x !== _releaseAnimData.lastFlickSpeed.x > 0) {
                    // "return to current" duration, e.g. when dragging from slide 0 to -1
                    finishAnimDuration = 333;
                } else {
                    finishAnimDuration = Math.abs(_releaseAnimData.lastFlickSpeed.x) > 0 ?
                        animateToDist / Math.abs(_releaseAnimData.lastFlickSpeed.x) :
                        333;

                    finishAnimDuration = Math.min(finishAnimDuration, 400);
                    finishAnimDuration = Math.max(finishAnimDuration, 250);
                }

                if(_currZoomedItemIndex === _currentItemIndex) {
                    itemChanged = false;
                }

                _mainScrollAnimating = true;

                _shout('mainScrollAnimStart');

                _animateProp('mainScroll', _mainScrollPos.x, animateToX, finishAnimDuration, framework.easing.cubic.out,
                    _moveMainScroll,
                    function() {
                        _stopAllAnimations();
                        _mainScrollAnimating = false;
                        _currZoomedItemIndex = -1;

                        if(itemChanged || _currZoomedItemIndex !== _currentItemIndex) {
                            self.updateCurrItem();
                        }

                        _shout('mainScrollAnimComplete');
                    }
                );

                if(itemChanged) {
                    self.updateCurrItem(true);
                }

                return itemChanged;
            },

            _calculateZoomLevel = function(touchesDistance) {
                return  1 / _startPointsDistance * touchesDistance * _startZoomLevel;
            },

            // Resets zoom if it's out of bounds
            _completeZoomGesture = function() {
                var destZoomLevel = _currZoomLevel,
                    minZoomLevel = _getMinZoomLevel(),
                    maxZoomLevel = _getMaxZoomLevel();

                if ( _currZoomLevel < minZoomLevel ) {
                    destZoomLevel = minZoomLevel;
                } else if ( _currZoomLevel > maxZoomLevel ) {
                    destZoomLevel = maxZoomLevel;
                }

                var destOpacity = 1,
                    onUpdate,
                    initialOpacity = _bgOpacity;

                if(_opacityChanged && !_isZoomingIn && !_wasOverInitialZoom && _currZoomLevel < minZoomLevel) {
                    //_closedByScroll = true;
                    self.close();
                    return true;
                }

                if(_opacityChanged) {
                    onUpdate = function(now) {
                        _applyBgOpacity(  (destOpacity - initialOpacity) * now + initialOpacity );
                    };
                }

                self.zoomTo(destZoomLevel, 0, 200,  framework.easing.cubic.out, onUpdate);
                return true;
            };


        _registerModule('Gestures', {
            publicMethods: {

                initGestures: function() {

                    // helper function that builds touch/pointer/mouse events
                    var addEventNames = function(pref, down, move, up, cancel) {
                        _dragStartEvent = pref + down;
                        _dragMoveEvent = pref + move;
                        _dragEndEvent = pref + up;
                        if(cancel) {
                            _dragCancelEvent = pref + cancel;
                        } else {
                            _dragCancelEvent = '';
                        }
                    };

                    _pointerEventEnabled = _features.pointerEvent;
                    if(_pointerEventEnabled && _features.touch) {
                        // we don't need touch events, if browser supports pointer events
                        _features.touch = false;
                    }

                    if(_pointerEventEnabled) {
                        if(navigator.pointerEnabled) {
                            addEventNames('pointer', 'down', 'move', 'up', 'cancel');
                        } else {
                            // IE10 pointer events are case-sensitive
                            addEventNames('MSPointer', 'Down', 'Move', 'Up', 'Cancel');
                        }
                    } else if(_features.touch) {
                        addEventNames('touch', 'start', 'move', 'end', 'cancel');
                        _likelyTouchDevice = true;
                    } else {
                        addEventNames('mouse', 'down', 'move', 'up');
                    }

                    _upMoveEvents = _dragMoveEvent + ' ' + _dragEndEvent  + ' ' +  _dragCancelEvent;
                    _downEvents = _dragStartEvent;

                    if(_pointerEventEnabled && !_likelyTouchDevice) {
                        _likelyTouchDevice = (navigator.maxTouchPoints > 1) || (navigator.msMaxTouchPoints > 1);
                    }
                    // make variable public
                    self.likelyTouchDevice = _likelyTouchDevice;

                    _globalEventHandlers[_dragStartEvent] = _onDragStart;
                    _globalEventHandlers[_dragMoveEvent] = _onDragMove;
                    _globalEventHandlers[_dragEndEvent] = _onDragRelease; // the Kraken

                    if(_dragCancelEvent) {
                        _globalEventHandlers[_dragCancelEvent] = _globalEventHandlers[_dragEndEvent];
                    }

                    // Bind mouse events on device with detected hardware touch support, in case it supports multiple types of input.
                    if(_features.touch) {
                        _downEvents += ' mousedown';
                        _upMoveEvents += ' mousemove mouseup';
                        _globalEventHandlers.mousedown = _globalEventHandlers[_dragStartEvent];
                        _globalEventHandlers.mousemove = _globalEventHandlers[_dragMoveEvent];
                        _globalEventHandlers.mouseup = _globalEventHandlers[_dragEndEvent];
                    }

                    if(!_likelyTouchDevice) {
                        // don't allow pan to next slide from zoomed state on Desktop
                        _options.allowPanToNext = false;
                    }
                }

            }
        });


        /*>>gestures*/

        /*>>show-hide-transition*/
        /**
         * show-hide-transition.js:
         *
         * Manages initial opening or closing transition.
         *
         * If you're not planning to use transition for gallery at all,
         * you may set options hideAnimationDuration and showAnimationDuration to 0,
         * and just delete startAnimation function.
         *
         */


        var _showOrHideTimeout,
            _showOrHide = function(item, img, out, completeFn) {

                if(_showOrHideTimeout) {
                    clearTimeout(_showOrHideTimeout);
                }

                _initialZoomRunning = true;
                _initialContentSet = true;

                // dimensions of small thumbnail {x:,y:,w:}.
                // Height is optional, as calculated based on large image.
                var thumbBounds;
                if(item.initialLayout) {
                    thumbBounds = item.initialLayout;
                    item.initialLayout = null;
                } else {
                    thumbBounds = _options.getThumbBoundsFn && _options.getThumbBoundsFn(_currentItemIndex);
                }

                var duration = out ? _options.hideAnimationDuration : _options.showAnimationDuration;

                var onComplete = function() {
                    _stopAnimation('initialZoom');
                    if(!out) {
                        _applyBgOpacity(1);
                        if(img) {
                            img.style.display = 'block';
                        }
                        framework.addClass(template, 'pswp--animated-in');
                        _shout('initialZoom' + (out ? 'OutEnd' : 'InEnd'));
                    } else {
                        self.template.removeAttribute('style');
                        self.bg.removeAttribute('style');
                    }

                    if(completeFn) {
                        completeFn();
                    }
                    _initialZoomRunning = false;
                };

                // if bounds aren't provided, just open gallery without animation
                if(!duration || !thumbBounds || thumbBounds.x === undefined) {

                    _shout('initialZoom' + (out ? 'Out' : 'In') );

                    _currZoomLevel = item.initialZoomLevel;
                    _equalizePoints(_panOffset,  item.initialPosition );
                    _applyCurrentZoomPan();

                    template.style.opacity = out ? 0 : 1;
                    _applyBgOpacity(1);

                    if(duration) {
                        setTimeout(function() {
                            onComplete();
                        }, duration);
                    } else {
                        onComplete();
                    }

                    return;
                }

                var startAnimation = function() {
                    var closeWithRaf = _closedByScroll,
                        fadeEverything = !self.currItem.src || self.currItem.loadError || _options.showHideOpacity;

                    // apply hw-acceleration to image
                    if(item.miniImg) {
                        item.miniImg.style.webkitBackfaceVisibility = 'hidden';
                    }

                    if(!out) {
                        _currZoomLevel = thumbBounds.w / item.w;
                        _panOffset.x = thumbBounds.x;
                        _panOffset.y = thumbBounds.y - _initalWindowScrollY;

                        self[fadeEverything ? 'template' : 'bg'].style.opacity = 0.001;
                        _applyCurrentZoomPan();
                    }

                    _registerStartAnimation('initialZoom');

                    if(out && !closeWithRaf) {
                        framework.removeClass(template, 'pswp--animated-in');
                    }

                    if(fadeEverything) {
                        if(out) {
                            framework[ (closeWithRaf ? 'remove' : 'add') + 'Class' ](template, 'pswp--animate_opacity');
                        } else {
                            setTimeout(function() {
                                framework.addClass(template, 'pswp--animate_opacity');
                            }, 30);
                        }
                    }

                    _showOrHideTimeout = setTimeout(function() {

                        _shout('initialZoom' + (out ? 'Out' : 'In') );


                        if(!out) {

                            // "in" animation always uses CSS transitions (instead of rAF).
                            // CSS transition work faster here,
                            // as developer may also want to animate other things,
                            // like ui on top of sliding area, which can be animated just via CSS

                            _currZoomLevel = item.initialZoomLevel;
                            _equalizePoints(_panOffset,  item.initialPosition );
                            _applyCurrentZoomPan();
                            _applyBgOpacity(1);

                            if(fadeEverything) {
                                template.style.opacity = 1;
                            } else {
                                _applyBgOpacity(1);
                            }

                            _showOrHideTimeout = setTimeout(onComplete, duration + 20);
                        } else {

                            // "out" animation uses rAF only when PhotoSwipe is closed by browser scroll, to recalculate position
                            var destZoomLevel = thumbBounds.w / item.w,
                                initialPanOffset = {
                                    x: _panOffset.x,
                                    y: _panOffset.y
                                },
                                initialZoomLevel = _currZoomLevel,
                                initalBgOpacity = _bgOpacity,
                                onUpdate = function(now) {

                                    if(now === 1) {
                                        _currZoomLevel = destZoomLevel;
                                        _panOffset.x = thumbBounds.x;
                                        _panOffset.y = thumbBounds.y  - _currentWindowScrollY;
                                    } else {
                                        _currZoomLevel = (destZoomLevel - initialZoomLevel) * now + initialZoomLevel;
                                        _panOffset.x = (thumbBounds.x - initialPanOffset.x) * now + initialPanOffset.x;
                                        _panOffset.y = (thumbBounds.y - _currentWindowScrollY - initialPanOffset.y) * now + initialPanOffset.y;
                                    }

                                    _applyCurrentZoomPan();
                                    if(fadeEverything) {
                                        template.style.opacity = 1 - now;
                                    } else {
                                        _applyBgOpacity( initalBgOpacity - now * initalBgOpacity );
                                    }
                                };

                            if(closeWithRaf) {
                                _animateProp('initialZoom', 0, 1, duration, framework.easing.cubic.out, onUpdate, onComplete);
                            } else {
                                onUpdate(1);
                                _showOrHideTimeout = setTimeout(onComplete, duration + 20);
                            }
                        }

                    }, out ? 25 : 90); // Main purpose of this delay is to give browser time to paint and
                    // create composite layers of PhotoSwipe UI parts (background, controls, caption, arrows).
                    // Which avoids lag at the beginning of scale transition.
                };
                startAnimation();


            };

        /*>>show-hide-transition*/

        /*>>items-controller*/
        /**
         *
         * Controller manages gallery items, their dimensions, and their content.
         *
         */

        var _items,
            _tempPanAreaSize = {},
            _imagesToAppendPool = [],
            _initialContentSet,
            _initialZoomRunning,
            _controllerDefaultOptions = {
                index: 0,
                errorMsg: '<div class="pswp__error-msg"><a href="%url%" target="_blank">The image</a> could not be loaded.</div>',
                forceProgressiveLoading: false, // TODO
                preload: [1,1],
                getNumItemsFn: function() {
                    return _items.length;
                }
            };


        var _getItemAt,
            _getNumItems,
            _initialIsLoop,
            _getZeroBounds = function() {
                return {
                    center:{x:0,y:0},
                    max:{x:0,y:0},
                    min:{x:0,y:0}
                };
            },
            _calculateSingleItemPanBounds = function(item, realPanElementW, realPanElementH ) {
                var bounds = item.bounds;

                // position of element when it's centered
                bounds.center.x = Math.round((_tempPanAreaSize.x - realPanElementW) / 2);
                bounds.center.y = Math.round((_tempPanAreaSize.y - realPanElementH) / 2) + item.vGap.top;

                // maximum pan position
                bounds.max.x = (realPanElementW > _tempPanAreaSize.x) ?
                    Math.round(_tempPanAreaSize.x - realPanElementW) :
                    bounds.center.x;

                bounds.max.y = (realPanElementH > _tempPanAreaSize.y) ?
                    Math.round(_tempPanAreaSize.y - realPanElementH) + item.vGap.top :
                    bounds.center.y;

                // minimum pan position
                bounds.min.x = (realPanElementW > _tempPanAreaSize.x) ? 0 : bounds.center.x;
                bounds.min.y = (realPanElementH > _tempPanAreaSize.y) ? item.vGap.top : bounds.center.y;
            },
            _calculateItemSize = function(item, viewportSize, zoomLevel) {

                if (item.src && !item.loadError) {
                    var isInitial = !zoomLevel;

                    if(isInitial) {
                        if(!item.vGap) {
                            item.vGap = {top:0,bottom:0};
                        }
                        // allows overriding vertical margin for individual items
                        _shout('parseVerticalMargin', item);
                    }


                    _tempPanAreaSize.x = viewportSize.x;
                    _tempPanAreaSize.y = viewportSize.y - item.vGap.top - item.vGap.bottom;

                    if (isInitial) {
                        var hRatio = _tempPanAreaSize.x / item.w;
                        var vRatio = _tempPanAreaSize.y / item.h;

                        item.fitRatio = hRatio < vRatio ? hRatio : vRatio;
                        //item.fillRatio = hRatio > vRatio ? hRatio : vRatio;

                        var scaleMode = _options.scaleMode;

                        if (scaleMode === 'orig') {
                            zoomLevel = 1;
                        } else if (scaleMode === 'fit') {
                            zoomLevel = item.fitRatio;
                        }

                        if (zoomLevel > 1) {
                            zoomLevel = 1;
                        }

                        item.initialZoomLevel = zoomLevel;

                        if(!item.bounds) {
                            // reuse bounds object
                            item.bounds = _getZeroBounds();
                        }
                    }

                    if(!zoomLevel) {
                        return;
                    }

                    _calculateSingleItemPanBounds(item, item.w * zoomLevel, item.h * zoomLevel);

                    if (isInitial && zoomLevel === item.initialZoomLevel) {
                        item.initialPosition = item.bounds.center;
                    }

                    return item.bounds;
                } else {
                    item.w = item.h = 0;
                    item.initialZoomLevel = item.fitRatio = 1;
                    item.bounds = _getZeroBounds();
                    item.initialPosition = item.bounds.center;

                    // if it's not image, we return zero bounds (content is not zoomable)
                    return item.bounds;
                }

            },




            _appendImage = function(index, item, baseDiv, img, preventAnimation, keepPlaceholder) {


                if(item.loadError) {
                    return;
                }

                if(img) {

                    item.imageAppended = true;
                    _setImageSize(item, img, (item === self.currItem && _renderMaxResolution) );

                    baseDiv.appendChild(img);

                    if(keepPlaceholder) {
                        setTimeout(function() {
                            if(item && item.loaded && item.placeholder) {
                                item.placeholder.style.display = 'none';
                                item.placeholder = null;
                            }
                        }, 500);
                    }
                }
            },



            _preloadImage = function(item) {
                item.loading = true;
                item.loaded = false;
                var img = item.img = framework.createEl('pswp__img', 'img');
                var onComplete = function() {
                    item.loading = false;
                    item.loaded = true;

                    if(item.loadComplete) {
                        item.loadComplete(item);
                    } else {
                        item.img = null; // no need to store image object
                    }
                    img.onload = img.onerror = null;
                    img = null;
                };
                img.onload = onComplete;
                img.onerror = function() {
                    item.loadError = true;
                    onComplete();
                };

                img.src = item.src;// + '?a=' + Math.random();

                return img;
            },
            _checkForError = function(item, cleanUp) {
                if(item.src && item.loadError && item.container) {

                    if(cleanUp) {
                        item.container.innerHTML = '';
                    }

                    item.container.innerHTML = _options.errorMsg.replace('%url%',  item.src );
                    return true;

                }
            },
            _setImageSize = function(item, img, maxRes) {
                if(!item.src) {
                    return;
                }

                if(!img) {
                    img = item.container.lastChild;
                }

                var w = maxRes ? item.w : Math.round(item.w * item.fitRatio),
                    h = maxRes ? item.h : Math.round(item.h * item.fitRatio);

                if(item.placeholder && !item.loaded) {
                    item.placeholder.style.width = w + 'px';
                    item.placeholder.style.height = h + 'px';
                }

                img.style.width = w + 'px';
                img.style.height = h + 'px';
            },
            _appendImagesPool = function() {

                if(_imagesToAppendPool.length) {
                    var poolItem;

                    for(var i = 0; i < _imagesToAppendPool.length; i++) {
                        poolItem = _imagesToAppendPool[i];
                        if( poolItem.holder.index === poolItem.index ) {
                            _appendImage(poolItem.index, poolItem.item, poolItem.baseDiv, poolItem.img, false, poolItem.clearPlaceholder);
                        }
                    }
                    _imagesToAppendPool = [];
                }
            };



        _registerModule('Controller', {

            publicMethods: {

                lazyLoadItem: function(index) {
                    index = _getLoopedId(index);
                    var item = _getItemAt(index);

                    if(!item || ((item.loaded || item.loading) && !_itemsNeedUpdate)) {
                        return;
                    }

                    _shout('gettingData', index, item);

                    if (!item.src) {
                        return;
                    }

                    _preloadImage(item);
                },
                initController: function() {
                    framework.extend(_options, _controllerDefaultOptions, true);
                    self.items = _items = items;
                    _getItemAt = self.getItemAt;
                    _getNumItems = _options.getNumItemsFn; //self.getNumItems;



                    _initialIsLoop = _options.loop;
                    if(_getNumItems() < 3) {
                        _options.loop = false; // disable loop if less then 3 items
                    }

                    _listen('beforeChange', function(diff) {

                        var p = _options.preload,
                            isNext = diff === null ? true : (diff >= 0),
                            preloadBefore = Math.min(p[0], _getNumItems() ),
                            preloadAfter = Math.min(p[1], _getNumItems() ),
                            i;


                        for(i = 1; i <= (isNext ? preloadAfter : preloadBefore); i++) {
                            self.lazyLoadItem(_currentItemIndex+i);
                        }
                        for(i = 1; i <= (isNext ? preloadBefore : preloadAfter); i++) {
                            self.lazyLoadItem(_currentItemIndex-i);
                        }
                    });

                    _listen('initialLayout', function() {
                        self.currItem.initialLayout = _options.getThumbBoundsFn && _options.getThumbBoundsFn(_currentItemIndex);
                    });

                    _listen('mainScrollAnimComplete', _appendImagesPool);
                    _listen('initialZoomInEnd', _appendImagesPool);



                    _listen('destroy', function() {
                        var item;
                        for(var i = 0; i < _items.length; i++) {
                            item = _items[i];
                            // remove reference to DOM elements, for GC
                            if(item.container) {
                                item.container = null;
                            }
                            if(item.placeholder) {
                                item.placeholder = null;
                            }
                            if(item.img) {
                                item.img = null;
                            }
                            if(item.preloader) {
                                item.preloader = null;
                            }
                            if(item.loadError) {
                                item.loaded = item.loadError = false;
                            }
                        }
                        _imagesToAppendPool = null;
                    });
                },


                getItemAt: function(index) {
                    if (index >= 0) {
                        return _items[index] !== undefined ? _items[index] : false;
                    }
                    return false;
                },

                allowProgressiveImg: function() {
                    // 1. Progressive image loading isn't working on webkit/blink
                    //    when hw-acceleration (e.g. translateZ) is applied to IMG element.
                    //    That's why in PhotoSwipe parent element gets zoom transform, not image itself.
                    //
                    // 2. Progressive image loading sometimes blinks in webkit/blink when applying animation to parent element.
                    //    That's why it's disabled on touch devices (mainly because of swipe transition)
                    //
                    // 3. Progressive image loading sometimes doesn't work in IE (up to 11).

                    // Don't allow progressive loading on non-large touch devices
                    return _options.forceProgressiveLoading || !_likelyTouchDevice || _options.mouseUsed || screen.width > 1200;
                    // 1200 - to eliminate touch devices with large screen (like Chromebook Pixel)
                },

                setContent: function(holder, index) {

                    if(_options.loop) {
                        index = _getLoopedId(index);
                    }

                    var prevItem = self.getItemAt(holder.index);
                    if(prevItem) {
                        prevItem.container = null;
                    }

                    var item = self.getItemAt(index),
                        img;

                    if(!item) {
                        holder.el.innerHTML = '';
                        return;
                    }

                    // allow to override data
                    _shout('gettingData', index, item);

                    holder.index = index;
                    holder.item = item;

                    // base container DIV is created only once for each of 3 holders
                    var baseDiv = item.container = framework.createEl('pswp__zoom-wrap');



                    if(!item.src && item.html) {
                        if(item.html.tagName) {
                            baseDiv.appendChild(item.html);
                        } else {
                            baseDiv.innerHTML = item.html;
                        }
                    }

                    _checkForError(item);

                    _calculateItemSize(item, _viewportSize);

                    if(item.src && !item.loadError && !item.loaded) {

                        item.loadComplete = function(item) {

                            // gallery closed before image finished loading
                            if(!_isOpen) {
                                return;
                            }

                            // check if holder hasn't changed while image was loading
                            if(holder && holder.index === index ) {
                                if( _checkForError(item, true) ) {
                                    item.loadComplete = item.img = null;
                                    _calculateItemSize(item, _viewportSize);
                                    _applyZoomPanToItem(item);

                                    if(holder.index === _currentItemIndex) {
                                        // recalculate dimensions
                                        self.updateCurrZoomItem();
                                    }
                                    return;
                                }
                                if( !item.imageAppended ) {
                                    if(_features.transform && (_mainScrollAnimating || _initialZoomRunning) ) {
                                        _imagesToAppendPool.push({
                                            item:item,
                                            baseDiv:baseDiv,
                                            img:item.img,
                                            index:index,
                                            holder:holder,
                                            clearPlaceholder:true
                                        });
                                    } else {
                                        _appendImage(index, item, baseDiv, item.img, _mainScrollAnimating || _initialZoomRunning, true);
                                    }
                                } else {
                                    // remove preloader & mini-img
                                    if(!_initialZoomRunning && item.placeholder) {
                                        item.placeholder.style.display = 'none';
                                        item.placeholder = null;
                                    }
                                }
                            }

                            item.loadComplete = null;
                            item.img = null; // no need to store image element after it's added

                            _shout('imageLoadComplete', index, item);
                        };

                        if(framework.features.transform) {

                            var placeholderClassName = 'pswp__img pswp__img--placeholder';
                            placeholderClassName += (item.msrc ? '' : ' pswp__img--placeholder--blank');

                            var placeholder = framework.createEl(placeholderClassName, item.msrc ? 'img' : '');
                            if(item.msrc) {
                                placeholder.src = item.msrc;
                            }

                            _setImageSize(item, placeholder);

                            baseDiv.appendChild(placeholder);
                            item.placeholder = placeholder;

                        }




                        if(!item.loading) {
                            _preloadImage(item);
                        }


                        if( self.allowProgressiveImg() ) {
                            // just append image
                            if(!_initialContentSet && _features.transform) {
                                _imagesToAppendPool.push({
                                    item:item,
                                    baseDiv:baseDiv,
                                    img:item.img,
                                    index:index,
                                    holder:holder
                                });
                            } else {
                                _appendImage(index, item, baseDiv, item.img, true, true);
                            }
                        }

                    } else if(item.src && !item.loadError) {
                        // image object is created every time, due to bugs of image loading & delay when switching images
                        img = framework.createEl('pswp__img', 'img');
                        img.style.opacity = 1;
                        img.src = item.src;
                        _setImageSize(item, img);
                        _appendImage(index, item, baseDiv, img, true);
                    }


                    if(!_initialContentSet && index === _currentItemIndex) {
                        _currZoomElementStyle = baseDiv.style;
                        _showOrHide(item, (img ||item.img) );
                    } else {
                        _applyZoomPanToItem(item);
                    }

                    holder.el.innerHTML = '';
                    holder.el.appendChild(baseDiv);
                },

                cleanSlide: function( item ) {
                    if(item.img ) {
                        item.img.onload = item.img.onerror = null;
                    }
                    item.loaded = item.loading = item.img = item.imageAppended = false;
                }

            }
        });

        /*>>items-controller*/

        /*>>tap*/
        /**
         * tap.js:
         *
         * Displatches tap and double-tap events.
         *
         */

        var tapTimer,
            tapReleasePoint = {},
            _dispatchTapEvent = function(origEvent, releasePoint, pointerType) {
                var e = document.createEvent( 'CustomEvent' ),
                    eDetail = {
                        origEvent:origEvent,
                        target:origEvent.target,
                        releasePoint: releasePoint,
                        pointerType:pointerType || 'touch'
                    };

                e.initCustomEvent( 'pswpTap', true, true, eDetail );
                origEvent.target.dispatchEvent(e);
            };

        _registerModule('Tap', {
            publicMethods: {
                initTap: function() {
                    _listen('firstTouchStart', self.onTapStart);
                    _listen('touchRelease', self.onTapRelease);
                    _listen('destroy', function() {
                        tapReleasePoint = {};
                        tapTimer = null;
                    });
                },
                onTapStart: function(touchList) {
                    if(touchList.length > 1) {
                        clearTimeout(tapTimer);
                        tapTimer = null;
                    }
                },
                onTapRelease: function(e, releasePoint) {
                    if(!releasePoint) {
                        return;
                    }

                    if(!_moved && !_isMultitouch && !_numAnimations) {
                        var p0 = releasePoint;
                        if(tapTimer) {
                            clearTimeout(tapTimer);
                            tapTimer = null;

                            // Check if taped on the same place
                            if ( _isNearbyPoints(p0, tapReleasePoint) ) {
                                _shout('doubleTap', p0);
                                return;
                            }
                        }

                        if(releasePoint.type === 'mouse') {
                            _dispatchTapEvent(e, releasePoint, 'mouse');
                            return;
                        }

                        var clickedTagName = e.target.tagName.toUpperCase();
                        // avoid double tap delay on buttons and elements that have class pswp__single-tap
                        if(clickedTagName === 'BUTTON' || framework.hasClass(e.target, 'pswp__single-tap') ) {
                            _dispatchTapEvent(e, releasePoint);
                            return;
                        }

                        _equalizePoints(tapReleasePoint, p0);

                        tapTimer = setTimeout(function() {
                            _dispatchTapEvent(e, releasePoint);
                            tapTimer = null;
                        }, 300);
                    }
                }
            }
        });

        /*>>tap*/

        /*>>desktop-zoom*/
        /**
         *
         * desktop-zoom.js:
         *
         * - Binds mousewheel event for paning zoomed image.
         * - Manages "dragging", "zoomed-in", "zoom-out" classes.
         *   (which are used for cursors and zoom icon)
         * - Adds toggleDesktopZoom function.
         *
         */

        var _wheelDelta;

        _registerModule('DesktopZoom', {

            publicMethods: {

                initDesktopZoom: function() {

                    if(_oldIE) {
                        // no zoom for old IE (<=8)
                        return;
                    }

                    if(_likelyTouchDevice) {
                        // if detected hardware touch support, we wait until mouse is used,
                        // and only then apply desktop-zoom features
                        _listen('mouseUsed', function() {
                            self.setupDesktopZoom();
                        });
                    } else {
                        self.setupDesktopZoom(true);
                    }

                },

                setupDesktopZoom: function(onInit) {

                    _wheelDelta = {};

                    var events = 'wheel mousewheel DOMMouseScroll';

                    _listen('bindEvents', function() {
                        framework.bind(template, events,  self.handleMouseWheel);
                    });

                    _listen('unbindEvents', function() {
                        if(_wheelDelta) {
                            framework.unbind(template, events, self.handleMouseWheel);
                        }
                    });

                    self.mouseZoomedIn = false;

                    var hasDraggingClass,
                        updateZoomable = function() {
                            if(self.mouseZoomedIn) {
                                framework.removeClass(template, 'pswp--zoomed-in');
                                self.mouseZoomedIn = false;
                            }
                            if(_currZoomLevel < 1) {
                                framework.addClass(template, 'pswp--zoom-allowed');
                            } else {
                                framework.removeClass(template, 'pswp--zoom-allowed');
                            }
                            removeDraggingClass();
                        },
                        removeDraggingClass = function() {
                            if(hasDraggingClass) {
                                framework.removeClass(template, 'pswp--dragging');
                                hasDraggingClass = false;
                            }
                        };

                    _listen('resize' , updateZoomable);
                    _listen('afterChange' , updateZoomable);
                    _listen('pointerDown', function() {
                        if(self.mouseZoomedIn) {
                            hasDraggingClass = true;
                            framework.addClass(template, 'pswp--dragging');
                        }
                    });
                    _listen('pointerUp', removeDraggingClass);

                    if(!onInit) {
                        updateZoomable();
                    }

                },

                handleMouseWheel: function(e) {

                    if(_currZoomLevel <= self.currItem.fitRatio) {
                        if( _options.modal ) {

                            if (!_options.closeOnScroll || _numAnimations || _isDragging) {
                                e.preventDefault();
                            } else if(_transformKey && Math.abs(e.deltaY) > 2) {
                                // close PhotoSwipe
                                // if browser supports transforms & scroll changed enough
                                _closedByScroll = true;
                                self.close();
                            }

                        }
                        return true;
                    }

                    // allow just one event to fire
                    e.stopPropagation();

                    // https://developer.mozilla.org/en-US/docs/Web/Events/wheel
                    _wheelDelta.x = 0;

                    if('deltaX' in e) {
                        if(e.deltaMode === 1 /* DOM_DELTA_LINE */) {
                            // 18 - average line height
                            _wheelDelta.x = e.deltaX * 18;
                            _wheelDelta.y = e.deltaY * 18;
                        } else {
                            _wheelDelta.x = e.deltaX;
                            _wheelDelta.y = e.deltaY;
                        }
                    } else if('wheelDelta' in e) {
                        if(e.wheelDeltaX) {
                            _wheelDelta.x = -0.16 * e.wheelDeltaX;
                        }
                        if(e.wheelDeltaY) {
                            _wheelDelta.y = -0.16 * e.wheelDeltaY;
                        } else {
                            _wheelDelta.y = -0.16 * e.wheelDelta;
                        }
                    } else if('detail' in e) {
                        _wheelDelta.y = e.detail;
                    } else {
                        return;
                    }

                    _calculatePanBounds(_currZoomLevel, true);

                    var newPanX = _panOffset.x - _wheelDelta.x,
                        newPanY = _panOffset.y - _wheelDelta.y;

                    // only prevent scrolling in nonmodal mode when not at edges
                    if (_options.modal ||
                        (
                            newPanX <= _currPanBounds.min.x && newPanX >= _currPanBounds.max.x &&
                            newPanY <= _currPanBounds.min.y && newPanY >= _currPanBounds.max.y
                        ) ) {
                        e.preventDefault();
                    }

                    // TODO: use rAF instead of mousewheel?
                    self.panTo(newPanX, newPanY);
                },

                toggleDesktopZoom: function(centerPoint) {
                    centerPoint = centerPoint || {x:_viewportSize.x/2 + _offset.x, y:_viewportSize.y/2 + _offset.y };

                    var doubleTapZoomLevel = _options.getDoubleTapZoom(true, self.currItem);
                    var zoomOut = _currZoomLevel === doubleTapZoomLevel;

                    self.mouseZoomedIn = !zoomOut;

                    self.zoomTo(zoomOut ? self.currItem.initialZoomLevel : doubleTapZoomLevel, centerPoint, 333);
                    framework[ (!zoomOut ? 'add' : 'remove') + 'Class'](template, 'pswp--zoomed-in');
                }

            }
        });


        /*>>desktop-zoom*/

        /*>>history*/
        /**
         *
         * history.js:
         *
         * - Back button to close gallery.
         *
         * - Unique URL for each slide: example.com/&pid=1&gid=3
         *   (where PID is picture index, and GID and gallery index)
         *
         * - Switch URL when slides change.
         *
         */


        var _historyDefaultOptions = {
            history: true,
            galleryUID: 1
        };

        var _historyUpdateTimeout,
            _hashChangeTimeout,
            _hashAnimCheckTimeout,
            _hashChangedByScript,
            _hashChangedByHistory,
            _hashReseted,
            _initialHash,
            _historyChanged,
            _closedFromURL,
            _urlChangedOnce,
            _windowLoc,

            _supportsPushState,

            _getHash = function() {
                return _windowLoc.hash.substring(1);
            },
            _cleanHistoryTimeouts = function() {

                if(_historyUpdateTimeout) {
                    clearTimeout(_historyUpdateTimeout);
                }

                if(_hashAnimCheckTimeout) {
                    clearTimeout(_hashAnimCheckTimeout);
                }
            },

            // pid - Picture index
            // gid - Gallery index
            _parseItemIndexFromURL = function() {
                var hash = _getHash(),
                    params = {};

                if(hash.length < 5) { // pid=1
                    return params;
                }

                var i, vars = hash.split('&');
                for (i = 0; i < vars.length; i++) {
                    if(!vars[i]) {
                        continue;
                    }
                    var pair = vars[i].split('=');
                    if(pair.length < 2) {
                        continue;
                    }
                    params[pair[0]] = pair[1];
                }
                if(_options.galleryPIDs) {
                    // detect custom pid in hash and search for it among the items collection
                    var searchfor = params.pid;
                    params.pid = 0; // if custom pid cannot be found, fallback to the first item
                    for(i = 0; i < _items.length; i++) {
                        if(_items[i].pid === searchfor) {
                            params.pid = i;
                            break;
                        }
                    }
                } else {
                    params.pid = parseInt(params.pid,10)-1;
                }
                if( params.pid < 0 ) {
                    params.pid = 0;
                }
                return params;
            },
            _updateHash = function() {

                if(_hashAnimCheckTimeout) {
                    clearTimeout(_hashAnimCheckTimeout);
                }


                if(_numAnimations || _isDragging) {
                    // changing browser URL forces layout/paint in some browsers, which causes noticable lag during animation
                    // that's why we update hash only when no animations running
                    _hashAnimCheckTimeout = setTimeout(_updateHash, 500);
                    return;
                }

                if(_hashChangedByScript) {
                    clearTimeout(_hashChangeTimeout);
                } else {
                    _hashChangedByScript = true;
                }


                var pid = (_currentItemIndex + 1);
                var item = _getItemAt( _currentItemIndex );
                if(item.hasOwnProperty('pid')) {
                    // carry forward any custom pid assigned to the item
                    pid = item.pid;
                }
                var newHash = _initialHash + '&'  +  'gid=' + _options.galleryUID + '&' + 'pid=' + pid;

                if(!_historyChanged) {
                    if(_windowLoc.hash.indexOf(newHash) === -1) {
                        _urlChangedOnce = true;
                    }
                    // first time - add new hisory record, then just replace
                }

                var newURL = _windowLoc.href.split('#')[0] + '#' +  newHash;

                if( _supportsPushState ) {

                    if('#' + newHash !== window.location.hash) {
                        history[_historyChanged ? 'replaceState' : 'pushState']('', document.title, newURL);
                    }

                } else {
                    if(_historyChanged) {
                        _windowLoc.replace( newURL );
                    } else {
                        _windowLoc.hash = newHash;
                    }
                }



                _historyChanged = true;
                _hashChangeTimeout = setTimeout(function() {
                    _hashChangedByScript = false;
                }, 60);
            };





        _registerModule('History', {



            publicMethods: {
                initHistory: function() {

                    framework.extend(_options, _historyDefaultOptions, true);

                    if( !_options.history ) {
                        return;
                    }


                    _windowLoc = window.location;
                    _urlChangedOnce = false;
                    _closedFromURL = false;
                    _historyChanged = false;
                    _initialHash = _getHash();
                    _supportsPushState = ('pushState' in history);


                    if(_initialHash.indexOf('gid=') > -1) {
                        _initialHash = _initialHash.split('&gid=')[0];
                        _initialHash = _initialHash.split('?gid=')[0];
                    }


                    _listen('afterChange', self.updateURL);
                    _listen('unbindEvents', function() {
                        framework.unbind(window, 'hashchange', self.onHashChange);
                    });


                    var returnToOriginal = function() {
                        _hashReseted = true;
                        if(!_closedFromURL) {

                            if(_urlChangedOnce) {
                                history.back();
                            } else {

                                if(_initialHash) {
                                    _windowLoc.hash = _initialHash;
                                } else {
                                    if (_supportsPushState) {

                                        // remove hash from url without refreshing it or scrolling to top
                                        history.pushState('', document.title,  _windowLoc.pathname + _windowLoc.search );
                                    } else {
                                        _windowLoc.hash = '';
                                    }
                                }
                            }

                        }

                        _cleanHistoryTimeouts();
                    };


                    _listen('unbindEvents', function() {
                        if(_closedByScroll) {
                            // if PhotoSwipe is closed by scroll, we go "back" before the closing animation starts
                            // this is done to keep the scroll position
                            returnToOriginal();
                        }
                    });
                    _listen('destroy', function() {
                        if(!_hashReseted) {
                            returnToOriginal();
                        }
                    });
                    _listen('firstUpdate', function() {
                        _currentItemIndex = _parseItemIndexFromURL().pid;
                    });




                    var index = _initialHash.indexOf('pid=');
                    if(index > -1) {
                        _initialHash = _initialHash.substring(0, index);
                        if(_initialHash.slice(-1) === '&') {
                            _initialHash = _initialHash.slice(0, -1);
                        }
                    }


                    setTimeout(function() {
                        if(_isOpen) { // hasn't destroyed yet
                            framework.bind(window, 'hashchange', self.onHashChange);
                        }
                    }, 40);

                },
                onHashChange: function() {

                    if(_getHash() === _initialHash) {

                        _closedFromURL = true;
                        self.close();
                        return;
                    }
                    if(!_hashChangedByScript) {

                        _hashChangedByHistory = true;
                        self.goTo( _parseItemIndexFromURL().pid );
                        _hashChangedByHistory = false;
                    }

                },
                updateURL: function() {

                    // Delay the update of URL, to avoid lag during transition,
                    // and to not to trigger actions like "refresh page sound" or "blinking favicon" to often

                    _cleanHistoryTimeouts();


                    if(_hashChangedByHistory) {
                        return;
                    }

                    if(!_historyChanged) {
                        _updateHash(); // first time
                    } else {
                        _historyUpdateTimeout = setTimeout(_updateHash, 800);
                    }
                }

            }
        });


        /*>>history*/
        framework.extend(self, publicMethods); };
    return PhotoSwipe;
});/*! PhotoSwipe Default UI - 4.1.1 - 2015-12-24
* http://photoswipe.com
* Copyright (c) 2015 Dmitry Semenov; */
/**
 *
 * UI on top of main sliding area (caption, arrows, close button, etc.).
 * Built just using public methods/properties of PhotoSwipe.
 *
 */
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define(factory);
    } else if (typeof exports === 'object') {
        module.exports = factory();
    } else {
        root.PhotoSwipeUI_Default = factory();
    }
})(this, function () {

    'use strict';



    var PhotoSwipeUI_Default =
        function(pswp, framework) {

            var ui = this;
            var _overlayUIUpdated = false,
                _controlsVisible = true,
                _fullscrenAPI,
                _controls,
                _captionContainer,
                _fakeCaptionContainer,
                _indexIndicator,
                _shareButton,
                _shareModal,
                _shareModalHidden = true,
                _initalCloseOnScrollValue,
                _isIdle,
                _listen,

                _loadingIndicator,
                _loadingIndicatorHidden,
                _loadingIndicatorTimeout,

                _galleryHasOneSlide,

                _options,
                _defaultUIOptions = {
                    barsSize: {top:44, bottom:'auto'},
                    closeElClasses: ['item', 'caption', 'zoom-wrap', 'ui', 'top-bar'],
                    timeToIdle: 4000,
                    timeToIdleOutside: 1000,
                    loadingIndicatorDelay: 1000, // 2s

                    addCaptionHTMLFn: function(item, captionEl /*, isFake */) {
                        if(!item.title) {
                            captionEl.children[0].innerHTML = '';
                            return false;
                        }
                        captionEl.children[0].innerHTML = item.title;
                        return true;
                    },

                    closeEl:true,
                    captionEl: true,
                    fullscreenEl: true,
                    zoomEl: true,
                    shareEl: true,
                    counterEl: true,
                    arrowEl: true,
                    preloaderEl: true,

                    tapToClose: false,
                    tapToToggleControls: true,

                    clickToCloseNonZoomable: true,

                    shareButtons: [
                        {id:'facebook', label:'Share on Facebook', url:'https://www.facebook.com/sharer/sharer.php?u={{url}}'},
                        {id:'twitter', label:'Tweet', url:'https://twitter.com/intent/tweet?text={{text}}&url={{url}}'},
                        {id:'pinterest', label:'Pin it', url:'http://www.pinterest.com/pin/create/button/'+
                                '?url={{url}}&media={{image_url}}&description={{text}}'},
                        {id:'download', label:'Download image', url:'{{raw_image_url}}', download:true}
                    ],
                    getImageURLForShare: function( /* shareButtonData */ ) {
                        return pswp.currItem.src || '';
                    },
                    getPageURLForShare: function( /* shareButtonData */ ) {
                        return window.location.href;
                    },
                    getTextForShare: function( /* shareButtonData */ ) {
                        return pswp.currItem.title || '';
                    },

                    indexIndicatorSep: ' / ',
                    fitControlsWidth: 1200

                },
                _blockControlsTap,
                _blockControlsTapTimeout;



            var _onControlsTap = function(e) {
                    if(_blockControlsTap) {
                        return true;
                    }


                    e = e || window.event;

                    if(_options.timeToIdle && _options.mouseUsed && !_isIdle) {
                        // reset idle timer
                        _onIdleMouseMove();
                    }


                    var target = e.target || e.srcElement,
                        uiElement,
                        clickedClass = target.getAttribute('class') || '',
                        found;

                    for(var i = 0; i < _uiElements.length; i++) {
                        uiElement = _uiElements[i];
                        if(uiElement.onTap && clickedClass.indexOf('pswp__' + uiElement.name ) > -1 ) {
                            uiElement.onTap();
                            found = true;

                        }
                    }

                    if(found) {
                        if(e.stopPropagation) {
                            e.stopPropagation();
                        }
                        _blockControlsTap = true;

                        // Some versions of Android don't prevent ghost click event
                        // when preventDefault() was called on touchstart and/or touchend.
                        //
                        // This happens on v4.3, 4.2, 4.1,
                        // older versions strangely work correctly,
                        // but just in case we add delay on all of them)
                        var tapDelay = framework.features.isOldAndroid ? 600 : 30;
                        _blockControlsTapTimeout = setTimeout(function() {
                            _blockControlsTap = false;
                        }, tapDelay);
                    }

                },
                _fitControlsInViewport = function() {
                    return !pswp.likelyTouchDevice || _options.mouseUsed || screen.width > _options.fitControlsWidth;
                },
                _togglePswpClass = function(el, cName, add) {
                    framework[ (add ? 'add' : 'remove') + 'Class' ](el, 'pswp__' + cName);
                },

                // add class when there is just one item in the gallery
                // (by default it hides left/right arrows and 1ofX counter)
                _countNumItems = function() {
                    var hasOneSlide = (_options.getNumItemsFn() === 1);

                    if(hasOneSlide !== _galleryHasOneSlide) {
                        _togglePswpClass(_controls, 'ui--one-slide', hasOneSlide);
                        _galleryHasOneSlide = hasOneSlide;
                    }
                },
                _toggleShareModalClass = function() {
                    _togglePswpClass(_shareModal, 'share-modal--hidden', _shareModalHidden);
                },
                _toggleShareModal = function() {

                    _shareModalHidden = !_shareModalHidden;


                    if(!_shareModalHidden) {
                        _toggleShareModalClass();
                        setTimeout(function() {
                            if(!_shareModalHidden) {
                                framework.addClass(_shareModal, 'pswp__share-modal--fade-in');
                            }
                        }, 30);
                    } else {
                        framework.removeClass(_shareModal, 'pswp__share-modal--fade-in');
                        setTimeout(function() {
                            if(_shareModalHidden) {
                                _toggleShareModalClass();
                            }
                        }, 300);
                    }

                    if(!_shareModalHidden) {
                        _updateShareURLs();
                    }
                    return false;
                },

                _openWindowPopup = function(e) {
                    e = e || window.event;
                    var target = e.target || e.srcElement;

                    pswp.shout('shareLinkClick', e, target);

                    if(!target.href) {
                        return false;
                    }

                    if( target.hasAttribute('download') ) {
                        return true;
                    }

                    window.open(target.href, 'pswp_share', 'scrollbars=yes,resizable=yes,toolbar=no,'+
                        'location=yes,width=550,height=420,top=100,left=' +
                        (window.screen ? Math.round(screen.width / 2 - 275) : 100)  );

                    if(!_shareModalHidden) {
                        _toggleShareModal();
                    }

                    return false;
                },
                _updateShareURLs = function() {
                    var shareButtonOut = '',
                        shareButtonData,
                        shareURL,
                        image_url,
                        page_url,
                        share_text;

                    for(var i = 0; i < _options.shareButtons.length; i++) {
                        shareButtonData = _options.shareButtons[i];

                        image_url = _options.getImageURLForShare(shareButtonData);
                        page_url = _options.getPageURLForShare(shareButtonData);
                        share_text = _options.getTextForShare(shareButtonData);

                        shareURL = shareButtonData.url.replace('{{url}}', encodeURIComponent(page_url) )
                            .replace('{{image_url}}', encodeURIComponent(image_url) )
                            .replace('{{raw_image_url}}', image_url )
                            .replace('{{text}}', encodeURIComponent(share_text) );

                        shareButtonOut += '<a href="' + shareURL + '" target="_blank" '+
                            'class="pswp__share--' + shareButtonData.id + '"' +
                            (shareButtonData.download ? 'download' : '') + '>' +
                            shareButtonData.label + '</a>';

                        if(_options.parseShareButtonOut) {
                            shareButtonOut = _options.parseShareButtonOut(shareButtonData, shareButtonOut);
                        }
                    }
                    _shareModal.children[0].innerHTML = shareButtonOut;
                    _shareModal.children[0].onclick = _openWindowPopup;

                },
                _hasCloseClass = function(target) {
                    for(var  i = 0; i < _options.closeElClasses.length; i++) {
                        if( framework.hasClass(target, 'pswp__' + _options.closeElClasses[i]) ) {
                            return true;
                        }
                    }
                },
                _idleInterval,
                _idleTimer,
                _idleIncrement = 0,
                _onIdleMouseMove = function() {
                    clearTimeout(_idleTimer);
                    _idleIncrement = 0;
                    if(_isIdle) {
                        ui.setIdle(false);
                    }
                },
                _onMouseLeaveWindow = function(e) {
                    e = e ? e : window.event;
                    var from = e.relatedTarget || e.toElement;
                    if (!from || from.nodeName === 'HTML') {
                        clearTimeout(_idleTimer);
                        _idleTimer = setTimeout(function() {
                            ui.setIdle(true);
                        }, _options.timeToIdleOutside);
                    }
                },
                _setupFullscreenAPI = function() {
                    if(_options.fullscreenEl && !framework.features.isOldAndroid) {
                        if(!_fullscrenAPI) {
                            _fullscrenAPI = ui.getFullscreenAPI();
                        }
                        if(_fullscrenAPI) {
                            framework.bind(document, _fullscrenAPI.eventK, ui.updateFullscreen);
                            ui.updateFullscreen();
                            framework.addClass(pswp.template, 'pswp--supports-fs');
                        } else {
                            framework.removeClass(pswp.template, 'pswp--supports-fs');
                        }
                    }
                },
                _setupLoadingIndicator = function() {
                    // Setup loading indicator
                    if(_options.preloaderEl) {

                        _toggleLoadingIndicator(true);

                        _listen('beforeChange', function() {

                            clearTimeout(_loadingIndicatorTimeout);

                            // display loading indicator with delay
                            _loadingIndicatorTimeout = setTimeout(function() {

                                if(pswp.currItem && pswp.currItem.loading) {

                                    if( !pswp.allowProgressiveImg() || (pswp.currItem.img && !pswp.currItem.img.naturalWidth)  ) {
                                        // show preloader if progressive loading is not enabled,
                                        // or image width is not defined yet (because of slow connection)
                                        _toggleLoadingIndicator(false);
                                        // items-controller.js function allowProgressiveImg
                                    }

                                } else {
                                    _toggleLoadingIndicator(true); // hide preloader
                                }

                            }, _options.loadingIndicatorDelay);

                        });
                        _listen('imageLoadComplete', function(index, item) {
                            if(pswp.currItem === item) {
                                _toggleLoadingIndicator(true);
                            }
                        });

                    }
                },
                _toggleLoadingIndicator = function(hide) {
                    if( _loadingIndicatorHidden !== hide ) {
                        _togglePswpClass(_loadingIndicator, 'preloader--active', !hide);
                        _loadingIndicatorHidden = hide;
                    }
                },
                _applyNavBarGaps = function(item) {
                    var gap = item.vGap;

                    if( _fitControlsInViewport() ) {

                        var bars = _options.barsSize;
                        if(_options.captionEl && bars.bottom === 'auto') {
                            if(!_fakeCaptionContainer) {
                                _fakeCaptionContainer = framework.createEl('pswp__caption pswp__caption--fake');
                                _fakeCaptionContainer.appendChild( framework.createEl('pswp__caption__center') );
                                _controls.insertBefore(_fakeCaptionContainer, _captionContainer);
                                framework.addClass(_controls, 'pswp__ui--fit');
                            }
                            if( _options.addCaptionHTMLFn(item, _fakeCaptionContainer, true) ) {

                                var captionSize = _fakeCaptionContainer.clientHeight;
                                gap.bottom = parseInt(captionSize,10) || 44;
                            } else {
                                gap.bottom = bars.top; // if no caption, set size of bottom gap to size of top
                            }
                        } else {
                            gap.bottom = bars.bottom === 'auto' ? 0 : bars.bottom;
                        }

                        // height of top bar is static, no need to calculate it
                        gap.top = bars.top;
                    } else {
                        gap.top = gap.bottom = 0;
                    }
                },
                _setupIdle = function() {
                    // Hide controls when mouse is used
                    if(_options.timeToIdle) {
                        _listen('mouseUsed', function() {

                            framework.bind(document, 'mousemove', _onIdleMouseMove);
                            framework.bind(document, 'mouseout', _onMouseLeaveWindow);

                            _idleInterval = setInterval(function() {
                                _idleIncrement++;
                                if(_idleIncrement === 2) {
                                    ui.setIdle(true);
                                }
                            }, _options.timeToIdle / 2);
                        });
                    }
                },
                _setupHidingControlsDuringGestures = function() {

                    // Hide controls on vertical drag
                    _listen('onVerticalDrag', function(now) {
                        if(_controlsVisible && now < 0.95) {
                            ui.hideControls();
                        } else if(!_controlsVisible && now >= 0.95) {
                            ui.showControls();
                        }
                    });

                    // Hide controls when pinching to close
                    var pinchControlsHidden;
                    _listen('onPinchClose' , function(now) {
                        if(_controlsVisible && now < 0.9) {
                            ui.hideControls();
                            pinchControlsHidden = true;
                        } else if(pinchControlsHidden && !_controlsVisible && now > 0.9) {
                            ui.showControls();
                        }
                    });

                    _listen('zoomGestureEnded', function() {
                        pinchControlsHidden = false;
                        if(pinchControlsHidden && !_controlsVisible) {
                            ui.showControls();
                        }
                    });

                };



            var _uiElements = [
                {
                    name: 'caption',
                    option: 'captionEl',
                    onInit: function(el) {
                        _captionContainer = el;
                    }
                },
                {
                    name: 'share-modal',
                    option: 'shareEl',
                    onInit: function(el) {
                        _shareModal = el;
                    },
                    onTap: function() {
                        _toggleShareModal();
                    }
                },
                {
                    name: 'button--share',
                    option: 'shareEl',
                    onInit: function(el) {
                        _shareButton = el;
                    },
                    onTap: function() {
                        _toggleShareModal();
                    }
                },
                {
                    name: 'button--zoom',
                    option: 'zoomEl',
                    onTap: pswp.toggleDesktopZoom
                },
                {
                    name: 'counter',
                    option: 'counterEl',
                    onInit: function(el) {
                        _indexIndicator = el;
                    }
                },
                {
                    name: 'button--close',
                    option: 'closeEl',
                    onTap: pswp.close
                },
                {
                    name: 'button--arrow--left',
                    option: 'arrowEl',
                    onTap: pswp.prev
                },
                {
                    name: 'button--arrow--right',
                    option: 'arrowEl',
                    onTap: pswp.next
                },
                {
                    name: 'button--fs',
                    option: 'fullscreenEl',
                    onTap: function() {
                        if(_fullscrenAPI.isFullscreen()) {
                            _fullscrenAPI.exit();
                        } else {
                            _fullscrenAPI.enter();
                        }
                    }
                },
                {
                    name: 'preloader',
                    option: 'preloaderEl',
                    onInit: function(el) {
                        _loadingIndicator = el;
                    }
                }

            ];

            var _setupUIElements = function() {
                var item,
                    classAttr,
                    uiElement;

                var loopThroughChildElements = function(sChildren) {
                    if(!sChildren) {
                        return;
                    }

                    var l = sChildren.length;
                    for(var i = 0; i < l; i++) {
                        item = sChildren[i];
                        classAttr = item.className;

                        for(var a = 0; a < _uiElements.length; a++) {
                            uiElement = _uiElements[a];

                            if(classAttr.indexOf('pswp__' + uiElement.name) > -1  ) {

                                if( _options[uiElement.option] ) { // if element is not disabled from options

                                    framework.removeClass(item, 'pswp__element--disabled');
                                    if(uiElement.onInit) {
                                        uiElement.onInit(item);
                                    }

                                    //item.style.display = 'block';
                                } else {
                                    framework.addClass(item, 'pswp__element--disabled');
                                    //item.style.display = 'none';
                                }
                            }
                        }
                    }
                };
                loopThroughChildElements(_controls.children);

                var topBar =  framework.getChildByClass(_controls, 'pswp__top-bar');
                if(topBar) {
                    loopThroughChildElements( topBar.children );
                }
            };




            ui.init = function() {

                // extend options
                framework.extend(pswp.options, _defaultUIOptions, true);

                // create local link for fast access
                _options = pswp.options;

                // find pswp__ui element
                _controls = framework.getChildByClass(pswp.scrollWrap, 'pswp__ui');

                // create local link
                _listen = pswp.listen;


                _setupHidingControlsDuringGestures();

                // update controls when slides change
                _listen('beforeChange', ui.update);

                // toggle zoom on double-tap
                _listen('doubleTap', function(point) {
                    var initialZoomLevel = pswp.currItem.initialZoomLevel;
                    if(pswp.getZoomLevel() !== initialZoomLevel) {
                        pswp.zoomTo(initialZoomLevel, point, 333);
                    } else {
                        pswp.zoomTo(_options.getDoubleTapZoom(false, pswp.currItem), point, 333);
                    }
                });

                // Allow text selection in caption
                _listen('preventDragEvent', function(e, isDown, preventObj) {
                    var t = e.target || e.srcElement;
                    if(
                        t &&
                        t.getAttribute('class') && e.type.indexOf('mouse') > -1 &&
                        ( t.getAttribute('class').indexOf('__caption') > 0 || (/(SMALL|STRONG|EM)/i).test(t.tagName) )
                    ) {
                        preventObj.prevent = false;
                    }
                });

                // bind events for UI
                _listen('bindEvents', function() {
                    framework.bind(_controls, 'pswpTap click', _onControlsTap);
                    framework.bind(pswp.scrollWrap, 'pswpTap', ui.onGlobalTap);

                    if(!pswp.likelyTouchDevice) {
                        framework.bind(pswp.scrollWrap, 'mouseover', ui.onMouseOver);
                    }
                });

                // unbind events for UI
                _listen('unbindEvents', function() {
                    if(!_shareModalHidden) {
                        _toggleShareModal();
                    }

                    if(_idleInterval) {
                        clearInterval(_idleInterval);
                    }
                    framework.unbind(document, 'mouseout', _onMouseLeaveWindow);
                    framework.unbind(document, 'mousemove', _onIdleMouseMove);
                    framework.unbind(_controls, 'pswpTap click', _onControlsTap);
                    framework.unbind(pswp.scrollWrap, 'pswpTap', ui.onGlobalTap);
                    framework.unbind(pswp.scrollWrap, 'mouseover', ui.onMouseOver);

                    if(_fullscrenAPI) {
                        framework.unbind(document, _fullscrenAPI.eventK, ui.updateFullscreen);
                        if(_fullscrenAPI.isFullscreen()) {
                            _options.hideAnimationDuration = 0;
                            _fullscrenAPI.exit();
                        }
                        _fullscrenAPI = null;
                    }
                });


                // clean up things when gallery is destroyed
                _listen('destroy', function() {
                    if(_options.captionEl) {
                        if(_fakeCaptionContainer) {
                            _controls.removeChild(_fakeCaptionContainer);
                        }
                        framework.removeClass(_captionContainer, 'pswp__caption--empty');
                    }

                    if(_shareModal) {
                        _shareModal.children[0].onclick = null;
                    }
                    framework.removeClass(_controls, 'pswp__ui--over-close');
                    framework.addClass( _controls, 'pswp__ui--hidden');
                    ui.setIdle(false);
                });


                if(!_options.showAnimationDuration) {
                    framework.removeClass( _controls, 'pswp__ui--hidden');
                }
                _listen('initialZoomIn', function() {
                    if(_options.showAnimationDuration) {
                        framework.removeClass( _controls, 'pswp__ui--hidden');
                    }
                });
                _listen('initialZoomOut', function() {
                    framework.addClass( _controls, 'pswp__ui--hidden');
                });

                _listen('parseVerticalMargin', _applyNavBarGaps);

                _setupUIElements();

                if(_options.shareEl && _shareButton && _shareModal) {
                    _shareModalHidden = true;
                }

                _countNumItems();

                _setupIdle();

                _setupFullscreenAPI();

                _setupLoadingIndicator();
            };

            ui.setIdle = function(isIdle) {
                _isIdle = isIdle;
                _togglePswpClass(_controls, 'ui--idle', isIdle);
            };

            ui.update = function() {
                // Don't update UI if it's hidden
                if(_controlsVisible && pswp.currItem) {

                    ui.updateIndexIndicator();

                    if(_options.captionEl) {
                        _options.addCaptionHTMLFn(pswp.currItem, _captionContainer);

                        _togglePswpClass(_captionContainer, 'caption--empty', !pswp.currItem.title);
                    }

                    _overlayUIUpdated = true;

                } else {
                    _overlayUIUpdated = false;
                }

                if(!_shareModalHidden) {
                    _toggleShareModal();
                }

                _countNumItems();
            };

            ui.updateFullscreen = function(e) {

                if(e) {
                    // some browsers change window scroll position during the fullscreen
                    // so PhotoSwipe updates it just in case
                    setTimeout(function() {
                        pswp.setScrollOffset( 0, framework.getScrollY() );
                    }, 50);
                }

                // toogle pswp--fs class on root element
                framework[ (_fullscrenAPI.isFullscreen() ? 'add' : 'remove') + 'Class' ](pswp.template, 'pswp--fs');
            };

            ui.updateIndexIndicator = function() {
                if(_options.counterEl) {
                    _indexIndicator.innerHTML = (pswp.getCurrentIndex()+1) +
                        _options.indexIndicatorSep +
                        _options.getNumItemsFn();
                }
            };

            ui.onGlobalTap = function(e) {
                e = e || window.event;
                var target = e.target || e.srcElement;

                if(_blockControlsTap) {
                    return;
                }

                if(e.detail && e.detail.pointerType === 'mouse') {

                    // close gallery if clicked outside of the image
                    if(_hasCloseClass(target)) {
                        pswp.close();
                        return;
                    }

                    if(framework.hasClass(target, 'pswp__img')) {
                        if(pswp.getZoomLevel() === 1 && pswp.getZoomLevel() <= pswp.currItem.fitRatio) {
                            if(_options.clickToCloseNonZoomable) {
                                pswp.close();
                            }
                        } else {
                            pswp.toggleDesktopZoom(e.detail.releasePoint);
                        }
                    }

                } else {

                    // tap anywhere (except buttons) to toggle visibility of controls
                    if(_options.tapToToggleControls) {
                        if(_controlsVisible) {
                            ui.hideControls();
                        } else {
                            ui.showControls();
                        }
                    }

                    // tap to close gallery
                    if(_options.tapToClose && (framework.hasClass(target, 'pswp__img') || _hasCloseClass(target)) ) {
                        pswp.close();
                        return;
                    }

                }
            };
            ui.onMouseOver = function(e) {
                e = e || window.event;
                var target = e.target || e.srcElement;

                // add class when mouse is over an element that should close the gallery
                _togglePswpClass(_controls, 'ui--over-close', _hasCloseClass(target));
            };

            ui.hideControls = function() {
                framework.addClass(_controls,'pswp__ui--hidden');
                _controlsVisible = false;
            };

            ui.showControls = function() {
                _controlsVisible = true;
                if(!_overlayUIUpdated) {
                    ui.update();
                }
                framework.removeClass(_controls,'pswp__ui--hidden');
            };

            ui.supportsFullscreen = function() {
                var d = document;
                return !!(d.exitFullscreen || d.mozCancelFullScreen || d.webkitExitFullscreen || d.msExitFullscreen);
            };

            ui.getFullscreenAPI = function() {
                var dE = document.documentElement,
                    api,
                    tF = 'fullscreenchange';

                if (dE.requestFullscreen) {
                    api = {
                        enterK: 'requestFullscreen',
                        exitK: 'exitFullscreen',
                        elementK: 'fullscreenElement',
                        eventK: tF
                    };

                } else if(dE.mozRequestFullScreen ) {
                    api = {
                        enterK: 'mozRequestFullScreen',
                        exitK: 'mozCancelFullScreen',
                        elementK: 'mozFullScreenElement',
                        eventK: 'moz' + tF
                    };



                } else if(dE.webkitRequestFullscreen) {
                    api = {
                        enterK: 'webkitRequestFullscreen',
                        exitK: 'webkitExitFullscreen',
                        elementK: 'webkitFullscreenElement',
                        eventK: 'webkit' + tF
                    };

                } else if(dE.msRequestFullscreen) {
                    api = {
                        enterK: 'msRequestFullscreen',
                        exitK: 'msExitFullscreen',
                        elementK: 'msFullscreenElement',
                        eventK: 'MSFullscreenChange'
                    };
                }

                if(api) {
                    api.enter = function() {
                        // disable close-on-scroll in fullscreen
                        _initalCloseOnScrollValue = _options.closeOnScroll;
                        _options.closeOnScroll = false;

                        if(this.enterK === 'webkitRequestFullscreen') {
                            pswp.template[this.enterK]( Element.ALLOW_KEYBOARD_INPUT );
                        } else {
                            return pswp.template[this.enterK]();
                        }
                    };
                    api.exit = function() {
                        _options.closeOnScroll = _initalCloseOnScrollValue;

                        return document[this.exitK]();

                    };
                    api.isFullscreen = function() { return document[this.elementK]; };
                }

                return api;
            };



        };
    return PhotoSwipeUI_Default;


});
/*!
 * jQuery Form Plugin
 * version: 3.51.0-2014.06.20
 * Requires jQuery v1.5 or later
 * Copyright (c) 2014 M. Alsup
 * Examples and documentation at: http://malsup.com/jquery/form/
 * Project repository: https://github.com/malsup/form
 * Dual licensed under the MIT and GPL licenses.
 * https://github.com/malsup/form#copyright-and-license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery"],e):e("undefined"!=typeof jQuery?jQuery:window.Zepto)}(function(e){"use strict";function t(t){var r=t.data;t.isDefaultPrevented()||(t.preventDefault(),e(t.target).ajaxSubmit(r))}function r(t){var r=t.target,a=e(r);if(!a.is("[type=submit],[type=image]")){var n=a.closest("[type=submit]");if(0===n.length)return;r=n[0]}var i=this;if(i.clk=r,"image"==r.type)if(void 0!==t.offsetX)i.clk_x=t.offsetX,i.clk_y=t.offsetY;else if("function"==typeof e.fn.offset){var o=a.offset();i.clk_x=t.pageX-o.left,i.clk_y=t.pageY-o.top}else i.clk_x=t.pageX-r.offsetLeft,i.clk_y=t.pageY-r.offsetTop;setTimeout(function(){i.clk=i.clk_x=i.clk_y=null},100)}function a(){if(e.fn.ajaxSubmit.debug){var t="[jquery.form] "+Array.prototype.join.call(arguments,"");window.console&&window.console.log?window.console.log(t):window.opera&&window.opera.postError&&window.opera.postError(t)}}var n={};n.fileapi=void 0!==e("<input type='file'/>").get(0).files,n.formdata=void 0!==window.FormData;var i=!!e.fn.prop;e.fn.attr2=function(){if(!i)return this.attr.apply(this,arguments);var e=this.prop.apply(this,arguments);return e&&e.jquery||"string"==typeof e?e:this.attr.apply(this,arguments)},e.fn.ajaxSubmit=function(t){function r(r){var a,n,i=e.param(r,t.traditional).split("&"),o=i.length,s=[];for(a=0;o>a;a++)i[a]=i[a].replace(/\+/g," "),n=i[a].split("="),s.push([decodeURIComponent(n[0]),decodeURIComponent(n[1])]);return s}function o(a){for(var n=new FormData,i=0;i<a.length;i++)n.append(a[i].name,a[i].value);if(t.extraData){var o=r(t.extraData);for(i=0;i<o.length;i++)o[i]&&n.append(o[i][0],o[i][1])}t.data=null;var s=e.extend(!0,{},e.ajaxSettings,t,{contentType:!1,processData:!1,cache:!1,type:u||"POST"});t.uploadProgress&&(s.xhr=function(){var r=e.ajaxSettings.xhr();return r.upload&&r.upload.addEventListener("progress",function(e){var r=0,a=e.loaded||e.position,n=e.total;e.lengthComputable&&(r=Math.ceil(a/n*100)),t.uploadProgress(e,a,n,r)},!1),r}),s.data=null;var c=s.beforeSend;return s.beforeSend=function(e,r){r.data=t.formData?t.formData:n,c&&c.call(this,e,r)},e.ajax(s)}function s(r){function n(e){var t=null;try{e.contentWindow&&(t=e.contentWindow.document)}catch(r){a("cannot get iframe.contentWindow document: "+r)}if(t)return t;try{t=e.contentDocument?e.contentDocument:e.document}catch(r){a("cannot get iframe.contentDocument: "+r),t=e.document}return t}function o(){function t(){try{var e=n(g).readyState;a("state = "+e),e&&"uninitialized"==e.toLowerCase()&&setTimeout(t,50)}catch(r){a("Server abort: ",r," (",r.name,")"),s(k),j&&clearTimeout(j),j=void 0}}var r=f.attr2("target"),i=f.attr2("action"),o="multipart/form-data",c=f.attr("enctype")||f.attr("encoding")||o;w.setAttribute("target",p),(!u||/post/i.test(u))&&w.setAttribute("method","POST"),i!=m.url&&w.setAttribute("action",m.url),m.skipEncodingOverride||u&&!/post/i.test(u)||f.attr({encoding:"multipart/form-data",enctype:"multipart/form-data"}),m.timeout&&(j=setTimeout(function(){T=!0,s(D)},m.timeout));var l=[];try{if(m.extraData)for(var d in m.extraData)m.extraData.hasOwnProperty(d)&&l.push(e.isPlainObject(m.extraData[d])&&m.extraData[d].hasOwnProperty("name")&&m.extraData[d].hasOwnProperty("value")?e('<input type="hidden" name="'+m.extraData[d].name+'">').val(m.extraData[d].value).appendTo(w)[0]:e('<input type="hidden" name="'+d+'">').val(m.extraData[d]).appendTo(w)[0]);m.iframeTarget||v.appendTo("body"),g.attachEvent?g.attachEvent("onload",s):g.addEventListener("load",s,!1),setTimeout(t,15);try{w.submit()}catch(h){var x=document.createElement("form").submit;x.apply(w)}}finally{w.setAttribute("action",i),w.setAttribute("enctype",c),r?w.setAttribute("target",r):f.removeAttr("target"),e(l).remove()}}function s(t){if(!x.aborted&&!F){if(M=n(g),M||(a("cannot access response document"),t=k),t===D&&x)return x.abort("timeout"),void S.reject(x,"timeout");if(t==k&&x)return x.abort("server abort"),void S.reject(x,"error","server abort");if(M&&M.location.href!=m.iframeSrc||T){g.detachEvent?g.detachEvent("onload",s):g.removeEventListener("load",s,!1);var r,i="success";try{if(T)throw"timeout";var o="xml"==m.dataType||M.XMLDocument||e.isXMLDoc(M);if(a("isXml="+o),!o&&window.opera&&(null===M.body||!M.body.innerHTML)&&--O)return a("requeing onLoad callback, DOM not available"),void setTimeout(s,250);var u=M.body?M.body:M.documentElement;x.responseText=u?u.innerHTML:null,x.responseXML=M.XMLDocument?M.XMLDocument:M,o&&(m.dataType="xml"),x.getResponseHeader=function(e){var t={"content-type":m.dataType};return t[e.toLowerCase()]},u&&(x.status=Number(u.getAttribute("status"))||x.status,x.statusText=u.getAttribute("statusText")||x.statusText);var c=(m.dataType||"").toLowerCase(),l=/(json|script|text)/.test(c);if(l||m.textarea){var f=M.getElementsByTagName("textarea")[0];if(f)x.responseText=f.value,x.status=Number(f.getAttribute("status"))||x.status,x.statusText=f.getAttribute("statusText")||x.statusText;else if(l){var p=M.getElementsByTagName("pre")[0],h=M.getElementsByTagName("body")[0];p?x.responseText=p.textContent?p.textContent:p.innerText:h&&(x.responseText=h.textContent?h.textContent:h.innerText)}}else"xml"==c&&!x.responseXML&&x.responseText&&(x.responseXML=X(x.responseText));try{E=_(x,c,m)}catch(y){i="parsererror",x.error=r=y||i}}catch(y){a("error caught: ",y),i="error",x.error=r=y||i}x.aborted&&(a("upload aborted"),i=null),x.status&&(i=x.status>=200&&x.status<300||304===x.status?"success":"error"),"success"===i?(m.success&&m.success.call(m.context,E,"success",x),S.resolve(x.responseText,"success",x),d&&e.event.trigger("ajaxSuccess",[x,m])):i&&(void 0===r&&(r=x.statusText),m.error&&m.error.call(m.context,x,i,r),S.reject(x,"error",r),d&&e.event.trigger("ajaxError",[x,m,r])),d&&e.event.trigger("ajaxComplete",[x,m]),d&&!--e.active&&e.event.trigger("ajaxStop"),m.complete&&m.complete.call(m.context,x,i),F=!0,m.timeout&&clearTimeout(j),setTimeout(function(){m.iframeTarget?v.attr("src",m.iframeSrc):v.remove(),x.responseXML=null},100)}}}var c,l,m,d,p,v,g,x,y,b,T,j,w=f[0],S=e.Deferred();if(S.abort=function(e){x.abort(e)},r)for(l=0;l<h.length;l++)c=e(h[l]),i?c.prop("disabled",!1):c.removeAttr("disabled");if(m=e.extend(!0,{},e.ajaxSettings,t),m.context=m.context||m,p="jqFormIO"+(new Date).getTime(),m.iframeTarget?(v=e(m.iframeTarget),b=v.attr2("name"),b?p=b:v.attr2("name",p)):(v=e('<iframe name="'+p+'" src="'+m.iframeSrc+'" />'),v.css({position:"absolute",top:"-1000px",left:"-1000px"})),g=v[0],x={aborted:0,responseText:null,responseXML:null,status:0,statusText:"n/a",getAllResponseHeaders:function(){},getResponseHeader:function(){},setRequestHeader:function(){},abort:function(t){var r="timeout"===t?"timeout":"aborted";a("aborting upload... "+r),this.aborted=1;try{g.contentWindow.document.execCommand&&g.contentWindow.document.execCommand("Stop")}catch(n){}v.attr("src",m.iframeSrc),x.error=r,m.error&&m.error.call(m.context,x,r,t),d&&e.event.trigger("ajaxError",[x,m,r]),m.complete&&m.complete.call(m.context,x,r)}},d=m.global,d&&0===e.active++&&e.event.trigger("ajaxStart"),d&&e.event.trigger("ajaxSend",[x,m]),m.beforeSend&&m.beforeSend.call(m.context,x,m)===!1)return m.global&&e.active--,S.reject(),S;if(x.aborted)return S.reject(),S;y=w.clk,y&&(b=y.name,b&&!y.disabled&&(m.extraData=m.extraData||{},m.extraData[b]=y.value,"image"==y.type&&(m.extraData[b+".x"]=w.clk_x,m.extraData[b+".y"]=w.clk_y)));var D=1,k=2,A=e("meta[name=csrf-token]").attr("content"),L=e("meta[name=csrf-param]").attr("content");L&&A&&(m.extraData=m.extraData||{},m.extraData[L]=A),m.forceSync?o():setTimeout(o,10);var E,M,F,O=50,X=e.parseXML||function(e,t){return window.ActiveXObject?(t=new ActiveXObject("Microsoft.XMLDOM"),t.async="false",t.loadXML(e)):t=(new DOMParser).parseFromString(e,"text/xml"),t&&t.documentElement&&"parsererror"!=t.documentElement.nodeName?t:null},C=e.parseJSON||function(e){return window.eval("("+e+")")},_=function(t,r,a){var n=t.getResponseHeader("content-type")||"",i="xml"===r||!r&&n.indexOf("xml")>=0,o=i?t.responseXML:t.responseText;return i&&"parsererror"===o.documentElement.nodeName&&e.error&&e.error("parsererror"),a&&a.dataFilter&&(o=a.dataFilter(o,r)),"string"==typeof o&&("json"===r||!r&&n.indexOf("json")>=0?o=C(o):("script"===r||!r&&n.indexOf("javascript")>=0)&&e.globalEval(o)),o};return S}if(!this.length)return a("ajaxSubmit: skipping submit process - no element selected"),this;var u,c,l,f=this;"function"==typeof t?t={success:t}:void 0===t&&(t={}),u=t.type||this.attr2("method"),c=t.url||this.attr2("action"),l="string"==typeof c?e.trim(c):"",l=l||window.location.href||"",l&&(l=(l.match(/^([^#]+)/)||[])[1]),t=e.extend(!0,{url:l,success:e.ajaxSettings.success,type:u||e.ajaxSettings.type,iframeSrc:/^https/i.test(window.location.href||"")?"javascript:false":"about:blank"},t);var m={};if(this.trigger("form-pre-serialize",[this,t,m]),m.veto)return a("ajaxSubmit: submit vetoed via form-pre-serialize trigger"),this;if(t.beforeSerialize&&t.beforeSerialize(this,t)===!1)return a("ajaxSubmit: submit aborted via beforeSerialize callback"),this;var d=t.traditional;void 0===d&&(d=e.ajaxSettings.traditional);var p,h=[],v=this.formToArray(t.semantic,h);if(t.data&&(t.extraData=t.data,p=e.param(t.data,d)),t.beforeSubmit&&t.beforeSubmit(v,this,t)===!1)return a("ajaxSubmit: submit aborted via beforeSubmit callback"),this;if(this.trigger("form-submit-validate",[v,this,t,m]),m.veto)return a("ajaxSubmit: submit vetoed via form-submit-validate trigger"),this;var g=e.param(v,d);p&&(g=g?g+"&"+p:p),"GET"==t.type.toUpperCase()?(t.url+=(t.url.indexOf("?")>=0?"&":"?")+g,t.data=null):t.data=g;var x=[];if(t.resetForm&&x.push(function(){f.resetForm()}),t.clearForm&&x.push(function(){f.clearForm(t.includeHidden)}),!t.dataType&&t.target){var y=t.success||function(){};x.push(function(r){var a=t.replaceTarget?"replaceWith":"html";e(t.target)[a](r).each(y,arguments)})}else t.success&&x.push(t.success);if(t.success=function(e,r,a){for(var n=t.context||this,i=0,o=x.length;o>i;i++)x[i].apply(n,[e,r,a||f,f])},t.error){var b=t.error;t.error=function(e,r,a){var n=t.context||this;b.apply(n,[e,r,a,f])}}if(t.complete){var T=t.complete;t.complete=function(e,r){var a=t.context||this;T.apply(a,[e,r,f])}}var j=e("input[type=file]:enabled",this).filter(function(){return""!==e(this).val()}),w=j.length>0,S="multipart/form-data",D=f.attr("enctype")==S||f.attr("encoding")==S,k=n.fileapi&&n.formdata;a("fileAPI :"+k);var A,L=(w||D)&&!k;t.iframe!==!1&&(t.iframe||L)?t.closeKeepAlive?e.get(t.closeKeepAlive,function(){A=s(v)}):A=s(v):A=(w||D)&&k?o(v):e.ajax(t),f.removeData("jqxhr").data("jqxhr",A);for(var E=0;E<h.length;E++)h[E]=null;return this.trigger("form-submit-notify",[this,t]),this},e.fn.ajaxForm=function(n){if(n=n||{},n.delegation=n.delegation&&e.isFunction(e.fn.on),!n.delegation&&0===this.length){var i={s:this.selector,c:this.context};return!e.isReady&&i.s?(a("DOM not ready, queuing ajaxForm"),e(function(){e(i.s,i.c).ajaxForm(n)}),this):(a("terminating; zero elements found by selector"+(e.isReady?"":" (DOM not ready)")),this)}return n.delegation?(e(document).off("submit.form-plugin",this.selector,t).off("click.form-plugin",this.selector,r).on("submit.form-plugin",this.selector,n,t).on("click.form-plugin",this.selector,n,r),this):this.ajaxFormUnbind().bind("submit.form-plugin",n,t).bind("click.form-plugin",n,r)},e.fn.ajaxFormUnbind=function(){return this.unbind("submit.form-plugin click.form-plugin")},e.fn.formToArray=function(t,r){var a=[];if(0===this.length)return a;var i,o=this[0],s=this.attr("id"),u=t?o.getElementsByTagName("*"):o.elements;if(u&&!/MSIE [678]/.test(navigator.userAgent)&&(u=e(u).get()),s&&(i=e(':input[form="'+s+'"]').get(),i.length&&(u=(u||[]).concat(i))),!u||!u.length)return a;var c,l,f,m,d,p,h;for(c=0,p=u.length;p>c;c++)if(d=u[c],f=d.name,f&&!d.disabled)if(t&&o.clk&&"image"==d.type)o.clk==d&&(a.push({name:f,value:e(d).val(),type:d.type}),a.push({name:f+".x",value:o.clk_x},{name:f+".y",value:o.clk_y}));else if(m=e.fieldValue(d,!0),m&&m.constructor==Array)for(r&&r.push(d),l=0,h=m.length;h>l;l++)a.push({name:f,value:m[l]});else if(n.fileapi&&"file"==d.type){r&&r.push(d);var v=d.files;if(v.length)for(l=0;l<v.length;l++)a.push({name:f,value:v[l],type:d.type});else a.push({name:f,value:"",type:d.type})}else null!==m&&"undefined"!=typeof m&&(r&&r.push(d),a.push({name:f,value:m,type:d.type,required:d.required}));if(!t&&o.clk){var g=e(o.clk),x=g[0];f=x.name,f&&!x.disabled&&"image"==x.type&&(a.push({name:f,value:g.val()}),a.push({name:f+".x",value:o.clk_x},{name:f+".y",value:o.clk_y}))}return a},e.fn.formSerialize=function(t){return e.param(this.formToArray(t))},e.fn.fieldSerialize=function(t){var r=[];return this.each(function(){var a=this.name;if(a){var n=e.fieldValue(this,t);if(n&&n.constructor==Array)for(var i=0,o=n.length;o>i;i++)r.push({name:a,value:n[i]});else null!==n&&"undefined"!=typeof n&&r.push({name:this.name,value:n})}}),e.param(r)},e.fn.fieldValue=function(t){for(var r=[],a=0,n=this.length;n>a;a++){var i=this[a],o=e.fieldValue(i,t);null===o||"undefined"==typeof o||o.constructor==Array&&!o.length||(o.constructor==Array?e.merge(r,o):r.push(o))}return r},e.fieldValue=function(t,r){var a=t.name,n=t.type,i=t.tagName.toLowerCase();if(void 0===r&&(r=!0),r&&(!a||t.disabled||"reset"==n||"button"==n||("checkbox"==n||"radio"==n)&&!t.checked||("submit"==n||"image"==n)&&t.form&&t.form.clk!=t||"select"==i&&-1==t.selectedIndex))return null;if("select"==i){var o=t.selectedIndex;if(0>o)return null;for(var s=[],u=t.options,c="select-one"==n,l=c?o+1:u.length,f=c?o:0;l>f;f++){var m=u[f];if(m.selected){var d=m.value;if(d||(d=m.attributes&&m.attributes.value&&!m.attributes.value.specified?m.text:m.value),c)return d;s.push(d)}}return s}return e(t).val()},e.fn.clearForm=function(t){return this.each(function(){e("input,select,textarea",this).clearFields(t)})},e.fn.clearFields=e.fn.clearInputs=function(t){var r=/^(?:color|date|datetime|email|month|number|password|range|search|tel|text|time|url|week)$/i;return this.each(function(){var a=this.type,n=this.tagName.toLowerCase();r.test(a)||"textarea"==n?this.value="":"checkbox"==a||"radio"==a?this.checked=!1:"select"==n?this.selectedIndex=-1:"file"==a?/MSIE/.test(navigator.userAgent)?e(this).replaceWith(e(this).clone(!0)):e(this).val(""):t&&(t===!0&&/hidden/.test(a)||"string"==typeof t&&e(this).is(t))&&(this.value="")})},e.fn.resetForm=function(){return this.each(function(){("function"==typeof this.reset||"object"==typeof this.reset&&!this.reset.nodeType)&&this.reset()})},e.fn.enable=function(e){return void 0===e&&(e=!0),this.each(function(){this.disabled=!e})},e.fn.selected=function(t){return void 0===t&&(t=!0),this.each(function(){var r=this.type;if("checkbox"==r||"radio"==r)this.checked=t;else if("option"==this.tagName.toLowerCase()){var a=e(this).parent("select");t&&a[0]&&"select-one"==a[0].type&&a.find("option").selected(!1),this.selected=t}})},e.fn.ajaxSubmit.debug=!1});/**
 * jQuery org-chart/tree plugin.
 *
 * Author: Wes Nolte
 * http://twitter.com/wesnolte
 *
 * Based on the work of Mark Lee
 * http://www.capricasoftware.co.uk
 *
 * Copyright (c) 2011 Wesley Nolte
 * Dual licensed under the MIT and GPL licenses.
 *
 */
(function($) {

    $.fn.jOrgChart = function(options) {
        var opts = $.extend({}, $.fn.jOrgChart.defaults, options);
        var $appendTo = $(opts.chartElement);

        // build the tree
        $this = $(this);
        var $container = $("<div class='" + opts.chartClass + "'/>");
        if($this.is("ul")) {
            buildNode($this.find("li:first"), $container, 0, opts);
        }
        else if($this.is("li")) {
            buildNode($this, $container, 0, opts);
        }
        $appendTo.append($container);

        // add drag and drop if enabled
        if(opts.dragAndDrop){
            $('div.node').draggable({
                cursor      : 'move',
                distance    : 40,
                helper      : 'clone',
                opacity     : 0.8,
                revert      : 'invalid',
                revertDuration : 100,
                snap        : 'div.node.expanded',
                snapMode    : 'inner',
                stack       : 'div.node'
            });

            $('div.node').droppable({
                accept      : '.node',
                activeClass : 'drag-active',
                hoverClass  : 'drop-hover'
            });

            // Drag start event handler for nodes
            $('div.node').bind("dragstart", function handleDragStart( event, ui ){

                var sourceNode = $(this);
                sourceNode.parentsUntil('.node-container')
                    .find('*')
                    .filter('.node')
                    .droppable('disable');
            });

            // Drag stop event handler for nodes
            $('div.node').bind("dragstop", function handleDragStop( event, ui ){

                /* reload the plugin */
                $(opts.chartElement).children().remove();
                $this.jOrgChart(opts);
            });

            // Drop event handler for nodes
            $('div.node').bind("drop", function handleDropEvent( event, ui ) {

                var targetID = $(this).data("tree-node");
                var targetLi = $this.find("li").filter(function() { return $(this).data("tree-node") === targetID; } );
                var targetUl = targetLi.children('ul');

                var sourceID = ui.draggable.data("tree-node");
                var sourceLi = $this.find("li").filter(function() { return $(this).data("tree-node") === sourceID; } );
                var sourceUl = sourceLi.parent('ul');

                if (targetUl.length > 0){
                    targetUl.append(sourceLi);
                } else {
                    targetLi.append("<ul></ul>");
                    targetLi.children('ul').append(sourceLi);
                }

                //Removes any empty lists
                if (sourceUl.children().length === 0){
                    sourceUl.remove();
                }

            }); // handleDropEvent

        } // Drag and drop
    };

    // Option defaults
    $.fn.jOrgChart.defaults = {
        chartElement : 'body',
        depth      : -1,
        chartClass : "jOrgChart",
        dragAndDrop: false
    };

    var nodeCount = 0;
    // Method that recursively builds the tree
    function buildNode($node, $appendTo, level, opts) {
        var $table = $("<table cellpadding='0' cellspacing='0' border='0'/>");
        var $tbody = $("<tbody/>");

        // Construct the node container(s)
        var $nodeRow = $("<tr/>").addClass("node-cells");
        var $nodeCell = $("<td/>").addClass("node-cell").attr("colspan", 2);
        var $childNodes = $node.children("ul:first").children("li");
        var $nodeDiv;

        if($childNodes.length > 1) {
            $nodeCell.attr("colspan", $childNodes.length * 2);
        }
        // Draw the node
        // Get the contents - any markup except li and ul allowed
        var $nodeContent = $node.clone()
            .children("ul,li")
            .remove()
            .end()
            .html();

        //Increaments the node count which is used to link the source list and the org chart
        nodeCount++;
        $node.data("tree-node", nodeCount);
        $nodeDiv = $("<div>").addClass("node")
            .data("tree-node", nodeCount)
            .append($nodeContent);

        // Expand and contract nodes
        if ($childNodes.length > 0) {
            $nodeDiv.click(function() {
                var $this = $(this);
                var $tr = $this.closest("tr");

                if($tr.hasClass('contracted')){
                    $this.css('cursor','n-resize');
                    $tr.removeClass('contracted').addClass('expanded');
                    $tr.nextAll("tr").css('visibility', '');

                    // Update the <li> appropriately so that if the tree redraws collapsed/non-collapsed nodes
                    // maintain their appearance
                    $node.removeClass('collapsed');
                }else{
                    $this.css('cursor','s-resize');
                    $tr.removeClass('expanded').addClass('contracted');
                    $tr.nextAll("tr").css('visibility', 'hidden');

                    $node.addClass('collapsed');
                }
            });
        }

        $nodeCell.append($nodeDiv);
        $nodeRow.append($nodeCell);
        $tbody.append($nodeRow);

        if($childNodes.length > 0) {
            // if it can be expanded then change the cursor
            $nodeDiv.css('cursor','n-resize');

            // recurse until leaves found (-1) or to the level specified
            if(opts.depth == -1 || (level+1 < opts.depth)) {
                var $downLineRow = $("<tr/>");
                var $downLineCell = $("<td/>").attr("colspan", $childNodes.length*2);
                $downLineRow.append($downLineCell);

                // draw the connecting line from the parent node to the horizontal line
                $downLine = $("<div></div>").addClass("line down");
                $downLineCell.append($downLine);
                $tbody.append($downLineRow);

                // Draw the horizontal lines
                var $linesRow = $("<tr/>");
                $childNodes.each(function() {
                    var $left = $("<td>&nbsp;</td>").addClass("line left top");
                    var $right = $("<td>&nbsp;</td>").addClass("line right top");
                    $linesRow.append($left).append($right);
                });

                // horizontal line shouldn't extend beyond the first and last child branches
                $linesRow.find("td:first")
                    .removeClass("top")
                    .end()
                    .find("td:last")
                    .removeClass("top");

                $tbody.append($linesRow);
                var $childNodesRow = $("<tr/>");
                $childNodes.each(function() {
                    var $td = $("<td class='node-container'/>");
                    $td.attr("colspan", 2);
                    // recurse through children lists and items
                    buildNode($(this), $td, level+1, opts);
                    $childNodesRow.append($td);
                });

            }
            $tbody.append($childNodesRow);
        }

        // any classes on the LI element get copied to the relevant node in the tree
        // apart from the special 'collapsed' class, which collapses the sub-tree at this point
        if ($node.attr('class') != undefined) {
            var classList = $node.attr('class').split(/\s+/);
            $.each(classList, function(index,item) {
                if (item == 'collapsed') {
                    console.log($node);
                    $nodeRow.nextAll('tr').css('visibility', 'hidden');
                    $nodeRow.removeClass('expanded');
                    $nodeRow.addClass('contracted');
                    $nodeDiv.css('cursor','s-resize');
                } else {
                    $nodeDiv.addClass(item);
                }
            });
        }

        $table.append($tbody);
        $appendTo.append($table);

        /* Prevent trees collapsing if a link inside a node is clicked */
        $nodeDiv.children('a').click(function(e){
            console.log(e);
            e.stopPropagation();
        });
    };

})(jQuery);
/*!
 * Datepicker for Bootstrap v1.7.0-dev (https://github.com/uxsolutions/bootstrap-datepicker)
 *
 * Licensed under the Apache License v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 */

(function(factory){
    if (typeof define === "function" && define.amd) {
        define(["jquery"], factory);
    } else if (typeof exports === 'object') {
        factory(require('jquery'));
    } else {
        factory(jQuery);
    }
}(function($, undefined){

    function UTCDate(){
        return new Date(Date.UTC.apply(Date, arguments));
    }
    function UTCToday(){
        var today = new Date();
        return UTCDate(today.getFullYear(), today.getMonth(), today.getDate());
    }
    function isUTCEquals(date1, date2) {
        return (
            date1.getUTCFullYear() === date2.getUTCFullYear() &&
            date1.getUTCMonth() === date2.getUTCMonth() &&
            date1.getUTCDate() === date2.getUTCDate()
        );
    }
    function alias(method){
        return function(){
            return this[method].apply(this, arguments);
        };
    }
    function isValidDate(d) {
        return d && !isNaN(d.getTime());
    }

    var DateArray = (function(){
        var extras = {
            get: function(i){
                return this.slice(i)[0];
            },
            contains: function(d){
                // Array.indexOf is not cross-browser;
                // $.inArray doesn't work with Dates
                var val = d && d.valueOf();
                for (var i=0, l=this.length; i < l; i++)
                    // Use date arithmetic to allow dates with different times to match
                    if (0 <= this[i].valueOf() - val && this[i].valueOf() - val < 1000*60*60*24)
                        return i;
                return -1;
            },
            remove: function(i){
                this.splice(i,1);
            },
            replace: function(new_array){
                if (!new_array)
                    return;
                if (!$.isArray(new_array))
                    new_array = [new_array];
                this.clear();
                this.push.apply(this, new_array);
            },
            clear: function(){
                this.length = 0;
            },
            copy: function(){
                var a = new DateArray();
                a.replace(this);
                return a;
            }
        };

        return function(){
            var a = [];
            a.push.apply(a, arguments);
            $.extend(a, extras);
            return a;
        };
    })();


    // Picker object

    var Datepicker = function(element, options){
        $.data(element, 'datepicker', this);
        this._process_options(options);

        this.dates = new DateArray();
        this.viewDate = this.o.defaultViewDate;
        this.focusDate = null;

        this.element = $(element);
        this.isInput = this.element.is('input');
        this.inputField = this.isInput ? this.element : this.element.find('input');
        this.component = this.element.hasClass('date') ? this.element.find('.add-on, .input-group-addon, .btn') : false;
        if (this.component && this.component.length === 0)
            this.component = false;
        this.isInline = !this.component && this.element.is('div');

        this.picker = $(DPGlobal.template);

        // Checking templates and inserting
        if (this._check_template(this.o.templates.leftArrow)) {
            this.picker.find('.prev').html(this.o.templates.leftArrow);
        }

        if (this._check_template(this.o.templates.rightArrow)) {
            this.picker.find('.next').html(this.o.templates.rightArrow);
        }

        this._buildEvents();
        this._attachEvents();

        if (this.isInline){
            this.picker.addClass('datepicker-inline').appendTo(this.element);
        }
        else {
            this.picker.addClass('datepicker-dropdown dropdown-menu');
        }

        if (this.o.rtl){
            this.picker.addClass('datepicker-rtl');
        }

        if (this.o.calendarWeeks) {
            this.picker.find('.datepicker-days .datepicker-switch, thead .datepicker-title, tfoot .today, tfoot .clear')
                .attr('colspan', function(i, val){
                    return Number(val) + 1;
                });
        }

        this._allow_update = false;

        this.setStartDate(this._o.startDate);
        this.setEndDate(this._o.endDate);
        this.setDaysOfWeekDisabled(this.o.daysOfWeekDisabled);
        this.setDaysOfWeekHighlighted(this.o.daysOfWeekHighlighted);
        this.setDatesDisabled(this.o.datesDisabled);

        this.setViewMode(this.o.startView);
        this.fillDow();
        this.fillMonths();

        this._allow_update = true;

        this.update();

        if (this.isInline){
            this.show();
        }
    };

    Datepicker.prototype = {
        constructor: Datepicker,

        _resolveViewName: function(view){
            $.each(DPGlobal.viewModes, function(i, viewMode){
                if (view === i || $.inArray(view, viewMode.names) !== -1){
                    view = i;
                    return false;
                }
            });

            return view;
        },

        _resolveDaysOfWeek: function(daysOfWeek){
            if (!$.isArray(daysOfWeek))
                daysOfWeek = daysOfWeek.split(/[,\s]*/);
            return $.map(daysOfWeek, Number);
        },

        _check_template: function(tmp){
            try {
                // If empty
                if (tmp === undefined || tmp === "") {
                    return false;
                }
                // If no html, everything ok
                if ((tmp.match(/[<>]/g) || []).length <= 0) {
                    return true;
                }
                // Checking if html is fine
                var jDom = $(tmp);
                return jDom.length > 0;
            }
            catch (ex) {
                return false;
            }
        },

        _process_options: function(opts){
            // Store raw options for reference
            this._o = $.extend({}, this._o, opts);
            // Processed options
            var o = this.o = $.extend({}, this._o);

            // Check if "de-DE" style date is available, if not language should
            // fallback to 2 letter code eg "de"
            var lang = o.language;
            if (!dates[lang]){
                lang = lang.split('-')[0];
                if (!dates[lang])
                    lang = defaults.language;
            }
            o.language = lang;

            // Retrieve view index from any aliases
            o.startView = this._resolveViewName(o.startView);
            o.minViewMode = this._resolveViewName(o.minViewMode);
            o.maxViewMode = this._resolveViewName(o.maxViewMode);

            // Check view is between min and max
            o.startView = Math.max(this.o.minViewMode, Math.min(this.o.maxViewMode, o.startView));

            // true, false, or Number > 0
            if (o.multidate !== true){
                o.multidate = Number(o.multidate) || false;
                if (o.multidate !== false)
                    o.multidate = Math.max(0, o.multidate);
            }
            o.multidateSeparator = String(o.multidateSeparator);

            o.weekStart %= 7;
            o.weekEnd = (o.weekStart + 6) % 7;

            var format = DPGlobal.parseFormat(o.format);
            if (o.startDate !== -Infinity){
                if (!!o.startDate){
                    if (o.startDate instanceof Date)
                        o.startDate = this._local_to_utc(this._zero_time(o.startDate));
                    else
                        o.startDate = DPGlobal.parseDate(o.startDate, format, o.language, o.assumeNearbyYear);
                }
                else {
                    o.startDate = -Infinity;
                }
            }
            if (o.endDate !== Infinity){
                if (!!o.endDate){
                    if (o.endDate instanceof Date)
                        o.endDate = this._local_to_utc(this._zero_time(o.endDate));
                    else
                        o.endDate = DPGlobal.parseDate(o.endDate, format, o.language, o.assumeNearbyYear);
                }
                else {
                    o.endDate = Infinity;
                }
            }

            o.daysOfWeekDisabled = this._resolveDaysOfWeek(o.daysOfWeekDisabled||[]);
            o.daysOfWeekHighlighted = this._resolveDaysOfWeek(o.daysOfWeekHighlighted||[]);

            o.datesDisabled = o.datesDisabled||[];
            if (!$.isArray(o.datesDisabled)) {
                o.datesDisabled = o.datesDisabled.split(',');
            }
            o.datesDisabled = $.map(o.datesDisabled, function(d){
                return DPGlobal.parseDate(d, format, o.language, o.assumeNearbyYear);
            });

            var plc = String(o.orientation).toLowerCase().split(/\s+/g),
                _plc = o.orientation.toLowerCase();
            plc = $.grep(plc, function(word){
                return /^auto|left|right|top|bottom$/.test(word);
            });
            o.orientation = {x: 'auto', y: 'auto'};
            if (!_plc || _plc === 'auto')
                ; // no action
            else if (plc.length === 1){
                switch (plc[0]){
                    case 'top':
                    case 'bottom':
                        o.orientation.y = plc[0];
                        break;
                    case 'left':
                    case 'right':
                        o.orientation.x = plc[0];
                        break;
                }
            }
            else {
                _plc = $.grep(plc, function(word){
                    return /^left|right$/.test(word);
                });
                o.orientation.x = _plc[0] || 'auto';

                _plc = $.grep(plc, function(word){
                    return /^top|bottom$/.test(word);
                });
                o.orientation.y = _plc[0] || 'auto';
            }
            if (o.defaultViewDate) {
                var year = o.defaultViewDate.year || new Date().getFullYear();
                var month = o.defaultViewDate.month || 0;
                var day = o.defaultViewDate.day || 1;
                o.defaultViewDate = UTCDate(year, month, day);
            } else {
                o.defaultViewDate = UTCToday();
            }
        },
        _events: [],
        _secondaryEvents: [],
        _applyEvents: function(evs){
            for (var i=0, el, ch, ev; i < evs.length; i++){
                el = evs[i][0];
                if (evs[i].length === 2){
                    ch = undefined;
                    ev = evs[i][1];
                } else if (evs[i].length === 3){
                    ch = evs[i][1];
                    ev = evs[i][2];
                }
                el.on(ev, ch);
            }
        },
        _unapplyEvents: function(evs){
            for (var i=0, el, ev, ch; i < evs.length; i++){
                el = evs[i][0];
                if (evs[i].length === 2){
                    ch = undefined;
                    ev = evs[i][1];
                } else if (evs[i].length === 3){
                    ch = evs[i][1];
                    ev = evs[i][2];
                }
                el.off(ev, ch);
            }
        },
        _buildEvents: function(){
            var events = {
                keyup: $.proxy(function(e){
                    if ($.inArray(e.keyCode, [27, 37, 39, 38, 40, 32, 13, 9]) === -1)
                        this.update();
                }, this),
                keydown: $.proxy(this.keydown, this),
                paste: $.proxy(this.paste, this)
            };

            if (this.o.showOnFocus === true) {
                events.focus = $.proxy(this.show, this);
            }

            if (this.isInput) { // single input
                this._events = [
                    [this.element, events]
                ];
            }
            // component: input + button
            else if (this.component && this.inputField.length) {
                this._events = [
                    // For components that are not readonly, allow keyboard nav
                    [this.inputField, events],
                    [this.component, {
                        click: $.proxy(this.show, this)
                    }]
                ];
            }
            else {
                this._events = [
                    [this.element, {
                        click: $.proxy(this.show, this),
                        keydown: $.proxy(this.keydown, this)
                    }]
                ];
            }
            this._events.push(
                // Component: listen for blur on element descendants
                [this.element, '*', {
                    blur: $.proxy(function(e){
                        this._focused_from = e.target;
                    }, this)
                }],
                // Input: listen for blur on element
                [this.element, {
                    blur: $.proxy(function(e){
                        this._focused_from = e.target;
                    }, this)
                }]
            );

            if (this.o.immediateUpdates) {
                // Trigger input updates immediately on changed year/month
                this._events.push([this.element, {
                    'changeYear changeMonth': $.proxy(function(e){
                        this.update(e.date);
                    }, this)
                }]);
            }

            this._secondaryEvents = [
                [this.picker, {
                    click: $.proxy(this.click, this)
                }],
                [this.picker, '.prev, .next', {
                    click: $.proxy(this.navArrowsClick, this)
                }],
                [$(window), {
                    resize: $.proxy(this.place, this)
                }],
                [$(document), {
                    'mousedown touchstart': $.proxy(function(e){
                        // Clicked outside the datepicker, hide it
                        if (!(
                            this.element.is(e.target) ||
                            this.element.find(e.target).length ||
                            this.picker.is(e.target) ||
                            this.picker.find(e.target).length ||
                            this.isInline
                        )){
                            this.hide();
                        }
                    }, this)
                }]
            ];
        },
        _attachEvents: function(){
            this._detachEvents();
            this._applyEvents(this._events);
        },
        _detachEvents: function(){
            this._unapplyEvents(this._events);
        },
        _attachSecondaryEvents: function(){
            this._detachSecondaryEvents();
            this._applyEvents(this._secondaryEvents);
        },
        _detachSecondaryEvents: function(){
            this._unapplyEvents(this._secondaryEvents);
        },
        _trigger: function(event, altdate){
            var date = altdate || this.dates.get(-1),
                local_date = this._utc_to_local(date);

            this.element.trigger({
                type: event,
                date: local_date,
                viewMode: this.viewMode,
                dates: $.map(this.dates, this._utc_to_local),
                format: $.proxy(function(ix, format){
                    if (arguments.length === 0){
                        ix = this.dates.length - 1;
                        format = this.o.format;
                    } else if (typeof ix === 'string'){
                        format = ix;
                        ix = this.dates.length - 1;
                    }
                    format = format || this.o.format;
                    var date = this.dates.get(ix);
                    return DPGlobal.formatDate(date, format, this.o.language);
                }, this)
            });
        },

        show: function(){
            if (this.inputField.prop('disabled') || (this.inputField.prop('readonly') && this.o.enableOnReadonly === false))
                return;
            if (!this.isInline)
                this.picker.appendTo(this.o.container);
            this.place();
            this.picker.show();
            this._attachSecondaryEvents();
            this._trigger('show');
            if ((window.navigator.msMaxTouchPoints || 'ontouchstart' in document) && this.o.disableTouchKeyboard) {
                $(this.element).blur();
            }
            return this;
        },

        hide: function(){
            if (this.isInline || !this.picker.is(':visible'))
                return this;
            this.focusDate = null;
            this.picker.hide().detach();
            this._detachSecondaryEvents();
            this.setViewMode(this.o.startView);

            if (this.o.forceParse && this.inputField.val())
                this.setValue();
            this._trigger('hide');
            return this;
        },

        destroy: function(){
            this.hide();
            this._detachEvents();
            this._detachSecondaryEvents();
            this.picker.remove();
            delete this.element.data().datepicker;
            if (!this.isInput){
                delete this.element.data().date;
            }
            return this;
        },

        paste: function(e){
            var dateString;
            if (e.originalEvent.clipboardData && e.originalEvent.clipboardData.types
                && $.inArray('text/plain', e.originalEvent.clipboardData.types) !== -1) {
                dateString = e.originalEvent.clipboardData.getData('text/plain');
            } else if (window.clipboardData) {
                dateString = window.clipboardData.getData('Text');
            } else {
                return;
            }
            this.setDate(dateString);
            this.update();
            e.preventDefault();
        },

        _utc_to_local: function(utc){
            if (!utc) {
                return utc;
            }

            var local = new Date(utc.getTime() + (utc.getTimezoneOffset() * 60000));

            if (local.getTimezoneOffset() !== utc.getTimezoneOffset()) {
                local = new Date(utc.getTime() + (local.getTimezoneOffset() * 60000));
            }

            return local;
        },
        _local_to_utc: function(local){
            return local && new Date(local.getTime() - (local.getTimezoneOffset()*60000));
        },
        _zero_time: function(local){
            return local && new Date(local.getFullYear(), local.getMonth(), local.getDate());
        },
        _zero_utc_time: function(utc){
            return utc && UTCDate(utc.getUTCFullYear(), utc.getUTCMonth(), utc.getUTCDate());
        },

        getDates: function(){
            return $.map(this.dates, this._utc_to_local);
        },

        getUTCDates: function(){
            return $.map(this.dates, function(d){
                return new Date(d);
            });
        },

        getDate: function(){
            return this._utc_to_local(this.getUTCDate());
        },

        getUTCDate: function(){
            var selected_date = this.dates.get(-1);
            if (selected_date !== undefined) {
                return new Date(selected_date);
            } else {
                return null;
            }
        },

        clearDates: function(){
            this.inputField.val('');
            this.update();
            this._trigger('changeDate');

            if (this.o.autoclose) {
                this.hide();
            }
        },

        setDates: function(){
            var args = $.isArray(arguments[0]) ? arguments[0] : arguments;
            this.update.apply(this, args);
            this._trigger('changeDate');
            this.setValue();
            return this;
        },

        setUTCDates: function(){
            var args = $.isArray(arguments[0]) ? arguments[0] : arguments;
            this.setDates.apply(this, $.map(args, this._utc_to_local));
            return this;
        },

        setDate: alias('setDates'),
        setUTCDate: alias('setUTCDates'),
        remove: alias('destroy'),

        setValue: function(){
            var formatted = this.getFormattedDate();
            this.inputField.val(formatted);
            return this;
        },

        getFormattedDate: function(format){
            if (format === undefined)
                format = this.o.format;

            var lang = this.o.language;
            return $.map(this.dates, function(d){
                return DPGlobal.formatDate(d, format, lang);
            }).join(this.o.multidateSeparator);
        },

        getStartDate: function(){
            return this.o.startDate;
        },

        setStartDate: function(startDate){
            this._process_options({startDate: startDate});
            this.update();
            this.updateNavArrows();
            return this;
        },

        getEndDate: function(){
            return this.o.endDate;
        },

        setEndDate: function(endDate){
            this._process_options({endDate: endDate});
            this.update();
            this.updateNavArrows();
            return this;
        },

        setDaysOfWeekDisabled: function(daysOfWeekDisabled){
            this._process_options({daysOfWeekDisabled: daysOfWeekDisabled});
            this.update();
            return this;
        },

        setDaysOfWeekHighlighted: function(daysOfWeekHighlighted){
            this._process_options({daysOfWeekHighlighted: daysOfWeekHighlighted});
            this.update();
            return this;
        },

        setDatesDisabled: function(datesDisabled){
            this._process_options({datesDisabled: datesDisabled});
            this.update();
            return this;
        },

        place: function(){
            if (this.isInline)
                return this;
            var calendarWidth = this.picker.outerWidth(),
                calendarHeight = this.picker.outerHeight(),
                visualPadding = 10,
                container = $(this.o.container),
                windowWidth = container.width(),
                scrollTop = this.o.container === 'body' ? $(document).scrollTop() : container.scrollTop(),
                appendOffset = container.offset();

            var parentsZindex = [];
            this.element.parents().each(function(){
                var itemZIndex = $(this).css('z-index');
                if (itemZIndex !== 'auto' && itemZIndex !== 0) parentsZindex.push(parseInt(itemZIndex));
            });
            var zIndex = Math.max.apply(Math, parentsZindex) + this.o.zIndexOffset;
            var offset = this.component ? this.component.parent().offset() : this.element.offset();
            var height = this.component ? this.component.outerHeight(true) : this.element.outerHeight(false);
            var width = this.component ? this.component.outerWidth(true) : this.element.outerWidth(false);
            var left = offset.left - appendOffset.left,
                top = offset.top - appendOffset.top;

            if (this.o.container !== 'body') {
                top += scrollTop;
            }

            this.picker.removeClass(
                'datepicker-orient-top datepicker-orient-bottom '+
                'datepicker-orient-right datepicker-orient-left'
            );

            if (this.o.orientation.x !== 'auto'){
                this.picker.addClass('datepicker-orient-' + this.o.orientation.x);
                if (this.o.orientation.x === 'right')
                    left -= calendarWidth - width;
            }
            // auto x orientation is best-placement: if it crosses a window
            // edge, fudge it sideways
            else {
                if (offset.left < 0) {
                    // component is outside the window on the left side. Move it into visible range
                    this.picker.addClass('datepicker-orient-left');
                    left -= offset.left - visualPadding;
                } else if (left + calendarWidth > windowWidth) {
                    // the calendar passes the widow right edge. Align it to component right side
                    this.picker.addClass('datepicker-orient-right');
                    left += width - calendarWidth;
                } else {
                    if (this.o.rtl) {
                        // Default to right
                        this.picker.addClass('datepicker-orient-right');
                    } else {
                        // Default to left
                        this.picker.addClass('datepicker-orient-left');
                    }
                }
            }

            // auto y orientation is best-situation: top or bottom, no fudging,
            // decision based on which shows more of the calendar
            var yorient = this.o.orientation.y,
                top_overflow;
            if (yorient === 'auto'){
                top_overflow = -scrollTop + top - calendarHeight;
                yorient = top_overflow < 0 ? 'bottom' : 'top';
            }

            this.picker.addClass('datepicker-orient-' + yorient);
            if (yorient === 'top')
                top -= calendarHeight + parseInt(this.picker.css('padding-top'));
            else
                top += height;

            if (this.o.rtl) {
                var right = windowWidth - (left + width);
                this.picker.css({
                    top: top,
                    right: right,
                    zIndex: zIndex
                });
            } else {
                this.picker.css({
                    top: top,
                    left: left,
                    zIndex: zIndex
                });
            }
            return this;
        },

        _allow_update: true,
        update: function(){
            if (!this._allow_update)
                return this;

            var oldDates = this.dates.copy(),
                dates = [],
                fromArgs = false;
            if (arguments.length){
                $.each(arguments, $.proxy(function(i, date){
                    if (date instanceof Date)
                        date = this._local_to_utc(date);
                    dates.push(date);
                }, this));
                fromArgs = true;
            } else {
                dates = this.isInput
                    ? this.element.val()
                    : this.element.data('date') || this.inputField.val();
                if (dates && this.o.multidate)
                    dates = dates.split(this.o.multidateSeparator);
                else
                    dates = [dates];
                delete this.element.data().date;
            }

            dates = $.map(dates, $.proxy(function(date){
                return DPGlobal.parseDate(date, this.o.format, this.o.language, this.o.assumeNearbyYear);
            }, this));
            dates = $.grep(dates, $.proxy(function(date){
                return (
                    !this.dateWithinRange(date) ||
                    !date
                );
            }, this), true);
            this.dates.replace(dates);

            if (this.o.updateViewDate) {
                if (this.dates.length)
                    this.viewDate = new Date(this.dates.get(-1));
                else if (this.viewDate < this.o.startDate)
                    this.viewDate = new Date(this.o.startDate);
                else if (this.viewDate > this.o.endDate)
                    this.viewDate = new Date(this.o.endDate);
                else
                    this.viewDate = this.o.defaultViewDate;
            }

            if (fromArgs){
                // setting date by clicking
                this.setValue();
                this.element.change();
            }
            else if (this.dates.length){
                // setting date by typing
                if (String(oldDates) !== String(this.dates) && fromArgs) {
                    this._trigger('changeDate');
                    this.element.change();
                }
            }
            if (!this.dates.length && oldDates.length) {
                this._trigger('clearDate');
                this.element.change();
            }

            this.fill();
            return this;
        },

        fillDow: function(){
            var dowCnt = this.o.weekStart,
                html = '<tr>';
            if (this.o.calendarWeeks){
                html += '<th class="cw">&#160;</th>';
            }
            while (dowCnt < this.o.weekStart + 7){
                html += '<th class="dow';
                if ($.inArray(dowCnt, this.o.daysOfWeekDisabled) !== -1)
                    html += ' disabled';
                html += '">'+dates[this.o.language].daysMin[(dowCnt++)%7]+'</th>';
            }
            html += '</tr>';
            this.picker.find('.datepicker-days thead').append(html);
        },

        fillMonths: function(){
            var localDate = this._utc_to_local(this.viewDate);
            var html = '',
                i = 0;
            while (i < 12){
                var focused = localDate && localDate.getMonth() === i ? ' focused' : '';
                html += '<span class="month' + focused + '">' + dates[this.o.language].monthsShort[i++]+'</span>';
            }
            this.picker.find('.datepicker-months td').html(html);
        },

        setRange: function(range){
            if (!range || !range.length)
                delete this.range;
            else
                this.range = $.map(range, function(d){
                    return d.valueOf();
                });
            this.fill();
        },

        getClassNames: function(date){
            var cls = [],
                year = this.viewDate.getUTCFullYear(),
                month = this.viewDate.getUTCMonth(),
                today = UTCToday();
            if (date.getUTCFullYear() < year || (date.getUTCFullYear() === year && date.getUTCMonth() < month)){
                cls.push('old');
            } else if (date.getUTCFullYear() > year || (date.getUTCFullYear() === year && date.getUTCMonth() > month)){
                cls.push('new');
            }
            if (this.focusDate && date.valueOf() === this.focusDate.valueOf())
                cls.push('focused');
            // Compare internal UTC date with UTC today, not local today
            if (this.o.todayHighlight && isUTCEquals(date, today)) {
                cls.push('today');
            }
            if (this.dates.contains(date) !== -1)
                cls.push('active');
            if (!this.dateWithinRange(date)){
                cls.push('disabled');
            }
            if (this.dateIsDisabled(date)){
                cls.push('disabled', 'disabled-date');
            }
            if ($.inArray(date.getUTCDay(), this.o.daysOfWeekHighlighted) !== -1){
                cls.push('highlighted');
            }

            if (this.range){
                if (date > this.range[0] && date < this.range[this.range.length-1]){
                    cls.push('range');
                }
                if ($.inArray(date.valueOf(), this.range) !== -1){
                    cls.push('selected');
                }
                if (date.valueOf() === this.range[0]){
                    cls.push('range-start');
                }
                if (date.valueOf() === this.range[this.range.length-1]){
                    cls.push('range-end');
                }
            }
            return cls;
        },

        _fill_yearsView: function(selector, cssClass, factor, step, currentYear, startYear, endYear, callback){
            var html, view, year, steps, startStep, endStep, thisYear, i, classes, tooltip, before;

            html      = '';
            view      = this.picker.find(selector);
            year      = parseInt(currentYear / factor, 10) * factor;
            startStep = parseInt(startYear / step, 10) * step;
            endStep   = parseInt(endYear / step, 10) * step;
            steps     = $.map(this.dates, function(d){
                return parseInt(d.getUTCFullYear() / step, 10) * step;
            });

            view.find('.datepicker-switch').text(year + '-' + (year + step * 9));

            thisYear = year - step;
            for (i = -1; i < 11; i += 1) {
                classes = [cssClass];
                tooltip = null;

                if (i === -1) {
                    classes.push('old');
                } else if (i === 10) {
                    classes.push('new');
                }
                if ($.inArray(thisYear, steps) !== -1) {
                    classes.push('active');
                }
                if (thisYear < startStep || thisYear > endStep) {
                    classes.push('disabled');
                }
                if (thisYear === this.viewDate.getFullYear()) {
                    classes.push('focused');
                }

                if (callback !== $.noop) {
                    before = callback(new Date(thisYear, 0, 1));
                    if (before === undefined) {
                        before = {};
                    } else if (typeof before === 'boolean') {
                        before = {enabled: before};
                    } else if (typeof before === 'string') {
                        before = {classes: before};
                    }
                    if (before.enabled === false) {
                        classes.push('disabled');
                    }
                    if (before.classes) {
                        classes = classes.concat(before.classes.split(/\s+/));
                    }
                    if (before.tooltip) {
                        tooltip = before.tooltip;
                    }
                }

                html += '<span class="' + classes.join(' ') + '"' + (tooltip ? ' title="' + tooltip + '"' : '') + '>' + thisYear + '</span>';
                thisYear += step;
            }
            view.find('td').html(html);
        },

        fill: function(){
            var d = new Date(this.viewDate),
                year = d.getUTCFullYear(),
                month = d.getUTCMonth(),
                startYear = this.o.startDate !== -Infinity ? this.o.startDate.getUTCFullYear() : -Infinity,
                startMonth = this.o.startDate !== -Infinity ? this.o.startDate.getUTCMonth() : -Infinity,
                endYear = this.o.endDate !== Infinity ? this.o.endDate.getUTCFullYear() : Infinity,
                endMonth = this.o.endDate !== Infinity ? this.o.endDate.getUTCMonth() : Infinity,
                todaytxt = dates[this.o.language].today || dates['en'].today || '',
                cleartxt = dates[this.o.language].clear || dates['en'].clear || '',
                titleFormat = dates[this.o.language].titleFormat || dates['en'].titleFormat,
                tooltip,
                before;
            if (isNaN(year) || isNaN(month))
                return;
            this.picker.find('.datepicker-days .datepicker-switch')
                .text(DPGlobal.formatDate(d, titleFormat, this.o.language));
            this.picker.find('tfoot .today')
                .text(todaytxt)
                .toggle(this.o.todayBtn !== false);
            this.picker.find('tfoot .clear')
                .text(cleartxt)
                .toggle(this.o.clearBtn !== false);
            this.picker.find('thead .datepicker-title')
                .text(this.o.title)
                .toggle(this.o.title !== '');
            this.updateNavArrows();
            this.fillMonths();
            var prevMonth = UTCDate(year, month, 0),
                day = prevMonth.getUTCDate();
            prevMonth.setUTCDate(day - (prevMonth.getUTCDay() - this.o.weekStart + 7)%7);
            var nextMonth = new Date(prevMonth);
            if (prevMonth.getUTCFullYear() < 100){
                nextMonth.setUTCFullYear(prevMonth.getUTCFullYear());
            }
            nextMonth.setUTCDate(nextMonth.getUTCDate() + 42);
            nextMonth = nextMonth.valueOf();
            var html = [];
            var weekDay, clsName;
            while (prevMonth.valueOf() < nextMonth){
                weekDay = prevMonth.getUTCDay();
                if (weekDay === this.o.weekStart){
                    html.push('<tr>');
                    if (this.o.calendarWeeks){
                        // ISO 8601: First week contains first thursday.
                        // ISO also states week starts on Monday, but we can be more abstract here.
                        var
                            // Start of current week: based on weekstart/current date
                            ws = new Date(+prevMonth + (this.o.weekStart - weekDay - 7) % 7 * 864e5),
                            // Thursday of this week
                            th = new Date(Number(ws) + (7 + 4 - ws.getUTCDay()) % 7 * 864e5),
                            // First Thursday of year, year from thursday
                            yth = new Date(Number(yth = UTCDate(th.getUTCFullYear(), 0, 1)) + (7 + 4 - yth.getUTCDay()) % 7 * 864e5),
                            // Calendar week: ms between thursdays, div ms per day, div 7 days
                            calWeek = (th - yth) / 864e5 / 7 + 1;
                        html.push('<td class="cw">'+ calWeek +'</td>');
                    }
                }
                clsName = this.getClassNames(prevMonth);
                clsName.push('day');

                if (this.o.beforeShowDay !== $.noop){
                    before = this.o.beforeShowDay(this._utc_to_local(prevMonth));
                    if (before === undefined)
                        before = {};
                    else if (typeof before === 'boolean')
                        before = {enabled: before};
                    else if (typeof before === 'string')
                        before = {classes: before};
                    if (before.enabled === false)
                        clsName.push('disabled');
                    if (before.classes)
                        clsName = clsName.concat(before.classes.split(/\s+/));
                    if (before.tooltip)
                        tooltip = before.tooltip;
                }

                //Check if uniqueSort exists (supported by jquery >=1.12 and >=2.2)
                //Fallback to unique function for older jquery versions
                if ($.isFunction($.uniqueSort)) {
                    clsName = $.uniqueSort(clsName);
                } else {
                    clsName = $.unique(clsName);
                }

                html.push('<td class="'+clsName.join(' ')+'"' + (tooltip ? ' title="'+tooltip+'"' : '') + (this.o.dateCells ? ' data-date="'+(prevMonth.getTime().toString())+'"' : '') + '>'+prevMonth.getUTCDate() + '</td>');
                tooltip = null;
                if (weekDay === this.o.weekEnd){
                    html.push('</tr>');
                }
                prevMonth.setUTCDate(prevMonth.getUTCDate() + 1);
            }
            this.picker.find('.datepicker-days tbody').html(html.join(''));

            var monthsTitle = dates[this.o.language].monthsTitle || dates['en'].monthsTitle || 'Months';
            var months = this.picker.find('.datepicker-months')
                .find('.datepicker-switch')
                .text(this.o.maxViewMode < 2 ? monthsTitle : year)
                .end()
                .find('tbody span').removeClass('active');

            $.each(this.dates, function(i, d){
                if (d.getUTCFullYear() === year)
                    months.eq(d.getUTCMonth()).addClass('active');
            });

            if (year < startYear || year > endYear){
                months.addClass('disabled');
            }
            if (year === startYear){
                months.slice(0, startMonth).addClass('disabled');
            }
            if (year === endYear){
                months.slice(endMonth+1).addClass('disabled');
            }

            if (this.o.beforeShowMonth !== $.noop){
                var that = this;
                $.each(months, function(i, month){
                    var moDate = new Date(year, i, 1);
                    var before = that.o.beforeShowMonth(moDate);
                    if (before === undefined)
                        before = {};
                    else if (typeof before === 'boolean')
                        before = {enabled: before};
                    else if (typeof before === 'string')
                        before = {classes: before};
                    if (before.enabled === false && !$(month).hasClass('disabled'))
                        $(month).addClass('disabled');
                    if (before.classes)
                        $(month).addClass(before.classes);
                    if (before.tooltip)
                        $(month).prop('title', before.tooltip);
                });
            }

            // Generating decade/years picker
            this._fill_yearsView(
                '.datepicker-years',
                'year',
                10,
                1,
                year,
                startYear,
                endYear,
                this.o.beforeShowYear
            );

            // Generating century/decades picker
            this._fill_yearsView(
                '.datepicker-decades',
                'decade',
                100,
                10,
                year,
                startYear,
                endYear,
                this.o.beforeShowDecade
            );

            // Generating millennium/centuries picker
            this._fill_yearsView(
                '.datepicker-centuries',
                'century',
                1000,
                100,
                year,
                startYear,
                endYear,
                this.o.beforeShowCentury
            );
        },

        updateNavArrows: function(){
            if (!this._allow_update)
                return;

            var d = new Date(this.viewDate),
                year = d.getUTCFullYear(),
                month = d.getUTCMonth(),
                prevState,
                nextState;
            switch (this.viewMode){
                case 0:
                    prevState = (
                        this.o.startDate !== -Infinity &&
                        year <= this.o.startDate.getUTCFullYear() &&
                        month <= this.o.startDate.getUTCMonth()
                    );

                    nextState = (
                        this.o.endDate !== Infinity &&
                        year >= this.o.endDate.getUTCFullYear() &&
                        month >= this.o.endDate.getUTCMonth()
                    );
                    break;
                case 1:
                case 2:
                case 3:
                case 4:
                    prevState = (
                        this.o.startDate !== -Infinity &&
                        year <= this.o.startDate.getUTCFullYear()
                    );

                    nextState = (
                        this.o.endDate !== Infinity &&
                        year >= this.o.endDate.getUTCFullYear()
                    );
                    break;
            }

            this.picker.find('.prev').toggleClass('disabled', prevState);
            this.picker.find('.next').toggleClass('disabled', nextState);
        },

        click: function(e){
            e.preventDefault();
            e.stopPropagation();

            var target, dir, day, year, month;
            target = $(e.target);

            // Clicked on the switch
            if (target.hasClass('datepicker-switch') && this.viewMode !== this.o.maxViewMode){
                this.setViewMode(this.viewMode + 1);
            }

            // Clicked on today button
            if (target.hasClass('today') && !target.hasClass('day')){
                this.setViewMode(0);
                this._setDate(UTCToday(), this.o.todayBtn === 'linked' ? null : 'view');
            }

            // Clicked on clear button
            if (target.hasClass('clear')){
                this.clearDates();
            }

            if (!target.hasClass('disabled')){
                // Clicked on a day
                if (target.hasClass('day')){
                    day = Number(target.text());
                    year = this.viewDate.getUTCFullYear();
                    month = this.viewDate.getUTCMonth();

                    if (target.hasClass('old') || target.hasClass('new')){
                        dir = target.hasClass('old') ? -1 : 1;
                        month = (month + dir + 12) % 12;
                        if ((dir === -1 && month === 11) || (dir === 1 && month === 0)) {
                            year += dir;
                            if (this.o.updateViewDate) {
                                this._trigger('changeYear', this.viewDate);
                            }
                        }
                        if (this.o.updateViewDate) {
                            this._trigger('changeMonth', this.viewDate);
                        }
                    }
                    this._setDate(UTCDate(year, month, day));
                }

                // Clicked on a month, year, decade, century
                if (target.hasClass('month')
                    || target.hasClass('year')
                    || target.hasClass('decade')
                    || target.hasClass('century')) {
                    this.viewDate.setUTCDate(1);

                    day = 1;
                    if (this.viewMode === 1){
                        month = target.parent().find('span').index(target);
                        year = this.viewDate.getUTCFullYear();
                        this.viewDate.setUTCMonth(month);
                    } else {
                        month = 0;
                        year = Number(target.text());
                        this.viewDate.setUTCFullYear(year);
                    }

                    this._trigger(DPGlobal.viewModes[this.viewMode - 1].e, this.viewDate);

                    if (this.viewMode === this.o.minViewMode){
                        this._setDate(UTCDate(year, month, day));
                    } else {
                        this.setViewMode(this.viewMode - 1);
                        this.fill();
                    }
                }
            }

            if (this.picker.is(':visible') && this._focused_from){
                this._focused_from.focus();
            }
            delete this._focused_from;
        },

        // Clicked on prev or next
        navArrowsClick: function(e){
            var target = $(e.target);
            var dir = target.hasClass('prev') ? -1 : 1;
            if (this.viewMode !== 0){
                dir *= DPGlobal.viewModes[this.viewMode].navStep * 12;
            }
            this.viewDate = this.moveMonth(this.viewDate, dir);
            this._trigger(DPGlobal.viewModes[this.viewMode].e, this.viewDate);
            this.fill();
        },

        _toggle_multidate: function(date){
            var ix = this.dates.contains(date);
            if (!date){
                this.dates.clear();
            }

            if (ix !== -1){
                if (this.o.multidate === true || this.o.multidate > 1 || this.o.toggleActive){
                    this.dates.remove(ix);
                }
            } else if (this.o.multidate === false) {
                this.dates.clear();
                this.dates.push(date);
            }
            else {
                this.dates.push(date);
            }

            if (typeof this.o.multidate === 'number')
                while (this.dates.length > this.o.multidate)
                    this.dates.remove(0);
        },

        _setDate: function(date, which){
            if (!which || which === 'date')
                this._toggle_multidate(date && new Date(date));
            if ((!which && this.o.updateViewDate) || which === 'view')
                this.viewDate = date && new Date(date);

            this.fill();
            this.setValue();
            if (!which || which !== 'view') {
                this._trigger('changeDate');
            }
            this.inputField.trigger('change');
            if (this.o.autoclose && (!which || which === 'date')){
                this.hide();
            }
        },

        moveDay: function(date, dir){
            var newDate = new Date(date);
            newDate.setUTCDate(date.getUTCDate() + dir);

            return newDate;
        },

        moveWeek: function(date, dir){
            return this.moveDay(date, dir * 7);
        },

        moveMonth: function(date, dir){
            if (!isValidDate(date))
                return this.o.defaultViewDate;
            if (!dir)
                return date;
            var new_date = new Date(date.valueOf()),
                day = new_date.getUTCDate(),
                month = new_date.getUTCMonth(),
                mag = Math.abs(dir),
                new_month, test;
            dir = dir > 0 ? 1 : -1;
            if (mag === 1){
                test = dir === -1
                    // If going back one month, make sure month is not current month
                    // (eg, Mar 31 -> Feb 31 == Feb 28, not Mar 02)
                    ? function(){
                        return new_date.getUTCMonth() === month;
                    }
                    // If going forward one month, make sure month is as expected
                    // (eg, Jan 31 -> Feb 31 == Feb 28, not Mar 02)
                    : function(){
                        return new_date.getUTCMonth() !== new_month;
                    };
                new_month = month + dir;
                new_date.setUTCMonth(new_month);
                // Dec -> Jan (12) or Jan -> Dec (-1) -- limit expected date to 0-11
                new_month = (new_month + 12) % 12;
            }
            else {
                // For magnitudes >1, move one month at a time...
                for (var i=0; i < mag; i++)
                    // ...which might decrease the day (eg, Jan 31 to Feb 28, etc)...
                    new_date = this.moveMonth(new_date, dir);
                // ...then reset the day, keeping it in the new month
                new_month = new_date.getUTCMonth();
                new_date.setUTCDate(day);
                test = function(){
                    return new_month !== new_date.getUTCMonth();
                };
            }
            // Common date-resetting loop -- if date is beyond end of month, make it
            // end of month
            while (test()){
                new_date.setUTCDate(--day);
                new_date.setUTCMonth(new_month);
            }
            return new_date;
        },

        moveYear: function(date, dir){
            return this.moveMonth(date, dir*12);
        },

        moveAvailableDate: function(date, dir, fn){
            do {
                date = this[fn](date, dir);

                if (!this.dateWithinRange(date))
                    return false;

                fn = 'moveDay';
            }
            while (this.dateIsDisabled(date));

            return date;
        },

        weekOfDateIsDisabled: function(date){
            return $.inArray(date.getUTCDay(), this.o.daysOfWeekDisabled) !== -1;
        },

        dateIsDisabled: function(date){
            return (
                this.weekOfDateIsDisabled(date) ||
                $.grep(this.o.datesDisabled, function(d){
                    return isUTCEquals(date, d);
                }).length > 0
            );
        },

        dateWithinRange: function(date){
            return date >= this.o.startDate && date <= this.o.endDate;
        },

        keydown: function(e){
            if (!this.picker.is(':visible')){
                if (e.keyCode === 40 || e.keyCode === 27) { // allow down to re-show picker
                    this.show();
                    e.stopPropagation();
                }
                return;
            }
            var dateChanged = false,
                dir, newViewDate,
                focusDate = this.focusDate || this.viewDate;
            switch (e.keyCode){
                case 27: // escape
                    if (this.focusDate){
                        this.focusDate = null;
                        this.viewDate = this.dates.get(-1) || this.viewDate;
                        this.fill();
                    }
                    else
                        this.hide();
                    e.preventDefault();
                    e.stopPropagation();
                    break;
                case 37: // left
                case 38: // up
                case 39: // right
                case 40: // down
                    if (!this.o.keyboardNavigation || this.o.daysOfWeekDisabled.length === 7)
                        break;
                    dir = e.keyCode === 37 || e.keyCode === 38 ? -1 : 1;
                    if (this.viewMode === 0) {
                        if (e.ctrlKey){
                            newViewDate = this.moveAvailableDate(focusDate, dir, 'moveYear');

                            if (newViewDate)
                                this._trigger('changeYear', this.viewDate);
                        } else if (e.shiftKey){
                            newViewDate = this.moveAvailableDate(focusDate, dir, 'moveMonth');

                            if (newViewDate)
                                this._trigger('changeMonth', this.viewDate);
                        } else if (e.keyCode === 37 || e.keyCode === 39){
                            newViewDate = this.moveAvailableDate(focusDate, dir, 'moveDay');
                        } else if (!this.weekOfDateIsDisabled(focusDate)){
                            newViewDate = this.moveAvailableDate(focusDate, dir, 'moveWeek');
                        }
                    } else if (this.viewMode === 1) {
                        if (e.keyCode === 38 || e.keyCode === 40) {
                            dir = dir * 4;
                        }
                        newViewDate = this.moveAvailableDate(focusDate, dir, 'moveMonth');
                    } else if (this.viewMode === 2) {
                        if (e.keyCode === 38 || e.keyCode === 40) {
                            dir = dir * 4;
                        }
                        newViewDate = this.moveAvailableDate(focusDate, dir, 'moveYear');
                    }
                    if (newViewDate){
                        this.focusDate = this.viewDate = newViewDate;
                        this.setValue();
                        this.fill();
                        e.preventDefault();
                    }
                    break;
                case 13: // enter
                    if (!this.o.forceParse)
                        break;
                    focusDate = this.focusDate || this.dates.get(-1) || this.viewDate;
                    if (this.o.keyboardNavigation) {
                        this._toggle_multidate(focusDate);
                        dateChanged = true;
                    }
                    this.focusDate = null;
                    this.viewDate = this.dates.get(-1) || this.viewDate;
                    this.setValue();
                    this.fill();
                    if (this.picker.is(':visible')){
                        e.preventDefault();
                        e.stopPropagation();
                        if (this.o.autoclose)
                            this.hide();
                    }
                    break;
                case 9: // tab
                    this.focusDate = null;
                    this.viewDate = this.dates.get(-1) || this.viewDate;
                    this.fill();
                    this.hide();
                    break;
            }
            if (dateChanged){
                if (this.dates.length)
                    this._trigger('changeDate');
                else
                    this._trigger('clearDate');
                this.inputField.trigger('change');
            }
        },

        setViewMode: function(viewMode){
            this.viewMode = viewMode;
            this.picker
                .children('div')
                .hide()
                .filter('.datepicker-' + DPGlobal.viewModes[this.viewMode].clsName)
                .show();
            this.updateNavArrows();
            this._trigger('changeViewMode', new Date(this.viewDate));
        }
    };

    var DateRangePicker = function(element, options){
        $.data(element, 'datepicker', this);
        this.element = $(element);
        this.inputs = $.map(options.inputs, function(i){
            return i.jquery ? i[0] : i;
        });
        delete options.inputs;

        this.keepEmptyValues = options.keepEmptyValues;
        delete options.keepEmptyValues;

        datepickerPlugin.call($(this.inputs), options)
            .on('changeDate', $.proxy(this.dateUpdated, this));

        this.pickers = $.map(this.inputs, function(i){
            return $.data(i, 'datepicker');
        });
        this.updateDates();
    };
    DateRangePicker.prototype = {
        updateDates: function(){
            this.dates = $.map(this.pickers, function(i){
                return i.getUTCDate();
            });
            this.updateRanges();
        },
        updateRanges: function(){
            var range = $.map(this.dates, function(d){
                return d.valueOf();
            });
            $.each(this.pickers, function(i, p){
                p.setRange(range);
            });
        },
        dateUpdated: function(e){
            // `this.updating` is a workaround for preventing infinite recursion
            // between `changeDate` triggering and `setUTCDate` calling.  Until
            // there is a better mechanism.
            if (this.updating)
                return;
            this.updating = true;

            var dp = $.data(e.target, 'datepicker');

            if (dp === undefined) {
                return;
            }

            var new_date = dp.getUTCDate(),
                keep_empty_values = this.keepEmptyValues,
                i = $.inArray(e.target, this.inputs),
                j = i - 1,
                k = i + 1,
                l = this.inputs.length;
            if (i === -1)
                return;

            $.each(this.pickers, function(i, p){
                if (!p.getUTCDate() && (p === dp || !keep_empty_values))
                    p.setUTCDate(new_date);
            });

            if (new_date < this.dates[j]){
                // Date being moved earlier/left
                while (j >= 0 && new_date < this.dates[j]){
                    this.pickers[j--].setUTCDate(new_date);
                }
            } else if (new_date > this.dates[k]){
                // Date being moved later/right
                while (k < l && new_date > this.dates[k]){
                    this.pickers[k++].setUTCDate(new_date);
                }
            }
            this.updateDates();

            delete this.updating;
        },
        destroy: function(){
            $.map(this.pickers, function(p){ p.destroy(); });
            $(this.inputs).off('changeDate', this.dateUpdated);
            delete this.element.data().datepicker;
        },
        remove: alias('destroy')
    };

    function opts_from_el(el, prefix){
        // Derive options from element data-attrs
        var data = $(el).data(),
            out = {}, inkey,
            replace = new RegExp('^' + prefix.toLowerCase() + '([A-Z])');
        prefix = new RegExp('^' + prefix.toLowerCase());
        function re_lower(_,a){
            return a.toLowerCase();
        }
        for (var key in data)
            if (prefix.test(key)){
                inkey = key.replace(replace, re_lower);
                out[inkey] = data[key];
            }
        return out;
    }

    function opts_from_locale(lang){
        // Derive options from locale plugins
        var out = {};
        // Check if "de-DE" style date is available, if not language should
        // fallback to 2 letter code eg "de"
        if (!dates[lang]){
            lang = lang.split('-')[0];
            if (!dates[lang])
                return;
        }
        var d = dates[lang];
        $.each(locale_opts, function(i,k){
            if (k in d)
                out[k] = d[k];
        });
        return out;
    }

    var old = $.fn.datepicker;
    var datepickerPlugin = function(option){
        var args = Array.apply(null, arguments);
        args.shift();
        var internal_return;
        this.each(function(){
            var $this = $(this),
                data = $this.data('datepicker'),
                options = typeof option === 'object' && option;
            if (!data){
                var elopts = opts_from_el(this, 'date'),
                    // Preliminary otions
                    xopts = $.extend({}, defaults, elopts, options),
                    locopts = opts_from_locale(xopts.language),
                    // Options priority: js args, data-attrs, locales, defaults
                    opts = $.extend({}, defaults, locopts, elopts, options);
                if ($this.hasClass('input-daterange') || opts.inputs){
                    $.extend(opts, {
                        inputs: opts.inputs || $this.find('input').toArray()
                    });
                    data = new DateRangePicker(this, opts);
                }
                else {
                    data = new Datepicker(this, opts);
                }
                $this.data('datepicker', data);
            }
            if (typeof option === 'string' && typeof data[option] === 'function'){
                internal_return = data[option].apply(data, args);
            }
        });

        if (
            internal_return === undefined ||
            internal_return instanceof Datepicker ||
            internal_return instanceof DateRangePicker
        )
            return this;

        if (this.length > 1)
            throw new Error('Using only allowed for the collection of a single element (' + option + ' function)');
        else
            return internal_return;
    };
    $.fn.datepicker = datepickerPlugin;

    var defaults = $.fn.datepicker.defaults = {
        assumeNearbyYear: false,
        autoclose: false,
        beforeShowDay: $.noop,
        beforeShowMonth: $.noop,
        beforeShowYear: $.noop,
        beforeShowDecade: $.noop,
        beforeShowCentury: $.noop,
        calendarWeeks: false,
        clearBtn: false,
        toggleActive: false,
        daysOfWeekDisabled: [],
        daysOfWeekHighlighted: [],
        datesDisabled: [],
        endDate: Infinity,
        forceParse: true,
        format: 'mm/dd/yyyy',
        keepEmptyValues: false,
        keyboardNavigation: true,
        language: 'en',
        minViewMode: 0,
        maxViewMode: 4,
        multidate: false,
        multidateSeparator: ',',
        orientation: "auto",
        rtl: false,
        startDate: -Infinity,
        startView: 0,
        todayBtn: false,
        todayHighlight: false,
        updateViewDate: true,
        weekStart: 0,
        disableTouchKeyboard: false,
        enableOnReadonly: true,
        showOnFocus: true,
        zIndexOffset: 10,
        container: 'body',
        immediateUpdates: false,
        dateCells:false,
        title: '',
        templates: {
            leftArrow: '&#x00AB;',
            rightArrow: '&#x00BB;'
        }
    };
    var locale_opts = $.fn.datepicker.locale_opts = [
        'format',
        'rtl',
        'weekStart'
    ];
    $.fn.datepicker.Constructor = Datepicker;
    var dates = $.fn.datepicker.dates = {
        en: {
            days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
            daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
            daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
            months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
            monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            today: "Today",
            clear: "Clear",
            titleFormat: "MM yyyy"
        }
    };

    var DPGlobal = {
        viewModes: [
            {
                names: ['days', 'month'],
                clsName: 'days',
                e: 'changeMonth'
            },
            {
                names: ['months', 'year'],
                clsName: 'months',
                e: 'changeYear',
                navStep: 1
            },
            {
                names: ['years', 'decade'],
                clsName: 'years',
                e: 'changeDecade',
                navStep: 10
            },
            {
                names: ['decades', 'century'],
                clsName: 'decades',
                e: 'changeCentury',
                navStep: 100
            },
            {
                names: ['centuries', 'millennium'],
                clsName: 'centuries',
                e: 'changeMillennium',
                navStep: 1000
            }
        ],
        validParts: /dd?|DD?|mm?|MM?|yy(?:yy)?/g,
        nonpunctuation: /[^ -\/:-@\u5e74\u6708\u65e5\[-`{-~\t\n\r]+/g,
        parseFormat: function(format){
            if (typeof format.toValue === 'function' && typeof format.toDisplay === 'function')
                return format;
            // IE treats \0 as a string end in inputs (truncating the value),
            // so it's a bad format delimiter, anyway
            var separators = format.replace(this.validParts, '\0').split('\0'),
                parts = format.match(this.validParts);
            if (!separators || !separators.length || !parts || parts.length === 0){
                throw new Error("Invalid date format.");
            }
            return {separators: separators, parts: parts};
        },
        parseDate: function(date, format, language, assumeNearby){
            if (!date)
                return undefined;
            if (date instanceof Date)
                return date;
            if (typeof format === 'string')
                format = DPGlobal.parseFormat(format);
            if (format.toValue)
                return format.toValue(date, format, language);
            var fn_map = {
                    d: 'moveDay',
                    m: 'moveMonth',
                    w: 'moveWeek',
                    y: 'moveYear'
                },
                dateAliases = {
                    yesterday: '-1d',
                    today: '+0d',
                    tomorrow: '+1d'
                },
                parts, part, dir, i, fn;
            if (date in dateAliases){
                date = dateAliases[date];
            }
            if (/^[\-+]\d+[dmwy]([\s,]+[\-+]\d+[dmwy])*$/i.test(date)){
                parts = date.match(/([\-+]\d+)([dmwy])/gi);
                date = new Date();
                for (i=0; i < parts.length; i++){
                    part = parts[i].match(/([\-+]\d+)([dmwy])/i);
                    dir = Number(part[1]);
                    fn = fn_map[part[2].toLowerCase()];
                    date = Datepicker.prototype[fn](date, dir);
                }
                return UTCDate(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate());
            }

            parts = date && date.match(this.nonpunctuation) || [];
            date = new Date();

            function applyNearbyYear(year, threshold){
                if (threshold === true)
                    threshold = 10;

                // if year is 2 digits or less, than the user most likely is trying to get a recent century
                if (year < 100){
                    year += 2000;
                    // if the new year is more than threshold years in advance, use last century
                    if (year > ((new Date()).getFullYear()+threshold)){
                        year -= 100;
                    }
                }

                return year;
            }

            var parsed = {},
                setters_order = ['yyyy', 'yy', 'M', 'MM', 'm', 'mm', 'd', 'dd'],
                setters_map = {
                    yyyy: function(d,v){
                        return d.setUTCFullYear(assumeNearby ? applyNearbyYear(v, assumeNearby) : v);
                    },
                    m: function(d,v){
                        if (isNaN(d))
                            return d;
                        v -= 1;
                        while (v < 0) v += 12;
                        v %= 12;
                        d.setUTCMonth(v);
                        while (d.getUTCMonth() !== v)
                            d.setUTCDate(d.getUTCDate()-1);
                        return d;
                    },
                    d: function(d,v){
                        return d.setUTCDate(v);
                    }
                },
                val, filtered;
            setters_map['yy'] = setters_map['yyyy'];
            setters_map['M'] = setters_map['MM'] = setters_map['mm'] = setters_map['m'];
            setters_map['dd'] = setters_map['d'];
            date = UTCToday();
            var fparts = format.parts.slice();
            // Remove noop parts
            if (parts.length !== fparts.length){
                fparts = $(fparts).filter(function(i,p){
                    return $.inArray(p, setters_order) !== -1;
                }).toArray();
            }
            // Process remainder
            function match_part(){
                var m = this.slice(0, parts[i].length),
                    p = parts[i].slice(0, m.length);
                return m.toLowerCase() === p.toLowerCase();
            }
            if (parts.length === fparts.length){
                var cnt;
                for (i=0, cnt = fparts.length; i < cnt; i++){
                    val = parseInt(parts[i], 10);
                    part = fparts[i];
                    if (isNaN(val)){
                        switch (part){
                            case 'MM':
                                filtered = $(dates[language].months).filter(match_part);
                                val = $.inArray(filtered[0], dates[language].months) + 1;
                                break;
                            case 'M':
                                filtered = $(dates[language].monthsShort).filter(match_part);
                                val = $.inArray(filtered[0], dates[language].monthsShort) + 1;
                                break;
                        }
                    }
                    parsed[part] = val;
                }
                var _date, s;
                for (i=0; i < setters_order.length; i++){
                    s = setters_order[i];
                    if (s in parsed && !isNaN(parsed[s])){
                        _date = new Date(date);
                        setters_map[s](_date, parsed[s]);
                        if (!isNaN(_date))
                            date = _date;
                    }
                }
            }
            return date;
        },
        formatDate: function(date, format, language){
            if (!date)
                return '';
            if (typeof format === 'string')
                format = DPGlobal.parseFormat(format);
            if (format.toDisplay)
                return format.toDisplay(date, format, language);
            var val = {
                d: date.getUTCDate(),
                D: dates[language].daysShort[date.getUTCDay()],
                DD: dates[language].days[date.getUTCDay()],
                m: date.getUTCMonth() + 1,
                M: dates[language].monthsShort[date.getUTCMonth()],
                MM: dates[language].months[date.getUTCMonth()],
                yy: date.getUTCFullYear().toString().substring(2),
                yyyy: date.getUTCFullYear()
            };
            val.dd = (val.d < 10 ? '0' : '') + val.d;
            val.mm = (val.m < 10 ? '0' : '') + val.m;
            date = [];
            var seps = $.extend([], format.separators);
            for (var i=0, cnt = format.parts.length; i <= cnt; i++){
                if (seps.length)
                    date.push(seps.shift());
                date.push(val[format.parts[i]]);
            }
            return date.join('');
        },
        headTemplate: '<thead>'+
            '<tr>'+
            '<th colspan="7" class="datepicker-title"></th>'+
            '</tr>'+
            '<tr>'+
            '<th class="prev">&laquo;</th>'+
            '<th colspan="5" class="datepicker-switch"></th>'+
            '<th class="next">&raquo;</th>'+
            '</tr>'+
            '</thead>',
        contTemplate: '<tbody><tr><td colspan="7"></td></tr></tbody>',
        footTemplate: '<tfoot>'+
            '<tr>'+
            '<th colspan="7" class="today"></th>'+
            '</tr>'+
            '<tr>'+
            '<th colspan="7" class="clear"></th>'+
            '</tr>'+
            '</tfoot>'
    };
    DPGlobal.template = '<div class="datepicker">'+
        '<div class="datepicker-days">'+
        '<table class="table-condensed">'+
        DPGlobal.headTemplate+
        '<tbody></tbody>'+
        DPGlobal.footTemplate+
        '</table>'+
        '</div>'+
        '<div class="datepicker-months">'+
        '<table class="table-condensed">'+
        DPGlobal.headTemplate+
        DPGlobal.contTemplate+
        DPGlobal.footTemplate+
        '</table>'+
        '</div>'+
        '<div class="datepicker-years">'+
        '<table class="table-condensed">'+
        DPGlobal.headTemplate+
        DPGlobal.contTemplate+
        DPGlobal.footTemplate+
        '</table>'+
        '</div>'+
        '<div class="datepicker-decades">'+
        '<table class="table-condensed">'+
        DPGlobal.headTemplate+
        DPGlobal.contTemplate+
        DPGlobal.footTemplate+
        '</table>'+
        '</div>'+
        '<div class="datepicker-centuries">'+
        '<table class="table-condensed">'+
        DPGlobal.headTemplate+
        DPGlobal.contTemplate+
        DPGlobal.footTemplate+
        '</table>'+
        '</div>'+
        '</div>';

    $.fn.datepicker.DPGlobal = DPGlobal;


    /* DATEPICKER NO CONFLICT
	* =================== */

    $.fn.datepicker.noConflict = function(){
        $.fn.datepicker = old;
        return this;
    };

    /* DATEPICKER VERSION
	 * =================== */
    $.fn.datepicker.version = '1.7.0-dev';

    /* DATEPICKER DATA-API
	* ================== */

    $(document).on(
        'focus.datepicker.data-api click.datepicker.data-api',
        '[data-provide="datepicker"]',
        function(e){
            var $this = $(this);
            if ($this.data('datepicker'))
                return;
            e.preventDefault();
            // component click requires us to explicitly show it
            datepickerPlugin.call($this, 'show');
        }
    );
    $(function(){
        datepickerPlugin.call($('[data-provide="datepicker-inline"]'));
    });

}));
/**
 @license jQuery Toggles v4.0.0
 Copyright 2012 - 2015 Simon Tabor - MIT License
 https://github.com/simontabor/jquery-toggles / http://simontabor.com/labs/toggles
 */
(function(root) {

    var factory = function($) {

        var Toggles = root['Toggles'] = function(el, opts) {
            var self = this;

            if (typeof opts === 'boolean' && el.data('toggles')) {
                el.data('toggles').toggle(opts);
                return;
            }

            var dataAttr = [
                'on',
                'drag',
                'click',
                'width',
                'height',
                'animate',
                'easing',
                'type',
                'checkbox'
            ];
            var dataOpts = {};
            for (var i = 0; i < dataAttr.length; i++) {
                var opt = el.data('toggle-' + dataAttr[i]);
                if (typeof opt !== 'undefined') dataOpts[dataAttr[i]] = opt;
            }

            // extend default opts with the users options
            opts = $.extend({
                // can the toggle be dragged
                'drag': true,
                // can it be clicked to toggle
                'click': true,
                'text': {
                    // text for the ON/OFF position
                    'on': 'ON',
                    'off': 'OFF'
                },
                // is the toggle ON on init
                'on': false,
                // animation time (ms)
                'animate': 250,
                // animation transition easing function,
                'easing': 'swing',
                // the checkbox to toggle (for use in forms)
                'checkbox': null,
                // element that can be clicked on to toggle. removes binding from the toggle itself (use nesting)
                'clicker': null,
                // width (falls back to 50px)
                'width': 0,
                // height (falls back to 20px)
                'height': 0,
                // defaults to a compact toggle, other option is 'select' where both options are shown at once
                'type': 'compact',
                // the event name to fire when we toggle
                'event': 'toggle'
            }, opts || {}, dataOpts);

            el.data('toggles', self);

            // set active to the opposite of what we want, so toggle will run properly
            var active = !opts['on'];

            var selectType = opts['type'] === 'select';

            // make checkbox a jquery element
            var checkbox = $(opts['checkbox']);

            var clicker = opts['clicker'] && $(opts['clicker']);

            var height = opts['height'] || el.height() || 20;
            var width = opts['width'] || el.width() || 50;

            el.height(height);
            el.width(width);

            var div = function(name) {
                return $('<div class="toggle-' + name + '">');
            };

            // wrapper inside toggle
            var elSlide = div('slide');
            // inside slide, this bit moves
            var elInner = div('inner');
            // the on/off divs
            var elOn = div('on');
            var elOff = div('off');
            // the grip to drag the toggle
            var elBlob = div('blob');

            var halfHeight = height / 2;
            var onOffWidth = width - halfHeight;

            var text = opts['text'];

            // set up the CSS for the individual elements
            elOn
                .css({
                    height: height,
                    width: onOffWidth,
                    textIndent: selectType ? '' : -height / 3,
                    lineHeight: height + 'px'
                })
                .html(text['on']);

            elOff
                .css({
                    height: height,
                    width: onOffWidth,
                    marginLeft: selectType ? '' : -halfHeight,
                    textIndent: selectType ? '' : height / 3,
                    lineHeight: height + 'px'
                })
                .html(text['off']);

            elBlob.css({
                height: height,
                width: height,
                marginLeft: -halfHeight
            });

            elInner.css({
                width: width * 2 - height,
                marginLeft: selectType ? 0 : -width + height
            });

            if (selectType) {
                elSlide.addClass('toggle-select');
                el.css('width', onOffWidth * 2);
                elBlob.hide();
            }

            // construct the toggle
            elInner.append(elOn, elBlob, elOff);
            elSlide.html(elInner);
            el.html(elSlide);

            var doToggle = self.toggle = function(state, noAnimate, noEvent) {
                // check we arent already in the desired state
                if (active === state) return;

                active = self['active'] = !active;

                el.data('toggle-active', active);

                elOff.toggleClass('active', !active);
                elOn.toggleClass('active', active);
                checkbox.prop('checked', active);

                if (!noEvent) el.trigger(opts['event'], active);

                if (selectType) return;

                var margin = active ? 0 : -width + height;

                // move the toggle!
                elInner.stop().animate({
                    'marginLeft': margin
                }, noAnimate ? 0 : opts['animate'], opts['easing']);
            };


            // evt handler for click events
            var clickHandler = function(e) {
                // if the target isn't the blob or dragging is disabled, toggle!
                if (!el.hasClass('disabled') && (e['target'] !== elBlob[0] || !opts['drag'])) {
                    doToggle();
                }
            };

            // if click is enabled and toggle isn't within the clicker element (stops double binding)
            if (opts['click'] && (!clicker || !clicker.has(el).length)) {
                el.on('click', clickHandler);
            }

            // setup the clicker element
            if (clicker) {
                clicker.on('click', clickHandler);
            }

            // bind up dragging stuff
            if (opts['drag'] && !selectType) {
                // time to begin the dragging parts/blob clicks
                var diff;
                var slideLimit = (width - height) / 4;

                // fired on mouseup and mouseleave events
                var upLeave = function(e) {
                    el.off('mousemove');
                    elSlide.off('mouseleave');
                    elBlob.off('mouseup');

                    if (!diff && opts['click'] && e.type !== 'mouseleave') {
                        doToggle();
                        return;
                    }

                    var overBound = active ? diff < -slideLimit : diff > slideLimit;
                    if (overBound) {
                        // dragged far enough, toggle
                        doToggle();
                    } else {
                        // reset to previous state
                        elInner.stop().animate({
                            marginLeft: active ? 0 : -width + height
                        }, opts['animate'] / 2, opts['easing']);
                    }
                };

                var wh = -width + height;

                elBlob.on('mousedown', function(e) {

                    if (el.hasClass('disabled')) return;

                    // reset diff
                    diff = 0;

                    elBlob.off('mouseup');
                    elSlide.off('mouseleave');
                    var cursor = e.pageX;

                    el.on('mousemove', elBlob, function(e) {
                        diff = e.pageX - cursor;
                        var marginLeft;

                        if (active) {
                            marginLeft = diff;

                            // keep it within the limits
                            if (diff > 0) marginLeft = 0;
                            if (diff < wh) marginLeft = wh;
                        } else {
                            marginLeft = diff + wh;

                            if (diff < 0) marginLeft = wh;
                            if (diff > -wh) marginLeft = 0;
                        }

                        elInner.css('margin-left', marginLeft);
                    });

                    elBlob.on('mouseup', upLeave);
                    elSlide.on('mouseleave', upLeave);
                });
            }

            // toggle the toggle to the correct state with no animation and no event
            doToggle(opts['on'], true, true);
        };

        $.fn['toggles'] = function(opts) {
            return this.each(function() {
                new Toggles($(this), opts);
            });
        };
    };

    if (typeof define === 'function' && define['amd']) {
        define(['jquery'], factory);
    } else {
        factory(root['jQuery'] || root['Zepto'] || root['ender'] || root['$'] || $);
    }

})(this);
var itau = (function() {

    var outputData = function() {
        $.ajax({
            async: false,
            dataType: "json",
            url: $('#itau-shopline').find('[name="itau-data-url"]').val(),
            success: function(data) {
                var token   = data.token;

                $('#data-itau-token').remove();
                $('#itau-shopline').attr('action', data.destination);
                $('#itau-shopline').append('<input type="hidden" name="DC" value="'+ token +'" id="data-itau-token" />')
            }
        });

        window.open('', 'SHOPLINE', 'toolbar=yes,menubar=yes,resizable=yes,status=no,scrollbars=yes,width=815,height=575');
    };

    return {
        output: outputData
    };
})();// Variaveis globais
var iconSet = 'fa'

// Verificando qual é a página
$(document).ready(function(){
    dataScreen = $('body').data('screen');
})

// Input number
function initTouchSpin() {
    $.map($('.touch-spin'), function(item) {
        var $touchSpin = $(item);

        var min = 1, max = 100

        if ($touchSpin.data('touch-spin-min')) {
            min = $touchSpin.data('touch-spin-min')
        } else if (!isNaN($touchSpin.prop('min'))) {
            min = $touchSpin.prop('min')
        }

        if ($touchSpin.data('touch-spin-max')) {
            max = $touchSpin.data('touch-spin-max')
        } else if (!isNaN($touchSpin.prop('max')) && $touchSpin.prop('max')) {
            console.log('teste', $touchSpin.prop('max'))
            max = $touchSpin.prop('max')
        }

        $touchSpin.TouchSpin({
            min: min,
            max: max,
            stepinterval: 50,
            maxboostedstep: 10000000,
            step: $touchSpin.data('touch-spin-step') ? $touchSpin.data('touch-spin-step') : 1,
            decimals: $touchSpin.data('touch-spin-decimals') ? $touchSpin.data('touch-spin-decimals') : 0
        });
    })
}

function initFormDataActions() {

    $('body').on('click', 'a[data-action="delete"]', function(e) {

        e.preventDefault();

        var _this = this;

        var form = false;
        var urlLocation = false;

        if ($(this).data('form') && $(this).data('type') == 'submit') {
            form = $($(this).data('form'));
        } else {
            urlLocation = $(this).attr('href');
        }

        var options = {
            title: $(this).data('title') ? $(this).data('title') : 'Você tem certeza?',
            text: $(this).data('text') ? $(this).data('text') : "Você realmente deseja remover esta informação?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-success",
            confirmButtonText: "Tenho",
            cancelButtonText: "Não"
        };

        swal(options, function(isConfirm) {
            if (isConfirm) {
                loadOnDisabledElement(_this);
                if (form) {
                    form.trigger('submit');
                } else {
                    window.location = urlLocation;
                }
            }
        });
    });

    $('body').on('click', 'a[data-action="transferencia_pontos"]', function(e) {

        e.preventDefault();

        var _this = this;

        var form = $($(this).parents('#form_transferencia_pontos'));

        var options = {
            title: $(this).data('title') ? $(this).data('title') : 'Você tem certeza?',
            text: $(this).data('text') ? $(this).data('text') : "Você realmente deseja transferir seu bônus?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-success",
            confirmButtonText: "Tenho",
            cancelButtonText: "Não"
        };

        swal(options, function(isConfirm) {
            if (isConfirm) {
                form.submit();
            }
        });
    });

    //$('[data-action="delete"]').on('click', function(e) {
    //    if (confirm('Deseja excluir este registro?')) {
    //        return true;
    //    }
    //    e.preventDefault();
    //    return false;
    //});
}

function identifyScreen() {
    var screen = $(window).innerWidth();

    $('body').attr('data-screen', 'xs');

    if(screen >= 768) {
        $('body').attr('data-screen', 'sm');
    }

    if(screen >= 992) {
        $('body').attr('data-screen', 'md');
    }

    if(screen >= 1200) {
        $('body').attr('data-screen', 'lg');
    }
}

function identifyPage(){
    pageName = '';
    if($('body').data('page')) {
        pageName = $('body').data('page');
    }
}


// Setando altura total para o menu mobile
function setHeightMenuMobile(){
    $('#menu-mobile').css('height', heightWindow = $(window).innerHeight());
};

// Abrindo e fechando menu mobile
function openMenuMobile(){
    var element = '#menu-mobile';

    function hideMenuMobile(){
        $(element).removeClass('active');

        $('body, #main-header').animate({
            left: '0px'
        }, 250);

        $(element).animate({
            left: '-290px'
        }, 250);

    };

    function openMenuMobile(){
        $(element).addClass('active');
        $('body, #main-header').animate({
            left: '290px'
        }, 250);

        $(element).animate({
            left: '0px'
        }, 250);
    };

    $('.open-menu-mobile').click(function(){

        if($(element).hasClass('active')) {
            hideMenuMobile();
        } else {
            openMenuMobile();
        }
    });
};

// Lightbox
function initLightbox(seletor, options) {
    initMagnificPopupModal();
    //if (typeof seletor === 'object') {
    //    options = seletor;
    //    seletor = undefined;
    //}
    //
    //var options = options || {};
    //
    //var options_default = {
    //    fixed: true,
    //    iframe: true,
    //    close: '<span class="' + iconSet + ' ' + iconSet + '-close"></span>',
    //    escKey: false,
    //    imgError: 'Imagem Não encontrada',
    //    xhrError: 'Conteudo Não encontrado',
    //    width: '100%',
    //    height: '100%',
    //    maxHeight: '870px',
    //    maxWidth: '870px'
    //};
    //
    //$.extend(options_default, options);
    //var seletor = seletor || '[data-lightbox="iframe"]';
    //
    //$(seletor).colorbox(options_default);
    return true;
};

// Informa o php se Ã© o documento Ã© um lightbox
$(function () {
    $(document).bind('cbox_open', function(){
        var href = $.colorbox.element().attr('href');
        if (href) {
            var url = new Url(href);
            url.query.isLightbox = 'true';
            $.colorbox.element().attr('href', url.toString());
        }
    });
});

// Lightbox de galeria de imagens
//function initLightboxGallery(seletor, options) {
//
//    if (typeof seletor === 'object') {
//        options = seletor;
//        seletor = undefined;
//    }
//
//    var options = options || {};
//
//    var options_default = {
//        fixed: true,
//        photo: true,
//        scalePhotos: true,
//        close: '<span class="' + iconSet + ' ' + iconSet + '-close' + '"></span>',
//        escKey: false,
//        imgError: 'Imagem não encontrada',
//        xhrError: 'Conteudo não encontrado',
//        width: '100%',
//        maxWidth: '870px',
//        maxHeight: '870px',
//        rel: 'gal',
//        previous: '<span class="' + iconSet + ' ' + iconSet + '-chevron-left' + '"></span>',
//        next: '<span class="' + iconSet + ' ' + iconSet + '-chevron-right' + '"></span>'
//    };
//
//    $.extend(options_default, options);
//
//    var seletor = seletor || '[data-lightbox="photo"]';
//    $(seletor).colorbox(options_default);
//
//    return true;
//}

// Esconde o topo quando o usuario desce a ppágina, e mostra o topo novamente quando sobe a pagina
function hideHeaderOnScroll(){

    var mainHeaderElement   = $('#main-header'),
        scrollInit          = $(window).scrollTop(),
        headerHeight        = mainHeaderElement.innerHeight();

    $(window).scroll(function(){

        if($('#menu-mobile').hasClass('active') == false) {
            // Rolando pra baixo
            if ($(window).scrollTop() < scrollInit) {
                mainHeaderElement.css('top', '0');
            } else {
                if ($(window).scrollTop() > headerHeight) {
                    mainHeaderElement.css('top', headerHeight*-1 + 'px');

                    $('.nav-mobile').find('button').addClass('collapsed')
                    $('header .panel .in').removeClass('in');
                }
            }

            scrollInit = $(window).scrollTop();
        }
    });
}

// Filtro de listas
function filterList() {
    $('.filter-input').fastLiveFilter('.filter-list', {
        timeout: 200
    });
}

// MÃ¡scaras
function initMasks(){
    $(".mask-cep").mask("00000-000", {clearIfNotMatch: true});
    $(".mask-cpf").mask("000.000.000-00", {clearIfNotMatch: true});
    $(".mask-date").mask("00/00/0000", {clearIfNotMatch: true});
    $(".mask-cnpj").mask("00.000.000/0000-00", {clearIfNotMatch: true});
    $(".mask-year").mask("0000", {clearIfNotMatch: true});
    $(".mask-mes").mask("00", {clearIfNotMatch: true});

    var maskSecurityCodeCard = function (val) {
            return val.replace(/\D/g, '').length === 11 ? '000' : '0009';
        },
        options = {onKeyPress: function(val, e, field, options) {
                field.mask(maskSecurityCodeCard.apply({}, arguments), options);
            }
        };

    $('.mask-security-code-card').mask(maskSecurityCodeCard, options);

    var maskTel = function (val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        },
        options = {onKeyPress: function(val, e, field, options) {
                field.mask(maskTel.apply({}, arguments), options);
            }
        };

    $('.mask-tel').mask(maskTel, options);
}

// Validando HTML5
function validity(seletor, message){
    $(seletor).on('change', function(){
        try{
            this.setCustomValidity('');
        } catch(e){}
    });

    $(seletor).on('invalid', function(){
        this.setCustomValidity(message);
    });
}

// Exibir listagem de produtos em forma de lista ou grade
function gridOrList(){
    var list            = '.product-list:not(.product-list-carousel)',
        product         = list + ' .product',
        productImage    = list + ' .product-image',
        productInfo     = list + ' .product-info',
        gridButton      = '[data-products-visualization="grid"]',
        listButton      = '[data-products-visualization="list"]';

    $.cookie('products-visualization');

    if($.cookie('products-visualization') == 'grid') {
        $(list).addClass('grid').removeClass('list');
        $(gridButton).addClass('active');
        $(listButton).removeClass('active');
        $(product).removeClass('col-md-12').addClass('col-md-3');
        $(productImage).removeClass('col-md-3').addClass('col-md-12');
        $(productInfo).removeClass('col-md-7').addClass('col-md-12');
    } else if ($.cookie('products-visualization') == 'list') {
        $(list).addClass('list').removeClass('grid');
        $(listButton).addClass('active');
        $(gridButton).removeClass('active');
        $(product).removeClass('col-md-3').addClass('col-md-12');
        $(productImage).removeClass('col-md-12').addClass('col-md-3');
        $(productInfo).removeClass('col-md-12').addClass('col-md-7');
    }
}

// Altera o cookie de list ou grid
function changeCookieGridOrList() {
    gridOrList();

    $('[data-products-visualization]').click(function(){
        $('.product-list').hide();

        setTimeout(function(){
            $('.product-list').fadeIn();
        }, 100);

        if($(this).data('products-visualization') == 'grid') {
            $.cookie('products-visualization', 'grid', {expire: 30});
        } else if ($(this).data('products-visualization') == 'list') {
            $.cookie('products-visualization', 'list', {expire: 30});
        }
        gridOrList();
    })
}

// Disable load
function loadOnDisabledElement(element){
    $(element)
        .attr('disabled', true);

    if ($(element).find('.fa-spinner').length == 0) {
        $(element).prepend(iconLoading());
    }
}

function iconLoading() {
    return '<i class="fa fa-spin fa-spinner"></i> ';
}

// Remove o svg do button ou link
function removeLoaderFromElement(element) {
    $(element).each(function(i, el) {
        if ($(el).is('form')) {
            $(el).find('[type="submit"]').find('.fa-spinner').remove();
        } else {
            $(element).find('.fa-spinner').remove();
        }
    });
}

//  Desabilita o botão de submit quando tenta enviar o formulário
function disableFormOnSubmit(){

    $('form.form-disabled-on-load').submit(function(){
        loadOnDisabledElement($(this).find('[type="submit"]'));
    });

    $('a.form-disabled-on-load,.btn-spinner').click(function(){
        loadOnDisabledElement(this);
    });
}

function creditCardType(cardNumber) {
    function isValidInputType(cardNumber) {
        return typeof cardNumber === "string" || cardNumber instanceof String;
    }
    function addMatchingCardsToResults(cardNumber, cardConfiguration, results) {
        var i, patternLength;
        for (i = 0; i < cardConfiguration.patterns.length; i++) {
            var pattern = cardConfiguration.patterns[i];
            if (!matches(cardNumber, pattern)) {
                continue;
            }
            var clonedCardConfiguration = creditCardType.clone(cardConfiguration);
            if (Array.isArray(pattern)) {
                patternLength = String(pattern[0]).length;
            }
            else {
                patternLength = String(pattern).length;
            }
            if (cardNumber.length >= patternLength) {
                clonedCardConfiguration.matchStrength = patternLength;
            }
            results.push(clonedCardConfiguration);
            break;
        }
    }
    function matchesRange(cardNumber, min, max) {
        var maxLengthToCheck = String(min).length;
        var substr = cardNumber.substr(0, maxLengthToCheck);
        var integerRepresentationOfCardNumber = parseInt(substr, 10);
        min = parseInt(String(min).substr(0, substr.length), 10);
        max = parseInt(String(max).substr(0, substr.length), 10);
        return (integerRepresentationOfCardNumber >= min &&
            integerRepresentationOfCardNumber <= max);
    }
    function matchesPattern(cardNumber, pattern) {
        pattern = String(pattern);
        return (pattern.substring(0, cardNumber.length) ===
            cardNumber.substring(0, pattern.length));
    }
    function matches(cardNumber, pattern) {
        if (Array.isArray(pattern)) {
            return matchesRange(cardNumber, pattern[0], pattern[1]);
        }
        return matchesPattern(cardNumber, pattern);
    }
    function hasEnoughResultsToDetermineBestMatch(results) {
        var numberOfResultsWithMaxStrengthProperty = results.filter(function (result) { return result.matchStrength; }).length;
        /*
         * if all possible results have a maxStrength property that means the card
         * number is sufficiently long enough to determine conclusively what the card
         * type is
         * */
        return (numberOfResultsWithMaxStrengthProperty > 0 &&
            numberOfResultsWithMaxStrengthProperty === results.length);
    }
    function findBestMatch(results) {
        if (!hasEnoughResultsToDetermineBestMatch(results)) {
            return null;
        }
        return results.reduce(function (bestMatch, result) {
            if (!bestMatch) {
                return result;
            }
            /*
             * If the current best match pattern is less specific than this result, set
             * the result as the new best match
             * */
            if (Number(bestMatch.matchStrength) < Number(result.matchStrength)) {
                return result;
            }
            return bestMatch;
        });
    }

    var results = [];
    if (!isValidInputType(cardNumber)) {
        return results;
    }
    if (cardNumber.length === 0) {
        return creditCardType.getAllCardTypes();
    }
    creditCardType.testOrder.forEach(function (cardType) {
        var cardConfiguration = creditCardType.findType(cardType);
        addMatchingCardsToResults(cardNumber, cardConfiguration, results);
    });
    var bestMatch = findBestMatch(results);
    if (bestMatch) {
        return [bestMatch];
    }
    return results;
}
creditCardType.__assign = function () {
    var __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
creditCardType.cardTypes = {
    visa: {
        niceType: "Visa",
        type: "visa",
        patterns: [4],
        gaps: [4, 8, 12],
        lengths: [16, 18, 19],
        code: {
            name: "CVV",
            size: 3,
        },
    },
    mastercard: {
        niceType: "Mastercard",
        type: "mastercard",
        patterns: [[51, 55], [2221, 2229], [223, 229], [23, 26], [270, 271], 2720],
        gaps: [4, 8, 12],
        lengths: [16],
        code: {
            name: "CVC",
            size: 3,
        },
    },
    amex: {
        niceType: "American Express",
        type: "amex",
        patterns: [34, 37],
        gaps: [4, 10],
        lengths: [15],
        code: {
            name: "CID",
            size: 4,
        },
    },
    "diners-club": {
        niceType: "Diners Club",
        type: "diners",
        patterns: [[300, 305], 36, 38, 39],
        gaps: [4, 10],
        lengths: [14, 16, 19],
        code: {
            name: "CVV",
            size: 3,
        },
    },
    discover: {
        niceType: "Discover",
        type: "discover",
        patterns: [6011, [644, 649], 65],
        gaps: [4, 8, 12],
        lengths: [16, 19],
        code: {
            name: "CID",
            size: 3,
        },
    },
    jcb: {
        niceType: "JCB",
        type: "jcb",
        patterns: [2131, 1800, [3528, 3589]],
        gaps: [4, 8, 12],
        lengths: [16, 17, 18, 19],
        code: {
            name: "CVV",
            size: 3,
        },
    },
    unionpay: {
        niceType: "UnionPay",
        type: "unionpay",
        patterns: [
            620,
            [624, 626],
            [62100, 62182],
            [62184, 62187],
            [62185, 62197],
            [62200, 62205],
            [622010, 622999],
            622018,
            [622019, 622999],
            [62207, 62209],
            [622126, 622925],
            [623, 626],
            6270,
            6272,
            6276,
            [627700, 627779],
            [627781, 627799],
            [6282, 6289],
            6291,
            6292,
            810,
            [8110, 8131],
            [8132, 8151],
            [8152, 8163],
            [8164, 8171],
        ],
        gaps: [4, 8, 12],
        lengths: [14, 15, 16, 17, 18, 19],
        code: {
            name: "CVN",
            size: 3,
        },
    },
    maestro: {
        niceType: "Maestro",
        type: "maestro",
        patterns: [
            493698,
            [500000, 504174],
            [504176, 506698],
            [506779, 508999],
            [56, 59],
            63,
            67,
            6,
        ],
        gaps: [4, 8, 12],
        lengths: [12, 13, 14, 15, 16, 17, 18, 19],
        code: {
            name: "CVC",
            size: 3,
        },
    },
    elo: {
        niceType: "Elo",
        type: "elo",
        patterns: [
            401178,
            401179,
            438935,
            457631,
            457632,
            431274,
            451416,
            457393,
            504175,
            [506699, 506778],
            [509000, 509999],
            627780,
            636297,
            636368,
            [650031, 650033],
            [650035, 650051],
            [650405, 650439],
            [650485, 650538],
            [650541, 650598],
            [650700, 650718],
            [650720, 650727],
            [650901, 650978],
            [651652, 651679],
            [655000, 655019],
            [655021, 655058],
        ],
        gaps: [4, 8, 12],
        lengths: [16],
        code: {
            name: "CVE",
            size: 3,
        },
    },
    mir: {
        niceType: "Mir",
        type: "mir",
        patterns: [[2200, 2204]],
        gaps: [4, 8, 12],
        lengths: [16, 17, 18, 19],
        code: {
            name: "CVP2",
            size: 3,
        },
    },
    hiper: {
        niceType: "Hiper",
        type: "hiper",
        patterns: [637095, 637568, 637599, 637609, 637612],
        gaps: [4, 8, 12],
        lengths: [16],
        code: {
            name: "CVC",
            size: 3,
        },
    },
    hipercard: {
        niceType: "Hipercard",
        type: "hipercard",
        patterns: [606282],
        gaps: [4, 8, 12],
        lengths: [16],
        code: {
            name: "CVC",
            size: 3,
        },
    },
};
creditCardType.types = {
    VISA: "visa",
    MASTERCARD: "mastercard",
    AMERICAN_EXPRESS: "amex",
    DINERS_CLUB: "diners-club",
    DISCOVER: "discover",
    JCB: "jcb",
    UNIONPAY: "unionpay",
    MAESTRO: "maestro",
    ELO: "elo",
    MIR: "mir",
    HIPER: "hiper",
    HIPERCARD: "hipercard",
};
creditCardType.ORIGINAL_TEST_ORDER = [
    creditCardType.types.VISA,
    creditCardType.types.MASTERCARD,
    creditCardType.types.AMERICAN_EXPRESS,
    creditCardType.types.DINERS_CLUB,
    creditCardType.types.DISCOVER,
    // creditCardType.types.JCB,
    // creditCardType.types.UNIONPAY,
    // creditCardType.types.MAESTRO,
    creditCardType.types.ELO,
    // creditCardType.types.MIR,
    // creditCardType.types.HIPER,
    creditCardType.types.HIPERCARD,
];
creditCardType.customCards = {};
creditCardType.clone = function (originalObject) {
    if (!originalObject) {
        return null;
    }
    return JSON.parse(JSON.stringify(originalObject));
};
creditCardType.testOrder = creditCardType.clone(creditCardType.ORIGINAL_TEST_ORDER)
creditCardType.getCardPosition = function (name, ignoreErrorForNotExisting) {
    if (ignoreErrorForNotExisting === void 0) { ignoreErrorForNotExisting = false; }
    var position = creditCardType.testOrder.indexOf(name);
    if (!ignoreErrorForNotExisting && position === -1) {
        throw new Error('"' + name + '" is not a supported card type.');
    }
    return position;
};
creditCardType.findType = function (cardType) {
    return creditCardType.customCards[cardType] || creditCardType.cardTypes[cardType];
};
creditCardType.getAllCardTypes = function () {
    return creditCardType.testOrder.map(function (cardType) { return creditCardType.clone(creditCardType.findType(cardType)); });
};
creditCardType.getTypeInfo = function (cardType) {
    return creditCardType.clone(creditCardType.findType(cardType));
};
creditCardType.removeCard = function (name) {
    var position = getCardPosition(name);
    creditCardType.testOrder.splice(position, 1);
};
creditCardType.addCard = function (config) {
    var existingCardPosition = getCardPosition(config.type, true);
    creditCardType.customCards[config.type] = config;
    if (existingCardPosition === -1) {
        creditCardType.testOrder.push(config.type);
    }
};
creditCardType.updateCard = function (cardType, updates) {
    var originalObject = creditCardType.customCards[cardType] || cardTypes[cardType];
    if (!originalObject) {
        throw new Error("\"" + cardType + "\" is not a recognized type. Use `addCard` instead.'");
    }
    if (updates.type && originalObject.type !== updates.type) {
        throw new Error("Cannot overwrite type parameter.");
    }
    var clonedCard = creditCardType.clone(originalObject);
    clonedCard = creditCardType.__assign(creditCardType.__assign({}, clonedCard), updates);
    creditCardType.customCards[clonedCard.type] = clonedCard;
};
creditCardType.changeOrder = function (name, position) {
    var currentPosition = getCardPosition(name);
    creditCardType.testOrder.splice(currentPosition, 1);
    creditCardType.testOrder.splice(position, 0, name);
};
creditCardType.resetModifications = function () {
    creditCardType.testOrder = creditCardType.clone(creditCardType.ORIGINAL_TEST_ORDER);
    creditCardType.customCards = {};
};

// Verifica qual o tipo de cartão de crédito
function validateCreditCard(){

    $('.input-credit-card').on('input', function() {
        var $this = $(this),
            value = $this.val().replace(/\D+/g, ''),
            brandArr = new creditCardType(value),
            brand = brandArr && brandArr.length > 0 ? brandArr[0] : null
    
        if (value.length > 0 && brand) {
            $this.next('span').attr('class', 'icon-' + brand.type + '-32');
            $('#flag-card').val(brand.type);

            var mask = ''

            for (var i = 0; i < brand.lengths[0]; i++) {
                if (brand.gaps.indexOf(i) !== -1) {
                    mask += ' '
                }

                mask += '0'
            }

            $this.val('').mask(mask).val(value);
            $('#security-code').mask(brand.code.size === 4 ? '0009' : '000');

            if(brand.lengths.indexOf(value.length) !== -1) {
                this.setCustomValidity('');
            } else {
                this.setCustomValidity('Número do cartão inválido.');
            }
        } else {
            $this.next('span').attr('class', '');
            $('#flag-card').val('');
            $this.mask('0000 0000 0000 0000');
            $('#security-code').val('');
        }
    })
};

// Verifica qual o tipo de cartão de crédito
function validateDebitCard(){

    // $('.input-credit-card').validateCreditCard(function(result) {
    //     var input           = document.getElementById('number-card-debit'),
    //         securityInput   = $('#security-code-debit');

    //     if (result.card_type != null) {
    //         var cartName = result.card_type.name;

    //         if($(input).val().length != 0) {
    //             $(input).next('span').attr('class', 'icon-' + cartName + '-32');
    //             $('#flag-card-debit').val(cartName);
    //         }

    //         var backup = $(input).val();
    //         $(input).val('').mask(result.card_type.mask_number).val(backup);
    //         $(securityInput).mask(result.card_type.mask_security);

    //         var space_regex = /\s/g;
    //         var lengthInput = $(input).val().replace(space_regex, "").length;

    //         if(lengthInput != result.card_type.valid_length) {
    //             input.setCustomValidity('Número do cartão inválido.');
    //         } else {
    //             input.setCustomValidity('');
    //         }
    //     } else {
    //         $(input).next('span').attr('class', '');
    //         $('#flag-card-debit').val('');
    //         $(input).mask('0000000000000000');
    //         $(securityInput).val('');
    //     }


    // }, {
    //     accept: ['amex', 'diners','discover','elo', 'mastercard', 'visa']
    // })
};

// Recarrega o conteudo
function reloadContent() {
    $('body').load(window.location.href + ' .update-onload');
}

/*
 Mostra o botão de comprar fixo quando o botão de comprar normal não aparece na tela
 Usado no mobile nos detalhes do produto.
 */
function showBuyButton() {
}

/* Select com links (direciona após o change)
 * */
function selectWithLinks(seletor){
    if (!seletor){
        seletor = '.select-with-links';
    }
    $(seletor).bind('change', function () {
        var url = $(this).val();
        if (url) {
            window.location = url;
        }
        return false;
    });
}

// Busca sugestiva
function suggestiveSearch() {

    if ($("#suggestive-search-input").length > 0) {
        $("#suggestive-search-input").autocomplete({
            appendTo: '#suggestive-search',
            source: root_path + '/busca/search/',
            select: function(event, ui) {
                $("#suggestive-search-input").val(ui.item.nome);
                return false;
            },
            minChars: 3,
            messages: {
                noResults: '',
                results: function() {}
            }
        }).data("autocomplete")._renderItem = function(ul, item) {
            return $("<li></li>")
                .data("item.autocomplete", item)
                .append(
                    "<a href='" + item.url + "'>" +
                    "<div class='img'>" + item.image + "</div>" +
                    "<div class='info'>" +
                    "<div class='name'>" +item.name + "</div>" +
                    "<div class='price'>" +item.price + "</div>" +
                    "</div>" +
                    "</a>"
                )
                .appendTo(ul);
        };
    }
}

/**
 * Função que carrega as cidades com base no id do estado.
 *
 * @param int estado_id ID do estado que deseja carregar as cidades
 * @param object target DIV na qual serÃ¡ carregado o elemento select
 * @param string defaultValue Nome da cidade na qual virá selecionada
 */
function loadCidades(estado_id, target, defaultValue){

    var loadingHTML = '<select><option>Carregando...</option></select>';

    $.ajax({
        url: window.root_path + '/ajax/ajax-cidades',
        data: {estadoId: estado_id, cidade: defaultValue},
        beforeSend: function() {
            target.html(loadingHTML);
        },
        success: function(response) {
            target.html(response);
        }
    });

}

/**
 * Função que carrega os dados de endereço com base no CEP
 *
 * @param string cep
 * @param object fields Deve conter o mapeamento dos objetos a serem atualizados
 */
function loadEndereco(cep, fields){

    // Define como o cep deve retornar
    var pattern = /[0-9]{8}/;

    // Limpa o CEP deixando apenas os dÃ­gitos
    var cep = cep.replace(/\D/g, '');

    // Passa os campos em variÃ¡veis
    var $field_cep = fields.cep
        , $field_endereco = fields.endereco
        , $field_bairro = fields.bairro
        , $field_estado = fields.estado
        , $field_cidade = fields.cidade
        , $field_numero = fields.numero;

    if (pattern.test(cep)) {

        $.ajax({
            url: window.root_path + '/ajax/consulta-endereco/?cep=' +cep,
            dataType: 'json',
            beforeSend: function() {
                $field_cep.parent()
                    .css('position', 'relative');

                $('<svg class="loader cep-loading" xml:space="preserve" style="enable-background:new 0 0 50 50;" viewBox="0 0 50 50" height="34px" width="34px" y="0px" x="0px">' +
                    '<path d="M25.251,6.461c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615V6.461z" fill="#000">' +
                    '<animateTransform repeatCount="indefinite" dur="0.6s" to="360 25 25" from="0 25 25" type="rotate" attributeName="transform" attributeType="xml">' +
                    '</path>' +
                    '</svg> Aguarde...')
                    .css({
                        position: 'absolute',
                        top: '10px',
                        right: '10px'
                    })
                    .insertAfter($field_cep);

                // endereço
                $field_endereco.attr('disabled', true);
                $field_bairro.attr('disabled', true);
                $field_estado.attr('disabled', true);
                $field_cidade.attr('disabled', true);
                return;
            },
            success: function(response) {
            
                if (typeof response == 'object') {

                    $field_endereco.val(response.logradouro);
                    $field_bairro.val(response.bairro);

                    var estadoId = $field_estado
                        .find('option[data-sigla="' + response.uf + '"]')
                        .val();
                    $field_estado.val(estadoId)

                    // Cidade
                    loadCidades(estadoId, $field_cidade.parent(), response.cidade);

                    if ($(':focus').length == 0) {
                        // Adiciona o focus no endereço se vier vazio
                        if ($field_endereco.val() == '') {
                            $field_endereco.focus();
                            return;
                        }

                        // Adiciona o focus no bairro se vier vazio
                        if ($field_bairro.val() == '') {
                            $field_bairro.focus();
                            return;
                        }

                        // Adiciona o focus no campo número
                        $field_numero.focus();
                    }

                    return;
                }

                // Se der algum erro, limpa todos os campos e dÃ¡ o foco no campo de endereço
                $field_estado.val('');
                $field_cidade.val('');
                $field_endereco.val('');
                $field_bairro.val('');
                $field_numero.val('');

                $field_endereco.focus();

                alert("Não foi possível carregar as informações do CEP. Continue o cadastro preenchendo as suas informações manualmente.");


            },
            complete: function() {
                $('.cep-loading').fadeOut(function() {
                    $(this).remove();
                });

                $field_estado.removeAttr('disabled');
                $field_cidade.removeAttr('disabled');
                $field_endereco.removeAttr('disabled');
                $field_bairro.removeAttr('disabled');
                $field_numero.removeAttr('disabled');
            },
            error: function() {

                // Se der algum erro, limpa todos os campos e dÃ¡ o foco no campo de endereço
                $field_estado.val('');
                $field_cidade.val('');
                $field_endereco.val('');
                $field_bairro.val('');
                $field_numero.val('');

                $field_endereco.focus();

                alert("Não foi possível carregar as informações do CEP. Continue o cadastro preenchendo as suas informações manualmente.");
            }
        });

    } else {
        alert("O CEP informado é inválido. Por favor, informe um CEP válido.");
    }
}

function initPicture() {
    picturefill();
}

function initRating() {
    $('.rating').rating().on('rating.change', function (event, value, caption) {
        $(this).attr('value', value);
        $('.rating-title').attr('value', window.starRate[value]);
    });
}

function initFormFilterProduct() {
    $( ".form-filter select" ).on( "change", function( event ) {
        $('.form-filter').submit();
    });
    $(".form-filter").on("submit", function( event ) {
        event.preventDefault();
        var separator = window.location.href.indexOf('?') == -1 ? '?' : '&';
        $('.container-product-list').load(window.location.href + separator + $( this ).serialize() + ' .product-list ', function() {
            initRating();
            initPicture();
        });
    });
}

function svgLoader(width) {
    return '<svg class="loader" xml:space="preserve" style="enable-background:new 0 0 50 50;" viewBox="0 0 50 50" y="0px" x="0px">' +
        '<path d="M25.251,6.461c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615V6.461z" fill="#fff">' +
        '<animateTransform repeatCount="indefinite" dur="0.6s" to="360 25 25" from="0 25 25" type="rotate" attributeName="transform" attributeType="xml">' +
        '</path>' +
        '</svg>';
}

function openMagnificPopupIframe(url) {

    var markupStyle =
        '@media (max-width: 991px) { ' +
        '.mfp-iframe-holder, .mfp-iframe-scaler { padding: 0; } .mfp-iframe-holder .mfp-content {max-width: 100%; max-height: 100%; height:100%} ' +
        '} ' +
        '.mfp-iframe-holder .mfp-content { max-width: 992px; height:100% } .mfp-iframe-scaler { padding: 0; } ';

    $.magnificPopup.open({
        items: {
            src: url
        },
        tLoading: 'Carregando...',
        type: 'iframe',
        mainClass: 'initial-popup',
        iframe: {
            markup: '<style>'+markupStyle+'</style>'+
                '<div class="mfp-iframe-scaler" >'+
                '<div class="mfp-close"></div>'+
                '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
                '</div></div>'
        }
    }, 0);

    $(document).on('click', '.popup-modal-dismiss', function (e) {
        e.preventDefault();
        $.magnificPopup.close();
    });
}

function initMagnificPopupModal() {
    $('body').on('click', '[data-lightbox*="iframe"]', function(e) {
        e.preventDefault();
        if ($(this).is('a')) {
            openMagnificPopupIframe($(this).attr('href'));
        } else {
            if ($(this).data('href')) {
                openMagnificPopupIframe($(this).data('href'));
            }
        }
    });
}window.easyZoomIsActive = false;

$(window).load(function(){
    hideLoader();
});

PNotify.prototype.options.styling = "bootstrap3";

function hideLoader() {
    $('body').css('cursor', 'default');
    $('#modalLoadingContent').fadeOut('normal', function() {
        $(this).removeClass('show');
    });
}
function showLoader() {
    $('body').css('cursor', 'wait');
    $('#modalLoadingContent').addClass('show').fadeIn('normal');
}


// Iniciando funções para todas as página
$(document).ready(function(){

    /**
     * Menu mobile
     */
    $('nav#menu').removeClass('hidden').mmenu({
        extensions: ["border-full"],
        onClick: {
            blockUI: true
        }
    });

    /**
     * Topo flutuante
     */
    if ($('header .middle').lenght > 0) {
        var $top = $('header .middle');
        var offset = $top.offset();
        $(window).scroll(function () {
            if ($('body').scrollTop() > offset.top) {
                $('body').addClass('fixed');
            } else {
                $('body').removeClass('fixed');
            }
        });
    }

    initPicture();
    identifyPage();
    setHeightMenuMobile();
    openMenuMobile();
    initMasks();
    //initLightbox();
    //initLightboxGallery();
    filterList();
    selectWithLinks();
    changeCookieGridOrList();
    disableFormOnSubmit();
    removeLoaderFromElement('.form-disabled-on-load');
    initFormFilterProduct();
    initFormDataActions();
    initTouchSpin();
    initMagnificPopupModal();

    $('input, textarea').placeholder();

    $('input.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true
    });

    if(dataScreen == 'xs' || dataScreen == 'sm') {
        hideHeaderOnScroll();
    } else {
        suggestiveSearch();
    }

    // Fechar o menu de todas categorias quando sai dele
    /*$('#menu-desktop').on('mouseleave', function(){
     $('#categories').collapse('hide')
     });
     $('#menu-desktop .first-level').on('hover', function(){
     $('#categories').collapse('hide')
     });*/

    // Abrindo menu de todas as categorias no hover (opcional)
    /*$('.open-all-categories').on('hover', function(){
     $('#categories').collapse('show')
     })*/

    // Atualiza a cidade com base no estado selecionado
    $('body').on('change', '#address-uf', function() {
        loadCidades($(this).val(), $('#register-city').parent(), null);
    });

    // Atualiza o endereço com base no CEP informado
    $('body').on('change', '#register-cep', function() {
        if($(this).is(":hidden") == false) {
            loadEndereco($(this).val(), {
                cep: $(this),
                endereco: $('#register-street'),
                bairro: $('#register-district'),
                numero: $('#register-number'),
                estado: $('#address-uf'),
                cidade: $('#register-city')
            });
        }
    });

    /**
     * Adiciona o evento click nos links do menu principal para carregar a páginacom base no href
     */
    $('.dropdown-hover a').click(function(e){
        window.location.href = $(this).attr('href');
        e.preventDefault();
        return false;
    });

    // Usando a classe "dropdown-hover" pra abrir o dropdown no hover (ah vÃ¡)
    $('.dropdown-hover').hover(function(){
        $(this).find('.dropdown-menu').dropdown('toggle');

        /*  Alinha o submenu (do menu de categorias em destaque) pela direita da <li> pai
         *   caso o submenu passe do conteudo centralizado.
         * */

        var nav                 = '#menu-desktop nav';
        var submenu             = '#menu-desktop .open .dropdown-menu';

        var navOffsetRight      = $(window).width() - ($(nav).offset().left + $(nav).outerWidth());
        var navOffsetLeft       = $(nav).offset().left;
        var submenuOffsetLeft   = $(submenu).offset() != undefined ? $(submenu).offset().left : 0;
        var submenuOffsetRight  = $(window).width() - submenuOffsetLeft + $(submenu).outerWidth();

        if(navOffsetRight > submenuOffsetRight){
            $(submenu).addClass('pull-right');
            if(navOffsetLeft > $(submenu).offset().left) {
                submenuAlign = '-' + ($(submenu).innerWidth() / 2) + 'px';
                $(submenu).removeClass('pull-right').css('margin-left','50%').css('left', submenuAlign);
            }
        }

    });

    // Validando HTML5
    validity('.validity-default', 'Preenche este campo');
    validity('.validity-name', 'Informe seu nome');
    validity('.validity-email', 'E-mail inválido');
    validity('.validity-birthday', 'Data de nascimento inválida');
    validity('.validity-cpf', 'CPF inválido');
    validity('.validity-tel', 'Telefone inválido');
    validity('.validity-question', 'Informe sua dúvida');
    validity('.validity-city', 'Selecione uma cidade');
    validity('.validity-state', 'Selecione um estado');
    validity('.validity-neighborhood', 'Insira o nome do bairro');
    validity('.validity-number', 'Número inválido');
    validity('.validity-cep', 'CEP inválido');
    validity('.validity-password', 'A senha deve ter no máximo 6 caracteres');
    validity('#name-card', 'Digite o nome exatamente como está impresso no cartão');

});

function showNotify(options) {

    var defaultOptions = {
        delay: 2000,
        addclass: "stack-modal"
    };

    options = $.extend({}, defaultOptions, options);

    new window.parent.PNotify(options);
}

function showNotifySuccess(options) {

    var defaultOptions = {
        delay: 2000,
        addclass: "stack-modal",
        type: 'success',
        icon: 'fa fa-check-circle',
    };

    options = $.extend({}, defaultOptions, options);

    showNotify(options);
}

function showNotifyError(options) {

    var defaultOptions = {
        delay: 2000,
        addclass: "stack-modal",
        type: 'error',
        icon: 'fa fa-times',
    };

    options = $.extend({}, defaultOptions, options);

    showNotify(options);
}

$(document).ready(function() {

    PNotify.prototype.options.styling = "bootstrap3";

    /**
     * Ajuste a altura dos paineis com os endereços
     */
    var $addressPanels = $(".equals .panel .panel-body");
    var heights = $addressPanels.map(function() {
        return $(this).height();
    }).get();
    var maxHeight = Math.max.apply(null, heights);
    $addressPanels.height(maxHeight);

    if (pageName == 'checkout-endereco') {
        var panelHeight = $(".equals .panel:first").height();
        var $btnAddAddress = $(".btn.add-address");

        var paddingH = (panelHeight - $btnAddAddress.height()) / 2;
        $btnAddAddress.css({
            'padding-top': paddingH + 'px',
            'padding-bottom': paddingH + 'px',
        })
    }

    // Home
    if(pageName == 'home') {

        setTimeout(function() {

            $("#carousel-marca").owlCarousel({
                itemsDesktop: [1920,10],
                itemsDesktopSmall: [1200,8],
                itemsTablet: [992, 6],
                itemsMobile: [768, 3],
                navigation: true,
                navigationText: false,
                pagination: false,
                slideSpeed: 300,
                paginationSpeed: 400,
                autoPlay: 4000000,
                addClassActive: true,
                rewindNav: false
            });

            $("#advantage-banner").owlCarousel({
                pagination: false,
                navigation: false,
                slideSpeed: 300,
                paginationSpeed: 400,
                singleItem: true,
                autoPlay: true,
            });

            // $("#carousel-banner").owlCarousel({
            //     navigation: false,
            //     slideSpeed: 300,
            //     paginationSpeed: 400,
            //     singleItem: true,
            //     autoPlay: 4000
            // });
            $('#carousel-banner').owlCarousel12({
                loop: true,
                margin: 0,
                dots: true ,

                autoHeight: true,
                nav: false,
                autoplay: true,
                smartSpeed: 1000,
                addClassActive: true,
                responsiveClass: true,
                responsive: {
                    0: {
                        items: 1,
                        lazyLoad: true,
                    }
                }
            });

            $(".carousel-products").owlCarousel({
                itemsDesktop: [2000,4],
                itemsDesktopSmall: [991,1],
                itemsTablet: [768, 1],
                navigation: true,
                navigationText: false,
                itemsMobile: [479, 1],
                slideSpeed: 300,
                paginationSpeed: 400,
                autoPlay: 4000000,
                addClassActive: true,
                rewindNav: false
            });
        }, 200);
    }

    // Empresa
    if(pageName == 'empresa') {
        $("#owl-about").owlCarousel({
            navigation: false,
            slideSpeed: 300,
            paginationSpeed: 400,
            items: 4,
            itemsDesktop: [1186, 4],
            itemsDesktopSmall: [978, 3],
            itemsTablet: [600, 1]
        });
    }

    // Cadastro
    if(pageName == 'cadastro') {

        // Escolhendo pessoa física ou jurídica
        $('[name="people-type"]').click(function(){
            checked = $('[name="people-type"]:checked').attr('id');

            if(checked == 'people-type-1') {
                $('#company-data').hide();
                $('#person-data h2').text('Dados pessoais');
                $('#person-data').addClass('col-md-offset-3');
                $('#company-data input[type="text"]').removeAttr('required');
            } else {
                $('#company-data').stop(true, true, true).fadeIn().removeClass('collapse');
                $('#person-data h2').text('Dados do responsável');
                $('#person-data').removeClass('col-md-offset-3');
                $('#company-data input[type="text"]').attr('required', true);
                setTimeout(function(){
                    $("#company-data input").each(function() {
                        if ($(this).val() == "") {
                            $(this).focus();
                            return false;
                        }
                    })
                }, 300);
            }
        });

        var hasValueInCompanyData = false;
        $('#company-data :input').each(function(i, input) {
            if ($(input).val() != '') {
                hasValueInCompanyData = true;
            }
        });

        if (!hasValueInCompanyData) {
            $('#company-data :input').val('').removeAttr('required');
        }
    };

    // Carrinho
    if(pageName == 'carrinho') {
        // Atualizar quantidade
        //window.reloadTimer;
        $('body').on('change', '.product-qtd', function() {

            // Como são necessários dois inputs, ele altera o valor dos dois inputs quando um muda
            var valor = this.value;
            var data_item_id = $(this).data('item-id');
            $('[data-item-id="'+data_item_id+'"]').attr('value', valor);

            var bodyHeight  = $(window).innerHeight() + 'px',
                form        = $(this).parents('form'),
                action      = form.attr('action'),
                data        = form.serialize();

            $('body').css('cursor', 'wait');

            clearTimeout(window.reloadTimer);
            window.reloadTimer = setTimeout(function() {
                $.post(action, data, function() {
                    $('main').load(window.location.href + ' .update-onload', function() {
                        $('body').css('cursor', 'default');
                        initTouchSpin();
                        initMasks();
                    });
                });
            }, 300);
        });

        $('body').on('click', '#cancelar-simulacao-frete', function(e) {
            e.preventDefault();
            var action = $(this).attr('href');
            $.get(action, function() {
                $('main').load(window.location.href + ' .update-onload', function() {
                    $('body').css('cursor', 'default');
                });
            });
        })
    };

    // Checkout pagamento
    if(pageName == 'checkout-pagamento') {

        /**
         * Valida o cartão de crédito
         */
        validateCreditCard();
        validateDebitCard();

        /**
         * Executa quando um panel é aberto.
         */
        function fnShownCollapse(e) {
            var body = $('html,body');
            var panel = $(e.target).parents('.panel:first');
            if (panel.length) {
                body.stop().animate({scrollTop: panel.offset().top}, 500);
            }
        }

        /**
         * Executa quando um panel é fechado.
         */
        function fnHiddenCollapse(e) {
        }

        // Inicializa os eventos de abertura e fechamento dos panels para a escolha da forma de pagamento.
        $('.accordion-payment-type .panel .panel-collapse').on('hidden.bs.collapse', fnHiddenCollapse);
        $('.accordion-payment-type .panel .panel-collapse').on('shown.bs.collapse', fnShownCollapse);

        // Eventos na alteraÃ§Ã£o da forma de entrega
        $('body').on('change', '[name="frete"]:input', function() {
            /**
             * Caso seja retirada na loja, adiciona o id do local da retirada.
             * Do contrário, o remove.
             */
            
            var $boxSelects = $('.box-select-retirada-loja');
            var $selectRetiradaLoja = $('[name="pedido_retirada_loja"]');

            if ($(this).val() !== 'retirada_loja') {
                $selectRetiradaLoja.val('').attr('required', false);
                $boxSelects.slideUp(250);
            } else {
                $selectRetiradaLoja.attr('required', true);
                $boxSelects.slideDown(250);

                // $('#optionEstado').change();
            }
        });

        /**
         * Ao efetuar o submit do formulário de pagamento (seja cartão, boleto, pagseguro, outros)
         * o sistema verifica se existe um meio de entrega e se algum está preenchido.
         * Caso não esteja, apresenta uma mensagem de erro de informando o cliente para que selecione
         * um dos itens referentes ao meio de entrega.
         *
         * Verifica também se o código do patrocinador foi informado (apenas quando necessário).
         */
        $('body').on('click', '.confirm-payment', function(e) {
            var $btConfirm = $(this);

            if ($('input[name="frete"]').length > 0 && $('input[name="frete"]:checked').length == 0) {
                $('#shipping-error').show();
                $("body").animate({
                    top: $('#shipping-type').offset().top
                }, 300);
                e.preventDefault();
                return false;
            }

            var $formPatrocinador = $('#form-patrocinador');
            if ($formPatrocinador.size() > 0) {
                //verifica se o patrocinador foi confirmado.
                var $confirmado = $formPatrocinador.find('#patrocinador-confirmado');
                if ($confirmado.val() != 1) {

                    var options = {
                        title: 'Patrocinador não informado',
                        text: "Deseja que o sistema escolha um patrocinador automaticamente?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "Sim",
                        cancelButtonText: "Não"
                    };

                    swal(options, function(isConfirm) {
                        if (isConfirm) {
                            $confirmado.val('1');
                            $btConfirm.closest('form').submit();
                        } else {
                            setTimeout(function () {
                                $formPatrocinador.find('#codigo-patrocinador').focus();
                            }, 100);
                        }
                    });

                    e.preventDefault();
                    return false;

                } else {
                    var patrocinadorDesc = $('#patrocinador-desc').val();
                    if (patrocinadorDesc) {

                        var options = {
                            title: 'Confirmação Patrocinador',
                            text: 'Confirma patrocinador "' + patrocinadorDesc + '"?',
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-success",
                            confirmButtonText: "Sim",
                            cancelButtonText: "Não"
                        };

                        swal(options, function(isConfirm) {
                            if (isConfirm) {
                                $confirmado.val('1');
                                $btConfirm.closest('form').submit();
                            }
                        });

                        e.preventDefault();
                        return false;
                    }
                }
            }
        });

    };

    // Minha Conta - Avaliações
    if(pageName == 'minha-conta-avaliacoes') {
        initRating();
    }

    //Visualizar Rede
    if (pageName == 'minha-conta-visualizar-rede') {

        $("#rede-clientes").jOrgChart({chartElement: $('#rede-container')[0]});

    }

    // Produto Avaliação
    if(pageName == 'produto-avalie') {
        initRating();
        $('.rating').val('').show();
    };

    // Produto detalhes
    if(pageName == 'produto-detalhes') {

        /**
         * Ações relacionadas a galeria de imagens do produto
         */

        // Inicializa cada galeria ao Gerenciador de Galerias
        $('[data-gallery-id]').each(function(i, element) {
            QPGalleryManager.addGallery($(element).data('gallery-id'));
        });

        // Inicializa a galeria por popup (com PhotoSwipe)
        $(document).on('click', '.openGalleryPhotoSwipe', function () {
            var psGalleryId = $(this).data('gallery-id');
            var $element = $('.gallery-photo-swipe[data-gallery-id="'+psGalleryId+'"]');
            QPPhotoSwipe.create($element, {index: QPGalleryManager.getCurrentIndex(psGalleryId)}).init();
        });

        // Inicializa todas as galerias
        var _initGalleryPlugins = function() {

            // Desabilita o evento click da galeria vertical
            $('.swiper-gallery-products .swiper-slide').on('click', 'a', function(e) {
                e.preventDefault();
            });

            // Inicializa o caroussel das fotos maiores (OwlCarousel)
            var $owlFotosSelector = $(".owl-fotos");
            $owlFotosSelector.each(function(key, gallery) {
                $(gallery).find('.item').on('click', 'a', function(e) {
                    if ($(window).width() <= 992) {
                        e.preventDefault();
                        var psGalleryId = $(gallery).data('gallery-id');
                        var $element = $('.gallery-photo-swipe[data-gallery-id="'+psGalleryId+'"]');
                        QPPhotoSwipe.create($element, {index: QPGalleryManager.getCurrentIndex(psGalleryId)}).init();
                    }
                });
                QPOwlGallery.init($(gallery));
            });


            initSwiperGallery();

            // Inicializa o Zoom das imagens
            QPEasyZoom.init($(".easyzoom"));


            // Thumb horizontal
            var owlThumbs = $(".owl-fotos-miniaturas");
            owlThumbs.owlCarousel({
                itemsDesktop: [1024, 5],
                itemsDesktopSmall: [991 ,5],
                itemsTablet: [768, 4],
                itemsMobile: [479, 4],
                navigation: false,
                navigationText: false,
                addClassActive: true,
                rewindNav: false,
                pagination: false,
            });

            owlThumbs.on('click', '.owl-item', function(e) {
                e.preventDefault();
                var index = $(this).data("owlItem");
                var galleryId = $(this).parents('[data-gallery-id]').data('gallery-id');
                QPGalleryManager.setCurrentIndex(galleryId, index);
            });

        }

        _initGalleryPlugins();


        /**
         * Abre o box de avaliação após o cliente efetuar o login e ser redirecionado a
         * tela de detalhes do produto novamente.
         */
        if (window.location.hash == '#box-avalie') {
            initLightbox('.btn._avaliacao', {open: true});
        }

        initLightbox();
        initPicture();
        initRating();


        /**
         * @type {*|jQuery|HTMLElement}
         */
        var $formAtributosSelecionados = $('#form-atributos-selecionados');
        if ($formAtributosSelecionados.length == 0) {
            $formAtributosSelecionados = $('<form>').attr('id', '#form-atributos-selecionados');
            $('body').prepend($formAtributosSelecionados);
        }

        var fnAtualizaAtributosSelecionados = function() {

            $('.variation[data-selected="true"]').each(function(i, select) {

                var produtoId           = $(this).data('produto-id');
                var produtoAtributoId   = $(select).data('attribute');
                var variacao;

                if ($(this).is('select')) {
                    variacao = $(select).find('option:selected').text();
                } else {
                    variacao = $(select).val();
                }

                var name    = 'opcoes[' + produtoId + '][' + produtoAtributoId + ']';
                var $input  = $formAtributosSelecionados.find('[name="' + name + '"]');

                if ($input.length == 1) {
                    $input.val(variacao);
                } else {
                    $input = $('<input>').attr({
                        type: 'hidden',
                        name: name
                    });
                    $input.appendTo($formAtributosSelecionados);
                }

                $input.val(variacao);

            });

        }

        /**
         * Para cada variação pré-selecionada, preenche a variável que possui o mapeamento das variações
         * pré-selecionadas pelo sistema.
         */
        fnAtualizaAtributosSelecionados();

        /**
         * Controlando os eventos das variações:
         * A cada mudança, o sistema verificará as opções disponiveis de acordo
         * com as opções que já foram selecionadas
         */
        $(document).on('change', '.variation', function() {

            var typeField   = $(this).is('select') ? 'select' : 'input';
            var produtoId   = $(this).data('produto-id');
            var $submitBtn  = $('#buy-button-' + produtoId);

            var backupValue = $submitBtn.html();
            var loadingHtml = "<span class='fa fa-spin fa-spinner'></span> Aguarde...";

            $submitBtn
                .attr('disabled', true)
                .html(loadingHtml)
            ;

            if (typeField == 'select') {
                $(this).attr('data-selected', ($(this).val() != ''));
            } else if (typeField == 'input') {
                var name = ($(this).attr('name'));
                console.log($('[name="'+name+'"][data-produto-id="'+produtoId+'"]'));
                $('[name="'+name+'"][data-produto-id="'+produtoId+'"]').removeAttr('data-selected');
                $(this).attr('data-selected', true);
            }

            // Cria um array com todos os atributos já selecionados
            // para que no server, o php saiba quais atributos faltam
            // ser preenchidos.
            fnAtualizaAtributosSelecionados();

            var self = $(this);

            // Faz a requisição solicitando as variações que ainda não foram selecionadas
            // com base nas que já foram selecionadas.
            $.ajax({
                url: window.root_path + '/produtos/actions/variacao',
                dataType: 'json',
                data: {atributos: $formAtributosSelecionados.serialize(), produto_id: produtoId},
                type: 'POST',
                success: function(response) {

                    $.each(response, function(produtoId, values) {

                        // Atualiza os valores que mudam de acordo com a variaÃ§Ã£o
                        $.each(values.data_content_id, function(id, value) {

                            var $content = $('[data-content-id="' + id + '"]');

                            if ($content.hasClass('owl-carousel')) {
                                $content.parent().css({height: $content.parent().height(), overflow: 'hidden'});
                            }

                            if ($content.length > 0) {
                                $content.html(value);

                                if ($content.hasClass('owl-carousel'))  {
                                    $content.data('owlCarousel').reinit();
                                }

                                $content.parent().css('height', 'auto');
                            }
                        });

                        initSwiperGallery();

                        // Inicializa o Zoom das imagens
                        QPEasyZoom.init($(".easyzoom"));

                        // Atualiza a variaÃ§Ã£o de acordo com a variaÃ§Ã£o selecionada
                        $('[name*="quantidade_pv['+produtoId+']"]').attr('name', 'quantidade_pv['+produtoId+']['+values.produto_variacao_id+']');
                        $('[name="produto_variacao_id[' + produtoId + ']"]').val(values.produto_variacao_id);

                        self.closest('.product-info')
                            .find('.add-to-cart')
                            .attr('data-product-variation', values.produto_variacao_id);
                    });


                    initPicture();
                    initTouchSpin();
                    //_initGalleryPlugins();

                    $submitBtn.html(backupValue).prop('disabled', false);
                }
            });
        });

        showBuyButton();
    };

    if (pageName === 'minha-conta-visualizar-rede') {

        var postLadoRede = function (lado, successCallback) {
            $.ajax({
                type: 'POST',
                url: root_path + '/minha-conta/visualizar-rede/configuracao.ajax.php',
                data: {lado: lado},
                success: successCallback,
                error: function () {
                    alert('Não foi possível salvar a configuração.');
                }
            });
        };

        $('#toggle-rede-automatica').toggles({
            text: {
                on: 'SIM',
                off: 'NÃO'
            },
            width: 100,
            height: 22
        }).on('toggle', function(e, active) {

            var lado = (active) ? 'AUTOMATICO' : 'ESQUERDO';

            postLadoRede(lado, function () {
                if (active) {
                    $('#toggle-lado-rede').closest('tr').hide(); /* se escolheu lado automatico, nao precisa exibir as opções manuais. */
                } else {
                    $('#toggle-lado-rede').closest('tr').show(); /* desativou a escolha de lado automatico. Mostra as opções manuais.  */

                    //marca esquerda (on) como padrão
                    var myToggle = $('#toggle-lado-rede').data('toggles');
                    myToggle.toggle(true, true, true); // myToggle.toggle(state, noAnimate, noEvent)

                }
            });
        });

        $('#toggle-lado-rede').toggles({
            text: {
                on: 'ESQUERDA',
                off: 'DIREITA'
            },
            width: 100,
            height: 22
        }).on('toggle', function(e, active) {

            var lado = (active) ? 'ESQUERDO' : 'DIREITO';

            postLadoRede(lado);
        });
    }


    /**
     * Conjuntos de funções para adicionar ao carrinho por ajax
     * @type {*|jQuery|HTMLElement}
     */

    $.each($('.form-ajax-adicionar-ao-carrinho'), function(i, formAdicionarAoCarrinhoAjax) {

        var $formAdicionarAoCarrinhoAjax = $(formAdicionarAoCarrinhoAjax);

        var $submitBtn = $formAdicionarAoCarrinhoAjax.find('[type=submit]');

        var backupValue = $submitBtn.html();
        var loadingHtml = iconLoading() + 'Aguarde...';

        if ($formAdicionarAoCarrinhoAjax.data('modal') == 'true') {
            $('.box-exibir-grade').hide();
        }

        $formAdicionarAoCarrinhoAjax.ajaxForm({

            beforeSubmit: function (formData, jqForm, options) {

                var isSuccess = false;

                // Busca o elemento novamente, pois ele pode ter sido carregado e a variável perde a referência do botão.
                var $submitBtn = $formAdicionarAoCarrinhoAjax.find('[type=submit]');

                $submitBtn.html(loadingHtml);
                $submitBtn.prop('disabled', true);

                $.ajax({
                    url: window.root_path + '/produtos/actions/validate-quantity-products?' + $.param(formData),
                    async: false,
                }).done(function (response) {
                    isSuccess = response.status == "success";
                });

                if (isSuccess == false) {
                    $submitBtn.html(backupValue);
                    $submitBtn.prop('disabled', false);
                    $('.box-flash-messages').load(window.root_path + '/ajax/flash-messages');
                }

                return isSuccess;
            },

            success: function (response) {

                // Busca o elemento novamente, pois ele pode ter sido carregado e a variável perde a referẽncias do botão.
                var $submitBtn = $formAdicionarAoCarrinhoAjax.find('[type=submit]');

                $submitBtn.html(backupValue);
                $submitBtn.prop('disabled', false);

                if (response.status == 'success') {

                    window.parent.$('.cart-quantity').load(window.root_path + '/ajax/cart.quantity');

                    showNotifySuccess({
                        text: 'Produtos adicionados com sucesso!',
                    });

                    window.parent.$.magnificPopup.close();
                } else {
                    $('.box-flash-messages').load(window.root_path + '/ajax/flash-messages');
                }
            }

        });
    });

    $.each($('.form-ajax-adicionar-kit-ao-carrinho'), function(i, formAdicionarAoCarrinhoAjax) {
        var $formAdicionarAoCarrinhoAjax = $(formAdicionarAoCarrinhoAjax);

        $formAdicionarAoCarrinhoAjax.ajaxForm({
            success: function (response) {
                $.get(window.root_path + '/ajax/cart.quantity', function(quantity) {
                    var mobileQty = quantity.match(/\d+/)[0] || 0;

                    window.parent.$('.quantity-item').text(mobileQty);
                    window.parent.$('.cart-quantity').text(quantity);

                    window.parent.$('#shopping-cart-id').load(window.root_path + '/ajax/cart.boxed', function() {
                        window.parent.$.magnificPopup.close();
                    });
                });

                showNotifySuccess({
                    text: 'Kit adicionado com sucesso!',
                });
            },
            error: function(response) {
                showNotifyError({
                    text: response.responseJSON
                        ? response.responseJSON.message
                        : 'Ocorreu um erro ao adicionar o kit no carrino',
                });
            }
        });
    });

    $('.product-info').on('click', '.add-to-cart-variation', function(e) {
        e.preventDefault();
        var url = $(this).data('href');
        openMagnificPopupIframe(url);
    });

    $('.btn-action-comprar-junto').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        openMagnificPopupIframe(url);
    });
    $('.product-info').on('click', '.add-to-cart', function(e) {
        e.preventDefault();

        var $button    = $(this);

        var backupValue = $button.html();
        var loadingHtml = "<span class='fa fa-spin fa-spinner'></span> Aguarde...";

        $button.html(loadingHtml);
        $button.prop('disabled', true);

        //var quantidade = $('#quantiaItem_' + $button.data('product')).val();
        var quantidade = +$(this).closest('.product-info').find('[name^="quantidade"]').val() || 1;

        var produto_id = $button.data('product');
        var produto_v_id = $button.data('product-variation');

        if(quantidade > 0){
            $.post(window.root_path + '/carrinho/actions/adicionarAjax/?isLightbox=1', {
                product_id: produto_id,
                product_variation_id: produto_v_id,
                quantidade: quantidade
            })
            .done(function( data ) {
                if (data.status == 'success') {
                    showNotifySuccess({
                        text: 'Produtos adicionados com sucesso!'
                    });

                    $.get(window.root_path + '/ajax/cart.quantity', function(quantity) {
                        var mobileQty = quantity.match(/\d+/)[0] || 0;

                        window.parent.$('.quantity-item').text(mobileQty);
                        window.parent.$('.cart-quantity').text(quantity);

                        window.parent.$('#shopping-cart-id').load(window.root_path + '/ajax/cart.boxed', function() {
                            window.parent.$.magnificPopup.close();
                        });
                    });

                    // $button.parents('.product').find('.tag-added-cart').removeClass('hidden');
                } else {
                    showNotifyError({
                        text: data.message
                    });
                }
            })
            .fail(function() {
                showNotifyError({
                    text: 'Ocorreu um erro ao adicionar os produtos no carrinho!'
                });
            })
            .always(function() {
                $button.html(backupValue);
                $button.prop('disabled', false);
            });
        }else{
            showNotify({
                text: 'Quantidade deve ser maior que 0!'
            });

            $button.html(backupValue);
            $button.prop('disabled', false);
        }

    });

});

function initSwiperGallery() {
    // Inicializa a galeria vertical (Swiper)
    $('.swiper-gallery-products').css('height', $('.owl-fotos .owl-wrapper-outer').css('height'));
    QPVerticalGallery.init($('.swiper-container'));
}

var QPEasyZoom = (function() {

    var _init = function(el) {
        _resize(el);
        $(window).resize(function() {
            _resize(el)
        });
    }

    var _resize = function(el) {
        if ($(window).width() >= 992) {
            el.each(function(i, v){
                $(this).easyZoom();
            })
        } else {
            el.each(function(i, v){
                apiEazyZoom = $(this).easyZoom().data('easyZoom').teardown();
            })
        }
    }

    return {
        init: _init,
    }

})();

/**
 * QPGalleryManager
 * @type {{currentIndex, setCurrentIndex}}
 */
var QPGalleryManager = (function() {

    var _collGalleries = {};

    var _updateIndexInPlugins = function(gallery) {

        index = gallery.currentIndex;

        if (QPVerticalGallery.getInstance(gallery.galleryId)) {
            QPVerticalGallery.getInstance(gallery.galleryId).swipeTo(index-1, 300, '');
        }

        QPOwlGallery.goTo(gallery);

    }

    var _addGallery = function(galleryId) {

        if(_collGalleries[galleryId] == undefined) {
            _collGalleries[galleryId] = {
                galleryId: galleryId,
                currentIndex: 1,
            };
        }
    }

    var _getCurrentIndex = function(galleryId) {
        var gallery = _getGalleryById(galleryId);
        if (gallery) {
            return gallery.currentIndex;
        }
        return false;
    }
    var _setCurrentIndex = function(galleryId, index) {
        var gallery = _getGalleryById(galleryId);
        if (gallery) {
            gallery.currentIndex = index;
            _updateIndexInPlugins(gallery);
        }
        return false;
    }

    var _getGalleryById = function(galleryId) {
        if(_collGalleries[galleryId] != undefined) {
            return _collGalleries[galleryId];
        }
        return false;
    }

    return {
        addGallery: _addGallery,
        getCurrentIndex: _getCurrentIndex,
        setCurrentIndex: _setCurrentIndex
    };

})();

/**
 * @type {{init, instance}}
 */
var QPOwlGallery = (function() {

    var _instance = {};

    var _init = function(jqueryContainer, options)
    {
        jqueryContainer.each(function(i, element) {
            $(element).owlCarousel($.extend({
                singleItem : true,
                autoHeight : true,
                lazyLoad : true,
                afterInit: _resizeOwlFotos,
                afterAction: function() {
                    QPGalleryManager.setCurrentIndex($(element).data('gallery-id'), this.owl.currentItem);
                    _resizeOwlFotos();
                }
            }, options));

            _instance[$(element).data('gallery-id')] = {
                element: $(element),
                dataOwl: $(element).data('owlCarousel')
            };
        });

        return _instance;
    }

    var _resizeOwlFotos = function()
    {
        if (_instance.length > 0) {
            if ($(window).width() >= 992) {
                $(_instance).each(function (i, gallery) {
                    gallery.element.find('.owl-controls').hide();
                });
            } else {
                $(_instance).each(function (i, gallery) {
                    gallery.element.find('.owl-controls').show();
                });
            }
        }
    }

    var _getInstance = function(galleryId)
    {
        if (_instance[galleryId] != undefined) {
            return _instance[galleryId];
        }
        return false;
    }

    var _goTo = function(gallery)
    {
        if (_getInstance(gallery.galleryId)) {
            _getInstance(gallery.galleryId).dataOwl.goTo(gallery.currentIndex)
        }
    }

    return  {
        init: _init,
        getInstance: _getInstance,
        goTo: _goTo
    }

})();

/**
 * QPVerticalGallery
 * @type {{init, instance}}
 */
var QPVerticalGallery = (function() {

    var _productThumbnailsSwiper = {};

    var _init = function(jqueryContainer, options) {

        var galleryId = (jqueryContainer).data('gallery-id');
        _productThumbnailsSwiper[galleryId] = jqueryContainer.swiper($.extend({
            mode: 'vertical',
            slidesPerView: 'auto',
            watchActiveIndex: true,
            mousewheelControl: true,
            onSlideClick : function() {
                QPGalleryManager.setCurrentIndex(galleryId, _productThumbnailsSwiper[galleryId].clickedSlideIndex);
            }
        }, options));

        return _productThumbnailsSwiper;
    }

    var _getInstance = function(galleryId) {
        if (_productThumbnailsSwiper[galleryId]) {
            return _productThumbnailsSwiper[galleryId];
        }
        return false;
    }

    return  {
        init: _init,
        getInstance: _getInstance
    }

})();

/**
 * PhotoSwipe
 * @type {{init}}
 */
var QPPhotoSwipe = (function() {

    var _pswp = null;

    var _create = function(jqueryElementGallery, options) {
        var pswpElement = document.querySelectorAll('.pswp')[0];
        var items = _parseThumbnailElements(jqueryElementGallery);
        var defaultOptions = _defaultOptions();
        var options = $.extend(defaultOptions, options);

        _pswp = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
        _pswp.listen('beforeChange', function() {
            QPGalleryManager.setCurrentIndex(_pswp.getCurrentIndex());
        });

        return _pswp;
    }

    var _openGallery = function() {
        _pswp.init();
    };

    var _getInstance = function() {
        return _pswp;
    };

    var _defaultOptions = function() {
        return {
            mainClass: 'pswp--minimal--dark',
            barsSize: {
                top: 0,
                bottom: 0
            },
            captionEl: false,
            fillscreenEl: false,
            shareEl: false,
            bgOpacity: 0.85,
            tapToClose: true,
            tapToToggleControls: false
        }
    };

    var _parseThumbnailElements = function(jqueryElementGallery) {
        var $nodes = jqueryElementGallery.find('a');
        var items = [];

        $nodes.each(function(i, el) {
            childElements = el.children;
            size = el.getAttribute('data-size').split('x');

            item = {
                src: el.getAttribute('href'),
                w: parseInt(size[0], 10),
                h: parseInt(size[1], 10)
            };

            item.o = {
                src: item.src,
                w: item.w,
                h: item.h
            };

            items.push(item);
        });

        return items;
    };

    return {
        create: _create,
        openGallery: _openGallery,
        getInstance: _getInstance
    };

})();

// Máscara para input referente a valor em R$
$(function() {
    initMaskMoney();
});

function initMaskMoney(seletor, options) {
    if (typeof seletor === 'object') {
        options = seletor;
        seletor = undefined;
    }
    var options = options || {thousands:'.', decimal:',', allowZero:true, prefix: 'R$ '};
    var seletor = seletor || '.mask-money';
    $(seletor).maskMoney(options);
    return true;
}