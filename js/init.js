$(function(){
    // Имитация плейсхолдера для ИЕ8-9
    $('#header-search-input').focus(function(){
        if($(this).val() == "Поиск") {
            $(this).val('');
        }
    });
    $('#header-search-input').blur(function(){
        if($(this).val() == "") {
            $(this).val('Поиск');
        }
    });

    //Инициализация слайдера на главной
    $('#iview').iView({
        pauseTime: 5000,
        pauseOnHover: true,
        directionNav: false,
        controlNav: true,
        controlNavTooltip: false,
        controlNavHoverOpacity: 1
    });
})