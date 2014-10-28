$(document).ready(function() {
    //go top
    $(document).ready(function() {
        var offset = 220;
        var duration = 300;
        $(window).scroll(function() {
            if ($(this).scrollTop() > offset) {
                $('.go-to-top').fadeIn(duration);
            } else {
                $('.go-to-top').fadeOut(duration);
            }
        });

        $('.go-to-top').click(function(event) {
            event.preventDefault();
            $('html, body').animate({scrollTop: 0}, duration);
            return false;
        });
    });
    //Accordion
    $('#myCollapsible').collapse({
        toggle: false
    });
    //tooltip
//		$('[data-toggle="tooltip"]').tooltip({
//    'placement': 'top'
//	});
    //Tabs
    $('#myTab a').click(function(e) {
        e.preventDefault()
        $(this).tab('show')
    })
    //Dropdown
    function DropDown(el) {
        this.dd = el;
        this.initEvents();
    }
    DropDown.prototype = {
        initEvents: function() {
            var obj = this;

            obj.dd.on('click', function(event) {
                $(this).toggleClass('active');
                event.stopPropagation();
            });
        }
    }

    $(function() {

        var dd = new DropDown($('#dd'));

        $(document).click(function() {
            // all dropdowns
            $('.wrapper-dropdown').removeClass('active');
        });

    });
});