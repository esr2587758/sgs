$(function(){
    $('.action').toggle(function(){
        $(this).removeClass('atb').addClass('at');
        $(this).next().slideDown();    
    },function(){
        $(this).next().slideUp('normal',function(){
            $(this).prev().removeClass('at').addClass('atb');  
        })  
    });

    $('.action').next().find('li').click(function(){
        $(this).siblings().removeClass('active').end().addClass('active');
        $(this).parent().prev().attr('rel',$(this).attr('rel'));
    });

    $('.submit').click(function(){
        loadingAnimate();
        go();
        return false;
    })

    $('.fileupload').change(function(){
        loadingAnimate();
        $(this).parent().submit();
    })
    

})

function builddata(){
    var wujiang = [];
    wujiang.name = $('.name').val();
    wujiang.chenghao = $('.chenghao').val();
    wujiang.blood = $('.blood').attr('rel');
    wujiang.race = $('.race').attr('rel');
    wujiang.pic_path = $('#pic_path').text();
    wujiang['skill[0][title]'] = $('.s0t').val()?$('.s0t').val():'';
    wujiang['skill[0][desc]'] = $('.s0d').val()?$('.s0d').val():'';
    wujiang['skill[1][title]'] = $('.s1t').val()?$('.s1t').val():'';
    wujiang['skill[1][desc]'] = $('.s1d').val()?$('.s1d').val():'';
    wujiang['skill[2][title]'] = $('.s2t').val()?$('.s2t').val():'';
    wujiang['skill[2][desc]'] = $('.s2d').val()?$('.s2d').val():'';
    var str = '';
    for(i in wujiang){
        if(str == ''){
            str = i+'='+wujiang[i];
        }else{
            str = str +'&' +i+'='+wujiang[i];
        }
    }
    return str;
}

function go(){
    var data_str = builddata();
    $.ajax({
        type:'POST',
        url:'../ajax.php',
        data:data_str,
        success:function(data){
            $('#pic_preview').attr("src",data+'?'+Math.random());
            loadingAnimate(false);
        }
    });

} 

function loadingAnimate(show){
    if(arguments.length == 0){
        show = true;
    }
    if(show){
        $('.loading').removeClass('hide');
    }else{
        $('.loading').addClass('hide');
    }

}
