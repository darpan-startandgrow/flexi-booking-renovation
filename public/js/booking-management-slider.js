jQuery(document).ready(function ($) {
    var counter = jQuery(document).find('div.svc_by_cat_class').length;

    if (counter != 0) {
        for (var i = 1; i <= counter; i++) {
            // Get the specific view mode for each category_search_tab_nav based on the counter
            var savedViewMode = localStorage.getItem('viewMode_' + i) || 'grid';

            // Select the first tab and div by default based on the saved view mode
            $('#category_search_tab_nav_' + i + ' > ul > li > a').removeClass('selected textblue');
            $('#category_search_tab_nav_' + i + ' > div').removeClass('selected').css('display', 'none');

            if (savedViewMode === 'carousel') {
                $('#category_search_tab_nav_' + i + ' > ul > li').eq(1).find('a').addClass("selected textblue");
                $('#category_search_tab_nav_' + i + ' > div').eq(1).addClass("selected").css('display', 'block');
            } else {
                $('#category_search_tab_nav_' + i + ' > ul > li').eq(0).find('a').addClass("selected textblue");
                $('#category_search_tab_nav_' + i + ' > div').eq(0).addClass("selected").css('display', 'block');
            }

            $('#category_search_tab_nav_' + i).css({
                'display': 'block',
                'width': '100%',
            });

            $('#category_search_tab_nav_' + i + ' ul').css({
                'padding': '0px',
                'list-style': 'none',
                'overflow': 'hidden',
                'position': 'absolute',
                'right': '30px',
                'top': '0px',
            });

            $('#category_search_tab_nav_' + i + ' li').css({
                'display': 'table-cell',
                'text-align': 'center',
                'padding': '0 2px 0 0',
            });

            $('#category_search_tab_nav_' + i + ' li a').css({
                'display': 'block',
                'font-weight': 'bold',
                'color': '#2E465C',
                'margin-left': '10px',
                'text-decoration': 'none',
                'font-size': '24px',
            });

            $('#category_search_tab_nav_' + i + ' li a img').css({
                'height': '22px',
                'width': 'auto',
            });

            $('#category_search_tab_nav_' + i + ' .tabcontent').css({
                'display': 'none',
            });

            $('#category_search_tab_nav_' + i + ' div.selected').css({
                'display': 'block',
            });

            $('#category_search_tab_nav_' + i + ' > ul').on('click', 'a', function () {
                var id = $(this).parents('div').attr('id');
                var counter = id.split('_').pop(); // Extract the counter from the ID
                var aElement = $('#' + id + ' > ul > li > a');
                var divContent = $('#' + id + ' > div');
                var slider = $(divContent).find('.slider1');

                // Handle Tab Nav
                aElement.removeClass("selected textblue");
                $(this).addClass("selected textblue");

                // Handle Tab Content
                var clicked_index = aElement.index(this);
                divContent.css('display', 'none');
                divContent.eq(clicked_index).css('display', 'block');
                $(slider).slick('setPosition');
                $(this).blur();

                // Store the selected view mode in local storage for this specific tab
                var selectedView = clicked_index === 1 ? 'carousel' : 'grid';
                localStorage.setItem('viewMode_' + counter, selectedView);

                return false;
            });
        }

        activateSlick();
    }
});

function activateSlick() {
    jQuery('.slider1').not('.slick-initialized').slick({
        dots: true,
        infinite: true,
        speed: 800,
        autoplay: true,
        autoplaySpeed: 2000,
        slidesToShow: 4,
        slidesToScroll: 4,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 3,
                    infinite: true,
                    dots: true
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });
}
