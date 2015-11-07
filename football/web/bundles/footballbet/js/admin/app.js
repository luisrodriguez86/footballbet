/**
Core script to handle the entire theme and core functions
**/
var App = function () {

    // IE mode
    var isRTL = false;
    var isIE8 = false;
    var isIE9 = false;
    var isIE10 = false;

    var sidebarWidth = 225;
    var sidebarCollapsedWidth = 35;

    var responsiveHandlers = [];

    // theme layout color set
    var layoutColorCodes = {
        'blue': '#4b8df8',
        'red': '#e02222',
        'green': '#35aa47',
        'purple': '#852b99',
        'grey': '#555555',
        'light-grey': '#fafafa',
        'yellow': '#ffb848'
    };

    // To get the correct viewport width based on  http://andylangton.co.uk/articles/javascript/get-viewport-size-javascript/
    var _getViewPort = function () {
        var e = window, a = 'inner';
        if (!('innerWidth' in window)) {
            a = 'client';
            e = document.documentElement || document.body;
        }
        return {
            width: e[a + 'Width'],
            height: e[a + 'Height']
        }
    }

    // initializes main settings
    var handleInit = function () {

        if (jQuery('body').css('direction') === 'rtl') {
            isRTL = true;
        }

        isIE8 = !! navigator.userAgent.match(/MSIE 8.0/);
        isIE9 = !! navigator.userAgent.match(/MSIE 9.0/);
        isIE10 = !! navigator.userAgent.match(/MSIE 10.0/);

        if (isIE10) {
            jQuery('html').addClass('ie10'); // detect IE10 version
        }
        
        if (isIE10 || isIE9 || isIE8) {
            jQuery('html').addClass('ie'); // detect IE10 version
        }

        /*
          Virtual keyboards:
          Also, note that if you're using inputs in your modal – iOS has a rendering bug which doesn't 
          update the position of fixed elements when the virtual keyboard is triggered  
        */
        var deviceAgent = navigator.userAgent.toLowerCase();
        if (deviceAgent.match(/(iphone|ipod|ipad)/)) {
            jQuery(document).on('focus', 'input, textarea', function () {
                jQuery('.header').hide();
                jQuery('.footer').hide();
            });
            jQuery(document).on('blur', 'input, textarea', function () {
                jQuery('.header').show();
                jQuery('.footer').show();
            });
        }
    }

    var handleSidebarState = function () {
        // remove sidebar toggler if window width smaller than 992(for tablet and phone mode)
        var viewport = _getViewPort();
        if (viewport.width < 992) {
            jQuery('body').removeClass("page-sidebar-closed");
        }
    }

    // runs callback functions set by App.addResponsiveHandler().
    var runResponsiveHandlers = function () {
        // reinitialize other subscribed elements
        for (var i in responsiveHandlers) {
            var each = responsiveHandlers[i];
            each.call();
        }
    }

    // reinitialize the laypot on window resize
    var handleResponsive = function () {
        handleSidebarState();
        handleSidebarAndContentHeight();
        handleFixedSidebar();
        runResponsiveHandlers();
    }

    // initialize the layout on page load
    var handleResponsiveOnInit = function () {
        handleSidebarState();
        handleSidebarAndContentHeight();
    }

    // handle the layout reinitialization on window resize
    var handleResponsiveOnResize = function () {
        var resize;
        if (isIE8) {
            var currheight;
            jQuery(window).resize(function () {
                if (currheight == document.documentElement.clientHeight) {
                    return; //quite event since only body resized not window.
                }
                if (resize) {
                    clearTimeout(resize);
                }
                resize = setTimeout(function () {
                    handleResponsive();
                }, 50); // wait 50ms until window resize finishes.                
                currheight = document.documentElement.clientHeight; // store last body client height
            });
        } else {
            jQuery(window).resize(function () {
                if (resize) {
                    clearTimeout(resize);
                }
                resize = setTimeout(function () {
                    handleResponsive();
                }, 50); // wait 50ms until window resize finishes.
            });
        }
    }

    //* BEGIN:CORE HANDLERS *//
    // this function handles responsive layout on screen size resize or mobile device rotate.

    // Set proper height for sidebar and content. The content and sidebar height must be synced always.
    var handleSidebarAndContentHeight = function () {
        var content = jQuery('.page-content');
        var sidebar = jQuery('.page-sidebar');
        var body = jQuery('body');
        var height;

        if (body.hasClass("page-footer-fixed") === true && body.hasClass("page-sidebar-fixed") === false) {
            var available_height = jQuery(window).height() - jQuery('.footer').outerHeight();
            if (content.height() < available_height) {
                content.attr('style', 'min-height:' + available_height + 'px !important');
            }
        } else {
            if (body.hasClass('page-sidebar-fixed')) {
                height = _calculateFixedSidebarViewportHeight();
            } else {
                height = sidebar.height() + 20;
            }
            if (height >= content.height()) {
                content.attr('style', 'min-height:' + height + 'px !important');
            }
        }
    }

    // Handle sidebar menu
    var handleSidebarMenu = function () {
        jQuery('.page-sidebar').on('click', 'li > a', function (e) {
            if (jQuery(this).next().hasClass('sub-menu') == false) {
                if (jQuery('.btn-navbar').hasClass('collapsed') == false) {
                    jQuery('.btn-navbar').click();
                }
                return;
            }

            if (jQuery(this).next().hasClass('sub-menu.always-open')) {
                return;
            }

            var parent = jQuery(this).parent().parent();
            var the = jQuery(this);

            parent.children('li.open').children('a').children('.arrow').removeClass('open');
            parent.children('li.open').children('.sub-menu').slideUp(200);
            parent.children('li.open').removeClass('open');

            var sub = jQuery(this).next();
            var slideOffeset = -200;
            var slideSpeed = 200;

            if (sub.is(":visible")) {
                jQuery('.arrow', jQuery(this)).removeClass("open");
                jQuery(this).parent().removeClass("open");
                sub.slideUp(slideSpeed, function () {
                    if (jQuery('body').hasClass('page-sidebar-fixed') == false && jQuery('body').hasClass('page-sidebar-closed') == false) {
                        App.scrollTo(the, slideOffeset);
                    }
                    handleSidebarAndContentHeight();
                });
            } else {
                jQuery('.arrow', jQuery(this)).addClass("open");
                jQuery(this).parent().addClass("open");
                sub.slideDown(slideSpeed, function () {
                    if (jQuery('body').hasClass('page-sidebar-fixed') == false && jQuery('body').hasClass('page-sidebar-closed') == false) {
                        App.scrollTo(the, slideOffeset);
                    }
                    handleSidebarAndContentHeight();
                });
            }

            e.preventDefault();
        });

        // handle ajax links
        jQuery('.page-sidebar').on('click', ' li > a.ajaxify', function (e) {
            e.preventDefault();
            App.scrollTop();

            var url = jQuery(this).attr("href");
            var menuContainer = jQuery('.page-sidebar ul');
            var pageContent = jQuery('.page-content');
            var pageContentBody = jQuery('.page-content .page-content-body');

            menuContainer.children('li.active').removeClass('active');
            menuContainer.children('arrow.open').removeClass('open');

            jQuery(this).parents('li').each(function () {
                jQuery(this).addClass('active');
                jQuery(this).children('a > span.arrow').addClass('open');
            });
            jQuery(this).parents('li').addClass('active');

            App.blockUI(pageContent, false);

            jQuery.ajax({
                type: "GET",
                cache: false,
                url: url,
                dataType: "html",
                success: function (res) {
                    App.unblockUI(pageContent);
                    pageContentBody.html(res);
                    App.fixContentHeight(); // fix content height
                    App.initAjax(); // initialize core stuff
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    pageContentBody.html('<h4>Could not load the requested content.</h4>');
                    App.unblockUI(pageContent);
                },
                async: false
            });
        });
    }

    // Helper function to calculate sidebar height for fixed sidebar layout.
    var _calculateFixedSidebarViewportHeight = function () {
        var sidebarHeight = jQuery(window).height() - jQuery('.header').height() + 1;
        if (jQuery('body').hasClass("page-footer-fixed")) {
            sidebarHeight = sidebarHeight - jQuery('.footer').outerHeight();
        }

        return sidebarHeight;
    }

    // Handles fixed sidebar
    var handleFixedSidebar = function () {
        var menu = jQuery('.page-sidebar-menu');

        if (menu.parent('.slimScrollDiv').size() === 1) { // destroy existing instance before updating the height
            menu.slimScroll({
                destroy: true
            });
            menu.removeAttr('style');
            jQuery('.page-sidebar').removeAttr('style');
        }

        if (jQuery('.page-sidebar-fixed').size() === 0) {
            handleSidebarAndContentHeight();
            return;
        }

        var viewport = _getViewPort();
        if (viewport.width >= 992) {
            var sidebarHeight = _calculateFixedSidebarViewportHeight();

            menu.slimScroll({
                size: '7px',
                color: '#a1b2bd',
                opacity: .3,
                position: isRTL ? 'left' : 'right',
                height: sidebarHeight,
                allowPageScroll: false,
                disableFadeOut: false
            });
            handleSidebarAndContentHeight();
        }
    }

    // Handles the sidebar menu hover effect for fixed sidebar.
    var handleFixedSidebarHoverable = function () {
        if (jQuery('body').hasClass('page-sidebar-fixed') === false) {
            return;
        }

        jQuery('.page-sidebar').off('mouseenter').on('mouseenter', function () {
            var body = jQuery('body');

            if ((body.hasClass('page-sidebar-closed') === false || body.hasClass('page-sidebar-fixed') === false) || jQuery(this).hasClass('page-sidebar-hovering')) {
                return;
            }

            body.removeClass('page-sidebar-closed').addClass('page-sidebar-hover-on');
            jQuery(this).addClass('page-sidebar-hovering');
            jQuery(this).animate({
                width: sidebarWidth
            }, 400, '', function () {
                jQuery(this).removeClass('page-sidebar-hovering');
            });
        });

        jQuery('.page-sidebar').off('mouseleave').on('mouseleave', function () {
            var body = jQuery('body');

            if ((body.hasClass('page-sidebar-hover-on') === false || body.hasClass('page-sidebar-fixed') === false) || jQuery(this).hasClass('page-sidebar-hovering')) {
                return;
            }

            jQuery(this).addClass('page-sidebar-hovering');
            jQuery(this).animate({
                width: sidebarCollapsedWidth
            }, 400, '', function () {
                jQuery('body').addClass('page-sidebar-closed').removeClass('page-sidebar-hover-on');
                jQuery(this).removeClass('page-sidebar-hovering');
            });
        });
    }

    // Handles sidebar toggler to close/hide the sidebar.
    var handleSidebarToggler = function () {
        var viewport = _getViewPort();
        if (jQuery.cookie('sidebar_closed') === '1' && viewport.width >= 992) {
            jQuery('body').addClass('page-sidebar-closed');
        }

        // handle sidebar show/hide
        jQuery('.page-sidebar').on('click', '.sidebar-toggler', function (e) {
            var body = jQuery('body');
            var sidebar = jQuery('.page-sidebar');

            if ((body.hasClass("page-sidebar-hover-on") && body.hasClass('page-sidebar-fixed')) || sidebar.hasClass('page-sidebar-hovering')) {
                body.removeClass('page-sidebar-hover-on');
                sidebar.css('width', '').hide().show();
                jQuery.cookie('sidebar_closed', '0');
                e.stopPropagation();
                runResponsiveHandlers();
                return;
            }

            jQuery(".sidebar-search", sidebar).removeClass("open");

            if (body.hasClass("page-sidebar-closed")) {
                body.removeClass("page-sidebar-closed");
                if (body.hasClass('page-sidebar-fixed')) {
                    sidebar.css('width', '');
                }
                jQuery.cookie('sidebar_closed', '0');
            } else {
                body.addClass("page-sidebar-closed");
                jQuery.cookie('sidebar_closed', '1');
            }
            runResponsiveHandlers();
        });

        // handle the search bar close
        jQuery('.page-sidebar').on('click', '.sidebar-search .remove', function (e) {
            e.preventDefault();
            jQuery('.sidebar-search').removeClass("open");
        });

        // handle the search query submit on enter press
        jQuery('.page-sidebar').on('keypress', '.sidebar-search input', function (e) {
            if (e.which == 13) {
                jQuery('.sidebar-search').submit();
                return false; //<---- Add this line
            }
        });

        // handle the search submit
        jQuery('.sidebar-search .submit').on('click', function (e) {
            e.preventDefault();
            if (jQuery('body').hasClass("page-sidebar-closed")) {
                if (jQuery('.sidebar-search').hasClass('open') == false) {
                    if (jQuery('.page-sidebar-fixed').size() === 1) {
                        jQuery('.page-sidebar .sidebar-toggler').click(); //trigger sidebar toggle button
                    }
                    jQuery('.sidebar-search').addClass("open");
                } else {
                    jQuery('.sidebar-search').submit();
                }
            } else {
                jQuery('.sidebar-search').submit();
            }
        });
    }

    // Handles the horizontal menu
    var handleHorizontalMenu = function () {
        //handle hor menu search form toggler click
        jQuery('.header').on('click', '.hor-menu .hor-menu-search-form-toggler', function (e) {
            if (jQuery(this).hasClass('off')) {
                jQuery(this).removeClass('off');
                jQuery('.header .hor-menu .search-form').hide();
            } else {
                jQuery(this).addClass('off');
                jQuery('.header .hor-menu .search-form').show();
            }
            e.preventDefault();
        });

        //handle hor menu search button click
        jQuery('.header').on('click', '.hor-menu .search-form .btn', function (e) {
            jQuery('.form-search').submit();
            e.preventDefault();
        });

        //handle hor menu search form on enter press
        jQuery('.header').on('keypress', '.hor-menu .search-form input', function (e) {
            if (e.which == 13) {
                jQuery('.form-search').submit();
                return false;
            }
        });
    }

    // Handles the go to top button at the footer
    var handleGoTop = function () {
        /* set variables locally for increased performance */
        jQuery('.footer').on('click', '.go-top', function (e) {
            App.scrollTo();
            e.preventDefault();
        });
    }

    // Handles portlet tools & actions
    var handlePortletTools = function () {
        jQuery('body').on('click', '.portlet > .portlet-title > .tools > a.remove', function (e) {
            e.preventDefault();
            jQuery(this).closest(".portlet").remove();
        });

        jQuery('body').on('click', '.portlet > .portlet-title > .tools > a.reload', function (e) {
            e.preventDefault();
            var el = jQuery(this).closest(".portlet").children(".portlet-body");
            App.blockUI(el);
            window.setTimeout(function () {
                App.unblockUI(el);
            }, 1000);
        });

        jQuery('body').on('click', '.portlet > .portlet-title > .tools > .collapse, .portlet .portlet-title > .tools > .expand', function (e) {
            e.preventDefault();
            var el = jQuery(this).closest(".portlet").children(".portlet-body");
            if (jQuery(this).hasClass("collapse")) {
                jQuery(this).removeClass("collapse").addClass("expand");
                el.slideUp(200);
            } else {
                jQuery(this).removeClass("expand").addClass("collapse");
                el.slideDown(200);
            }
        });
    }

    // Handles custom checkboxes & radios using jQuery Uniform plugin
    var handleUniform = function () {
        if (!jQuery().uniform) {
            return;
        }
        var test = jQuery("input[type=checkbox]:not(.toggle), input[type=radio]:not(.toggle, .star)");
        if (test.size() > 0) {
            test.each(function () {
                if (jQuery(this).parents(".checker").size() == 0) {
                    jQuery(this).show();
                    jQuery(this).uniform();
                }
            });
        }
    }

    // Handles Bootstrap Accordions.
    var handleAccordions = function () {
        var lastClicked;
        //add scrollable class name if you need scrollable panes
        jQuery('body').on('click', '.accordion.scrollable .accordion-toggle', function () {
            lastClicked = jQuery(this);
        }); //move to faq section

        jQuery('body').on('show.bs.collapse', '.accordion.scrollable', function () {
            jQuery('html,body').animate({
                scrollTop: lastClicked.offset().top - 150
            }, 'slow');
        });
    }

    // Handles Bootstrap Tabs.
    var handleTabs = function () {
        // fix content height on tab click
        jQuery('body').on('shown.bs.tab', '.nav.nav-tabs', function () {
            handleSidebarAndContentHeight();
        });

        //activate tab if tab id provided in the URL
        if (location.hash) {
            var tabid = location.hash.substr(1);
            jQuery('a[href="#' + tabid + '"]').click();
        }
    }

    // Handles Bootstrap Modals.
    var handleModals = function () {

        // fix stackable modal issue: when 2 or more modals opened, closing one of modal will remove .modal-open class. 
        jQuery('body').on('hide.bs.modal', function () {
           if (jQuery('.modal:visible').size() > 1 && jQuery('html').hasClass('modal-open') == false) {
              jQuery('html').addClass('modal-open');
           } else if (jQuery('.modal:visible').size() <= 1) {
              jQuery('html').removeClass('modal-open');
           }
        });
    }

    // Handles Bootstrap Tooltips.
    var handleTooltips = function () {
       jQuery('.tooltips').tooltip();
    }

    // Handles Bootstrap Dropdowns
    var handleDropdowns = function () {
        /*
          For touch supported devices disable the 
          hoverable dropdowns - data-hover="dropdown"  
        */
        if (App.isTouchDevice()) {
            jQuery('[data-hover="dropdown"]').each(function(){
                jQuery(this).parent().off("hover");
                jQuery(this).off("hover");
            });
        }
        /*
          Hold dropdown on click  
        */
        jQuery('body').on('click', '.dropdown-menu.hold-on-click', function (e) {
            e.stopPropagation();
        })
    }

    // Handle Hower Dropdowns
    var handleDropdownHover = function () {
        jQuery('[data-hover="dropdown"]').dropdownHover();
    }

    // Handles Bootstrap Popovers

    // last popep popover
    var lastPopedPopover;

    var handlePopovers = function () {
        jQuery('.popovers').popover();

        // close last poped popover

        jQuery(document).on('click.bs.popover.data-api', function (e) {
            if (lastPopedPopover) {
                lastPopedPopover.popover('hide');
            }
        });
    }

    // Handles scrollable contents using jQuery SlimScroll plugin.
    var handleScrollers = function () {
        jQuery('.scroller').each(function () {
            var height;
            if (jQuery(this).attr("data-height")) {
                height = jQuery(this).attr("data-height");
            } else {
                height = jQuery(this).css('height');
            }
            jQuery(this).slimScroll({
                size: '7px',
                color: (jQuery(this).attr("data-handle-color")  ? jQuery(this).attr("data-handle-color") : '#a1b2bd'),
                railColor: (jQuery(this).attr("data-rail-color")  ? jQuery(this).attr("data-rail-color") : '#333'),
                position: isRTL ? 'left' : 'right',
                height: height,
                alwaysVisible: (jQuery(this).attr("data-always-visible") == "1" ? true : false),
                railVisible: (jQuery(this).attr("data-rail-visible") == "1" ? true : false),
                disableFadeOut: true
            });
        });
    }

    // Handles Image Preview using jQuery Fancybox plugin
    var handleFancybox = function () {
        if (!jQuery.fancybox) {
            return;
        }

        if (jQuery(".fancybox-button").size() > 0) {
            jQuery(".fancybox-button").fancybox({
                groupAttr: 'data-rel',
                prevEffect: 'none',
                nextEffect: 'none',
                closeBtn: true,
                helpers: {
                    title: {
                        type: 'inside'
                    }
                }
            });
        }
    }

    // Fix input placeholder issue for IE8 and IE9
    var handleFixInputPlaceholderForIE = function () {
        //fix html5 placeholder attribute for ie7 & ie8
        if (isIE8 || isIE9) { // ie8 & ie9
            // this is html5 placeholder fix for inputs, inputs with placeholder-no-fix class will be skipped(e.g: we need this for password fields)
            jQuery('input[placeholder]:not(.placeholder-no-fix), textarea[placeholder]:not(.placeholder-no-fix)').each(function () {

                var input = jQuery(this);

                if (input.val() == '' && input.attr("placeholder") != '') {
                    input.addClass("placeholder").val(input.attr('placeholder'));
                }

                input.focus(function () {
                    if (input.val() == input.attr('placeholder')) {
                        input.val('');
                    }
                });

                input.blur(function () {
                    if (input.val() == '' || input.val() == input.attr('placeholder')) {
                        input.val(input.attr('placeholder'));
                    }
                });
            });
        }
    }

    // Handle full screen mode toggle
    var handleFullScreenMode = function() {
        // mozfullscreenerror event handler
       
        // toggle full screen
        function toggleFullScreen() {
          if (!document.fullscreenElement &&    // alternative standard method
              !document.mozFullScreenElement && !document.webkitFullscreenElement) {  // current working methods
            if (document.documentElement.requestFullscreen) {
              document.documentElement.requestFullscreen();
            } else if (document.documentElement.mozRequestFullScreen) {
              document.documentElement.mozRequestFullScreen();
            } else if (document.documentElement.webkitRequestFullscreen) {
              document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
            }
          } else {
            if (document.cancelFullScreen) {
              document.cancelFullScreen();
            } else if (document.mozCancelFullScreen) {
              document.mozCancelFullScreen();
            } else if (document.webkitCancelFullScreen) {
              document.webkitCancelFullScreen();
            }
          }
        }

        jQuery('#trigger_fullscreen').click(function() {
            toggleFullScreen();
        });
    }

    // Handle Select2 Dropdowns
    var handleSelect2 = function() {
        if (jQuery().select2) {
            jQuery('.select2me').select2({
                placeholder: "Select",
                allowClear: true
            });
        }
    }

    // Handle Theme Settings
    var handleTheme = function () {

        var panel = jQuery('.theme-panel');

        if (jQuery('body').hasClass('page-boxed') == false) {
            jQuery('.layout-option', panel).val("fluid");
        }

        jQuery('.sidebar-option', panel).val("default");
        jQuery('.header-option', panel).val("fixed");
        jQuery('.footer-option', panel).val("default");

        //handle theme layout
        var resetLayout = function () {
            jQuery("body").
            removeClass("page-boxed").
            removeClass("page-footer-fixed").
            removeClass("page-sidebar-fixed").
            removeClass("page-header-fixed");

            jQuery('.header > .header-inner').removeClass("container");

            if (jQuery('.page-container').parent(".container").size() === 1) {
                jQuery('.page-container').insertAfter('body > .clearfix');
            }

            if (jQuery('.footer > .container').size() === 1) {
                jQuery('.footer').html(jQuery('.footer > .container').html());
            } else if (jQuery('.footer').parent(".container").size() === 1) {
                jQuery('.footer').insertAfter('.page-container');
            }

            jQuery('body > .container').remove();
        }

        var lastSelectedLayout = '';

        var setLayout = function () {

            var layoutOption = jQuery('.layout-option', panel).val();
            var sidebarOption = jQuery('.sidebar-option', panel).val();
            var headerOption = jQuery('.header-option', panel).val();
            var footerOption = jQuery('.footer-option', panel).val();

            if (sidebarOption == "fixed" && headerOption == "default") {
                alert('Default Header with Fixed Sidebar option is not supported. Proceed with Fixed Header with Fixed Sidebar.');
                jQuery('.header-option', panel).val("fixed");
                jQuery('.sidebar-option', panel).val("fixed");
                sidebarOption = 'fixed';
                headerOption = 'fixed';
            }

            resetLayout(); // reset layout to default state

            if (layoutOption === "boxed") {
                jQuery("body").addClass("page-boxed");

                // set header
                jQuery('.header > .header-inner').addClass("container");
                var cont = jQuery('body > .clearfix').after('<div class="container"></div>');

                // set content
                jQuery('.page-container').appendTo('body > .container');

                // set footer
                if (footerOption === 'fixed') {
                    jQuery('.footer').html('<div class="container">' + jQuery('.footer').html() + '</div>');
                } else {
                    jQuery('.footer').appendTo('body > .container');
                }
            }

            if (lastSelectedLayout != layoutOption) {
                //layout changed, run responsive handler:
                runResponsiveHandlers();
            }
            lastSelectedLayout = layoutOption;

            //header
            if (headerOption === 'fixed') {
                jQuery("body").addClass("page-header-fixed");
                jQuery(".header").removeClass("navbar-static-top").addClass("navbar-fixed-top");
            } else {
                jQuery("body").removeClass("page-header-fixed");
                jQuery(".header").removeClass("navbar-fixed-top").addClass("navbar-static-top");
            }

            //sidebar
            if (sidebarOption === 'fixed') {
                jQuery("body").addClass("page-sidebar-fixed");
            } else {
                jQuery("body").removeClass("page-sidebar-fixed");
            }

            //footer 
            if (footerOption === 'fixed') {
                jQuery("body").addClass("page-footer-fixed");
            } else {
                jQuery("body").removeClass("page-footer-fixed");
            }

            handleSidebarAndContentHeight(); // fix content height            
            handleFixedSidebar(); // reinitialize fixed sidebar
            handleFixedSidebarHoverable(); // reinitialize fixed sidebar hover effect
        }

        // handle theme colors
        var setColor = function (color) {
            jQuery('#style_color').attr("href", "assets/css/themes/" + color + ".css");
            jQuery.cookie('style_color', color);
        }

        jQuery('.toggler', panel).click(function () {
            jQuery('.toggler').hide();
            jQuery('.toggler-close').show();
            jQuery('.theme-panel > .theme-options').show();
        });

        jQuery('.toggler-close', panel).click(function () {
            jQuery('.toggler').show();
            jQuery('.toggler-close').hide();
            jQuery('.theme-panel > .theme-options').hide();
        });

        jQuery('.theme-colors > ul > li', panel).click(function () {
            var color = jQuery(this).attr("data-style");
            setColor(color);
            jQuery('ul > li', panel).removeClass("current");
            jQuery(this).addClass("current");
        });

        jQuery('.layout-option, .header-option, .sidebar-option, .footer-option', panel).change(setLayout);

        if (jQuery.cookie('style_color')) {
            setColor(jQuery.cookie('style_color'));
        }
    }

    //* END:CORE HANDLERS *//

    return {

        //main function to initiate the theme
        init: function () {

            //IMPORTANT!!!: Do not modify the core handlers call order.

            //core handlers
            handleInit(); // initialize core variables
            handleResponsiveOnResize(); // set and handle responsive
            handleUniform(); // hanfle custom radio & checkboxes
            handleScrollers(); // handles slim scrolling contents 
            handleResponsiveOnInit(); // handler responsive elements on page load

            //layout handlers
            handleFixedSidebar(); // handles fixed sidebar menu
            handleFixedSidebarHoverable(); // handles fixed sidebar on hover effect 
            handleSidebarMenu(); // handles main menu
            handleHorizontalMenu(); // handles horizontal menu
            handleSidebarToggler(); // handles sidebar hide/show            
            handleFixInputPlaceholderForIE(); // fixes/enables html5 placeholder attribute for IE9, IE8
            handleGoTop(); //handles scroll to top functionality in the footer
            handleTheme(); // handles style customer tool

            //ui component handlers
            handleFancybox() // handle fancy box
            handleSelect2(); // handle custom Select2 dropdowns
            handlePortletTools(); // handles portlet action bar functionality(refresh, configure, toggle, remove)
            handleDropdowns(); // handle dropdowns
            handleTabs(); // handle tabs
            handleTooltips(); // handle bootstrap tooltips
            handlePopovers(); // handles bootstrap popovers
            handleAccordions(); //handles accordions 
            handleModals(); // handle modals
            handleFullScreenMode(); // handles full screen
        },

        //main function to initiate core javascript after ajax complete
        initAjax: function () {
            handleSelect2(); // handle custom Select2 dropdowns
            handleDropdowns(); // handle dropdowns
            handleTooltips(); // handle bootstrap tooltips
            handlePopovers(); // handles bootstrap popovers
            handleAccordions(); //handles accordions 
            handleUniform(); // hanfle custom radio & checkboxes     
            handleDropdownHover() // handles dropdown hover       
        },

        //public function to fix the sidebar and content height accordingly
        fixContentHeight: function () {
            handleSidebarAndContentHeight();
        },

        //public function to remember last opened popover that needs to be closed on click
        setLastPopedPopover: function (el) {
            lastPopedPopover = el;
        },

        //public function to add callback a function which will be called on window resize
        addResponsiveHandler: function (func) {
            responsiveHandlers.push(func);
        },

        // useful function to make equal height for contacts stand side by side
        setEqualHeight: function (els) {
            var tallestEl = 0;
            els = jQuery(els);
            els.each(function () {
                var currentHeight = jQuery(this).height();
                if (currentHeight > tallestEl) {
                    tallestColumn = currentHeight;
                }
            });
            els.height(tallestEl);
        },

        // wrapper function to scroll(focus) to an element
        scrollTo: function (el, offeset) {
            pos = (el && el.size() > 0) ? el.offset().top : 0;
            jQuery('html,body').animate({
                scrollTop: pos + (offeset ? offeset : 0)
            }, 'slow');
        },

        // function to scroll to the top
        scrollTop: function () {
            App.scrollTo();
        },

        // wrapper function to  block element(indicate loading)
        blockUI: function (el, centerY) {
            var el = jQuery(el);
            if (el.height() <= 400) {
                centerY = true;
            }
            el.block({
                message: '<img src="./assets/img/ajax-loading.gif" align="">',
                centerY: centerY != undefined ? centerY : true,
                css: {
                    top: '10%',
                    border: 'none',
                    padding: '2px',
                    backgroundColor: 'none'
                },
                overlayCSS: {
                    backgroundColor: '#000',
                    opacity: 0.05,
                    cursor: 'wait'
                }
            });
        },

        // wrapper function to  un-block element(finish loading)
        unblockUI: function (el) {
            jQuery(el).unblock({
                onUnblock: function () {
                    jQuery(el).removeAttr("style");
                }
            });
        },

        // initializes uniform elements
        initUniform: function (els) {
            if (els) {
                jQuery(els).each(function () {
                    if (jQuery(this).parents(".checker").size() == 0) {
                        jQuery(this).show();
                        jQuery(this).uniform();
                    }
                });
            } else {
                handleUniform();
            }

        },

        //wrapper function to update/sync jquery uniform checkbox & radios
        updateUniform: function (els) {
            jQuery.uniform.update(els); // update the uniform checkbox & radios UI after the actual input control state changed
        },

        //public function to initialize the fancybox plugin
        initFancybox: function () {
            handleFancybox();
        },

        //public helper function to get actual input value(used in IE9 and IE8 due to placeholder attribute not supported)
        getActualVal: function (el) {
            var el = jQuery(el);
            if (el.val() === el.attr("placeholder")) {
                return "";
            }
            return el.val();
        },

        //public function to get a paremeter by name from URL
        getURLParameter: function (paramName) {
            var searchString = window.location.search.substring(1),
                i, val, params = searchString.split("&");

            for (i = 0; i < params.length; i++) {
                val = params[i].split("=");
                if (val[0] == paramName) {
                    return unescape(val[1]);
                }
            }
            return null;
        },

        // check for device touch support
        isTouchDevice: function () {
            try {
                document.createEvent("TouchEvent");
                return true;
            } catch (e) {
                return false;
            }
        },

        // check IE8 mode
        isIE8: function () {
            return isIE8;
        },

        // check IE9 mode
        isIE9: function () {
            return isIE9;
        },

        //check RTL mode
        isRTL: function () {
            return isRTL;
        },

        // get layout color code by color name
        getLayoutColorCode: function (name) {
            if (layoutColorCodes[name]) {
                return layoutColorCodes[name];
            } else {
                return '';
            }
        }

    };

}();