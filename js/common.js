$(function () {
    //詳細検索の状態記録
    $('#clps-search').on('shown.bs.collapse', function(){
        $('.is_clps_open').attr('value', '1');
    });
    $('#clps-search').on('hidden.bs.collapse', function(){
        $('.is_clps_open').attr('value', '');
    });

    //料理データの数
    var num = $('#cuisine').data('num');

    $('.owl-carousel').owlCarousel({
        loop:false,
        margin:15,
        nav:false,
        item:num,
        responsive:{
            0:{
                items:3
            },
            600:{
                items:5
            },
            1000:{
                items:7
            }
        }
    });

});
